/**
 * Custom script 
 */
;(function(window, document, $) {


    var HOME_URL    = $('meta[name="home_url"]').attr('content');

    // Set csrf token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        }
    });

    var product = {
        upload : function() {

            // Show loading button
            $('.btn-green').addClass('hide');
            $('#uploadLoading').removeClass('hide');
            
            // Set table
            var table = $('.initTable').DataTable();
            
            // Set counter
            var totalRows = table.rows().count();
            var processedRows = 0;
            
            // Start sending data
            sendData();
            
            function sendData() {
            
                // Grab all rowsubscriber data
                var element = $(table.row('.row-product.queue').node());
                
                // Stop sending if all 
                // Show done button
                if (!element) {
                    $('.btn-green').addClass('hide');
                    return $('#uploadFinish').removeClass('hide');
                }
                
                // Grab category
                var product     = element.find('.cell-product').data('product');
                var category    = element.find('.cell-category').data('category');
                
                if (!category || !product) {
                    
                    $('.btn-green').addClass('hide');
                    $('#uploadFinish').removeClass('hide');
                    return;
                    
                }
                
                // Set data
                var data = {
                    product: product,
                    category: category,
                };
                
                // Ajax request
                $.post(HOME_URL+'/product/model/single', data)
                    .done(function(response) {
                    
                        // Increment counter
                        processedRows++;

                        $('#uploadCounter').text(processedRows+'/'+totalRows);
                    
                        if(processedRows % 10 == 0)
                        {
                            var page = processedRows / 10;
                            table.page(page).draw('page');
                        } 
                    
                        if (response !== 'Saved') {
                            
                            // Add error class
                            element.removeClass('queue').addClass('error');
                            
                            // Append response to the text
                            var text = element.find('.cell-status').text();
                            text += ' (' + response + ')';
                            
                            element.find('.cell-status').text(text);
                            
                        } else {
                            
                            // Add error class
                            element.removeClass('queue').addClass('success');

                            // Append response to the text
                            var text = element.find('.cell-status').text();
                            text += ' (' + response + ')';
                            
                            element.find('.cell-status').text(text);
                            // Remove element and redraw table
                            // table.row(element).remove().draw();
                            
                        }
                    
                        return sendData();
                        
                    })
                    .fail(function() {
                        
                        // Show alert
                        alert('Failed connect to the internet. Please try again.');
                        
                        // Show upload button again
                        $('.btn-green').addClass('hide');
                        $('#uploadProduct').removeClass('hide');
                        $('#upload-meter').addClass('hide');
                        
                    });
                
            }
        },

        uploadPrice : function() {

            // Show loading button
            $('.btn-green').addClass('hide');
            $('#uploadLoading').removeClass('hide');
            $('#upload-meter').removeClass('hide');
                
            // Set table
            var table = $('.initTable').DataTable();
            
            // Set counter
            var totalRows = table.rows().count();
            var processedRows = 0;
            
            // Start sending data
            sendData();
            
            function sendData() {
            
                // Grab all row product price data
                var element = $(table.row('.row-product.queue').node());
                // Stop sending if all 
                // Show done button
                if (!element) {
                    $('.btn-green').addClass('hide');
                    return $('#uploadFinish').removeClass('hide');
                }
                
                // Grab data
                var product   = element.find('.cell-product').data('product');
                var dealerType   = element.find('.cell-dealer_type').data('dealer_type');
                var price_MUP   = element.find('.cell-price_mup').data('price_mup');
                var price_SO   = element.find('.cell-price_so').data('price_so');
                var price_SMO   = element.find('.cell-price_smo').data('price_smo');
                
                if (!dealerType || !product) {
                    
                    table.destroy();
                    $('.initTable').DataTable();
                    
                    $('.btn-green').addClass('hide');
                    return $('#uploadFinish').removeClass('hide');
                }

                // Set data
                var data = {
                    product: product,
                    dealerType: dealerType,
                    price_MUP: price_MUP,
                    price_SO: price_SO,
                    price_SMO: price_SMO,
                };
                
                // Ajax request
                $.post(HOME_URL+'/product/price/single', data)
                    .done(function(response) {
                    
                        // Increment counter
                        processedRows++;

                        $('#uploadCounter').text(processedRows+'/'+totalRows);
                        $('#upload-meter .meter-value').css('width', ((processedRows/totalRows)*100)+'%');
                    
                        if(processedRows % 10 == 0)
                        {
                            var page = processedRows / 10;
                            table.page(page).draw('page');
                        }    
                    
                        if (response == 'General error') {
                            
                            // Add error class
                            element.removeClass('queue').addClass('error');
                        } else {
                            // Add error class
                            element.removeClass('queue').addClass('updated');
                        }

                        // Append response to the text
                        var text = element.find('.cell-status').text();
                        text += ' (' + response + ')';
                        element.find('.cell-status').text(text);
                    
                        return sendData();
                        
                    })
                    .fail(function() {
                        
                        // Show alert
                        alert('Failed connect to the internet. Please try again.');
                        
                        // Show upload button again
                        $('.btn-green').addClass('hide');
                        return $('#uploadProductPrice').removeClass('hide');
                        
                    });
                
            }
        }
    };
    
    /**
     * Promotor module
     */
    var promotor = {
        
        selectParent: function() {
            
            // Clear selected option
            $('#parent-field').addClass('hide');
            $('#parent-select option').removeClass('show');
            
            if ($('#parent-select').val().length === 0) {
                $('#parent-select option:eq(0)').prop('selected', true);
            }
            
            // Get ID
            var type = $('#promotor-type-select').val();
            // Get data
            var data = $('#parent-data').data('content');

            //Pick selected office data
            var result = '';
            
            switch (type) {
                case 'promotor':
                    result = 'tl';
                    break;
                case 'tl':
                    result = 'arco';
                    break;
                case 'arco':
                    result = 'panasonic';
                    break;
                default: 
                    result = '';
            }

            var selected = data[('type-'+result)];

            if (!selected) {
                return;
            }

            $('#parent-select option').each(function(i, e) {

                if (selected.indexOf(parseInt($(this).attr('value'))) != -1) {
                    $(this).addClass('show');
                }

            });
            
            $('#parent-field').removeClass('hide');

        }
    };
    
    
    /**
     * Report module
     */
    var report = {
        

        /**
         * Expand other value by selecting category
         */
        editSelect: function() {
            
            var type = $('.product_type').val();
            
            // Hide all
            $('.report-field').addClass('hide');
            
            // Show selected
            $('.report-field.'+type).removeClass('hide');
        }
    };

    $(document).ready(function() {
        
        // Attach event
        $('#promotor-type-select').on('change', promotor.selectParent);
        
        $('.chosen-select').chosen();


        // Choose  promotor target by date
        $('.targetDate').on('change', function() {
            
            var type = $(this).data('type');
            var date = $(this).val();
            
            if (date === '0') {
                return;
            }
            
            // Redirect to specific page
            return window.location.href = HOME_URL+'/sales-target?type='+type+'&date='+date;
            
        });

        // Choose  promotor target by date
        $('.reportDate').on('change', function() {
            
            var date = $(this).val();
            var ID   = $(this).data('id');
            
            if (date === '0') {
                return;
            }
            
            // Redirect to specific page
            return window.location.href = HOME_URL+'/report/view?ID='+ID+'&date='+date;
            
        });

        /**
         * Initialize target promotor form
         */
        function initTargetPromotorForm() {
            
            if (!$('#product-type-select').length) {
                return;
            }
            
            $('#product-type-select').append('<option value="">(none)</option>');
            $('#product-model-select').append('<option value="">(none)</option>');
            
            var oldCategory = $('input[name="old_category"]').val();
            
            if (oldCategory !== '') {
                selectProductType(oldCategory);
            }
            
            var oldType = $('input[name="old_type"]').val();
            
            if (oldType !== '') {
                selectProductModel(oldType);
                $('#product-type-select option[value="'+oldType+'"]').attr('selected', 'selected');
            }
            
            var oldModel = $('input[name="old_model"]').val();
            
            if (oldModel !== '') {
                $('#product-model-select option[value="'+oldModel+'"]').attr('selected', 'selected');
            }
        }

        /**
         * Expand product types by selecting product category (target promotor)
         */
        function selectProductType(ID) {
            
            if (!$('#product-types').length) {
                return;
            }
            
            // Get data
            var types   = $('#product-types').data('content');
            var data    = $('#product-type-data').data('content');
            
            // Pick selected product type data
            var selected = data[('product-type-'+ID)];
            
            if (!selected) {
                return;
            }
            
            // Empty product type and model select
            $('#product-type-select').empty();
            $('#product-type-select').append('<option value="">(none)</option>');
            $('#product-model-select').empty();
            $('#product-model-select').append('<option value="">(none)</option>');
            
            for (var a in types) {
                
                var key = parseInt(a);
                
                if (selected.indexOf(key) > -1) {
                    $('#product-type-select').append('<option value="'+a+'">'+types[a]+'</option>');
                }
            }

        }

        /**
         * Expand other value by selecting category
         */
        function selectProductModel(ID) {
            
            if (!$('#product-models').length) {
                return;
            }
            
            // Get data
            var models  = $('#product-models').data('content');
            var data    = $('#product-model-data').data('content');
            
            // Pick selected product data
            var selected = data[('product-'+ID)];
            
            if (!selected) {
                return;
            }
            
            $('#product-model-select').empty();
            $('#product-model-select').append('<option value="">(none)</option>');
     
            
            for (var a in models) {
                
                var key = parseInt(a);
                
                if (selected.indexOf(key) > -1) {
                    $('#product-model-select').append('<option value="'+a+'">'+models[a]+'</option>');
                }
            }

        }

        

        // Init
        initTargetPromotorForm();

        // Attach event
        $('#product-category-select').on('change', function() {
            selectProductType($(this).val());
        });

        $('#product-type-select').on('change', function() {
            selectProductModel($(this).val());
        });
        
         $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 


        // Product 
        $('#uploadProduct').on('click', product.upload);
        $('#uploadProductPrice').on('click', product.uploadPrice);
        
        // Reports
        report.editSelect();
        $(".product_type").on('change', report.editSelect);
    });
    
})(window, window.document, jQuery);
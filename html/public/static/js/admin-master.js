/**
 * Admin Master application script
 */

;(function(window, document, $) {
    
    /**
     * Navigation Bar module
     */
    var navbar = {
        
        // Desktop related function
        desktop: {
            
            // Check if event is allowed to aborted for several reason
            isAbort: function(element) {
                
                // Abort animation if mobile menu button shown
                if ($('#expandNavbar').css('display') === 'block') {
                    return true;
                }
                
                // Abort animation if its already selected
                if (element.hasClass('selected')) {
                    return true;
                }

                return false;

            },
            
            // Expand sub menu on mouseenter
            expand: function() {

                var element     = $(this);
                var icon        = element.find('i');
                
                if (navbar.desktop.isAbort(element)) {
                    return;
                }
                
                icon.removeClass('fa-plus').addClass('fa-caret-right');
                element.addClass('expanded');
            },
            
            // Collapse sub menu on mouse leave
            collapse: function() {
            
                var element     = $(this);
                var icon        = element.find('i');

                if (navbar.desktop.isAbort(element)) {
                    return;
                }

                icon.removeClass('fa-caret-right').addClass('fa-plus');
                element.removeClass('expanded');
            }
            
        },
        
        // Mobile related function
        mobile: {
            
            // Expand main item
            expandMain: function() {

                var target      = $('#navbar > ul');
                var isExpanded  = target.hasClass('expanded'); 

                // Calculate how much item on list

                target.addClass('expanded');

                // If already expanded close it
                if (isExpanded) {
                    target.removeClass('expanded');
                    return target.removeAttr('style');
                }

                navbar.mobile.resizeMain();
            },
            
            // Resize main container
            resizeMain: function() {
                
                var height = $('#navbar > ul > li').length;
                
                // If there is any expanded child item
                // add more height
                if ($('.expandNavbarItem.expanded').length > 0) {
                    height += $('.expandNavbarItem.expanded li').length;
                }
                
                console.log(height);
                
                $('#navbar > ul').css('height', (height * 43)+'px');
                
            },
            
            // Helper function to collapse child
            collapseChild: function(element) {
                
                element.removeClass('expanded');
                element.find('i').removeClass('fa-minus').addClass('fa-plus');
                element.find('ul').css('height', '0px');
                
            },
            
            // Expand child item
            expandChild: function() {

                // Abort animation if mobile menu button hidden
                if ($('#expandNavbar').css('display') === 'none') {
                    return;
                }

                var element = $(this);
                var icon = element.find('i');
                
                // If current item is already expanded, then collapse it
                if (element.hasClass('expanded')) {
                    navbar.mobile.collapseChild(element);
                    return navbar.mobile.resizeMain();
                }

                // Reset all expanded item
                $('.expandNavbarItem').each(function(i, e) {
                    navbar.mobile.collapseChild($(e));
                });

                element.addClass('expanded');
                icon.removeClass('fa-plus').addClass('fa-minus');

                // Calculate height
                var height = element.find('li').length * 43;
                element.find('ul').css('height', height+'px');
                navbar.mobile.resizeMain();
                
            }
        
        }
    };
    
    /**
     * Data table initialization
     */
    
    function initTable() {
        
        // Set element and column width
        var element = $('.initTable');
        var data = {
            'bAutoWidth': false,
            'aoColumns' : []
        };
        
        // Fetch column width from table
        element.find('th').each(function(i, e) {
            data['aoColumns'].push({'sWidth': $(this).data('width')});
        });
        
        // Reordering handle
        if (element.data('reorder')) {
            
            data['rowReorder'] = true;
            
            element.on('row-reorder.dt', function() {

                var sequences = [];

                element.find('tbody tr').each(function(i, e) {
                    sequences.push($(e).data('id'));
                });

                $.ajax({
                    url: element.data('reorder-url'),
                    data: {
                        sequences: sequences
                    },
                    success: function(response) {
                        alert('Re-ordering success.');
                    },
                    error: function() {
                        alert('Oops, error. Re-ordering failed.')
                    }
                });

            });
            
        }
        
        // Init data table
        element.DataTable(data);
    }
    

    $(document).ready(function() {
        
        // Initialize data table
        initTable();
        
        // Initialize date picker
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        // Navigation bar mobile event hook
        $('#expandNavbar').on('click', navbar.mobile.expandMain);
        $('.expandNavbarItem').on('click', navbar.mobile.expandChild);
        
        // Navigation bar desktop event hook
        $('.expandNavbarItem').on('mouseenter', navbar.desktop.expand);
        $('.expandNavbarItem').on('mouseleave', navbar.desktop.collapse);

    });

})(window, window.document, jQuery);
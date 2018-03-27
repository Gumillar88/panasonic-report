/**
 * Application script
 */

;(function(w, doc, $) {
        
    // Set chart width
    $('#chart').css('width', $(window).width() - 300);

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    
    /**
     * Show all tooltips
     */
    Chart.pluginService.register({
        beforeRender: function (chart) {
            if (chart.config.options.showAllTooltips) {
                // create an array of tooltips
                // we can't use the chart tooltip because there is only one tooltip per chart
                chart.pluginTooltips = [];
                chart.config.data.datasets.forEach(function (dataset, i) {
                    chart.getDatasetMeta(i).data.forEach(function (sector, j) {
                        chart.pluginTooltips.push(new Chart.Tooltip({
                            _chart: chart.chart,
                            _chartInstance: chart,
                            _data: chart.data,
                            _options: chart.options.tooltips,
                            _active: [sector]
                        }, chart));
                    });
                });

                // turn off normal tooltips
                chart.options.tooltips.enabled = false;
            }
        },
        afterDraw: function (chart, easing) {

            if (chart.config.options.showAllTooltips) {
                // we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
                if (!chart.allTooltipsOnce) {
                    if (easing !== 1) {
                        return;
                    }
                    chart.allTooltipsOnce = true;
                }

                // turn on tooltips
                chart.options.tooltips.enabled = true;

                Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
                    tooltip.initialize();
                    tooltip.update();
                    // we don't actually need this since we are not animating tooltips
                    tooltip.pivot();
                    tooltip.transition(easing).draw();
                });
                chart.options.tooltips.enabled = false;
            }
        }
    });
    
    /**
     * Chart module
     */
    var chart = {
        
        /**
         * Filter account ID
         */
        accountID: 0,
        dealerID: 0,
        regionID: 0,
        branchID: 0,
        channelID: 0,
        
        /**
         * Time range
         */
        timeType: 'month',

        timeValue: $('meta[name="month"]').attr('content'),
        
        /**
         * Chart container
         */
        totalChart: false,
        productChart: false,
        channelChart: false,

        /**
         * Chart data
         */
        $accountData : [],
        
        /**
         * Initialize chart data
         */
        init: function(callback) {
            
            // Start loading
            $('#chart .loading').removeClass('hide');

            // Set code
            var data = {
                timeType: chart.timeType,
                timeValue: chart.timeValue,
                code: $('meta[name="code"]').attr('content')
            };
            
            // Add region ID
            if (chart.regionID > 0) {
                data.regionID = chart.regionID;
            }
            
            // Add branch ID
            if (chart.branchID > 0) {
                data.branchID = chart.branchID;
            }

            // Add account ID
            if (chart.accountID > 0) {
                data.accountID = chart.accountID;
            }
            
            // Add dealer ID
            if (chart.dealerID > 0) {
                data.dealerID = chart.dealerID;
            }

            // Add channelID
            if (chart.channelID > 0) {
                data.channelID = chart.channelID;
            }

            $.get('/dashboard/chart', data, function(result) {
                
                $('#chart .loading').addClass('hide');

                chart.accountData = result.salesAccount;
                
                // Detect chart data
                if (result.salesTrend.length === 0 && chart.totalChart !== false) {
                    $('#overall-chart-tooltip-0').addClass('hide');
                    chart.totalChart.destroy();
                }
                
                if (result.salesAccount.length === 0 && chart.channelChart !== false) {
                    chart.channelChart.destroy();
                }
                
                if (result.salesProduct.length === 0 && chart.productChart !== false) {
                    $('.sales-detail .product-detail .wrapper').empty();
                    chart.productChart.destroy();
                }
                
                if (result.salesTrend.length === 0) {
                    return alert('No data found based on selected filter.');
                }

                chart.total(result.salesTrend);
                chart.account(result.salesAccount , 3);
                chart.product(result.salesProduct);
                chart.channel(result.salesChannel);
            });
        },
        
        /**
         * Helper class to show custom tooltips on total chart
         */
        showTotalTooltip: function(data) {
            
            // Tooltip Element
            var tooltipEl = $('#overall-chart-tooltip-'+data.index);

            // Calculate value
            var value = 'Rp ' + data.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Show percentage for total sales line
            if (data.index === 0) {

                // Calculate percentage
                var percentage = Math.floor((parseInt(data.value)/data.target)*10000)/100;

                // Append to value
                value += ' (' + percentage + '%)';
            }

            // Set data to tooltip
            tooltipEl.find('.label').text(data.label);
            tooltipEl.find('.value').text(value);

            tooltipEl.removeClass('hide');
            tooltipEl.css({
                left: (data.x - 62) + 'px',
                top: (data.y - 60) + 'px'
            });
        },
        
        /**
         * Show initial total chart tooltip
         */
        showInitialTotalTooltip: function(chartInstance, chartData) {
            
            var chartPos    = chartInstance._chart.canvas.getBoundingClientRect();
            var chartMeta   = chartInstance._chart.controller.getDatasetMeta(0);
            var index       = chartMeta.data.length-1;
            var lastDataPos = chartMeta.data[index]._model;

            chart.showTotalTooltip({
                x: chartPos.left +lastDataPos.x,
                y: chartPos.top +lastDataPos.y,
                label: chartInstance._data.labels[index],
                value: chartInstance._data.datasets[0].data[index],
                target: chartData[0].target,
                index: 0
            });
            
        },
        
        /**
         * Line chart (total sales)
         */
        total: function(trend) {

            var ctx = document.getElementById("sales-chart-total").getContext("2d");
            
            var options = { 
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        id: 'y-axis-0',
                        ticks: {
                            beginAtZero:true,
                            mirror:false,
                            suggestedMin: 0,
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }],
                    xAxes: [{
                        id: 'x-axis-0',
                        ticks: {
                            maxRotation: 0,
                            padding: 10
                        }
                    }]
                },
                tooltips: {
                    enabled: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return JSON.stringify(tooltipItem);
                        }
                    },
                    custom: function(tooltip) {

                        // Define chart position
                        var chartPos = this._chart.canvas.getBoundingClientRect();

                        // Hide all tooltip
                        $('.overall-chart-tooltip').addClass('hide');

                        // If tooltip is hidden show default tooltip
                        if (tooltip.opacity === 0) {

                            chart.showInitialTotalTooltip(this, trend);

                            return;
                        }

                        // Hide tooltip if it has no body
                        if (!tooltip.body) {
                            return;
                        }

                        // Parse data
                        var data = JSON.parse(tooltip.body[0].lines[0]);

                        // Get meta data based on line index
                        var metadata = this._chart.controller.getDatasetMeta(data.datasetIndex);

                        var pointPos = metadata.data[data.index]._model;

                        // Determine location of tooltip
                        var pointPos = {
                            x: chartPos.left + pointPos.x,
                            y: chartPos.top + pointPos.y
                        };

                        chart.showTotalTooltip({
                            x: pointPos.x,
                            y: pointPos.y,
                            label: data.xLabel,
                            value: data.yLabel,
                            target: trend[0].target,
                            index: data.datasetIndex
                        });
                    }
                },
                legend: {
                    position: 'bottom'
                }
            };

            var dataLabels = [];
            var salesTotal = 0;
            var dataValues = {
                total: [],
                trend: [],
                target: []
            };
            
            $.each(trend, function(i, item) {
                
                var salesValue = Math.round(parseInt(item.total)/1000000);
                salesTotal += salesValue;

                dataLabels.push(item.label);
                dataValues.trend.push(salesValue);
                dataValues.total.push(salesTotal);
                dataValues.target.push(item.target);
                
            });

            var data = {
                labels: dataLabels,
                datasets: [
                    {
                        label: "Total Sales",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(15,88,168,0.4)",
                        borderColor: "rgba(15,88,168,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(15,88,168,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: "rgba(15,88,168,1)",
                        pointHoverBorderColor: "rgba(15,88,168,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 5,
                        data: dataValues.total,
                        spanGaps: false,
                    },
                    {
                        label: "Sales Trend",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(67,149,238,0.4)",
                        borderColor: "rgba(67,149,238,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(67,149,238,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: "rgba(67,149,238,1)",
                        pointHoverBorderColor: "rgba(67,149,238,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 5,
                        data: dataValues.trend,
                        spanGaps: false,
                    },
                    {
                        label: "Sales Target",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(242,38,19,0.4)",
                        borderColor: "rgba(242,38,19,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(242,38,19,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: "rgba(242,38,19,1)",
                        pointHoverBorderColor: "rgba(242,38,19,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 5,
                        data: dataValues.target,
                        spanGaps: false,
                    },
                ]
            };
            
            if (chart.totalChart !== false) {
                chart.totalChart.destroy();
            }


            chart.totalChart = new Chart.Line(ctx, {
                data: data,
                options: options
            });
            
            chart.showInitialTotalTooltip(chart.totalChart.tooltip, trend);
        },
        
        /**
         * Sales chart by account
         */
        initAccountTooltip: function(tooltip, data, className) {
            
            // Define parent
            var parent = $('#sales-chart');

            if (className === 'popup-sales-chart-account') {
                parent = $('#popup-sales-chart')
            }
            
            var chartMeta   = tooltip._chart.controller.getDatasetMeta(0);
            
            // Insert tooltip based on data length
            parent.find('.tooltip').empty();
            
            $.each(chartMeta.data, function(i, item) {
                
                // Convert to number format
                var total = Math.round(parseInt(data[i].total)/1000000);
                var value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                
                // Define position
                var pos = {
                    x: item._model.x,
                    y: item._model.y
                };
                
                // Append to tooltip container
                parent.find('.tooltip').append(
                    '<span class="tooltip-item" style="top:'+pos.y+'px;left:'+pos.x+'px;">Rp '+value+'<span>'
                );
                
            })
            
        },
        
        account: function(dataAccount, loop, className = '') {
            
            // Define class type
            if(className === 'popup') {  
                
                $('#popup-sales-chart')
                    .empty()
                    .append('<canvas id="popup-sales-chart-account"></canvas>')
                    .append('<div class="tooltip"></div>');
                
                var className = className+'-sales-chart-account';
                
            } else {
                
                $('#sales-chart')
                    .empty()
                    .append('<canvas id="sales-chart-account"></canvas>')
                    .append('<div class="tooltip"></div>');
                
                var className = 'sales-chart-account';
                
            }
            
            // Define canvas
            var ctx = document.getElementById(className).getContext("2d");
            
            // Define data
            var labels = [];
            var dataValues = [];
            
            $.each(dataAccount, function(i, item) {
                
                if (i < loop) {
                    labels.push(item.name);
                    dataValues.push(Math.round(parseInt(item.total)/1000000));
                }
                
            });
            
            var data = {
                labels: labels,
                datasets: [
                    {
                        label: "My First dataset",
                        backgroundColor: [
                            '#6aedc3',
                            '#f49ff4',
                            '#c0f3f9',
                            '#fccaab',
                            '#8d81ea',
                            '#6ff79c',
                            '#ef89ff',
                            '#b2fff6',
                            '#84cbe8',
                            '#ed6a8b',
                        ],
                        borderColor: [
                            '#6aedc3',
                            '#f49ff4',
                            '#c0f3f9',
                            '#fccaab',
                            '#8d81ea',
                            '#6ff79c',
                            '#ef89ff',
                            '#b2fff6',
                            '#84cbe8',
                            '#ed6a8b',
                        ],
                        borderWidth: 1,
                        data: dataValues
                    }
                ]
            };
            
            // Define options
            var options = { 
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    enabled: false
                },
                scales: {
                    yAxes: [{
                        id: 'y-axis-0',
                        ticks: {
                            beginAtZero:true,
                            mirror:false,
                            suggestedMin: 0,
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }],
                    xAxes: [{
                        id: 'x-axis-0',
                        ticks: {
                            maxRotation: 90,
                            padding: 10
                        }
                    }]
                }
            };
            
            
            // Initialize bar chart
            var barChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: options
            });
            
            // Initialize account tooltip
            chart.initAccountTooltip(barChart.tooltip, dataAccount, className);
        },
        
        /**
         * Product pie chart
         */
        product: function(dataProduct) {
            
            var labels      = [];
            var dataValues  = [];
            
            var colors      = [
                '#ffb5bd',
                '#6aedc3',
                '#f49ff4',
                '#c0f3f9',
                '#fccaab',
                '#8d81ea',
                '#6ff79c',
                '#ef89ff',
                '#b2fff6',
                '#84cbe8',
                '#ed6a8b',
                '#6df299',
                '#a8ff9b',
                '#c5c4ff',
                '#a0ffd4',
                '#fcbab5',
                '#a0f1f7',
                '#f97b70',
                '#76f2cf',
                '#bdc5fc',
                '#b172f9',
                '#c098ea',
                '#ed8bd4'
            ];
            var dataTotal   = 0;
            
            $.each(dataProduct, function(i, item) {
                dataTotal += parseInt(item.total);
            });
            
            $.each(dataProduct, function(i, item) {
                labels.push(item.name);
                dataValues.push(Math.floor((parseInt(item.total)/dataTotal)*10000)/100);
                
                // Set color
                dataProduct[i].color = colors[i];
            });
            
            var options = { 
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                    position: 'right',
                },
                pointHitDetectionRadius : 1,
                tooltips: {
                    backgroundColor: 'rgba(0,0,0,0)',
                    xPadding: -90,
                    yPadding: 0,
                    caretSize: 0,
                    bodyFontSize: 10,
                    bodyFontColor: '#000',
                    bodyFontStyle: 'bold',
                    callbacks: {
                      label: function(tooltipItem, data) {
                          var label = data.labels[tooltipItem.index];
                          var value = data.datasets[0].data[tooltipItem.index];
                          
                          if (value < 7) {
                              return;
                          }
                          
                          return [label+' '+value+'%'];
                        // Return string from this function. You know the datasetIndex and the data index from the tooltip item. You could compute the percentage here and attach it to the string.
                      }
                    }
                },
                showAllTooltips: true,
            };
            
            var data = {
                labels: labels,
                datasets: [
                    {
                        data: dataValues,
                        backgroundColor: colors,
                        hoverBackgroundColor: colors
                    }
                ]
            };
            
            
            var ctx = document.getElementById("sales-chart-product").getContext("2d");

            if (chart.productChart !== false) {
                chart.productChart.destroy();
            }

            
            // For a pie chart
            chart.productChart = new Chart(ctx,{
                type: 'pie',
                data: data,
                options: options
            });
            
            // Generate table 
            $('#chart .product-detail .wrapper').empty();
            
            var html = '';
            
            html += '<table>';
            
            $.each(dataProduct, function(i, item) {
                
                if (!(i%2)) {
                    
                    html += '<tr>';
                    
                    html += '\
                        <td class="item" data-id="'+item.ID+'"> \
                            <span class="color" style="background:'+item.color+'"></span> \
                            <div class="category"><strong>'+item.name+'</strong></div> \
                            <div class="value">Rp. '+Math.round(parseInt(item.total)/1000000)+' ('+item.qty+' qty) </div> \
                        </td>';
                    
                } else {
                    
                    html += '\
                        <td class="item" data-id="'+item.ID+'">\
                            <span class="color" style="background:'+item.color+'"></span> \
                            <div class="category" ><strong>'+item.name+'</strong></div> \
                            <div class="value">Rp. '+Math.round(parseInt(item.total)/1000000)+' ('+item.qty+' qty)</div> \
                        </td>';
                    
                    html += '</tr>';
                    
                }
            });
            
            $('#chart .product-detail .wrapper').append(html);
            $('#title-product').text("Sales by Products");
            
        },

        product_detail: function(name){

            // Start loading
            $('#chart .loading').removeClass('hide');

            // Set code
            var data = {
                timeType: chart.timeType,
                timeValue: chart.timeValue,
                category_ID: name,
                code: $('meta[name="code"]').attr('content')
            };

            // Add region ID
            if (chart.regionID > 0) {
                data.regionID = chart.regionID;
            }

            // Add branch ID
            if (chart.branchID > 0) {
                data.branchID = chart.branchID;
            }

            // Add channelID
            if (chart.channelID > 0) {
                data.channelID = chart.channelID;
            }
            
            // Add account ID
            if (chart.accountID > 0) {
                data.accountID = chart.accountID;
            }
            
            if (chart.dealerID > 0) {
                data.dealerID = chart.dealerID;
            }
            
            $.get('/dashboard/product', data, function(result) {
                
                $('#chart .loading').addClass('hide');

                chart.product_detail_render(result);
            });
        },

        product_detail_render: function(dataProduct) {
            
            var labels      = [];
            var dataValues  = [];
            var colors      = [
                '#ffb5bd',
                '#6aedc3',
                '#f49ff4',
                '#c0f3f9',
                '#fccaab',
                '#8d81ea',
                '#6ff79c',
                '#ef89ff',
                '#b2fff6',
                '#84cbe8',
                '#ed6a8b',
                '#6df299',
                '#a8ff9b',
                '#c5c4ff',
                '#a0ffd4',
                '#fcbab5',
                '#a0f1f7',
                '#f97b70',
                '#76f2cf',
                '#bdc5fc',
                '#b172f9',
                '#c098ea',
                '#ed8bd4'
            ];
            var dataTotal   = 0;
            
            $.each(dataProduct, function(i, item) {
                
                dataTotal += parseInt(item.total);
            });

            var limit = 5;
            $.each(dataProduct, function(i, item) {
                labels.push(item.name);
                if(limit != 0 )
                {
                    dataValues.push(Math.floor((parseInt(item.total)/dataTotal)*10000)/100);
                    limit--;
                }
                
                // Set color
                dataProduct[i].color = colors[i];
            });
            
            var options = { 
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                    position: 'bottom'
                },
                pointHitDetectionRadius : 1,
                tooltips: {
                    backgroundColor: 'rgba(0,0,0,0)',
                    xPadding: -90,
                    yPadding: 0,
                    caretSize: 0,
                    bodyFontSize: 10,
                    bodyFontColor: '#000',
                    bodyFontStyle: 'bold',
                    callbacks: {
                      label: function(tooltipItem, data) {
                          var label = data.labels[tooltipItem.index];
                          var value = data.datasets[0].data[tooltipItem.index];
                          
                          if (value < 7) {
                              return;
                          }
                          
                          return [label+' '+value+'%'];
                        // Return string from this function. You know the datasetIndex and the data index from the tooltip item. You could compute the percentage here and attach it to the string.
                      }
                    }
                },
                showAllTooltips: true,
            };
            
            var data = {
                labels: labels,
                datasets: [
                    {
                        data: dataValues,
                        backgroundColor: colors,
                        hoverBackgroundColor: colors
                    }
                ]
            };
            
            
            var ctx = document.getElementById("sales-chart-product").getContext("2d");

            if (chart.productChart !== false) {
                chart.productChart.destroy();
            }

            
            // For a pie chart
            chart.productChart = new Chart(ctx,{
                type: 'pie',
                data: data,
                options: options
            });
            
            // Generate table 
            $('#chart .product-detail .wrapper').empty();
            
            var html = '';
            
            html += '<button id="back"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button><table>';
            
            $.each(dataProduct, function(i, item) {
                
                if (!(i%2)) {
                    
                    html += '<tr>';
                    
                    html += '\
                        <td> \
                            <span class="color" style="background:'+item.color+'"></span> \
                            <div class="category"><strong>'+item.name+'</strong></div> \
                            <div class="value">Rp. '+Math.round(parseInt(item.total)/1000000)+' ('+item.qty+' qty)</div> \
                        </td>';
                    
                } else {
                    
                    html += '\
                        <td> \
                            <span class="color" style="background:'+item.color+'"></span> \
                            <divclass="category"><strong>'+item.name+'</strong></div> \
                            <div class="value">Rp. '+Math.round(parseInt(item.total)/1000000)+' ('+item.qty+' qty)</div> \
                        </td>';
                    
                    html += '</tr>';
                    
                }
            });
            
            $('#chart .product-detail .wrapper').append(html);

            $('#title-product').text("Sales by Product Category");
            
        },

        channel: function(dataChannel) {
            
            var labels      = [];
            var dataValues  = [];
            var colors      = [
                '#6aedc3',
                '#f49ff4',
                '#c0f3f9',
            ];
            var dataTotal   = 0;
            
            $.each(dataChannel, function(i, item) {
                dataTotal += parseInt(item.total);
            });
            
            $.each(dataChannel, function(i, item) {
                labels.push(item.name);
                dataValues.push(Math.floor((parseInt(item.total)/dataTotal)*10000)/100);
                
                // Set color
                dataChannel[i].color = colors[i];
            });
            
            var options = { 
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                    position: 'bottom'
                },
                pointHitDetectionRadius : 1,
                tooltips: {
                    backgroundColor: 'rgba(0,0,0,0)',
                    xPadding: -10,
                    yPadding: 0,
                    caretSize: 0,
                    bodyFontSize: 11,
                    bodyFontColor: '#000',
                    bodyFontStyle: 'bold',
                    callbacks: {
                      label: function(tooltipItem, data) {
                          var label = data.labels[tooltipItem.index];
                          var value = data.datasets[0].data[tooltipItem.index];
                          
                          if (value < 7) {
                              return;
                          }
                          
                          return [label, value+'%'];
                        // Return string from this function. You know the datasetIndex and the data index from the tooltip item. You could compute the percentage here and attach it to the string.
                      }
                    }
                },
                showAllTooltips: true,
            };
            
            var data = {
                labels: labels,
                datasets: [
                    {
                        data: dataValues,
                        backgroundColor: colors,
                        hoverBackgroundColor: colors
                    }
                ]
            };
            
            
            var ctx = document.getElementById("sales-chart-channel").getContext("2d");

            if (chart.channelChart !== false) {
                chart.channelChart.destroy();
            }

            
            // For a pie chart
            chart.channelChart = new Chart(ctx,{
                type: 'pie',
                data: data,
                options: options
            });
            
        },
    }
    
    /**
     * Detail menu
     */
    var detail = {
        
        /**
         * Open detail menu
         */
        open: function() {
            
            $('#detail .content.expanded')
                .removeClass('expanded')
                .css('height', '56px');
            
            $('#detail .content.expanded')
                .find('.list')
                .css('height', '0px');
            
            // Open the menu
            $(this).parents('.content').addClass('expanded');
            
            // Set height
            var contentHeight = $('#detail').height() - 190;
            $(this).parents('.content').css('height', contentHeight - 42);
            $(this).parents('.content').find('.list').css('height', (contentHeight - 82));

            detail.stock();
        },
        
        /**
         * Close detail menu
         */
        close: function() {
            
            $(this)
                .parents('.content')
                .removeClass('expanded')
                .css('height', '56px');
            
            $(this)
                .parents('.content')
                .find('.list')
                .css('height', '0px');
        },

        /**
         * Render data to template
         */
        render: function(data) {
            
            // Empty container
            $('#empty-stock .list').empty();
            
            // Generate html
            var html = '';

            for (var a in data) {
                html += '<div class="item" id="dealer-account" data-id="'+a+'"><p class="title">'+a+'</p>';
                html += '<div class="hide" id="dealer" data-id="'+a+'">';
                for(var item in data[a]) {
                    html += '<div class="title">'+item+'</div>';
                    html += '<ul>';

                    for(var product in data[a][item]) 
                    {
                        html += '<li>'+data[a][item][product]+'</li>';
                    }
                    
                    html += '</ul>';

                }
                html += '</div>';
                html += '</div>';


            };

            if(Object.getOwnPropertyNames(data).length === 0)
            {
                html = 'There is no empty stock report.';
            }
            
            
            $('#empty-stock .list').append(html);
        },
        
        /**
         * Show loading 
         */
        loading: function(type) {
            
            if (type === 'show') {
                $('#empty-stock .loading').removeClass('hide');
                $('#empty-stock .list').addClass('hide');
            }
            
            if (type === 'hide') {
                $('#empty-stock .loading').addClass('hide');
                $('#empty-stock .list').removeClass('hide');
            }
        },
        
        /**
         * Get account data
         */
        stock: function() {
            
            // Show loading
            detail.loading('show');
            
            // Set code
            var data = {
                code: $('meta[name="code"]').attr('content')
            };
            
            $.get('/dashboard/stock', data, function(result) {
                detail.loading('hide');
                detail.render(result);
            });
            
        },
    };
    
    /**
     * Chart filter
     */
    var chartFilter = {
        
        // Select type of time filter
        timeType: function() {
            
            if ($(this).val() !== '') {
                chart.timeType = $(this).val();
            }

            $('.filter-time').addClass('hide');
            chart.timeValue = $('#filter-time-'+chart.timeType+' option:first').val();
            $('#filter-time-'+chart.timeType).removeClass('hide');
            
        },
        
        // Select month of time filter
        timeValue: function() {
            
            if ($(this).val() !== '') {
                chart.timeValue = $(this).val();
            }
        },

        branch: function() {

            // Remove value
            chart.channelID = 0;
            chart.accountID = 0;
            chart.dealerID = 0;

            // Clear selected option
            $('#channel-select option').removeClass('show');
            $('#channel-select option:eq(0)').prop('selected', true);

            $('#dealer_account-select option').removeClass('show');
            $('#dealer_account-select option:eq(0)').prop('selected', true);

            $('#dealer-select option').removeClass('show');
            $('#dealer-select option:eq(0)').prop('selected', true);
            
            $('#channel').removeClass('hide');
            $('#dealer_account').addClass('hide');
            $('#dealer').addClass('hide');

             // Get ID
            var ID = $('#branch-select').val();
            chart.branchID = ID;
            
        },

        region: function() {
            
            // Remove value
            chart.branchID = 0;
            chart.channelID = 0;
            chart.accountID = 0;
            chart.dealerID = 0;

            // Clear selected option
            $('#branch-select option').removeClass('show');
            $('#branch-select option:eq(0)').prop('selected', true);

            $('#channel-select option').removeClass('show');
            $('#channel-select option:eq(0)').prop('selected', true);

            $('#dealer_account-select option').removeClass('show');
            $('#dealer_account-select option:eq(0)').prop('selected', true);

            $('#dealer-select option').removeClass('show');
            $('#dealer-select option:eq(0)').prop('selected', true);
            
            $('#channel').removeClass('hide');
            $('#dealer_account').addClass('hide');
            $('#dealer').addClass('hide');
            $('#branch').removeClass('hide');

             // Get ID
            var ID = $('#region-select').val();
            chart.regionID = ID;

            if (ID == 0) {
                $('#channel').addClass('hide');
                $('#dealer_account').addClass('hide');
                $('#dealer').addClass('hide');
                $('#branch').addClass('hide');
                return;
            }

            // Get data
            var data = $('#branch-data').data('content');

            // Pick selected region data
            var selected = data[('region-'+chart.regionID)];
            
            if (selected) {
            
                $('#branch').removeClass('hide');

                $('#branch-select option').each(function(i, e) {

                    if (selected.indexOf(parseInt($(this).attr('value'))) !== -1) {

                        $(this).addClass('show');
                    }

                });

                $('#branch-select option:eq(0)').addClass('show').prop('selected', true);
                
                return;
            }
            
        },

        channel : function() {
            //set value
            chart.accountID = 0;
            chart.dealerID = 0;

            // Clear selected option
            $('#dealer_account-select option').removeClass('show');
            $('#dealer_account-select option:eq(0)').prop('selected', true);

            $('#dealer-select option').removeClass('show');
            $('#dealer-select option:eq(0)').prop('selected', true);
            
            $('#dealer').addClass('hide');
            $('#dealer_account').addClass('hide');

            var ID = $('#channel-select').val();
            chart.channelID = ID;
            
            if (ID == 0) {
                return;
            }

            // Get data
            var data = $('#dealer-channel-data').data('content');
            
            // Pick selected region data
            var selected = data[('branch-'+chart.branchID+'-channel-'+ID)];
            
            if (selected) {
            
                $('#dealer_account').removeClass('hide');

                $('#dealer_account-select option').each(function(i, e) {

                    if (selected.indexOf(parseInt($(this).attr('value'))) !== -1) {
                        $(this).addClass('show');
                    }

                });

                $('#dealer_account-select option:eq(0)').addClass('show').prop('selected', true);
                chart.accountID = 0;
                
                return;
            }
            
            var data     = $('#dealer-data').data('content');
            var selected = data[('branch-'+chart.branchID+'-channel-'+chart.channelID)];
            
            $('#dealer').removeClass('hide');
            
            if (!selected) {
                $('#dealer-select option:eq(1)').addClass('show').prop('selected', true);
                return;
            }

            $('#dealer-select option').each(function(i, e) {
                    
                if (selected.indexOf(parseInt($(this).attr('value'))) !== -1) {
                    $(this).addClass('show');
                }

            });
        },

        dealerAccount : function() {

            // Remove value
            chart.dealerID = 0;

            // Clear selected option
            $('#dealer-select option').removeClass('show');
            $('#dealer-select option:eq(0)').prop('selected', true);

            $('#dealer').addClass('hide');

             // Get ID
            var ID = $('#dealer_account-select').val();
            chart.accountID = ID;

            // Get data
            var data = $('#dealer-data').data('content');

            // Pick selected region data
            var selected = data[('branch-'+chart.branchID+'-channel-'+chart.channelID+'-account-'+ID)];

            $('#dealer').removeClass('hide');
            
            if (!selected) {
                $('#dealer-select option:eq(-1)').addClass('show').prop('selected', true);
                return;
            }

            $('#dealer-select option').each(function(i, e) {
                    
                if (selected.indexOf(parseInt($(this).attr('value'))) !== -1) {
                    $(this).addClass('show');
                }

            });
            $('#dealer-select option:eq(0)').addClass('show').prop('selected', true);
            chart.dealerID = 0;

        },

        dealer : function() {
            var dealerID = $('#dealer-select').val();
            chart.dealerID = dealerID;
        },

    };
    
    /**
     * Sidebar explore
     */
    var explore = {
        
        /**
         * Render data to template
         */
        render: function(data) {
            
            // Generate html
            var html = '';
            
            if(data.male)
            {
                // Empty container
                $('#explore .list').empty();
                html += '<div class="item"> \
                            <div class="title">Total Promotor : '+data.total+'</div></br> \
                            <div class="title">Overview</div> \
                            <div class="gender"><i class="fa fa-mars" aria-hidden="true"></i> : '+data.male+' ('+data.persentase_male+'%)</div> \
                            <div class="gender"><i class="fa fa-venus" aria-hidden="true"></i> : '+data.female+' ('+data.persentase_female+'%)</div><div class="clear"></div>';
            }else
            {

                for (var a in data) {
                    html += '<div class="item">';
                    html += '<div class="title">'+a+'</div>';
                    html += '<div class="gender"><i class="fa fa-mars" aria-hidden="true"></i> : '+data[a].male+' ('+data[a].persentase_male+'%)</div>';
                    html += '<div class="gender"><i class="fa fa-venus" aria-hidden="true"></i> : '+data[a].female+' ('+data[a].persentase_female+'%)</div><div class="clear"></div></div>';
                };
            }

            html += '</div>';
            
            $('#explore .list').append(html);
        },
        
        /**
         * Show loading 
         */
        loading: function(type) {
            
            if (type === 'show') {
                $('#explore .loading').removeClass('hide');
                $('#explore .list').addClass('hide');
            }
            
            if (type === 'hide') {
                $('#explore .loading').addClass('hide');
                $('#explore .list').removeClass('hide');
            }
        },
        
        /**
         * Back button
         */
        back: function() {
            
            if ($('#explore.store').length) {
                return explore.account();
            }
            
            if ($('#explore.promoter').length) {
                return explore.store($(this).data('id'));
            }
        },
        
        /**
         * Get gender data
         */
        gender: function() {
            
            // Show loading
            explore.loading('show');
            
            // Set code
            var data = {
                code: $('meta[name="code"]').attr('content')
            };
            
            $.get('/dashboard/data/gender', data, function(result) {
                explore.render(result.gender);
                explore.render(result.genderBranch);
                explore.loading('hide');
                $('#explore')
                    .addClass('account')
                    .removeClass('store')
                    .removeClass('promoter');
            });
            
        },

        /**
         * Get explore data
         */
        explore: function() {
            
            // Show loading
            $('#table-detail').empty();
            
            // Set code
            var data = {
                timeType: chart.timeType,
                timeValue: chart.timeValue,
                code: $('meta[name="code"]').attr('content')
            };

            // Add region ID
            if (chart.regionID > 0) {
                data.regionID = chart.regionID;
            }
            
            // Add branch ID
            if (chart.branchID > 0) {
                data.branchID = chart.branchID;
            }
            
            // Add account ID
            if (chart.accountID > 0) {
                data.accountID = chart.accountID;
            }
            
            // Add dealer ID
            if (chart.dealerID > 0) {
                data.dealerID = chart.dealerID;
            }

            // Add channelID
            if (chart.channelID > 0) {
                data.channelID = chart.channelID;
            }
            
            $.get('/dashboard/data/explore', data, function(result) {
                $('#table-detail').append(result.sales);
            });
            
        },
    }
    
    /**
     * Report download module
     */
    var report = {

        /**
         * Filter account ID & month
         */
        accountID: 0,
        timeValue: 0,
        
        downloadHelper: function(code, month, type, ID) {
            
            type = type || false;
            ID = ID || false;
            
            var url = '/dashboard/download?month='+month+'&code='+code;
            
            if (type === 'branch' && ID !== false) {
                url = '/dashboard/download?month='+month+'&type=branch&branchID='+ID+'&code='+code;
            }

            if (type === 'region' && ID !== false) {
                url = '/dashboard/download?month='+month+'&type=region&regionID='+ID+'&code='+code;
            }
            
            if (type === 'account' && ID !== false) {
                url = '/dashboard/download?month='+month+'&type=account&accountID='+ID+'&code='+code;
            }
            
            if (type === 'account-all' && ID !== false) {
                url = '/dashboard/download?month='+month+'&type=account-all&accountID='+ID+'&code='+code;
            }
            
            $('body').append('<iframe id="download-excel-'+month+'" style="display:none;" src="'+url+'"></iframe>');

            setTimeout(function() {
                $('download-excel-'+month).remove();
            }, 5000);
        },

        download: function(){

            // Set code
            var data = {
                month: $('#popup-filter-time-month').val(),
                code: $('meta[name="code"]').attr('content'),
                type: $('#popup-filter-type').val(),
                branchID: parseInt($('#popup-filter-branch').val()),
                regionID: parseInt($('#popup-filter-region').val()),
                accountID: parseInt($('#popup-filter-account').val()),
                accountName: $('#popup-filter-account-all').val()
            };
            
            for (var a = 0; a < data.month.length; a++) {
                
                // Add abranch ID
                if (data.type === 'branch' && data.branchID > 0) {
                    
                    (function(a) {
                        report.downloadHelper(data.code, data.month[a], data.type, data.branchID);
                    })(a);
                    
                } else if (data.type === 'region' && data.regionID > 0) {
                    
                    (function(a) {
                        report.downloadHelper(data.code, data.month[a], data.type, data.regionID);
                    })(a);
                    
                } else if (data.type === 'account' && data.accountID > 0) {
                    
                    (function(a) {
                        report.downloadHelper(data.code, data.month[a], data.type, data.accountID);
                    })(a);
                    
                } else if (data.type === 'account-all' && data.accountName != '') {
                    
                    (function(a) {
                        report.downloadHelper(data.code, data.month[a], data.type, data.accountName);
                    })(a);
                    
                }  else {
                    
                    (function(a) {
                        report.downloadHelper(data.code, data.month[a]);
                    })(a);
                    
                }
            }
        }
    };
    
    /**
     * Competitor report module
     */
    var competitor = {
        
        /**
         * Table container
         */
        table: false,
        
        // Data container
        data: {
            type: '',
            value: '',
            brandID: ''
        },
        
        /**
         * Handle back button event
         */
        back: function() {
            
            // Define action
            var action = $(this).data('action');
            
            // Action from competitor type list
            if (action === 'open-type-list') {
                
                // Get type
                var type = $(this).data('type');
                
                // Reset back to normal
                $('#competitor-type').removeClass('hide');
                $('#competitor-list-'+type).addClass('hide');
                $(this).addClass('hide');
                competitor.data.type = '';
                
                return;
            }
            
            // Action from competitor view list
            if (action === 'open-view-list') {
                
                var type = competitor.data.type;
                
                $('#competitor-brand').addClass('hide');
                $('#competitor-list-'+type).removeClass('hide');
                
                competitor.data.value  = '';
                
                
                $('#competitor .back').data('type', type);
                $('#competitor .back').data('action', 'open-type-list');
                
                return;
            }
            
        },
        
        /**
         * Open competitor type
         */
        openType: function() {
            
            // Get type
            var type = $(this).data('type');
            
            $('#competitor .back').data('type', type);
            $('#competitor .back').data('action', 'open-type-list');
            $('#competitor .back').removeClass('hide');
            
            $('#competitor-type').addClass('hide');
            $('#competitor-list-'+type).removeClass('hide');
            
            competitor.data.type = type;
            
        },
        
        openList: function() {
            
            var value = $(this).data('value');
            competitor.data.value = value;
            
            $('#competitor .back').data('action', 'open-view-list');
            $(this).parents('.view-list').addClass('hide');
            $('#competitor-brand').removeClass('hide');
            
        },
        
        openDetail: function() {
            
            // Show loading
            if (competitor.table != false) {
                competitor.table.destroy();
            }
            
            $('#table-competitor').empty();
            $('#div-competitor').empty();
            $('#popup-competitor').removeClass('hide');
            
            // Set code
            var data = {
                month: chart.timeValue,
                type: competitor.data.type,
                value: competitor.data.value,
                brandID: $(this).data('id'),
                code: $('meta[name="code"]').attr('content')
            };
            
            $.get('/dashboard/competitor', data, function(result) {
                
                $('#div-competitor').append(result);
                competitor.table = $('#table-competitor').DataTable();
            });
            
        },

        download: function(){

            // Set code
            var data = {
                month: chart.timeValue,
                type: competitor.data.type,
                value: competitor.data.value,
                brandID: $('#id').val(),
                code: $('meta[name="code"]').attr('content')
            };

            var url = '/dashboard/download/competitor?month='+data['month']+'&code='+data['code']+'&type='+data['type']+'&value='+data['value']+'&brandID='+data['brandID'];
            
            $('body').append('<iframe id="download-excel-'+data['month']+'" style="display:none;" src="'+url+'"></iframe>');

            setTimeout(function() {
                $('download-excel-'+data['month']).remove();
            }, 5000);
        }

        
    };
    
    $(doc).on('ready', function() {
        
        // Get selected month
        chart.init();
        
        
        // Detail menu
        $('#detail .openMenu').on('click', detail.open);
        $('#detail .closeMenu').on('click', detail.close);
        
        $('#explore .openMenu').on('click',  explore.gender);
        $(document).on('click', '#empty-stock .list #dealer-account', function() {
            
            var data = $(this).data("id");
            var html = '';
            
            if($(this).data('id') == data)
            {
                $('#empty-stock .list #dealer-account').addClass('hide');

                if($(this).find("#dealer").data('id') == data)
                {   
                    $('#empty-stock-back').removeClass('hide');
                    $(this).addClass('hide');
                    $('#result-empty-stock').remove();

                    html = '<div id="result-empty-stock">';
                    html += $(this).html();
                    var result = html.replace("hide","show");
                    html += '</div>';
                    $('#empty-stock .list').append(result);
                }
            }
            
        });

        $('#empty-stock-back').on('click',function(){
            $('#result-empty-stock').remove();
            $('#empty-stock-back').addClass('hide');

            $('#empty-stock .list #dealer-account').removeClass('hide');
        });

        //table product
        $(document).on('click', '.product-detail .wrapper .item', function() {  
            return chart.product_detail($(this).data('id'));
        });

        //back table product
        $(document).on('click', '.product-detail .wrapper #back', function() {
            chart.init();
        });


        /**
         * Popup utility
         */
        $('.popup-open').on('click', function() {
            
            var name = $(this).data('popup');
            $('#popup-'+name).removeClass('hide');
            
            // Activate chosen for download report
            if (name === 'download') {
                $('.chosen').chosen();
            }
        });

        $('.popup-close, .popup-background').on('click', function(){
            $(this).parents('.popup-wrapper').addClass('hide');
        });
        
        // Download report
        $('#report-download').on('click', report.download);
        
        // View promotor detail data
        $('#view-detail').on('click', explore.explore);

        /**
         * Download report filtering
         */
        $('#popup-filter-type').on('change', function() {
            
            // Hide all field
            $('.popup-filter.type').addClass('hide');
            
            // Get value and show it
            $('.popup-filter.type.'+$(this).val()).removeClass('hide');
            
        });


        /**
         * Chart filtering
        */
       
        $('#button-filter').on('click', function(){
             $('#popup-filter').removeClass('hide');
        });


        $('#region-select').on('change', chartFilter.region);
        $('#branch-select').on('change', chartFilter.branch);
        $('#dealer_account-select').on('change', chartFilter.dealerAccount);
        $('#dealer-select').on('change', chartFilter.dealer);
        $('#channel-select').on('change', chartFilter.channel);

        $('#filter-time-type').on('change', chartFilter.timeType);

        $('#filter-time-month').on('change', chartFilter.timeValue);
        $('#filter-time-quarter').on('change', chartFilter.timeValue);
        $('#filter-time-semester').on('change', chartFilter.timeValue);
        $('#filter-time-year').on('change', chartFilter.timeValue);


        $('#filter-search').on('click', function() {
            $('#popup-filter').addClass('hide');
            chart.init();
        });
        
        /**
         * View account chart
         */
        $('#view-account-chart').on('click', function(){
             chart.account(chart.accountData, 10, 'popup');
        });
        
        /**
         * Competitor price event handle
         */
        $('#competitor .back').on('click', competitor.back);
        $('#competitor-type .item').on('click', competitor.openType);
        $('#competitor .view-list .item').on('click', competitor.openList);
        $('#competitor-brand .item').on('click', competitor.openDetail);
        $(document).on('click', '#competitor-report-download', competitor.download);

        /**
         * Start interval to refresh dashboard every hour
         */
        setInterval(function() {
            chart.init();
        }, 3600000);
    });
    
})(window, document, jQuery);
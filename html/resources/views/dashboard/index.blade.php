<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="shortcut icon" href="{{ env('APP_HOME_URL') }}/static/img/favicon.ico" />

		<title>Dashboard - Panasonic Report</title>
		<meta name="description" content="Havas Worldwide Jakarta" />
		<meta name="robots" content="nofollow" />
		<meta name="copyright" content="Havas Worldwide Jakarta 2015" />
		<meta name="home_url" content="{{ env('APP_HOME_URL') }}" />

        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400">
        <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/lib.css" />
        <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/chosen.min.css" />
	    <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/dashboard.css?t={{ time() }}" />
        <meta name="code" content="{{ $code }}" />
        <meta name="month" content="{{ date('Y-m') }}" />
        <meta charset="UTF-8">
	</head>
	<body>
        <div id="container">
            <div id="chart">
                <div id="dealer-channel-data" class="hide" data-content="{{ $dealerChannelData }}"></div>
                <div id="dealer-data" class="hide" data-content="{{ $dealerData }}"></div>
                <div id="branch-data" class="hide" data-content="{{ $branchData }}"></div>
                
                <div class="loading">
                    <i class="fa fa-cog fa-spin"></i>
                </div>
                
                <section class="sales-total">
                    <h1>Overall</h1>
                    <div class="options">
                        <a class="btn popup-open" data-popup="filter">Filter</a>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="sales-chart-total"></canvas>
                        <span>(* All values in million Rupiah</span>
                        
                        <div id="overall-chart-tooltip-0" class="overall-chart-tooltip hide">
                            <div class="label"></div>
                            <div class="value"></div>
                        </div>
                        <div id="overall-chart-tooltip-1" class="overall-chart-tooltip hide">
                            <div class="label"></div>
                            <div class="value"></div>
                        </div>
                        <div id="overall-chart-tooltip-2" class="overall-chart-tooltip hide">
                            <div class="label"></div>
                            <div class="value"></div>
                        </div>
                        
                    </div>
                </section>
                <section class="sales-detail">
                    <div class="part account-chart">
                        <h2>Sales by Accounts</h2>
                        <div class="chart-wrapper">
                            <div id="sales-chart">
                                <canvas id="sales-chart-account"></canvas>
                            </div>
                            <span class="left">(* All values in million Rupiah</span>
                            <a id="view-account-chart" class="btn popup-open" data-popup="account">
                                Show More
                            </a>
                        </div>
                    </div>
                    <div class="part product-chart">
                        <h2 id="title-product">Sales by Products</h2>
                        <div class="chart-wrapper">
                            <canvas id="sales-chart-product"></canvas>
                        </div>
                        
                        <h2 id="title-channel">Sales by Channel</h2>
                        <div class="chart-wrapper">
                            <canvas id="sales-chart-channel"></canvas>
                        </div>
                    </div>
                    
                    <div class="part product-detail">
                        <div class="wrapper"></div>
                        <span class="note">(* All values in million Rupiah</span>
                    </div>
                </section>
            </div>
            <div id="detail">
                <div class="header">
                    <img src="{{ env('APP_HOME_URL') }}/static/img/logo.png" />
                    <h1>Promoter Report Dashboard</h1>
                </div>
                
                <div class="text-center">
                    <a class="btn popup-open" data-popup="download">Download Report</a>
                    <a id="view-detail" class="btn popup-open" data-popup="detail">Detail</a>
                </div>

                <div id="empty-stock" class="content">
                    <h2><button id="empty-stock-back" class="hide"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>Empty Stock 
                        <i class="fa fa-plus openMenu"></i>
                        <i class="fa fa-minus closeMenu"></i>
                    </h2>
                    
                    <div class="loading">
                        <i class="fa fa-cog fa-spin"></i>
                    </div>

                    <div class="list">
                    </div>
                </div>
                
                <div id="explore" class="content">
                    <h2>
                        Gender Statistic 
                        <i class="fa fa-plus openMenu"></i>
                        <i class="fa fa-minus closeMenu"></i>
                    </h2>
                    
                    <div class="loading">
                        <i class="fa fa-cog fa-spin"></i>
                    </div>
                    
                    <div class="list">
                    </div>
                </div>
                
                <div id="competitor" class="content">
                    <h2>
                        <i class="fa fa-arrow-left back hide"></i>
                        Competitor Report
                        <i class="fa fa-plus openMenu"></i>
                        <i class="fa fa-minus closeMenu"></i>
                    </h2>
                    
                    <div id="competitor-type" class="list">
                        <div class="item" data-type="branch">
                            <p class="title">Branch</p>
                        </div>
                        
                        <div class="item" data-type="account">
                            <p class="title">Account (Jakarta)</p>
                        </div>
                        
                        <div class="item" data-type="account-all">
                            <p class="title">Account (Nationwide)</p>
                        </div>
                    </div>
                    
                    <div id="competitor-list-branch" class="list hide view-list">
                        @foreach($branches as $ID => $name)
                            @if ($ID > 0)
                                <div class="item" data-value="{{ $ID }}">
                                    <p class="title">{{ $name }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <div id="competitor-list-account" class="list hide view-list">
                        @foreach($accounts as $ID => $name)
                            <div class="item" data-value="{{ $ID }}">
                                <p class="title">{{ $name }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div id="competitor-list-account-all" class="list hide view-list">
                        @foreach($accountAllNation as $name)
                            <div class="item" data-value="{{ str_replace(' ', '-', strtolower($name)) }}">
                                <p class="title">{{ $name }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div id="competitor-brand" class="list hide">
                        @foreach($brands as $ID => $name)
                            <div class="item" data-id="{{ $ID }}">
                                <p class="title">{{ $name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="popup-download" class="popup-wrapper hide">
            <div class="popup-background"></div>
            <div class="container">
                <button class="popup-close"><i class="fa fa-times"></i></button>
                <h2>Download Report</h2>
                <div class="popup-filter">
                    <label>Type</label>
                    <select id="popup-filter-type" class="form-control" autocomplete="off">
                        <option value="region" selected>Region</option>
                        <option value="branch">Branch</option>
                        <option value="account">Account (Jakarta SO)</option>
                        <option value="account-all">Account (All)</option>
                    </select>
                </div>
                <div class="popup-filter type region">
                    <div class="form-group" id="region-download">
                        <label for="name">Region</label>
                        {!! Form::select('region', $regionsDownload, null, ['class' => 'form-control', 'id' => 'popup-filter-region', 'autocomplete' => 'off']) !!}
                    </div>
                </div>
                <div class="popup-filter type branch hide">
                    <label>Branch</label>
                    <select id="popup-filter-branch" class="form-control" autocomplete="off">
                        @foreach($branches as $ID => $name)
                            @if ($ID > 0)
                                <option value="{{ $ID }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="popup-filter type account hide">
                    <label>Account</label>
                    <select id="popup-filter-account" class="form-control" autocomplete="off">
                        @foreach($accounts as $ID => $name)
                            <option value="{{ $ID }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="popup-filter type account-all hide">
                    <label>Account</label>
                    <select id="popup-filter-account-all" class="form-control" autocomplete="off">
                        @foreach($accountAllNation as $name)
                            <option value="{{ str_replace(' ', '-', strtolower($name)) }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="popup-filter">
                    <label>Time</label>
                    <select id="popup-filter-time-month" class="form-control chosen" multiple>
                        @foreach($time['months'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-center">
                    <a id="report-download" class="btn">Download</a>
                </p>
            </div>
        </div>

        <div id="popup-detail" class="popup-wrapper hide">
            <div class="popup-background"></div>
            <div class="container">
                <button class="popup-close"><i class="fa fa-times"></i></button>
                <table border="1" id="table-detail">
                    
                </table>
            </div>
        </div>
        
        <div id="popup-competitor" class="popup-wrapper hide">
            <div class="popup-background"></div>
            <div class="container">
                <button class="popup-close"><i class="fa fa-times"></i></button>
                <div id="div-competitor">
                    
                </div>
            </div>
        </div>

        <div id="popup-account" class="popup-wrapper hide">
            <div class="popup-background"></div>
            <div class="container" >
                <button class="popup-close"><i class="fa fa-times"></i></button>
                <div id="popup-sales-chart">
                    <canvas id="popup-sales-chart-account"></canvas>
                </div>
            </div>
        </div>

        <div id="popup-filter" class="popup-wrapper hide">
            <div class="popup-background"></div>
            <div class="container">
                <button class="popup-close"><i class="fa fa-times"></i></button>
                <h2>Filter</h2>

                <div class="form-group" id="region">
                    <label for="name">Region</label>
                    {!! Form::select('region', $regions, null, ['class' => 'form-control', 'id' => 'region-select', 'autocomplete' => 'off']) !!}
                </div>

                <div class="form-group hide" id="branch">
                    <label for="name">Branch</label>
                    {!! Form::select('branch', $branches, null, ['class' => 'form-control', 'id' => 'branch-select', 'autocomplete' => 'off']) !!}
                </div>

                <div class="form-group hide" id="channel">
                    <label for="name">Channel</label>
                    {!! Form::select('channel', $dealerChannel, null, ['class' => 'form-control', 'id' => 'channel-select', 'autocomplete' => 'off']) !!}
                </div>

                <div class="form-group hide" id="dealer_account">
                    <label for="name">Dealer Account</label>
                    {!! Form::select('dealer_account', $dealerAccounts, null, ['class' => 'form-control', 'id' => 'dealer_account-select', 'autocomplete' => 'off']) !!}
                </div>

                <div class="form-group hide" id="dealer">
                    <label for="name">Dealer</label>
                    {!! Form::select('dealer', $dealers, null, ['class' => 'form-control', 'id' => 'dealer-select', 'autocomplete' => 'off']) !!}
                </div>


                <div class="form-group">
                    <label for="name">Time</label>
                    <select class="form-control" id="filter-time-type" autocomplete="off">
                        <option value="month" selected>Monthly</option>
                        <option value="quarter">Quarter</option>
                        <option value="semester">Semester</option>
                        <option value="year">1 Year</option>
                        <option value="year">5 Year</option>
                        <option value="year">All</option>
                    </select>
                    <select class="form-control filter-time" id="filter-time-month" autocomplete="off">
                        @foreach($time['months'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select> 
                    <select class="form-control hide filter-time" id="filter-time-quarter" autocomplete="off">
                        @foreach($time['quarters'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select> 
                    <select class="form-control hide filter-time" id="filter-time-semester" autocomplete="off">
                        @foreach($time['semesters'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select> 
                    <select class="form-control hide filter-time" id="filter-time-year" autocomplete="off">
                        @foreach($time['years'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="text-center">
                    <button class="btn" id="filter-search">Search</button>
                </p>

            </div>
        </div>

        
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/jquery.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/chart.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/randomColor.min.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/dataTables.min.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/dashboard.js?t={{ time() }}"></script>
    </body>
</html>
<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication
Route::get('/login',    'AuthController@loginRender');
Route::post('/login',   'AuthController@loginHandle');

/**
 * Dashboard
 */

Route::group(['prefix' => 'dashboard', 'namespace' => 'Dashboard'], function() {
    
    Route::get('/',                     'DashboardController@index');

    Route::get('/login',                'DashboardController@loginRender');
    Route::post('/login',               'DashboardController@loginHandle');
    Route::get('/auth/{code}',          'DashboardController@loginAuth');

    Route::get('/data/explore',         'DashboardDataController@dataExplore');
    Route::get('/data/gender',          'DashboardDataController@dataGender');
    Route::get('/stock',                'DashboardStockController@stock');
    Route::get('/chart',                'DashboardChartController@chart');
    Route::get('/product',              'DashboardController@product');
    Route::get('/download',             'DashboardDataController@downloadReport');
    Route::get('/download/competitor',  'DashboardCompetitorController@downloadCompetitorReport');
    Route::get('/competitor',           'DashboardCompetitorController@competitorReport');

});

Route::get('/sales', 'DashboardController@getSalesValue');

/**
 * API Section
 */
Route::group(['prefix' => 'api', 'namespace' => 'API', 'middleware' => 'api'], function() {

//    Route::group(['prefix' => '1.4.0', 'namespace' => 'V_1_4_0'], function() {
//        
//    
//        // Authentication
//        Route::get('/check',                    'AuthController@check');
//        Route::post('/login',                   'AuthController@login');
//
//        Route::get('/date-check',               'GeneralController@checkDate');
//        Route::get('/data-check',               'GeneralController@checkData');
//        Route::get('/data-list',                'GeneralController@getData');
//        Route::get('/data-profile',             'GeneralController@profile');
//
//        // Password endpoint
//        Route::post('/password-forgot',         'PasswordController@forgot');
//        Route::post('/password-check-code',     'PasswordController@checkCode');
//        Route::post('/password-reset',          'PasswordController@reset');
//        Route::post('/password-change',         'PasswordController@change');
//        Route::post('/password-generate',       'PasswordController@generate');
//
//        // Report endpoint
//        Route::post('/report-create',                   'ReportController@create');
//        Route::get('/report-list',                      'ReportController@getList');
//        Route::get('/report-sales',                     'ReportController@getSales');
//        Route::get('/report-sales-arco',                'ReportController@getSalesRegion');
//        Route::get('/report-sales-tl',                  'ReportController@getSalesBranch');
//        Route::get('/report-sales-account',             'ReportController@getSalesAccount');
//        Route::get('/report-sales-dealer',              'ReportController@getSalesDealer');
//        Route::get('/report-sales-promotor',            'ReportController@getSalesPromotor');
//        Route::get('/report-sales-promotor-detail',     'ReportController@getSalesPromotorDetail');
//        
//        Route::get('/report-nosales',                   'ReportController@checkNoSales');
//        Route::post('/report-nosales',                  'ReportController@handleNoSales');
//        
//        Route::get('/stock-empty-list',                 'StockController@index');
//        Route::post('/stock-empty-create',              'StockController@create');
//        Route::post('/stock-empty-update',              'StockController@update');
//
//        // News endpoint
//        Route::get('/news-total',               'NewsController@getListTotal');
//        Route::get('/news-list',                'NewsController@getList');
//        Route::get('/news-view',                'NewsController@getView');
//        Route::post('/news-create',             'NewsController@create');
//
//        //absence
//        Route::get('/absence-check',            'AbsenceController@checkData');
//        Route::post('/absence-create',          'AbsenceController@create');
//        
//    });
    
    Route::group(['prefix' => '1.5.0', 'namespace' => 'V_1_5_0'], function() {
        
    
        // Authentication
        Route::get('/check',                    'AuthController@check');
        Route::post('/login',                   'AuthController@login');

        Route::get('/date-check',               'GeneralController@checkDate');
        Route::get('/data-check',               'GeneralController@checkData');
        Route::get('/data-list',                'GeneralController@getData');
        Route::get('/data-profile',             'GeneralController@profile');

        // Password endpoint
        Route::post('/password-forgot',         'PasswordController@forgot');
        Route::post('/password-check-code',     'PasswordController@checkCode');
        Route::post('/password-reset',          'PasswordController@reset');
        Route::post('/password-change',         'PasswordController@change');
        Route::post('/password-generate',       'PasswordController@generate');

        // Report endpoint
        Route::post('/report-create',                   'ReportController@create');
        Route::get('/report-list',                      'ReportController@getList');
        Route::get('/report-sales',                     'ReportController@getSales');
        
        // Report no sales endpoint
        Route::get('/report-nosales',                   'ReportController@checkNoSales');
        Route::post('/report-nosales',                  'ReportController@handleNoSales');
        
        // Report stock endpoint
        Route::get('/stock-empty-list',                 'StockController@index');
        Route::post('/stock-empty-create',              'StockController@create');
        Route::post('/stock-empty-update',              'StockController@update');
        
        // Sales Report endpoint
        Route::get('/report-sales-region',              'ReportSalesController@getRegion');
        Route::get('/report-sales-branch',              'ReportSalesController@getBranch');
        Route::get('/report-sales-account',             'ReportSalesController@getAccount');
        Route::get('/report-sales-dealer',              'ReportSalesController@getDealer');
        Route::get('/report-sales-promotor',            'ReportSalesController@getPromotor');
        Route::get('/report-sales-promotor-detail',     'ReportSalesController@getPromotorDetail');

        // News endpoint
        Route::get('/news-total',               'NewsController@getListTotal');
        Route::get('/news-list',                'NewsController@getList');
        Route::get('/news-view',                'NewsController@getView');
        Route::post('/news-create',             'NewsController@create');

        // Absence
        Route::get('/absence-check',            'AbsenceController@checkData');
        Route::post('/absence-create',          'AbsenceController@create');
        
        // Competitor Report
        Route::get('/competitor-price-list',    'CompetitorController@priceList');
        Route::post('/competitor-price-set',    'CompetitorController@priceSet');
        Route::post('/competitor-price-get',    'CompetitorController@priceGet');
        
    });
});

/**
 * Admin CMS section
 */
Route::group(['middleware' => 'admin'], function() {

    // Home
    Route::get('/', function() {
        return view('home');
    });
    
    /**
     * Region management
     */
    Route::group(['prefix' => 'region'], function() {
        
        // Regional
        Route::get('/',    			'RegionController@index');
        Route::get('/create',    	'RegionController@createRender');
        Route::post('/create',    	'RegionController@createHandle');
        Route::get('/edit',    	    'RegionController@updateRender');
        Route::post('/edit',    	'RegionController@updateHandle');
        Route::get('/remove',    	'RegionController@removeRender');
        Route::post('/remove',    	'RegionController@removeHandle');

    });

    /**
     * Branch management
     */
    Route::group(['prefix' => 'branch'], function() {
        
        // Regional
        Route::get('/',             'BranchController@index');
        Route::get('/create',       'BranchController@createRender');
        Route::post('/create',      'BranchController@createHandle');
        Route::get('/edit',         'BranchController@updateRender');
        Route::post('/edit',        'BranchController@updateHandle');
        Route::get('/remove',       'BranchController@removeRender');
        Route::post('/remove',      'BranchController@removeHandle');

    });
    
	/**
     * Dealer management
     */
    Route::group(['prefix' => 'dealer'], function() {
        
        Route::get('/',    			'DealerController@index');
        Route::get('/create',    	'DealerController@createRender');
        Route::post('/create',    	'DealerController@createHandle');
        Route::get('/edit',    		'DealerController@updateRender');
        Route::post('/edit',    	'DealerController@updateHandle');
        Route::get('/remove',    	'DealerController@removeRender');
        Route::post('/remove',    	'DealerController@removeHandle');
        
    });

    /**
     * Dealer type management
     */
    Route::group(['prefix' => 'dealer-type'], function() {
        
        Route::get('/',             'DealerTypeController@index');
        Route::get('/create',       'DealerTypeController@createRender');
        Route::post('/create',      'DealerTypeController@createHandle');
        Route::get('/edit',         'DealerTypeController@updateRender');
        Route::post('/edit',        'DealerTypeController@updateHandle');
        Route::get('/remove',       'DealerTypeController@removeRender');
        Route::post('/remove',      'DealerTypeController@removeHandle');
        
    });

    /**
     * Dealer type management
     */
    Route::group(['prefix' => 'dealer-channel'], function() {
        
        Route::get('/',             'DealerChannelController@index');
        Route::get('/create',       'DealerChannelController@createRender');
        Route::post('/create',      'DealerChannelController@createHandle');
        Route::get('/edit',         'DealerChannelController@updateRender');
        Route::post('/edit',        'DealerChannelController@updateHandle');
        Route::get('/remove',       'DealerChannelController@removeRender');
        Route::post('/remove',      'DealerChannelController@removeHandle');
        
    });

    /**
     * Dealer account management
     */
    Route::group(['prefix' => 'dealer-account'], function() {
        
        Route::get('/',             'DealerAccountController@index');
        Route::get('/create',       'DealerAccountController@createRender');
        Route::post('/create',      'DealerAccountController@createHandle');
        Route::get('/edit',         'DealerAccountController@updateRender');
        Route::post('/edit',        'DealerAccountController@updateHandle');
        Route::get('/remove',       'DealerAccountController@removeRender');
        Route::post('/remove',      'DealerAccountController@removeHandle');
        
    });
    

    /**
     * Product 
     */
    Route::group(['prefix' => 'product'], function() {
        
        /**
         * Category Product
         */
        Route::group(['prefix' => 'category'], function() {
            Route::get('',              'ProductCategoryController@index');
            Route::get('/create',       'ProductCategoryController@createRender');
            Route::post('/create',      'ProductCategoryController@createHandle');
            Route::get('/edit',         'ProductCategoryController@updateRender');
            Route::post('/edit',        'ProductCategoryController@updateHandle');
            Route::get('/remove',       'ProductCategoryController@removeRender');
            Route::post('/remove',      'ProductCategoryController@removeHandle');
        });
        
        /**
         * Product model
         */
    	Route::group(['prefix' => 'model', 'middleware' => 'product'], function() {	
    		Route::get('/',    			'ProductModelController@index');
    		Route::get('/create',    	'ProductModelController@createRender');
    		Route::post('/create',    	'ProductModelController@createHandle');
    		Route::get('/edit',    		'ProductModelController@updateRender');
    		Route::post('/edit',    	'ProductModelController@updateHandle');
    		Route::get('/remove',    	'ProductModelController@removeRender');
    		Route::post('/remove',    	'ProductModelController@removeHandle');

            Route::get('/upload',       'ProductModelController@uploadRender');
            Route::post('/upload',      'ProductModelController@uploadHandle');
            Route::post('/single',      'ProductModelController@uploadSingle');
    	});

        /**
         * Price Product
         */
        Route::group(['prefix' => 'price'], function() {  
            
            Route::get('/',             'ProductPriceController@index');
            
            Route::get('/create',       'ProductPriceController@createRender');
            Route::post('/create',      'ProductPriceController@createHandle');
            
            Route::get('/edit',         'ProductPriceController@updateRender');
            Route::post('/edit',        'ProductPriceController@updateHandle');
            
            Route::get('/remove',       'ProductPriceController@removeRender');
            Route::post('/remove',      'ProductPriceController@removeHandle');

            Route::get('/upload',       'ProductPriceController@uploadRender');
            Route::post('/upload',      'ProductPriceController@uploadHandle');
            Route::post('/single',      'ProductPriceController@uploadSingle');
        });
        
        /**
         * Price Product
         */
        Route::group(['prefix' => 'incentive'], function() {  
            
            Route::get('/',             'ProductIncentiveController@index');
            
            Route::get('/create',       'ProductIncentiveController@createRender');
            Route::post('/create',      'ProductIncentiveController@createHandle');
            
            Route::get('/edit',         'ProductIncentiveController@updateRender');
            Route::post('/edit',        'ProductIncentiveController@updateHandle');
            
            Route::get('/remove',       'ProductIncentiveController@removeRender');
            Route::post('/remove',      'ProductIncentiveController@removeHandle');
        });

    });
    
    
	
	/**
     * Promotor section
     */
    Route::group(['prefix' => 'promotor'], function() {
                    
        Route::get('/',                 'PromotorController@index');

        Route::get('/create',           'PromotorController@createRender');
        Route::post('/create',          'PromotorController@createHandle');

        Route::get('/edit',             'PromotorController@updateRender');
        Route::post('/edit',            'PromotorController@updateHandle');

        Route::get('/remove',           'PromotorController@removeRender');
        Route::post('/remove',          'PromotorController@removeHandle');
        
        Route::get('/block',           'PromotorController@blockHandle');
        Route::get('/logout',          'PromotorController@logoutHandle');
        Route::get('/reset',           'PromotorController@resetHandle');
        Route::get('/non-active',      'PromotorController@nonActiveHandle');
    });

    /**
     * Promotor Report section
     */
    Route::group(['prefix' => 'report'], function() {
                    
        Route::get('/',                 'ReportController@index');

        Route::get('/view',             'ReportController@viewRender');
        Route::post('/view',            'ReportController@updateHandle');

        Route::get('/edit',             'ReportController@updateRender');
        Route::post('/edit',            'ReportController@updateHandle');

        Route::get('/remove',           'ReportController@removeRender');
        Route::post('/remove',          'ReportController@removeHandle');

    });

    /**
     * Sales target section
     */
    Route::group(['prefix' => 'sales-target'], function() {
                    
        Route::get('/',                'SalesTargetController@index');

        Route::get('/create',          'SalesTargetController@createRender');
        Route::post('/create',         'SalesTargetController@createHandle');

        Route::get('/edit',            'SalesTargetController@updateRender');
        Route::post('/edit',           'SalesTargetController@updateHandle');

        
    });
    
    /**
     * Dashboard account section
     */
    Route::group(['prefix' => 'dashboard-account'], function() {
        
        Route::get('/',                 'Dashboard\DashboardAccountController@index');

        Route::get('/create',           'Dashboard\DashboardAccountController@createRender');
        Route::post('/create',          'Dashboard\DashboardAccountController@createHandle');

        Route::get('/edit',             'Dashboard\DashboardAccountController@updateRender');
        Route::post('/edit',            'Dashboard\DashboardAccountController@updateHandle');

        Route::get('/remove',           'Dashboard\DashboardAccountController@removeRender');
        Route::post('/remove',          'Dashboard\DashboardAccountController@removeHandle');
        
        Route::get('/remove-token',     'Dashboard\DashboardAccountController@removeTokenHandle');
        
    });
    
    /**
     * Competitor brand management
     */
    Route::group(['prefix' => 'competitor-brand'], function() {
        
        // Regional
        Route::get('/',    			'CompetitorBrandController@index');
        Route::get('/create',    	'CompetitorBrandController@createRender');
        Route::post('/create',    	'CompetitorBrandController@createHandle');
        Route::get('/edit',    	    'CompetitorBrandController@updateRender');
        Route::post('/edit',    	'CompetitorBrandController@updateHandle');
        Route::get('/remove',    	'CompetitorBrandController@removeRender');
        Route::post('/remove',    	'CompetitorBrandController@removeHandle');

    });
    
    /**
     * Competitor brand management
     */
    Route::group(['prefix' => 'competitor-price'], function() {
        
        // Regional
        Route::get('/',    			'CompetitorPriceController@index');
        Route::get('/remove',    	'CompetitorPriceController@removeRender');
        Route::post('/remove',    	'CompetitorPriceController@removeHandle');

    });
    
    
    /**
     * News section
     */
    Route::group(['prefix' => 'news'], function() {
                    
        Route::get('/',                'NewsController@index');

        Route::get('/create',          'NewsController@createRender');
        Route::post('/create',         'NewsController@createHandle');

        Route::get('/edit',            'NewsController@updateRender');
        Route::post('/edit',           'NewsController@updateHandle');

        Route::get('/remove',          'NewsController@removeRender');
        Route::post('/remove',         'NewsController@removeHandle');
        
    });
    
    /**
     * User management
     */
    Route::group(['prefix' => 'user', 'middleware' => 'superAdmin'], function() {

        Route::get('/',                'UserController@index');

        Route::get('/create',          'UserController@createRender');
        Route::post('/create',         'UserController@createHandle');

        Route::get('/edit',            'UserController@updateRender');
        Route::post('/edit',           'UserController@updateHandle');
        
        Route::get('/remove',          'UserController@removeRender');
        Route::post('/remove',         'UserController@removeHandle');

    });

    // Logout
    Route::get('/logout',   'AuthController@logout');

});

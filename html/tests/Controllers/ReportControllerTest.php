<?php
namespace Tests\Controllers;

use DB;
use TestCase;

use App;
use Validator;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\ReportModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductPriceModel;


class ReportControllerTest extends TestCase
{
	/**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

    /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;

    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
    /**
     * Dealer account model container
     *
     * @access protected
     */
    protected $dealerAccount;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

    /**
     * Target Promotor model container
     *
     * @access protected
     */
    protected $promotor_target;

    /**
     * Log model container
     *
     * @access protected
     */
    protected $log;

    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;

    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product_model;

    /**
     * category model container
     *
     * @access Protected
     */
    protected $product_category;

    /**
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;

    /**
     * Admin session data
     *
     * @access protected
     */
    protected $adminSession = [
        'user_ID'       => 1,
        'user_status'   => 'admin'
    ];

    /**
     * Region data sample
     *
     * @access protected
     */
    protected $reportData = [
        [
            'dealer_ID'         => 1,
            'promotor_ID'   	=> 1,
            'account_ID'        => 1,
            'tl_ID'   			=> 1,
            'arco_ID'          	=> 1,
            'customer_ID'   	=> 1,
            'product_model_ID'  => 1,
            'custom_name'   	=> '',
            'price'          	=> 3000000,
            'quantity'   		=> 1,
            'date'          	=> '2016-08-06',
            'created'   		=> 1470495609
        ],
        [
            'dealer_ID'         => 2,
            'promotor_ID'       => 2,
            'account_ID'        => 2,
            'tl_ID'             => 2,
            'arco_ID'           => 2,
            'customer_ID'       => 2,
            'product_model_ID'  => '',
            'custom_name'       => 'ABCD',
            'price'             => 5000000,
            'quantity'          => 1,
            'date'              => '2016-07-06',
            'created'           => 1470495609
        ],
        [
            'dealer_ID'         => 100,
            'promotor_ID'   	=> 3,
            'account_ID'        => 2,
            'tl_ID'   			=> 1,
            'arco_ID'          	=> 2,
            'customer_ID'   	=> 1,
            'product_model_ID'  => '',
            'custom_name'   	=> 'EEFFGHG',
            'price'          	=> 5000000,
            'quantity'   		=> 1,
            'date'          	=> '2016-07-06',
            'created'   		=> 1470495609
        ],
        [
            'dealer_ID'         => 4,
            'promotor_ID'       => 4,
            'account_ID'        => 1,
            'tl_ID'             => 1,
            'arco_ID'           => 1,
            'customer_ID'       => 1,
            'product_model_ID'  => 2,
            'custom_name'       => '',
            'price'             => 3500000,
            'quantity'          => 1,
            'date'              => '2016-07-10',
            'created'           => 1470495609
        ],
        [
            'dealer_ID'         => 5,
            'promotor_ID'       => 5,
            'account_ID'        => 1,
            'tl_ID'             => 1,
            'arco_ID'           => 1,
            'customer_ID'       => 1,
            'product_model_ID'  => 5,
            'custom_name'       => '',
            'price'             => 7000000,
            'quantity'          => 1,
            'date'              => '2016-02-02',
            'created'           => 1470495609
        ]
    ];
    
    /**
     * Region data sample
     *
     * @access protected
     */
    protected $regionData = [
        [
            'name'          => 'Jakarta',
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Indonesia Timur',
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Indonesia Barat',
            'promotor_ID'   => 1
        ]
    ];

    /**
     * Dealer data sample
     *
     * @access protected
     */
    protected $dealerData = [
        [
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 1,
            'name'              => 'BALI ELECTRONIC CENTER',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 2,
            'dealer_type_ID'    => 2,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 2,
            'name'              => 'ELECTRONIC CITY GATSU',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 3,
            'name'              => 'GALAXY',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 4,
            'dealer_type_ID'    => 10,
            'region_ID'         => 4,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 4,
            'name'              => 'ELECTRONIC',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 5,
            'dealer_type_ID'    => 1,
            'region_ID'         => 1,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 10,
            'code'              => 5,
            'name'              => 'CITY',
            'company'           => 'none',
            'address'           => 'none',
        ]
    ];

    /**
     * Dealer type data sample
     *
     * @access protected
     */
    protected $dealerTypeData = [
        [
            'name'          => 'R1',
        ],
        [
            'name'          => 'R2',
        ],
        [
            'name'          => 'R3',
        ]
    ];

    /**
     * Dealer channel data sample
     *
     * @access protected
     */
    protected $dealerChannelData = [
        [
            'name'          => 'SO'
        ],
        [
            'name'          => 'MUP'
        ],
        [
            'name'          => 'SMO'
        ]
    ];

    /**
     * Branch data sample
     *
     * @access protected
     */
    protected $branchData = [
        [        
            'name'          => 'Jakarta',
            'region_ID'     => 1,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bandung',
            'region_ID'     => 2,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 2
        ],
    ];

    /**
     * Dealer account data sample
     *
     * @access protected
     */
    protected $dealerAccountData = [
        [
            'name'          => 'Electronic City',
            'branch_ID'     => 1,
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Best Denki',
            'branch_ID'     => 2,
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Electronic Solution',
            'branch_ID'     => 3,
            'promotor_ID'   => 1
        ]
    ];

    /**
     * Promotor data sample
     *
     * @access protected
     */
    protected $promotorData = [
        [        
            'dealer_ID'     => 0,
            'phone'         => 4001,
            'password'      => '123456',
            'name'          => 'Ester Nursahbat',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 6002,
            'password'      => '123456',
            'name'          => 'Maryono',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 1
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4003,
            'password'      => '123456',
            'name'          => 'Area Coordinator C',
            'gender'        => 'male',
            'type'          => 'tl',
            'parent_ID'     => 0
        ],
    ];

    /**
     * Custom promotor meta data sample
     *
     * @access protected
     */
    protected $promotorMetaData = [        
        'name'         => 'block',
        'content'      => 'block',
    ];

    /**
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        [
            'name'          => 'Ac'
        ],
        [
            'name'          => 'Tv'
        ],
        [
            'name'          => 'Kulkas'
        ]
    ];

    /**
     * Product model data sample
     *
     * @access protected
     */
    protected $productModelData = [
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-2250',
            'price'                 => 0,
        ],
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-4200LJ-H',
            'price'                 => 0,
        ]
    ];

    /**
     * Product Price data sample
     *
     * @access protected
     */
    protected $productPriceData = [
        [
            'dealer_type_ID'      => 1,
            'dealer_channel_ID'   => 1,
            'product_ID'          => 1,
            'price'               => 1000,
        ],
        [
            'dealer_type_ID'      => 1,
            'dealer_channel_ID'   => 1,
            'product_ID'          => 2,
            'price'               => 3000,
        ],
        [
            'dealer_type_ID'      => 3,
            'dealer_channel_ID'   => 3,
            'product_ID'          => 1,
            'price'               => 3000,
        ]
    ];

    /**
     * Custom promotor data sample
     *
     * @access protected
     */
    protected $customPromotorData = [        
        'dealer_ID'     => 1,
        'phone'         => '+628978541254',
        'password'      => '123456',
        'name'          => 'Ester Nursahbat',
        'gender'        => 'male',
        'type'          => 'promotor',
        'parent_ID'     => 1
    ];

    /**
     * Custom report data sample
     *
     * @access protected
     */
    protected $customReportData = [
        'ID'                => 1,
        'quantity'          => 1,
        'type'              => 'source',
        'product_model_ID'  => 1,
        'custom_name'   	=> '',
        'price'          	=> 3000000,
        'date'          	=> '2016-08-06'
    ];
    
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer           = new DealerModel();
        $this->dealer_type      = new DealerTypeModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->report           = new ReportModel();
        $this->product_model    = new ProductModel();
        $this->product_category = new ProductCategoryModel();
        $this->product_price    = new ProductPriceModel();
    }

    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // populate Rgion
        foreach ($this->regionData as $region)
        {
             $this->region->create(
                 $region['name'],
                 $region['promotor_ID']
             );
        }
        
        // Populate branch
        foreach ($this->branchData as $branch)
        {
             $this->branch->create(
                 $branch['name'],
                 $branch['region_ID'],
                 $branch['promotor_ID']
             );
        }

        // Popular Dealer 
        foreach ($this->dealerData as $dealer)
        {   
            $data = [
                'region_ID'         => $dealer['region_ID'],
                'branch_ID'         => $dealer['branch_ID'],
                'dealer_account_ID' => $dealer['dealer_account_ID'],
                'dealer_channel_ID' => $dealer['dealer_channel_ID'],
                'dealer_type_ID'    => $dealer['dealer_type_ID'],
                'code'              => $dealer['code'],
                'name'              => $dealer['name'],
                'company'           => $dealer['company'],
                'address'           => $dealer['address']
            ];

            $this->dealer->create($data);
        }

        // Populate dealer tyoe
        foreach ($this->dealerTypeData as $dealerType)
        {
            $this->dealer_type->create(
                 $dealerType['name']
             );
        }

        // Populate dealer channel
        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $this->dealer_channel->create(
                 $dealerChannel['name']
             );
        }

        // Populate dealer account
        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->dealerAccount->create(
                 $dealerAccount['name'],
                 $dealerAccount['branch_ID'],
                 $dealerAccount['promotor_ID']
            );
        }

        // Populate product category
        foreach ($this->productCategoryData as $productCategory)
        {
            $this->product_category->create(
                 $productCategory['name']
             );
        }

        // Populate Product Model
        $productModelIDs = [];
        
        foreach ($this->productModelData as $productModel)
        {
             $productModelIDs[] = $this->product_model->create(
                 $productModel['product_category_ID'],
                 $productModel['name'],
                 $productModel['price']
             );
        }

        // Populate Product Price
        $productPriceIDs = [];
        
        foreach ($this->productPriceData as $productPrice)
        {
             $productPriceIDs[] = $this->product_price->create(
                 $productPrice['dealer_type_ID'],
                 $productPrice['dealer_channel_ID'],
                 $productPrice['product_ID'],
                 $productPrice['price']
             );
        }
        
        // Populate promotor
        $promotorIDs = [];
        
        foreach ($this->promotorData as $promotor)
        {   
            $promotorIDs[] = $this->promotor->create(
                 $promotor['dealer_ID'],
                 $promotor['phone'],
                 $promotor['password'],
                 $promotor['name'],
                 $promotor['gender'],
                 $promotor['type'],
                 $promotor['parent_ID']
            );
        }

        // Populate promotor
        $promotorReportIDs = [];
        
        foreach ($this->reportData as $report)
        {   
            $promotorReportIDs[] = $this->report->create($report);
        }
        
        return $promotorReportIDs;
    }

    /**
     * Test render promotor index page
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/report');
        
        // Verify response
        $this->assertPageContain($this->customPromotorData['name']);
        $this->assertViewHas('date');
        $this->assertViewHas('dataTarget');
        $this->assertPageContain('Promotor Reports');
    }

    /**
     * Test render view report page without ID
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderViewNoID()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/report/view');
        
        // Verify response
        $this->assertResponseStatus(404);
    }

    /**
     * Test render view report page
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderView()
    {
        // Populate data
        $this->_populate();

        // Set ID
        $params = [
            'ID' => 1
        ];
        
        // Make Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/report/view', $params);
        
        // Verify response
        $this->assertPageContain('Promotor Report View');
        $this->assertViewHas('date');
        $this->assertViewHas('dataReport');
        $this->assertViewHas('listMonth');
    }

    /**
     * Test render update report page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/report/edit');
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render update report page without valid report data
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderUpdateNoReport()
    {
        // Set parameter
        $params = [
            'ID' => 1
        ];
        
        $this->withSession($this->adminSession)
            ->call('GET', '/report/edit', $params);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render update promotor page success with source type
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderUpdateSuccessSource()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = 1;
        $report = $this->report->getOneReportPromotor($ID);
        
        $params = [
            'ID'    => $ID,
            'date'  => '2017-01-01'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/report/edit', $params);

        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('report');
        $this->assertViewHas('products');
        $this->assertViewHas('date', $params['date']);
        $this->assertViewHas('type', 'source');
    }
    
    /**
     * Test render update promotor page success with custom type
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderUpdateSuccessCustom()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = 2;
        
        // Update report
        $this->report->update($ID, [
            'custom_name'       => 'TH-32D',
            'product_model_ID'  => 0
        ]);
        
        $report = $this->report->getOneReportPromotor($ID);
        
        $params = [
            'ID'    => $ID,
            'date'  => '2017-01-01'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/report/edit', $params);

        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('report', $report);
        $this->assertViewHas('products');
        $this->assertViewHas('date', $params['date']);
        $this->assertViewHas('type', 'custom');
    }

    /**
     * Test handle update request without ID
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateNoID()
    {
        // Set parameters
        $params = [];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request without quantity parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateNoQuantity()
    {
        // Set parameters
        $params = [
            'ID' => 1
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request without date parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateNoDate()
    {
        // Set parameters
        $params = [
            'ID'        => 1,
            'quantity'  => 1,
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request with wrong type parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateWrongType()
    {
        // Set parameters
        $params = [
            'ID'        => 1,
            'quantity'  => 1,
            'date'      => '2017-01-01',
            'type'      => 'test'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request without product model ID and custom name parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController-2
     * @return Void
     */
    public function testHandleUpdateNoProductModelIDAndCustomName()
    {
        // Set parameters
        $params = [
            'ID'        => 1,
            'quantity'  => 1,
            'date'      => '2017-01-01',
            'type'      => 'source'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }

    /**
     * Test handle update request without report data
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateNoReportData()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customReportData;
        $params['ID'] = 100;

        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }

    

    /**
     * Test handle update request success from source to custom type
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateSuccessFromSourceToCustom()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customReportData;
        $params['type'] = 'custom';
        $params['custom_name'] = 'ABCD';
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/report/edit');
        $this->assertSessionHas('report-updated', '');
        
        // Validate data
        $report = $this->report->getOne($params['ID']);

        $this->assertEquals($report->product_model_ID,   0);
        $this->assertEquals($report->custom_name,   $params['custom_name']);
        $this->assertEquals($report->quantity,      $params['quantity']);
    }
    
    /**
     * Test handle update request success from custom to source
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateSuccessFromCustomToSource()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customReportData;
        $params['ID'] = 2;
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/report/edit');
        $this->assertSessionHas('report-updated', '');
        
        // Validate data
        $report = $this->report->getOne($params['ID']);

        $this->assertEquals($report->product_model_ID,  $params['product_model_ID']);
        $this->assertEquals($report->custom_name,       '');
        $this->assertEquals($report->quantity,          $params['quantity']);
    }
    
    /**
     * Test handle update request success from custom to source with special price
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleUpdateSuccessFromCustomToSourcePrice()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customReportData;
        $params['ID'] = 1;
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/edit', $params, [], [], ['HTTP_REFERER' => '/report/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/report/edit');
        $this->assertSessionHas('report-updated', '');
        
        // Validate report data
        $report = $this->report->getOne($params['ID']);

        $this->assertEquals($report->product_model_ID,  $params['product_model_ID']);
        $this->assertEquals($report->custom_name,       '');
        $this->assertEquals($report->quantity,          $params['quantity']);
        
        // Validate price data
        $dealer = $this->dealer->getOne($report->dealer_ID);
        $productPrice = $this->product_price->getDealerProductPrice(
            $dealer->dealer_type_ID, 
            $dealer->dealer_channel_ID, 
            $params['product_model_ID']
        );
        
        $this->assertEquals($report->price, $productPrice->price);
    }

    /**
     * Test render remove report page without report ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/report/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove report page without valid report data
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/report/remove', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove promotor page
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $this->_populate();

        // Set ID
        $params = $this->customReportData;
        $ID     = $params['ID'];

        $this->withSession($this->adminSession)
            ->call('GET', '/report/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Report');
    }

    /**
     * Test handle remove report request without report ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/report/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove report without valid report data
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleRemoveNoReport()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/report/remove', ['ID' => 01]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove report successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ReportController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $this->_populate();

        // Set ID
        $params = $this->customReportData;
        $ID     = $params['ID'];
        
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/report/remove', ['ID' => $ID]);

        // Validate response
        $this->assertRedirectedTo('/report/view?ID='.$ID.'&date=');
        $this->assertSessionHas('report-deleted', '');
        
        // Validate data
        $report = $this->report->getOne($ID);
        $this->assertEquals(null, $report);
    }
}


?>
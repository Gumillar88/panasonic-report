<?php
namespace Tests\Controllers;

use DB;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorTargetModel;

class SalesTargetControllerTest extends TestCase
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
     * Dealer account model container
     *
     * @access protected;
     */
    protected $dealerAccount;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

     /**
     * Promotor_meta model container
     *
     * @access protected
     */
    protected $promotor_meta;
    
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
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
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
            'branch_ID'     => 3,
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Best Denki',
            'branch_ID'     => 3,
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
            'phone'         => 1000,
            'password'      => '123456',
            'name'          => 'Panasonic',
            'gender'        => 'male',
            'type'          => 'panasonic',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 1001,
            'password'      => '123456',
            'name'          => 'Arco',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 1
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4001,
            'password'      => '123456',
            'name'          => 'Team Leader',
            'gender'        => 'male',
            'type'          => 'tl',
            'parent_ID'     => 2
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543635',
            'password'      => '123456',
            'name'          => 'Promotor A',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543631',
            'password'      => '123456',
            'name'          => 'Promotor B',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543632',
            'password'      => '123456',
            'name'          => 'Promotor C',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
    ];

    /**
     * promotor target data sample
     *
     * @access protected
     */
    protected $promotorTargetData = [        
        [
            'promotor_ID'       => 5,
            'dealer_ID'         => 1,
            'arco_ID'           => 2,
            'tl_ID'             => 3,
            'account_ID'        => 1,
            'product_ID'        => 1,
            'total'             => 20000,
            'month'             => '2016-11'
        ],
        [
            'promotor_ID'       => 6,
            'dealer_ID'         => 1,
            'arco_ID'           => 2,
            'tl_ID'             => 3,
            'account_ID'        => 1,
            'product_ID'        => 1,
            'total'             => 20000,
            'month'             => '2016-11'
        ],
    ];

    /**
     * Custom promotor target data sample
     *
     * @access protected
     */
    protected $customPromotorTargetData = [        
        'promotor_ID'       => 4,
        'dealer_ID'         => 1,
        'arco_ID'           => 2,
        'tl_ID'             => 3,
        'account_ID'        => 1,
        'product_ID'        => 1,
        'total'             => 2000,
        'month'             => '2016-12'
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
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_target  = new PromotorTargetModel();
    }
    
    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // populate Region
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

        // Populate dealer account
        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->dealerAccount->create(
                 $dealerAccount['name'],
                 $dealerAccount['branch_ID'],
                 $dealerAccount['promotor_ID']
            );
        }
        
        // Populate promotor
        
        foreach ($this->promotorData as $promotor)
        {   
            $this->promotor->create(
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
        $promotorTargetIDs = [];
        
        foreach ($this->promotorTargetData as $promotorTarget)
        {   
            $promotorTargetIDs[] = $this->promotor_target->create(
                 $promotorTarget['promotor_ID'],
                 $promotorTarget['dealer_ID'],
                 $promotorTarget['arco_ID'],
                 $promotorTarget['tl_ID'],
                 $promotorTarget['product_ID'],
                 $promotorTarget['total'],
                 $promotorTarget['month']
            );
        }
        
        return $promotorTargetIDs;
    }

    /**
     * Populate database with promotor meta data
     *
     * @access private
     * @return Array
     */
    private function _populatePromotorMeta($promotor_ID)
    {
        // Populate promotormeta
       $this->promotor_meta->set(
             $promotor_ID,
             $this->promotorMetaData['name'],
             $this->promotorMetaData['content']
         );
    }

    /**
     * Test render sales target index page dealer
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderIndexDealer()
    {
        // Populate data
        $this->_populate();
        
        // Set month
        $month = '2016-11';
        
        // Make Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/sales-target', ['type' => 'dealer', 'date' => $month]);
        
        // Verify response 
        $this->assertPageContain($this->dealerData[0]['name']);
        
        $this->assertViewHas('type', 'dealer');
        $this->assertViewHas('typeButton');
        $this->assertViewHas('listMonth');
        $this->assertViewHas('date', $month);
        $this->assertViewHas('dataTarget');
    }
    
    /**
     * Test render sales target index page for promotor
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderIndexPromotor()
    {
        // Populate data
        $this->_populate();
        
        // Set month
        $month = '2016-11';
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/sales-target', ['type' => 'promotor', 'date' => $month]);
        
        // Verify response
        foreach ($this->promotorData as $promotor)
        {
            if($promotor['type'] == 'promotor')
            {
                $this->assertPageContain($promotor['name']);
            }
        }
        
        $this->assertViewHas('type', 'promotor');
        $this->assertViewHas('typeButton');
        $this->assertViewHas('listMonth');
        $this->assertViewHas('date', $month);
        $this->assertViewHas('dataTarget');
    }
    
    /**
     * Test render create sales target page with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderCreateErrorValidation()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/create');
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render create sales target page not promotor
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderCreateNotPromotor()
    {
        // Populate data
        $this->_populate();

        $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/create',['promotor_ID' => 2, 'date' => '2016-12']);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render create promotor target page success
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderCreateSuccess()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();
        
        // Set data
        $ID                 = $this->_pickRandomItem($promotorTargetIDs);
        $promotor_target    = $this->promotor_target->getOne($ID);
        
        // Set parameter
        $params = [
            'promotor_ID'   => $promotor_target->promotor_ID , 
            'date'          => date('Y-m')
        ];

        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/create', $params);

        // Verify response
        $this->assertPageContain('Set Sales Target for Promotor');

        $this->assertViewHas('dealer');
        $this->assertViewHas('promotor');
        $this->assertViewHas('date');
    }
    
    /**
     * Test handle create promotor target request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set parameter and remove promotor name
        $params = $this->customPromotorTargetData;
        unset($params['total']);
        
        // Set data
        $ID                 = $this->_pickRandomItem($promotorTargetIDs);
        $promotor_target    = $this->promotor_target->getOne($ID);
        
        // Set parameter
        $params = [
            'promotor_ID'   => $promotor_target->promotor_ID , 
            'date'          => '2016-12'
        ];

        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/create', $params, [], [], ['HTTP_REFERER' => '/sales-target/create']);

        $this->assertRedirectedTo('/sales-target/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create promotor target request with success
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID                 = $this->_pickRandomItem($promotorTargetIDs);
        $promotor_target    = $this->promotor_target->getOne($ID);

        // Set parameter and remove promotor name
        $params                 = $this->customPromotorTargetData;
        $params['promotor_ID']  = $promotor_target->promotor_ID;
        $params['date']         = date('Y-m');
        

        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/create', $params);
        
        // Verify data
        $data = DB::table('promotor_targets')
                    ->where('promotor_ID', $promotor_target->promotor_ID)
                    ->where('month', $params['date'])
                    ->first();

        $this->assertRedirectedTo('/sales-target/edit?ID='.$params['promotor_ID'].'&date='.$params['date'].'&type=promotor');
        $this->assertSessionHas('target-created', '');
    }
    
    /**
     * Test render update sales target page with missing parameter
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderUpdateValidationError()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update promotor sales target without target
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderUpdatePromotorNoTarget()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Remove previous target
        $this->promotor_target->removeByPromotor($ID, $month);
        
        // Set parameter
        $params = [
            'ID'    => $ID,
            'date'  => $month,
            'type'  => 'promotor'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/edit', $params);
        
        // Verify response
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update promotor sales target successfully
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderUpdatePromotorSuccess()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->promotor_ID,
            'date'  => $month,
            'type' => 'promotor'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/edit', $params);
        
        // Validate data
        $this->assertViewHas('ID',      $params['ID']);
        $this->assertViewHas('date',    $params['date']);
        $this->assertViewHas('type',    $params['type']);
        $this->assertViewHas('total',   $promotorTarget->total);
        $this->assertViewHas('dealer');
        $this->assertViewHas('promotor');
    }
    
    /**
     * Test render update dealer sales target successfully
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testRenderUpdateDealerSuccess()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->dealer_ID,
            'date'  => $month,
            'type' => 'dealer'
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/sales-target/edit', $params);
        
        $total = DB::table('promotor_targets')
                    ->where('dealer_ID', $params['ID'])
                    ->where('month', $month)
                    ->sum('total');
        
        // Validate data
        $this->assertViewHas('ID',      $params['ID']);
        $this->assertViewHas('date',    $params['date']);
        $this->assertViewHas('type',    $params['type']);
        $this->assertViewHas('total',   $total);
        $this->assertViewHas('dealer');
        $this->assertViewMissing('promotor');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/edit', $this->customPromotorTargetData);

        // Verify response
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update promotor sales target no target data
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleUpdatePromotorNoTarget()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Remove previous target
        $this->promotor_target->removeByPromotor($promotorTarget->promotor_ID, $month);
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->promotor_ID,
            'date'  => $month,
            'type'  => 'promotor',
            'total' => 1000000
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/edit', $params);
        
        // Verify response
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update promotor sales target success
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleUpdatePromotorSuccess()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->promotor_ID,
            'date'  => $month,
            'type'  => 'promotor',
            'total' => 1000000
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/edit', $params, [], [], ['HTTP_REFERER' => '/sales-target/edit']);
        
        // Verify response
        $this->assertRedirectedTo('/sales-target/edit');
        $this->assertSessionHas('target-updated', '');
        
        // Verify data
        $total = DB::table('promotor_targets')
                    ->where('promotor_ID', $params['ID'])
                    ->where('month', $month)
                    ->value('total');
        
        $this->assertEquals($total, $params['total']);
    }
    
    /**
     * Test handle update dealer sales target with update promotor target process
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleUpdateDealerUpdateTarget()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->dealer_ID,
            'date'  => $month,
            'type'  => 'dealer',
            'total' => 1000000
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/edit', $params, [], [], ['HTTP_REFERER' => '/sales-target/edit']);
        
        // Verify response
        $this->assertRedirectedTo('/sales-target/edit');
        $this->assertSessionHas('target-updated', '');
        
        // Verify data
        $totals = DB::table('promotor_targets')
                    ->where('dealer_ID', $params['ID'])
                    ->where('month', $month)
                    ->pluck('total');
        
        foreach ($totals as $total)
        {
            $this->assertEquals($total, floor($params['total']/count($totals)));
        }
        
    }
    
    /**
     * Test handle update dealer sales target with create promotor target process
     *
     * @access public
     * @group CMS
     * @group CMS-SalesTargetController
     * @return Void
     */
    public function testHandleUpdateDealerCreateTarget()
    {
        // Populate data
        $promotorTargetIDs = $this->_populate();

        // Set data
        $ID             = $this->_pickRandomItem($promotorTargetIDs);
        $promotorTarget = $this->promotor_target->getOne($ID);
        $month          = '2016-11';
        
        // Remove one target
        $this->promotor_target->removeByPromotor($promotorTarget->promotor_ID, $month);
        
        // Set parameter
        $params = [
            'ID'    => $promotorTarget->dealer_ID,
            'date'  => $month,
            'type'  => 'dealer',
            'total' => 1000000
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/sales-target/edit', $params, [], [], ['HTTP_REFERER' => '/sales-target/edit']);
        
        // Verify response
        $this->assertRedirectedTo('/sales-target/edit');
        $this->assertSessionHas('target-updated', '');
        
        // Verify data
        $totals = DB::table('promotor_targets')
                    ->where('dealer_ID', $params['ID'])
                    ->where('month', $month)
                    ->pluck('total');
        
        foreach ($totals as $total)
        {
            $this->assertEquals($total, floor($params['total']/count($totals)));
        }
    }
    
}

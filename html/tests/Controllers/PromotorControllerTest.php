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
use App\Http\Models\PromotorMetaModel;

class PromotorControllerTest extends TestCase
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
     * Promotor target model container
     *
     * @access protected
     */
    protected $promotorTarget;

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
            'name'          => 'Area Coordinator A',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 6002,
            'password'      => '123456',
            'name'          => 'Maryono',
            'gender'        => 'male',
            'type'          => 'tl',
            'parent_ID'     => 1
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4003,
            'password'      => '123456',
            'name'          => 'Area Coordinator C',
            'gender'        => 'male',
            'type'          => 'arco',
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
        $this->promotor_meta    = new PromotorMetaModel();
        $this->promotorTarget   = new PromotorTargetModel();
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
        
        return $promotorIDs;
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
     * Test render promotor index page
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor');
        
        // Verify response
        foreach ($this->promotorData as $promotor)
        {
            $this->assertPageContain($promotor['name']);
        }
        
        $this->assertViewHas('dealers');
        $this->assertViewHas('promotors');
        $this->assertViewHas('user_type');
    }
    
    /**
     * Test render create promotor page
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/create');
        
        // Verify response
        $this->assertPageContain('Create Promotor');

        foreach ($this->dealerData as $dealer)
        {
            $this->assertPageContain($dealer['name']);
        }

        foreach ($this->promotorData as $promotor)
        {
            $this->assertPageContain($promotor['name']);
        }

        $this->assertViewHas('dealers');
        $this->assertViewHas('user_type');
        $this->assertViewHas('parents');
    }
    
    /**
     * Test handle create promotor request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove promotor name
        $params = $this->customPromotorData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/create', $params, [], [], ['HTTP_REFERER' => '/promotor/create']);
        
        $this->assertRedirectedTo('/promotor/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create promotor request with success without parent ID
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleCreateWithoutParentID()
    {
        // Set parameter
        $params = $this->customPromotorData;
        
        // Remove parent ID
        unset($params['parent_ID']);
        
        // Do request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/create', $params, [], [], ['HTTP_REFERER' => '/promotor/create']);
        
        $this->assertRedirectedTo('/promotor');
        $this->assertSessionHas('promotor-created', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', 1)->first();
        
        $this->assertEquals($params['dealer_ID'],   $data->dealer_ID);
        $this->assertEquals($params['name'],        $data->name);
        $this->assertEquals($params['phone'],       $data->phone);
        $this->assertEquals($params['gender'],      $data->gender);
        $this->assertEquals(0, $data->parent_ID);
    }
    
    /**
     * Test handle create promotor request with success with parent ID
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleCreateSuccessWithParentID()
    {
        // Set parameter
        $params = $this->customPromotorData;
        
        // Do request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/create', $params, [], [], ['HTTP_REFERER' => '/promotor/create']);
        
        $this->assertRedirectedTo('/promotor');
        $this->assertSessionHas('promotor-created', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', 1)->first();
        
        $this->assertEquals($params['dealer_ID'],   $data->dealer_ID);
        $this->assertEquals($params['name'],        $data->name);
        $this->assertEquals($params['phone'],       $data->phone);
        $this->assertEquals($params['gender'],      $data->gender);
        $this->assertEquals($params['parent_ID'],   $data->parent_ID);
    }
    
    /**
     * Test handle create promotor request using non promotor type
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleCreateSuccessNonPromotor()
    {
        // Set parameter
        $params = $this->customPromotorData;
        $params['type'] = 'panasonic';
        
        // Do request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/create', $params, [], [], ['HTTP_REFERER' => '/promotor/create']);
        
        $this->assertRedirectedTo('/promotor');
        $this->assertSessionHas('promotor-created', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', 1)->first();
        
        $this->assertEquals(0,                      $data->dealer_ID);
        $this->assertEquals($params['name'],        $data->name);
        $this->assertEquals($params['phone'],       $data->phone);
        $this->assertEquals($params['gender'],      $data->gender);
        $this->assertEquals($params['parent_ID'],   $data->parent_ID);
    }
    
    /**
     * Test render update promotor page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update promotor page without valid promotor data
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/edit', ['ID' => 5]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update promotor page success
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID         = $this->_pickRandomItem($promotorIDs);
        $promotor   = $this->promotor->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('promotor', $promotor);
        $this->assertViewHas('dealers');
        $this->assertViewHas('isBlocked', false);
        $this->assertViewHas('user_type');
        $this->assertViewHas('parents');
        $this->assertPageContain('Edit Promotor');
    }
    
    /**
     * Test render update promotor page with block status
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderUpdateSuccessBlockedStatus()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID         = $this->_pickRandomItem($promotorIDs);
        
        // Block promotor
        $this->_populatePromotorMeta($ID);
        
        // Get promotor data
        $promotor   = $this->promotor->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('promotor', $promotor);
        $this->assertViewHas('dealers');
        $this->assertViewHas('isBlocked', true);
        $this->assertViewHas('user_type');
        $this->assertViewHas('parents');
        $this->assertPageContain('Edit Promotor');
    }
    
    /**
     * Test handle update request without ID
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateNoID()
    {
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $this->customPromotorData, [], [], ['HTTP_REFERER' => '/promotor/edit']);

        // Verify
        $this->assertResponseStatus(404);
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set parameter
        $params         = $this->customPromotorData;
        $params['ID']   = $promotorIDs[0];
        unset($params['name']);
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);

        // Verify
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = $this->customPromotorData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($promotorIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-updated', '');
        
        // Validate data
        $promotor = $this->promotor->getOne($ID);

        $this->assertEquals($promotor->dealer_ID,   $this->customPromotorData['dealer_ID']);
        $this->assertEquals($promotor->phone,       $this->customPromotorData['phone']);
        $this->assertEquals($promotor->name,        $this->customPromotorData['name']);
        $this->assertEquals($promotor->gender,      $this->customPromotorData['gender']);
        $this->assertEquals($promotor->type,        $this->customPromotorData['type']);
        $this->assertEquals($promotor->parent_ID,   $this->customPromotorData['parent_ID']);
    }
    
    /**
     * Test handle update request success with non promotor type
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateSuccessNonPromotor()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = $this->customPromotorData;
        $params['type'] = 'panasonic';
        
        // Add ID
        $ID             = $this->_pickRandomItem($promotorIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-updated', '');
        
        // Validate data
        $promotor = $this->promotor->getOne($ID);

        $this->assertEquals($promotor->dealer_ID,   0);
        $this->assertEquals($promotor->phone,       $this->customPromotorData['phone']);
        $this->assertEquals($promotor->name,        $this->customPromotorData['name']);
        $this->assertEquals($promotor->gender,      $this->customPromotorData['gender']);
        $this->assertEquals($promotor->type,        'panasonic');
        $this->assertEquals($promotor->parent_ID,   $this->customPromotorData['parent_ID']);
    }
    
    /**
     * Test handle update request success with non promotor type
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateSuccessNoDealer()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = $this->customPromotorData;
        $params['dealer_ID'] = 0;
        
        // Add ID
        $ID             = $this->_pickRandomItem($promotorIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-updated', '');
        
        // Validate data
        $promotor = $this->promotor->getOne($ID);

        $this->assertEquals($promotor->dealer_ID,   0);
        $this->assertEquals($promotor->phone,       $this->customPromotorData['phone']);
        $this->assertEquals($promotor->name,        $this->customPromotorData['name']);
        $this->assertEquals($promotor->gender,      $this->customPromotorData['gender']);
        $this->assertEquals($promotor->type,        $this->customPromotorData['type']);
        $this->assertEquals($promotor->parent_ID,   $this->customPromotorData['parent_ID']);
    }
    
    /**
     * Test handle update request success without parent ID
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUpdateSuccessWithoutParentID()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = $this->customPromotorData;
        
        unset($params['parent_ID']);
        
        // Add ID
        $ID             = $this->_pickRandomItem($promotorIDs);
        $params['ID']   = $ID;
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/promotor/edit', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-updated', '');
        
        // Validate data
        $promotor = $this->promotor->getOne($ID);

        $this->assertEquals($promotor->dealer_ID,   $this->customPromotorData['dealer_ID']);
        $this->assertEquals($promotor->phone,       $this->customPromotorData['phone']);
        $this->assertEquals($promotor->name,        $this->customPromotorData['name']);
        $this->assertEquals($promotor->gender,      $this->customPromotorData['gender']);
        $this->assertEquals($promotor->type,        $this->customPromotorData['type']);
        $this->assertEquals($promotor->parent_ID,   0);
    }
    
    /**
     * Test render remove promotor page without promotor ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove promotor page without valid promotor data
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/remove', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove promotor page
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID         = $this->_pickRandomItem($promotorIDs);
        $promotor   = $this->promotor->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Promotor');
    }
    
    /**
     * Test handle remove promotor request without promotor ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove promotor without valid promotor data
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/remove', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove promotor successfully
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($promotorIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/promotor/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/promotor');
        $this->assertSessionHas('promotor-deleted', '');
        
        // Validate data
        $promotor = $this->promotor->getOne($ID);
        $this->assertEquals(null, $promotor);
    }

    /**
     * Test handle block promotor request without promotor ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleBlockNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/block');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render block promotor page without valid promotor data
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleBlockNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/block', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test handle block promotor successfully
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleBlockSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = [
            'ID' => $this->_pickRandomItem($promotorIDs)
        ];
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/block', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-block', '');
        
        // Verify data
        $data = DB::table('promotor_meta')
                    ->where('promotor_ID', $params['ID'])
                    ->where('name', 'block')
                    ->first();
        
        $this->assertTrue(!is_null($data));
    }

    /**
     * Test handle unblock promotor successfully
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleUnblockSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set params
        $params = [];
        
        // Add ID
        $ID             = $this->_pickRandomItem($promotorIDs);

        //populate promotor meta data
        $promotorMeta   = $this->_populatePromotorMeta($ID);

        $params['ID']   = $ID;
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/promotor/block', $params, [], [], ['HTTP_REFERER' => '/promotor/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-unblock', '');
        
        // Verify data
        $data = DB::table('promotor_meta')
                    ->where('promotor_ID', $params['ID'])
                    ->where('name', 'block')
                    ->first();
        
        $this->assertTrue(is_null($data));
    }

    /**
     * Test handle logout promotor request without promotor ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleLogoutNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/logout');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render logout promotor page without valid promotor data
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleLogoutNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/logout', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test handle logout promotor successfully
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleLogoutSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($promotorIDs);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/logout', ['ID' => $ID], [], [], ['HTTP_REFERER' => '/promotor/edit']);

        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-logout', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', $ID)->value('user_token');
        
        $this->assertEquals($data, '');
    }
    
    /**
     * Test handle reset password promotor without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleResetPasswordNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/reset');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle reset password promotor without valid user
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleResetPasswordNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/reset', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle reset password promotor success
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleResetPasswordSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($promotorIDs);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/reset', ['ID' => $ID], [], [], ['HTTP_REFERER' => '/promotor/edit']);

        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-reset-password', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', $ID)->value('password');
        
        $this->assertEquals($data, 'havas');
    }
    
    /**
     * Test handle non active promotor request without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleNonActiveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/non-active');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle non active promotor request without valid user
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleNonActiveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/non-active', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle non active promotor request success
     *
     * @access public
     * @group CMS
     * @group CMS-PromotorController
     * @return Void
     */
    public function testHandleNonActiveSuccess()
    {
        // Populate data
        $promotorIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($promotorIDs);
        
        // Set target
        $targetID = $this->promotorTarget->create($ID, 0, 0, 0, 0, 100, date('Y-m'));
        
        $this->withSession($this->adminSession)
            ->call('GET', '/promotor/non-active', ['ID' => $ID], [], [], ['HTTP_REFERER' => '/promotor/edit']);

        // Validate response
        $this->assertRedirectedTo('/promotor/edit');
        $this->assertSessionHas('promotor-non-active', '');
        
        // Verify data
        $data = DB::table('promotors')->where('ID', $ID)->first();
        
        $this->assertEquals($data->dealer_ID,   0);
        $this->assertEquals($data->password,    'none');
        $this->assertEquals($data->type,        'non-active');
        
        $targetData = DB::table('promotor_targets')->where('ID', $targetID)->first();
        $this->assertEquals(null, $targetData);
    }
    
}

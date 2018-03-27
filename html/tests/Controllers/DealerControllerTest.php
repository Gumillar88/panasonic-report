<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\DealerModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;

class DealerControllerTest extends TestCase
{
    /**
     * Dealer model container
     *
     * @access Protected
     */
    protected $dealer;
    
    /**
     * Dealer Type model container
     *
     * @access Protected
     */
    protected $type;
    
    /**
     * Dealer Channel model container
     *
     * @access protected
     */
    protected $channel;

    /**
     * Dealer Account model container
     *
     * @access Protected
     */
    protected $account;

    /**
     * Branch model container
     *
     * @access Protected
     */
    protected $branch;

    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
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
            'promotor_ID'   => 2
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
            'promotor_ID'   => 1
        ],
        [        
            'name'          => 'Bandung',
            'region_ID'     => 2,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 3
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
     * Dealer type data sample
     *
     * @access protected
     */
    protected $dealerTypeData = [
        [
            'name'          => 'R1'
        ],
        [
            'name'          => 'R2'
        ],
        [
            'name'          => 'R3'
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
     * Custom user data sample
     *
     * @access protected
     */
    protected $customDealerData = [
        'dealer_account_ID' => 1,
        'dealer_type_ID'    => 1,
        'region_ID'         => 0,
        'branch_ID'         => 1,
        'dealer_channel_ID' => 1,
        'code'              => 11,
        'name'              => 'ELECTRONIC SOLUTION',
        'company'           => 'none',
        'address'           => 'none',
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->region   = new RegionModel();
        $this->dealer   = new DealerModel();
        $this->type     = new DealerTypeModel();
        $this->channel  = new DealerChannelModel();
        $this->account  = new DealerAccountModel();
        $this->branch   = new BranchModel();
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

        // Populate dealer account
        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->account->create(
                 $dealerAccount['name'],
                 $dealerAccount['branch_ID'],
                 $dealerAccount['promotor_ID']
             );
        }

        // Populate dealer tyoe
        foreach ($this->dealerTypeData as $dealerType)
        {
            $this->type->create(
                 $dealerType['name']
             );
        }

        // Populate dealer channel
        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $this->channel->create(
                 $dealerChannel['name']
             );
        }
        
        // Populate dealer
        $dealerIDs = [];
        
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

             $dealerIDs[] = $this->dealer->create($data);
        }
        
        return $dealerIDs;
    }
    
    /**
     * Test render dealer index page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer');
        
        // Verify response
        foreach ($this->dealerData as $dealer)
        {
            $this->assertPageContain($dealer['name']);
        }
        
        $this->assertViewHas('dealers');
        $this->assertViewHas('regions');
        $this->assertViewHas('branch');
        $this->assertViewHas('dealerChannels');
        $this->assertViewHas('dealerTypes');
        $this->assertViewHas('dealerAccounts');
    }
    
    /**
     * Test render create dealer page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/create');
        
        // Verify response
        $this->assertPageContain('Create Dealer');
        
        foreach ($this->branchData as $branch)
        {
            $this->assertPageContain($branch['name']);
        }

        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->assertPageContain($dealerAccount['name']);
        }
        
        foreach ($this->dealerTypeData as $dealerType)
        {
            $this->assertPageContain($dealerType['name']);
        }

        foreach ($this->dealerChannelData as $dealerChannel)
        {
            $this->assertPageContain($dealerChannel['name']);
        }

        $this->assertViewHas('branches');
        $this->assertViewHas('dealerChannels');
        $this->assertViewHas('dealerTypes');
        $this->assertViewHas('dealerAccounts');
    }
    
    /**
     * Test handle create dealer request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove dealer name
        $params = $this->customDealerData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer/create', $params, [], [], ['HTTP_REFERER' => '/dealer/create']);
        
        $this->assertRedirectedTo('/dealer/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create dealer request with success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer/create', $this->customDealerData, [], [], ['HTTP_REFERER' => '/dealer/create']);
        
        $this->assertRedirectedTo('/dealer');
        $this->assertSessionHas('dealer-created', '');
    }
    
    /**
     * Test render update dealer page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer page without valid dealer data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/edit', ['ID' => 5]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer page success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $dealerIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($dealerIDs);
        $dealer = $this->dealer->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('dealer', $dealer);
        $this->assertViewHas('branches');
        $this->assertViewHas('dealerChannels');
        $this->assertViewHas('dealerTypes');
        $this->assertViewHas('dealerAccounts');
        $this->assertPageContain('Edit Dealer');
    }
    
    /**
     * Test handle update request without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleUpdateNoID()
    {
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer/edit', $this->customDealerData, [], [], ['HTTP_REFERER' => '/dealer/edit']);
        
        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $dealerIDs = $this->_populate();
        
        // Set parameter
        $params         = $this->customDealerData;
        $params['ID']   = $dealerIDs[0];
        unset($params['name']);
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer/edit', $params, [], [], ['HTTP_REFERER' => '/dealer/edit']);
        
        // Verify
        $this->assertRedirectedTo('/dealer/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $dealerIDs = $this->_populate();
        
        // Set params
        $params = $this->customDealerData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($dealerIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer/edit', $params, [], [], ['HTTP_REFERER' => '/dealer/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/dealer/edit');
        $this->assertSessionHas('dealer-updated', '');
        
        // Validate data
        $dealer = $this->dealer->getOne($ID);

        $this->assertEquals($dealer->name, $this->customDealerData['name']);
        $this->assertEquals($dealer->dealer_account_ID, $this->customDealerData['dealer_account_ID']);
        $this->assertEquals($dealer->dealer_type_ID, $this->customDealerData['dealer_type_ID']);
        $this->assertEquals($dealer->branch_ID, $this->customDealerData['branch_ID']);
        $this->assertEquals($dealer->dealer_channel_ID, $this->customDealerData['dealer_channel_ID']);
        $this->assertEquals($dealer->code, $this->customDealerData['code']);
        $this->assertEquals($dealer->company, $this->customDealerData['company']);
        $this->assertEquals($dealer->address, $this->customDealerData['address']);

    }
    
    /**
     * Test render remove dealer page without dealer ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer page without valid dealer data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/remove', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $dealerIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($dealerIDs);
        $dealer = $this->dealer->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Dealer');
    }
    
    /**
     * Test handle remove dealer request without dealer ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer without valid dealer data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer/remove', ['ID' => 10]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DealerController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $dealerIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($dealerIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/dealer');
        $this->assertSessionHas('dealer-deleted', '');
        
        // Validate data
        $dealer = $this->dealer->getOne($ID);
        $this->assertEquals(null, $dealer);
    }
    
    
}

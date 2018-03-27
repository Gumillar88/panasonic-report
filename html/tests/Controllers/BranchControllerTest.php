<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\RegionModel;
use App\Http\Models\DealerModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\BranchModel;

class BranchControllerTest extends TestCase
{
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
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
            'type'          => 'tl',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4002,
            'password'      => '123456',
            'name'          => 'Area Coordinator B',
            'gender'        => 'male',
            'type'          => 'tl',
            'parent_ID'     => 0
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
            'promotor_ID'   => 1
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 1
        ],
    ];
    
    /**
     * Custom user data sample
     *
     * @access protected
     */
    protected $customBranchData = [
        'name'          => 'Palembang',
        'region_ID'     => 3,
        'promotor_ID'   => 2
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
        $this->promotor = new PromotorModel();
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
        // Populate region
        foreach ($this->regionData as $region)
        {
             $regionIDs[] = $this->region->create(
                 $region['name'],
                 $region['promotor_ID']
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
        
        // Populate branch
        $branchIDs = [];
        
        foreach ($this->branchData as $branch)
        {
             $branchIDs[] = $this->branch->create(
                 $branch['name'],
                 $branch['region_ID'],
                 $branch['promotor_ID']
             );
        }
        
        return $branchIDs;
    }
    
    /**
     * Test render branch index page
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/branch');
        
        // Verify response
        foreach ($this->branchData as $branch)
        {
            $this->assertPageContain($branch['name']);
        }
        
        $this->assertViewHas('regions');
        $this->assertViewHas('dataTl');
        $this->assertViewHas('branches');
    }
    
    /**
     * Test render create branch page
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/create');
        
        // Verify response
        $this->assertPageContain('Create Branch');
        
        foreach ($this->promotorData as $promotor)
        {
            $this->assertPageContain($promotor['name']);
        }
        
        foreach ($this->regionData as $region)
        {
            $this->assertPageContain($region['name']);
        }
        
        $this->assertViewHas('dataTl');
        $this->assertViewHas('regions');
    }
    
    /**
     * Test handle create branch request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove branch name
        $params = $this->customBranchData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/branch/create', $params, [], [], ['HTTP_REFERER' => '/branch/create']);
        
        $this->assertRedirectedTo('/branch/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create branch request with success
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Populate data
        $this->_populate();
        
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/branch/create', $this->customBranchData, [], [], ['HTTP_REFERER' => '/branch/create']);
        
        $this->assertRedirectedTo('/branch');
        $this->assertSessionHas('branch-created', '');
    }
    
    /**
     * Test render update branch page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update branch page without valid branch data
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderUpdateNoBranch()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update branch page success
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $branchIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($branchIDs);
        $branch = $this->branch->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('branch', $branch);
        $this->assertViewHas('dataTl');
        $this->assertPageContain('Edit Branch');
    }

    
    /**
     * Test handle update branch request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/branch/edit', $this->customBranchData, [], [], ['HTTP_REFERER' => '/branch/edit']);
        
        // Verify
        $this->assertRedirectedTo('/branch/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $branchIDs = $this->_populate();
        
        // Set params
        $params = $this->customBranchData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($branchIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/branch/edit', $params, [], [], ['HTTP_REFERER' => '/branch/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/branch/edit');
        $this->assertSessionHas('branch-updated', '');
        
        // Validate data
        $branch = $this->branch->getOne($ID);
        $this->assertEquals($branch->name, $this->customBranchData['name']);
        $this->assertEquals($branch->promotor_ID, $this->customBranchData['promotor_ID']);
    }
    
    /**
     * Test render remove branch page without branch ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove branch page without valid branch data
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderRemoveNoBranch()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove branch page
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $branchIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($branchIDs);
        $branch = $this->branch->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/branch/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('branch', $branch);
        $this->assertPageContain('Remove Branch');
    }
    
    /**
     * Test handle remove branch request without region ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/branch/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove branch without valid branch data
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleRemoveNoBranch()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/branch/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove branch successfully
     *
     * @access public
     * @group CMS
     * @group CMS-BranchController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $branchIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($branchIDs);
        
        $dealerID = $this->dealer->create([
            'region_ID'         => 1,
            'branch_ID'         => $ID,
            'dealer_account_ID' => 0,
            'dealer_type_ID'    => 0,
            'dealer_channel_ID' => 0,
            'code'              => 'ABCD',
            'name'              => 'Dealer A',
            'company'           => 'none',
            'address'           => 'none'
        ]);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/branch/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/branch');
        $this->assertSessionHas('branch-deleted', '');
        
        // Validate data
        $branch = $this->branch->getOne($ID);
        $this->assertEquals(null, $branch);
        
        $dealer = $this->dealer->getOne($dealerID);
        $this->assertEquals(0, $dealer->branch_ID);
    }
    
    
}

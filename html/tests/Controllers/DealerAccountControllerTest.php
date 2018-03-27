<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\BranchModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;

class DealerAccountControllerTest extends TestCase
{
    /**
     * dealer account model container
     *
     * @access Protected
     */
    protected $dealer_account;
    
    /**
     * branch model container
     *
     * @access Protected
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
     * Dealer account data sample
     *
     * @access protected
     */
    protected $dealerAccountData = [
        [
            'name'          => 'Electronic City',
            'branch_ID'     => 1,
            'promotor_ID'   => 2,
        ],
        [
            'name'          => 'Best Denki',
            'branch_ID'     => 2,
            'promotor_ID'   => 2
        ],
        [
            'name'          => 'Electronic Solution',
            'branch_ID'     => 3,
            'promotor_ID'   => 2
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
            'promotor_ID'   => 1
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 1
        ],
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
        ]
    ];
    
    /**
     * Custom dealer account data sample
     *
     * @access protected
     */
    protected $customDealerAccountData = [
        'name'          => 'Benua',
        'branch_ID'     => 1,
        'promotor_ID'   => 1
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer_account   = new DealerAccountModel();
        $this->branch           = new BranchModel();
        $this->promotor         = new PromotorModel();
    }
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate branch
        foreach ($this->branchData as $branch)
        {
             $this->branch->create(
                 $branch['name'],
                 $branch['region_ID'],
                 $branch['promotor_ID']
             );
        }
        
        // Populate promotor
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
        
        // Populate dealer account
        $dealerAccountIDs = [];
        
        foreach ($this->dealerAccountData as $dealerAccount)
        {
             $dealerAccountIDs[] = $this->dealer_account->create(
                 $dealerAccount['name'],
                 $dealerAccount['branch_ID'],
                 $dealerAccount['promotor_ID']
             );
        }
        
        return $dealerAccountIDs;
    }
    
    /**
     * Test render dealer account index page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account');
        
        // Verify response
        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->assertPageContain($dealerAccount['name']);
        }
        
        $this->assertViewHas('dealer_accounts');
        $this->assertViewHas('branch');
    }
    
    /**
     * Test render create dealer account page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/create');
        
        // Verify response
        $this->assertPageContain('Create Dealer Account');
        
        foreach ($this->branchData as $branch)
        {
            $this->assertPageContain($branch['name']);
        }
        
        $this->assertViewHas('dataBranch');
    }
    
    /**
     * Test handle create dealer account request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove dealer account name
        $params = $this->customDealerAccountData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/create', $params, [], [], ['HTTP_REFERER' => '/dealer-account/create']);
        
        $this->assertRedirectedTo('/dealer-account/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create dealer account request with success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Populate data
        $this->_populate();
        
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/create', $this->customDealerAccountData, [], [], ['HTTP_REFERER' => '/dealer-account/create']);
        
        $this->assertRedirectedTo('/dealer-account');
        $this->assertSessionHas('dealer-account-created', '');
    }
    
    /**
     * Test render update dealer account page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer account page without valid dealer account data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer account page success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $dealerAccountIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerAccountIDs);
        $dealerAccount  = $this->dealer_account->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('dealer_account', $dealerAccount);
        $this->assertViewHas('dataBranch');
        $this->assertPageContain('Edit Dealer Account');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/edit', $this->customDealerAccountData, [], [], ['HTTP_REFERER' => '/dealer-account/edit']);
        
        // Verify
        $this->assertRedirectedTo('/dealer-account/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $dealerAccountID = $this->_populate();
        
        // Set params
        $params = $this->customDealerAccountData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($dealerAccountID);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/edit', $params, [], [], ['HTTP_REFERER' => '/dealer-account/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-account/edit');
        $this->assertSessionHas('dealer-account-updated', '');
        
        // Validate data
        $dealerAccount = $this->dealer_account->getOne($ID);
        $this->assertEquals($dealerAccount->name, $this->customDealerAccountData['name']);
        $this->assertEquals($dealerAccount->branch_ID, $this->customDealerAccountData['branch_ID']);
    }
    
    /**
     * Test render remove dealer account page without dealer account ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer account page without valid dealer account data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer account page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $dealerAccountIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerAccountIDs);
        $dealerAccount  = $this->dealer_account->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-account/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Dealer Account');
    }
    
    /**
     * Test handle remove dealer account request without dealer account ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer account without valid dealer account data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer account successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DealerAccountController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $dealerAccountIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($dealerAccountIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-account/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-account');
        $this->assertSessionHas('dealer-account-deleted', '');
        
        // Validate data
        $dealerAccount = $this->dealer_account->getOne($ID);
        $this->assertEquals(null, $dealerAccount);
        
    }
    
    
}

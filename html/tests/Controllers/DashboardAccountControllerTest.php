<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\DashboardAccountModel;
use App\Http\Models\DashboardTokenModel;

class DashboardAccountControllerTest extends TestCase
{
     /**
     * Dashboard account model container
     *
     * @access protected
     */
    protected $account;
    
    /**
     * Dashboard token model container
     *
     * @access protected
     */
    protected $token;
    
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
     * Account data sample
     *
     * @access protected
     */
    protected $accountData = [
        [
            'email'         => 'alfian.sibuea@gmail.com',
            'name'          => 'Alfian',
            'last_access'   => 0,
        ],
        [
            'email'         => 'alfiana.sibuea@havas.com',
            'name'          => 'Alfiana Sibuea',
            'last_access'   => 0,
        ],
        [
            'email'         => 'dimas.prasetyo@id.panasonic.com',
            'name'          => 'Dimas',
            'last_access'   => 0,
        ]
    ];

    /**
     * Token account data sample
     *
     * @access protected
     */
    protected $tokenData = [
        [
            'dashboard_account_ID'  => 1,
            'token'                 => 'QqxWvFH5TzX8iJbClcRQINrevIZ9fjKi',
        ],
        [
            'dashboard_account_ID'  => 2,
            'token'                 => 'MUf6dhl9n0NqWwego9fDCUKLdMZk71RS',
        ],
        [
            'dashboard_account_ID'  => 3,
            'token'                 => 'oyuFZFUqlGjptyJqKjPohEuhPEu4hLtr',
        ]
    ];
    
    /**
     * Custom account data sample
     *
     * @access protected
     */
    protected $customAccountData = [
        'email'         => 'ferry.a@id.panasonic.com',
        'name'          => 'Ferry ',
        'last_access'   => 0,
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->account  = new DashboardAccountModel();
        $this->token    = new DashboardTokenModel();
    }
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        //Populate token
        foreach ($this->tokenData as $token)
        {
             $this->token->create(
                 $token['dashboard_account_ID'],
                 $token['token']
             );
        }

        // Populate account
        $accountIDs = [];
        
        foreach ($this->accountData as $account)
        {
             $accountIDs[] = $this->account->create(
                 $account['email'],
                 $account['name'],
                 $account['last_access']
             );
        }
        
        return $accountIDs;
    }
    
    /**
     * Test render account index page
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account');
        
        // Verify response
        foreach ($this->accountData as $account)
        {
            $this->assertPageContain($account['name']);
        }
        
        $this->assertViewHas('accounts');
    }
    
    /**
     * Test render create account page
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/create');
        
        // Verify response
        $this->assertPageContain('Create Dashboard Account');
    }
    
    /**
     * Test handle create account request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove account title
        $params = $this->accountData;
        unset($params['email']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/create', $params, [], [], ['HTTP_REFERER' => '/dashboard-account/create']);
        
        $this->assertRedirectedTo('/dashboard-account/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create account request with success
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/create', $this->customAccountData, [], [], ['HTTP_REFERER' => '/dashboard-account/create']);

        $this->assertRedirectedTo('/dashboard-account');
        $this->assertSessionHas('account-created', '');
    }
    
    /**
     * Test render update account page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update account page without valid account data
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update account page success
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $accountIDs = $this->_populate();
        
        // Set data
        $ID                = $this->_pickRandomItem($accountIDs);
        $account           = $this->account->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('account', $account);
        $this->assertViewHas('tokens');
        $this->assertPageContain('Edit Dashboard Article');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/edit', $this->customAccountData, [], [], ['HTTP_REFERER' => '/dashboard-account/edit']);

        // Verify
        $this->assertRedirectedTo('/dashboard-account/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $accountID = $this->_populate();
        
        // Set params
        $params = $this->customAccountData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($accountID);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/edit', $params, [], [], ['HTTP_REFERER' => '/dashboard-account/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/dashboard-account/edit');
        $this->assertSessionHas('account-updated', '');
        
        // Validate data
        $account = $this->account->getOne($ID);
        $this->assertEquals($account->name, $this->customAccountData['name']);
        $this->assertEquals($account->email, $this->customAccountData['email']);
    }
    
    /**
     * Test render remove account page without account ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove account page without valid account data
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove account page
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $accountIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($accountIDs);
        $account        = $this->account->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('account', $account);
        $this->assertPageContain('Remove Dashboard Account');
    }
    
    /**
     * Test handle remove account request without account ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove account without valid account data
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove account successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $newsIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($newsIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dashboard-account/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/dashboard-account');
        $this->assertSessionHas('account-removed', '');
        
        // Validate data
        $account = $this->account->getOne($ID);
        $this->assertEquals(null, $account);
        
    }

    /**
     * Test handle remove token account successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardAccountController
     * @return Void
     */
    public function testHandleRemoveTokenSuccess()
    {
        // Populate data
        $accountIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($accountIDs);

        $token = $this->token->getByDashboardAccount($ID);
        $tokenID = $token[0]->ID;
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/dashboard-account/remove-token', ['tokenID' => $tokenID]);
        
        // Validate response
        $this->assertRedirectedTo('/dashboard-account/edit?ID='.$ID);
        $this->assertSessionHas('token-removed', '');
        
        // Validate data
        $token = $this->token->getByDashboardAccount($ID);
        $this->assertEquals([], $token);
        
    }
    
    
}

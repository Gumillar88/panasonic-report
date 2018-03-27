<?php
namespace Tests\Controllers;

use Hash;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\UserModel;

class AuthControllerTest extends TestCase
{
    /**
     * User model container
     *
     * @access protected
     */
    protected $user;
    
    /**
     * User data sample
     *
     * @access protected
     */
    protected $userData = [
        [
            'fullname'  => 'Alfiana Sibuea',
            'username'  => 'fian',
            'password'  => '123456',
            'type'      => 'admin'
        ],
        [
            'fullname'  => 'Indra Lionardy',
            'username'  => 'indra',
            'password'  => '654321',
            'type'      => 'normal'
        ],
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->user = new UserModel();
    }
    
    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        $userIDs = [];
        
        foreach ($this->userData as $user)
        {
             $userIDs[] = $this->user->create(
                 $user['fullname'], 
                 $user['username'], 
                 Hash::make($user['password']), 
                 $user['type']
             );
        }
        
        return $userIDs;
    }
    
    /**
     * Test render login page with user session
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testRenderLoginWithUserSession()
    {
        // Make request
        $this->withSession(['user_ID' => 1])
            ->call('GET', '/login');
        
        // Verify response
        $this->assertRedirectedTo('/');
    }
    
    /**
     * Test render login success
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testRenderLoginSuccess()
    {
        // Make request
        $this->call('GET', '/login');
        
        // Verify response
        $this->assertResponseOk();
    }
    
    /**
     * Test handle login request without valid user data
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testHandleLoginNoUser()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = [
            'username' => 'hello',
            'password' => '1234',
        ];
        
        // Make request
        $this->call('POST', '/login', $params, [], [], ['HTTP_REFERER' => '/login']);
        
        // Verify response
        $this->assertRedirectedTo('/login');
        $this->assertSessionHas('login-error', 'login-error');
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle login request with no match password input
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testHandleLoginPasswordNotMatch()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = rand(0, 1)+1;
        $user   = $this->user->getOne($ID);
        
        // Set params
        $params = [
            'username' => $user->username,
            'password' => '1234',
        ];
        
        // Make request
        $this->call('POST', '/login', $params, [], [], ['HTTP_REFERER' => '/login']);
        
        // Verify response
        $this->assertRedirectedTo('/login');
        $this->assertSessionHas('login-error', 'login-error');
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle login request success
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testHandleLoginSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = rand(0, 1)+1;
        $user   = $this->user->getOne($ID);
        
        // Set params
        $params = [
            'username' => $user->username,
            'password' => $this->userData[$ID-1]['password'],
        ];
        
        // Make request
        $this->call('POST', '/login', $params, [], [], ['HTTP_REFERER' => '/login']);
        
        // Verify response
        $this->assertRedirectedTo('/');
        $this->assertSessionHas('user_ID', $user->ID);
        $this->assertSessionHas('user_status', $user->type);
        
    }
    
    
    
    /**
     * Test render homepage with user session
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testRenderHomepage()
    {
        // Make request
        $this->withSession(['user_ID' => 1])
            ->call('GET', '/');
        
        // Verify response
        $this->assertPageContain('Home');
    }
    
    /**
     * Test handle logout request
     *
     * @access public
     * @group CMS
     * @group CMS-AuthController
     * @return Void
     */
    public function testHandleLogout()
    {
        // Setup session
        $session = [
            'user_ID'       => 1, 
            'user_status'   => 'normal'
        ];
        
        // Make request
        $this->withSession($session)
            ->call('GET', '/logout');
        
        // Verify response
        $this->assertRedirectedTo('/login');
        $this->assertSessionMissing('user_ID');
        $this->assertSessionMissing('user_status');
        
    }
    
}

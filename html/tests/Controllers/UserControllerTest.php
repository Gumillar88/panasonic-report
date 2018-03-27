<?php
namespace Tests\Controllers;

use Hash;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\UserModel;

class UserControllerTest extends TestCase
{
    /**
     * User model container
     *
     * @access protected
     */
    protected $user;
    
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
     * User types data sample
     *
     * @access protected
     */
    protected $userTypes = [
        'normal'    => 'Normal',
        'admin'     => 'Admin'
    ];
    
    /**
     * Custom user data sample
     *
     * @access protected
     */
    protected $customUserData = [
        'fullname'  => 'Gumilar Lesmana',
        'username'  => 'gumilar',
        'password'  => '111111',
        'type'      => 'normal'
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
     * Test render user index page
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Visit page
        $this->withSession($this->adminSession)
            ->visit('/user')
            ->see('Users')
            ->see($this->userData[0]['fullname'])
            ->see($this->userData[1]['fullname']);
    }
    
    /**
     * Test render create user page
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Visit page
        $this->withSession($this->adminSession)
            ->visit('/user/create')
            ->see('Create User');
        
        // Check view data
        $this->assertViewHas('types', [
            'normal'    => 'Normal',
            'admin'     => 'Admin'
        ]);
    }
    
    /**
     * Test handle create user request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove fullname
        $params = $this->customUserData;
        unset($params['fullname']);
        
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/user/create', $params, [], [], ['HTTP_REFERER' => '/user/create']);
        
        $this->assertRedirectedTo('/user/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create user request with success
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/user/create', $this->customUserData, [], [], ['HTTP_REFERER' => '/user/create']);
        
        $this->assertRedirectedTo('/user');
        $this->assertSessionHas('user-created', '');
    }
    
    /**
     * Test render update user pageout ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/user/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update user page without valid user
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/user/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update user page with valid user ID
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = rand(0, 1)+1;
        $user   = $this->user->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/user/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('user', $user);
        $this->assertViewHas('types', $this->userTypes);
    }
    
    /**
     * Test handle update request without user ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleUpdateNoID()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/user/edit', $this->customUserData, [], [], ['HTTP_REFERER' => '/user/edit']);
        
        // Verify
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customUserData;
        
        // Add ID
        $ID = rand(0, 1)+1;
        $params['ID'] = $ID;
        
        // Remove fullname
        unset($params['fullname']);
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/user/edit', $params, [], [], ['HTTP_REFERER' => '/user/edit']);
        
        // Verify
        $this->assertRedirectedTo('/user/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request without password change
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleUpdateWithoutPassword()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customUserData;
        
        // Add ID
        $ID = rand(0, 1)+1;
        $params['ID'] = $ID;
        
        // Remove type and remove password
        unset($params['password']);
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/user/edit', $params, [], [], ['HTTP_REFERER' => '/user/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/user/edit');
        $this->assertSessionHas('user-updated', '');
        
        // Validate data
        $user = $this->user->getOne($ID);
        $this->assertEquals($user->fullname, $this->customUserData['fullname']);
        $this->assertEquals($user->username, $this->customUserData['username']);
        $this->assertEquals(false, Hash::check($this->customUserData['password'], $user->password));
    }
    
    /**
     * Test handle update request with password change
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleUpdateWithPassword()
    {
        // Populate data
        $this->_populate();
        
        // Set params
        $params = $this->customUserData;
        
        // Add ID
        $ID = rand(0, 1)+1;
        $params['ID'] = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/user/edit', $params, [], [], ['HTTP_REFERER' => '/user/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/user/edit');
        $this->assertSessionHas('user-updated', '');
        
        // Validate data
        $user = $this->user->getOne($ID);
        $this->assertEquals($user->fullname, $this->customUserData['fullname']);
        $this->assertEquals($user->username, $this->customUserData['username']);
        $this->assertEquals(true, Hash::check($this->customUserData['password'], $user->password));
    }
    
    /**
     * Test render remove user page without user ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/user/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove user page without valid user data
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/user/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove user page
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = rand(0, 1)+1;
        $user   = $this->user->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/user/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('user', $user);
    }
    
    /**
     * Test handle remove user request without user ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/user/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove primary admin user request
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleRemoveAdmin()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/user/remove', ['ID' => 1]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove user without valid user data
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/user/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove user successfully
     *
     * @access public
     * @group CMS
     * @group CMS-UserController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = 2;
        
        $this->withSession($this->adminSession)
            ->call('POST', '/user/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/user');
        $this->assertSessionHas('user-deleted', '');
        
        // Validate data
        $user   = $this->user->getOne($ID);
        $this->assertEquals(null, $user);
    }
    
    
}

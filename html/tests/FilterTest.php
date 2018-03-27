<?php
namespace Tests;

use Hash;
use TestCase;

use App\Http\Models\UserModel;

class FilterTest extends TestCase
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
     * Test access CMS without user ID in session
     *
     * @access public
     * @group Filter
     * @return Void
     */
    public function testAccessCMSWithoutUserID()
    {
        // Make request
        $this->call('GET', '/');
        
        // Verify response
        $this->assertRedirectedTo('/login');
    }
    
    /**
     * Test access CMS with user ID in session
     *
     * @access public
     * @group Filter
     * @return Void
     */
    public function testAccessCMSWithUserID()
    {
        // Make request
        $this->withSession(['user_ID' => 1])
            ->call('GET', '/login');
        
        // Verify response
        $this->assertRedirectedTo('/');
    }
    
    /**
     * Test access Super Admin CMS without super admin user
     *
     * @access public
     * @group Filter
     * @return Void
     */
    public function testAccessCMSAdminWithoutAdminUser()
    {
        // Make request
        $this->withSession(['user_ID' => 1])
            ->call('GET', '/user');
        
        // Verify response
        $this->assertRedirectedTo('/');
    }
    
    /**
     * Test access Super Admin CMS with super admin user
     *
     * @access public
     * @group Filter
     * @return Void
     */
    public function testAccessCMSAdminWithAdminUser()
    {
        // Make request
        $this->withSession(['user_ID' => 1, 'user_status' => 'admin'])
            ->call('GET', '/user');
        
        // Verify response
        $this->assertResponseOk();
    }
}

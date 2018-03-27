<?php
namespace Tests\Controllers;

use Hash;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\UserModel;
use App\Http\Models\LogModel;
use App\Http\Models\UserMetaModel;

class PasswordControllerTest extends TestCase
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
        'user_status'   => ['admin']
    ];

    /**
     * User data sample
     *
     * @access protected
     */
    protected $userData = [
        [
            'fullname'      => 'gumilarlesmana',
            'email'         => 'admas@gmail.com',
            'password'      => '12345678',
            'type'          => 'admin'
        ],
        [
            'fullname'      => 'gumilarlesmana',
            'email'         => 'indralesmana@havas.com',
            'password'      => '12345678',
            'type'          => 'admin'
        ],
    ];

    /**
     * User Meta data sample
     *
     * @access protected
     */
    protected $userMetaData = [
        [
            'user_ID'       => 1,
            'name'          => 'token',
            'content'       => 'S59MT8fyHmyb0QvOj6xpuGTDeeiOuBPA',
        ],
        [
            'user_ID'       => 2,
            'name'          => 'token',
            'content'       => 'S59MT8fyHmyb0QvOj6xpuGTDEeiOuBP0',
        ],
    ];

    /**
    *
    * @access protected
    */ 
    protected $userTypes = [
        'admin'     => 'Admin',
        'normal'    => 'Normal',
    ];

    /**
     * Custom user data sample
     *
     * @access protected
     */
    protected $customUserData = [
        'fullname'          => 'gumilarlesmana',
        'email'             => 'admin@gmail.com',
        'password'          => '12345678'
    ];

    /**
     * Custom user meta data sample
     *
     * @access protected
     */
    protected $customUserMetaData = [
        'user_ID'           => '1',
        'name'              => 'token',
        'content'           => 'S59MT8fyHmyb0QvOj6xpuGTDeeiOuBPA'
    ];

    /**
    * Object Constructor 
    *
    * @access public
    * @return Void
    */ 
    public function __construct()
    {
        $this->user         = new UserModel();
        $this->log          = new LogModel();
        $this->user_meta    = new UserMetaModel();
    }

    /**
    * Populate database user data
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
                $user['email'], 
                Hash::make($user['password']),
                $user['type']
            );
        }
        return $userIDs;
    }

    /**
    * Populate database user meta data
    *
    * @access private
    * @return Array
    */
    private function _populateMeta()
    {
        $userMetaIDs = [];

        foreach ($this->userMetaData as $user)
        {
            $userMetaIDs[] = $this->user_meta->set(
                $user['user_ID'], 
                $user['name'], 
                $user['content']
            );
        }

        return $userMetaIDs;
    }

    /**
     * Test render Forgot index page
     *
     * @access public
     * @group CMS
     * @group CMS-PasswordController
     * @return Void
     */
    public function testRenderForgotIndex()
    {
		// Make Request
        $response = $this->withSession($this->adminSession)
                ->call('GET', $this->appHomeSection.'password/forgot');

        $users = [];
        // Verify response
        foreach ($this->userData as $user)
        {
            $users[] = $this->user->getOne(
                $user['fullname'],
                $user['email'],
                $user['type']
            );

        }

        return $users;
        
        $response->assertViewHas($this->appHomeSection.'password/forgot');
    }

    /**
    * Test handle forgot password with email validation error
    *
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleForgotValidationEmailError()
    {
        // populate data
        $forgotID   = $this->_populate();

        // set data
        $ID     = $this->_pickRandomItem($forgotID);
        $user   = $this->user->getOne($ID);

        // set params
        $params = [
            'email' => $user->email
        ];
        // make Request
        $response = $this->call('POST', $this->appHomeSection.'/password/forgot', [], [], [], ['HTTP_REFERER' => $this->appHomeSection.'/password/forgot']);

        // verify Response
        // $response->assertRedirect($this->appHomeSection.'password/forgot');
        // $response->assertSessionHas('errors');
        
    }

    /**
    * Test handle forgot password with email Expired
    *
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleForgotValidationEmailExpired()
    {
        // populate data
        $forgotID = $this->_populate();

        // set data
        $ID     = $this->_pickRandomItem($forgotID);
        $user   = $this->user->getOne($ID);

        // set params
        $params = [
            'email' => $user->email
        ];

        // make request
        $response = $this->call('POST', $this->appHomeSection.'password/forgot', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/forgot']);

        // verify response
        // $response->assertRedirect($this->appHomeSection.'password/forgot');
        // $response->assertSessionHas('expired', '');
    }

    /**
    * Test handle forgot password with email validation error email exist
    *
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleForgotValidationEmailExist()
    {
        // populate data
        $forgotID   = $this->_populate();

        // set data
        $ID     = $this->_pickRandomItem($forgotID);
        $user   = $this->user->getOne($ID);

        // set params
        $params = [
            'email' => $user->email
        ];

        // make Request
        $response = $this->call('POST', $this->appHomeSection.'password/forgot', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/forgot']);

        // verify Response
        // $response->assertRedirect($this->appHomeSection.'password/forgot');
        // $response->assertSessionHas('errors', '');
    }

    /**
     * Test handle forgot request success send email
     *
     * @access public
     * @group CMS
     * @group CMS-PasswordController
    * @return Void
     */
    public function testHandleForgotAuthSuccessSendEmail()
    {
        // Populate data
        $forgotID = $this->_populate();
        
        // Set data
        $ID   = $this->_pickRandomItem($forgotID);
        
        $user = $this->user->getOne($ID);
        
        // Set code
        $code = str_random(32);
        
        // Save access key to user meta
        $this->user_meta->set($user->ID, 'token', $code);

        // Make request
        $response = $this->call('GET', $this->appHomeSection.'password/forgot/'.$code);


        // Verify response
        // $response->assertSessionHas('forgot-notes', '');
        
    }

    /**
    * Test Handle Forgot Log data
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleForgotLogRecordData()
    {
        //  populate data
        $forgotID = $this->_populate();

        //  get ID user data
        $ID     = $this->_pickRandomItem($forgotID);
        $user   = $this->user->getOne($ID);

        $params = [
                'ID'        => $ID,
                'email'     => $user->email
        ];
        
        $action = 'Forgot Password';
        // save record data
        $this->log->record($user->ID, $action, $params);

        // make Response
        $response = $this->call('POST', $this->appHomeSection.'password/forgot', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/forgot/']);

        // verify response
        // $response->assertSessionHas('forgot-notes', '');
    }

    /**
    * Test render Reset page Index No Token
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testRenderResetIndexNoToken()
    {
        $this->_populateMeta();
        
        // get token
        $token = '';

        // params
        $params = [
            'token'     => $token
        ];

        $response = $this->call('GET', $this->appHomeSection.'/password/reset?token='.$token, $params);
        
        // $response->assertResponseStatus(404);
    }

    /**
    * Test render Reset page Index Get By Content
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testRenderResetIndexGetByContent()
    {
        // get populate user meta data
        $resetID = $this->_populateMeta();
        
        //  set data
        $meta   = $this->user_meta->getOne($resetID);

        // get token
        $token = $meta->content;

        // get params
        $params = [
            'token'     => $token
        ];

        $response = $this->call('GET', $this->appHomeSection.'password/reset?token='.$token, $params);
        
        // $response->assertResponseStatus(404);
        // $response->assertSessionHas('expired', '');
    }

    /**
    * Test render Reset page Index No get by content
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testRenderResetIndexNoGetByContent()
    {
        $this->_populateMeta();
        
        // get token
        $token = 'SDPmqsafQqDNzgFKn4FI7gFakNy8GTQE';

        // get params
        $params = [
            'token'     => $token
        ];

        // make response
        $response = $this->withSession($this->adminSession)
                    ->call('GET', $this->appHomeSection.'/password/reset?token='.$token, $params);
        
        // $response->assertStatus(404);
    }

    /**
    * Test render Reset page Index get data user  
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testRenderIndexGetDataUser()
    {
        // populate user data
        $userID = $this->_populate();
        
        // get user data
        $user = $this->user->getOne($userID);

        // populate user meta data
        $metaID = $this->_populateMeta();

        // get user meta data
        $meta = $this->user_meta->getOne($metaID);
        
        $token = $meta->content;

        $params = [
            'content' => $token,
            'user'    => $user
        ];
        
        $response = $this->withSession($this->adminSession)
                    ->call('GET', $this->appHomeSection.'password/reset?token='.$token, $params);
        
        // $response->assertViewHas('token');
        // $response->assertViewHas('user');

    }

    /**
    * Test render Reset page Index if his data more than an hour  
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testRenderIndexCheckTimeMoreThanOneHour()
    {
        
        // populate user data
        $userID = $this->_populate();

        $ID     = $this->_pickRandomItem($userID);

        // get user data
        $user = $this->user->getOne($ID);
        
        // populate user meta data
        $metaID = $this->_populateMeta();

        // get user meta data
        $meta = $this->user_meta->getByName($ID, 'token');

        // get time created user meta data
        $time = $meta->created - 3800;

        // get token user meta data
        $token = $meta->content;

        // get time
        $data['created'] = $time;

        // update created user meta data
        $this->user_meta->update($meta->ID, $data);

        // params
        $params = [
            'token' => $token
        ];

        // make response
        $response = $this->withSession($this->adminSession)
                    ->call('GET', $this->appHomeSection.'password/reset', $params, $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'/password/reset']);
        
        // remove user ID and token user meta data
        // $this->user_meta->remove($meta->user_ID, 'token');
        // $response->assertSessionHas('expired');
    }

    /**
    * Test Handle Reset Password without password parameter
    *
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleResetValidationWithoutPassword()
    {
        // get params
        $params = [];

        $response = $this->call('POST', $this->appHomeSection.'password/reset', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'/password/reset']);
        // $response->assertSessionHas('errors');
    }

    /**
    * Test Handle Reset Password with password less than 8 characters
    *
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleResetValidationPasswordlessThanEightCharacters()
    {
        // get params
        $params = [
            'password' => str_random(5)
        ];

        $response = $this->call('POST', $this->appHomeSection.'password/reset', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'/password/reset']);
        // $response->assertSessionHas('errors');


    }

    /**
    * Test Handle Reset page without token
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testHandleResetIndexNoToken()
    {
        $password = str_random(32);
        // get params
        $params = [
            'password'                  => $password,
            'password_confirmation'     => $password,
            'token'                     => ''
        ];

        $response = $this->call('POST', $this->appHomeSection.'password/reset', $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/reset']);
        
        // $response->assertStatus(404);

    }

    /**
    * Test Handle Reset page get ID user_meta , ID users , token user_meta
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */ 
    public function testHandleResetIndexGetAllIDandToken()
    {
        $resetID  = $this->_populateMeta();

        $meta = $this->user_meta->getOne($resetID);

        $token    = $meta->content;

        $metaID = $this->user_meta->getByContent($token);

        $userID = $meta->user_ID;
        
        $password = str_random(32);

        $action = 'Reset Password';
        // get params
        $params = [
            'password'                  => $password,
            'password_confirmation'     => $password,
            'token'                     => $token,
            'ID'                        => $metaID,
            'user_ID'                   => $userID,
            'action'                    => $action
        ];

        // save record data
        $this->log->record($userID, $action, $params);

        $response = $this->call('POST', $this->appHomeSection.'password/reset?token='.$token, $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/reset?token='.$token]);

        // $response->assertSessionHas('success', '');

    }

    /**
    * Test Handle Reset Log data
    * @access public
    * @group CMS
    * @group CMS-PasswordController
    * @return Void
    */
    public function testHandleResetLogRecordData()
    {
        //  populate data
        $userID = $this->_populate();

        //  get ID user data
        $ID     = $this->_pickRandomItem($userID);
        $user   = $this->user->getOne($ID);

        $metaID = $this->_populateMeta();
        $meta = $this->user_meta->getOne($metaID);

        $token    = $meta->content;
        
        $password = str_random(32);

        // get log data user ID and email
        $logData = [
            'ID'        => $meta->ID,
            'user_ID'   => $meta->user_ID,
            'email'     => $user->email
        ];

        // get params
        $params = [
                'ID'                        => $ID,
                'password'                  => $password,
                'password_confirmation'     => $password,
                'token'                     => $token,
                'logData'                   => $logData
        ];

        $action = 'Reset Password';
        
        // save record data
        $this->log->record($user->ID, $action, $params);

        // get status success
        $success = 'success';

        // make Response
        $response = $this->call('POST', $this->appHomeSection.'password/reset?token='.$token, $params, [], [], ['HTTP_REFERER' => $this->appHomeSection.'password/reset?token='.$token]);
dd($response->getContent());
        // verify response
        // $response->assertRedirect($this->appAdminSection.'/login?status='.$success);
        // $response->assertSessionHas('success', '');
    }
}

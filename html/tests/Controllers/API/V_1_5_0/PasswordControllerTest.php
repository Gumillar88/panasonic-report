<?php
namespace Tests\Controllers\API\V_1_5_0;

use Hash;
use TestCase;
use Crypt;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorMetaModel;
use App\Http\Models\TokenModel;

class PasswordControllerTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Promotor meta model container
     *
     * @access protected
     */
    protected $promotor_meta;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;
    
    /**
     * Promotor data sample
     *
     * @access protected
     */
    protected $promotorData = [
        'dealer_ID'     => 1,
        'phone'         => '+6280010003000',
        'phoneNormal'   => '080010003000',
        'password'      => '1234',
        'name'          => 'Alfian',
        'gender'        => 'male',
        'type'          => 'promotor',
        'parent_ID'     => 0,
    ];

    /**
     * News data sample
     *
     * @access protected
     */
    protected $promotorNewsData = [
        'promotor_ID'       => 1,
        'name'              => 'reset-password-code',
        'content'           => '1234',
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->promotor         = new PromotorModel();
        $this->promotor_meta    = new PromotorMetaModel();
        $this->token            = new TokenModel();
    }
    
    /**
     * Populate promotor meta database with promotorNews data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotorMeta()
    {
        return $this->promotor_meta->set(
            $this->promotorNewsData['promotor_ID'], 
            $this->promotorNewsData['name'], 
            $this->promotorNewsData['content']
        );
    }

    /**
     * Populate promotor database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotor()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            $this->promotorData['type'], 
            $this->promotorData['parent_ID']
        );
    }

    /**
     * Populate TL database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populateTL()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            '+6280010003001',
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'tl', 
            1
        );
    }

    /**
     * Populate arco database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populateArco()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'arco', 
            $this->promotorData['parent_ID']
        );
    }

    /**
     * Populate panasonic database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populatePanasonic()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'panasonic', 
            $this->promotorData['parent_ID']
        );
    }
    
    /**
     * Test forgot passowrd with no phone
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testForgotNoPhone()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-forgot')
            ->_result(['error' => 'phone-not-valid']);
    }
    
    /**
     * Test forgot passowrd with format without +
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testForgotWithInvalidFormatPhoneNo()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Do request
        $this->_request('POST', '/api/1.5.0/password-forgot', ['phone' => '080010003001'])
            ->_result(['error' => 'phone-not-valid']);
    }

    /**
     * Test forgot passowrd with format +
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testForgotWithInvalidPhoneNo()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Do request
        $this->_request('POST', '/api/1.5.0/password-forgot', ['phone' => '+6280010003001'])
            ->_result(['error' => 'phone-not-valid']);
    }

    /**
     * Testforgot passowrd with valid data
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testForgotWithValidPhoneNo()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'phone' => '+6280010003000'
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-forgot', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Testforgot passowrd with valid data and format without +
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testForgotWithValidPhoneNoAndFormatPhone()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'phone' => '080010003000'
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-forgot', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test check code with no secret
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeNoSecret()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code')
            ->_result(['error' => 'no-secret']);
    }

    /**
     * Test check code with no code
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeNoCode()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code',['secret' => 'secret'])
            ->_result(['error' => 'no-code']);
    }

    /**
     * Test check code with invalid secret
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeWithInvalidSecret()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code',['secret' => 'secret','code' => 'code'])
            ->_result(['error' => 'invalid-secret']);
    }

    /**
     * Test check code with no promotor
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeWithValidSecretAndNoPromotor()
    {
        $secret = Crypt::encrypt(json_encode(['phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code',['secret' => $secret,'code' => 'code'])
            ->_result(['error' => 'no-user']);
    }

    /**
     * Test check code with valid secret and no promotor meta
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeWithValidSecretAndNoPromtorMeta()
    {
        $promotorID     = $this->_populatePromotor();

        $secret = Crypt::encrypt(json_encode(['phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code',['secret' => $secret,'code' => 'code'])
            ->_result(['error' => 'no-code']);
    }

    /**
     * Test check code with valid secret and promotor meta
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeWithValidSecretAndPromtorMeta()
    {
        $promotorID         = $this->_populatePromotor();
        $promotorMetaID     = $this->_populatePromotorMeta();

        $secret = Crypt::encrypt(json_encode(['phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-check-code',['secret' => $secret,'code' => 'code'])
            ->_result(['error' => 'code-invalid']);
    }

    /**
     * Test check code with valid secret and promotor meta
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testCheckCodeWithValidData()
    {
        $promotorID         = $this->_populatePromotor();
        $promotorMetaID     = $this->_populatePromotorMeta();

        $secret = Crypt::encrypt(json_encode(['phone' => $this->promotorData['phone'], 'time' => time()]));

        $params = [
            'secret'    => $secret, 
            'code'      => '1234' 
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-check-code', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test reset password with no secret
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithNoSecret()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset')
            ->_result(['error' => 'no-secret']);
    }

    /**
     * Test reset password with no password
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithNoPassword()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => '1234'])
            ->_result(['error' => 'password-empty']);
    }

    /**
     * Test reset password with password and no password conf
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithNoPasswordConf()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => '1234', 'password' => '12345'])
            ->_result(['error' => 'password-empty']);
    }

    /**
     * Test reset password with password under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithPasswordLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => '1234', 'password' => '12345', 'passconf' => '123456'])
            ->_result(['error' => 'password-min-length']);
    }

    /**
     * Test reset password with passconf under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithPassconfLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => '1234', 'password' => '123456', 'passconf' => '12345'])
            ->_result(['error' => 'password-min-length']);
    }

    /**
     * Test reset password cehck password and passconf
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetCheckPasswordAndPassconf()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => '1234', 'password' => '123456', 'passconf' => '1234567'])
            ->_result(['error' => 'password-not-match']);
    }

    /**
     * Test reset password invalid secret
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithInvalidSecret()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => 'secret', 'password' => '123456', 'passconf' => '123456'])
            ->_result(['error' => 'invalid-secret']);
    }

    /**
     * Test reset password valid secret
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithValidSecret()
    {
        $secret = Crypt::encrypt(json_encode(['code' => '1234', 'phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => $secret, 'password' => '123456', 'passconf' => '123456'])
            ->_result(['error' => 'no-user']);
    }

    /**
     * Test reset password with promotor data without promotor meta
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithPromotor()
    {
        $promotorID = $this->_populatePromotor();

        $secret = Crypt::encrypt(json_encode(['code' => '1234', 'phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => $secret, 'password' => '123456', 'passconf' => '123456'])
            ->_result(['error' => 'no-code']);
    }

    /**
     * Test reset password with promotor data with promotor meta and wrong code
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithInvalidCodeAndPromotorAndPromotorMeta()
    {
        $promotorID = $this->_populatePromotor();
        $promotorMetaID     = $this->_populatePromotorMeta();

        $secret = Crypt::encrypt(json_encode(['code' => '123', 'phone' => $this->promotorData['phone'], 'time' => time()]));

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-reset', ['secret' => $secret, 'password' => '123456', 'passconf' => '123456'])
            ->_result(['error' => 'code-invalid']);
    }

    /**
     * Test reset password with valid data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testResetWithValidData()
    {
        $promotorID = $this->_populatePromotor();
        $promotorMetaID     = $this->_populatePromotorMeta();

        $secret = Crypt::encrypt(json_encode(['code' => '1234', 'phone' => $this->promotorData['phone'], 'time' => time()]));

        $params = [
            'secret'  => $secret, 
            'password' => '123456',
            'passconf' => '123456'
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-reset', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('token', $result));
    }

    /**
     * Test change password with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test change password with no password old
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithNoPasswordOld()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234'])
            ->_result(['error' => 'password-old-empty']);
    }

    /**
     * Test change password with no new password 
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithNoPasswordNew()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '12345'])
            ->_result(['error' => 'password-new-empty']);
    }

     /**
     * Test change password with no new password conf 
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithNoPasswordConf()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '12345', 'passwordNew' => '123456'])
            ->_result(['error' => 'password-new-empty']);
    }

    /**
     * Test change password with password under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithPasswordNewLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '12345', 'passwordNew' => '12345', 'passwordConf' => '123456'])
            ->_result(['error' => 'password-new-min-length']);
    }

    /**
     * Test change password with passconf under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithPasswordConfLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '123456', 'passwordNew' => '123456', 'passwordConf' => '12345'])
            ->_result(['error' => 'password-new-min-length']);
    }

    /**
     * Test change password cehck password and passconf
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangePasswordNewAndPasswordOld()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '123456', 'passwordNew' => '123456', 'passwordConf' => '1234567'])
            ->_result(['error' => 'password-new-not-match']);
    }

    /**
     * Test change password without promotor
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithoutPromotor()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => '1234', 'passwordOld' => '123456', 'passwordNew' => '1234567', 'passwordConf' => '1234567'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test change password wrong password promotor
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithInvalidPassword()
    {
        $promotorID = $this->_populatePromotor();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);

        // Do Request
        $this->_request('POST', '/api/1.5.0/password-change', ['token' => $encryptedToken, 'passwordOld' => '123456', 'passwordNew' => '1234567', 'passwordConf' => '1234567'])
            ->_result(['error' => 'password-old-error']);
    }

    /**
     * Test change password with valid data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testChangeWithValidData()
    {
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);

        $params = [
            'token'  => $encryptedToken, 
            'passwordOld'  => '1234', 
            'passwordNew'  => '1234567', 
            'passwordConf'  => '1234567', 
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-change', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));

    }

    /**
     * Test generate password with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test generate password with no password
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithNoPassword()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234'])
            ->_result(['error' => 'password-empty']);
    }

    /**
     * Test generate password with no new password 
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithNoPasswordConf()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234', 'password' => '12345'])
            ->_result(['error' => 'password-empty']);
    }

    /**
     * Test generate password with password under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithPasswordLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234', 'password' => '12345', 'passconf' => '123456'])
            ->_result(['error' => 'password-min-length']);
    }

    /**
     * Test generate password with passconf under 6 char
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithPasswordConfLength()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234', 'password' => '123456', 'passconf' => '12345'])
            ->_result(['error' => 'password-min-length']);
    }

    /**
     * Test generate password check pasword with passconf
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithWrongPasswordAndPasswordConf()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234', 'password' => '123457', 'passconf' => '123456'])
            ->_result(['error' => 'password-not-match']);
    }

    /**
     * Test generate password without promotor
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithoutPromotor()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/password-generate', ['token' => '1234', 'password' => '123456', 'passconf' => '123456'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test generate password with valid data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-PasswordController
     * @return Void
     */
    public function testGenerateWithValidData()
    {
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);

        $params = [
            'token'  => $encryptedToken, 
            'password'  => '123456', 
            'passconf'  => '123456', 
        ];

        // Do request
        $response   = $this->call('POST', '/api/1.5.0/password-generate', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));

    }
}
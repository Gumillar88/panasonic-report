<?php
namespace Tests\Controllers\API\V_1_5_0;

use Hash;
use TestCase;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\DealerModel;
use App\Http\Models\TokenModel;

class GeneralControllerTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * Product Category model container
     *
     * @access protected;
     */
    protected $productCategory;
    
    /**
     * Product model container
     *
     * @access protected
     */
    protected $product;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;
    
    /**
     * Dealer data sample
     *
     * @access protected
     */
    protected $dealerData = [
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
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        'name'   => 'Meja',
    ];

    /**
     * Product data sample
     *
     * @access protected
     */
    protected $productData = [
        'product_category_ID'   => 1,
        'name'                  => 'Meja A',
        'price'                 => 1000,
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->productCategory  = new ProductCategoryModel();
        $this->product          = new ProductModel();
        $this->promotor         = new PromotorModel();
        $this->dealer           = new DealerModel();
        $this->token            = new TokenModel();
    }
    
    /**
     * Tear down test
     *
     * @acces public
     * @return Void
     */
    public function tearDown()
    {
        Carbon::setTestNow();

        parent::tearDown();
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
     * Populate dealer data
     *
     * @access private
     * @return Void
     */
    private function _populateDealer()
    {
        $this->dealer->create($this->dealerData);
    }

    /**
     * Populate product category database with product category data
     *
     * @access private
     * @return Integer
     */
    private function _populateProductCategory()
    {
        return $this->productCategory->create(
            $this->productCategoryData['name']
        );
    }

    /**
     * Populate product database with product data
     *
     * @access private
     * @return Integer
     */
    private function _populateProduct()
    {
        return $this->product->create(
            $this->productData['product_category_ID'], 
            $this->productData['name'], 
            $this->productData['price']
        );
    }
    
    /**
     * Test check date with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/date-check')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test check date with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/date-check', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }
    

    /**
     * Test check date with valid promotor token (12:00 - 10:00 AM)
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateBetweenOneAndTenAM()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        Carbon::setTestNow(Carbon::parse('2016-12-25 09:00:00')); 
        
        // Do request
        $this->_request('GET', '/api/1.5.0/date-check', ['token' => $encryptedToken])
            ->_result(['time' => false]);
    }
    
    /**
     * Test check date with valid promotor token (>10:00 AM)
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateOverTenAM()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        Carbon::setTestNow(Carbon::parse('2016-12-25 12:00:00')); 
        
        // Do request
        $this->_request('GET', '/api/1.5.0/date-check', ['token' => $encryptedToken])
            ->_result(['time' => true]);
    }
    
    /**
     * Test check date with valid promotor token (12:00 - 03:00 AM - Monday)
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateMondayBetweenOneAndThreeAM()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        Carbon::setTestNow(Carbon::parse('2016-12-26 02:00:00')); 
        
        // Do request
        $this->_request('GET', '/api/1.5.0/date-check', ['token' => $encryptedToken])
            ->_result(['time' => false]);
    }
    
    /**
     * Test check date with valid promotor token (>03:00 AM - Monday)
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDateMondayOverThreeAM()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        Carbon::setTestNow(Carbon::parse('2016-12-26 05:00:00')); 
        
        // Do request
        $this->_request('GET', '/api/1.5.0/date-check', ['token' => $encryptedToken])
            ->_result(['time' => true]);
    }

    /**
     * Test check data with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDataNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/data-check')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test check data with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDataWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/data-check', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }
    

    /**
     * Test check data with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testCheckDataWithValidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        $productCategoryID = $this->_populateProductCategory();
        $productID = $this->_populateProduct();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/data-check', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('version', $result));
    }

    /**
     * Test get data with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testGetDataNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/data-list')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test get data with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testGetDataWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/data-list', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test get data with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testGetDataWithValidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        $productCategoryID = $this->_populateProductCategory();
        $productID = $this->_populateProduct();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/data-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test check profile with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testProfileNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/data-profile')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test check profile with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testProfileWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/data-profile', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test check profile with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-GeneralController
     * @return Void
     */
    public function testProfileWithValidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        $this->_populateDealer();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/data-profile', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }
    
}

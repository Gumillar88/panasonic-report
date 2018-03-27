<?php
namespace Tests\Controllers\API\V_1_5_0;

use DB;
use Hash;
use TestCase;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\TokenModel;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorTargetModel;
use App\Http\Models\ReportModel;
use App\Http\Models\ReportSaleModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;
use App\Http\Models\CustomerModel;

class ReportControllerTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * Token model container
     *
     * @access protected
     */
    protected $token;
    
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

     /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;
    
    /**
     * Dealer channel model container
     *
     * @access protected
     */
    protected $dealer_channel;

    /**
     * dealer account model container
     *
     * @access Protected
     */
    protected $dealer_account;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;
    
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
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;

    /**
     * Report No Sale model container
     *
     * @access protected
     */
    protected $report_sale;

    /**
     * Target Promotor model container
     *
     * @access protected
     */
    protected $promotor_target;
    
    /**
     * Customer model container
     *
     * @access protected
     */
    protected $customer;
    
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
    
    protected $regionData = [
        'Jakarta',
        'Indonesia Barat',
        'Indonesia Timur'
    ];

    /**
     * Branch data sample
     *
     * @access protected
     */
    protected $branchData = [
        'name'          => 'Jakarta SO',
        'region_ID'     => 1,
    ];

    /**
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        'name' => 'Meja',
    ];

    /**
     * Product data sample
     *
     * @access protected
     */
    protected $productData = [
        'name'  => 'Meja A',
        'price' => 1000,
    ];

    /**
     * Dealer data sample
     *
     * @access protected
     */
    protected $dealerData = [
        'region_ID'         => 1,
        'branch_ID'         => 1,
        'dealer_account_ID' => 1,
        'dealer_type_ID'    => 1,
        'dealer_channel_ID' => 1,
        'code'              => 'BALI',
        'name'              => 'BALI ELECTRONIC CENTER',
        'company'           => 'none',
        'address'           => 'none'
    ];
    
    /**
     * Dealer type data sample
     *
     * @access protected
     */
    protected $dealerTypeData = [
        'R1',
        'R2',
        'R3',
        'R4'
    ];
    
    /**
     * Dealer channel data sample
     *
     * @access protected
     */
    protected $dealerChannelData = [
        'SO',
        'SMO',
        'MUP'
    ];

    /**
     * Dealer Account data sample
     *
     * @access protected
     */
    protected $dealerAccountData = [
        'name'          => 'Electronic City',
        'branch_ID'     => 1,
        'promotor_ID'   => 1,
    ];

    /**
     * Price product data sample
     *
     * @access protected
     */
    protected $productPrice = [
        'price' => 10000,
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->token            = new TokenModel();
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer           = new DealerModel();
        $this->dealer_type      = new DealerTypeModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->dealer_account   = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_target  = new PromotorTargetModel();
        $this->report           = new ReportModel();
        $this->report_sale      = new ReportSaleModel();
        $this->productCategory  = new ProductCategoryModel();
        $this->product          = new ProductModel();
        $this->product_price    = new ProductPriceModel();
        $this->customer         = new CustomerModel();
    }
    
    /**
     * Populate general data
     *
     * @access private
     * @return Void
     */
    private function _populateGeneralData()
    {
        foreach ($this->dealerTypeData as $type)
        {
            $this->dealer_type->create($type);
        }
        
        foreach ($this->dealerChannelData as $channel)
        {
            $this->dealer_channel->create($channel);
        }
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
    private function _populateProduct($productCategoryID)
    {
        return $this->product->create( 
            $productCategoryID,
            $this->productData['name'], 
            $this->productData['price']
        );
    }

    /**
     * Populate promotor database with promotor data
     *
     * @access private
     * @param Integer $parent_ID
     * @return Integer
     */
    private function _populatePromotor($parent_ID)
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            $this->promotorData['type'], 
            $parent_ID
        );
    }

     /**
     * Populate TL database with promotor data
     *
     * @access private
     * @param Integer $parent_ID
     * @return Integer
     */
    private function _populateTL($parent_ID)
    {
        return $this->promotor->create(
            0, 
            '+6280010003001',
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'tl', 
            $parent_ID
        );
    }

    /**
     * Populate arco database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populateArco($parent_ID)
    {
        return $this->promotor->create(
            0, 
            '+6280010003002',
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'arco', 
            $parent_ID
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
            0, 
            '+6280010003003',
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'panasonic', 
            $this->promotorData['parent_ID']
        );
    }

    /**
     * Populate branch database with branch data
     *
     * @access private
     * @return Integer
     */
    private function _populateBranch($promotor_ID)
    {
        return $this->branch->create(
            $this->branchData['name'], 
            $this->branchData['region_ID'], 
            $promotor_ID
        );
    }

    /**
     * Populate dealer database with dealer data
     *
     * @access private
     * @return Integer
     */
    private function _populateDealer()
    {
        return $this->dealer->create($this->dealerData);
    }

    /**
     * Populate dealer account database with dealer Account data
     *
     * @access private
     * @param Integer $promotorID
     * @return Integer
     */
    private function _populateDealerAccount($promotorID)
    {
        return $this->dealer_account->create(
            $this->dealerAccountData['name'], 
            $this->dealerAccountData['branch_ID'],
            $promotorID
        ); 
    }

    /**
     * Populate price product database with price data
     *
     * @access private
     * @return Integer
     */
    private function _populateProductPrice($productID)
    {
        return $this->product_price->create(
            1,
            1,
            $productID,
            $this->productPrice['price']
        );
    }

    /**
     * Populate report sale database with price data
     *
     * @access private
     * @return Integer
     */
    private function _populateReportNoSales()
    {
        $time = date('Y-m-d', time());

        $data = [
            'promotor_ID'   => 1,
            'dealer_ID'     => 1,
            'account_ID'    => 0,
            'tl_ID'         => 0,
            'arco_ID'       => 0,
            'date'          => $time
        ];

        return $this->report_sale->create($data);
    }

    /**
     * Populate promotor target database 
     *
     * @access private
     * @param Array
     * @return Integer
     */
    private function _populatePromotorTarget($data)
    {
        return $this->promotor_target->create(
            $data['promotorID'],
            $data['dealerID'],
            $data['dealerAccountID'], 
            $data['TLID'], 
            $data['arcoID'], 
            $data['total'], 
            $data['month']
        );
    }

    /**
     * Populate promotor target database 
     *
     * @access private
     * @param Array $data
     * @return Integer
     */
    private function _populateReport($data)
    {
        return $this->report->create([
            'dealer_ID'         => $data['dealerID'], 
            'promotor_ID'       => $data['promotorID'], 
            'account_ID'        => $data['dealerAccountID'], 
            'tl_ID'             => $data['TLID'], 
            'arco_ID'           => $data['arcoID'], 
            'customer_ID'       => 1,
            'product_model_ID'  => $data['productID'], 
            'custom_name'       => $data['customName'], 
            'price'             => $data['price'], 
            'quantity'          => $data['quantity'], 
            'date'              => $data['date']
        ]);
    }

    /**
     * Test report no sales with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCheckNoSalesNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-nosales')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test report no sales with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCheckNoSalesWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-nosales', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report no sales with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCheckNoSalesWithValidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-nosales', ['token' => $encryptedToken])
            ->_result(['result' => 0]);
    }

    /**
     * Test report no sales with valid data and already report
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCheckNoSalesWithValidDataAndAlreadyReport()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        $reportNoSalesID = $this->_populateReportNoSales();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-nosales', ['token' => $encryptedToken])
            ->_result(['result' => 1]);
    }

    /**
     * Test create report no sales with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoSalesNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/report-nosales')
            ->_result(['error' => 'no-token']);
    }



    /**
     * Test create report no sales with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoSalesWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-nosales', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test create report no sales with valid data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoSalesWithValidData()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-nosales', ['token' => $encryptedToken, 'date' => 1])
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('report_nosale')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,       $arcoID);
        $this->assertEquals($result->tl_ID,         $tlID);
        $this->assertEquals($result->account_ID,    $dealerAccountID);
        $this->assertEquals($result->promotor_ID,   $promotorID);
        $this->assertEquals($result->date,          date('Y-m-d'));
    }

    /**
     * Test create report no sales with valid data yesterday
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoSalesWithValidDataYesterday()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-nosales', ['token' => $encryptedToken, 'date' => 0])
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('report_nosale')->where('ID', 1)->first();
                
        $this->assertEquals($result->arco_ID,       $arcoID);
        $this->assertEquals($result->tl_ID,         $tlID);
        $this->assertEquals($result->account_ID,    $dealerAccountID);
        $this->assertEquals($result->promotor_ID,   $promotorID);
        $this->assertEquals($result->date,          date('Y-m-d', (time()-24*3600)));
    }

    /**
     * Test report create with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/report-create')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test report create with no product ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoProductID()
    {
        // Set parameters
        $params = [
            'token' => '123'
        ];
        
        // Do Request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['error' => 'no-product-id']);
    }

    /**
     * Test report create with no custom_name
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoCustomName()
    {
        // Set parameters
        $params = [
            'token'     => '123',
            'productID' => 0
        ];
        
        // Do Request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['error' => 'no-product-id']);
    }
    
    /**
     * Test report create with no quantity
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateNoQuantity()
    {
        // Set parameters
        $params = [
            'token'     => '123',
            'productID' => 1
        ];
        
        // Do Request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['error' => 'no-quantity']);
    }

    /**
     * Test report create with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token'     => '1234', 
            'productID' => 1, 
            'quantity'  => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report create with no product
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithNoProduct()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token'     => $encryptedToken, 
            'productID' => 1, 
            'quantity'  => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['error' => 'no-product']);
    }

    /**
     * Test report create with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithProductID()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'     => $encryptedToken, 
            'productID' => 1, 
            'quantity'  => $quantity, 
            'date'      => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productData['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d'));
    }

    /**
     * Test report create with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithProductIDYesterday()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        //populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'     => $encryptedToken, 
            'productID' => 1, 
            'quantity'  => $quantity,
            'date'      => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productData['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d', time()-24*3600));
    }
    
    /**
     * Test report create with missing customer data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithMissingCustomer()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'             => $encryptedToken, 
            'productID'         => 1, 
            'quantity'          => $quantity, 
            'date'              => 1,
            'customerPhone'     => '1234',
            'customerName'      => '',
            'customerGender'    => '',
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->customer_ID,       0);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productData['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d'));
    }
    
    /**
     * Test report create with new customer parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithNewCustomer()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'             => $encryptedToken, 
            'productID'         => 1, 
            'quantity'          => $quantity, 
            'date'              => 1,
            'customerPhone'     => '1234',
            'customerName'      => 'Alfian Sibuea',
            'customerGender'    => 'male',
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->customer_ID,       1);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productData['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d'));
        
        $customerData = $this->customer->getByPhone($params['customerPhone']);
        
        $this->assertEquals($customerData->name,    $params['customerName']);
        $this->assertEquals($customerData->gender,  $params['customerGender']);
    }
    
    /**
     * Test report create with existing customer data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithExistingCustomer()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'             => $encryptedToken, 
            'productID'         => 1, 
            'quantity'          => $quantity, 
            'date'              => 1,
            'customerPhone'     => '1234',
            'customerName'      => 'Alfian Sibuea',
            'customerGender'    => 'male',
        ];
        
        $this->customer->create('Indra', '1111', 'male');
        $customerID = $this->customer->create($params['customerName'], $params['customerPhone'], $params['customerGender']);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->customer_ID,       $customerID);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productData['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d'));
        
        $customerData = $this->customer->getByPhone($params['customerPhone']);
        
        $this->assertEquals($customerData->name,    $params['customerName']);
        $this->assertEquals($customerData->gender,  $params['customerGender']);
    }

    /**
     * Test report create with valid product price
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithProductPrice()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate category, product, price
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        $productPriceID     = $this->_populateProductPrice($productID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'     => $encryptedToken, 
            'productID' => 1, 
            'quantity'  => $quantity,
            'date'      => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productPrice['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d'));
    }

    /**
     * Test report create with valid product price
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithProductPriceYesterday()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        $productPriceID     = $this->_populateProductPrice($productID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'     => $encryptedToken, 
            'productID' => 1, 
            'quantity'  => $quantity,
            'date'      => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->where('ID', 1)->first();
        
        $this->assertEquals($result->arco_ID,           $arcoID);
        $this->assertEquals($result->tl_ID,             $tlID);
        $this->assertEquals($result->account_ID,        1);
        $this->assertEquals($result->promotor_ID,       $promotorID);
        $this->assertEquals($result->product_model_ID,  $productID);
        $this->assertEquals($result->custom_name,       '');
        $this->assertEquals($result->price,             $this->productPrice['price']);
        $this->assertEquals($result->quantity,          $quantity);
        $this->assertEquals($result->date,              date('Y-m-d', time()-24*3600));
    }

    /**
     * Test report create with combination product
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithCombinationProduct()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        //populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        $combineProductID   = $this->_populateProduct($productCategoryID);
        $productPriceID     = $this->_populateProductPrice($productID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'                 => $encryptedToken, 
            'productID'             => 1, 
            'productCombinationID'  => 2 ,
            'quantity'              => $quantity, 
            'date'                  => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->whereIn('ID', [1, 2])->get();
        
        // Check fist product
        $this->assertEquals($result[0]->arco_ID,           $arcoID);
        $this->assertEquals($result[0]->tl_ID,             $tlID);
        $this->assertEquals($result[0]->account_ID,        1);
        $this->assertEquals($result[0]->promotor_ID,       $promotorID);
        $this->assertEquals($result[0]->product_model_ID,  $productID);
        $this->assertEquals($result[0]->custom_name,       '');
        $this->assertEquals($result[0]->price,             $this->productPrice['price']);
        $this->assertEquals($result[0]->quantity,          $quantity);
        $this->assertEquals($result[0]->date,              date('Y-m-d'));
        
        // Check second product
        $this->assertEquals($result[1]->arco_ID,           $arcoID);
        $this->assertEquals($result[1]->tl_ID,             $tlID);
        $this->assertEquals($result[1]->account_ID,        1);
        $this->assertEquals($result[1]->promotor_ID,       $promotorID);
        $this->assertEquals($result[1]->product_model_ID,  $combineProductID);
        $this->assertEquals($result[1]->custom_name,       '');
        $this->assertEquals($result[1]->price,             $this->productData['price']);
        $this->assertEquals($result[1]->quantity,          $quantity);
        $this->assertEquals($result[1]->date,              date('Y-m-d'));
    }

    /**
     * Test report create with combination product
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithCombinationProductYesterday()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        //populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        $combineProductID   = $this->_populateProduct($productCategoryID);
        $productPriceID     = $this->_populateProductPrice($productID);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'                 => $encryptedToken, 
            'productID'             => 1, 
            'productCombinationID'  => 2 ,
            'quantity'              => $quantity, 
            'date'                  => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->whereIn('ID', [1, 2])->get();
        
        // Check fist product
        $this->assertEquals($result[0]->arco_ID,           $arcoID);
        $this->assertEquals($result[0]->tl_ID,             $tlID);
        $this->assertEquals($result[0]->account_ID,        1);
        $this->assertEquals($result[0]->promotor_ID,       $promotorID);
        $this->assertEquals($result[0]->product_model_ID,  $productID);
        $this->assertEquals($result[0]->custom_name,       '');
        $this->assertEquals($result[0]->price,             $this->productPrice['price']);
        $this->assertEquals($result[0]->quantity,          $quantity);
        $this->assertEquals($result[0]->date,              date('Y-m-d', time()-24*3600));
        
        // Check second product
        $this->assertEquals($result[1]->arco_ID,           $arcoID);
        $this->assertEquals($result[1]->tl_ID,             $tlID);
        $this->assertEquals($result[1]->account_ID,        1);
        $this->assertEquals($result[1]->promotor_ID,       $promotorID);
        $this->assertEquals($result[1]->product_model_ID,  $combineProductID);
        $this->assertEquals($result[1]->custom_name,       '');
        $this->assertEquals($result[1]->price,             $this->productData['price']);
        $this->assertEquals($result[1]->quantity,          $quantity);
        $this->assertEquals($result[1]->date,              date('Y-m-d', time()-24*3600));
    }

    /**
     * Test report create with combination product
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithCombinationProductWithPrice()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        //populate product
        $productCategoryID      = $this->_populateProductCategory();
        $productID              = $this->_populateProduct($productCategoryID);
        $combineProductID       = $this->_populateProduct($productCategoryID);
        $productPriceID         = $this->_populateProductPrice($productID);
        $combinePoructPriceID   = $this->_populateProductPrice($combineProductID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'                 => $encryptedToken, 
            'productID'             => 1, 
            'productCombinationID'  => 2 ,
            'quantity'              => $quantity, 
            'date'                  => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->whereIn('ID', [1, 2])->get();
        
        // Check fist product
        $this->assertEquals($result[0]->arco_ID,           $arcoID);
        $this->assertEquals($result[0]->tl_ID,             $tlID);
        $this->assertEquals($result[0]->account_ID,        1);
        $this->assertEquals($result[0]->promotor_ID,       $promotorID);
        $this->assertEquals($result[0]->product_model_ID,  $productID);
        $this->assertEquals($result[0]->custom_name,       '');
        $this->assertEquals($result[0]->price,             $this->productPrice['price']);
        $this->assertEquals($result[0]->quantity,          $quantity);
        $this->assertEquals($result[0]->date,              date('Y-m-d'));
        
        // Check second product
        $this->assertEquals($result[1]->arco_ID,           $arcoID);
        $this->assertEquals($result[1]->tl_ID,             $tlID);
        $this->assertEquals($result[1]->account_ID,        1);
        $this->assertEquals($result[1]->promotor_ID,       $promotorID);
        $this->assertEquals($result[1]->product_model_ID,  $combineProductID);
        $this->assertEquals($result[1]->custom_name,       '');
        $this->assertEquals($result[1]->price,             $this->productPrice['price']);
        $this->assertEquals($result[1]->quantity,          $quantity);
        $this->assertEquals($result[1]->date,              date('Y-m-d'));
    }

    /**
     * Test report create with combination product
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportCreateWithCombinationProductWithPriceYesterday()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID      = $this->_populateProductCategory();
        $productID              = $this->_populateProduct($productCategoryID);
        $combineProductID       = $this->_populateProduct($productCategoryID);
        $productPriceID         = $this->_populateProductPrice($productID);
        $combinePoructPriceID   = $this->_populateProductPrice($combineProductID);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set quantity
        $quantity = rand(1, 100);
        
        // Set parameters
        $params = [
            'token'                 => $encryptedToken, 
            'productID'             => 1, 
            'productCombinationID'  => 2 ,
            'quantity'              => $quantity, 
            'date'                  => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/report-create', $params)
            ->_result(['result' => 'success']);
        
        // Verify database
        $result = DB::table('reports')->whereIn('ID', [1, 2])->get();
        
        // Check fist product
        $this->assertEquals($result[0]->arco_ID,           $arcoID);
        $this->assertEquals($result[0]->tl_ID,             $tlID);
        $this->assertEquals($result[0]->account_ID,        1);
        $this->assertEquals($result[0]->promotor_ID,       $promotorID);
        $this->assertEquals($result[0]->product_model_ID,  $productID);
        $this->assertEquals($result[0]->custom_name,       '');
        $this->assertEquals($result[0]->price,             $this->productPrice['price']);
        $this->assertEquals($result[0]->quantity,          $quantity);
        $this->assertEquals($result[0]->date,              date('Y-m-d', time()-24*3600));
        
        // Check second product
        $this->assertEquals($result[1]->arco_ID,           $arcoID);
        $this->assertEquals($result[1]->tl_ID,             $tlID);
        $this->assertEquals($result[1]->account_ID,        1);
        $this->assertEquals($result[1]->promotor_ID,       $promotorID);
        $this->assertEquals($result[1]->product_model_ID,  $combineProductID);
        $this->assertEquals($result[1]->custom_name,       '');
        $this->assertEquals($result[1]->price,             $this->productPrice['price']);
        $this->assertEquals($result[1]->quantity,          $quantity);
        $this->assertEquals($result[1]->date,              date('Y-m-d', time()-24*3600));
    }

    /**
     * Test report list with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportListNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-list')
            ->_result(['error' => 'no-token']);
    }

     /**
     * Test report list with no date
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportListNoDate()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-list', ['token' => '1234'])
            ->_result(['error' => 'no-date']);
    }

    /**
     * Test report list with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportListWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-list', ['token' => '1234', 'date' => '2016-11-23'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report list with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testReportListWithValidData()
    {
        // Populate data
        $this->_populateGeneralData();
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Set date
        $date = date('Y-m-d');
        
        // Create report
        $this->_populateReport([
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => 1, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => 90000, 
            'quantity'          => 1, 
            'date'              => $date
        ]);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);

        $params = [
            'token' => $encryptedToken,
            'date'  => $date
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-list', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertEquals($result['result']['reports'][0]['name'],  $this->productData['name']);
        
    }

    /**
     * Test report get sales with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test report get sales with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report get sales with valid promotor token with today date value
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithValidPromotorToken()
    {   
        $knownDate = Carbon::parse('2017-06-20');
        Carbon::setTestNow($knownDate); 
        
        // Set time
        $dateObject = Carbon::now();
        
        // Set time
        $time           = $dateObject->format('Y-m');
        $thisMonth      = $dateObject->format('Y-m-d');
        
        // Substract date by 1 month
        $dateObject = Carbon::parse('2017-05-20');
        
        $timeLastMonth  = $dateObject->format('Y-m');
        $lastMonth      = $dateObject->format('Y-m-d');

        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);

        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Populate target
        $currentTargetValue = 100000;
        $lastTargetValue    = 500000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $currentTargetValue, 
            'month'             => $time
        ];
        
        $lastTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $lastTargetValue, 
            'month'             => $timeLastMonth
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        $this->_populatePromotorTarget($lastTarget);
        
        
        // Populate Report
        $priceThisMonth     = 90000;
        $priceLastMonth     = 100000;
        $quantityThisMonth  = 1;
        $quantityLastMonth  = 1;
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceThisMonth, 
            'quantity'          => $quantityThisMonth, 
            'date'              => $thisMonth
        ];
        
        $reportLastMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceLastMonth, 
            'quantity'          => $quantityLastMonth, 
            'date'              => $lastMonth
        ];
        
        $this->_populateReport($reportThisMonth);
        $this->_populateReport($reportLastMonth);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales', ['token' => $encryptedToken]);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertEquals($result['result']['targetPromotorProduct'][$productID]['product_ID'], $productID);
        $this->assertEquals($result['result']['targetPromotor'],        $currentTargetValue);
        $this->assertEquals($result['result']['targetPromotorData'],    $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['targetDealer'],          $currentTargetValue);
        $this->assertEquals($result['result']['targetDealerData'],      $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['comparison'],            $lastTargetValue);
        $this->assertEquals($result['result']['comparisonData'],        $priceLastMonth*$quantityLastMonth);
    }
    
    /**
     * Test report get sales with valid promotor token with odd month value
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithValidPromotorTokenOddMonth()
    {   
        $knownDate = Carbon::parse('2017-07-31');
        Carbon::setTestNow($knownDate); 
        
        // Set time
        $dateObject = Carbon::now();
        
        // Set time
        $time           = $dateObject->format('Y-m');
        $thisMonth      = $dateObject->format('Y-m-d');
        
        // Substract date by 1 month
        $dateObject = Carbon::parse('2017-06-20');
        
        $timeLastMonth  = $dateObject->format('Y-m');
        $lastMonth      = $dateObject->format('Y-m-d');

        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);

        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Populate target
        $currentTargetValue = 100000;
        $lastTargetValue    = 500000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $currentTargetValue, 
            'month'             => $time
        ];
        
        $lastTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $lastTargetValue, 
            'month'             => $timeLastMonth
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        $this->_populatePromotorTarget($lastTarget);
        
        
        // Populate Report
        $priceThisMonth     = 90000;
        $priceLastMonth     = 100000;
        $quantityThisMonth  = 1;
        $quantityLastMonth  = 1;
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceThisMonth, 
            'quantity'          => $quantityThisMonth, 
            'date'              => $thisMonth
        ];
        
        $reportLastMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceLastMonth, 
            'quantity'          => $quantityLastMonth, 
            'date'              => $lastMonth
        ];
        
        $this->_populateReport($reportThisMonth);
        $this->_populateReport($reportLastMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales', ['token' => $encryptedToken]);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertEquals($result['result']['targetPromotorProduct'][$productID]['product_ID'], $productID);
        $this->assertEquals($result['result']['targetPromotor'],        $currentTargetValue);
        $this->assertEquals($result['result']['targetPromotorData'],    $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['targetDealer'],          $currentTargetValue);
        $this->assertEquals($result['result']['targetDealerData'],      $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['comparison'],            $lastTargetValue);
        $this->assertEquals($result['result']['comparisonData'],        $priceLastMonth*$quantityLastMonth);
    }
    
    /**
     * Test report get sales with valid promotor token with march month value
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithValidPromotorTokenMarchMonth()
    {   
        $knownDate = Carbon::parse('2017-03-31');
        Carbon::setTestNow($knownDate); 
        
        // Set time
        $dateObject = Carbon::now();
        
        // Set time
        $time           = $dateObject->format('Y-m');
        $thisMonth      = $dateObject->format('Y-m-d');
        
        // Substract date by 1 month
        $dateObject = Carbon::parse('2017-02-20');
        
        $timeLastMonth  = $dateObject->format('Y-m');
        $lastMonth      = $dateObject->format('Y-m-d');

        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);

        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Populate target
        $currentTargetValue = 100000;
        $lastTargetValue    = 500000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $currentTargetValue, 
            'month'             => $time
        ];
        
        $lastTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $lastTargetValue, 
            'month'             => $timeLastMonth
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        $this->_populatePromotorTarget($lastTarget);
        
        
        // Populate Report
        $priceThisMonth     = 90000;
        $priceLastMonth     = 100000;
        $quantityThisMonth  = 1;
        $quantityLastMonth  = 1;
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceThisMonth, 
            'quantity'          => $quantityThisMonth, 
            'date'              => $thisMonth
        ];
        
        $reportLastMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceLastMonth, 
            'quantity'          => $quantityLastMonth, 
            'date'              => $lastMonth
        ];
        
        $this->_populateReport($reportThisMonth);
        $this->_populateReport($reportLastMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales', ['token' => $encryptedToken]);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertEquals($result['result']['targetPromotorProduct'][$productID]['product_ID'], $productID);
        $this->assertEquals($result['result']['targetPromotor'],        $currentTargetValue);
        $this->assertEquals($result['result']['targetPromotorData'],    $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['targetDealer'],          $currentTargetValue);
        $this->assertEquals($result['result']['targetDealerData'],      $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['comparison'],            $lastTargetValue);
        $this->assertEquals($result['result']['comparisonData'],        $priceLastMonth*$quantityLastMonth);
    }
    
    /**
     * Test report get sales with valid promotor token with march month value leap year
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithValidPromotorTokenMarchMonthLeapYear()
    {   
        $knownDate = Carbon::parse('2016-03-31');
        Carbon::setTestNow($knownDate); 
        
        // Set time
        $dateObject = Carbon::now();
        
        // Set time
        $time           = $dateObject->format('Y-m');
        $thisMonth      = $dateObject->format('Y-m-d');
        
        // Substract date by 1 month
        $dateObject = Carbon::parse('2016-02-20');
        
        $timeLastMonth  = $dateObject->format('Y-m');
        $lastMonth      = $dateObject->format('Y-m-d');

        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);

        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Populate target
        $currentTargetValue = 100000;
        $lastTargetValue    = 500000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $currentTargetValue, 
            'month'             => $time
        ];
        
        $lastTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $lastTargetValue, 
            'month'             => $timeLastMonth
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        $this->_populatePromotorTarget($lastTarget);
        
        
        // Populate Report
        $priceThisMonth     = 90000;
        $priceLastMonth     = 100000;
        $quantityThisMonth  = 1;
        $quantityLastMonth  = 1;
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceThisMonth, 
            'quantity'          => $quantityThisMonth, 
            'date'              => $thisMonth
        ];
        
        $reportLastMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceLastMonth, 
            'quantity'          => $quantityLastMonth, 
            'date'              => $lastMonth
        ];
        
        $this->_populateReport($reportThisMonth);
        $this->_populateReport($reportLastMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales', ['token' => $encryptedToken]);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertEquals($result['result']['targetPromotorProduct'][$productID]['product_ID'], $productID);
        $this->assertEquals($result['result']['targetPromotor'],        $currentTargetValue);
        $this->assertEquals($result['result']['targetPromotorData'],    $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['targetDealer'],          $currentTargetValue);
        $this->assertEquals($result['result']['targetDealerData'],      $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['comparison'],            $lastTargetValue);
        $this->assertEquals($result['result']['comparisonData'],        $priceLastMonth*$quantityLastMonth);
    }
    
    /**
     * Test report get sales with valid promotor token with custom model value
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportController
     * @return Void
     */
    public function testGetSalesWithValidPromotorTokenCustomModel()
    {   
        $knownDate = Carbon::parse('2017-06-20');
        Carbon::setTestNow($knownDate); 
        
        // Set time
        $dateObject = Carbon::now();
        
        // Set time
        $time           = $dateObject->format('Y-m');
        $thisMonth      = $dateObject->format('Y-m-d');
        
        // Substract date by 1 month
        $dateObject = Carbon::parse('2017-05-20');
        
        $timeLastMonth  = $dateObject->format('Y-m');
        $lastMonth      = $dateObject->format('Y-m-d');

        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);

        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);
        
        // Populate target
        $currentTargetValue = 100000;
        $lastTargetValue    = 500000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $currentTargetValue, 
            'month'             => $time
        ];
        
        $lastTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $lastTargetValue, 
            'month'             => $timeLastMonth
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        $this->_populatePromotorTarget($lastTarget);
        
        
        // Populate Report
        $priceThisMonth     = 90000;
        $priceLastMonth     = 100000;
        $quantityThisMonth  = 1;
        $quantityLastMonth  = 1;
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => 0, 
            'customName'        => 'TEST MODEL', 
            'price'             => $priceThisMonth, 
            'quantity'          => $quantityThisMonth, 
            'date'              => $thisMonth
        ];
        
        $reportLastMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $priceLastMonth, 
            'quantity'          => $quantityLastMonth, 
            'date'              => $lastMonth
        ];
        
        $this->_populateReport($reportThisMonth);
        $this->_populateReport($reportLastMonth);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales', ['token' => $encryptedToken]);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        $this->assertTrue(array_key_exists($reportThisMonth['customName'], $result['result']['targetPromotorProduct']));
        $this->assertEquals($result['result']['targetPromotor'],        $currentTargetValue);
        $this->assertEquals($result['result']['targetPromotorData'],    $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['targetDealer'],          $currentTargetValue);
        $this->assertEquals($result['result']['targetDealerData'],      $priceThisMonth*$quantityThisMonth);
        $this->assertEquals($result['result']['comparison'],            $lastTargetValue);
        $this->assertEquals($result['result']['comparisonData'],        $priceLastMonth*$quantityLastMonth);
    }

}

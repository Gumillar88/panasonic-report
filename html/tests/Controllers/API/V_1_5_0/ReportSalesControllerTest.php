<?php
namespace Tests\Controllers\API\V_1_5_0;

use DB;
use Hash;
use TestCase;

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

class ReportSalesControllerTest extends TestCase
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
     * Test report get sales region with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetRegionNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-region')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test report get sales region with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetRegionWithInvalidToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-region', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }
    
    /**
     * Test report get sales region with promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetRegionWithPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-region', ['token' => $encryptedToken])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report get sales region with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetRegion()
    {
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
        
        // Populate region
        $regionIDs = [];
        
        foreach ($this->regionData as $region)
        {
            $regionIDs[] = $this->region->create($region, $arcoID);
        }
        
        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($panasonicID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-region', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $regionIDs[0].'-'.$arcoID);
        $this->assertEquals($result['result'][1]['name'],         $this->regionData[0]);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales region with valid promotor token with no sales data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetRegionWithValidPromotorTokenNoSalesData()
    {
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
        
        // Populate region
        $regionIDs = [];
        
        foreach ($this->regionData as $region)
        {
            $regionIDs[] = $this->region->create($region, $arcoID);
        }
        
        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($panasonicID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-region', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $regionIDs[0].'-'.$arcoID);
        $this->assertEquals($result['result'][1]['name'],         $this->regionData[0]);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }

    /**
     * Test report get sales branch with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-branch')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test report get sales branch with no arco ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchNoArcoID()
    {
        // Set parameter
        $params = [
            'token'     => '1234'
        ];
        
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-branch', $params)
            ->_result(['error' => 'no-ID']);
    }

    /**
     * Test report get sales branch with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'     => '1234',
            'arcoID'    => '1'
        ];
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-branch', $params)
            ->_result(['error' => 'no-auth']);
    }

     /**
     * Test report get sales branch with arco and region ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchWithArcoAndRegionID()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token' => $encryptedToken ,
            'arcoID' => '1-'.$arcoID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-branch', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $branchID.'-'.$tlID);
        $this->assertEquals($result['result'][1]['name'],         $this->branchData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales branch with arco ID only
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchWithArcoID()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token' => $encryptedToken,
            'arcoID' => $arcoID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-branch', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $branchID.'-'.$tlID);
        $this->assertEquals($result['result'][1]['name'],         $this->branchData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales branch with no sales
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetBranchNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'arcoID'    => '1-'.$arcoID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-branch', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $branchID.'-'.$tlID);
        $this->assertEquals($result['result'][1]['name'],         $this->branchData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }

    /**
     * Test report get sales account with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-account')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test report get sales account with no team leader ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountNoTLID()
    {
        // Set parameters
        $params = [
            'token' => '1234'
        ];
        
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-account', $params)
            ->_result(['error' => 'no-ID']);
    }

    /**
     * Test report get sales account with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token' => '1234',
            'tlID'  => '1'
        ];
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-account', $params)
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report get sales account data using branch and TL ID
     * and resulted account data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingBranchAndTLIDWithAccountResult()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $branchID.'-'.$tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], '');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           '-'.$dealerAccountID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerAccountData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    
    /**
     * Test report get sales account data using branch and TL ID
     * and resulted account data with no sales
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingBranchAndTLIDWithAccountResultNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $branchID.'-'.$tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], '');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           '-'.$dealerAccountID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerAccountData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }
    
    /**
     * Test report get sales account data using branch and TL ID
     * and resulted skip result because branch has no account ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingBranchAndTLIDWithSkipResult()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $branchID.'-'.$tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], '');
        $this->assertEquals(count($result['result']), 0);
        $this->assertEquals($result['skip'], true);
    }
    
    /**
     * Test report get sales account data using TL ID only
     * and resulted single branch data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingTLIDOnlyWithBranchResult()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], 'branch');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           'branch-'.$branchID);
        $this->assertEquals($result['result'][1]['name'],         $this->branchData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales account data using TL ID only
     * and resulted single branch data no sales
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingTLIDOnlyWithBranchResultNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], 'branch');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           'branch-'.$branchID);
        $this->assertEquals($result['result'][1]['name'],         $this->branchData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }
    
    /**
     * Test report get sales account data using TL ID only
     * and resulted account data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingTLIDOnlyWithAccountResult()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($arcoID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], 'account');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           'account-'.$branchID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerAccountData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales account data using TL ID only
     * and resulted account data no sales
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetAccountUsingTLIDOnlyWithAccountResultNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($arcoID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'tlID'  => $tlID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-account', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertEquals($result['type'], 'account');
        
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           'account-'.$branchID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerAccountData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }

    /**
     * Test report get sales dealer with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-dealer')
            ->_result(['error' => 'no-token']);
    }
    
    
    /**
     * Test report get sales dealer with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerNoAccount()
    {
        // Set parameters
        $params = [
            'token' => '1234'
        ];
        
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-dealer', $params)
            ->_result(['error' => 'no-account']);
    }

    /**
     * Test report get sales dealer with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token'     => '1234',
            'accountID' => '1'
        ];
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-dealer', $params)
            ->_result(['error' => 'no-auth']);
    }

     /**
     * Test report get sales dealer using account parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerUsingAccount()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($arcoID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'accountID' => $tlID.'-'.$dealerAccountID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-dealer', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $dealerID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
     /**
     * Test report get sales dealer using account parameter no sales data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerUsingAccountNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($arcoID);
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount($tlID);
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => $dealerAccountID, 
            'TLID'              => $arcoID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'accountID' => $tlID.'-'.$dealerAccountID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-dealer', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $dealerID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }
    
     /**
     * Test report get sales dealer using branch parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerUsingBranch()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'accountID' => 'branch-'.$branchID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-dealer', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $dealerID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format($price*$quantity));
        $this->assertEquals($result['result'][1]['persentase'],   ($price*$quantity*100)/$targetValue);
    }
    
    /**
     * Test report get sales dealer using branch parameter no sales
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetDealerUsingBranchNoSales()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'accountID' => 'branch-'.$branchID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-dealer', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][1]['ID'],           $dealerID);
        $this->assertEquals($result['result'][1]['name'],         $this->dealerData['name']);
        $this->assertEquals($result['result'][1]['target'],       $targetValue);
        $this->assertEquals($result['result'][1]['sales'],        number_format(0));
        $this->assertEquals($result['result'][1]['persentase'],   0);
    }

    /**
     * Test report get sales promotor with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test report get sales promotor with no dealer ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorNoDealerID()
    {
        // Set parameters
        $params = [
            'token' => '1234'
        ];
        
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor', $params)
            ->_result(['error' => 'no-dealerID']);
    }

    /**
     * Test report get sales promotor with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token'     => '1234',
            'dealerID'  => 1
        ];
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor', $params)
            ->_result(['error' => 'no-auth']);
    }

     /**
     * Test report get sales promotor
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotor()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'dealerID'  => $dealerID 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-promotor', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $data = $result['result'][$promotorID];
        
        $this->assertEquals($data['ID'],           $promotorID);
        $this->assertEquals($data['name'],         $this->promotorData['name']);
        $this->assertEquals($data['target'],       $targetValue);
        $this->assertEquals($data['sales'],        number_format($price*$quantity));
        $this->assertEquals($data['persentase'],   ($price*$quantity*100)/$targetValue);
    }

    /**
     * Test report get sales promotor detail with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorDetailNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor-detail')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test report get sales promotor detail with no promotor ID
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorDetailNoPromotor()
    {
        // Set parameters
        $params = [
            'token' => '1234'
        ];
        
        // Do Request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor-detail', $params)
            ->_result(['error' => 'no-promotorID']);
    }

    /**
     * Test report get sales promotor detail with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorDetailWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor(1);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token'         => '1234',
            'promotorID'    => '1'
        ];
        
        // Do request
        $this->_request('GET', '/api/1.5.0/report-sales-promotor-detail', $params)
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test report get sales promotor detail with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-ReportSalesController
     * @return Void
     */
    public function testGetPromotorDetail()
    {
        // Populate data
        $panasonicID        = $this->_populatePanasonic();
        $arcoID             = $this->_populateArco($panasonicID);
        $tlID               = $this->_populateTL($arcoID);
        $promotorID         = $this->_populatePromotor($tlID);
        
        $regionID           = $this->region->create($this->regionData[0], $arcoID);
        $branchID           = $this->_populateBranch($tlID);
        $dealerID           = $this->_populateDealer();
        
        // Populate product data
        $productCategoryID  = $this->_populateProductCategory();
        $productID          = $this->_populateProduct($productCategoryID);

        // Populate target
        $targetValue = 1000000;
        
        $currentTarget = [
            'promotorID'        => $promotorID, 
            'dealerID'          => $dealerID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'total'             => $targetValue, 
            'month'             => date('Y-m')
        ];
        
        $this->_populatePromotorTarget($currentTarget);
        
        // Populate Report
        $price     = 90000;
        $quantity  = rand(1, 10);
        
        $reportThisMonth = [
            'dealerID'          => $dealerID, 
            'promotorID'        => $promotorID, 
            'dealerAccountID'   => 0, 
            'TLID'              => $tlID, 
            'arcoID'            => $arcoID, 
            'productID'         => $productID, 
            'customName'        => '', 
            'price'             => $price, 
            'quantity'          => $quantity, 
            'date'              => date('Y-m-d')
        ];
        
        $this->_populateReport($reportThisMonth);

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token'     => $encryptedToken,
            'promotorID' => $promotorID
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/report-sales-promotor-detail', $params);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
        $this->assertEquals($result['result'][0]['ID'],             $productID);
        $this->assertEquals($result['result'][0]['dealer_ID'],      $dealerID);
        $this->assertEquals($result['result'][0]['promotor_ID'],    $promotorID);
        $this->assertEquals($result['result'][0]['name'],           $this->productData['name']);
        $this->assertEquals($result['result'][0]['price'],          $price);
        $this->assertEquals($result['result'][0]['quantity'],       $quantity);
    }

}

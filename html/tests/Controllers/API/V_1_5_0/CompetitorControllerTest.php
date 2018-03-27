<?php
namespace Tests\Controllers\API\V_1_5_0;

use DB;
use Hash;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\TokenModel;

class CompetitorControllerTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $price;
    
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $brand;
    
    /**
     * Product Category model container
     *
     * @access protected;
     */
    protected $category;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

    /**
     * Product model container
     *
     * @access protected
     */
    protected $product_model;
    
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
     * Brand data sample
     *
     * @access protected
     */
    protected $brandData = [
        'Samsung', 'LG', 'Sharp'
    ];

    /**
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        'AC', 'TV', 'Refrigerator'
    ];
    
    /**
     * Price sample data
     *
     * @access protected
     */
    protected $priceData = [
        [
            'promotor_ID'               => 1,
            'dealer_ID'                 => 1,
            'product_model_ID'          => 1,
            'competitor_brand_ID'       => 1,
            'competitor_brand_custom'   => '',
            'product_category_ID'       => 1,
            'model_name'                => 'ABCD',
            'price_normal'              => 1000,
            'price_promo'               => 500
        ],
        [
            'promotor_ID'               => 1,
            'dealer_ID'                 => 1,
            'product_model_ID'          => 1,
            'competitor_brand_ID'       => -1,
            'competitor_brand_custom'   => 'Miyako',
            'product_category_ID'       => 1,
            'model_name'                => 'DCBA',
            'price_normal'              => 10000,
            'price_promo'               => 5000
        ]
    ];

    /**
     * Product model data sample
     *
     * @access protected
     */
    protected $productModelData = [
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-2250',
            'price'                 => 0,
        ],
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-4200LJ-H',
            'price'                 => 0,
        ]
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->brand    = new CompetitorBrandModel();
        $this->price    = new CompetitorPriceModel();
        $this->category = new ProductCategoryModel();
        $this->product_model  = new ProductModel();
        $this->promotor = new PromotorModel();
        $this->token    = new TokenModel();
    }
    
    
    /**
     * Populate database with data
     *
     * @access private
     * @return Array
     */
    private function _populateData()
    {
        // Populate brands
        foreach ($this->brandData as $brand)
        {
             $this->brand->create($brand);
        }
        
        // Populate product category
        foreach ($this->productCategoryData as $productCategory)
        {
            $this->category->create($productCategory);
        }

        // Populate product model
        foreach ($this->productModelData as $productModel)
        {
             $productModelIDs[] = $this->product_model->create(
                 $productModel['product_category_ID'],
                 $productModel['name'],
                 $productModel['price']
             );
        }
    }
    
    /**
     * Populate promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotor()
    {
        // Populate promotor
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
     * Test get list of competitor price without token parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceListNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/competitor-price-list')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test get list of competitor price with invalid token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceListWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/competitor-price-list', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }
    

    /**
     * Test get list of competitor price data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceListNormal()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        $finalPriceData         = $this->priceData[0];
        $finalPriceData['date'] = date('Y-m-d');
        $ID = $this->price->create($finalPriceData);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/competitor-price-list', ['token' => $encryptedToken])
            ->_result(['result' => [
                [
                    'ID'        => $ID,
                    'brand'     => $this->brandData[0],
                    'model'     => $finalPriceData['model_name'],
                    'category'  => $this->productCategoryData[0]
                ]
            ]]);
    }
    
    /**
     * Test get list of competitor price data with custom name
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceListCustomName()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        $finalPriceData         = $this->priceData[1];
        $finalPriceData['date'] = date('Y-m-d');
        $ID = $this->price->create($finalPriceData);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/competitor-price-list', ['token' => $encryptedToken])
            ->_result(['result' => [
                [
                    'ID'        => $ID,
                    'brand'     => $finalPriceData['competitor_brand_custom'],
                    'model'     => $finalPriceData['model_name'],
                    'category'  => $this->productCategoryData[0]
                ]
            ]]);
    }
    
    /**
     * Test set competitor data without token parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/competitor-price-set')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test set competitor data with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }
    
    /**
     * Test set competitor with validation error custom brand missing
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetValidationErrorCustomBrand()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'brandCustom'       => '',
            'modelName'         => $this->priceData[0]['model_name'],
            'priceNormal'       => $this->priceData[0]['price_normal'],
            'pricePromo'        => $this->priceData[0]['price_promo'],
            'date'              => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['error' => 'data-error']);
    }
    
    /**
     * Test set competitor prices with create process using today date
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetCreateToday()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'productModelID'    => 1,
            'brandID'           => 1,
            'brandCustom'       => '',
            'modelName'         => $this->priceData[0]['model_name'],
            'priceNormal'       => $this->priceData[0]['price_normal'],
            'pricePromo'        => $this->priceData[0]['price_promo'],
            'date'              => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('competitor_prices')->where('ID', 1)->first();
        
        $this->assertEquals($result->product_category_ID, 1);
        $this->assertEquals($result->competitor_brand_ID, 1);
        $this->assertEquals($result->competitor_brand_custom, '');
        $this->assertEquals($result->model_name, $this->priceData[0]['model_name']);
        $this->assertEquals($result->price_normal, $this->priceData[0]['price_normal']);
        $this->assertEquals($result->price_promo, $this->priceData[0]['price_promo']);
        $this->assertEquals($result->date, date('Y-m-d'));
    }
    
    /**
     * Test set competitor prices with create custom brand process using today date
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetCreateCustomToday()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'productModelID'    => 1,
            'brandCustom'       => $this->priceData[1]['competitor_brand_custom'],
            'modelName'         => $this->priceData[1]['model_name'],
            'priceNormal'       => $this->priceData[1]['price_normal'],
            'pricePromo'        => $this->priceData[1]['price_promo'],
            'date'              => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('competitor_prices')->where('ID', 1)->first();
        
        $this->assertEquals($result->product_category_ID, 1);
        $this->assertEquals($result->competitor_brand_ID, 0);
        $this->assertEquals($result->competitor_brand_custom, $this->priceData[1]['competitor_brand_custom']);
        $this->assertEquals($result->model_name, $this->priceData[1]['model_name']);
        $this->assertEquals($result->price_normal, $this->priceData[1]['price_normal']);
        $this->assertEquals($result->price_promo, $this->priceData[1]['price_promo']);
        $this->assertEquals($result->date, date('Y-m-d'));
    }
    
    /**
     * Test set competitor prices with create process using yesterday date
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetCreateYesterday()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'productModelID'    => 1,
            'brandID'           => 1,
            'brandCustom'       => '',
            'modelName'         => $this->priceData[0]['model_name'],
            'priceNormal'       => $this->priceData[0]['price_normal'],
            'pricePromo'        => $this->priceData[0]['price_promo'],
            'date'              => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('competitor_prices')->where('ID', 1)->first();
        
        $this->assertEquals($result->product_category_ID, 1);
        $this->assertEquals($result->competitor_brand_ID, 1);
        $this->assertEquals($result->competitor_brand_custom, '');
        $this->assertEquals($result->model_name, $this->priceData[0]['model_name']);
        $this->assertEquals($result->price_normal, $this->priceData[0]['price_normal']);
        $this->assertEquals($result->price_promo, $this->priceData[0]['price_promo']);
        $this->assertEquals($result->date, date('Y-m-d', strtotime('-1 day')));
    }
    
    /**
     * Test set competitor prices with create custom brand process using yesterday date
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetCreateCustomYesterday()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'productModelID'    => 1,
            'brandCustom'       => $this->priceData[1]['competitor_brand_custom'],
            'modelName'         => $this->priceData[1]['model_name'],
            'priceNormal'       => $this->priceData[1]['price_normal'],
            'pricePromo'        => $this->priceData[1]['price_promo'],
            'date'              => 0
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('competitor_prices')->where('ID', 1)->first();
        
        $this->assertEquals($result->product_category_ID, 1);
        $this->assertEquals($result->competitor_brand_ID, 0);
        $this->assertEquals($result->competitor_brand_custom, $this->priceData[1]['competitor_brand_custom']);
        $this->assertEquals($result->model_name, $this->priceData[1]['model_name']);
        $this->assertEquals($result->price_normal, $this->priceData[1]['price_normal']);
        $this->assertEquals($result->price_promo, $this->priceData[1]['price_promo']);
        $this->assertEquals($result->date, date('Y-m-d', strtotime('-1 day')));
    }
    
    /**
     * Test set competitor prices with update process
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceSetUpdate()
    {
        // Populate data
        $this->_populateData();
        $promotorID = $this->_populatePromotor();
        
        $finalPriceData         = $this->priceData[0];
        $finalPriceData['date'] = date('Y-m-d');
        $ID = $this->price->create($finalPriceData);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameter
        $params = [
            'ID'                => $ID,
            'token'             => $encryptedToken,
            'productCategoryID' => 1,
            'productModelID'    => 1,
            'brandCustom'       => $this->priceData[1]['competitor_brand_custom'],
            'modelName'         => $this->priceData[1]['model_name'],
            'priceNormal'       => $this->priceData[1]['price_normal'],
            'pricePromo'        => $this->priceData[1]['price_promo']
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-set', $params)
            ->_result(['result' => 'success']);
        
        // Verify data
        $result = DB::table('competitor_prices')->where('ID', 1)->first();
        
        $this->assertEquals($result->product_category_ID, 1);
        $this->assertEquals($result->competitor_brand_ID, 0);
        $this->assertEquals($result->competitor_brand_custom, $this->priceData[1]['competitor_brand_custom']);
        $this->assertEquals($result->model_name, $this->priceData[1]['model_name']);
        $this->assertEquals($result->price_normal, $this->priceData[1]['price_normal']);
        $this->assertEquals($result->price_promo, $this->priceData[1]['price_promo']);
        $this->assertEquals($result->date, date('Y-m-d'));
    }
    
    
    /**
     * Test get competitor data without token parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceGetNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/competitor-price-get')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test get competitor data without ID parameter
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceGetNoID()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/competitor-price-get', ['token' => '1234'])
            ->_result(['error' => 'no-id']);
    }
    
    /**
     * Test get competitor data with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceGetInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token' => '1234',
            'ID'    => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-get', $params)
            ->_result(['error' => 'no-auth']);
    }
    
    /**
     * Test get competitor data without price data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceGetNoData()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'ID'    => 1
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-get', $params)
            ->_result(['error' => 'no-data']);
    }
    
    /**
     * Test get competitor data success
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-CompetitorController
     * @return Void
     */
    public function testPriceGetDataSuccess()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        $finalPriceData         = $this->priceData[0];
        $finalPriceData['date'] = date('Y-m-d');
        $ID = $this->price->create($finalPriceData);
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Set parameters
        $params = [
            'token' => $encryptedToken,
            'ID'    => $ID
        ];
        
        // Do request
        $this->_request('POST', '/api/1.5.0/competitor-price-get', $params)
            ->_result(['result' =>  [
            'productModelID'    => $this->priceData[0]['product_model_ID'],
            'brandID'           => $this->priceData[0]['competitor_brand_ID'],
            'brandCustom'       => $this->priceData[0]['competitor_brand_custom'],
            'productCatagoryID' => $this->priceData[0]['product_category_ID'],
            'modelName'         => $this->priceData[0]['model_name'],
            'priceNormal'       => $this->priceData[0]['price_normal'],
            'pricePromo'        => $this->priceData[0]['price_promo'],
        ]]);
    }
}

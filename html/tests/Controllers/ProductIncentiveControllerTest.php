<?php
namespace Tests\Controllers;

use DB;
use TestCase;

use App\Http\Models\DealerChannelModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductIncentiveModel;


class ProductIncentiveControllerTest extends TestCase
{
    /**
     * Dealer channel model container
     *
     * @access Protected
     */
    protected $dealerChannel;
    
    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product;

    /**
     * Product incentive model container
     *
     * @access Protected
     */
    protected $incentive;
    
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
     * Product Incentive data sample
     *
     * @access protected
     */
    protected $incentiveData = [
        [
            'dealer_channel_ID'   => 1,
            'product_model_ID'    => 1,
            'value'               => 1000,
        ],
        [
            'dealer_channel_ID'   => 1,
            'product_model_ID'    => 2,
            'value'               => 3000,
        ],
        [
            'dealer_channel_ID'   => 2,
            'product_model_ID'    => 2,
            'value'               => 3000,
        ]
    ];

    /**
     * Dealer channel data sample
     *
     * @access protected
     */
    protected $dealerChannelData = [
        ['name' => 'SO'],
        ['name' => 'MUP'],
        ['name' => 'SMO']
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
        ],
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-4250LJ-K',
            'price'                 => 0,
        ]
    ];
    
    /**
     * Custom Product Incentive data sample
     *
     * @access protected
     */
    protected $customIncentiveData = [
        'dealer_channel_ID'     => 2,
        'product_model_ID'      => 1,
        'value'                 => 9000,
    ];
    

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealerChannel    = new DealerChannelModel();
        $this->product          = new ProductModel();
        $this->incentive        = new ProductIncentiveModel();
    }
    
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate dealer channel
        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $this->dealerChannel->create(
                 $dealerChannel['name']
             );
        }

        // Populate product model
        foreach ($this->productModelData as $productModel)
        {
            $this->product->create(
                 $productModel['product_category_ID'],
                 $productModel['name'],
                 $productModel['price']
             );
        }

        // Populate Product Price
        $incentiveIDs = [];
        
        foreach ($this->incentiveData as $incentive)
        {
             $incentiveIDs[] = $this->incentive->create(
                 $incentive['dealer_channel_ID'],
                 $incentive['product_model_ID'],
                 $incentive['value']
             );
        }
        
        return $incentiveIDs;
    }
    
    /**
     * Test render index page of product incentive
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive');
        
        $this->assertViewHas('dealerChannels');
        $this->assertViewHas('productModels');
        $this->assertViewHas('productIncentives');
        $this->assertPageContain('Product Incentives');
    }
    
    /**
     * Test render create product incentive page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/create');
        
        $this->assertViewHas('dealerChannels');
        $this->assertViewHas('productModels');
        $this->assertPageContain('Create Product Incentive');
    }
    
    /**
     * Test handle create product incentive request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleCreateValidatorError()
    {
        // Set parameter and remove value from parameter
        $params = $this->customIncentiveData;
        unset($params['value']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/create', $params, [], [], ['HTTP_REFERER' => '/product/incentive/create']);
        
        $this->assertRedirectedTo('/product/incentive/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create product incentive request but the same data already exist on database
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleCreateDataExist()
    {
        // Populate data
        $productIncentiveIDs = $this->_populate();
        
        // Set data
        $ID                 = $this->_pickRandomItem($productIncentiveIDs);
        $productIncentive   = $this->incentive->getOne($ID);
        
        $params = [
            'dealer_channel_ID' => $productIncentive->dealer_channel_ID,
            'product_model_ID'  => $productIncentive->product_model_ID,
            'value'             => $productIncentive->value
        ];
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/create', $params, [], [], ['HTTP_REFERER' => '/product/incentive/create']);
        
        // Verify response
        $this->assertRedirectedTo('/product/incentive/edit?ID='.$ID);
        $this->assertSessionHas('product-incentive-exist', '');
    }
    
    /**
     * Test handle create product incentive request successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Populate database
        $this->_populate();
        
        // Make request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/create', $this->customIncentiveData, [], [], ['HTTP_REFERER' => '/product/incentive/create']);
        
        // Verify response
        $this->assertRedirectedTo('/product/incentive');
        $this->assertSessionHas('product-incentive-created', '');
        
        // Verify data
        $result = DB::table('product_incentives')
                    ->where('dealer_channel_ID', $this->customIncentiveData['dealer_channel_ID'])
                    ->where('product_model_ID', $this->customIncentiveData['product_model_ID'])
                    ->where('value', $this->customIncentiveData['value'])
                    ->first();
        
        $this->assertTrue($result !== null);
    }
    
    /**
     * Test render update product incentive page with error because no ID in parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update product incentive page with error because data is invalid
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderUpdateNoData()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/edit', ['ID' => 1]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update product incentive request successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $productIncentiveIDs = $this->_populate();
        
        // Set data
        $ID                 = $this->_pickRandomItem($productIncentiveIDs);
        $productIncentive   = $this->incentive->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('incentive', $productIncentive);
        $this->assertViewHas('productModels');
        $this->assertViewHas('dealerChannels');

        $this->assertPageContain('Edit Product Incentive');
    }
    
    /**
     * Test handle update product incentive request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleUpdateValidatorError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/edit', $this->customIncentiveData, [], [], ['HTTP_REFERER' => '/product/incentive/edit']);
        
        // Verify
        $this->assertRedirectedTo('/product/incentive/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update product incentive request successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $productIncentiveIDs  = $this->_populate();
        
        // Set params
        $params = $this->customIncentiveData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($productIncentiveIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/edit', $params, [], [], ['HTTP_REFERER' => '/product/incentive/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/product/incentive/edit');
        $this->assertSessionHas('product-incentive-updated', '');
        
        // Validate data
        $productIncentive  = $this->incentive->getOne($ID);
        $this->assertEquals($productIncentive->value, $params['value']);
        $this->assertEquals($productIncentive->dealer_channel_ID, $params['dealer_channel_ID']);
        $this->assertEquals($productIncentive->product_model_ID, $params['product_model_ID']);
    }
    
    /**
     * Test render remove incentive page without ID as parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove incentive page with error because data is invalid
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderRemoveNoData()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/remove', ['ID' => 1]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove incentive page successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $productIncentiveIDs = $this->_populate();
        
        // Set data
        $ID                 = $this->_pickRandomItem($productIncentiveIDs);
        $productIncentive   = $this->incentive->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/incentive/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('incentive', $productIncentive);
        $this->assertViewHas('productModels');
        $this->assertViewHas('dealerChannels');

        $this->assertPageContain('Remove Product Incentive');
    }
    
    /**
     * Test handle remove incentive request without ID as parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove incentive request with error because data is invalid
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleRemoveNoData()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/remove', ['ID' => 1]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove incentive request successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductIncentiveController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $productIncentiveIDs  = $this->_populate();
        
        // Set params
        $params = $this->customIncentiveData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($productIncentiveIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/product/incentive/remove', $params, [], [], ['HTTP_REFERER' => '/product/incentive/remove']);
        
        // Validate response
        $this->assertRedirectedTo('/product/incentive');
        $this->assertSessionHas('product-incentive-removed', '');
        
        // Validate data
        $productIncentive  = $this->incentive->getOne($ID);
        $this->assertEquals(null, $productIncentive);
    }
}
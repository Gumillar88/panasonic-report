<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\DealerTypeModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;
use App\Http\Models\DealerChannelModel;


class ProductPriceControllerTest extends TestCase
{
    /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;

    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product_model;

    /**
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;
    
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
     * Product Price data sample
     *
     * @access protected
     */
    protected $productPriceData = [
        [
            'dealer_type_ID'      => 1,
            'dealer_channel_ID'   => 1,
            'product_ID'          => 1,
            'price'               => 1000,
        ],
        [
            'dealer_type_ID'      => 1,
            'dealer_channel_ID'   => 1,
            'product_ID'          => 2,
            'price'               => 3000,
        ],
        [
            'dealer_type_ID'      => 1,
            'dealer_channel_ID'   => 1,
            'product_ID'          => 1,
            'price'               => 3000,
        ]
    ];

    /**
     * Dealer channel data sample
     *
     * @access protected
     */
    protected $dealerChannelData = [
        [
            'name'          => 'SO'
        ],
        [
            'name'          => 'MUP'
        ],
        [
            'name'          => 'SMO'
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
        ],
        [
            'product_category_ID'   => 1,
            'name'                  => 'R-4250LJ-K',
            'price'                 => 0,
        ]
    ];
    
    /**
     * Dealer type data sample
     *
     * @access protected
     */
    protected $dealerTypeData = [
        [
            'name'          => 'R1',
        ],
        [
            'name'          => 'R2',
        ],
        [
            'name'          => 'R3',
        ]
    ];

    /**
     * Custom Product Price data sample
     *
     * @access protected
     */
    protected $customProductPriceData = [
        'dealer_type_ID'        => 1,
        'dealer_channel_ID'     => 1,
        'product_ID'            => 1,
        'price'                 => 9000,
    ];
    

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer_type      = new DealerTypeModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->product_model    = new ProductModel();
        $this->product_price    = new ProductPriceModel();
    }
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate dealer tyoe
        foreach ($this->dealerTypeData as $dealerType)
        {
            $this->dealer_type->create(
                 $dealerType['name']
             );
        }

        // Populate dealer channel
        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $this->dealer_channel->create(
                 $dealerChannel['name']
             );
        }

        // Populate product model
        foreach ($this->productModelData as $productModel)
        {
            $this->product_model->create(
                 $productModel['product_category_ID'],
                 $productModel['name'],
                 $productModel['price']
             );
        }

        // Populate Product Price
        $productPriceIDs = [];
        
        foreach ($this->productPriceData as $productPrice)
        {
             $productPriceIDs[] = $this->product_price->create(
                 $productPrice['dealer_type_ID'],
                 $productPrice['dealer_channel_ID'],
                 $productPrice['product_ID'],
                 $productPrice['price']
             );
        }
        
        return $productPriceIDs;
    }
    
    /**
     * Test render Product Price index page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price');
        
        $this->assertViewHas('product_prices');
        $this->assertViewHas('products');
        $this->assertViewHas('dealer_types');
        $this->assertViewHas('dealer_channels');
    }
    
    /**
     * Test render create Product Price page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/create');
        
        // Verify response
        $this->assertPageContain('Create Dealer Price');
    }
    
    /**
     * Test handle create Product Price request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove Product Price name
        $params = $this->customProductPriceData;
        unset($params['price']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/price/create', $params, [], [], ['HTTP_REFERER' => '/product/price/create']);
        
        $this->assertRedirectedTo('/product/price/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create Product Price request with success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Populate database
        $this->_populate();
        
        // Make request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/price/create', $this->customProductPriceData, [], [], ['HTTP_REFERER' => '/product/price/create']);
        
        // Verify response
        $this->assertRedirectedTo('/product/price');
        $this->assertSessionHas('product-price-created', '');
    }
    
    /**
     * Test render update Product Price page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Price page without valid Product Price data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Price page success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $productPriceIDs = $this->_populate();
        
        // Set data
        $ID              = $this->_pickRandomItem($productPriceIDs);
        $productPrice   = $this->product_price->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('product_price', $productPrice);
        $this->assertViewHas('products');
        $this->assertViewHas('dealer_types');
        $this->assertViewHas('dealer_channels');

        $this->assertPageContain('Edit Product Price');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/price/edit', $this->customProductPriceData, [], [], ['HTTP_REFERER' => '/product/price/edit']);
        
        // Verify
        $this->assertRedirectedTo('/product/price/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $productPriceIDs  = $this->_populate();
        
        // Set params
        $params = $this->customProductPriceData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($productPriceIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/product/price/edit', $params, [], [], ['HTTP_REFERER' => '/product/price/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/product/price/edit');
        $this->assertSessionHas('product-price-updated', '');
        
        // Validate data
        $productPrice  = $this->product_price->getOne($ID);
        $this->assertEquals($productPrice->price, $this->customProductPriceData['price']);
        $this->assertEquals($productPrice->dealer_type_ID, $this->customProductPriceData['dealer_type_ID']);
        $this->assertEquals($productPrice->dealer_channel_ID, $this->customProductPriceData['dealer_channel_ID']);
        $this->assertEquals($productPrice->product_ID, $this->customProductPriceData['product_ID']);
    }
    
    /**
     * Test render remove Product Price page without Product Price ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Price page without valid Product Price data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Price page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $productPriceIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($productPriceIDs);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/product/price/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Product Price');
    }
    
    /**
     * Test handle remove Product Price request without Product Price ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/price/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Price without valid Product Price data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/price/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Price successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $productPriceIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($productPriceIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/price/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/product/price');
        $this->assertSessionHas('product-price-removed', '');
        
        // Validate data
        $productPrice = $this->product_price->getOne($ID);
        $this->assertEquals(null, $productPrice);
        
    }
    
}

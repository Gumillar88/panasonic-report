<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;

class ProductModelControllerTest extends TestCase
{
/**
     * category model container
     *
     * @access Protected
     */
    protected $product_category;
    
    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product_model;
    
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
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        [
            'name'          => 'Ac'
        ],
        [
            'name'          => 'Tv'
        ],
        [
            'name'          => 'Kulkas'
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
     * Custom Product Model data sample
     *
     * @access protected
     */
    protected $customProductModelData = [
        'product_category_ID'   => 1,
        'name'                  => 'RC-700E9-SD',
        'price'                 => 0,
    ];
    

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->product_category = new ProductCategoryModel();
        $this->product_model    = new ProductModel();
    }
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate product category
        foreach ($this->productCategoryData as $productCategory)
        {
            $this->product_category->create(
                 $productCategory['name']
             );
        }

        // Populate Product Model
        $productModelIDs = [];
        
        foreach ($this->productModelData as $productModel)
        {
             $productModelIDs[] = $this->product_model->create(
                 $productModel['product_category_ID'],
                 $productModel['name'],
                 $productModel['price']
             );
        }
        
        return $productModelIDs;
    }

    /**
     * Test render Product Model index page without category
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderIndexNoCategory()
    {
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model');

        $this->assertResponseStatus(404);
    }

    /**
     * Test render Product Model index page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model', ['category_ID' => 1]);

        // Verify response
        foreach ($this->productModelData as $productModel)
        {
            $this->assertPageContain($productModel['name']);
        }
        
        $this->assertViewHas('categories');
        $this->assertViewHas('products');
    }
    
    /**
     * Test render create Product Model page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/create');
        
        // Verify response
        $this->assertPageContain('Create Product Model');

        $this->assertViewHas('product_categories');

    }
    
    /**
     * Test handle create Product Model request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove Product Model name
        $params = $this->customProductModelData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/model/create', $params, [], [], ['HTTP_REFERER' => '/product/model/create']);
        
        $this->assertRedirectedTo('/product/model/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create Product Model request with success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/model/create', $this->customProductModelData, [], [], ['HTTP_REFERER' => '/product/model/create']);
        
        $this->assertRedirectedTo('/product/category');
        $this->assertSessionHas('product-created', '');
    }
    
    /**
     * Test render update Product Model page without ID parameter
     *
     * @access public
     * @group ProductModelController
     * @group CMS-ProductPriceController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Model page without valid Product Model data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Model page success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $productModelIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($productModelIDs);
        $productModel   = $this->product_model->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('product', $productModel);
        $this->assertViewHas('product_category');
        $this->assertViewHas('product_categories');
        $this->assertViewHas('product');

        $this->assertPageContain('Edit Product Model');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/model/edit', $this->customProductModelData, [], [], ['HTTP_REFERER' => '/product/model/edit']);
        
        // Verify
        $this->assertRedirectedTo('/product/model/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $productModelIDs  = $this->_populate();
        
        // Set params
        $params = $this->customProductModelData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($productModelIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/product/model/edit', $params, [], [], ['HTTP_REFERER' => '/product/model/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/product/model/edit');
        $this->assertSessionHas('product-price-updated', '');
        
        // Validate data
        $productModel  = $this->product_model->getOne($ID);
        $this->assertEquals($productModel->product_category_ID, $this->customProductModelData['product_category_ID']);
        $this->assertEquals($productModel->name, $this->customProductModelData['name']);
        $this->assertEquals($productModel->price, $this->customProductModelData['price']);
    }
    
    /**
     * Test render remove Product Model page without Product Model ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Model page without valid Product Model data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Model page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $productModelIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($productModelIDs);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/product/model/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Product Model');
    }
    
    /**
     * Test handle remove Product Model request without Product Model ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/model/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Model without valid Product Model data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/model/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Model successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductModelController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $productModelIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($productModelIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/model/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/product/category');
        $this->assertSessionHas('product-removed', '');
        
        // Validate data
        $productModel = $this->product_model->getOne($ID);
        $this->assertEquals(null, $productModel);
        
    }
    
}

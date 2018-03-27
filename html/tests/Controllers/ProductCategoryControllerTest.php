<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\ProductCategoryModel;

class ProductCategoryControllerTest extends TestCase
{
    /**
     * category model container
     *
     * @access Protected
     */
    protected $category;
    
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
     * Custom Product Category data sample
     *
     * @access protected
     */
    protected $customProductCategoryData = [
        'name'          => 'Kulkas',
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->category = new ProductCategoryModel();
    }
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate Product Category
        $productCategoryIDs = [];
        
        foreach ($this->productCategoryData as $productCategory)
        {
             $productCategoryIDs[] = $this->category->create(
                 $productCategory['name']
             );
        }
        
        return $productCategoryIDs;
    }
    
    /**
     * Test render Product Category index page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category');
        
        // Verify response
        foreach ($this->productCategoryData as $productCategory)
        {
            $this->assertPageContain($productCategory['name']);
        }
        
        $this->assertViewHas('categories');
    }
    
    /**
     * Test render create Product Category page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/product/category/create');
        
        
        // Verify response
        $this->assertPageContain('Create Product Category');
    }
    
    /**
     * Test handle create Product Category request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove Product Category name
        $params = $this->customProductCategoryData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/category/create', $params, [], [], ['HTTP_REFERER' => '/product/category/create']);
        
        $this->assertRedirectedTo('/product/category/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create Product Category request with success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/category/create', $this->customProductCategoryData, [], [], ['HTTP_REFERER' => '/product/category/create']);
        
        $this->assertRedirectedTo('/product/category');
        $this->assertSessionHas('category-created', '');
    }
    
    /**
     * Test render update Product Category page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Category page without valid Product Category data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update Product Category page success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $productCategoryIDs = $this->_populate();
        
        // Set data
        $ID              = $this->_pickRandomItem($productCategoryIDs);
        $productCategory = $this->category->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('category', $productCategory);
        $this->assertPageContain('Rename Product Category');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/product/category/edit', $this->customProductCategoryData, [], [], ['HTTP_REFERER' => '/product/category/edit']);
        
        // Verify
        $this->assertRedirectedTo('/product/category/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $productCategoryIDs  = $this->_populate();
        
        // Set params
        $params = $this->customProductCategoryData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($productCategoryIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/product/category/edit', $params, [], [], ['HTTP_REFERER' => '/product/category/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/product/category/edit');
        $this->assertSessionHas('category-updated', '');
        
        // Validate data
        $productCategory  = $this->category->getOne($ID);
        $this->assertEquals($productCategory->name, $this->customProductCategoryData['name']);
    }
    
    /**
     * Test render remove Product Category page without Product Category ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Category page without valid Product Category data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove Product Category page
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $productCategoryIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($productCategoryIDs);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/product/category/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Product Category');
    }
    
    /**
     * Test handle remove Product Category request without Product Category ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/category/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Category without valid Product Category data
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/product/category/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove Product Category successfully
     *
     * @access public
     * @group CMS
     * @group CMS-ProductCategoryController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $productCategoryIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($productCategoryIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/product/category/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/product/category');
        $this->assertSessionHas('category-deleted', '');
        
        // Validate data
        $productCategory = $this->category->getOne($ID);
        $this->assertEquals(null, $productCategory);
        
    }
    
}

<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;

class CompetitorBrandControllerTest extends TestCase
{
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $brand;
    
    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $price;
    
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
     * Brand data sample
     *
     * @access protected
     */
    protected $brandData = [
        'Samsung', 'LG', 'Sharp'
    ];
    
    /**
     * Custom brand data sample
     *
     * @access protected
     */
    protected $customBrandData = 'Sony';
    
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
    }
    
    /**
     * Populate database with data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate brand
        $brandIDs = [];
        
        foreach ($this->brandData as $brand)
        {
             $brandIDs[] = $this->brand->create($brand);
        }
        
        return $brandIDs;
    }
    
    /**
     * Test render competitor brand index page
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand');
        
        // Verify response
        foreach ($this->brandData as $brand)
        {
            $this->assertPageContain($brand);
        }
        
        $this->assertViewHas('brands');
    }
    
    /**
     * Test render create competitor brand page
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/create');
        
        // Verify response
        $this->assertPageContain('Create Competitor Brand');
    }
    
    /**
     * Test handle create competitor brand request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Make request
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/create', [], [], [], ['HTTP_REFERER' => '/competitor-brand/create']);
        
        // Verify response
        $this->assertRedirectedTo('/competitor-brand/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create competitor brand request with success
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        // Set parameter
        $params = [
            'name' => $this->customBrandData
        ];
        
        // Make request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/create', $params, [], [], ['HTTP_REFERER' => '/competitor-brand/create']);
        
        $this->assertRedirectedTo('/competitor-brand');
        $this->assertSessionHas('brand-created', '');
    }
    
    /**
     * Test render update competitor brand page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update competitor brand page without valid brand data
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update competitor brand page success
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $brandIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($brandIDs);
        $brand  = $this->brand->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('brand', $brand);
        $this->assertPageContain('Edit Competitor Brand');
    }

    
    /**
     * Test handle update competitor brand request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Set parameters
        $params = [
            'name' => $this->customBrandData
        ];
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/edit', $params, [], [], ['HTTP_REFERER' => '/competitor-brand/edit']);
        
        // Verify
        $this->assertRedirectedTo('/competitor-brand/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update competitor brand request success
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $brandIDs = $this->_populate();
        
        // Set params
        $params = [
            'ID'    => $this->_pickRandomItem($brandIDs),
            'name'  => $this->customBrandData
        ];
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/edit', $params, [], [], ['HTTP_REFERER' => '/competitor-brand/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/competitor-brand/edit');
        $this->assertSessionHas('brand-updated', '');
        
        // Validate data
        $brand = $this->brand->getOne($params['ID']);
        $this->assertEquals($brand->name, $this->customBrandData);
    }
    
    /**
     * Test render remove competitor brand page without parameter
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove competitor brand page without valid brand data
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove competitor brand page
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $brandIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($brandIDs);
        $brand  = $this->brand->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-brand/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('brand', $brand);
        $this->assertPageContain('Remove Competitor Brand');
    }
    
    /**
     * Test handle remove competitor brand request without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove competitor brand without valid brand data
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove brand successfully
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorBrandController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $brandIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($brandIDs);
        $oldBrand = $this->brand->getOne($ID);
        
        // Populate price data
        $priceID = $this->price->create([
            'promotor_ID'               => 1,
            'dealer_ID'                 => 1,
            'competitor_brand_ID'       => $ID,
            'competitor_brand_custom'   => '',
            'product_category_ID'       => 1,
            'product_model_ID'          => 1,
            'model_name'                => 'ABCD',
            'price_normal'              => 1000,
            'price_promo'               => 500,
            'date'                      => '2016-12-28'
        ]);
        
        // Create request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/competitor-brand/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/competitor-brand');
        $this->assertSessionHas('brand-deleted', '');
        
        // Validate data
        $brand = $this->brand->getOne($ID);
        $this->assertEquals(null, $brand);
        
        $price = $this->price->getOne($priceID);
        $this->assertEquals(0, $price->competitor_brand_ID);
        $this->assertEquals($oldBrand->name, $price->competitor_brand_custom);
    }
    
    
}

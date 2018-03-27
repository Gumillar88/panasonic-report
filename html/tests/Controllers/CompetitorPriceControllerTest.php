<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;

class CompetitorPriceControllerTest extends TestCase
{
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
     * Product category model container
     *
     * @access protected
     */
    protected $category;
    
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
     * Product category data sample
     *
     * @access protected
     */
    protected $productCategoryData = [
        'AC', 'TV', 'Refrigerator'
    ];

    /**
     * Branch data sample
     *
     * @access protected
     */
    protected $branchData = [
        [        
            'name'          => 'Jakarta',
            'region_ID'     => 1,
            'promotor_ID'   => 1
        ],
        [        
            'name'          => 'Bandung',
            'region_ID'     => 2,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 3
        ],
    ];
    
    /**
     * Dealer data sample
     *
     * @access protected
     */
    protected $dealerData = [
        [
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 1,
            'name'              => 'BALI ELECTRONIC CENTER',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 2,
            'name'              => 'ELECTRONIC CITY GATSU',
            'company'           => 'none',
            'address'           => 'none',
        ],
        [
            'dealer_account_ID' => 1,
            'dealer_type_ID'    => 1,
            'region_ID'         => 0,
            'branch_ID'         => 1,
            'dealer_channel_ID' => 1,
            'code'              => 3,
            'name'              => 'GALAXY',
            'company'           => 'none',
            'address'           => 'none',
        ]
    ];
    
    /**
     * Price sample data
     *
     * @access protected
     */
    protected $priceData = [
        'promotor_ID'               => 1,
        'dealer_ID'                 => 1,
        'product_model_ID'          => 1,
        'competitor_brand_ID'       => 1,
        'competitor_brand_custom'   => '',
        'product_category_ID'       => 1,
        'model_name'                => 'ABCD',
        'price_normal'              => 1000,
        'price_promo'               => 500,
        'date'                      => '2016-12-28'
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->branch   = new BranchModel();
        $this->dealer   = new DealerModel();
        $this->category = new ProductCategoryModel();
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
        // Populate branch
        foreach ($this->branchData as $branch)
        {
             $this->branch->create(
                 $branch['name'],
                 $branch['region_ID'],
                 $branch['promotor_ID']
             );
        }
        
        // Populate dealer
        foreach ($this->dealerData as $dealer)
        {
            $this->dealer->create([
                'region_ID'         => $dealer['region_ID'],
                'branch_ID'         => $dealer['branch_ID'],
                'dealer_account_ID' => $dealer['dealer_account_ID'],
                'dealer_channel_ID' => $dealer['dealer_channel_ID'],
                'dealer_type_ID'    => $dealer['dealer_type_ID'],
                'code'              => $dealer['code'],
                'name'              => $dealer['name'],
                'company'           => $dealer['company'],
                'address'           => $dealer['address']
            ]);
        }
        
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
        
        // Populate price
        $this->price->create($this->priceData);
    }
    
    /**
     * Test render competitor price index page
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $response = $this->withSession($this->adminSession)
            ->call('GET', '/competitor-price', ['month' => '2016-12']);
        
        // Verify response
        $this->assertViewHas('prices');
        
        // Check dealer name
        $this->assertPageContain($this->dealerData[0]['name']);
        $this->assertPageContain($this->branchData[0]['name']);
        $this->assertPageContain($this->productCategoryData[0]);
        $this->assertPageContain($this->brandData[0]);
    }
    
    /**
     * Test render remove competitor price page without parameter
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-price/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove competitor price page without valid price data
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-price/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove competitor price page
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID     = 1;
        $price  = $this->price->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/competitor-price/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('price', $price);
        $this->assertPageContain('Remove Competitor Price');
    }
    
    /**
     * Test handle remove competitor price request without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-price/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove competitor price without valid price data
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/competitor-price/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove price successfully
     *
     * @access public
     * @group CMS
     * @group CMS-CompetitorPriceController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $this->_populate();
        
        // Set data
        $ID = 1;
        
        // Create request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/competitor-price/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/competitor-price');
        $this->assertSessionHas('price-deleted', '');
        
        // Validate data
        $price = $this->price->getOne($ID);
        $this->assertEquals(null, $price);
    }
    
    
}

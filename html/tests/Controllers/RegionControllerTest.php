<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\RegionModel;
use App\Http\Models\DealerModel;
use App\Http\Models\PromotorModel;

class RegionControllerTest extends TestCase
{
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
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
     * Region data sample
     *
     * @access protected
     */
    protected $regionData = [
        [
            'name'          => 'Jakarta',
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Indonesia Timur',
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Indonesia Barat',
            'promotor_ID'   => 2
        ]
    ];
    
    /**
     * Promotor data sample
     *
     * @access protected
     */
    protected $promotorData = [
        [        
            'dealer_ID'     => 0,
            'phone'         => 4001,
            'password'      => '123456',
            'name'          => 'Area Coordinator A',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4002,
            'password'      => '123456',
            'name'          => 'Area Coordinator B',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 0,
            'phone'         => 4003,
            'password'      => '123456',
            'name'          => 'Area Coordinator C',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 0
        ],
    ];
    
    /**
     * Custom user data sample
     *
     * @access protected
     */
    protected $customRegionData = [
        'name'          => 'Jakarta Raya',
        'promotor_ID'   => 3
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->region   = new RegionModel();
        $this->dealer   = new DealerModel();
        $this->promotor = new PromotorModel();
    }
    
    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate promotor
        foreach ($this->promotorData as $promotor)
        {
             $this->promotor->create(
                 $promotor['dealer_ID'],
                 $promotor['phone'],
                 $promotor['password'],
                 $promotor['name'],
                 $promotor['gender'],
                 $promotor['type'],
                 $promotor['parent_ID']
             );
        }
        
        // Populate region
        $regionIDs = [];
        
        foreach ($this->regionData as $region)
        {
             $regionIDs[] = $this->region->create(
                 $region['name'],
                 $region['promotor_ID']
             );
        }
        
        return $regionIDs;
    }
    
    /**
     * Test render region index page
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/region');
        
        // Verify response
        foreach ($this->regionData as $region)
        {
            $this->assertPageContain($region['name']);
        }
        
        $this->assertViewHas('regions');
        $this->assertViewHas('dataArco');
    }
    
    /**
     * Test render create region page
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/region/create');
        
        // Verify response
        $this->assertPageContain('Create Region');
        
        foreach ($this->promotorData as $promotor)
        {
            $this->assertPageContain($promotor['name']);
        }
        
        $this->assertViewHas('dataArco');
    }
    
    /**
     * Test handle create region request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove region name
        $params = $this->customRegionData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/region/create', $params, [], [], ['HTTP_REFERER' => '/region/create']);
        
        $this->assertRedirectedTo('/region/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create region request with success
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/region/create', $this->customRegionData, [], [], ['HTTP_REFERER' => '/region/create']);
        
        $this->assertRedirectedTo('/region');
        $this->assertSessionHas('region-created', '');
    }
    
    /**
     * Test render update region page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/region/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update region page without valid region data
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/region/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update region page success
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $regionIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($regionIDs);
        $region = $this->region->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/region/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('region', $region);
        $this->assertViewHas('dataArco');
        $this->assertPageContain('Edit Region');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/region/edit', $this->customRegionData, [], [], ['HTTP_REFERER' => '/region/edit']);
        
        // Verify
        $this->assertRedirectedTo('/region/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $regionIDs = $this->_populate();
        
        // Set params
        $params = $this->customRegionData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($regionIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/region/edit', $params, [], [], ['HTTP_REFERER' => '/region/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/region/edit');
        $this->assertSessionHas('region-updated', '');
        
        // Validate data
        $region = $this->region->getOne($ID);
        $this->assertEquals($region->name, $this->customRegionData['name']);
        $this->assertEquals($region->promotor_ID, $this->customRegionData['promotor_ID']);
    }
    
    /**
     * Test render remove region page without region ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/region/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove region page without valid region data
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/region/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove region page
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $regionIDs = $this->_populate();
        
        // Set data
        $ID     = $this->_pickRandomItem($regionIDs);
        $region = $this->region->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/region/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('region', $region);
        $this->assertPageContain('Remove Region');
    }
    
    /**
     * Test handle remove region request without region ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/region/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove region without valid region data
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/region/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove region successfully
     *
     * @access public
     * @group CMS
     * @group CMS-RegionController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $regionIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($regionIDs);
        
        $dealerID = $this->dealer->create([
            'region_ID'         => $ID,
            'branch_ID'         => 0,
            'dealer_account_ID' => 0,
            'dealer_type_ID'    => 0,
            'dealer_channel_ID' => 0,
            'code'              => 'ABCD',
            'name'              => 'Dealer A',
            'company'           => 'none',
            'address'           => 'none'
        ]);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/region/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/region');
        $this->assertSessionHas('region-deleted', '');
        
        // Validate data
        $region = $this->region->getOne($ID);
        $this->assertEquals(null, $region);
        
        $dealer = $this->dealer->getOne($dealerID);
        $this->assertEquals(0, $dealer->region_ID);
    }
    
    
}

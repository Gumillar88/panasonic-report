<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\DealerTypeModel;

class DealerTypeControllerTest extends TestCase
{
    /**
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;
    
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
     * Dealer type data sample
     *
     * @access protected
     */
    protected $dealerTypeData = [
        ['name' => 'R1'],
        ['name' => 'R2'],
        ['name' => 'R3'],
    ];
    
    
    /**
     * Custom dealer type data sample
     *
     * @access protected
     */
    protected $customDealerTypeData = [
        'name'          => 'R4',
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer_type  = new DealerTypeModel();
    }
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate dealer type
        $dealerTypeIDs = [];
        
        foreach ($this->dealerTypeData as $dealerType)
        {
             $dealerTypeIDs[] = $this->dealer_type->create(
                 $dealerType['name']
             );
        }
        
        return $dealerTypeIDs;
    }
    
    /**
     * Test render dealer type index page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type');
        
        // Verify response
        foreach ($this->dealerTypeData as $dealerType)
        {
            $this->assertPageContain($dealerType['name']);
        }
        
        $this->assertViewHas('dealer_types');
    }
    
    /**
     * Test render create dealer type page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/create');
        
        // Verify response
        $this->assertPageContain('Create Dealer Type');
    }
    
    /**
     * Test handle create dealer type request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove dealer type name
        $params = $this->customDealerTypeData;
        unset($params['name']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/create', $params, [], [], ['HTTP_REFERER' => '/dealer-type/create']);
        
        $this->assertRedirectedTo('/dealer-type/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create dealer type request with success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/create', $this->customDealerTypeData, [], [], ['HTTP_REFERER' => '/dealer-type/create']);
        
        $this->assertRedirectedTo('/dealer-type');
        $this->assertSessionHas('dealer-type-created', '');
    }
    
    /**
     * Test render update dealer type page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer type page without valid dealer type data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update dealer type page success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $dealerTypeIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerTypeIDs);
        $dealerType     = $this->dealer_type->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('dealer_type', $dealerType);
        $this->assertPageContain('Edit Dealer Type');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/edit', $this->customDealerTypeData, [], [], ['HTTP_REFERER' => '/dealer-type/edit']);
        
        // Verify
        $this->assertRedirectedTo('/dealer-type/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $dealerTypeID = $this->_populate();
        
        // Set params
        $params = $this->customDealerTypeData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($dealerTypeID);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/edit', $params, [], [], ['HTTP_REFERER' => '/dealer-type/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-type/edit');
        $this->assertSessionHas('dealer-type-updated', '');
        
        // Validate data
        $dealerType = $this->dealer_type->getOne($ID);
        $this->assertEquals($dealerType->name, $this->customDealerTypeData['name']);
    }
    
    /**
     * Test render remove dealer type page without dealer type ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer type page without valid dealer type data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove dealer type page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $dealerTypeIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerTypeIDs);
        $dealerType     = $this->dealer_type->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-type/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Dealer Type');
    }
    
    /**
     * Test handle remove dealer type request without dealer type ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer type without valid dealer type data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer type successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DealerTypeController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $dealerTypeIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($dealerTypeIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-type/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-type');
        $this->assertSessionHas('dealer-type-deleted', '');
        
        // Validate data
        $dealerType = $this->dealer_type->getOne($ID);
        $this->assertEquals(null, $dealerType);
        
    }
    
    
}

<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\DealerChannelModel;

class DealerChannelControllerTest extends TestCase
{
    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
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
     * Custom Dealer channel data sample
     *
     * @access protected
     */
    protected $customDealerChannelData = [
        'name'          => 'SOS',
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer_channel   = new DealerChannelModel();
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
        $dealerChannelIDs = [];
        
        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $dealerChannelIDs[] = $this->dealer_channel->create(
                 $dealerChannel['name']
             );
        }
        
        return $dealerChannelIDs;
    }
    
    /**
     * Test render dealer channel index page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel');
        
        // Verify response
        foreach ($this->dealerChannelData as $dealerChannel)
        {
            $this->assertPageContain($dealerChannel['name']);
        }
        
        $this->assertViewHas('dealer_channels');
    }
    
    /**
     * Test render create dealer channel page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/create');
        
        // Verify response
        $this->assertPageContain('Create Dealer Channel');

    }

    /**
     * Test handle create dealer channel request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove dealer channel name
        $params =[];
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/create', $params, [], [], ['HTTP_REFERER' => '/dealer-channel/create']);
        
        $this->assertRedirectedTo('/dealer-channel/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create dealer channel request with success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/create', $this->customDealerChannelData, [], [], ['HTTP_REFERER' => '/dealer-channel/create']);
        
        $this->assertRedirectedTo('/dealer-channel');
        $this->assertSessionHas('dealer-channel-created', '');
    }

    /**
     * Test render update dealer channel page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/edit');
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render update dealer channel page without valid dealer channel data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/edit', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render update dealer channel page success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $dealerChannelIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerChannelIDs);
        $dealerChannel  = $this->dealer_channel->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('dealer_channel', $dealerChannel);
        $this->assertPageContain('Edit Dealer Channel');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/edit', $this->customDealerChannelData, [], [], ['HTTP_REFERER' => '/dealer-channel/edit']);
        
        // Verify
        $this->assertRedirectedTo('/dealer-channel/edit');
        $this->assertSessionHasErrors();
    }

    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $dealerChannelIDs = $this->_populate();
        
        // Set params
        $params = $this->customDealerChannelData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($dealerChannelIDs);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/edit', $params, [], [], ['HTTP_REFERER' => '/dealer-channel/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-channel/edit');
        $this->assertSessionHas('dealer-channel-updated', '');
        
        // Validate data
        $dealerChannel = $this->dealer_channel->getOne($ID);
        $this->assertEquals($dealerChannel->name, $this->customDealerChannelData['name']);
    }

    /**
     * Test render remove dealer channel page without dealer channel ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/remove');
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render remove dealer channel page without valid dealer channel data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test render remove dealer channel page
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $dealerChannelID = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($dealerChannelID);
        $dealerChannel  = $this->dealer_channel->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/dealer-channel/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Dealer Channel');
    }
    
    /**
     * Test handle remove dealer channel request without dealer channel ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/remove');
        
        $this->assertResponseStatus(404);
    }

    /**
     * Test handle remove dealer channel without valid dealer channel data
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove dealer channel successfully
     *
     * @access public
     * @group CMS
     * @group CMS-DealerChannelController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $dealerChannelIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($dealerChannelIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/dealer-channel/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/dealer-channel');
        $this->assertSessionHas('dealer-channel-removed', '');
        
        // Validate data
        $dealerChannel  = $this->dealer_channel->getOne($ID);
        $this->assertEquals(null, $dealerChannel);
    }
    
}

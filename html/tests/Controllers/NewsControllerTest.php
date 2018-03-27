<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\NewsModel;
use App\Http\Models\DealerNewsModel;
use App\Http\Models\DealerModel;

class NewsControllerTest extends TestCase
{
    /**
     * News model container
     *
     * @access protected
     */
    protected $news;
    
    /**
     * Dealer news model container
     *
     * @access protected
     */
    protected $dealerNews;

    /**
     * Delaer model container
     *
     * @access protected
     */
    protected $dealer;
    
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
     * News data sample
     *
     * @access protected
     */
    protected $newsData = [
        [
            'title'         => 'news 1',
            'content'       => 'content 1',
            'created_by'    => 0
        ],
        [
            'title'         => 'news 2',
            'content'       => 'content 2',
            'created_by'    => 0
        ],
        [
            'title'         => 'news 3',
            'content'       => 'content 3',
            'created_by'    => 0
        ]
    ];
    
    
    /**
     * Custom news data sample
     *
     * @access protected
     */
    protected $customNewsData = [
        'title'         => 'news 4',
        'content'       => 'content 5',
        'dealer_ID'     => [1,2,3]
    ];
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->news         = new NewsModel();
        $this->dealerNews   = new DealerNewsModel();
        $this->dealer       = new DealerModel();
    }
    
    /**
     * Populate database 
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // Populate dealer
        foreach ($this->dealerData as $dealer)
        {   
            $data = [
                'region_ID'         => $dealer['region_ID'],
                'branch_ID'         => $dealer['branch_ID'],
                'dealer_account_ID' => $dealer['dealer_account_ID'],
                'dealer_channel_ID' => $dealer['dealer_channel_ID'],
                'dealer_type_ID'    => $dealer['dealer_type_ID'],
                'code'              => $dealer['code'],
                'name'              => $dealer['name'],
                'company'           => $dealer['company'],
                'address'           => $dealer['address']
            ];
            $this->dealer->create($data);
        }

        // Populate news
        $newsIDs = [];
        
        foreach ($this->newsData as $news)
        {
             $newsIDs[] = $this->news->create(
                 $news['title'],
                 $news['content'],
                 $news['created_by']
             );
        }
        
        return $newsIDs;
    }
    
    /**
     * Test render news index page
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderIndex()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/news');
        
        // Verify response
        foreach ($this->newsData as $news)
        {
            $this->assertPageContain($news['title']);
        }
        
        $this->assertViewHas('news');
        $this->assertViewHas('dealers');
    }
    
    /**
     * Test render create news page
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderCreate()
    {
        // Populate data
        $this->_populate();
        
        // Make request
        $this->withSession($this->adminSession)
            ->call('GET', '/news/create');
        
        // Verify response
        $this->assertPageContain('Create Article');
    }
    
    /**
     * Test handle create news request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleCreateValidationError()
    {
        // Set parameter and remove news title
        $params = $this->customNewsData;
        unset($params['title']);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/news/create', $params, [], [], ['HTTP_REFERER' => '/news/create']);
        
        $this->assertRedirectedTo('/news/create');
        $this->assertSessionHasErrors();
        $this->assertHasOldInput();
    }
    
    /**
     * Test handle create news request with success
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleCreateSuccess()
    {
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/news/create', $this->customNewsData, [], [], ['HTTP_REFERER' => '/news/create']);

        $this->assertRedirectedTo('/news');
        $this->assertSessionHas('news-created', '');
    }
    
    /**
     * Test render update news page without ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderUpdateNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/news/edit');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update news page without valid news data
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderUpdateNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/news/edit', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render update news page success
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderUpdateSuccess()
    {
        // Populate data
        $newsIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($newsIDs);
        $news           = $this->news->getOne($ID);
        
        // Request
        $this->withSession($this->adminSession)
            ->call('GET', '/news/edit', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('news', $news);
        $this->assertViewHas('dealers');
        $this->assertPageContain('Edit Article');
    }

    
    /**
     * Test handle update request with validation error
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleUpdateValidationError()
    {
        // Populate data
        $this->_populate();
        
        // Request
        $response = $this->withSession($this->adminSession)
            ->call('POST', '/news/edit', $this->customNewsData, [], [], ['HTTP_REFERER' => '/news/edit']);
        
        // Verify
        $this->assertRedirectedTo('/news/edit');
        $this->assertSessionHasErrors();
    }
    
    /**
     * Test handle update request success
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleUpdateSuccess()
    {
        // Populate data
        $newsID = $this->_populate();
        
        // Set params
        $params = $this->customNewsData;
        
        // Add ID
        $ID             = $this->_pickRandomItem($newsID);
        $params['ID']   = $ID;
        
        // Request
        $this->withSession($this->adminSession)
            ->call('POST', '/news/edit', $params, [], [], ['HTTP_REFERER' => '/news/edit']);
        
        // Validate response
        $this->assertRedirectedTo('/news/edit');
        $this->assertSessionHas('news-updated', '');
        
        // Validate data
        $news = $this->news->getOne($ID);
        $this->assertEquals($news->title, $this->customNewsData['title']);
    }
    
    /**
     * Test render remove news page without news ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/news/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove news page without valid news data
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('GET', '/news/remove', ['ID' => 4]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test render remove news page
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testRenderRemoveSuccess()
    {
        // Populate data
        $newsIDs = $this->_populate();
        
        // Set data
        $ID             = $this->_pickRandomItem($newsIDs);
        $news           = $this->news->getOne($ID);
        
        $this->withSession($this->adminSession)
            ->call('GET', '/news/remove', ['ID' => $ID]);
        
        // Verify
        $this->assertResponseOk();
        $this->assertViewHas('ID', $ID);
        $this->assertPageContain('Remove Article');
    }
    
    /**
     * Test handle remove news request without news ID parameter
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleRemoveNoID()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/news/remove');
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove news without valid news data
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleRemoveNoUser()
    {
        $this->withSession($this->adminSession)
            ->call('POST', '/news/remove', ['ID' => 2]);
        
        $this->assertResponseStatus(404);
    }
    
    /**
     * Test handle remove news successfully
     *
     * @access public
     * @group CMS
     * @group CMS-NewsController
     * @return Void
     */
    public function testHandleRemoveSuccess()
    {
        // Populate data
        $newsIDs = $this->_populate();
        
        // Set data
        $ID = $this->_pickRandomItem($newsIDs);
        
        $this->withSession($this->adminSession)
            ->call('POST', '/news/remove', ['ID' => $ID]);
        
        // Validate response
        $this->assertRedirectedTo('/news');
        $this->assertSessionHas('news-removed', '');
        
        // Validate data
        $news = $this->news->getOne($ID);
        $this->assertEquals(null, $news);
        
    }
    
    
}

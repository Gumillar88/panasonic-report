<?php
namespace Tests\Controllers\API\V_1_5_0;

use Hash;
use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

use App\Http\Models\DealerModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\BranchModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorMetaModel;
use App\Http\Models\NewsModel;
use App\Http\Models\DealerNewsModel;
use App\Http\Models\TokenModel;

class NewsControllerTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

    /**
     * Dealer account model container
     *
     * @access protected
     */
    protected $dealer_account;

    /**
     * Branch model container
     *
     * @access protected
     */
    protected $branch;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

    /**
     * Promotor meta model container
     *
     * @access protected
     */
    protected $promotor_meta;
    
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
     * Token model container
     *
     * @access protected
     */
    protected $token;
    
    /**
     * Promotor data sample
     *
     * @access protected
     */
    protected $promotorData = [
        'dealer_ID'     => 1,
        'phone'         => '+6280010003000',
        'phoneNormal'   => '080010003000',
        'password'      => '1234',
        'name'          => 'Alfian',
        'gender'        => 'male',
        'type'          => 'promotor',
        'parent_ID'     => 0,
    ];

    /**
     * Dealer data sample
     *
     * @access protected
     */
    protected $dealerData = [
        'region_ID' => 1,
        'branch_ID' => 1,
        'dealer_account_ID' => 1,
        'dealer_type_ID' => 1,
        'dealer_channel_ID' => 1,
        'code' => 1,
        'name' => 'BALI ELECTRONIC CENTER',
        'company' => 'none',
        'address' => 'none'
    ];

    /**
     * Dealer Account data sample
     *
     * @access protected
     */
    protected $dealerAccountData = [
        'name'          => 'Electronic City',
        'branch_ID'     => 1,
        'promotor_ID'   => 2,
    ];

    /**
     * Branch data sample
     *
     * @access protected
     */
    protected $branchData = [
        'name'          => 'BD Jakarta',
        'region_ID'     => 1,
        'promotor_ID'   => 2,
    ];

    /**
     * News data sample
     *
     * @access protected
     */
    protected $newsData = [
        'title'       => 'news',
        'content'     => 'news 1 2 3',
        'created_by'  => '1',
    ];

    /**
     * Dealer news data sample
     *
     * @access protected
     */
    protected $dealerNewsData = [1,2,3,4,5];

    /**
     * News data sample
     *
     * @access protected
     */
    protected $promotorNewsData = [
        'promotor_ID'       => 1,
        'name'              => 'news',
        'content'           => '[1]',
    ];

    /**
     * News data sample
     *
     * @access protected
     */
    protected $promotorNewsEmptyData = [
        'promotor_ID'       => 1,
        'name'              => 'news',
        'content'           => '[]',
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->dealer           = new DealerModel();
        $this->dealer_account   = new DealerAccountModel();
        $this->branch           = new BranchModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_meta    = new PromotorMetaModel();
        $this->news             = new NewsModel();
        $this->dealerNews       = new DealerNewsModel();
        $this->token            = new TokenModel();
    }
    
    /**
     * Populate promotor meta database with promotorNews data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotorMeta()
    {
        return $this->promotor_meta->set(
            $this->promotorNewsData['promotor_ID'], 
            $this->promotorNewsData['name'], 
            $this->promotorNewsData['content']
        );
    }

    /**
     * Populate promotor meta empty database with promotorNews data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotorMetaEmpty()
    {
        return $this->promotor_meta->set(
            $this->promotorNewsEmptyData['promotor_ID'], 
            $this->promotorNewsEmptyData['name'], 
            $this->promotorNewsEmptyData['content']
        );
    }

    /**
     * Populate news database with news data
     *
     * @access private
     * @return Integer
     */
    private function _populateNews()
    {
        return $this->news->create(
            $this->newsData['title'], 
            $this->newsData['content'], 
            $this->newsData['created_by']
        );
    }

    /**
     * Populate dealer news database with dealerNews data
     *
     * @access private
     * @return Integer
     */
    private function _populateDealerNews()
    {
        return $this->dealerNews->set(1,$this->dealerNewsData);
    }

    /**
     * Populate branch database with branch data
     *
     * @access private
     * @return Integer
     */
    private function _populateBranch()
    {
        return $this->branch->create(
            $this->branchData['name'], 
            $this->branchData['region_ID'], 
            $this->branchData['promotor_ID']
        );
    }

    /**
     * Populate dealer database with dealer data
     *
     * @access private
     * @return Integer
     */
    private function _populateDealer()
    {
        return $this->dealer->create($this->dealerData);
    }

    /**
     * Populate dealer account database with dealer Account data
     *
     * @access private
     * @return Integer
     */
    private function _populateDealerAccount()
    {
        return $this->dealer_account->create(
            $this->dealerAccountData['name'], 
            $this->dealerAccountData['branch_ID'],
            $this->dealerAccountData['promotor_ID']
        ); 
    }

    /**
     * Populate promotor database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populatePromotor()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            $this->promotorData['type'], 
            $this->promotorData['parent_ID']
        );
    }

     /**
     * Populate TL database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populateTL()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            '+6280010003001',
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'tl', 
            1
        );
    }

    /**
     * Populate arco database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populateArco()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'arco', 
            $this->promotorData['parent_ID']
        );
    }

    /**
     * Populate panasonic database with promotor data
     *
     * @access private
     * @return Integer
     */
    private function _populatePanasonic()
    {
        return $this->promotor->create(
            $this->promotorData['dealer_ID'], 
            $this->promotorData['phone'], 
            Hash::make($this->promotorData['password']), 
            $this->promotorData['name'], 
            $this->promotorData['gender'], 
            'panasonic', 
            $this->promotorData['parent_ID']
        );
    }
    
    /**
     * Test get total list with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/news-total')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test get total list with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/news-total', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test get total list with valid promotor token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalWithPromotorToken()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-total', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get total list with valid TL token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalWithTLToken()
    {
        // Populate data
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-total', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get total listwith valid Arco token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalWithArcoToken()
    {
        // Populate data
        $arcoID             = $this->_populateArco();
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($arcoID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-total', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get total list with valid panasonic token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListTotalWithPanasonicToken()
    {
        // Populate data
        $panasonicID = $this->_populatePanasonic();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($panasonicID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-total', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

     /**
     * Test get list with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/news-list')
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test get list with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/news-list', ['token' => '1234'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test get total list with valid promotor token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithPromotorToken()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();
        $newsID         = $this->_populateNews();
        $dealerNewsID   = $this->_populateDealerNews();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get total list with valid promotor token and promtor meta data
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithPromotorTokenAndPromotorMeta()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();
        $newsID         = $this->_populateNews();
        $dealerNewsID   = $this->_populateDealerNews();
        $promtorMetaID  = $this->_populatePromotorMeta();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get list with valid TL token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithTLToken()
    {
        // Populate data
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();
        $newsID             = $this->_populateNews();
        $dealerNewsID       = $this->_populateDealerNews();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get list with valid TL token and promotor meta data
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithTLTokenAndPromotorMeta()
    {
        // Populate data
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();
        $newsID             = $this->_populateNews();
        $dealerNewsID       = $this->_populateDealerNews();
        $promtorMetaID      = $this->_populatePromotorMeta();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get listwith valid Arco token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithArcoToken()
    {
        // Populate data
        $arcoID             = $this->_populateArco();
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();
        $newsID             = $this->_populateNews();
        $dealerNewsID       = $this->_populateDealerNews();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($arcoID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get listwith valid Arco token and promotor meta data
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithArcoTokenAndPromotorMeta()
    {
        // Populate data
        $arcoID             = $this->_populateArco();
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();
        $newsID             = $this->_populateNews();
        $dealerNewsID       = $this->_populateDealerNews();
        $promtorMetaID      = $this->_populatePromotorMeta();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($arcoID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get list with valid panasonic token
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithPanasonicToken()
    {
        // Populate data
        $panasonicID = $this->_populatePanasonic();
        $newsID         = $this->_populateNews();
        $dealerNewsID   = $this->_populateDealerNews();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($panasonicID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get list with valid panasonic token and promotor meta data
     * 
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetListWithPanasonicTokenAndPromotorMeta()
    {
        // Populate data
        $panasonicID = $this->_populatePanasonic();
        $newsID         = $this->_populateNews();
        $dealerNewsID   = $this->_populateDealerNews();
        $promtorMetaID      = $this->_populatePromotorMeta();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($panasonicID, $token);
        
        $params = [
            'token' => $encryptedToken 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-list', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get view news with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewNoToken()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/news-view')
            ->_result(['error' => 'no-news-id']);
    }

    /**
     * Test get view news with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewNoTokenWithNewsID()
    {
        // Do Request
        $this->_request('GET', '/api/1.5.0/news-view',['newsID' => '1'])
            ->_result(['error' => 'no-token']);
    }
    
    /**
     * Test get view news with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/news-view', ['token' => '1234', 'newsID' => '1'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test get view news with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewWithValidPromotorTokenAndInvalidNews()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('GET', '/api/1.5.0/news-view', ['token' => $encryptedToken, 'newsID' => '11'])
            ->_result(['error' => 'no-article']);
    }

    /**
     * Test get view news with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewWithValidPromotorTokenAndValidNews()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();
        $newsID         = $this->_populateNews();
        $promtorMetaID  = $this->_populatePromotorMetaEmpty();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token' => $encryptedToken,
            'newsID' => 1 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-view', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test get news view with invalid promotor token and promotor meta data
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testGetViewWithValidPromotorTokenAndValidNewsAndPromotorMeta()
    {
        // Populate data
        $promotorID     = $this->_populatePromotor();
        $newsID         = $this->_populateNews();
        $promtorMetaID  = $this->_populatePromotorMeta();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        $params = [
            'token' => $encryptedToken,
            'newsID' => 1 
        ];

        // Do request
        $response   = $this->call('GET', '/api/1.5.0/news-view', $params);
        $result     = json_decode($response->getContent(), true);

        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }
    
    /**
     * Test create news with no token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateNoToken()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/news-create')
            ->_result(['error' => 'no-token']);
    }

    /**
     * Test create news with no title
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateNoTitle()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/news-create',['token' => '123'])
            ->_result(['error' => 'no-title']);
    }
    
    /**
     * Test create news with no content
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateNoContent()
    {
        // Do Request
        $this->_request('POST', '/api/1.5.0/news-create',['token' => '123','title'=>'news'])
            ->_result(['error' => 'no-content']);
    }

    /**
     * Test create news with invalid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithInvalidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/news-create', ['token' => '1234', 'title' => 'news', 'content' => 'content'])
            ->_result(['error' => 'no-auth']);
    }

    /**
     * Test create news with valid promotor token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidPromotorToken()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/news-create', ['token' => $encryptedToken, 'title' => 'news', 'content' => 'content'])
            ->_result(['result' => 'success']);
    }

    /**
     * Test create news with valid promotor token and image
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidPromotorTokenAndImage()
    {
        // Populate data
        $promotorID = $this->_populatePromotor();
        
        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($promotorID, $token);
        
        //image
        $path = public_path('static/img/logo.png');
        $file = new UploadedFile($path, 'logo.png', filesize($path), 'image/png', null, true);

        // Do request
        $params = [
            'token' => $encryptedToken, 
            'title' => 'news',
            'content' => 'content',
        ];


        // Do request
        $response = $this->call('POST', '/api/1.5.0/news-create', $params, [], ['image' => $file], ['Accept' => 'multipart/form-data']);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test create news with valid TL token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidTlToken()
    {
        // Populate data
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        // Do request
        $this->_request('POST', '/api/1.5.0/news-create', ['token' => $encryptedToken, 'title' => 'news', 'content' => 'content'])
            ->_result(['result' => 'success']);
    }

    /**
     * Test create news with valid TL token and image
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidTlTokenAndImage()
    {
        // Populate data
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($tlID, $token);
        
        //image
        $path = public_path('static/img/logo.png');
        $file = new UploadedFile($path, 'logo.png', filesize($path), 'image/png', null, true);

        // Do request
        $params = [
            'token' => $encryptedToken, 
            'title' => 'news',
            'content' => 'content',
        ];


        // Do request
        $response = $this->call('POST', '/api/1.5.0/news-create', $params, [], ['image' => $file], ['Accept' => 'multipart/form-data']);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
    }

    /**
     * Test create news with valid arco token
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidArcoToken()
    {
        // Populate data
        $arcoID             = $this->_populateArco();
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($arcoID, $token);
        
        $path = public_path('static/img/logo.png');

        $file = new UploadedFile($path, 'logo.png', filesize($path), 'image/png', null, true);

        // Do request
        $this->_request('POST', '/api/1.5.0/news-create', ['token' => $encryptedToken, 'title' => 'news', 'content' => 'content'])
            ->_result(['result' => 'success']);
        
    }

    /**
     * Test create news with valid arco token and image
     *
     * @access public
     * @group API
     * @group API-1.5.0
     * @group API-1.5.0-NewsController
     * @return Void
     */
    public function testCreateWithValidArcoTokenAndImage()
    {
        // Populate data
        $arcoID             = $this->_populateArco();
        $tlID               = $this->_populateTL();
        $dealerID           = $this->_populateDealer();
        $dealerAccountID    = $this->_populateDealerAccount();
        $branchID           = $this->_populateBranch();

        // Create token
        $token = str_random(5);
        $encryptedToken = $this->token->encode($arcoID, $token);
        
        //image
        $path = public_path('static/img/logo.png');
        $file = new UploadedFile($path, 'logo.png', filesize($path), 'image/png', null, true);

        // Do request
        $params = [
            'token' => $encryptedToken, 
            'title' => 'news',
            'content' => 'content',
        ];


        // Do request
        $response = $this->call('POST', '/api/1.5.0/news-create', $params, [], ['image' => $file], ['Accept' => 'multipart/form-data']);
        $result     = json_decode($response->getContent(), true);
        
        // Verify
        $this->assertTrue(array_key_exists('result', $result));
        
    }
}
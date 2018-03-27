<?php
namespace Tests\Controllers;

use TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorMetaModel;
use App\Http\Models\DashboardAccountModel;
use App\Http\Models\DashboardTokenModel;
use App\Http\Models\ReportModel;

class DashboardControllerTest extends TestCase
{
    /**
     * Region model container
     *
     * @access protected
     */
    protected $region;
    
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
     * Dealer account model container
     *
     * @access protected;
     */
    protected $dealerAccount;

    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealerChannel;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

     /**
     * Promotor_meta model container
     *
     * @access protected
     */
    protected $promotor_meta;

    /**
     * Dashboard account model container
     *
     * @access protected
     */
    protected $account;
    
    /**
     * Dashboard token model container
     *
     * @access protected
     */
    protected $token;

    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;

    
    /**
     * Admin session data
     *
     * @access protected
     */
    protected $adminSession = [
        'code'       => 'QqxWvFH5TzX8iJbClcRQINrevIZ9fjKi',
    ];

    /**
     * Code session data
     *
     * @access protected
     */
    protected $customSession = [
        'code'       => '1QqxWvFH5TzX8iJbClcRQINrevIZ9fjKi',
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
            'promotor_ID'   => 1
        ]
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
     * Branch data sample
     *
     * @access protected
     */
    protected $branchData = [
        [        
            'name'          => 'Jakarta',
            'region_ID'     => 1,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bandung',
            'region_ID'     => 2,
            'promotor_ID'   => 2
        ],
        [        
            'name'          => 'Bekasi',
            'region_ID'     => 3,
            'promotor_ID'   => 2
        ],
    ];

    /**
     * Dealer account data sample
     *
     * @access protected
     */
    protected $dealerAccountData = [
        [
            'name'          => 'Electronic City',
            'branch_ID'     => 1,
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Best Denki',
            'branch_ID'     => 2,
            'promotor_ID'   => 1
        ],
        [
            'name'          => 'Electronic Solution',
            'branch_ID'     => 3,
            'promotor_ID'   => 1
        ]
    ];

    /**
     * Promotor data sample
     *
     * @access protected
     */
    protected $promotorData = [
        [        
            'dealer_ID'     => 1,
            'phone'         => 1000,
            'password'      => '123456',
            'name'          => 'Panasonic',
            'gender'        => 'male',
            'type'          => 'panasonic',
            'parent_ID'     => 0
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => 1001,
            'password'      => '123456',
            'name'          => 'Arco',
            'gender'        => 'male',
            'type'          => 'arco',
            'parent_ID'     => 1
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => 4001,
            'password'      => '123456',
            'name'          => 'Team Leader',
            'gender'        => 'male',
            'type'          => 'tl',
            'parent_ID'     => 2
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543635',
            'password'      => '123456',
            'name'          => 'Promotor A',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543631',
            'password'      => '123456',
            'name'          => 'Promotor B',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
        [        
            'dealer_ID'     => 1,
            'phone'         => '+6285776543632',
            'password'      => '123456',
            'name'          => 'Promotor C',
            'gender'        => 'male',
            'type'          => 'promotor',
            'parent_ID'     => 3
        ],
    ];

    /**
     * Custom promotor data sample
     *
     * @access protected
     */
    protected $customPromotorData = [        
        'dealer_ID'     => 1,
        'phone'         => '+628978541254',
        'password'      => '123456',
        'name'          => 'Ester Nursahbat',
        'gender'        => 'male',
        'type'          => 'promotor',
        'parent_ID'     => 1
    ];

     /**
     * Account data sample
     *
     * @access protected
     */
    protected $accountData = [
        [
            'email'         => 'alfian.sibuea@gmail.com',
            'name'          => 'Alfian',
            'last_access'   => 0,
        ],
        [
            'email'         => 'alfiana.sibuea@havas.com',
            'name'          => 'Alfiana Sibuea',
            'last_access'   => 0,
        ],
        [
            'email'         => 'dimas.prasetyo@id.panasonic.com',
            'name'          => 'Dimas',
            'last_access'   => 0,
        ]
    ];
    
    /**
     * Token account data sample
     *
     * @access protected
     */
    protected $tokenData = [
        [
            'dashboard_account_ID'  => 1,
            'token'                 => 'QqxWvFH5TzX8iJbClcRQINrevIZ9fjKi',
        ],
        [
            'dashboard_account_ID'  => 2,
            'token'                 => 'MUf6dhl9n0NqWwego9fDCUKLdMZk71RS',
        ],
        [
            'dashboard_account_ID'  => 3,
            'token'                 => 'oyuFZFUqlGjptyJqKjPohEuhPEu4hLtr',
        ]
    ];

    /**
     * Report data sample
     *
     * @access protected
     */
    protected $reportData = [
        [
            'dealer_ID'         => 1,
            'promotor_ID'       => 4,
            'account_ID'        => 1,
            'tl_ID'             => 2,
            'arco_ID'           => 3,
            'customer_ID'       => 1,
            'product_model_ID'  => 1,
            'custom_name'       => '',
            'price'             => 100,
            'quantity'          => 1,
            'date'              => '2016-12-09',
        ],
        [
            'dealer_ID'         => 1,
            'promotor_ID'       => 4,
            'account_ID'        => 1,
            'tl_ID'             => 2,
            'arco_ID'           => 3,
            'customer_ID'       => 1,
            'product_model_ID'  => 2,
            'custom_name'       => '',
            'price'             => 100,
            'quantity'          => 1,
            'date'              => '2016-12-09',
        ],
        [
            'dealer_ID'         => 1,
            'promotor_ID'       => 4,
            'account_ID'        => 1,
            'tl_ID'             => 2,
            'arco_ID'           => 3,
            'customer_ID'       => 1,
            'product_model_ID'  => 3,
            'custom_name'       => '',
            'price'             => 1020,
            'quantity'          => 1,
            'date'              => '2016-12-09',
        ]
    ];

    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer           = new DealerModel();
        $this->dealerChannel    = new DealerChannelModel();
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_meta    = new PromotorMetaModel();
        $this->account          = new DashboardAccountModel();
        $this->dealer           = new DealerModel();
        $this->token            = new DashboardTokenModel();
        $this->report           = new ReportModel();
    }
    
    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populate()
    {
        // populate Rgion
        foreach ($this->regionData as $region)
        {
             $this->region->create(
                 $region['name'],
                 $region['promotor_ID']
             );
        }
        
        // Populate branch
        foreach ($this->branchData as $branch)
        {
             $this->branch->create(
                 $branch['name'],
                 $branch['region_ID'],
                 $branch['promotor_ID']
             );
        }

        // Popular Dealer 
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

        foreach ($this->dealerChannelData as $dealerChannel)
        {
             $this->dealerChannel->create(
                 $dealerChannel['name']
             );
        }

        // Populate dealer account
        foreach ($this->dealerAccountData as $dealerAccount)
        {
            $this->dealerAccount->create(
                 $dealerAccount['name'],
                 $dealerAccount['branch_ID'],
                 $dealerAccount['promotor_ID']
            );
        }

        //Populate token
        foreach ($this->tokenData as $token)
        {
             $this->token->create(
                 $token['dashboard_account_ID'],
                 $token['token']
             );
        }

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

        // Populate report
        foreach ($this->reportData as $data)
        {
            return $this->report->create($data);

        }
        
        // Populate dashboard 
        $accountIDs = [];

        foreach ($this->accountData as $account)
        {
            $accountIDs[] = $this->account->create(
                 $account['email'],
                 $account['name'],
                 $account['last_access']
             );
        }

        return $accountIDs;
    }

    /**
     * Populate database with promotor data
     *
     * @access private
     * @return Array
     */
    private function _populateToken()
    {
        //Populate token
        foreach ($this->tokenData as $token)
        {
             $this->token->create(
                 $token['dashboard_account_ID'],
                 $token['token']
             );
        }
    }
    
    /**
     * Test render promotor index page without code
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardController
     * @return Void
     */
    public function testRenderIndexNoCode()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->call('GET', '/dashboard');

        $this->assertRedirectedTo('/dashboard/login');
    }

    /**
     * Test render promotor index page with wrong code
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardController
     * @return Void
     */
    public function testRenderIndexWrongCode()
    {
        // Populate data
        $this->_populate();
        
        // Make Request
        $this->withSession($this->customSession)
            ->call('GET', '/dashboard');

        $this->assertRedirectedTo('/dashboard/login');
    }

    /**
     * Test render promotor index page without code
     *
     * @access public
     * @group CMS
     * @group CMS-DashboardController
     * @return Void
     */
    public function testRenderIndexNoUser()
    {
        // Populate data
        $this->_populateToken();
        
        // Make Request
        $this->withSession($this->adminSession)
            ->call('GET', '/dashboard');

        $this->assertRedirectedTo('/dashboard/login');
    }
    
}

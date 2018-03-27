<?php

namespace App\Http\Controllers\Dashboard;

use App;
use Crypt;
use Hash;
use Mail;
use Carbon\Carbon;

use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Models\DashboardDataModel;
use App\Http\Models\DashboardAccountModel;
use App\Http\Models\DashboardTokenModel;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\CompetitorBrandModel;
use App\Http\Models\CompetitorPriceModel;


use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_NumberFormat;

class DashboardController extends Controller
{

    /**
     * Dashboard data model container
     *
     * @access protected
     */
    protected $data;
    
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
     * Region model container
     *
     * @access protected
     */
    protected $region;

    /**
     * branch model container
     *
     * @access protected
     */
    protected $branch;

    /**
     * Dealer Account model container
     *
     * @access protected
     */
    protected $dealer_account;

    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;

    /**
     * Channel model container
     *
     * @access protected
     */
    protected $dealer_channel;
    
    /**
     * Competitor brand model container
     *
     * @access protected
     */
    protected $competitorBrand;
    
    /**
     * Competitor price model container
     *
     * @access protected
     */
    protected $competitorPrice;
    
    /**
     * Static value for account all nation
     *
     * @access protected
     */
    protected $accountAllNation = [
        'Electronic City',
        'Best Denki',
        'Electronic Solution',
        'Hypermart',
        'Carrefour',
        'Lottemart',
        'Courts',
        'Depo Bangunan',
        'Mitra 10',
        'Save Max',
        'White Brown',
        'Giant',
        'Lulu'
    ];

    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
	public function __construct()
    {
        $this->data             = new DashboardDataModel();
        $this->account          = new DashboardAccountModel();
        $this->token            = new DashboardTokenModel();
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealer_account   = new DealerAccountModel();
        $this->dealer           = new DealerModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->competitorBrand  = new CompetitorBrandModel();
        $this->competitorPrice  = new CompetitorPriceModel();
    }
    
    /**
     * Get all region for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllRegion()
    {
        // Set default region data
        $regionData = [
            0 => 'All'
        ];

        $regions = $this->region->getAll();

        foreach ($regions as $key => $region) 
        {
            $regionData[$region->ID] = $region->name;   
        }
        
        return $regionData;
        
    }

    /**
     * Get all region for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllRegionDownload()
    {
        // Set default region data
        $regionData = [];

        $regions = $this->region->getAll();

        foreach ($regions as $key => $region) 
        {
            $regionData[$region->ID] = $region->name;   
        }
        
        return $regionData;
        
    }

    /**
     * Generate tree region data
     */
    private function _getRegionData()
    {
        // Get all region data
        $branches = $this->branch->getAll();
        
        $dataBranch = [];
        
        //get branch first, compile
        foreach($branches as $branch)
        {
            $key = $branch->region_ID;
            
            if (!array_key_exists($key, $dataBranch))
            {
                $dataBranch[$key] = [];
            }
            
            $dataBranch[$key][] = (int) $branch->ID;
        }


        $dealerAccounts = $this->dealer_account->getAll();

        $data= [];
        //check branch ID from dataBranch
        foreach($dealerAccounts as $dealerAccount)
        {
            foreach ($dataBranch as $key => $value) {
                if(in_array($dealerAccount->branch_ID, $value))
                {
                    $key = 'region-'.$key;

                    $data[$key][] = (int) $dealerAccount->ID;
                }
            }
        }



        return $data;
    }

    /**
     * Get all branch for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllBranch()
    {
        // Set default branch data
        $branchData = [
            0 => 'All',
        ];

        $branches = $this->branch->getAll();

        foreach ($branches as $key => $branch) 
        {
            $branchData[$branch->ID] = $branch->name;   
        }
        
        return $branchData;
    }

    /**
     * Get all dealer account for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerAccount()
    {
        // Set default dealer_account data
        $dealerAccountData = [
            0 => 'All',
            -1 => 'None',
        ];

        $dealerAccounts = $this->dealer_account->getAll();

        foreach ($dealerAccounts as $key => $dealer_account) 
        {
            $dealerAccountData[$dealer_account->ID] = $dealer_account->name;   
        }
        
        return $dealerAccountData;
        
    }


    /**
     * Get all dealer for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllDealer()
    {
        // Set default dealer data
        $dealerData = [
            0 => 'All',
            -1 => 'None',
        ];

        $dealers = $this->dealer->getAll();

        foreach ($dealers as $key => $dealer) 
        {
            $dealerData[$dealer->ID] = $dealer->name;   
        }
        
        return $dealerData;
        
    }

    /**
     * Generate tree dealer data
     */
    private function _getDealerData()
    {   
        // Get all dealer account data
        $dealers = $this->dealer->getAll();

        $data = [];

        foreach($dealers as $dealer)
        {
            $key = 'branch-'.$dealer->branch_ID.'-channel-'.$dealer->dealer_channel_ID;
            
            if ($dealer->dealer_account_ID > 0)
            {
                $key .= '-account-'.$dealer->dealer_account_ID;
            }
            
            if (!array_key_exists($key, $data))
            {
                $data[$key] = [];
            }
            
            $data[$key][] = $dealer->ID;
        }
        
        return $data;
    }

    /**
     * Generate tree branch data
     */
    private function _getBranchData()
    {   
        // Get all branch data
        $branches = $this->branch->getAll();

        $data = [];

        foreach($branches as $branch)
        {
            $key = 'region-'.$branch->region_ID;

            if (!array_key_exists($key, $data))
            {
                $data[$key] = [];
            }
            
            $data[$key][] = $branch->ID;
        }
        
        return $data;
    }

    /**
     * Get all dealer channel for user input data
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerChannel()
    {
        // Set default dealer_channel data
        $dealerAccountChannel = [
            0 => 'All',
        ];

        $dealerChannels = $this->dealer_channel->getAll();

        foreach ($dealerChannels as $key => $dealerChannel) 
        {
            $dealerAccountChannel[$dealerChannel->ID] = $dealerChannel->name;   
        }
        
        return $dealerAccountChannel;
        
    }

    /**
     * Generate tree dealer data
     */
    private function _getDealerChannelData()
    {   
        // Get all dealer account data
        $dealers = $this->dealer->getAll();

        $data = [];

        foreach($dealers as $dealer)
        {
            if ($dealer->dealer_account_ID > 0)
            {
                $key = 'branch-'.$dealer->branch_ID.'-channel-'.$dealer->dealer_channel_ID;

                if (!array_key_exists($key, $data))
                {
                    $data[$key] = [];
                }

                if(!in_array($dealer->dealer_account_ID, $data[$key]))
                {
                    $data[$key][] = (int) $dealer->dealer_account_ID;
                }
                
            }

        }
        
        return $data;
    }
    
    /**
     * Get all competitor brands
     */
    private function _getAllCompetitorBrand()
    {
        $brands = $this->competitorBrand->getAll();
        $data   = [];
        
        foreach ($brands as $brand)
        {
            $data[$brand->ID] = $brand->name;
        }
        
        $data[0] = 'Others';
        
        return $data;
    }
    
    /**
     * Generate month, quarters, semesters and years of report timespan
     *
     * @access private
     * @return Array
     */
    private function _generateTime()
    {
        // Get date
        $timeFirst  = strtotime($this->data->getFirstReportDate());
        $timeNow    = time();
        
        // Generate month
        $monthFirst = strtotime(date('Y-m', $timeFirst));
        $monthLast  = strtotime(date('Y-m'));
        
        $data = [
            'months' => [],
            'quarters' => [],
            'semesters' => [],
            'years' => []
        ];
        
        while ($monthLast > $monthFirst)
        {
            $monthValue = date('m', $monthLast);
            $yearValue  = date('Y', $monthLast);
            $month      = $yearValue.'-'.$monthValue;
            
            if ($monthValue === '03')
            {
                $data['quarters'][($yearValue.'-Q1')]   = $yearValue.' Q1';
            }
            else if ($monthValue === '06')
            {
                $data['quarters'][($yearValue.'-Q2')]   = $yearValue.' Q2';
                $data['semesters'][($yearValue.'-S1')]  = $yearValue.' S1';
            }
            else if ($monthValue === '09')
            {
                $data['quarters'][($yearValue.'-Q3')]   = $yearValue.' Q3';
            }
            else if ($monthValue === '12')
            {
                $data['quarters'][($yearValue.'-Q4')]   = $yearValue.' Q4';
                $data['semesters'][($yearValue.'-S2')]  = $yearValue.' S2';
            }
            
            // Push data to container
            $data['months'][$month] = date('F Y', $monthLast);
            
            if (!array_key_exists($yearValue, $data['years']))
            {
                $data['years'][$yearValue] = $yearValue;
            }
            
            $days = 30;
            
            // For february 28 days
            if ($monthValue == '03') 
            {
                $days = 28;
            }
            
            // Decrement month
            $monthLast -= 3600*24*$days;
        }
        
        return $data;
    }

    /**
     * Rounding to million
     *
     * @access private
     * @param Integer $value
     * @return Integer
     */
    private function _round($value)
    {
        return round((int) $value/1000000, 0, PHP_ROUND_HALF_DOWN);
    }
    
    /**
     * Validate code to dashboard account data
     *
     * @access public
     * @param String $code
     * @return Response
     */
    private function _validateCode($code)
    {
        if (env('APP_ENV') === 'local')
        {
            return true;
        }
        
        if (!$code)
        {
            return false;
        }
        
        // Check code
        $tokenData = $this->token->getByToken($code);
        
        if (!$tokenData)
        {
            return false;
        }
        
        $account = $this->account->getOne($tokenData->dashboard_account_ID);
        
        if (!$account)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Render index page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Validate code
        $code = $request->session()->get('code', false);
        $regionID = $request->get('regionID', false);
        
        if (!$this->_validateCode($code))
        {
            return redirect('/dashboard/login');
        }
        
        // Get account data
        $accounts = $this->data->getAccount();
        $accountFiltered = [];
        
        foreach ($accounts as $account)
        {
            if (!array_key_exists($account->ID, $accountFiltered))
            {
                $accountFiltered[$account->ID] = $account->name;
            }
        }

        $data = [
            'code'                  => $code,
            'time'                  => $this->_generateTime(),
            'accounts'              => $accountFiltered,
            'accountAllNation'      => $this->accountAllNation,
            'regions'               => $this->_getAllRegion(),
            'regionsDownload'       => $this->_getAllRegionDownload(),
            'branches'              => $this->_getAllBranch(),
            'branchData'            => json_encode($this->_getBranchData()),
            'dealerAccounts'        => $this->_getAllDealerAccount(),
            'dealerData'            => json_encode($this->_getDealerData()),
            'dealers'               => $this->_getAllDealer(),
            'dealerChannel'         => $this->_getAllDealerChannel(),
            'dealerChannelData'     => json_encode($this->_getDealerChannelData()),
            'brands'                => $this->_getAllCompetitorBrand()
        ];

        // Render dashboard
        return view('dashboard.index', $data);
    }
    
    /**
     * Render login page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function loginRender(Request $request)
    {
        return view('dashboard.login');
    }
    
    /**
     * Handle login request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function loginHandle(Request $request)
    {
        $email = $request->get('email', false);
        
        if (!$email)
        {
            $request->session()->flash('error', '');
            return redirect('/dashboard/login');
        }
        
        // Check account by email
        $account = $this->account->getByEmail($email);
        
        if (!$account)
        {
            $request->session()->flash('error', '');
            return redirect('/dashboard/login');
        }
        
        // Set code
        $code = str_random(32);
        
        // Save access key
        $this->token->create($account->ID, $code);
        
        // Set email data
        $emailData = [
            'link' => env('APP_HOME_URL').'/dashboard/auth/'.$code
        ];
        
        Mail::send('dashboard.email', $emailData, function ($m) use ($account) 
        {
            $m->from('havas.jakarta@havas.com', 'Havas Jakarta');
            $m->to($account->email, $account->name)->subject('Panasonic Report Dashboard Access');
        });
        
        // Send email
        $request->session()->flash('finish', '');
        return redirect('/dashboard/login');
    }
    
    /**
     * Handle authentication code from login
     * 
     * @access public
     * @param String $code
     * @return Void
     */
    public function loginAuth($code, Request $request)
    {
        // Check code
        $tokenData = $this->token->getByToken($code);
        
        if (!$tokenData)
        {
            return redirect('/dashboard/login');
        }
        
        // Check time
        if ((time() - (int) $tokenData->created) > 3600)
        {
            $this->token->remove($tokenData->ID);
            
            $request->session()->flash('expired', '');
            
            return redirect('/dashboard/login');
        }
        
        // Get account based on token data
        $account = $this->account->getOne($tokenData->dashboard_account_ID);
        
        if (!$account)
        {
            return redirect('/dashboard/login');
        }
        
        // Update last access of dashboard account
        $this->account->update($account->ID, ['last_access' => time()]);
        
        // Attach flash session 
        $request->session()->flash('code', $code);
        return redirect(env('APP_HOME_URL').'/dashboard');
    }
    
    /**
     * Get all account data
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function data($type, Request $request)
    {
        if (!$request->ajax())
        {
            return App::abort(404);
        }
        
        // Get code
        $code = $request->get('code', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }


        if ($type === 'explore')
        {
            // Set initial parameter
            $params = [];

            //set initial time 
            $semester = [
                'S1' => ['01', '02', '03', '04', '05', '06'],
                'S2' => ['07', '08', '09', '10', '11', '12'],
            ];

            $quarter = [
                'Q1' => ['01', '02', '03'],
                'Q2' => ['04', '05', '06'],
                'Q3' => ['07', '08', '09'],
                'Q4' => ['10', '11', '12'],
            ];
            
            // Set initial parameter
            $targetMonths   = [];
            $startDate      = date('Y-m').'-01';
            $finishDate     = date('Y-m').'-31';
            
            $type = $request->get('timeType');
            
            if (in_array($type, ['semester','quarter']))
            {
                // Collect month
                $time   = $request->get('timeValue');
                $time   = explode('-', $time);
                $year   = $time[0];
                $month  = $time[1];
            }

            if ($type === 'semester')
            {
                $startDate = $year.'-'.$semester[$month][0].'-01';
                $finishDate = $year.'-'.$semester[$month][5].'-31';
                
                foreach($semester[$month] as $item)
                {
                    $targetMonths[] = $year.'-'.$item;
                }
            }        
            else if ($type === 'quarter')
            {
                $startDate = $year.'-'.$quarter[$month][0].'-01';
                $finishDate = $year.'-'.$quarter[$month][2].'-31';
                
                foreach($quarter[$month] as $item)
                {
                    $targetMonths[] = $year.'-'.$item;
                }
            }
            else if ($type === 'month')
            {
                $monthValue    =  $request->get('timeValue', date('Y-m'));
                
                $startDate = $monthValue.'-01';
                $finishDate = $monthValue.'-31';
                
                $targetMonths[] = $monthValue;
            }
            else if ($type === 'year')
            {
                $startDate = date('Y').'-01-01';
                $finishDate = date('Y').'-12-31';
            }
            
            // Get data based on branch id
            $params = [];
            
            if ($request->has('branchID'))
            {
                $params['branch_ID'] = $request->get('branchID');
            }
            
            // Get data based on account id
            if ($request->has('accountID'))
            {
                $params['account_ID'] = $request->get('accountID');
            }

            // Get data based on dealer id
            if ($request->has('dealerID'))
            {   
                $dealerID = $request->get('dealerID');

                if ($dealerID != 0)
                {
                    $params['dealer_ID'] = $request->get('dealerID');
                }
            }

            // Get data based on channel id
            if ($request->has('channelID'))
            {
                $params['dealer_channel_ID'] = $request->get('channelID');
            }

            // Fetch data
            $dataSales          = $this->data->dataExploreSales($startDate, $finishDate, $params);
            $dataTarget         = $this->data->dataExploreTarget($targetMonths);
            $dataSalesPromotor  = $this->data->dataExploreSalesPromotor($startDate, $finishDate, $params);
            $dataTargetPromotor = $this->data->dataExploreTargetPromotor($targetMonths);
            
            // Data container
            $branches   = [];
            $accounts   = [];
            $dealers    = [];
            $promotors  = [];
            
            $dealerTarget   = [];
            $promotorTarget = [];
            
            // Compiled data target
            foreach ($dataTarget as $target)
            {
                $dealerTarget[$target->dealer_ID] = $target->total;
            }
            
            // Compiled sales data
            foreach ($dataSales as $sales) 
            {
                // Push branch
                if (!array_key_exists($sales->branch_ID, $branches))
                {
                    $branches[$sales->branch_ID] = [
                        'name'      => $sales->branch_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'accounts'  => [],
                        'dealers'   => [],
                    ];
                }
                
                $branches[$sales->branch_ID]['sales'] += $sales->total;
                
                // Push account
                if (
                    $sales->account_ID !== null &&
                    !array_key_exists($sales->account_ID, $accounts)
                )
                {
                    $accounts[$sales->account_ID] = [
                        'name'      => $sales->account_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'branch_ID' => $sales->branch_ID,
                        'dealers'   => [],
                    ];
                }
                
                if (array_key_exists($sales->account_ID, $accounts))
                {
                    $accounts[$sales->account_ID]['sales'] += $sales->total;
                }
                
                // Push dealers
                if (!array_key_exists($sales->dealer_ID, $dealers))
                {
                    $dealers[$sales->dealer_ID] = [
                        'name'      => $sales->dealer_name,
                        'sales'     => 0,
                        'target'    => 0,
                        'count'     => 0,
                        'account_ID'=> $sales->account_ID,
                        'branch_ID' => $sales->branch_ID,
                        'promotors' => [],
                    ];
                }
                
                $dealers[$sales->dealer_ID]['sales'] += $sales->total;
                
                if (array_key_exists($sales->dealer_ID, $dealerTarget))
                {
                    $dealers[$sales->dealer_ID]['target'] = $dealerTarget[$sales->dealer_ID];
                }
                
            }
            
            // Compiled target promotor
            foreach ($dataTargetPromotor as $target)
            {
                $promotorTarget[$target->promotor_ID] = $target->total;
            }
            
            // Compiled data promotor
            foreach ($dataSalesPromotor as $sales)
            {
                $target = 0;
                
                if (array_key_exists($sales->promotor_ID, $promotorTarget))
                {
                    $target = $promotorTarget[$sales->promotor_ID];
                }
                
                $dealers[$sales->dealer_ID]['promotors'][] = [
                    'name'      => $sales->promotor_name,
                    'sales'     => $sales->total,
                    'target'    => $target
                ];
                
                $dealers[$sales->dealer_ID]['count']++;
            }
            
            // Push dealer to branch or account
            foreach ($dealers as $ID => $dealer)
            {
                if ($dealer['account_ID'] !== null)
                {
                    $accounts[$dealer['account_ID']]['dealers'][] = $dealer;
                    $accounts[$dealer['account_ID']]['count'] += $dealer['count'];
                    $accounts[$dealer['account_ID']]['target'] += $dealer['target'];
                }
                else
                {
                    $branches[$dealer['branch_ID']]['dealers'][] = $dealer;
                    $branches[$dealer['branch_ID']]['count'] += $dealer['count'];
                    $branches[$dealer['branch_ID']]['target'] += $dealer['target'];
                }
            }
            
            // Push account to branch
            foreach ($accounts as $ID => $account)
            {
                $branches[$account['branch_ID']]['accounts'][] = $account;
                $branches[$account['branch_ID']]['count'] += $account['count'];
                $branches[$account['branch_ID']]['target'] += $account['target'];
            }
            
            $html = '<thead><tr><th>Branch</th><th>Account</th><th>Dealer</th><th>Promotor</th></tr></thead><tbody>';
            
            foreach ($branches as $branch) 
            {   
                $colspan = 1;
                $branchBackground = '#C5EFF7';
                
                if (count($branch['dealers']) > 0)
                {
                    $colspan = 2;
                }
                
                $branch_achievement = 100;
                
                if ($branch['target'] != 0)
                {
                    $branch_achievement = round(($branch['sales']/$branch['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                }
                
                if ($branch_achievement < 100)
                {
                    $branchBackground = '#f9dad6';
                }

                $html .= '<tr>';
                $html .= '
                    <td style="background:'.$branchBackground.'" rowspan="'.$branch['count'].'" colspan="'.$colspan.'">'.
                        '<strong>'.$branch['name'].'</strong>'.
                        '<br />Sales: Rp.'.number_format($branch['sales']).
                        '<br />Target: Rp.'.number_format($branch['target']).
                        '<br />Achievement: '.$branch_achievement.'%'.
                    '</td>';
                
                if (count($branch['accounts']) > 0)
                {
                    foreach ($branch['accounts'] as $key_account => $account) 
                    {
                        
                        $accountBackground = '#C5EFF7';
                        
                        if ($key_account > 0)
                        {
                            $html .= '<tr>';
                        }
                        
                        $account_achievement = 100;

                        if ($account['target'] !== 0)
                        {
                            $account_achievement = round(($account['sales']/$account['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        if ($account_achievement < 100)
                        {
                            $accountBackground = '#f9dad6';
                        }

                        $html .= '
                            <td style="background:'.$accountBackground.'" rowspan="'.$account['count'].'">'.
                                '<strong>'.$account['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($account['sales']).
                                '<br />Target: Rp.'.number_format($account['target']).
                                '<br />Achievement: '.$account_achievement.'%'.
                            '</td>';
                        
                        foreach ($account['dealers'] as $key_dealer => $dealer) 
                        {
                            
                            $dealerBackground = '#C5EFF7';
                            
                            if ($key_dealer > 0)
                            {
                                $html .= '<tr>';
                            }
                        
                            $dealer_achievement = 100;

                            if ($dealer['target'] != 0)
                            {
                                $dealer_achievement = round(($dealer['sales']/$dealer['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                            }
                        
                            if ($dealer_achievement < 100)
                            {
                                $dealerBackground = '#f9dad6';
                            }
                            
                            $html .= '
                                <td style="background:'.$dealerBackground.'" rowspan="'.$dealer['count'].'">'.
                                    '<strong>'.$dealer['name'].'</strong>'.
                                    '<br />Sales: Rp.'.number_format($dealer['sales']).
                                    '<br />Target: Rp.'.number_format($dealer['target']).
                                    '<br />Achievement: '.$dealer_achievement.'%'.
                                '</td>';
                            
                            foreach ($dealer['promotors'] as $key_promotor => $promotor)
                            {
                                $promotorBackground = '#C5EFF7';
                            
                                if ($key_promotor > 0)
                                {
                                    $html .= '<tr>';
                                }

                                $promotor_achievement = 100;
                                
                                if ($promotor['target'] != 0)
                                {
                                    $promotor_achievement = round(($promotor['sales']/$promotor['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                                }
                                
                                if ($promotor_achievement < 100)
                                {
                                    $promotorBackground = '#f9dad6';
                                }

                                $html .= '
                                <td style="background:'.$dealerBackground.'">'.
                                    '<strong>'.$promotor['name'].'</strong>'.
                                    '<br />Sales: Rp.'.number_format($promotor['sales']).
                                    '<br />Target: Rp.'.number_format($promotor['target']).
                                    '<br />Achievement: '.$promotor_achievement.'%'.
                                '</td>';

                                $html .= '</tr>';

                            }
                        }
                        
                    }
                }
                else
                {
                    foreach ($branch['dealers'] as $key_dealer => $dealer) 
                    {
                        $dealerBackground = '#C5EFF7';
                            
                        if ($key_dealer > 0)
                        {
                            $html .= '<tr>';
                        }
                        
                        $dealer_achievement = 100;

                        if ($dealer['target'] != 0)
                        {
                            $dealer_achievement = round(($dealer['sales']/$dealer['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        if ($dealer_achievement < 100)
                        {
                            $dealerBackground = '#f9dad6';
                        }

                        $html .= '
                            <td style="background:'.$dealerBackground.'" rowspan="'.$dealer['count'].'">'.
                                '<strong>'.$dealer['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($dealer['sales']).
                                '<br />Target: Rp.'.number_format($dealer['target']).
                                '<br />Achievement: '.$dealer_achievement.'%'.
                            '</td>';
                        
                        foreach ($dealer['promotors'] as $key_promotor => $promotor)
                        {
                            $promotorBackground = '#C5EFF7';
                            
                            if ($key_promotor > 0)
                            {
                                $html .= '<tr>';
                            }
                            
                            $promotor_achievement = 100;
                            
                            if ($promotor['target'] != 0)
                            {
                                $promotor_achievement = round(($promotor['sales']/$promotor['target'])*100, 2, PHP_ROUND_HALF_DOWN);
                            }
                            
                            if ($promotor_achievement < 100)
                            {
                                $promotorBackground = '#f9dad6';
                            }

                            $html .= '
                            <td style="background:'.$dealerBackground.'">'.
                                '<strong>'.$promotor['name'].'</strong>'.
                                '<br />Sales: Rp.'.number_format($promotor['sales']).
                                '<br />Target: Rp.'.number_format($promotor['target']).
                                '<br />Achievement: '.$promotor_achievement.'%'.
                            '</td>';
                            
                            $html .= '</tr>';
                        
                        }
                        
                    }
                }
            }
            
            $html .= '</tbody>';
            
            $data = [
                'sales'          => $html,
            ];

        }

        if ($type === 'gender')
        {

            $gender = $this->data->dataPromoterGender();

            $totalGender = [
                'male'              => 0,
                'female'            => 0,
                'total'             => count($gender),
                'persentase_male'   => 0,
                'persentase_female' => 0,
            ];

            $compiledGender = [];

            foreach ($gender as $key => $value) {

                if(!array_key_exists($value->name, $compiledGender))
                {
                    $compiledGender[$value->name]  =  [
                        'female'            => 0,
                        'male'              => 0,
                        'total'             => 0,
                        'persentase_male'   => 0,
                        'persentase_female' => 0,
                    ];
                }

                if($value->gender == 'male')
                {
                    $totalGender['male']++;
                    $compiledGender[$value->name]['male']++;
                }else
                {
                    $totalGender['female']++;
                    $compiledGender[$value->name]['female']++;
                }
                
                $compiledGender[$value->name]['total']++;
                
            }

            foreach ($compiledGender as $key => $value) {
                $compiledGender[$key]['persentase_male'] = round($value['male']/$value['total'] * 100 , 2, PHP_ROUND_HALF_DOWN);
                $compiledGender[$key]['persentase_female'] = round($value['female']/$value['total'] * 100, 2, PHP_ROUND_HALF_DOWN);
            }

            $totalGender['persentase_male'] = round(($totalGender['male']/$totalGender['total']) * 100, 2, PHP_ROUND_HALF_DOWN);
            $totalGender['persentase_female'] = round(($totalGender['female']/$totalGender['total']) * 100, 2, PHP_ROUND_HALF_DOWN);

            $data = [
                'gender'          => $totalGender,
                'genderBranch'   => $compiledGender,
            ];
        }


        return response()->json($data);
    }
    
    /**
     * Get data chart
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function chart(Request $request)
    {
        if (!$request->ajax())
        {
            return App::abort(404);
        }
        
        // Get code
        $code = $request->get('code', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }

        //set initial time 
        $semester = [
            'S1' => ['01', '02', '03', '04', '05', '06'],
            'S2' => ['07', '08', '09', '10', '11', '12'],
        ];

        $quarter = [
            'Q1' => ['01', '02', '03'],
            'Q2' => ['04', '05', '06'],
            'Q3' => ['07', '08', '09'],
            'Q4' => ['10', '11', '12'],
        ];
        
        // Set initial parameter
        $params = [];
        $params['targetMonths'] = [];
        $params['startDate']    = date('Y-m').'-01';
        $params['finishDate']   = date('Y-m').'-31';
        
        $type = $request->get('timeType');
        
        if ( in_array($type, ['semester','quarter']) )
        {
            // Collect month
            $time   = $request->get('timeValue');
            $time   = explode('-', $time);
            $year   = $time[0];
            $month  = $time[1];
        }

        if ($type === 'semester')
        {
            $params['startDate'] = $year.'-'.$semester[$month][0].'-01';
            $params['finishDate'] = $year.'-'.$semester[$month][5].'-31';
            
            foreach($semester[$month] as $item)
            {
                $params['targetMonths'][] = $year.'-'.$item;
            }
        }        
        else if ($type === 'quarter')
        {
            $params['startDate'] = $year.'-'.$quarter[$month][0].'-01';
            $params['finishDate'] = $year.'-'.$quarter[$month][2].'-31';
            
            foreach($quarter[$month] as $item)
            {
                $params['targetMonths'][] = $year.'-'.$item;
            }
        }
        else if ($type === 'month')
        {
            $monthValue    =  $request->get('timeValue', date('Y-m'));
            
            $params['startDate'] = $monthValue.'-01';
            $params['finishDate'] = $monthValue.'-31';
            
            $params['targetMonths'][] = $monthValue;
        }
        else if($type === 'year')
        {
            $params['startDate'] = date('Y').'-01-01';
            $params['finishDate'] = date('Y').'-12-31';
        }


        // Get data based on region id
        if ($request->has('regionID'))
        {
            $params['region_ID'] = $request->get('regionID');
        }

        // Get data based on branch id
        if ($request->has('branchID'))
        {
            $params['branch_ID'] = $request->get('branchID');
        }

        // Get data based on account id
        if ($request->has('accountID'))
        {
            $params['account_ID'] = $request->get('accountID');
        }
        
        // Get data based on dealer id
        if ($request->has('dealerID'))
        {   
            $dealerID = $request->get('dealerID');
            
            if ($dealerID != 0)
            {
                $params['dealer_ID'] = $request->get('dealerID');
            }
        }

        // Get data based on channel id
        if ($request->has('channelID'))
        {
            $params['dealer_channel_ID'] = $request->get('channelID');
        }

        // Compile sales trend Data
        $salesTarget    = $this->_round($this->data->chartSalesTarget($params));
        $salesTrend     = $this->data->chartSalesTrend($params);
        $salesProduct   = $this->data->chartSalesProduct($params);
        $salesChannel   = $this->data->chartSalesChannel($params);
        
        $compiledSalesTrend = [];
        
        foreach ($salesTrend as $item)
        {
            $compiledSalesTrend[] = [
                'label'     => date('j F Y', strtotime($item->date)),
                'total'     => (int) $item->total,
                'target'    => $salesTarget
            ];
        }
        
        // Set initial data
        $data = [
            'salesTrend'    => $compiledSalesTrend,
            'salesAccount'  => $this->data->chartSalesAccount($params),
            'salesProduct'  => $salesProduct,
            'salesChannel'  => $salesChannel,
        ];

        
        return response()->json($data);
    }

    /**
     * Get all empty stock
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function stock(Request $request)
    {
        if (!$request->ajax())
        {
            return App::abort(404);
        }
        
        // Get code
        $code = $request->get('code', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        $dataStock = $this->data->stockGet();
            
        $compiledData = [];

        foreach ($dataStock as $key => $value) 
        {
            if(!array_key_exists($value->branch_name, $compiledData))
            {
                $compiledData[$value->branch_name] = [];
            }

            if(!array_key_exists($value->dealer_name, $compiledData[$value->branch_name]))
            {
                $compiledData[$value->branch_name][$value->dealer_name] = [];
            }

            $compiledData[$value->branch_name][$value->dealer_name][] = $value->product_name;

        }

        return response()->json($compiledData);
    }

    /**
     * Get 5 product frp, category
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function product(Request $request)
    {
        if (!$request->ajax())
        {
            return App::abort(404);
        }
        
        // Get code
        $code = $request->get('code', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }

        $category_ID = $request->get('category_ID', false);
        
        if (!$category_ID)
        {
            return App::abort(404);
        }
        
        //set initial time 
        $semester = [
            'S1' => ['01', '02', '03', '04', '05', '06'],
            'S2' => ['07', '08', '09', '10', '11', '12'],
        ];

        $quarter = [
            'Q1' => ['01', '02', '03'],
            'Q2' => ['04', '05', '06'],
            'Q3' => ['07', '08', '09'],
            'Q4' => ['10', '11', '12'],
        ];
        
        // Set initial parameter
        $params = [];
        $params['startDate']    = date('Y-m').'-01';
        $params['finishDate']   = date('Y-m').'-31';
        
        $type = $request->get('timeType');
        
        if ( in_array($type, ['semester','quarter']) )
        {
            // Collect month
            $time   = $request->get('timeValue');
            $time   = explode('-', $time);
            $year   = $time[0];
            $month  = $time[1];
        }

        if ($type === 'semester')
        {
            $params['startDate'] = $year.'-'.$semester[$month][0].'-01';
            $params['finishDate'] = $year.'-'.$semester[$month][5].'-31';
        }        
        else if ($type === 'quarter')
        {
            $params['startDate'] = $year.'-'.$quarter[$month][0].'-01';
            $params['finishDate'] = $year.'-'.$quarter[$month][2].'-31';
        }
        else if ($type === 'month')
        {
            $monthValue    =  $request->get('timeValue', date('Y-m'));
            
            $params['startDate'] = $monthValue.'-01';
            $params['finishDate'] = $monthValue.'-31';
        }
        else if($type === 'year')
        {
            $params['startDate'] = date('Y').'-01-01';
            $params['finishDate'] = date('Y').'-12-31';
        }
        
        // Get data base on category id
        if ($request->has('category_ID'))
        {
            $params['category_ID'] = $request->get('category_ID');
        }

        // Get data based on region id
        if ($request->has('regionID'))
        {
            $params['region_ID'] = $request->get('regionID');
        }

        // Get data based on branch id
        if ($request->has('branchID'))
        {
            $params['branch_ID'] = $request->get('branchID');
        }

        // Get data based on account id
        if ($request->has('accountID'))
        {
            $params['account_ID'] = $request->get('accountID');
        }
        
        // Get data based on dealer id
        if ($request->has('dealerID'))
        {   
            $dealerID = $request->get('dealerID');
            if($dealerID != 0)
            {
                $params['dealer_ID'] = $request->get('dealerID');
            }
        }

        // Get data based on channel id
        if ($request->has('channelID'))
        {
            $params['dealer_channel_ID'] = $request->get('channelID');
        }
        
        // Compile sales trend montly
        $dataProduct = $this->data->productGet($params);

        return response()->json($dataProduct);
    }
    
    /**
     * Handle download report
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function downloadReport(Request $request)
    {   
        // Get code
        $code = $request->get('code', false);
        $type = $request->get('type', false);
        
        if (!$type)
        {
            return App::abort(404);
        }
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        // Set initial parameter
        $params = [];
        $name   = 'All branch';
        
        // Collect month
        if ($request->has('month'))
        {
            $params['month'] = $request->get('month', date('Y-m'));
        }
        
        // Get spreadsheet name
        if ($type === 'branch')
        {
            $params['branch_ID'] = $request->get('branchID' , 0);
            
            // Get branch ID
            $branch = $this->branch->getOne($params['branch_ID']);

            if (!$branch)
            {
                return App::abort(404);
            }

            $name = $branch->name;
        }
        else if ($type === 'account')
        {
            $params['account_ID'] = $request->get('accountID', 0);
            
            $account = $this->dealer_account->getOne($params['account_ID']);
            
            if (!$account)
            {
                return App::abort(404);
            }
            
            $name = $account->name;
        }
        else if ($type === 'account-all')
        {
            $params['account_name'] = $request->get('accountID', '');
            
            if (!$params['account_name'])
            {
                return App::abort(404);
            }
            
            $name = ucwords(str_replace('-', ' ', $params['account_name'])).' - All Nation';
        }

        $objPHPExcel = new PHPExcel();

        // Set aplhabet for looping the column
        $alphabet = range('A', 'Z');
        $time = $request->get('month', date('01-m-Y'));
        
        // Set title
        $title = ' - '.$name.' - '.date('F Y',strtotime($time));
        
        // Set currency code
        $currencyCode = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)';

        // Set sheet name
        $objPHPExcel->getActiveSheet()->setTitle('Sell Out');

        // Set title in sheet 1
        $objPHPExcel->getActiveSheet()->setCellValue('a2', 'Panasonic Promoter'.$title);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle("a2")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->mergeCells('a2:r2');


        $columnTitle = [];
        $columnTitle[1] = "Branch";
        $columnTitle[2] = "Team Leader";
        $columnTitle[3] = "Periods";
        $columnTitle[4] = "Week";
        $columnTitle[5] = "Date";
        $columnTitle[6] = "Cust. Name";
        $columnTitle[7] = "Address";
        $columnTitle[8] = "Cust Phone No.";
        $columnTitle[9] = "Promotors Name";
        $columnTitle[10] = "Promotors Employee No.";
        $columnTitle[11] = "Promotors Join Date";
        $columnTitle[12] = "Dealers Name.";
        $columnTitle[13] = "Store Name";
        $columnTitle[14] = "Product Category 2";
        $columnTitle[15] = "Model Name Promotors";
        $columnTitle[16] = "Model Name PGI";
        $columnTitle[17] = "Qty";
        $columnTitle[18] = "Selling Price Promotors";
        $columnTitle[19] = "Total";

        $row = 3; // 1-based index
        $col = 0;

        // Add array to sheet
        foreach ($columnTitle as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }

        // Style 
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFF00')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')

                 )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Set style
        $objPHPExcel->getActiveSheet()->getStyle("A3:".$alphabet[count($columnTitle)-1]."3")->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(40);

        // Data shhet 1
        $data = $this->data->report($params);

        $row = 4; // 1-based index

        // Add array to sheet
        foreach ($data as $key => $value) {
            
            // Account all nation fallback
            if ($type === 'account-all')
            {
                $value->dealer_account_name = ucwords(str_replace('-', ' ', $params['account_name']));
            }
            
            // Generate week number
            $weekNumber = Carbon::parse($value->date_string)->weekOfMonth;
            
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $value->team_leader);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $value->period);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $weekNumber);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $value->date_string);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $value->promotor_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $value->promotor_ID);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $value->dealer_account_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $value->dealer_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $value->category_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $value->product_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $value->qty);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $value->price);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $value->qty*$value->price);
            $row++;
        }

        // Set Width Auto
        foreach ($alphabet as $key => $value) 
        {
            if($key <= count($columnTitle)-1)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
            }
        }



        /*===============================================
         *
         * Data Pivot Sheet Sheet
         *
         *===============================================*/
        
        // Sheet 2
        $objPHPExcel->createSheet();

        // Move to new Sheet
        $objPHPExcel->setActiveSheetIndex(1);
        
        $activeSheet = $objPHPExcel->getActiveSheet();
        
        // Rename 2nd sheet
        $activeSheet->setTitle('Pivot Table');

        // Set Title Sheet 
        $activeSheet->setCellValue('a2', 'Store Data'.$title);
        $activeSheet->getStyle("a2")->getFont()->setSize(18);
        $activeSheet->getStyle("a2")->getFont()->setBold(true);
        $activeSheet->mergeCells('a2:f2');

        // Name content 
        $columnTitlePivot = [];
        $columnTitlePivot[1] = "STORE";
        $columnTitlePivot[2] = "Sum Of Qty";
        $columnTitlePivot[3] = "Sum of Selling Price Promotor";
        $columnTitlePivot[4] = "Total Promotor";
        $columnTitlePivot[5] = "Target Promotor";
        $columnTitlePivot[6] = "Achievement (%)";

        $row = 3; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitlePivot as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }

        // Style sheet 2
        $stylePivot = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ff8080')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')

                 )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Apply Style
        $activeSheet->getStyle("A3:".$alphabet[count($columnTitlePivot)-1]."3")->applyFromArray($stylePivot);
        $activeSheet->getRowDimension('3')->setRowHeight(40);

        // Data Sheet 2
        $dataPivot = $this->data->reportPivot($params);
        $compiledPivotData = [];

        //target
        foreach ($dataPivot['target'] as $key => $value) {
            if(!array_key_exists($value->dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$value->dealer_name]['dealer_name'] = $value->dealer_name;
                $compiledPivotData[$value->dealer_name]['sum_qty'] = 0;
                $compiledPivotData[$value->dealer_name]['sum_selling_price'] = 0;
                $compiledPivotData[$value->dealer_name]['total_promotor'] = 0;
                $compiledPivotData[$value->dealer_name]['total_target'] = $value->total_target;
            }
        }

        //summary
        foreach ($dataPivot['summary'] as $key => $value) {
            if(array_key_exists($value->dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$value->dealer_name]['sum_qty'] = $value->sum_qty;
                $compiledPivotData[$value->dealer_name]['sum_selling_price'] = $value->sum_selling_price;
            }
        }

        //summary
        foreach ($dataPivot['promotor'] as $key => $value) {
            if(array_key_exists($value->dealer_name, $compiledPivotData))
            {
                $compiledPivotData[$value->dealer_name]['total_promotor'] = $value->total_promotor;
            }
        }
        $row = 4; // 1-based index

        // Add array to sheet
        foreach ($compiledPivotData as $key => $value) 
        {
            $activeSheet->setCellValue('A'.$row, $value['dealer_name']);
            $activeSheet->setCellValue('B'.$row, $value['sum_qty']);
            $activeSheet->setCellValue('C'.$row, $value['sum_selling_price']);
            $activeSheet->setCellValue('D'.$row, $value['total_promotor']);
            $activeSheet->setCellValue('E'.$row, $value['total_target']);
            $activeSheet->setCellValue('F'.$row,'=(C'.$row.'/E'.$row.')');
            
            // Accounting format
            $activeSheet->getStyle('C'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('E'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            // Percentage format
            $activeSheet->getStyle('F'.$row)
                        ->getNumberFormat()->applyFromArray( 
                            array( 
                                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                            )
                        );
            $row++;
        }

        $activeSheet->setCellValue('A'.$row, 'Grand Total');
        $activeSheet->setCellValue('B'.$row,'=SUM(B4:B'.($row-1).')');
        $activeSheet->setCellValue('C'.$row,'=SUM(C4:C'.($row-1).')');
        $activeSheet->setCellValue('D'.$row,'=SUM(D4:D'.($row-1).')');
        $activeSheet->setCellValue('E'.$row,'=SUM(E4:E'.($row-1).')');
        $activeSheet->setCellValue('F'.$row,'=(C'.$row.'/E'.$row.')');
        

        // Accounting format
        $activeSheet->getStyle('C'.$row)
            ->getNumberFormat()
            ->setFormatCode($currencyCode);

        $activeSheet->getStyle('E'.$row)
            ->getNumberFormat()
            ->setFormatCode($currencyCode);
        
        // Percentage format
        $activeSheet->getStyle('F'.$row)
                    ->getNumberFormat()->applyFromArray( 
                        array( 
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                    );
        
        $activeSheet->getStyle('A'.$row.':'.$alphabet[count($columnTitlePivot)-1].$row)->applyFromArray($stylePivot);
        
        $row++;


        // Set Width Auto
        foreach ($alphabet as $key => $value) {
            if($key <= count($columnTitlePivot)-1)
            {
                $activeSheet->getColumnDimension($value)->setAutoSize(true);
            }
        }


        /*===============================================
         *
         * Strategic Achievement Sheet
         *
         *===============================================*/
        
        // Sheet 3
        $objPHPExcel->createSheet();

        // Move to new Sheet
        $objPHPExcel->setActiveSheetIndex(2);
        
        $activeSheet = $objPHPExcel->getActiveSheet();
        
        // Rename 3rd sheet
        $activeSheet->setTitle('Strategic Achievement');

        // Set Title Sheet 
        $activeSheet->setCellValue('a2', 'Strategic Achievement'.$title);
        $activeSheet->getStyle("a2")->getFont()->setSize(18);
        $activeSheet->getStyle("a2")->getFont()->setBold(true);
        $activeSheet->mergeCells('a2:f2');
        $activeSheet->getDefaultColumnDimension()->setWidth(25);

        // Name content 
        $columnTitleAchievement2 = [];
        $columnTitleAchievement2[1] = "Name";
        $columnTitleAchievement2[2] = "Dealer";
        $columnTitleAchievement2[3] = "Week 1 (1-7)";
        $columnTitleAchievement2[4] = "";
        $columnTitleAchievement2[5] = "Week 2 (1-14)";
        $columnTitleAchievement2[6] = "";
        $columnTitleAchievement2[7] = "Week 3 (1-21)";
        $columnTitleAchievement2[8] = "";
        $columnTitleAchievement2[9] = "Week 4 (All)";
        $columnTitleAchievement2[10] = "";
        $columnTitleAchievement2[11] = "Target";

        $row2 = 3; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitleAchievement2 as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row2, $value);
            $col++;
        }

        // Name content 
        $columnTitleAchievement3 = [];
        $columnTitleAchievement3[1] = "";
        $columnTitleAchievement3[2] = "";
        $columnTitleAchievement3[3] = "Sales";
        $columnTitleAchievement3[4] = "Achievement (25%)";
        $columnTitleAchievement3[5] = "Sales";
        $columnTitleAchievement3[6] = "Achievement (50%)";
        $columnTitleAchievement3[7] = "Sales";
        $columnTitleAchievement3[8] = "Achievement (75%)";
        $columnTitleAchievement3[9] = "Sales";
        $columnTitleAchievement3[10] = "Achievement (100%)";
        $columnTitleAchievement3[11] = "";

        $row3 = 4; // 1-based index
        $col = 0;

        // Add data 
        foreach ($columnTitleAchievement3 as $key => $value) {
            $activeSheet->setCellValueByColumnAndRow($col, $row3, $value);
            $col++;
        }

        // Style sheet 2
        $stylePivot = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '92d050')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Apply Style
        $activeSheet->getStyle("A3:".$alphabet[count($columnTitleAchievement3)-1]."4")->applyFromArray($stylePivot);
        $activeSheet->mergeCells('A3:A4');
        $activeSheet->mergeCells('B3:B4');
        $activeSheet->mergeCells('C3:D3');
        $activeSheet->mergeCells('E3:F3');
        $activeSheet->mergeCells('G3:H3');
        $activeSheet->mergeCells('I3:J3');
        $activeSheet->mergeCells('K3:K4');

        // Add array to sheet
        $object = [];

        $dataTarget = $this->data->reportPromotorTarget($params);
        $objectTarget = [];
        
        // Target
        foreach ($dataTarget as $key => $value) 
        {
            $objectTarget[$value->promotor_ID] = $value->total_target;
        }
        
        foreach ($data as $key => $value) 
        {
            // Set initial value
            if (!array_key_exists($value->promotor_ID, $object))
            {
                $object[$value->promotor_ID] = [
                    "promotor"              => $value->promotor_name,
                    "dealer"                => $value->dealer_name,
                    "sales_week_1"          => 0,
                    "achievement_week_1"    => 0,
                    "sales_week_2"          => 0,
                    "achievement_week_2"    => 0,
                    "sales_week_3"          => 0,
                    "achievement_week_3"    => 0,
                    "sales_week_4"          => 0,
                    "achievement_week_4"    => 0,
                    "total_target"          => 0
                ];
            }
            
            // Set target
            if (
                $object[$value->promotor_ID]['total_target'] === 0 && 
                array_key_exists($value->promotor_ID, $objectTarget)
            ) {
                $object[$value->promotor_ID]['total_target'] = $objectTarget[$value->promotor_ID];
            }
            
            // Get date
            $sortdate = (int) date('j', strtotime($value->date));
            
            // First week
            if ($sortdate <= 7)
            {
                $object[$value->promotor_ID]['sales_week_1'] += ($value->price * $value->qty);
            }
            
            // Second week
            if ($sortdate <= 14)
            {
                $object[$value->promotor_ID]['sales_week_2'] += ($value->price * $value->qty);
            }
            
            // Third week
            if ($sortdate <= 21)
            {
                $object[$value->promotor_ID]['sales_week_3'] += ($value->price * $value->qty);
            }
            
            // All week
            $object[$value->promotor_ID]['sales_week_4'] += ($value->price * $value->qty);

        }
        
        $styleAchievement = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FAA8A1')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );
        
        // Style sheet 3 Achievement
        $styleAchievementSuccess = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'B4D5F8')
            )
        );
        
        
        $activeSheet = $activeSheet;
        $row = 5;
        
        // Add array to sheet
        foreach ($object as $key => $value) 
        {
            // Set achievement
            if ($value['total_target'] != 0)
            {
                $value['achievement_week_1'] = $value['sales_week_1']/$value['total_target'];
                $value['achievement_week_2'] = $value['sales_week_2']/$value['total_target'];
                $value['achievement_week_3'] = $value['sales_week_3']/$value['total_target'];
                $value['achievement_week_4'] = $value['sales_week_4']/$value['total_target'];
            }
            
            $activeSheet->setCellValue('A'.$row, $value['promotor']); 
            $activeSheet->setCellValue('B'.$row, $value['dealer']);
            $activeSheet->setCellValue('C'.$row, $value['sales_week_1']);
            $activeSheet->setCellValue('D'.$row, $value['achievement_week_1']);
            $activeSheet->setCellValue('E'.$row, $value['sales_week_2']);
            $activeSheet->setCellValue('F'.$row, $value['achievement_week_2']);
            $activeSheet->setCellValue('G'.$row, $value['sales_week_3']);
            $activeSheet->setCellValue('H'.$row, $value['achievement_week_3']);
            $activeSheet->setCellValue('I'.$row, $value['sales_week_4']);
            $activeSheet->setCellValue('J'.$row, $value['achievement_week_4']);
            $activeSheet->setCellValue('K'.$row, $value['total_target']);
            
            // Accounting format
            $activeSheet->getStyle('C'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('E'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('G'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('I'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            $activeSheet->getStyle('K'.$row)
                ->getNumberFormat()
                ->setFormatCode($currencyCode);
            
            // Percentage format
            $activeSheet->getStyle('D'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('F'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('H'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            $activeSheet->getStyle('J'.$row)
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            
            // Set style
            $activeSheet->getStyle('D'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('F'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('H'.$row)->applyFromArray($styleAchievement);
            $activeSheet->getStyle('J'.$row)->applyFromArray($styleAchievement);
            
            if ($value['achievement_week_1'] >= 0.25)
            {
                $activeSheet->getStyle('D'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_2'] >= 0.50)
            {
                $activeSheet->getStyle('F'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_3'] >= 0.75)
            {
                $activeSheet->getStyle('H'.$row)->applyFromArray($styleAchievementSuccess);
            }

            if ($value['achievement_week_4'] >= 1)
            {
                $activeSheet->getStyle('J'.$row)->applyFromArray($styleAchievementSuccess);
            }

            $row++;
        }
        
        // Apply border
        $borderStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                 )
            )
        );

        $activeSheet->getStyle("A3:K".($row-1))->applyFromArray($borderStyle);
        
        // Auto size column
        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);



        // Back to Sheet 1
        $objPHPExcel->setActiveSheetIndex(0);

        // Create Excel file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = 'Panasonic Report '.date('F Y',strtotime($time)).' - '.$name.'.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        return $objWriter->save('php://output');

    }
    
    /**
     * Display competitor report data table
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function competitorReport(Request $request)
    {
        // Get code
        $code       = $request->get('code', false);
        $type       = $request->get('type', false);
        $value      = $request->get('value', false);
        $brandID    = $request->get('brandID', false);
        
        if (!$this->_validateCode($code))
        {
            return App::abort(404);
        }
        
        if (!$type)
        {
            return App::abort(404);
        }
        
        if (!$value)
        {
            return App::abort(404);
        }
        
        if ($brandID === false)
        {
            return App::abort(404);
        }
        
        // Set month and get data
        $month = $request->get('month', date('Y-m'));
        
        if ($brandID === '0')
        {
            $brandID = false;
        }
        
        $data = $this->competitorPrice->getCompiledIndex($month, $brandID, $type, $value);
        
        // Set brand field
        $brandField = '';
        
        if ($brandID === false)
        {
            $brandField = '<th>Brand</th>';
        }
        
        $accountField = '<th>Account</th>';
        
        if ($type === 'account-all')
        {
            $accountField = '';
        }
        
        // Set html
        $html = '
            <thead>
                <tr>
                    <th>Branch</th>
                    '.$accountField.'
                    <th>Dealer</th>
                    '.$brandField.'
                    <th>Product Category</th>
                    <th>Competitor Model</th>
                    <th>Panasonic Model</th>
                    <th>Normal Price</th>
                    <th>Promo Price</th>
                    <th>Discount (%)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data as $item)
        {
            // Set account
            $account = '(none)';
            
            if ($item->account)
            {
                $account = $item->account;
            }
            
            // Set brand custom 
            $brandField = '';
            
            if ($brandID === false)
            {
                if ($item->brand_custom === '')
                {
                    $item->brand_custom = '-';
                }
                
                $brandField = '<td>'.$item->brand_custom.'</td>';
            }
            
            $accountField = '<td>'.$account.'</td>';
            
            if ($type === 'account-all')
            {
                $accountField = '';
            }
            
            // Set discount
            $discount = floor((($item->price_normal - $item->price_promo) / $item->price_normal)*10000)/100;
            
            $html .= '
                <tr>
                    <td>'.$item->branch.'</td>
                    '.$accountField.'
                    <td>'.$item->dealer.'</td>
                    '.$brandField.'
                    <td>'.$item->category.'</td>
                    <td>'.$item->model_name.'</td>
                    <td>'.$item->product_model.'</td>
                    <td>'.number_format($item->price_normal).'</td>
                    <td>'.number_format($item->price_promo).'</td>
                    <td>'.$discount.'</td>
                    <td>'.$item->date.'</td>
                </tr>';
        }
        
        // Close html
        $html .= '</tbody>';
        
        // Return response
        return response()->json($html);
    }
}
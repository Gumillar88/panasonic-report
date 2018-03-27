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

class DashboardChartController extends Controller
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
        $channelName = '';
        
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
            $channelData = $this->dealer_channel->getOne($request->get('channelID'));
            $channelName = $channelData->name;
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

        $salesAccountData = $this->data->chartSalesAccount($params);

        $compiledSalesAccount = [];
        $compiledTopStore     = [];
        $accountName = [
            "Electronic City",
            "Best Denki",
            "Electronic Solution",
            "Depo Bangunan",
            "White Brown",
            "Carrefour",
            "Hypermart",
            "Courts",
            "Electronic Solution DGS",
            "Lotte Mart",
            "Mitra 10",
            "Save Max",
            "Giant",
            "Others",
            "Lulu",
            "Hartono"
        ];

        $count_dealer = 1;

        foreach ($salesAccountData as $item)
        {
            foreach ($accountName as $name)
            {
                if(strpos($item->name, strtoupper($name)) !== false) 
                {

                    if (!array_key_exists($name, $compiledSalesAccount))
                    {
                        $compiledSalesAccount[$name] = [
                            'ID'      => $item->ID,
                            'branch'  => $item->branch,
                            'name'    => $name,
                            'total'   => 0,
                            'count'   => $count_dealer
                        ];
                    }

                    $compiledSalesAccount[$name]['total'] += $item->total;
                    $compiledSalesAccount[$name]['count'] += $count_dealer;
                }
                
                if (strpos($item->name, strtoupper($name)) !== true)
                {
                    $compiledTopStore[$item->name] = [
                        'ID'      => $item->ID,
                        'branch'  => $item->branch,
                        'name'    => $item->name,
                        'total'   => $item->total,
                        'count'   => $count_dealer
                    ];
                }
            }
        }

        $salesAccountDataReady = [];

        if($channelName)
        {
            if ($channelName == 'MUP')
            {
                foreach ($compiledTopStore as $item)
                {
                    array_push($salesAccountDataReady, $item);
                }   
            }
        }    
        else
        {
            foreach ($compiledSalesAccount as $item)
            {
                array_push($salesAccountDataReady, $item);
            }   
        }

        $ready = [];
        foreach ($salesAccountDataReady as $key => $row)
        {
            $ready[$key] = $row['total'];
        }
        
        array_multisort($ready, SORT_DESC, $salesAccountDataReady);

        
        // Set initial data
        $data = [
            'salesTrend'    => $compiledSalesTrend,
            'salesAccount'  => $salesAccountDataReady,
            'salesProduct'  => $salesProduct,
            'salesChannel'  => $salesChannel,
        ];

        
        return response()->json($data);
    }

}

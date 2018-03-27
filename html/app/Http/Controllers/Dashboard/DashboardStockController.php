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

class DashboardStockController extends Controller
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
}

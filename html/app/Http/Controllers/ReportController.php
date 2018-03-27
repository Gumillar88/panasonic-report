<?php

namespace App\Http\Controllers;

use App;
use Validator;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\RegionModel;
use App\Http\Models\BranchModel;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerChannelModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorTargetModel;
use App\Http\Models\LogModel;
use App\Http\Models\DashboardDataModel;
use App\Http\Models\ReportModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;

class ReportController extends Controller
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
     * dealer type model container
     *
     * @access Protected
     */
    protected $dealer_type;

    /**
     * dealer channel model container
     *
     * @access Protected
     */
    protected $dealer_channel;
    
    /**
     * Dealer account model container
     *
     * @access protected
     */
    protected $dealerAccount;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;

    /**
     * Target Promotor model container
     *
     * @access protected
     */
    protected $promotor_target;

    /**
     * Log model container
     *
     * @access protected
     */
    protected $log;

    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;

    /**
     * Product model container
     *
     * @access Protected
     */
    protected $product_model;

    /**
     * Product price model container
     *
     * @access Protected
     */
    protected $product_price;
    
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
        $this->dealer_type      = new DealerTypeModel();
        $this->dealer_channel   = new DealerChannelModel();
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_target  = new PromotorTargetModel();
        $this->log              = new LogModel();
        $this->data             = new DashboardDataModel();
        $this->report           = new ReportModel();
        $this->product_model    = new ProductModel();
        $this->product_price    = new ProductPriceModel();
    }

    /**
     * Get all branch
     * 
     * @access private
     * @return Array
     */
    private function _getAllBranch()
    {
        $result = $this->branch->getAll();
        $data   = [
            '0' => 'none'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }
    
    /**
     * Get all region
     *
     * @access private
     * @return Array
     */
    private function _getAllDealerData()
    {
        $branches   = $this->_getAllBranch();
        $dealers    = $this->dealer->getAll();
        
        $data   = [
            '0' => [
                'branch' => '(none)',
                'name' => '(none)'
            ]
        ];
        
        foreach ($dealers as $item)
        {
            $data[$item->ID] = [
                'branch'    => $branches[$item->branch_ID],
                'name'      => $item->name,
            ];
            
        }
        
        return $data;
    }
    
    /**
     * Get only real promotor account
     *
     * @access private
     * @return Array
     */
    private function _getOnlyPromotor()
    {
        $data       = [];
        $dataRaw    = $this->promotor->getAll();
        
        foreach ($dataRaw as $promotor)
        {
            if ($promotor->type === 'promotor')
            {
                $data[] = $promotor;
            }
        }
        
        return $data;
    }

    /**
     * Get all product 
     *
     * @access private
     * @return Array
     */
    private function _getAllProduct()
    {
        $result = $this->product_model->getAll();
        $data   = [
            '0' => '(none)',
            '' => '(none)'
        ];
        
        foreach ($result as $item)
        {
            $data[$item->ID] = $item->name;
        }
        
        return $data;
    }

    /**
     * Render promotor report index page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {              
        // Compiled target data
        $dealers    = $this->_getAllDealerData();
        $dataTarget = [];
        
        $promotors  = $this->_getOnlyPromotor();
        $promotorTarget = [];
        
        
        foreach ($promotors as $promotor)
        {
            $dataTarget[$promotor->ID] = [
                'ID'            => $promotor->ID,
                'promotor_ID'   => $promotor->ID,
                'promotor'      => $promotor->name,
                'dealer'        => $dealers[$promotor->dealer_ID]['name'],
                'branch'        => $dealers[$promotor->dealer_ID]['branch'],
                'total'         => 0,
                'hasTarget'     => false,
            ];
        }
        
        $data = [
            'date'          => date('Y-m'),
            'dataTarget'    => $dataTarget,
        ];

        return view('report.reportIndex', $data);
    }

    /**
     * Render promotor report view page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function viewRender(Request $request)
    {   
        $dateOption = $this->_generateTime();

        // Set date and type
        $date 	 	 = $request->get('date', date('Y-m'));
        $promotor_ID = $request->get('ID', false);

        if(!$promotor_ID)
        {
            return App::abort(404);
        }
        
        $reports  = $this->report->getDisplayReportPromotorByMonth($promotor_ID, $date);
        
        $data = [
            'date'          => $date,
            'promotorID'    => $promotor_ID,
            'listMonth'     => $dateOption,
            'dataReport'    => $reports,
        ];

        return view('report.reportView', $data);
    }

    /**
     * Render edit promotor report page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateRender(Request $request)
    {
        // Set default value
        $ID     = $request->get('ID', false);
        $date   = $request->get('date', date('Y-m'));
        $type   = 'source';
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get promotor data
        $report = $this->report->getOneReportPromotor($ID);
        
        if (!$report)
        {
            return App::abort(404);
        }
        
        // Check if report is custom
        if($report->custom_name !== '')
        {
            $type = 'custom';
        }
        
        // Set data
        $data = [
            'report'    => $report,
            'products'  => $this->_getAllProduct(),
            'date'      => $date,
            'type'      => $type
        ];
        
        return view('report.reportEdit', $data);
    }

    /**
     * Handle edit promotor request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        // Validate parameter
        $ID             = $request->get('ID', false);
        $quantity       = $request->get('quantity', false);
        $type           = $request->get('type', false);
        $customName     = $request->get('custom_name', false);
        $productModelID = $request->get('product_model_ID', false);
        $price          = $request->get('price', false);
        $date           = $request->get('date', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        if (!$quantity)
        {
            return App::abort(404);
        }
        
        if (!$date)
        {
            return App::abort(404);
        }
        
        if (!in_array($type, ['source', 'custom']))
        {
            return App::abort(404);
        }
        
        if (!$productModelID && !$customName)
        {
            return App::abort(404);
        }
        
        // Check if report exist or not
        $report = $this->report->getOne($ID);

        if (!$report)
        {
            return App::abort(404);
        }
        
        // Check if dealer exist or not
        $dealer = $this->dealer->getOne($report->dealer_ID);

        if ($type === 'custom')
        {
            $productModelID = 0;
        }
        else
        {
            // Set product price based on model
            $product = $this->product_model->getOne($productModelID);
            $price   = $product->price;
            
            $productPrice = $this->product_price->getDealerProductPrice(
                $dealer->dealer_type_ID, 
                $dealer->dealer_channel_ID, 
                $productModelID
            );
            
            if ($productPrice)
            {
                $price = $productPrice->price;
            }
        }
        
        // Set data
        $data = [
            'product_model_ID'  => $productModelID,
            'custom_name'       => $customName,
            'price'             => $price,
            'quantity'          => $quantity,
            'date'              => $date,
        ];
        
        // Save data
        $this->report->update($ID, $data);
        
        // Log request
        $action = 'Update report (ID:'.$ID.')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('report-updated', '');
        return back();
    }

    /**
     * Render remove promotor report page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeRender(Request $request)
    {
        // Get ID
        $ID   = $request->get('ID', false);
        $date = $request->get('date', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get promotor
        $report = $this->report->getOne($ID);
        
        if (!$report)
        {
            return App::abort(404);
        }
        
        return view('report.reportRemove', ['ID' => $ID, 'date' => $date, 'promotorID' => $report->promotor_ID]);
    }

    /**
     * Handle remove promotor report request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function removeHandle(Request $request)
    {
        // Get ID
        $ID   = $request->get('ID', false);
        $date = $request->get('date', false);
        
        if (!$ID)
        {
            return App::abort(404);
        }
        
        // Get promotor
        $report = $this->report->getOne($ID);
        
        if (!$report)
        {
            return App::abort(404);
        }
        
        // Remove agent
        $this->report->remove($ID);
        
        // Log request
        $action = 'Remove report (ID:'.$ID.'|name:'.$report->product_model_ID.')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('report-removed', '');
        return redirect('report/view?ID='.$report->promotor_ID.'&date='.$date);
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
            'years' => []
        ];
        
        while ($monthLast > $monthFirst)
        {
            $monthValue = date('m', $monthLast);
            $yearValue  = date('Y', $monthLast);
            $month      = $yearValue.'-'.$monthValue;
            
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
}

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
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\PromotorTargetModel;
use App\Http\Models\LogModel;
use App\Http\Models\DashboardDataModel;


class SalesTargetController extends Controller
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
     * Month
     *
     * @access protected
     */
    protected $month = [
        '2016-05'   => 'Mei 2016',
        '2016-06'   => 'Juni 2016',
        '2016-07'   => 'Juli 2016',
        '2016-08'   => 'Agustus 2016',
        '2016-09'   => 'September 2016',
        '2016-10'   => 'Oktober 2016',
        '2016-11'   => 'November 2016',
        '2016-12'   => 'Desember 2016',
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
        $this->dealerAccount    = new DealerAccountModel();
        $this->promotor         = new PromotorModel();
        $this->promotor_target  = new PromotorTargetModel();
        $this->log              = new LogModel();
        $this->data             = new DashboardDataModel();
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
     * Helper function to save promotor target
     *
     * @access private
     * @param Integer $promotorID
     * @param String $month
     */
    private function _saveTargetPromotor($promotorID, $month, $total)
    {
        // Get promotor and dealer data
        $promotor   = $this->promotor->getOne($promotorID);
        $dealer     = $this->dealer->getOne($promotor->dealer_ID);
        
        // Get account ID
        $accountID = 0;

        if ($dealer->dealer_account_ID != 0)
        {
            $dealerAccount = $this->dealerAccount->getOne($dealer->dealer_account_ID);
            
            if ($dealerAccount)
            {
                $accountID = $dealerAccount->ID;
            }
        }
        
        // Get team leader and arco
        $TLID       = $promotor->parent_ID;
        $TL         = $this->promotor->getOne($TLID);
        $arcoID     = $TL->parent_ID;
        
        // Set target
        return $this->promotor_target->create(
            $promotor->ID, 
            $promotor->dealer_ID, 
            $accountID, 
            $TLID, 
            $arcoID, 
            $total, 
            $month
        );
    }
    
    /**
     * Render sales target index page by promotor or dealer
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {   
        $dateOption = $this->_generateTime();

        // Set date and type
        $date = $request->get('date', date('Y-m'));
        $type = $request->get('type', 'dealer');
        
        // Set dafault type button
        $typeButton = [
            'dealer'    => 'btn btn-green',
            'promotor'  => 'btn btn-green'
        ];
        
        // Prevent another type
        if ($type !== 'promotor')
        {
            $type = 'dealer';
        }
        
        // Set active button
        $typeButton[$type] = 'btn';
        
        
        // Compiled target data
        $targets    = $this->promotor_target->getAllByMonth($date);
        $dealers    = $this->_getAllDealerData();
        $dataTarget = [];
        
        if ($type === 'promotor')
        {
            $promotors  = $this->_getOnlyPromotor();
            $promotorTarget = [];
            
            foreach ($targets as $target)
            {
                $promotorTarget[$target->promotor_ID] = $target->total;
            }
            
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
                
                if (array_key_exists($promotor->ID, $promotorTarget))
                {
                    $dataTarget[$promotor->ID]['total']     = $promotorTarget[$promotor->ID];
                    $dataTarget[$promotor->ID]['hasTarget'] = true;
                    
                }
            }
        }
        
        if ($type === 'dealer')
        {
            foreach ($targets as $target)
            {
                if (!array_key_exists($target->dealer_ID, $dataTarget))
                {
                    $dataTarget[$target->dealer_ID] = [
                        'ID'            => $target->dealer_ID,
                        'dealer'        => $dealers[$target->dealer_ID]['name'],
                        'branch'        => $dealers[$target->dealer_ID]['branch'],
                        'totalPromotor' => 0,
                        'total'         => 0,
                        'hasTarget'     => true,
                    ];
                }

                $dataTarget[$target->dealer_ID]['totalPromotor']++;
                $dataTarget[$target->dealer_ID]['total'] += $target->total;
            }
        }
        
        $data = [
            'type'          => $type,
            'typeButton'    => $typeButton,
            'date'          => $date,
            'listMonth'     => $dateOption,
            'dataTarget'    => $dataTarget,
        ];

        return view('sales-target.salesTargetIndex', $data);
    }

    /**
     * Render create sales target page (for promotor only)
     *
     * @access public
     * @return Response
     */
    public function createRender(Request $request)
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'date'          => 'required',
            'promotor_ID'   => 'required|exists:promotors,ID',
        ]);
        
        if ($validator->fails())
        {
            return App::abort(404);
        }
        
        // Get promotor data
        $promotor = $this->promotor->getOne($input['promotor_ID']);
        
        if ($promotor->type !== 'promotor')
        {
            return App::abort(404);
        }

        $data = [
            'dealer'    => $this->dealer->getOne($promotor->dealer_ID),
            'promotor'  => $promotor,
            'date'      => $input['date'],
        ];

        return view('sales-target.salesTargetCreate',$data);
    }
    
    /**
     * Handle create sales target request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function createHandle(Request $request)
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'date'          => 'required',
            'promotor_ID'   => 'required|exists:promotors,ID',
            'total'         => 'required',
        ]);
        
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get promotor and dealer data
        $promotor   = $this->promotor->getOne($input['promotor_ID']);
        
        // Set target
        $ID = $this->_saveTargetPromotor($input['promotor_ID'], $input['date'], $input['total']);
        
        // Log request
        $action = 'Create a promotor target (promotor_ID:'.$promotor->ID.', dealer_ID:'.$promotor->dealer_ID.')';
        $this->log->record($request->userID, $action);

        // Set session and redirect
        $request->session()->flash('sales-target-created', '');
        return redirect('/sales-target/edit?ID='.$input['promotor_ID'].'&date='.$input['date'].'&type=promotor');
        
    }
    
    /**
     * Render edit sales target page
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateRender(Request $request)
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'date'  => 'required',
            'ID'    => 'required',
            'type'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            return App::abort(404);
        }
        
        // Set default type
        $type = 'dealer';
        
        if ($input['type'] === 'promotor')
        {
            $type = 'promotor';
        }
        
        // Set default data
        $data = [
            'type'  => $type,
            'ID'    => $input['ID'],
            'date'  => $input['date']
        ];
        
        if ($input['type'] === 'promotor')
        {
            // Get promotor data
            $target = $this->promotor_target->getByPromotor($input['ID'], $input['date']);
            
            if (!$target)
            {
                return App::abort(404);
            }
            
            // Get promotor data
            $promotor = $this->promotor->getOne($input['ID']);

            $data['dealer']     = $this->dealer->getOne($promotor->dealer_ID);
            $data['promotor']   = $promotor;
            $data['total']      = $target->total;
        }
        
        if ($input['type'] === 'dealer')
        {
            // Get dealer data
            $total      = 0;
            $targets    = $this->promotor_target->getAllByDealer($input['ID'], $input['date']);
            
            foreach ($targets as $target)
            {
                $total += $target->total;
            }

            $data['dealer'] = $this->dealer->getOne($input['ID']);
            $data['total']  = $total;
        }
        
        return view('sales-target.salesTargetEdit', $data);
    }
    
    /**
     * Handle edit sales target request
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function updateHandle(Request $request)
    {
        
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'date'  => 'required',
            'ID'    => 'required',
            'type'  => 'required',
            'total' => 'required',
        ]);
        
        if ($validator->fails())
        {
            return App::abort(404);
        }
        
        // Set default type
        $type = 'dealer';
        
        if ($input['type'] === 'promotor')
        {
            $type = 'promotor';
        }
        
        // Update promotor sales target
        if ($type === 'promotor')
        {
            // Get target
            $target = $this->promotor_target->getByPromotor($input['ID'], $input['date']);
            
            if (!$target)
            {
                return App::abort(404);
            }
            
            $this->promotor_target->update($target->ID, ['total' => $input['total']]);
        }
        
        // Update promotor sales target based on dealer
        if ($input['type'] === 'dealer')
        {
            // Get target
            $promotors = $this->promotor->getByDealer($input['ID']);
            
            // Split total based on promotor
            $total = floor($input['total']/count($promotors));
            
            foreach ($promotors as $promotor)
            {
                // Get target
                $target = $this->promotor_target->getByPromotor($promotor->ID, $input['date']);
                
                // Update if target exists
                if ($target)
                {
                    $this->promotor_target->update($target->ID, ['total' => $total]);
                }
                else // or create new one
                {
                    $this->_saveTargetPromotor($promotor->ID, $input['date'], $total);
                }
            }
        }
        
        // Log request
        $action = 'Update promotor target (promotor_ID:'.$input['ID'].')';
        $this->log->record($request->userID, $action);
        
        // Set session and redirect
        $request->session()->flash('sales-target-updated', '');
        return back();
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
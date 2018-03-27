<?php

namespace App\Http\Controllers\API\V_1_5_0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\DealerModel;
use App\Http\Models\DealerTypeModel;
use App\Http\Models\DealerAccountModel;
use App\Http\Models\PromotorModel;
use App\Http\Models\ReportModel;
use App\Http\Models\ProductCategoryModel;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductPriceModel;
use App\Http\Models\TokenModel;
use App\Http\Models\BranchModel;
use App\Http\Models\RegionModel;
use App\Http\Models\ReportSaleModel;
use Carbon\Carbon;

class ReportSalesController extends Controller
{
    /**
     * Token model container
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
     * Branch model container
     *
     * @access protected
     */
    protected $branch;

    /**
     * dealer account model container
     *
     * @access Protected
     */
    protected $dealerAccount;
    
    /**
     * Dealer model container
     *
     * @access protected
     */
    protected $dealer;
    
    /**
     * Promotor model container
     *
     * @access protected
     */
    protected $promotor;
    
    /**
     * Report model container
     *
     * @access protected
     */
    protected $report;
    
    /**
     * Object constructor
     *
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->token            = new TokenModel();
        $this->region           = new RegionModel();
        $this->branch           = new BranchModel();
        $this->dealerAccount   = new DealerAccountModel();
        $this->dealer           = new DealerModel();
        $this->promotor         = new PromotorModel();
        $this->report           = new ReportModel();
    }
    
    /**
     * Get salest target by region
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getRegion(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        if ($promotor->type !== 'panasonic')
        {
            return response()->json(['error' => 'no-auth']);
        }

        // Set time YYYY-MM
        $time = date('Y-m', time());

        $dataRegion = [];

        $target = $this->report->getTargetSalesRegion($time);
        $sales  = $this->report->getSalesRegion($time);
        
        foreach ($target as $item)
        {
            $total = 0;
            
            if ($item->total !== null)
            {
                $total = $item->total;
            }
            
            $dataRegion[$item->ID] = [
                // Use arco ID for backward compatibility, will be removed in v1.5.0
                'ID'            => $item->ID.'-'.$item->promotor_ID, 
                'name'          => $item->name ,
                'target'        => $total,
                'sales'         => 0,
                'persentase'    => 0,
            ];
        }
        
        foreach ($sales as $item)
        {
            $salesData  = 0;
            
            if ($item->total !== null)
            {
                $salesData = $item->total;   
            }
            
            $targetData                     = $dataRegion[$item->ID]['target'];
            $dataRegion[$item->ID]['sales'] = number_format($item->total);
            
            if($salesData != 0 && $targetData != 0)
            {
                $dataRegion[$item->ID]['persentase'] = round(( $salesData / $targetData) * 100, 2);
            }
        }


        return response()->json(['result' => $dataRegion]);
    }

    /**
     * Get sales data by branch
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getBranch(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $arcoID     = $request->get('arcoID', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$arcoID)
        {
            return response()->json(['error' => 'no-ID']);
        }
        
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Split arco id
        if(strpos($arcoID, '-') !== false)
        {
            $regionID   = explode('-', $arcoID)[0];
            $arcoID     = explode('-', $arcoID)[1];
        }
        else
        {
            $regionID   = 0;
            $arcoID     = $arcoID;
        }
        
        //set time YYYY-MM
        $time = date('Y-m', time());
        
        $dataBranch = [];

        $target = $this->report->getTargetSalesBranchByRegionArco($regionID, $arcoID, $time);
        $sales  = $this->report->getSalesBranchByRegionArco($regionID, $arcoID, $time);
        
        foreach ($target as $item)
        {
            $total = 0;
            
            if ($item->total !== null)
            {
                $total = $item->total;
            }
            
            $dataBranch[$item->ID] = [
                // Use team leader ID for backward compatibility, will be removed in v1.5.0
                'ID'            => $item->ID.'-'.$item->promotor_ID, 
                'name'          => $item->name,
                'target'        => $total,
                'sales'         => 0,
                'persentase'    => 0,
            ];
        }
        
        foreach ($sales as $item)
        {
            $salesData  = 0;
            
            if ($item->total !== null)
            {
                $salesData = $item->total;   
            }
            
            $dataBranch[$item->ID]['sales'] = number_format($item->total);
            $targetData = $dataBranch[$item->ID]['target'];
            
            if($salesData != 0 && $targetData != 0)
            {
                $dataBranch[$item->ID]['persentase'] = round(( $salesData / $targetData) * 100, 2);
            }
        }
        
        
        return response()->json(['result' => $dataBranch]);
    }

    /**
     * Get sales data by account
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getAccount(Request $request)
    {
        // Validate parameter
        $token  = $request->get('token', false);
        $TLID  = $request->get('tlID', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$TLID)
        {
            return response()->json(['error' => 'no-ID']);
        }

        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        $type       = '';
        $branchID   = 0;
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        // Quickfix branch
        $TLIDs = [];
        
        // Split team leader id
        if(strpos($TLID, '-') !== false)
        {
            $branchID   = explode('-', $TLID)[0];
            $TLID       = explode('-', $TLID)[1];
        }
        else
        {
            // Branch Manager association with tl (Quickfix)
            $associations = [
                '809'   => [958, 959, 960, 961], // Medan
                '812'   => [962, 963, 964], // Pekanbaru
                '808'   => [965, 966], // Batam
                '807'   => [976, 968, 969, 970], // Palembang
                '810'   => [971, 972, 973], // Semarang
                '1186'  => [974, 975, 976, 977, 978, 1017], // Jatim Tradi
                '1067'  => [974, 975, 976, 977, 978, 1017], // Jatim Modern
                '803'   => [979, 980], // Bali
                '804'   => [981, 982], // Banjarmasin
                '801'   => [983, 984], // Samarinda
                '805'   => [985, 986, 987], // Makassar
                '802'   => [988, 989], // Manado
                '806'   => [990, 991, 992, 1258], // Yogya
                '1086'  => [993, 994, 995, 996, 997, 998, 999], // Jakarta MUP
                '1008'  => [1000, 1001, 1002, 1003, 1004, 1005], // Jakarta SO
                '811'   => [1006, 1007, 1074, 1076, 1077, 1079, 1080, 1081, 1082], // Jabar
            ];
            
            foreach ($associations as $key => $item)
            {
                if (in_array($TLID, $item))
                {
                    $TLIDs[] = $key;
                }
            }
            
            // Set primary tlid
            $TLIDs[] = $TLID;
            
            // Check if TL has branch
            $branch = $this->branch->getByPromotor($TLIDs[0]);
            
            
            // Check if TL has account
            if ($branch)
            {
                $type       = 'branch';
                
                // Quickfix Jakarta SO
                if ($TLIDs[0] === 1008)
                {
                    // Set branch ID
                    $branchID = $branch->ID;
                    $TLID = $TLIDs[0];
                    $type = '';
                }
            }
            else
            {
                $account = $this->dealerAccount->getByPromotor($TLID);
                
                if (!$account) 
                {
                    return response()->json(['error' => 'no-data']);
                }
                
                $type       = 'account';
            }
        }

        //set time YYYY-MM
        $time = date('Y-m', time());

        $dataAccount = [];
        
        // Get single branch data
        if ($type === 'branch')
        {
            $target = $this->report->getTargetSalesBranchByTL($TLIDs, $time);
            $sales  = $this->report->getSalesBranchByTL($TLIDs, $time);
        }
        else if ($type === 'account') // Get account data
        {
            $target = $this->report->getTargetSalesAccountByTL($TLID, $time);
            $sales  = $this->report->getSalesAccountByTL($TLID, $time);
        }
        else
        {
            $target = $this->report->getTargetSalesAccountByBranchTL($branchID, $TLID, $time);
            $sales  = $this->report->getSalesAccountByBranchTL($branchID, $TLID, $time);
        }
        
        foreach ($target as $item)
        {
            $total = 0;
            
            if ($item->total !== null)
            {
                $total = $item->total;
            }
            
            $dataAccount[$item->ID] = [
                'ID'            => $type.'-'.$item->ID, 
                'name'          => $item->name,
                'target'        => $total,
                'sales'         => 0,
                'persentase'    => 0,
            ];
        }
        
        foreach ($sales as $item)
        {
            
            $salesData  = 0;
            
            if ($item->total !== null)
            {
                $salesData = $item->total;   
            }
            
            $dataAccount[$item->ID]['sales'] = number_format($item->total);
            $targetData = $dataAccount[$item->ID]['target'];
            
            if($salesData != 0 && $targetData != 0)
            {
                $dataAccount[$item->ID]['persentase'] = round(( $salesData / $targetData) * 100, 2);
            }
        }
        
        // Set response data
        $responseData = [
            'type'      => $type,
            'result'    => $dataAccount
        ];
        
        // Check if branch has account or not
        $accounts = $this->dealerAccount->getByBranch($branchID);
        
        if (!$accounts && $branchID !== 0) 
        {
            $responseData['skip'] = true;
        }
        
        return response()->json($responseData);
    }
    
    /**
     * Get sales data by dealer
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getDealer(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $accountID  = $request->get('accountID', false);

        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$accountID)
        {
            return response()->json(['error' => 'no-account']);
        }
        
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }
        
        $paramData = explode('-', $accountID);
        $accountID = $paramData[1];

        // Define default dealer
        $branchID = 0;
        
        if (in_array('branch', $paramData))
        {
            $branchID = $paramData[1];
        }

        //set time YYYY-MM
        $time = date('Y-m', time());

        $dataDealer = [];
        
        if ($branchID !== 0)
        {
            $dealers = $this->dealer->getByBranch($branchID);
        }
        else
        {
            $dealers = $this->dealer->getByAccount($accountID);
        }
        
        // Complied data target
        foreach ($dealers as $key => $value) 
        {
            //get target dealer by user
            $targetDealer = $this->report->getTargetSalesDealer($value->ID, $time);

            $total = 0;
            // calculate total target
            foreach ($targetDealer as $row) 
            {
                $total += $row->total;
            }

            //marge data
            $dataDealer[$value->ID] = [
                'ID'        => $value->ID ,
                'name'      => $value->name ,
                'target'    => $total,
                'persentase' => 0,
            ];
        }

        // Compiled data sales
        foreach ($dealers as $key => $value) 
        {
            //get sales dealer by user
            $salesDealer = $this->report->getSalesDealer($value->ID);

            $total = 0 ;
            //calculate total sales
            foreach ($salesDealer as $row) 
            {
                $total += $row->price * $row->quantity;
            }
            
            // Merge data
            $dataDealer[$value->ID]['sales'] = $total;
        }

        // Compiled data dealer
        foreach ($dataDealer as $key => $value) 
        {
            if ($value['sales'] > 0 && $value['target'] > 0 )
            {
                $dataDealer[$key]['persentase'] = round(( $value['sales'] / $value['target'] ) * 100, 2);
            }
            
            $dataDealer[$key]['sales'] = number_format($dataDealer[$key]['sales']);
        }

        return response()->json(['result' => $dataDealer]);
    }

    /**
     * Get sales data by promotor
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getPromotor(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $dealerID   = $request->get('dealerID', false);
        
        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }

        if (!$dealerID)
        {
            return response()->json(['error' => 'no-dealerID']);
        }
        
        $promotorID = $this->token->decode($token);
        $promotor   = $this->promotor->getOne($promotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }

        //set time YYYY-MM
        $time = date('Y-m', time());

        $dataPromotor = [];

        // Get promotor by dealer
        $dataDealer = $this->promotor->getByDealer($dealerID);
        
        // Compiled data target
        foreach ($dataDealer as $key => $value) 
        {
            // Get target promotor by user
            $targetPromotor = $this->report->getTargetSalesPromotor($value->ID, $time);

            $total = 0;
            
            // Calculate total target
            foreach ($targetPromotor as $row) 
            {
                $total += $row->total;
            }

            // Merge data
            $dataPromotor[$value->ID] = [
                'ID'        => $value->ID ,
                'name'      => $value->name ,
                'target'    => $total,
                'persentase' => 0,
            ];
        }

        // Compiled data sales
        foreach ($dataDealer as $key => $value) 
        {
            // Get sales promotor by user
            $salesPromotor = $this->report->getSalesPromotor($value->ID);

            $total = 0;
            
            // Calculate total sales
            foreach ($salesPromotor as $row) 
            {
                $total += $row->price * $row->quantity;
            }
            
            // Merge data
            $dataPromotor[$value->ID]['sales'] = $total;
        }

        // Complied data promotor
        foreach ($dataPromotor as $key => $value) 
        {
            
            if($value['sales'] > 0 && $value['target'] > 0 )
            {
                $dataPromotor[$key]['persentase'] = round(( $value['sales'] / $value['target'] ) * 100, 2);
            }
            
            $dataPromotor[$key]['sales'] = number_format($dataPromotor[$key]['sales']);
        }
        
        return response()->json(['result' => $dataPromotor]);
    }

    /**
     * Get sales data by promotor detail
     *
     * @access public
     * @param Request $request
     * @return Response
     */
    public function getPromotorDetail(Request $request)
    {
        // Validate parameter
        $token      = $request->get('token', false);
        $promotorID = $request->get('promotorID', false);

        if (!$token)
        {
            return response()->json(['error' => 'no-token']);
        }
        
        if (!$promotorID)
        {
            return response()->json(['error' => 'no-promotorID']);
        }

        $currentPromotorID  = $this->token->decode($token);
        $promotor           = $this->promotor->getOne($currentPromotorID);
        
        if (!$promotor)
        {
            return response()->json(['error' => 'no-auth']);
        }

        //set time YYYY-MM
        $time = date('Y-m', time());

        $promotorReportData = $this->report->getReportPromotorByMonth($promotorID, $time);

        return response()->json(['result' => $promotorReportData]);
    }

}
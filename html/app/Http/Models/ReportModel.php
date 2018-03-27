<?php

/**
 * Report data module
 */

namespace App\Http\Models;

use Exception;
use DB;

class ReportModel
{
    /**
     * Create new report
     *
     *
     * @access public
     * @param Array $data
     * @return Integer
     */
    public function create($data)
    {
        // Set time
        $time = time();
        
        // Set final data container
        $finalData = [
            'created' => $time,
            'updated' => $time
        ];
        
        // Validate data key
        $validKeys = [
            'dealer_ID',
            'promotor_ID',
            'account_ID',
            'tl_ID',
            'arco_ID',
            'customer_ID',
            'product_model_ID',
            'custom_name',
            'price',
            'quantity',
            'date',
        ];
        
        foreach ($validKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                throw new Exception('Report Model: Key "'.$key.'" is not found on data parameter');
            }
            
            $finalData[$key] = $data[$key];
        }
        
        return DB::table('reports')->insertGetId($finalData);
    }

    /**
     * Get one report data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('reports')->where('ID', $ID)->first();
    }
    
    /**
     * Get all report by date and dealer
     *
     * @access public
     * @param Integer $dealerID
     * @param String $date
     * @return Array
     */
    public function getByDateDealer($dealerID, $date)
    {
        // Set default query
        $query = DB::table('reports')
            
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->join('product_categories', 'product_categories.ID', '=', 'products.product_category_ID')
            
                ->join('promotors', 'promotors.ID', '=', 'reports.promotor_ID')
                ->join('dealers', 'dealers.ID', '=', 'promotors.dealer_ID')
            
                ->where('reports.date', $date)
                ->where('dealers.ID',   $dealerID)
                ->select(
                    'reports.ID as ID',
                    'promotors.name as promotor_name',
                    'product_categories.name as category_name',
                    'reports.quantity as quantity'
                );
        
        return $query->get()->all();
    }
    
    /**
     * Get all report by dealer and date (for App)
     *
     * @access public
     * @param Integer $promotorID
     * @param String $date
     * @return Array
     */
    public function getByDatePromotor($promotorID, $date)
    {
        $query = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date', $date)
                ->where('reports.promotor_ID', $promotorID)
                ->select(
                    'reports.ID as ID',
                    'reports.promotor_ID as promotor_ID',
                    'product_models.name as name',
                    'product_models.subcategory as subcategory',
                    'reports.quantity as quantity',
                    'reports.custom_name as custom_name'
                );
        
        return $query->get()->all();
    }

    /**
     * Get all report by 1 month and dealer (for app)
     *
     * @access public
     * @param Integer $dealerID
     * @param String $date
     * @return Array
     */
    public function getByMonthDealer($dealerID, $date)
    {
        // Set default query
        $query = DB::table('reports')
            
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->join('product_categories', 'product_categories.ID', '=', 'product_models.product_category_ID')
            
                ->join('promotors', 'promotors.ID', '=', 'reports.promotor_ID')
                ->join('dealers', 'dealers.ID', '=', 'promotors.dealer_ID')
            
                ->where('reports.date', $date)
                ->where('dealers.ID',   $dealerID)
                ->select(
                    'reports.ID as ID',
                    'promotors.name as promotor_name',
                    'product_categories.name as category_name',
                    'reports.quantity as quantity'
                );
        
        return $query->get()->all();
    }
    


    
    /**
     * Get all target and product model for promotor based on month (for App)
     *
     * @access public
     * @param Integer $promotorID
     * @param String $month
     * @return Array
     */
    public function getByMonthTargetPromotor($promotorID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.promotor_ID', $promotorID)
                ->select(
                    'promotor_targets.total as total'
                );
        
        return $query->first();
    }
    
    /**
     * Get list report by promotor based on month
     *
     * @access public
     * @param Integer $promotorID
     * @param String $month
     * @return Array
     */
    public function getReportPromotorByMonth($promotorID, $month)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', $month.'-01')
                ->where('reports.date','<=', $month.'-31')
                ->where('reports.promotor_ID', $promotorID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );

        return $result->get()->all();
    }

    /**
     * Get list display report by promotor based on month
     *
     * @access public
     * @param Integer $promotorID
     * @param String $month
     * @return Array
     */
    public function getDisplayReportPromotorByMonth($promotorID, $month)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.promotor_ID', $promotorID)
                ->where('reports.date','LIKE', $month.'%')
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.date as date',
                    'reports.quantity as quantity'
                );

        return $result->get()->all();
    }

    /**
     * Get one report by promotor based on ID
     *
     * @access public
     * @param Integer $promotorID
     * @param String $month
     * @return Array
     */
    public function getOneReportPromotor($reportID)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.ID', $reportID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.date as date',
                    'reports.quantity as quantity'
                );

        return $result->get()->first();
    }
    

    /**
     * Get all report a month data promotor (for App)
     *
     * @access public
     * @param Integer $promotorID
     * @param String $date
     * @param String $curDate
     * @return Array
     */
    public function getByMonthTargetPromotorData($promotorID,$date, $curDate)
    {
        $query = DB::table('reports')
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->join('promotor_targets', 'promotor_targets.product_ID', '=', 'product_models.ID')
                ->whereBetween('reports.date', [$date,$curDate])
                ->where('reports.promotor_ID', $promotorID)
                ->select(
                    'reports.ID as ID',
                    'reports.quantity as quantity',
                    'product_models.ID as productID',
                    'reports.price as price'
                );
        
        return $query->get()->all();
    }

    /**
     * Get all report by dealer and month (for App)
     *
     * @access public
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getByMonthTargetDealer($dealerID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.dealer_ID', $dealerID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.product_ID as product_ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * Get all report a month data dealer (for App)
     *
     * @access public
     * @param Integer $dealerID
     * @param String $date
     * @param String $curDate
     * @return Array
     */
    public function getByMonthTargetDealerData($dealerID, $date, $curDate)
    {
        $query = DB::table('reports')
                ->whereBetween('reports.date', [$date, $curDate])
                ->where('reports.dealer_ID', $dealerID)
                ->select(
                    'reports.ID as ID',
                    'reports.quantity as quantity',
                    'reports.price as price'
                );
        
        return $query->get()->all();
    }
    

    /**
     * Get all report by dealer and month (for App)
     *
     * @access public
     * @param Integer $promotorID
     * @param String $last
     * @param String $lastMonth
     * @return Array
     */
    public function getByMonthComparisonByPromotor($promotorID, $last, $lastMonth)
    {
        $query = DB::table('reports')
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->whereBetween('reports.date',[$last,$lastMonth])
                ->where('reports.promotor_ID', $promotorID)
                ->select(
                    'reports.ID as ID',
                    'reports.quantity as quantity',
                    'product_models.ID as productID',
                    'reports.price as price'
                );
        return $query->get()->all();
    }

    /**
     * TARGET SALES ARCO (For App)
     *
     * @access public
     * @param Integer $arco_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesArco($arco_ID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.arco_ID', $arco_ID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * SALES ARCO (For App)
     *
     * @access public
     * @param Integer $arco_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getSalesArco($arco_ID)
    {
        $result = DB::table('reports')
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', date('Y-m-01'))
                ->where('reports.date','<=', date('Y-m-31'))
                ->where('reports.arco_ID', $arco_ID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );
        
        return $result->get()->all();
    }
    
    /**
     * Get sales target by region
     *
     * Use arco ID for backward compatibility, will be removed in v1.5.0
     *
     * @access public
     * @param String $month
     * @return Array
     */
    public function getTargetSalesRegion($month)
    {
        return DB::select(
            'SELECT 
                regions.ID AS ID, 
                regions.promotor_ID AS promotor_ID,
                regions.name AS name,
                SUM(promotor_targets.total) AS total 
            FROM regions
	           JOIN dealers ON dealers.region_ID = regions.ID
	           LEFT JOIN promotor_targets 
                ON promotor_targets.dealer_ID = dealers.ID
                    AND promotor_targets.month = :month
            GROUP BY regions.ID',
            ['month' => $month]
        );
    }
    
    /**
     * Get sales by region
     *
     * @access public
     * @param String $month
     * @return Array
     */
    public function getSalesRegion($month)
    {
        return DB::select(
            'SELECT 
                regions.ID AS ID, 
                regions.name AS name, 
                SUM(reports.price * reports.quantity) AS total 
            FROM regions
                JOIN dealers ON dealers.region_ID = regions.ID
                LEFT JOIN reports 
                    ON reports.dealer_ID = dealers.ID
                        AND reports.date BETWEEN :firstDate AND :lastDate
            GROUP BY regions.ID',
            [
                'firstDate' => $month.'-01',
                'lastDate'  => $month.'-31'
            ]
        );
    }
    
    /**
     * Get sales target by branch using arco ID
     *
     * Use arco ID for backward compatibility, will be removed in v1.5.0
     *
     * @access public
     * @param Integer $regionID
     * @param Integer $arcoID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesBranchByRegionArco($regionID, $arcoID, $month)
    {
        $params = [
            'arcoID'    => $arcoID,
            'month'     => $month
        ];
        
        $regionQuery = '';
        
        if ($regionID != 0)
        {
            $params['regionID'] = $regionID;
            $regionQuery = 'AND regions.ID = :regionID';
        }
        
        return DB::select(
            'SELECT 
                branches.ID AS ID, 
                branches.promotor_ID AS promotor_ID,
                branches.name AS name, 
                SUM(promotor_targets.total) AS total 
            FROM branches  
                JOIN dealers ON dealers.branch_ID = branches.ID
                JOIN regions 
                    ON regions.ID = branches.region_ID
                        AND regions.promotor_ID = :arcoID
                        '.$regionQuery.'
                LEFT JOIN promotor_targets 
                    ON promotor_targets.dealer_ID = dealers.ID
                        AND promotor_targets.month = :month
            GROUP BY branches.ID',
            $params
        );
    }
    
    /**
     * Get sales by branch using arco ID
     *
     * @access public
     * @param Integer $arcoID
     * @param Integer $regionID
     * @param String $month
     * @return Array
     */
    public function getSalesBranchByRegionArco($regionID, $arcoID, $month)
    {
        $params = [
            'arcoID'    => $arcoID,
            'firstDate' => $month.'-01',
            'lastDate'  => $month.'-31'
        ];
        
        $regionQuery = '';
        
        if ($regionID != 0)
        {
            $params['regionID'] = $regionID;
            $regionQuery = 'AND regions.ID = :regionID';
        }
        
        return DB::select(
            'SELECT 
                branches.ID AS ID,
                branches.name AS name, 
                SUM(reports.price * reports.quantity) AS total 
            FROM branches
                JOIN dealers ON dealers.branch_ID = branches.ID
                JOIN regions 
                    ON regions.ID = branches.region_ID
                        AND regions.promotor_ID = :arcoID
                        '.$regionQuery.'
                LEFT JOIN reports 
                    ON reports.dealer_ID = dealers.ID
                        AND reports.date BETWEEN :firstDate AND :lastDate
            GROUP BY branches.ID',
            $params
        );
    }
    
    /**
     * Get sales target by account using TLID
     *
     * @access public
     * @param Integer $branchID
     * @param Integer $TLID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesAccountByBranchTL($branchID, $TLID, $month)
    {
        $params = [
            'TLID'      => $TLID,
            'month'     => $month
        ];
        
        $branchQuery = '';
        
        if ($branchID != 0)
        {
            $params['branchID'] = $branchID;
            $branchQuery = 'AND branches.ID = :branchID';
        }
        
        return DB::select(
            'SELECT 
                dealer_accounts.ID AS ID,
                dealer_accounts.name AS name, 
                SUM(promotor_targets.total) AS total
            FROM dealer_accounts
                JOIN dealers ON dealers.dealer_account_ID = dealer_accounts.ID
                JOIN branches 
                    ON branches.ID = dealer_accounts.branch_ID
                        AND branches.promotor_ID = :TLID
                        '.$branchQuery.'
                LEFT JOIN promotor_targets 
                    ON promotor_targets.dealer_ID = dealers.ID
                        AND promotor_targets.month = :month
            GROUP BY dealer_accounts.ID',
            $params
        );
    }
    
    /**
     * Get sales by account using TLID
     *
     * @access public
     * @param Integer $branchID
     * @param Integer $TLID
     * @param String $month
     * @return Array
     */
    public function getSalesAccountByBranchTL($branchID, $TLID, $month)
    {
        $params = [
            'TLID'      => $TLID,
            'firstDate' => $month.'-01',
            'lastDate'  => $month.'-31'
        ];
        
        $branchQuery = '';
        
        if ($branchID != 0)
        {
            $params['branchID'] = $branchID;
            $branchQuery = 'AND branches.ID = :branchID';
        }
        
        return DB::select(
            'SELECT 
                dealer_accounts.ID AS ID,
                dealer_accounts.name AS name, 
                SUM(reports.price * reports.quantity) AS total 
            FROM dealer_accounts
                JOIN dealers ON dealers.dealer_account_ID = dealer_accounts.ID
                JOIN branches 
                    ON branches.ID = dealer_accounts.branch_ID
                        AND branches.promotor_ID = :TLID
                        '.$branchQuery.'
                LEFT JOIN reports 
                    ON reports.dealer_ID = dealers.ID
                        AND reports.date BETWEEN :firstDate AND :lastDate
            GROUP BY dealer_accounts.ID',
            $params
        );
    }
    
    
    
    /**
     * Get sales target by account using TLID
     *
     * @access public
     * @param Integer $TLID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesAccountByTL($TLID, $month)
    {
        $params = [
            'TLID'      => $TLID,
            'month'     => $month
        ];
        
        return DB::select(
            'SELECT 
                dealer_accounts.ID AS ID,
                dealer_accounts.name AS name, 
                SUM(promotor_targets.total) AS total
            FROM dealer_accounts
                JOIN dealers ON dealers.dealer_account_ID = dealer_accounts.ID
                LEFT JOIN promotor_targets 
                    ON promotor_targets.dealer_ID = dealers.ID
                        AND promotor_targets.month = :month
            WHERE dealer_accounts.promotor_ID = :TLID
            GROUP BY dealer_accounts.ID',
            $params
        );
    }
    
    /**
     * Get sales by account using TLID
     *
     * @access public
     * @param Integer $TLID
     * @param String $month
     * @return Array
     */
    public function getSalesAccountByTL($TLID, $month)
    {
        $params = [
            'TLID'      => $TLID,
            'firstDate' => $month.'-01',
            'lastDate'  => $month.'-31'
        ];
        
        return DB::select(
            'SELECT 
                dealer_accounts.ID AS ID,
                dealer_accounts.name AS name, 
                SUM(reports.price * reports.quantity) AS total 
            FROM dealer_accounts
                JOIN dealers ON dealers.dealer_account_ID = dealer_accounts.ID
                LEFT JOIN reports 
                    ON reports.dealer_ID = dealers.ID
                        AND reports.date BETWEEN :firstDate AND :lastDate
            WHERE dealer_accounts.promotor_ID = :TLID
            GROUP BY dealer_accounts.ID',
            $params
        );
    }
    
    /**
     * Get sales target by branch using TLID
     *
     * @access public
     * @param Array $TLIDs
     * @param String $month
     * @return Array
     */
    public function getTargetSalesBranchByTL($TLIDs, $month)
    {
        $paramData      = [$month];
        $paramString    = [];
        
        foreach ($TLIDs as $item)
        {
            $paramData[]    = $item;
            $paramString[]  = '?';
        }
        
        return DB::select(
            'SELECT 
                branches.ID AS ID,
                branches.name AS name, 
                SUM(promotor_targets.total) AS total
            FROM branches
                JOIN dealers ON dealers.branch_ID = branches.ID
                LEFT JOIN promotor_targets 
                    ON promotor_targets.dealer_ID = dealers.ID
                        AND promotor_targets.month = ?
            WHERE branches.promotor_ID IN ('.implode(',', $paramString).')
            GROUP BY branches.ID',
            $paramData
        );
    }
    
    /**
     * Get sales by branch using TLID
     *
     * @access public
     * @param Array $TLIDs
     * @param String $month
     * @return Array
     */
    public function getSalesBranchByTL($TLIDs, $month)
    {
        $paramData      = [$month.'-01', $month.'-31'];
        $paramString    = [];
        
        foreach ($TLIDs as $item)
        {
            $paramData[]    = $item;
            $paramString[]  = '?';
        }
        
        return DB::select(
            'SELECT 
                branches.ID AS ID,
                branches.name AS name, 
                SUM(reports.price * reports.quantity) AS total 
            FROM branches
                JOIN dealers ON dealers.branch_ID = branches.ID
                LEFT JOIN reports 
                    ON reports.dealer_ID = dealers.ID
                        AND reports.date BETWEEN ? AND ?
            WHERE branches.promotor_ID IN ('.implode(',', $paramString).')
            GROUP BY branches.ID',
            $paramData
        );
    }

    /**
     * TARGET SALES TL (For App)
     *
     * @access public
     * @param Integer $tl_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesTl($tl_ID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.tl_ID', $tl_ID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.product_ID as product_ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * SALES ARCO (For App)
     *
     * @access public
     * @param Integer $tl_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getSalesTl($tl_ID)
    {
        $result = DB::table('reports')
                ->join('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', date('Y-m-01'))
                ->where('reports.date','<=', date('Y-m-31'))
                ->where('reports.tl_ID', $tl_ID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );
        
        return $result->get()->all();
    }


    /**
     * TARGET SALES Account (For App)
     *
     * @access public
     * @param Integer $account_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesAccount($account_ID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.account_ID', $account_ID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.product_ID as product_ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * SALES Account (For App)
     *
     * @access public
     * @param Integer $account_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getSalesAccount($account_ID)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', date('Y-m-01'))
                ->where('reports.date','<=', date('Y-m-31'))
                ->where('reports.account_ID', $account_ID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );
        
        return $result->get()->all();
    }

    /**
     * TARGET SALES DEALER (For App)
     *
     * @access public
     * @param Integer $dealer_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesDealer($dealer_ID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.dealer_ID', $dealer_ID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.product_ID as product_ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * SALES DEALER (For App)
     *
     * @access public
     * @param Integer $dealer_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getSalesDealer($dealer_ID)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', date('Y-m-01'))
                ->where('reports.date','<=', date('Y-m-31'))
                ->where('reports.dealer_ID', $dealer_ID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );
        
        return $result->get()->all();
    }

    /**
     * TARGET SALES PROMOTOR (For App)
     *
     * @access public
     * @param Integer $promotor_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getTargetSalesPromotor($promotor_ID, $month)
    {
        $query = DB::table('promotor_targets')
                ->where('promotor_targets.month', '=', $month)
                ->where('promotor_targets.promotor_ID', $promotor_ID)
                ->select(
                    'promotor_targets.ID as ID',
                    'promotor_targets.product_ID as product_ID',
                    'promotor_targets.total as total'
                );
        
        return $query->get()->all();
    }

    /**
     * SALES DEALER (For App)
     *
     * @access public
     * @param Integer $promotor_ID
     * @param Integer $dealerID
     * @param String $month
     * @return Array
     */
    public function getSalesPromotor($promotor_ID)
    {
        $result = DB::table('reports')
                ->leftJoin('product_models', 'product_models.ID', '=', 'reports.product_model_ID')
                ->where('reports.date','>=', date('Y-m-01'))
                ->where('reports.date','<=', date('Y-m-31'))
                ->where('reports.promotor_ID', $promotor_ID)
                ->select(
                    'reports.ID as ID',
                    'reports.dealer_ID as dealer_ID',
                    'reports.promotor_ID as promotor_ID',
                    'reports.product_model_ID as product_model_ID',
                    'reports.dealer_ID as dealer_ID',
                    'product_models.name as name',
                    'reports.custom_name as custom_name',
                    'reports.price as price',
                    'reports.quantity as quantity'
                );
        
        return $result->get()->all();
    }

    /**
     * Update report data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted report data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('reports')
            ->where('ID', $ID)
            ->update($data);
    }
    
    /**
     * Update promotor meta in reports when promotor move to another dealer
     *
     * @access public
     * @param Integer $promotorID
     * @param Integer $dealerID
     * @param Integer $accountID
     * @param Integer $TLID
     * @param Integer $arcoID
     * @return Void
     */
    public function updatePromotorMeta($promotorID, $dealerID, $accountID, $TLID, $arcoID)
    {
        DB::table('reports')
            ->where('promotor_ID', $promotorID)
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = "'.date('Y-m').'"')
            ->update([
                'dealer_ID'     => $dealerID,
                'account_ID'    => $accountID,
                'tl_ID'         => $TLID,
                'arco_ID'       => $arcoID
            ]);
    }

    /**
     * Remove a report
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('reports')
            ->where('ID', $ID)
            ->delete();
    }

}

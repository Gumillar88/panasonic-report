<?php

/**
 * Dashboard data module
 */

namespace App\Http\Models;

use DB;

class DashboardDataModel
{
    /**
     * Get first report date
     *
     * @access public
     * @return String
     */
    public function getFirstReportDate()
    {
        return DB::table('reports')->orderBy('date', 'asc')->value('date');
    }

    /**
     * get data account
     *
     * @access public
     * @return Void
     */
    public function getAccount()
    {
        return DB::select(
            'SELECT 
                DISTINCT reports.account_ID,
                dealer_accounts.ID AS ID,
                dealer_accounts.name AS name,
                reports.dealer_ID AS dealer_ID
            FROM reports 
                JOIN dealer_accounts 
                    ON dealer_accounts.ID = reports.account_ID'
        );
    }

    /**
     * get data dealer
     *
     * @access public
     * @return Void
     */
    public function getDealer()
    {
        return DB::select(
            'SELECT 
                DISTINCT reports.dealer_ID,
                dealers.ID AS ID,
                dealers.name AS name
            FROM reports 
                JOIN dealers 
                    ON dealers.ID = reports.dealer_ID'
        );
    }

    /**
     * Get sales data sales 
     *
     * @access public
     * @param Arrat $params
     * @param String $month
     * @param Array $params
     * @return Object
     */
    public function dataExploreSales($startDate, $finishDate, $params)
    {
        $parameter = [
            'startDate'     => $startDate,
            'finishDate'    => $finishDate
        ];
        
        $additionalQuery = '';
        
        if (array_key_exists('branch_ID', $params))
        {
            $parameter['branchID'] = $params['branch_ID'];
            $additionalQuery = 'AND branches.ID = :branchID';
        }

        $query ='SELECT 
                    branches.ID AS branch_ID,
                    branches.name AS branch_name,
                    dealer_accounts.ID AS account_ID,
                    dealer_accounts.name AS account_name,
                    dealers.ID AS dealer_ID,
                    dealers.name AS dealer_name,
                    SUM(reports.quantity * reports.price) AS total
                FROM reports 
                    LEFT OUTER JOIN dealer_accounts 
                        ON reports.account_ID = dealer_accounts.ID
                    JOIN dealers
                        ON reports.dealer_ID = dealers.ID
                    JOIN branches
                        ON branches.ID = dealers.branch_ID
                        '.$additionalQuery.'
                WHERE reports.date BETWEEN :startDate AND :finishDate';

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {   
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        }
        
        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {   
            $query .= ' AND reports.account_ID = :account_ID';
            
            $parameter['account_ID'] = $params['account_ID'];
        }

        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query .= ' AND reports.dealer_ID = :dealer_ID';
            $parameter['dealer_ID'] = $params['dealer_ID'];
        }

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query .= ' AND dealers.dealer_channel_ID = :dealer_channel_ID';            
            $parameter['dealer_channel_ID'] = $params['dealer_channel_ID'];
        }
        
        // Add grouping
        $query .= ' GROUP BY dealer_ID ORDER BY branch_ID ASC';


        return DB::select($query, $parameter);

    }
    
    
    /**
     * Get sales data sales by promotor
     *
     * @access public
     * @param String $startDate
     * @param String $finishDate
     * @param Integer $branchID (optional)
     * @return Object
     */
    public function dataExploreSalesPromotor($startDate, $finishDate, $params)
    {
        $parameter = [
            'startDate'     => $startDate,
            'finishDate'    => $finishDate
        ];
        
        $additionalQuery = '';
        
        if (array_key_exists('branch_ID', $params))
        {
            $parameter['branchID'] = $params['branch_ID'];
            $additionalQuery = 'AND branches.ID = :branchID';
        }

        $query ='SELECT 
                    promotors.ID AS promotor_ID,
                    promotors.name AS promotor_name,
                    reports.dealer_ID AS dealer_ID,
                    SUM(reports.quantity * reports.price) AS total
                FROM reports 
                    JOIN promotors
                        ON reports.promotor_ID = promotors.ID
                    JOIN dealers
                        ON reports.dealer_ID = dealers.ID
                    JOIN branches
                        ON branches.ID = dealers.branch_ID
                        '.$additionalQuery.'
                WHERE reports.date BETWEEN :startDate AND :finishDate';
        
        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {   
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        }
        
        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {   
            $query .= ' AND reports.account_ID = :account_ID';
            
            $parameter['account_ID'] = $params['account_ID'];
        }

        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query .= ' AND reports.dealer_ID = :dealer_ID';
            $parameter['dealer_ID'] = $params['dealer_ID'];
        }

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query .= ' AND dealers.dealer_channel_ID = :dealer_channel_ID';            
            $parameter['dealer_channel_ID'] = $params['dealer_channel_ID'];
        }
        
        // Add grouping
        $query .= ' GROUP BY promotor_ID, dealer_ID';

        return DB::select($query, $parameter);

    }

    /**
     * Get target data target 
     *
     * @access public
     * @param Array $months
     * @return Object
     */
    public function dataExploreTarget($months = [])
    {
        $params = [];
        
        foreach ($months as $month)
        {
            $params[] = '?';
        }

        $query = 'SELECT 
                    dealer_ID,
                    SUM(promotor_targets.total) AS total
                FROM promotor_targets
                WHERE promotor_targets.month IN ('.implode(',', $params).') GROUP BY dealer_ID';

        return DB::select($query, $months);

    }
    
    /**
     * Get target data target by promotor
     *
     * @access public
     * @param Array $months
     * @return Object
     */
    public function dataExploreTargetPromotor($months = [])
    {
        $params = [];
        
        foreach ($months as $month)
        {
            $params[] = '?';
        }

        $query = 'SELECT 
                    promotor_ID,
                    SUM(promotor_targets.total) AS total
                FROM promotor_targets
                WHERE promotor_targets.month IN ('.implode(',', $params).') GROUP BY promotor_ID';

        return DB::select($query, $months);

    }

    /**
     * Get gender data based on promoter
     *
     * @access public
     * @return Object
     */
    public function dataPromoterGender()
    {
        return DB::select(
            'SELECT 
                promotors.gender,
                promotors.dealer_ID,
                branches.name
            FROM promotors
                JOIN dealers
                    ON promotors.dealer_ID = dealers.ID
                JOIN branches
                    ON branches.ID = dealers.branch_ID'
        );
    }
    
    /**
     * Get chart data sales target
     *
     * @access public
     * @param Array $months
     * @param Array $params
     * @return Object
     */
    public function chartSalesTarget($params = [])
    {
        $query = DB::table('promotor_targets')
                    ->join('dealers', 'dealers.ID', '=', 'promotor_targets.dealer_ID')
                    ->join('branches', 'branches.ID', '=', 'dealers.branch_ID')
                    ->whereIn('promotor_targets.month', $params['targetMonths']);

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query->where('branches.region_ID', $params['region_ID']);
        } 

        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $query->where('dealers.branch_ID', $params['branch_ID']);
        } 

        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {
            $query->where('promotor_targets.account_ID', $params['account_ID']);
        } 

        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query->where('promotor_targets.dealer_ID', $params['dealer_ID']);
        }

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query->where('dealers.dealer_channel_ID', $params['dealer_channel_ID']);
        }

        return $query->select(DB::raw('SUM(total) AS total'))
            ->value('total');
    }
    
    /**
     * Get list daily sales trend based on month
     *
     * @access public
     * @param String $month
     * @param String $params
     * @return Array
     */
    public function chartSalesTrend($params)
    {
        $query = 
            'SELECT 
                date,
                SUM(quantity * price) As total
            FROM reports
                JOIN dealers 
                    ON dealers.ID = reports.dealer_ID
                JOIN branches 
                    ON branches.ID = dealers.branch_ID 
            WHERE reports.date BETWEEN :startDate AND :finishDate';

        $parameter = [
            'startDate'     => $params['startDate'],
            'finishDate'    => $params['finishDate']
        ];

        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {   
            $query .= ' AND reports.account_ID = :account_ID';
            
            $parameter['account_ID'] = $params['account_ID'];
        } 

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        } 

        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $query .= ' AND dealers.branch_ID = :branch_ID';
            $parameter['branch_ID'] = $params['branch_ID'];
        } 

        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query .= ' AND reports.dealer_ID = :dealer_ID';
            $parameter['dealer_ID'] = $params['dealer_ID'];
        }

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query .= ' AND dealers.dealer_channel_ID = :dealer_channel_ID';            
            $parameter['dealer_channel_ID'] = $params['dealer_channel_ID'];
        }

        $query .= ' GROUP BY date';
        return DB::select($query, $parameter);
    }

    /**
     * Get sales product based on product on month
     *
     * @access public
     * @param String $month
     * @param Array $params
     * @return Array
     */
    public function chartSalesProduct($params)
    {
        $query = 
            'SELECT 
                product_categories.ID AS ID,
                product_categories.name AS name,
                SUM(reports.quantity) AS qty,
                SUM(reports.quantity * reports.price) AS total
            FROM reports 
                JOIN product_models
                    ON product_models.ID = reports.product_model_ID
                JOIN product_categories
                    ON product_categories.ID = product_models.product_category_ID
                JOIN dealers 
                    ON dealers.ID = reports.dealer_ID
                JOIN branches 
                    ON branches.ID = dealers.branch_ID 
                WHERE reports.date BETWEEN :startDate AND :finishDate';

        $parameter = [
            'startDate'     => $params['startDate'],
            'finishDate'    => $params['finishDate']
        ];
        
        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query .= ' AND reports.dealer_ID = :dealer_ID';
            $parameter['dealer_ID'] = $params['dealer_ID'];
        }

        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {
            $query .= ' AND reports.account_ID = :account_ID';
            $parameter['account_ID'] = $params['account_ID'];
        } 

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        } 

        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $query .= ' AND dealers.branch_ID = :branch_ID';
            $parameter['branch_ID'] = $params['branch_ID'];
        } 

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query .= ' AND dealers.dealer_channel_ID = :dealer_channel_ID';            
            $parameter['dealer_channel_ID'] = $params['dealer_channel_ID'];
        }

        $query .= ' GROUP BY ID
                    ORDER BY total DESC';
        return DB::select($query, $parameter);
    }
    
    /**
     * Get sales detail based on account on montj
     *
     * @access public
     * @param String $month
     * @return Array
     */
    public function chartSalesAccount($params)
    {
        $query ='SELECT 
                    dealers.ID AS ID,
                    dealers.name AS name,
                    branches.name AS branch,
                    SUM(reports.quantity * reports.price) AS total
                FROM reports 
                    JOIN dealers 
                        ON dealers.ID = reports.dealer_ID
                    JOIN branches 
                        ON branches.ID = dealers.branch_ID
                    WHERE reports.date BETWEEN :startDate AND :finishDate';

        $parameter = [
            'startDate'     => $params['startDate'],
            'finishDate'    => $params['finishDate']
        ];

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        } 

        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {
            $query .= ' AND dealers.dealer_account_ID = :account_ID';
            $parameter['account_ID'] = $params['account_ID'];
        } 


        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $query .= ' AND dealers.branch_ID = :branch_ID';
            $parameter['branch_ID'] = $params['branch_ID'];
        } 
        
        $query .= ' GROUP BY ID ORDER BY total DESC';

        return DB::select($query, $parameter);
    }
    
    /**
     * Get sales detail per dealer based on account ID
     *
     * @access public
     * @param String $month
     * @param Integer $accountID
     * @return Array
     */
    public function chartSalesDealer($month, $accountID)
    {
        return DB::select(
            'SELECT 
                dealers.ID AS ID,
                dealers.name AS name,
                SUM(reports.quantity * reports.price) AS total
            FROM reports 
                JOIN dealers 
                    ON dealers.ID = reports.dealer_ID
            WHERE reports.date BETWEEN :startDate AND :finishDate
                AND reports.account_ID = :accountID
            GROUP BY ID',
            [
                'accountID'     => $accountID,
                'startDate'     => $month.'-01',
                'finishDate'    => $month.'-31'
            ]
        );
    }
    
    /**
     * Get sales detail per promoter based on dealer ID
     *
     * @access public
     * @param String $month
     * @param Integer $dealerID
     * @return Array
     */
    public function chartSalesPromoter($month, $dealerID)
    {
        return DB::select(
            'SELECT 
                promoters.ID AS ID,
                promoters.name AS name,
                SUM(reports.quantity * reports.price) AS total
            FROM reports 
                JOIN promoters 
                    ON promoters.ID = reports.promoters_ID
            WHERE reports.date BETWEEN :startDate AND :finishDate
                AND reports.dealer_ID = :dealerID
            GROUP BY ID',
            [
                'dealerID'      => $dealerID,
                'startDate'     => $month.'-01',
                'finishDate'    => $month.'-31'
            ]
        );
    }

    /**
     * Get channel detail  based on report ID
     *
     * @access public
     * @param Array $params
     * @return Array
     */
    public function chartSalesChannel($params)
    {
        $query ='SELECT 
                    dealer_channels.ID AS ID,
                    dealer_channels.name AS name,
                    SUM(reports.quantity * reports.price) AS total
                FROM reports 
                    JOIN dealers 
                        ON dealers.ID = reports.dealer_ID
                    JOIN branches 
                        ON branches.ID = dealers.branch_ID
                    JOIN dealer_channels 
                        ON dealer_channels.ID = dealers.dealer_channel_ID
                    WHERE reports.date BETWEEN :startDate AND :finishDate';

        $parameter = [
            'startDate'     => $params['startDate'],
            'finishDate'    => $params['finishDate']
        ];

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        } 

        $query .= ' GROUP BY ID';

        return DB::select($query, $parameter);
    }
    
    /**
     * Get data empty stock
     *
     * @access public
     * @return Object
     */
    public function stockGet()
    {
        return DB::select(
            'SELECT 
                dealers.ID AS dealer_ID,
                branches.name AS branch_name,
                report_empty_stock.product_model_ID AS ID,
                product_models.name AS product_name,
                dealers.name AS dealer_name
            FROM report_empty_stock 
                JOIN product_models 
                    ON product_models.ID = report_empty_stock.product_model_ID
                JOIN dealers 
                    ON dealers.ID = report_empty_stock.dealer_ID
                JOIN branches
                    ON branches.ID = dealers.branch_ID
            WHERE report_empty_stock.resolver_ID = 0'
        );


    }

    /**
     * Get data product
     *
     * @access public
     * @return Object
     */
    public function productGet($params)
    {
        $query = 
            'SELECT 
                product_models.ID AS ID,
                product_models.name AS name,
                SUM(reports.quantity) AS qty,
                SUM(reports.quantity * reports.price) AS total
            FROM reports 
                JOIN product_models
                    ON product_models.ID = reports.product_model_ID
                JOIN product_categories
                    ON product_categories.ID = product_models.product_category_ID
                JOIN dealers 
                    ON dealers.ID = reports.dealer_ID
                JOIN branches 
                    ON branches.ID = dealers.branch_ID
            WHERE product_categories.ID = :category_ID
            AND reports.date BETWEEN :startDate AND :finishDate';
        
        $parameter = [
            'category_ID'   => $params['category_ID'],
            'startDate'     => $params['startDate'],
            'finishDate'    => $params['finishDate']
        ];

        // Check if account id in parameter
        if (array_key_exists('account_ID', $params))
        {
            $query .= ' AND reports.account_ID = :account_ID';
            $parameter['account_ID'] =  $params['account_ID'];
        }

        // Check if dealer id in parameter
        if (array_key_exists('dealer_ID', $params))
        {
            $query .= ' AND reports.dealer_ID = :dealer_ID';
            $parameter['dealer_ID'] =  $params['dealer_ID'];
        }

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $query .= ' AND branches.region_ID = :region_ID';
            $parameter['region_ID'] = $params['region_ID'];
        } 

        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $query .= ' AND dealers.branch_ID = :branch_ID';
            $parameter['branch_ID'] = $params['branch_ID'];
        } 

        // Check if channel id in parameter
        if (array_key_exists('dealer_channel_ID', $params))
        {
            $query .= ' AND dealers.dealer_channel_ID = :dealer_channel_ID';            
            $parameter['dealer_channel_ID'] = $params['dealer_channel_ID'];
        }

        $query .= ' GROUP BY ID
            ORDER BY total DESC';

        
        return DB::select($query, $parameter);
             
    }
    
    /**
     * Generate report data for excel
     *
     * @access public
     * @param Array $params
     * @return Void
     */
    public function report($params)
    {    
        $accountQuery = '';
        
        $parameter = [
            'startDate'     => $params['month'].'-01',
            'finishDate'    => $params['month'].'-31',
        ];


        // Check if account id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $accountQuery       = 'LEFT OUTER ';
            $additionalQuery = ' AND dealers.branch_ID = :branch_ID';
            
            $parameter['branch_ID'] = $params['branch_ID'];
        }   

        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $accountQuery       = 'LEFT OUTER ';
            $additionalQuery = ' AND branches.region_ID = :region_ID';
            
            $parameter['region_ID'] = $params['region_ID'];
        }  
        
        // Check if there is account ID
        if (array_key_exists('account_ID', $params))
        {
            $additionalQuery   = ' AND dealers.dealer_account_ID = :account_ID';
            
            $parameter['account_ID'] = $params['account_ID'];
        } 
        
        if (array_key_exists('account_name', $params))
        {
            $accountQuery      = 'LEFT OUTER ';
            $additionalQuery   = ' AND dealers.name LIKE :account_name';
            
            $parameter['account_name'] = '%'.str_replace('-', ' ', $params['account_name']).'%';
        } 
        
        
        $query = 
            'SELECT 
                branches.name AS branch_name,
                team_leaders.name AS team_leader,
                DATE_FORMAT(date, "%b-%y") AS period,
                DATE_FORMAT(date, "%e-%b-%y") AS date_string,
                reports.date AS date,
                customers.name AS customer_name,
                customers.phone AS customer_phone,
                promotors.created AS created,
                promotors.name AS promotor_name,
                reports.promotor_ID, 
                dealer_accounts.name AS dealer_account_name,
                dealers.name AS dealer_name,
                product_categories.name AS category_name,
                product_models.name AS product_name,
                reports.custom_name AS custom_product_name,
                product_incentives.value AS incentive,
                reports.quantity AS qty,
                reports.price AS price
            FROM reports
                '.$accountQuery.'JOIN dealer_accounts ON
                    reports.account_ID = dealer_accounts.ID
                JOIN dealers ON
                    reports.dealer_ID = dealers.ID
                JOIN branches ON
                    branches.ID = dealers.branch_ID
                LEFT OUTER JOIN product_models ON
                    reports.product_model_ID = product_models.ID
                LEFT OUTER JOIN product_categories ON
                    product_models.product_category_ID = product_categories.ID
                LEFT OUTER JOIN product_incentives ON
                    product_models.ID = product_incentives.product_model_ID
                LEFT OUTER JOIN customers ON
                    reports.customer_ID = customers.ID
                JOIN promotors ON
                    reports.promotor_ID = promotors.ID
                JOIN promotors AS team_leaders ON
                    promotors.parent_ID = team_leaders.ID
            WHERE reports.date BETWEEN :startDate AND :finishDate'.$additionalQuery.' ORDER BY reports.date ASC';
        
        return DB::select($query, $parameter);

    }
    
    /**
     * Generate data for report pivot table
     *
     * @access public
     * @param Array $params
     * @return Array
     */
    public function reportPivot($params)
    {
        $querySummary =  'SELECT 
                            dealers.name AS dealer_name,
                            SUM(reports.quantity) AS sum_qty,
                            SUM(reports.quantity*reports.price) AS sum_selling_price
                        FROM reports
                        JOIN dealers ON
                            reports.dealer_ID = dealers.ID
                        JOIN branches ON
                            branches.ID = dealers.branch_ID
                        JOIN promotors ON
                            reports.promotor_ID = promotors.ID
                        WHERE reports.date BETWEEN :startDate AND :finishDate
                            AND promotors.type IN ("promotor", "non-active")';  
        $parameterSummary = [
            'startDate'     => $params['month'].'-01',
            'finishDate'    => $params['month'].'-31',
        ];


        $queryTarget = 'SELECT 
                            dealers.name AS dealer_name,
                            SUM(promotor_targets.total) AS total_target
                        FROM promotor_targets
                        JOIN dealers ON
                            promotor_targets.dealer_ID = dealers.ID
                        JOIN branches ON
                            branches.ID = dealers.branch_ID
                        JOIN promotors ON
                            promotor_targets.promotor_ID = promotors.ID
                        WHERE promotor_targets.month = :month
                            AND promotors.type IN ("promotor", "non-active")';  
        $parameterTarget = [
            'month'     => $params['month'],
        ];
        
        

        $queryPromotor = 'SELECT 
                                COUNT(promotor_targets.promotor_ID) AS total_promotor,
                                dealers.ID AS dealerID, 
                                dealers.name AS dealer_name
                            FROM promotor_targets
                            JOIN dealers 
                                ON dealers.ID = promotor_targets.dealer_ID
                            JOIN branches ON
                                branches.ID = dealers.branch_ID
                            JOIN promotors ON
                                promotor_targets.promotor_ID = promotors.ID
                            WHERE promotor_targets.month = :month
                            AND promotors.type IN ("promotor", "non-active")';  
        $paramaterPromotor = [
            'month'     => $params['month'],
        ];


        // Check if region id in parameter
        if (array_key_exists('region_ID', $params))
        {
            $querySummary   .= ' AND branches.region_ID = :region_ID';
            $queryTarget    .= ' AND branches.region_ID = :region_ID';
            $queryPromotor  .= ' AND branches.region_ID = :region_ID';
            
            $parameterSummary['region_ID'] = $params['region_ID'];
            $parameterTarget['region_ID'] = $params['region_ID'];
            $paramaterPromotor['region_ID'] = $params['region_ID'];
        }  

        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $querySummary   .= ' AND dealers.branch_ID = :branch_ID';
            $queryTarget    .= ' AND dealers.branch_ID = :branch_ID';
            $queryPromotor  .= ' AND dealers.branch_ID = :branch_ID';
            
            $parameterSummary['branch_ID'] = $params['branch_ID'];
            $parameterTarget['branch_ID'] = $params['branch_ID'];
            $paramaterPromotor['branch_ID'] = $params['branch_ID'];
        }
        
        // Check if there is account ID
        if (array_key_exists('account_ID', $params))
        {
            $querySummary   .= ' AND dealers.dealer_account_ID = :account_ID';
            $queryTarget    .= ' AND dealers.dealer_account_ID = :account_ID';
            $queryPromotor  .= ' AND dealers.dealer_account_ID = :account_ID';
            
            $parameterSummary['account_ID'] = $params['account_ID'];
            $parameterTarget['account_ID'] = $params['account_ID'];
            $paramaterPromotor['account_ID'] = $params['account_ID'];
        }
        
        
        if (array_key_exists('account_name', $params))
        {
            $querySummary   .= ' AND dealers.name LIKE :account_name';
            $queryTarget    .= ' AND dealers.name LIKE :account_name';
            $queryPromotor  .= ' AND dealers.name LIKE :account_name';
            
            $words = '%'.str_replace('-', ' ', $params['account_name']).'%';
            
            $parameterSummary['account_name']   = $words;
            $parameterTarget['account_name']    = $words;
            $paramaterPromotor['account_name']  = $words;
        }

        $querySummary   .= ' GROUP BY reports.dealer_ID';
        $queryTarget    .= ' GROUP BY promotor_targets.dealer_ID';
        $queryPromotor  .= ' GROUP BY dealerID';
        
        $data = [
            'summary'   => DB::select($querySummary, $parameterSummary),
            'target'    => DB::select($queryTarget, $parameterTarget),
            'promotor'  => DB::select($queryPromotor, $paramaterPromotor)
        ];
        
        return $data;
    }

    /**
     * Generate data for report pivot table
     *
     * @access public
     * @param Array $params
     * @return Array
     */
    public function reportPromotorTarget($params)
    {
        $queryTarget = 'SELECT 
                            promotors.ID AS promotor_ID,
                            promotors.name AS promotor_name,
                            SUM(promotor_targets.total) AS total_target
                        FROM promotor_targets
                        JOIN dealers ON
                            promotor_targets.dealer_ID = dealers.ID
                        JOIN promotors ON
                            promotor_targets.promotor_ID = promotors.ID
                        WHERE promotor_targets.month = :month
                            AND promotors.type = "promotor"';  
        $parameterTarget = [
            'month'     => $params['month'],
        ];


        // Check if branch id in parameter
        if (array_key_exists('branch_ID', $params))
        {
            $queryTarget    .= ' AND dealers.branch_ID = :branch_ID';
            $parameterTarget['branch_ID'] = $params['branch_ID'];
        }
        
        // Check if there is account ID
        if (array_key_exists('account_ID', $params))
        {
            $queryTarget    .= ' AND dealers.dealer_account_ID = :account_ID';
            $parameterTarget['account_ID'] = $params['account_ID'];
        }
        
        
        if (array_key_exists('account_name', $params))
        {
            $queryTarget    .= ' AND dealers.name LIKE :account_name';            
            $words = '%'.str_replace('-', ' ', $params['account_name']).'%';
            
            $parameterTarget['account_name']    = $words;
        }

        $queryTarget    .= ' GROUP BY promotor_targets.promotor_ID';
        
        return DB::select($queryTarget, $parameterTarget);
    }

}

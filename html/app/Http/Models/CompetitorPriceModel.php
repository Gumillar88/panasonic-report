<?php

/**
 * Competitor brand data module
 */

namespace App\Http\Models;

use DB;

class CompetitorPriceModel
{
    /**
     * Create new brand
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
            'promotor_ID',
            'dealer_ID',
            'product_model_ID',
            'competitor_brand_ID',
            'competitor_brand_custom',
            'product_category_ID',
            'model_name',
            'price_normal',
            'price_promo',
            'date',
        ];
        
        foreach ($validKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                throw new Exception('Report Competitor Price Model: Key "'.$key.'" is not found on data parameter');
            }
            
            $finalData[$key] = $data[$key];
        }
        
        return DB::table('competitor_prices')->insertGetId($finalData);
    }

    /**
     * Get one competitor price data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('competitor_prices')
            ->where('ID', $ID)
            ->first();
    }
    
    /**
     * Get compiled competitor price for index
     *
     * @access public
     * @param String $month
     * @param Integer $brandID (optional)
     * @param String $type (optional)
     * @param String $value (optional)
     * @return Array
     */
    public function getCompiledIndex($month, $brandID = false, $type = '', $value = '')
    {
        // Set params
        $params = [
            'month' => $month
        ];
        
        // Define additional brand query
        $brandQuery = '';
        
        if ($brandID)
        {
            $params['brandID']  = $brandID;
            $brandQuery         = 'AND competitor_prices.competitor_brand_ID = :brandID ';
        }
        
        // Define additional type query
        $typeQuery = '';
        
        if ($type === 'branch')
        {
            $typeQuery = 'AND branches.ID = :value ';
            $params['value'] = $value;
        }
        else if ($type === 'account')
        {
            $typeQuery = 'AND dealer_accounts.ID = :value ';
            $params['value'] = $value;
        }
        else if ($type === 'account-all')
        {
            $typeQuery = 'AND dealers.name LIKE :value ';
            $params['value'] = '%'.str_replace('-', ' ', $value).'%';
        }
        
        $query = 
            'SELECT 
                competitor_prices.ID AS ID,
                product_models.name AS product_model,
                competitor_brands.name AS brand,
                dealer_accounts.name AS account,
                competitor_prices.competitor_brand_custom AS brand_custom,
                dealers.name AS dealer,
                branches.name AS branch,
                product_categories.name AS category,
                competitor_prices.model_name AS model_name,
                competitor_prices.price_normal AS price_normal,
                competitor_prices.price_promo AS price_promo,
                competitor_prices.date AS date,
                DATE_FORMAT(competitor_prices.date, "%Y-%m") AS month
            FROM competitor_prices 
                LEFT JOIN competitor_brands
                    ON competitor_brands.ID = competitor_prices.competitor_brand_ID
                JOIN dealers 
                    ON competitor_prices.dealer_ID = dealers.ID
                LEFT JOIN product_models
                    ON product_models.ID = competitor_prices.product_model_ID
                LEFT JOIN dealer_accounts
                    ON dealer_accounts.ID = dealers.dealer_account_ID
                JOIN branches
                    ON branches.ID = dealers.branch_ID
                JOIN product_categories
                    ON product_categories.ID = competitor_prices.product_category_ID
            WHERE DATE_FORMAT(competitor_prices.date, "%Y-%m") = :month '.$brandQuery.$typeQuery;
        
        return DB::select($query, $params);
    }
    
    /**
     * Get list of competitor price data based on promotor ID and date
     *
     * @access public
     * @param Integer $promotorID
     * @param String $date
     * @return Array
     */
    public function getListByPromotor($promotorID, $date)
    {
        return DB::select(
            'SELECT 
                competitor_prices.ID AS ID,
                competitor_brands.name AS brand,
                competitor_prices.competitor_brand_custom AS brand_custom,
                product_categories.name AS category,
                competitor_prices.model_name AS model_name
            FROM competitor_prices 
                LEFT JOIN competitor_brands
                    ON competitor_brands.ID = competitor_prices.competitor_brand_ID
                JOIN product_categories
                    ON product_categories.ID = competitor_prices.product_category_ID
            WHERE 
                competitor_prices.date = :date 
                AND 
                competitor_prices.promotor_ID = :promotorID',
            [
                'promotorID'    => $promotorID,
                'date'          => $date
            ]
        );
    }
    
    /**
     * Update price data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted competitor price data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('competitor_prices')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove price data
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('competitor_prices')
            ->where('ID', $ID)
            ->delete();
    }

}
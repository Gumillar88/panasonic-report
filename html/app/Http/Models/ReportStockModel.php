<?php

/**
 * Empty Stock report data module
 */

namespace App\Http\Models;

use DB;

class ReportStockModel
{
    /**
     * Create empty stock report
     *
     * @access public
     * @param Integer $promotorID
     * @param Integer $dealerID
     * @param Integer $productModelID
     * @return Void
     */
    public function create($promotorID, $dealerID, $productModelID)
    {
        // Set time
        $time = time();
        
        return DB::table('report_empty_stock')->insertGetId([
            'promotor_ID'       => $promotorID,
            'resolver_ID'       => 0,
            'dealer_ID'         => $dealerID,
            'product_model_ID'  => $productModelID,
            'created'           => $time,
            'updated'           => 0
        ]);
    }

    /**
     * Get one empty stock report data
     *
     * @access public
     * @param Integer $product_model_ID
     * @param Integer $dealer_ID
     * @return Object
     */
    public function getOne($product_model_ID, $dealer_ID)
    {
        return DB::table('report_empty_stock')
            ->where('product_model_ID', $product_model_ID)
            ->where('dealer_ID', $dealer_ID)
            ->where('updated', 0)
            ->first();
    }

    /**
     * Get all empty stock report with pagination use timestamp as parameter
     *
     * @access public
     * @param Integer $dealerID
     * @param Integer $timestamp
     * @return Array
     */
    public function getPagination($dealerID, $timestamp)
    {   
        return DB::table('report_empty_stock')
            ->join('product_models', 'product_models.ID', '=', 'report_empty_stock.product_model_ID')
            ->join('product_categories', 'product_categories.ID', '=', 'product_models.product_category_ID')
            ->where('report_empty_stock.created', '<', $timestamp)
            ->where('report_empty_stock.updated', 0)
            ->where('report_empty_stock.dealer_ID', $dealerID)
            ->limit(11)
            ->select(
                    'report_empty_stock.ID as ID',
                    'report_empty_stock.promotor_ID as promotor_ID',
                    'report_empty_stock.dealer_ID as dealer_ID',
                    'report_empty_stock.product_model_ID as product_model_ID',
                    'product_categories.name as category_name',
                    'product_models.name as name'
                )
            ->orderBy('report_empty_stock.created', 'asc')
            ->get()->all();
    }

    /**
     * Update empty stock report data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data
     * @return Void
     */
    public function update($ID, $data)
    {
        DB::table('report_empty_stock')
            ->where('ID', $ID)
            ->update($data);
    }
}

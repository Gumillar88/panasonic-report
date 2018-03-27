<?php

/**
 * Promotor Target data module
 */

namespace App\Http\Models;

use DB;

class PromotorTargetModel
{
    /**
     * Create new promotor target
     *
     * @access public
     * @param Integer $promotorID
     * @param Integer $dealerID
     * @param Integer $accountID
     * @param Integer $TLID
     * @param Integer $arcoID
     * @param Integer $total
     * @param String $date
     * @return Integer
     */
    public function create($promotorID, $dealerID, $accountID, $TLID, $arcoID, $total, $date)
    {
        $time = time();
        
        return DB::table('promotor_targets')->insertGetId([
            'promotor_ID'       => $promotorID,
            'dealer_ID'         => $dealerID,
            'account_ID'        => $accountID,
            'tl_ID'             => $TLID,
            'arco_ID'           => $arcoID,
            'product_ID'        => 0,
            'total'             => $total,
            'month'             => $date,
            'created'           => $time,
            'updated'           => $time
        ]);
    }
    
    /**
     * Get one target data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('promotor_targets')
                ->where('ID', $ID)
                ->first();
    }
    
    /**
     * Get one target data by promotor
     *
     * @access public
     * @param Integer $promotorID
     * @param String $month
     * @return Object
     */
    public function getByPromotor($promotorID, $month)
    {
        return DB::table('promotor_targets')
                ->where('month', $month)
                ->where('promotor_ID', $promotorID)
                ->first();
    }

    /**
     * Get All target data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getAll()
    {
        return DB::table('promotor_targets')->get()->all();
    }
    
    /**
     * Get target data by dealer
     *
     * @access public
     * @param Integer $dealerID
     * @param String $date
     * @return Array
     */
    public function getAllByDealer($dealerID, $date)
    {
        return DB::table('promotor_targets')
                ->where('month', $date)
                ->where('dealer_ID', $dealerID)
                ->get()
                ->all();
    }
    
    /**
     * Get all list of target data based on month
     *
     * @access public
     * @param String $date
     * @return Array
     */
    public function getAllByMonth($date)
    {
        return DB::table('promotor_targets')
                ->where('month', $date)
                ->get()
                ->all();
    }
    

    /**
     * Update region data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted region data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('promotor_targets')
            ->where('ID', $ID)
            ->update($data);
    }
    
    /**
     * Update promotor meta in promotor target when promotor move to another dealer
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
        DB::table('promotor_targets')
            ->where('promotor_ID', $promotorID)
            ->where('month', date('Y-m'))
            ->update([
                'dealer_ID'     => $dealerID,
                'account_ID'    => $accountID,
                'tl_ID'         => $TLID,
                'arco_ID'       => $arcoID
            ]);
    }
    
    /**
     * Remove promotor target data based on promotor ID
     *
     * @access public
     * @param Integer $promotorID
     * @param String $date
     * @return Void
     */
    public function removeByPromotor($promotorID, $month)
    {
        DB::table('promotor_targets')
            ->where('promotor_ID', $promotorID)
            ->where('month', $month)
            ->delete();
    }

}

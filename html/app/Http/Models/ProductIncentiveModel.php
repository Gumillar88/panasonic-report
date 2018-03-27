<?php

/**
 * Product incentive data module
 */

namespace App\Http\Models;

use DB;

class ProductIncentiveModel
{
    /**
     * Create new product incentive
     *
     * @access public
     * @param Integer $dealerChannelID
     * @param Integer $productModelID
     * @param Integer $value
     * @return Integer
     */
    public function create($dealerChannelID, $productModelID, $value)
    {
        $time = time();
        
        return DB::table('product_incentives')->insertGetId([
            'dealer_channel_ID'     => $dealerChannelID,
            'product_model_ID'      => $productModelID,
			'value'  		        => $value,
            'created'   	        => $time,
            'updated'               => $time,
        ]);
    }
    
    
    /**
     * Get one product incentive data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('product_incentives')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get all product incentive data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('product_incentives')->get()->all();
    }
    
    /**
     * Get product incentive data by dealer channel ID and product model ID
     *
     * @access public
     * @param Integer $dealerChannelID
     * @param Integer $productModelID
     * @return Object
     */
    public function getByChannelModel($dealerChannelID, $productModelID)
    {
        return DB::table('product_incentives')
                ->where('dealer_channel_ID', $dealerChannelID)
                ->where('product_model_ID', $productModelID)
                ->first();
    }

    /**
     * Update product incentive data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted product incentive data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('product_incentives')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove product incentive
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('product_incentives')
            ->where('ID', $ID)
            ->delete();
        
    }

}

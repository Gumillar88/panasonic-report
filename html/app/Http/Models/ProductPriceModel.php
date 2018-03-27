<?php

/**
 * Product price data module
 */

namespace App\Http\Models;

use DB;

class ProductPriceModel
{
    /**
     * Create new product price
     *
     * @access public
     * @param Integer $dealer_type_ID
     * @param Integer $dealer_channel_ID
     * @param Integer $product_ID
     * @param Integer $price
     * @return Integer
     */
    public function create($dealer_type_ID, $dealer_channel_ID, $product_ID, $price)
    {
        $time = time();
        
        return DB::table('product_price')->insertGetId([
            'dealer_type_ID'        => $dealer_type_ID,
            'dealer_channel_ID'     => $dealer_channel_ID,
            'product_ID'            => $product_ID,
			'price'  		        => $price,
            'created'   	        => $time,
            'updated'               => $time,
        ]);
    }
    
    
    /**
     * Get one product price data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('product_price')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get price by dealer type ID
     *
     * @access public
     * @param Integer $dealer_type_ID
     * @return Array
     */
    public function getDealerType($dealer_type_ID)
    {
        return DB::table('product_price')
            ->where('dealer_type_ID', $dealer_type_ID)
            ->get()
            ->all();
    }
    
    /**
     * Get price by dealer type and product ID
     *
     * @access public
     * @param Integer $dealerTypeID
     * @param Integer $productID
     * @param Integer $dealerChannelID
     * @return Object
     */
    public function getDealerProduct($dealerID, $productID, $dealerChannelID)
    {
        return DB::table('product_price')
            ->join('dealers', 'dealers.dealer_type_ID', '=', 'product_price.dealer_type_ID')
            ->where('dealers.ID', $dealerID)
            ->where('product_price.product_ID', $productID)
            ->where('product_price.dealer_channel_ID', $dealerChannelID)
            ->select('product_price.price as price')
            ->first();
    }

    /**
     * Get price by dealer type and dealer channel
     *
     * @access public
     * @param Integer $dealer_type_ID
     * @param Integer $dealer_channel_ID
     * @param Integer $product_ID
     * @return Object
     */
    public function getDealerProductPrice($dealer_type_ID, $dealer_channel_ID ,$product_ID)
    {
        return DB::table('product_price')
            ->where('dealer_type_ID', $dealer_type_ID)
            ->where('dealer_channel_ID', $dealer_channel_ID)
            ->where('product_ID', $product_ID)
            ->first();
    }

    /**
     * Get all product price data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('product_price')->get()->all();
    }

    /**
     * Update product price data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted product price data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('product_price')
            ->where('ID', $ID)
            ->update($data);
    }
    
    /** 
     * Get latest timestamp of updated data
     *
     * @access public
     * @return Integer
     */
    public function getLatestTimestamp()
    {
        return DB::table('product_price')->orderBy('updated', 'desc')->value('updated');
    }

    /**
     * Remove product price
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('product_price')
            ->where('ID', $ID)
            ->delete();
        
    }
    
    /**
     * Record product price change
     *
     * @access public
     * @return Void
     */
    public function log($productID, $productTypeID, $productChannelID, $oldPrice, $newPrice)
    {
        DB::table('product_price_logs')->insert([
            'product_ID'            => $productID,
            'product_type_ID'       => $productTypeID,
            'product_channel_ID'    => $productChannelID,
            'old'                   => $oldPrice,
            'new'                   => $newPrice,
            'created'               => time()
        ]);
    }

}

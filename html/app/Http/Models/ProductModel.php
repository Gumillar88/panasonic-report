<?php

/**
 * Product data module
 */

namespace App\Http\Models;

use DB;

class ProductModel
{
    /**
     * Create new product
     *
     * @access public
     * @param Integer $product_category_ID
     * @param String $name
     * @param Integer $price
     * @return Integer
     */
    public function create($product_category_ID, $name, $price)
    {
        $time = time();
        
        return DB::table('product_models')->insertGetId([
            'product_category_ID'       => $product_category_ID,
            'name'                  => $name,
            'price'                 => $price,
            'created'               => $time,
            'updated'               => $time
        ]);
    }

    /**
     * Get one product data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('product_models')
                ->where('ID', $ID)
                ->first();
    }

    /**
     * Get all product data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('product_models')->get()->all();
    }

    /**
     * Get all product data by type
     *
     * @access public
     * @return Array
     */
    public function getByCategory($category_ID)
    {
        return DB::table('product_models')
            ->where('product_category_ID',$category_ID)
            ->get()
            ->all();
    }

    /**
     * Get all product data by type
     *
     * @access public
     * @return Array
     */
    public function getByProduct($category_ID, $name)
    {
        return DB::table('product_models')
            ->where('product_category_ID',$category_ID)
            ->where('name',$name)
            ->first();
    }
    

    /**
     * Get all product data by type
     *
     * @access public
     * @return Array
     */
    public function getByProductName($name)
    {
        return DB::table('product_models')
            ->where('name',$name)
            ->first();
    }

    /** 
     * Get latest timestamp of update data
     *
     * @access public
     * @return Integer
     */
    public function getLatestTimestamp()
    {
        return DB::table('product_models')->orderBy('updated', 'desc')->value('updated');
    }

    /**
     * Update product data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted product data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('product_models')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove product
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('product_models')->where('ID', $ID)->delete();
    }

}

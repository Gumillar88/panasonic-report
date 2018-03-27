<?php

/**
 * Product category data module
 */

namespace App\Http\Models;

use DB;

class ProductCategoryModel
{
    /**
     * Create new Product category
     *
     * @access public
     * @param String $name
     * @return Integer
     */
    public function create($name)
    {
        $time = time();
        
        return DB::table('product_categories')->insertGetId([
			'name'  		=> $name,
            'created'   	=> $time,
            'updated'   	=> $time
        ]);
    }

    /**
     * Get one Product category data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('product_categories')->where('ID', $ID)->first();
    }

    /**
     * Get one Product category data
     *
     * @access public
     * @param String $name
     * @return Object
     */
    public function getCategory($name)
    {
        return DB::table('product_categories')->where('name', $name)->first();
    }

    /**
     * Get all Product category data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('product_categories')->get()->all();
    }
    
    /** 
     * Get latest timestamp of updated data
     *
     * @access public
     * @return Integer
     */
    public function getLatestTimestamp()
    {
        return DB::table('product_categories')->orderBy('updated', 'desc')->value('updated');
    }

    /**
     * Update Product category data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted Product category data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('product_categories')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove Product category
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('product_categories')->where('ID', $ID)->delete();
    }

}

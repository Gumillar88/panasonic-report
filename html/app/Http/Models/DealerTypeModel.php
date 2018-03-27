<?php

/**
 * Dealer type data module
 */

namespace App\Http\Models;

use DB;

class DealerTypeModel
{
    /**
     * Create new dealer type
     *
     * @access public
     * @param String $name
     * @return Integer
     */
    public function create($name)
    {
        $time = time();
        
        return DB::table('dealer_types')->insertGetId([
			'name'  		=> $name,
            'created'   	=> $time,
            'updated'       => $time,
        ]);
    }
    
    /**
     * Get one dealer type data
     *
     * @access public
     * @param String $name
     * @return Object
     */
    public function getDealertType($name)
    {
        return DB::table('dealer_types')->where('name', $name)->first();
    }
    
    /**
     * Get one dealer type data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dealer_types')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get all dealer type data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('dealer_types')->get()->all();
    }

    /**
     * Update dealer data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted dealer type data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('dealer_types')
            ->where('ID', $ID)
            ->update($data);
    }
    
    /**
     * Remove dealer type
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('dealer_types')
            ->where('ID', $ID)
            ->delete();
        
    }

}

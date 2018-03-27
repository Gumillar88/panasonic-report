<?php

/**
 * Dealer channel data module
 */

namespace App\Http\Models;

use DB;

class DealerChannelModel
{
    /**
     * Create new dealer channels
     *
     * @access public
     * @param String $name
     * @return Integer
     */
    public function create($name)
    {
        $time = time();
        
        return DB::table('dealer_channels')->insertGetId([
			'name'  		=> $name,
            'created'   	=> $time,
            'updated'       => $time,
        ]);
    }
    
    
    /**
     * Get one dealer channels data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dealer_channels')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get all dealer channels data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('dealer_channels')->get()->all();
    }

    /**
     * Update dealer data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted dealer channels data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('dealer_channels')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove dealer channels
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('dealer_channels')
            ->where('ID', $ID)
            ->delete();
        
    }

}

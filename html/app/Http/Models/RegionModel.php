<?php

/**
 * Region data module
 */

namespace App\Http\Models;

use DB;

class RegionModel
{
    /**
     * Create new region
     *
     * @access public
     * @param String $name
     * @param Integer $promotor_ID
     * @return Integer
     */
    public function create($name, $promotor_ID)
    {
        $time = time();
        
        return DB::table('regions')->insertGetId([
            'name'          => $name,
            'promotor_ID'  	=> $promotor_ID,
            'created'       => $time,
            'updated'       => $time
        ]);
    }

    /**
     * Get one region data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('regions')
            ->where('ID', $ID)
            ->first();
    }
    
    /**
     * Get all region data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('regions')->get()->all();
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
        
        DB::table('regions')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove region
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::transaction(function() use ($ID) {
            
            DB::table('regions')
                ->where('ID', $ID)
                ->delete();
            
            DB::table('dealers')
                ->where('region_ID', $ID)
                ->update(['region_ID' => 0]);
        });
    }

}

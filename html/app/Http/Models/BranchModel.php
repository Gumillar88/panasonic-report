<?php

/**
 * Branch data module
 */

namespace App\Http\Models;

use DB;

class BranchModel
{
    /**
     * Create new branch
     *
     * @access public
     * @param String $name
     * @param Integer $promotorID
     * @return Integer
     */
    public function create($name, $regionID, $promotorID)
    {
        $time = time();
        
        return DB::table('branches')->insertGetId([
            'name'          => $name,
            'region_ID'     => $regionID,
            'promotor_ID'  	=> $promotorID,
            'created'       => $time,
            'updated'       => $time
        ]);
    }

    /**
     * Get one branch data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('branches')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get one branch data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getAllByRegion($regionID)
    {
        return DB::table('branches')
            ->where('region_ID', $regionID)
            ->get()->all();
    }
    
    /**
     * Get one branch by promotor ID
     *
     * @access public
     * @param Integer $promotorID
     * @return Object
     */
    public function getByPromotor($promotorID)
    {
        return DB::table('branches')
            ->where('promotor_ID', $promotorID)
            ->first();
    }

    /**
     * Get all branch data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('branches')->get()->all();
    }

    /**
     * Update branch data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted branch data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('branches')
            ->where('ID', $ID)
            ->update($data);
    }
    
    /**
     * Remove branch
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::transaction(function() use ($ID) {
            
            DB::table('branches')
                ->where('ID', $ID)
                ->delete();
            
            DB::table('dealers')
                ->where('branch_ID', $ID)
                ->update(['branch_ID' => 0]);
        });

        
    }

}

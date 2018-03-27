<?php

/**
 * Competitor brand data module
 */

namespace App\Http\Models;

use DB;

class CompetitorBrandModel
{
    /**
     * Create new brand
     *
     * @access public
     * @param String $name
     * @return Integer
     */
    public function create($name)
    {
        $time = time();
        
        return DB::table('competitor_brands')->insertGetId([
            'name'          => $name,
            'created'       => $time,
            'updated'       => $time
        ]);
    }

    /**
     * Get one brand data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('competitor_brands')
            ->where('ID', $ID)
            ->first();
    }
    
    /**
     * Get all competitor brand data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('competitor_brands')->get()->all();
    }
    
    /** 
     * Get latest timestamp of update data
     *
     * @access public
     * @return Integer
     */
    public function getLatestTimestamp()
    {
        return DB::table('competitor_brands')->orderBy('updated', 'desc')->value('updated');
    }

    /**
     * Update brand data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted competitor brand data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('competitor_brands')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove brand data and update the price report
     *
     * @access public
     * @param Integer $ID
     * @param String $name
     * @return Void
     */
    public function remove($ID, $name)
    {
        DB::transaction(function() use ($ID, $name) {
            
            DB::table('competitor_brands')
                ->where('ID', $ID)
                ->delete();
            
            DB::table('competitor_prices')
                ->where('competitor_brand_ID', $ID)
                ->update([
                    'competitor_brand_ID'       => 0,
                    'competitor_brand_custom'   => $name
                ]);
        });
    }

}
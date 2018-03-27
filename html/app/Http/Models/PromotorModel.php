<?php

/**
 * Promotor data module
 */

namespace App\Http\Models;

use DB;

class PromotorModel
{
    /**
     * Create new promotor
     *
     * @access public
     * @param Integer $dealerID
     * @param String $phone
     * @param String $password
     * @param String $name
     * @param String $gender
     * @param String $type
     * @param Integer $parent_ID
     * @return Integer
     */
    public function create($dealerID, $phone, $password, $name, $gender, $type, $parent_ID)
    {
        $time = time();
        
        return DB::table('promotors')->insertGetId([
            'dealer_ID'     => $dealerID,
            'phone'         => $phone,
            'password'      => $password,
            'name'          => $name,
            'gender'        => $gender,
            'type'          => $type,
            'parent_ID'     => $parent_ID,
            'created'       => $time,
            'updated'       => $time
        ]);
    }

    /**
     * Get one promotor data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('promotors')
            ->where('ID', $ID)
            ->first();
    }
    
    
    public function getCalc($ID)
    {
        DB::table('promotors')
            ->where('ID', $ID)
            ->count();
        
    }

    /**
     * Get one promotor based on their phone number
     *
     * @access public
     * @param String $phone
     * @return Object
     */
    public function getByPhone($phone)
    {
        return DB::table('promotors')
                ->where('phone', $phone)
                ->first();
    }
    
    /**
     * Get all promotor by dealer
     *
     * @access public
     * @param Integer $dealerID
     * @return Array
     */
    public function getByDealer($dealerID)
    {
        return DB::table('promotors')
            ->where('dealer_ID', $dealerID)
            ->get()
            ->all();
    }

    /**
     * Get all promotor by type
     *
     * @access public
     * @param String $type
     * @return Array
     */
    public function getByType($type)
    {
        return DB::table('promotors')
            ->where('type', $type)
            ->get()
            ->all();
    }


    /**
     * Get all promotor by type
     *
     * @access public
     * @param String $type
     * @param Integer $parent_ID
     * @return Array
     */
    public function getByTypeParent($type, $parent_ID)
    {
        return DB::table('promotors')
            ->where('type', $type)
            ->where('parent_ID', $parent_ID)
            ->get()
            ->all();
    }

    /**
     * Get all promotor by parent ID
     *
     * @access public
     * @param Integer $parent_ID
     * @return Array
     */
    public function getByParent($parent_ID)
    {
        return DB::table('promotors')
            ->where('parent_ID', $parent_ID)
            ->get();
    }

    /**
     * Get all promotor data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('promotors')->get()->all();
    }
    
    /**
     * Get promotor data by type
     *
     * @access public
     * @params Array $types
     * @return Array
     */
    public function getAllByType($types)
    {
        return DB::table('promotors')
                ->whereIn('type', $types)
                ->get()
                ->all();
    }


    /**
     * Update promotor data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted promotor data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('promotors')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove promotor data
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('promotors')
            ->where('ID', $ID)
            ->delete();
    }

}

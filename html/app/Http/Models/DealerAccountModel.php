<?php

/**
 * Dealer account data module
 */

namespace App\Http\Models;

use DB;

class DealerAccountModel
{
    /**
     * Create new dealer account
     *
     * @access public
     * @param String $name
     * @param Integer $branch_ID
     * @param Integer $promotor_ID
     * @return Integer
     */
    public function create($name, $branch_ID, $promotor_ID)
    {
        $time = time();
        
        return DB::table('dealer_accounts')->insertGetId([
            'name'          => $name,
			'branch_ID'  	=> $branch_ID,
            'promotor_ID'   => $promotor_ID,
            'created'   	=> $time,
            'updated'       => $time,
        ]);
    }
    
    
    /**
     * Get one dealer account data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dealer_accounts')
            ->where('ID', $ID)
            ->first();
    }
    
    
    /**
     * Get one dealer account by promotor ID
     *
     * @access public
     * @param Integer $promotorID
     * @return Object
     */
    public function getByPromotor($promotorID)
    {
        return DB::table('dealer_accounts')
            ->where('promotor_ID', $promotorID)
            ->get()
            ->all();
    }


    /**
     * Get dealer account by parent
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getByParent($ID)
    {
        return DB::table('dealer_accounts')
            ->select('dealer_accounts.*')
            ->join('branches', 'branches.ID', '=', 'dealer_accounts.branch_ID')
            ->where('branches.promotor_ID', $ID)
            ->get()
            ->all();
    }
    
    /**
     * Get dealer account by branch
     *
     * @access public
     * @param Integer $branchID
     * @return Object
     */
    public function getByBranch($branchID)
    {
        return DB::table('dealer_accounts')
            ->where('branch_ID', $branchID)
            ->get()
            ->all();
    }

    /**
     * Get all dealer account data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('dealer_accounts')->get()->all();
    }

    /**
     * Update dealer account data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted dealer account data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('dealer_accounts')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove dealer account
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('dealer_accounts')
            ->where('ID', $ID)
            ->delete();
        
    }

}

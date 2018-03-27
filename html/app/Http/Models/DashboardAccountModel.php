<?php

/**
 * Dashboard account data model
 */
namespace App\Http\Models;

use DB;

class DashboardAccountModel
{
    /**
     * Create dashboard account
     *
     * @access public
     * @param String $name
     * @param String $email
     * @return Void
     */
    public function create($name, $email)
    {
        $time = time();
        
        return DB::table('dashboard_accounts')->insertGetId([
            'email'         => $email,
            'name'          => $name,
            'last_access'   => 0,
            'created'       => $time,
            'updated'       => $time,
        ]);
    }
    
    /**
     * Get one dashboard account
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dashboard_accounts')
                ->where('ID', $ID)
                ->first();
    }
    
    /**
     * Get one dashboard account by email
     *
     * @access public
     * @param String $email
     * @return Object
     */
    public function getByEmail($email)
    {
        return DB::table('dashboard_accounts')
                ->where('email', $email)
                ->first();
    }
    
    /**
     * Get all dashboard account
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('dashboard_accounts')
                ->get()
                ->all();
    }
    
    /**
     * Update dashboard account
     *
     * @access public
     * @param Integer $ID
     * @param Array $data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        return DB::table('dashboard_accounts')
                ->where('ID', $ID)
                ->update($data);
    }
    
    /**
     * Remove dashboard account data
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::transaction(function() use ($ID)
        {
            DB::table('dashboard_accounts')
                ->where('ID', $ID)
                ->delete();
            
            DB::table('dashboard_token')
                ->where('dashboard_account_ID', $ID)
                ->delete();
        });
    }
}

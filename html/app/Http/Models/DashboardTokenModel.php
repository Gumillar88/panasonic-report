<?php

/**
 * Dashboard token data model
 */

namespace App\Http\Models;

use DB;

class DashboardTokenModel
{
    /**
     * Create dashboard token
     *
     * @access public
     * @param Integer $dashboardAccountID
     * @param String $token
     * @return Void
     */
    public function create($dashboardAccountID, $token)
    {
        return DB::table('dashboard_token')->insert([
            'dashboard_account_ID'  => $dashboardAccountID,
            'token'                 => $token,
            'created'               => time()
        ]);
    }
    
    /**
     * Get one dashboard data token by ID
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dashboard_token')
                ->where('ID', $ID)
                ->first();
    }
    
    /**
     * Get one dashboard data token by token
     *
     * @access public
     * @param String $token
     * @return Object
     */
    public function getByToken($token)
    {
        return DB::table('dashboard_token')
                ->where('token', $token)
                ->first();
    }
    
    /**
     * Get one dashboard token
     *
     * @access public
     * @param Integer $dashboardAccountID
     * @return Object
     */
    public function getByDashboardAccount($dashboardAccountID)
    {
        return DB::table('dashboard_token')
                ->where('dashboard_account_ID', $dashboardAccountID)
                ->get()
                ->all();
    }
    
    /**
     * Remove token data by ID
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('dashboard_token')
            ->where('ID', $ID)
            ->delete();
    }
    
    /**
     * Remove token data by token
     *
     * @access public
     * @param String $token
     * @return Void
     */
    public function removeByToken($token)
    {
        DB::table('dashboard_token')
            ->where('token', $token)
            ->delete();
    }
    
    /**
     * Remove dashboard token data by dashboard account ID
     *
     * @access public
     * @param Integer $dashboardAccountID
     * @return Void
     */
    public function removeByDashboardAccount($dashboardAccountID)
    {
        DB::table('dashboard_token')
            ->where('dashboard_account_ID', $dashboardAccountID)
            ->delete();
    }
}

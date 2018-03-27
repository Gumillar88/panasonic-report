<?php

/**
 * Dealer data module
 */

namespace App\Http\Models;

use DB;

class DealerModel
{
    /**
     * Create new dealer
     *
     * @access public
     * @param Array $data
     * @return Integer
     */
    public function create($data)
    {
        $time = time();
        
        $finalData = [
            'created'   => $time,
            'updated'   => $time
        ];
        
        $dataKeys = [
            'region_ID',
            'branch_ID',
            'dealer_account_ID',
            'dealer_type_ID',
            'dealer_channel_ID',
            'code',
            'name',
            'company',
            'address'
        ];
        
        // Validate data keys
        foreach ($dataKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                throw new Exception('Key '.$key.' is not exists on dealer create data.');
            }
            
            $finalData[$key] = $data[$key];
        }
        
        return DB::table('dealers')->insertGetId($finalData);
    }
    
    
    /**
     * Get one dealer data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('dealers')
            ->where('ID', $ID)
            ->first();
    }

    /**
     * Get dealer by account_ID
     *
     * @access public
     * @param Integer $accountID
     * @return Object
     */
    public function getByAccount($accountID)
    {
        return DB::select(
            'SELECT DISTINCT dealers.* FROM dealers 
                LEFT JOIN promotors ON promotors.dealer_ID = dealers.ID 
            WHERE dealers.dealer_account_ID = :accountID
                AND promotors.ID IS NOT NULL',
            ['accountID' => $accountID]
        );
    }
    
    /**
     * Get list dealer by branch
     * 
     * @access public
     * @param Integer $branchID
     * @return Object
     */
    public function getByBranch($branchID)
    {
        return DB::select(
            'SELECT DISTINCT dealers.* FROM dealers 
                LEFT JOIN promotors ON promotors.dealer_ID = dealers.ID 
            WHERE dealers.branch_ID = :branchID
                AND promotors.ID IS NOT NULL',
            ['branchID' => $branchID]
        );
    }
    
    /**
     * Get dealer by its team leader
     *
     * @access public
     * @param Array $TLIDs
     * @return Array
     */
    public function getByTeamLeader($TLIDs)
    {
        return DB::table('dealers')
            ->join('dealer_accounts', 'dealer_accounts.ID', '=', 'dealers.dealer_account_ID')
            ->join('branches', 'branches.ID', '=', 'dealer_accounts.branch_ID')
            ->whereIn('branches.promotor_ID', $TLIDs)
            ->select('dealers.*')
            ->get();
    }

    /**
     * Get all dealer data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('dealers')->get();
    }

    /**
     * Update dealer data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted dealer data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('dealers')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove dealer
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::transaction(function() use ($ID) {
            
            DB::table('dealers')
                ->where('ID', $ID)
                ->delete();
            
            DB::table('promotors')
                ->where('dealer_ID', $ID)
                ->update([
                    'dealer_ID' => 0,
                    'updated'   => time()
                ]);
        });
        
    }

}

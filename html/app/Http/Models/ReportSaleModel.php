<?php

/**
 * Report Sale data module
 */

namespace App\Http\Models;

use DB;
use Exception;

class ReportSaleModel
{
    /**
     * Create Report Sale
     *
     * @access public
     * @param Array $data
     * @return Void
     */
    public function create($data)
    {   
        // Set time
        $time = time();
        
        // Set final data container
        $finalData = [
            'created' => $time,
        ];
        
        // Validate data key
        $validKeys = [
            'promotor_ID',
            'dealer_ID',
            'account_ID',
            'tl_ID',
            'arco_ID',
            'date',
        ];
        
        foreach ($validKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                throw new Exception('Report No Sale Model: Key "'.$key.'" is not found on data parameter');
            }
            
            $finalData[$key] = $data[$key];
        }
        
        return DB::table('report_nosale')->insertGetId($finalData);
    }

    
    /**
     * Get Report Sale for date
     *
     * @access public
     * @param Integer $promotor_ID
     * @param Integer $date
     * @return Object
     */
    public function getData($promotor_ID, $date)
    {
        return DB::table('report_nosale')
            ->where('promotor_ID', $promotor_ID)
            ->where('date', $date)
            ->first();
    }
}

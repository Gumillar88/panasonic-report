<?php

/**
 * Absence data module
 */

namespace App\Http\Models;

use DB;

class AbsenceModel
{
    /**
     * Create absence data
     *
     * @access public
     * @param Integer $promotorID
     * @param String $reason
     * @param Data $date
     * @return Void
     */
    public function create($promotorID, $reason, $date)
    {
        DB::table('absences')->insert([
            'promotor_ID' 	=> $promotorID,
            'reason' 		=> $reason,
            'date' 			=> $date,
            'created' 		=> time()
        ]);
    }

    /**
     * Get absence for date
     *
     * @access public
     * @param Integer $promotorID
     * @return Object
     */
    public function get($promotorID, $date)
    {
        return DB::table('absences')
            ->where('promotor_ID', $promotorID)
            ->where('date', $date)
            ->first();
    }
}

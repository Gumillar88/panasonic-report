<?php

/**
 * Log data module
 */

namespace App\Http\Models;

use DB;

class LogModel
{
    /**
     * Record user activity
     *
     * @access public
     * @param Integer $userID
     * @param Integer $action
     * @return Void
     */
    public function record($userID, $action)
    {
        DB::table('logs')->insert([
            'user_ID' => $userID,
            'action' => $action,
            'created' => time()
        ]);
    }
}

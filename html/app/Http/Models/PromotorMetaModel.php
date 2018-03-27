<?php

/**
 * Promotor meta data module
 */

namespace App\Http\Models;

use DB;

class PromotorMetaModel
{
    /**
     * Set meta data for promotor
     *
     * @access public
     * @param Integer $promotorID
     * @param String $name
     * @param String $content
     * @return Void
     */
    public function set($promotorID, $name, $content)
    {
        // Set time
        $time = time();
        
        // Check if meta key is available
        $check = $this->get($promotorID, $name);
        
        // If it's available then update it
        if ($check)
        {
            DB::table('promotor_meta')
                ->where('promotor_ID', $promotorID)
                ->where('name', $name)
                ->update([
                    'content' => $content,
                    'updated' => $time
                ]);
        }
        else
        {
            DB::table('promotor_meta')->insert([
                'promotor_ID'   => $promotorID,
                'name'          => $name,
                'content'       => $content,
                'created'       => $time,
                'updated'       => $time
            ]);
        }
    }
    
    /**
     * Get one meta value
     *
     * @access public
     * @param Integer $promotorID
     * @param String $name
     * @return String
     */
    public function get($promotorID, $name)
    {
        return DB::table('promotor_meta')
                ->where('promotor_ID', $promotorID)
                ->where('name', $name)
                ->value('content');
    }
    
    /**
     * Remove a meta data
     *
     * @access public
     * @param Integer $promotorID
     * @param String $name
     * @return Void
     */
    public function remove($promotorID, $name)
    {
        DB::table('promotor_meta')
            ->where('promotor_ID', $promotorID)
            ->where('name', $name)
            ->delete();
    }
}
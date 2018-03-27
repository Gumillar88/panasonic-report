<?php

/**
 * Dealer News data module
 */

namespace App\Http\Models;

use DB;

class DealerNewsModel
{
    /**
     * Create dealer news
     *
     * @access public
     * @param Integer $newsID
     * @param Array $dealerIDs
     * @return Void
     */
    public function set($newsID, $dealerIDs)
    {
        $time = time();
        
        $data = [];
        
        foreach ($dealerIDs as $dealerID) 
        {
            $data[] = [
                'news_ID'   => $newsID,
                'dealer_ID' => $dealerID,
                'created'   => $time
            ];
        }
        
        // Remove existing data
        DB::table('dealer_news')->where('news_ID', $newsID)->delete();
        
        // Save data
        DB::table('dealer_news')->insert($data);
    }
    
    /**
     * Get unique dealer news with pagination feature
     *
     * @access public
     * @param Array $dealerIDs
     * @return Array
     */
    public function getAll($dealerIDs)
    {
        return DB::table('dealer_news')
            ->select('news_ID')
            ->whereIn('dealer_ID', $dealerIDs)
            ->distinct()
            ->get()
            ->all();
    }
    
    /**
     * Get unique dealer news with pagination feature
     *
     * @access public
     * @param Array $dealerIDs
     * @return Array
     */
    public function getPagination($dealerIDs, $timestamp)
    {
        return DB::table('dealer_news')
            ->select('news_ID')
            ->whereIn('dealer_ID', $dealerIDs)
            ->where('created', '<=', $timestamp)
            ->distinct()
            ->limit(11)
            ->orderBy('created', 'desc')
            ->get()
            ->all();
    }
    
}
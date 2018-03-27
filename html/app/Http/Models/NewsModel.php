<?php

/**
 * News data module
 */

namespace App\Http\Models;

use DB;

class NewsModel
{
    /**
     * Create news data
     *
     * @access public
     * @param String $title
     * @param String $content
     * @param Integer $created_by
     * @return Integer
     */
    public function create($title, $content, $created_by)
    {
        $time = time();
        
        return DB::table('news')->insertGetId([
            'title'       => $title,
            'content'     => $content,
            'created_by'  => $created_by,
            'created'     => $time,
            'updated'     => $time
        ]);
    }

    /**
     * Get one news data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('news')->where('ID', $ID)->first();
    }
    
    /**
     * Get all news data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('news')->get()->all();
    }
    
    /**
     * Get all news with pagination use timestamp as parameter
     *
     * @access public
     * @param Integer $timestamp
     * @return Array
     */
    public function getPagination($timestamp)
    {
        return DB::table('news')
                ->select('ID', 'title', 'created')
                ->where('created', '<=', $timestamp)
                ->limit(11)
                ->orderBy('created', 'desc')
                ->get()
                ->all();
    }
    
    /**
     * Get many news content
     *
     * @access public
     * @param Array $newsIDs
     * @return Array
     */
    public function getMany($newsIDs)
    {
        return DB::table('news')
                ->select('ID', 'title', 'created')
                ->whereIn('ID', $newsIDs)
                ->get()
                ->all();
    }

    /**
     * Update news data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted region data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('news')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove news
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::transaction(function() use ($ID) {
            
            // Remove news
            DB::table('news')
                ->where('ID', $ID)
                ->delete();
            
            // Remove all dealer connection
            DB::table('dealer_news')
                ->where('news_ID', $ID)
                ->delete();
        });
    }

}

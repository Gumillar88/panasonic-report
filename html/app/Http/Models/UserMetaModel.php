<?php 

/**
* User Meta module
*/ 

namespace App\Http\Models;

use DB;

class UserMetaModel
{
	/**
     * Set new user meta data
     * Insert new data if data is not found 
     * or update it if it already exist in database
     *
     * @access public
     * @param Integer $userID
     * @param String $name
     * @param String $content
     * @return Void
     */
	public function set($userID, $name, $content)
    {
        $time   = time();

        $result = $this->get($userID, $name);
        
        // If data not found insert it
        if ($result === null)
        {
            return DB::table('user_meta')->insertGetId([
                'user_ID'	=> $userID,
                'name'      => $name,
                'content'   => $content,
                'created'   => $time,
                'updated'   => $time
            ]);
        }
        
        // Or update it
        return DB::table('user_meta')
                ->where('user_ID', $userID)
                ->where('name', $name)
                ->update([
                    'content' => $content,
                    'updated' => $time
                ]);
    }

    /**
     * Get one user meta data
     *
     * @access public
     * @param Integer $userID
     * @param String $name
     * @return String
     */
    public function get($userID, $name)
    {
        return DB::table('user_meta')
            ->where('user_ID', $userID)
            ->where('name', $name)
            ->value('content');
    }

    /**
     * Get one user meta data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('user_meta')->where('ID', $ID)->first();
    }

    /**
     * Get By name user meta data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getByName($userID, $name)
    {
        return DB::table('user_meta')
                ->where('user_ID', $userID)
                ->where('name', $name)
                ->first();
    }
    /**
     * Update user_meta data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted user data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('user_meta')
            ->where('ID', $ID)
            ->update($data);
    }
    /**
     * Get one user meta data from content
     *
     * @access public
     * @param String $content
     * @return String
     */
    public function getByContent($content)
    {
        return DB::table('user_meta')
            ->where('content', $content)
            ->first();
    }

    /**
     * Remove user meta data
     *
     * @access public
     * @param Integer $userID
     * @param String $name
     * @return Void
     */
    public function remove($userID, $name)
    {
        DB::table('user_meta')
            ->where('user_ID', $userID)
            ->where('name', $name)
            ->delete();
    }
}
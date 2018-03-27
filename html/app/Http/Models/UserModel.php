<?php

/**
 * User data module
 */

namespace App\Http\Models;

use DB;

class UserModel
{
    /**
     * Create new user
     *
     * @access public
     * @param String $fullname
     * @param String $username
     * @param String $password
     * @param String $type
     * @return Integer
     */
    public function create($fullname, $username, $password, $type)
    {
        $time = time();
        
        return DB::table('users')->insertGetId([
            'fullname'  => $fullname,
            'username'  => $username,
            'password'  => $password,
            'type'      => $type,
            'created'   => $time,
            'updated'   => $time
        ]);
    }

    /**
     * Get one user data
     *
     * @access public
     * @param Integer $ID
     * @return Object
     */
    public function getOne($ID)
    {
        return DB::table('users')->where('ID', $ID)->first();
    }

    /**
     * Get all user data
     *
     * @access public
     * @return Array
     */
    public function getAll()
    {
        return DB::table('users')->get()->all();
    }

    /**
     * Get user based on their username
     *
     * @access public
     * @param String $username
     * @return Object
     */
    public function getByUsername($username)
    {
        return DB::table('users')->where('username', $username)->first();
    }

    /**
     * Update user data
     *
     * @access public
     * @param Integer $ID
     * @param Array $data - formatted user data
     * @return Void
     */
    public function update($ID, $data)
    {
        $data['updated'] = time();
        
        DB::table('users')
            ->where('ID', $ID)
            ->update($data);
    }

    /**
     * Remove user
     *
     * @access public
     * @param Integer $ID
     * @return Void
     */
    public function remove($ID)
    {
        DB::table('users')
            ->where('ID', $ID)
            ->delete();
    }

}

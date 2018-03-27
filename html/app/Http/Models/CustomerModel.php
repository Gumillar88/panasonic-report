<?php

/**
 * Customer data module
 */

namespace App\Http\Models;

use DB;

class CustomerModel
{
    /**
     * Create new customer
     *
     * @access public
     * @param String $name
     * @param String $phone
     * @param String $gender
     * @return Integer
     */
    public function create($name, $phone, $gender)
    {
        $time = time();
        
        return DB::table('customers')->insertGetId([
            'name'          => $name,
            'phone'         => $phone,
            'gender'        => $gender,
            'email'         => '',
            'address'       => '',
            'city'          => '',
            'province'      => '',
            'birthdate'     => '2016-12-31',
            'created'       => $time,
            'updated'       => $time
        ]);
    }

    /**
     * Get one customer data by phone
     *
     * @access public
     * @param String $phone
     * @return Object
     */
    public function getByPhone($phone)
    {
        return DB::table('customers')
            ->where('phone', $phone)
            ->first();
    }

}

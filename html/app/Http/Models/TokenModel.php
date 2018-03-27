<?php

/**
 * Token data model for JSON Web Token (JWT)
 */
namespace App\Http\Models;

use \Firebase\JWT\JWT;
use DB;

class TokenModel 
{   
    /**
     * Create new token using user ID as token parameter
     *
     * @access public
     * @param Integer $token_ID
     * @return String
     */
    public function encode($promotor_ID, $token_ID)
    {
        $time       = time();
        $expired    = $time + (86400*30);
        
        $token = [
            'token_ID'      => $token_ID,
            'iat'           => $time,
            //'nbf'   => $expired
        ];
        
        $jwt = JWT::encode($token, env('APP_KEY'));

        //save token
        DB::table('promotors')
            ->where('ID', $promotor_ID)
            ->update(['user_token'=> $token_ID]);
        
        return $jwt;
    }
    
    /**
     * Decode token to get user id
     *
     * @access public
     * @param String $token
     * @return Integer
     */
    public function decode($token)
    {
        try
        {
            $decoded = JWT::decode($token, env('APP_KEY'), array('HS256'));
            $token_ID = $decoded->token_ID;

            // get promotor by token
            $promotor =  DB::table('promotors')
                        ->where('user_token', $token_ID)
                        ->select('ID')
                        ->first();
            
            if (!$promotor)
            {
                return 0;
            }
                        
            return $promotor->ID;
        }
        catch (\Exception $e)
        {
            return 0;
        }
        
    }
}
<?php

namespace App\Http\Middleware;

use Closure;

class APIFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) 
        {
            header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials: true');
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
        {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) 
            {
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  
            }


            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            {
                header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
            }
        }
        
        return $next($request);
    }
}

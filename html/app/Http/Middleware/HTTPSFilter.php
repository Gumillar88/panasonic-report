<?php

namespace App\Http\Middleware;

use Closure;

class HTTPSFilter 
{

    public function handle($request, Closure $next)
    {
        // Redirect http to https
        $request->setTrustedProxies( [ $request->getClientIp() ] ); 
        
        if (!$request->secure() && env('APP_ENV') === 'production') 
        {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request); 
    }
}
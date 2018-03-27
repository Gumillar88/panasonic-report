<?php

namespace App\Http\Middleware;

use Closure;

class Administrator
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
        if (!$request->session()->has('user_ID'))
        {
            return redirect('login');
        }
        
        // Attach user id to request object
        $request->merge(['userID' => $request->session()->get('user_ID')]);

        return $next($request);
    }
}

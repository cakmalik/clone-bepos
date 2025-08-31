<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MenuSecure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $menu)
    {
           //check in user have permission on menu or not
           if (!in_array($menu, getMenuPermissions())) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return $next($request);
    }
}

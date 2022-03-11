<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class OnlyAllowAdmins extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!Auth::user()->is_admin) {
            throw new UnauthorizedException('Unauthorized. You do not seem to have permissions required for this operation.', 401);
        }
        
        return $next($request);
    }
    
}

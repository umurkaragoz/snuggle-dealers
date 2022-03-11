<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Repositories\AuthRepository;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class Authenticate extends Middleware
{
    private AuthRepository $authRepo;
    
    public function __construct(Factory $auth, AuthRepository $authRepo)
    {
        parent::__construct($auth);
        
        $this->authRepo = $authRepo;
    }
    
    public function handle($request, Closure $next, ...$guards)
    {
        $tokenString = $request->header('Authorization');
        
        if (!$tokenString) {
            throw new UnauthorizedException('Unauthorized.', 401);
        }
        
        $tokenString = substr($request->header('Authorization'), 7);
        
        $token = $this->authRepo->validateToken($tokenString);
        
        // Log in the verified user for this request only.
        Auth::setUser((new User())->forceFill(['id' => $token->user_id, 'is_admin' => $token->is_admin]));
        
        return $next($request);
    }
    
}

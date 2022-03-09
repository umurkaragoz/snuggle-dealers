<?php

namespace App\Repositories;


use App\DataTransferObjects\LoginDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class AuthRepository
{
    public function login(LoginDto $loginDto)
    {
        // Validate credentials.
        if (!Auth::once($loginDto->toArray())) {
            throw new UnauthorizedException('Invalid credentials.');
        }
        
        
    }
    
}

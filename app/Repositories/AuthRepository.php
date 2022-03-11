<?php

namespace App\Repositories;

use App\DataTransferObjects\LoginDto;
use App\DataTransferObjects\UserCreateDto;
use App\Models\JwtToken;
use App\Models\User;
use Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use JetBrains\PhpStorm\ArrayShape;


// --------------------------------------------------------------------------------------------------------------------------------------------------|
// ------------------------------------------------------------------------------------------------------------------------------- Auth Repository --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
class AuthRepository
{
    /* ---------------------------------------------------------------------------------------------------------------------------------- login -+- */
    /**
     * Authenticates user using `email` and `password`, issues and returns a new JWT token.
     *
     * @param LoginDto $loginDto
     *
     * @return string
     */
    public function login(LoginDto $loginDto): string
    {
        // Validate credentials.
        // Authenticate the user just for this request, i.e. tell the guard not to set cookies or start a session.
        // Subsequent requests will use JWT auth instead.
        if (!Auth::once($loginDto->toArray())) {
            throw new UnauthorizedException('Invalid credentials.', 401);
        }
        
        Auth::user()->update(['last_login_at' => now()]);
        
        return $this->generateJwtToken(Auth::user());
    }
    
    
    /* --------------------------------------------------------------------------------------------------------------------- generate Jwt Token -+- */
    public function generateJwtToken(User $user): string
    {
        // Invalidate previous any tokens for this user.
        $this->invalidateTokensFor($user);
        
        // Issue a new JWT token for the user.
        $token = new JwtToken();
        $token->expires_at = now()->addSeconds(config('jwt.ttl'));
        $token->uuid = Str::uuid();
        $token->token_title = 'auth';
        $token->is_admin = 1;
        
        $user->jwtTokens()->save($token);
        
        // Sign and return the token.
        return JWT::encode($this->createTokenPayload($token), config('jwt.private_key'), 'RS256');
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------------- validate Token -+- */
    /**
     * Validates provided JWT token.
     *
     * @param string $token
     *
     * @return JwtToken
     */
    public function validateToken(string $token): JwtToken
    {
        try {
            $decodedToken = JWT::decode($token, new Key(config('jwt.public_key'), 'RS256'));
        } catch (SignatureInvalidException) {
            throw new UnauthorizedException('Token is invalid.', 401);
        }
        
        // Refuse expired tokens.
        if ($decodedToken->exp < now()->timestamp) {
            throw new UnauthorizedException('Token has expired.', 401);
        }
        
        // Refuse blacklisted tokens.
        if (in_array($decodedToken->jti, $this->getInvalidatedTokens())) {
            throw new UnauthorizedException('Token is invalidated.', 401);
        }
        
        $jwtToken = new JwtToken();
        $jwtToken->user_id = $decodedToken->uid;
        $jwtToken->is_admin = $decodedToken->adm;
        
        return $jwtToken;
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------ invalidate Tokens For -+- */
    /**
     * Invalidate *all* tokens created for given user.
     */
    public function invalidateTokensFor(User $user)
    {
        $user->jwtTokens()->whereValid()->update(['invalidated_at' => now()]);
        
        // Flush the invalidated token cache.
        Cache::forget('invalidated-tokens');
    }
    
    
    /* ----------------------------------------------------------------------------------------------------------------- get Invalidated Tokens -+- */
    /**
     * Get a list containing the UUIDs of invalidated tokens.
     *
     * @return array
     */
    public function getInvalidatedTokens(): array
    {
        return Cache::rememberForever('invalidated-tokens', function() {
            return JwtToken::whereFresh()->whereNotNull('invalidated_at')->pluck('uuid')->toArray();
        });
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------- register -+- */
    public function register(UserCreateDto $dto): string
    {
        $user = new User();
        
        $user->email = $dto->email;
        $user->first_name = $dto->first_name;
        $user->last_name = $dto->last_name;
        $user->address = $dto->address;
        $user->avatar = $dto->avatar;
        $user->phone_number = $dto->phone_number;
        $user->is_marketing = $dto->is_marketing;
        $user->is_admin = $dto->is_admin;
        
        $user->password = bcrypt($dto->password);
        
        $user->save();
        
        return $this->generateJwtToken($user);
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------- create Token Payload -+- */
    /**
     * Creates a new JWT token payload/claim using a JwtToken entity.
     *
     * @param JwtToken $token
     *
     * @return array
     */
    #[ArrayShape(["iss" => "string", "aud" => "string", "jti" => "string", "iat" => "float|int|string", "exp" => "float|int|string", "uid" => "", "adm" => "mixed"])]
    private function createTokenPayload(JwtToken $token): array
    {
        return [
            "iss" => "snuggle-dealers.com",
            "aud" => "snuggle-dealers.com",
            "jti" => $token->uuid,
            // Issued at
            "iat" => $token->created_at->timestamp,
            // Expiry
            "exp" => $token->expires_at->timestamp,
            // User UUID
            "uid" => $token->user->uuid,
            // Is admin
            "adm" => $token->is_admin,
        ];
    }
    
}

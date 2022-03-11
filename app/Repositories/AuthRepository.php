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
    private UserRepository $userRepo;
    
    /* ------------------------------------------------------------------------------------------------------------------------------ construct -+- */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    
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
        $token->is_admin = $user->is_admin;
        
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
     * @return array
     */
    #[ArrayShape(['token_uuid' => "string", 'user_uuid' => "string", 'is_admin' => "bool"])]
    public function validateToken(string $token): array
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
        
        return [
            'token_uuid' => $decodedToken->jti,
            'user_uuid'  => $decodedToken->uid,
            'is_admin'   => $decodedToken->adm,
        ];
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
        return Cache::rememberForever('invalidated-tokens', function () {
            return JwtToken::whereFresh()->whereNotNull('invalidated_at')->pluck('uuid')->toArray();
        });
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------- register -+- */
    public function register(UserCreateDto $dto): string
    {
        $user = $this->userRepo->create($dto);
        
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
    #[ArrayShape([
        "iss" => "string",
        "aud" => "string",
        "jti" => "string",
        "iat" => "float|int|string",
        "exp" => "float|int|string",
        "uid" => "",
        "adm" => "mixed",
    ])]
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

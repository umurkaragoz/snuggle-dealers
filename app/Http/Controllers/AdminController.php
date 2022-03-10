<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\OpenApi\RequestBodies\LoginRequestBody;
use App\OpenApi\Responses\InternalServerErrorResponse;
use App\OpenApi\Responses\OkResponse;
use App\OpenApi\Responses\PageNotFoundResponse;
use App\OpenApi\Responses\UnprocessableEntityResponse;
use App\Repositories\AuthRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


// --------------------------------------------------------------------------------------------------------------------------------------------------|
// ------------------------------------------------------------------------------------------------------------------------------ Admin Controller --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
#[OpenApi\PathItem]
class AdminController extends Controller
{
    private AuthRepository $authRepo;
    
    /* ------------------------------------------------------------------------------------------------------------------------------ construct -+- */
    public function __construct(AuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }
    
    /* ---------------------------------------------------------------------------------------------------------------------------------- login -+- */
    /**
     * Login using existing user credentials.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['admin'], method: 'POST')]
    #[OpenApi\RequestBody(factory: LoginRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authRepo->login($request->getDto());
        
        return response()->json(['token' => $token]);
    }
    
    /* --------------------------------------------------------------------------------------------------------------------------------- logout -+- */
    public function logout(): JsonResponse
    {
        $this->authRepo->invalidateTokensFor(Auth::user());
        
        return response()->json(['success' => true]);
    }
}

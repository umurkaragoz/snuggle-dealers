<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\LoginDto;
use App\Http\Requests\LoginRequest;
use App\OpenApi\RequestBodies\LoginRequestBody;
use App\OpenApi\Responses\InternalServerErrorResponse;
use App\OpenApi\Responses\NotFoundResponse;
use App\OpenApi\Responses\OkResponse;
use App\OpenApi\Responses\PageNotFoundResponse;
use App\OpenApi\Responses\UnprocessableEntityResponse;
use App\Repositories\AuthRepository;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

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
     * @param LoginDto     $loginDto
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['admin'], method: 'POST')]
    #[OpenApi\RequestBody(factory: LoginRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function login(LoginRequest $request, LoginDto $loginDto)
    {
        $this->authRepo->login($loginDto);
        
        return response()->json($loginDto);
    }
    
}

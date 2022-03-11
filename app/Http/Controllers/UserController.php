<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserEditRequest;
use App\OpenApi\RequestBodies\LoginRequestBody;
use App\OpenApi\RequestBodies\UserCreateRequestBody;
use App\OpenApi\RequestBodies\UserEditRequestBody;
use App\OpenApi\Responses\InternalServerErrorResponse;
use App\OpenApi\Responses\OkResponse;
use App\OpenApi\Responses\PageNotFoundResponse;
use App\OpenApi\Responses\UnprocessableEntityResponse;
use App\OpenApi\SecuritySchemes\GlobalSecurityScheme;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


// --------------------------------------------------------------------------------------------------------------------------------------------------|
// ------------------------------------------------------------------------------------------------------------------------------- User Controller --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
#[OpenApi\PathItem]
class UserController extends Controller
{
    private AuthRepository $authRepo;
    private UserRepository $userRepo;
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------ construct -+- */
    public function __construct(AuthRepository $authRepo, UserRepository $userRepo)
    {
        $this->authRepo = $authRepo;
        $this->userRepo = $userRepo;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------------------------------------- show -+- */
    /**
     * Show user details.
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], security: GlobalSecurityScheme::class, method: 'GET')]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function show(): JsonResponse
    {
        $user = $this->userRepo->getDetails(Auth::user()->uuid);
        
        return response()->json($user->toArray());
    }
    
    /* ---------------------------------------------------------------------------------------------------------------------------------- login -+- */
    /**
     * Login using existing user credentials.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], method: 'POST')]
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
    /**
     * Logout a user account.
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], security: GlobalSecurityScheme::class, method: 'GET')]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function logout(): JsonResponse
    {
        $this->authRepo->invalidateTokensFor(Auth::user());
        
        return response()->json(['success' => true]);
    }
    
    
    /* --------------------------------------------------------------------------------------------------------------------------------- create -+- */
    /**
     * Create a new user account.
     *
     * @param UserEditRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], security: GlobalSecurityScheme::class, method: 'POST')]
    #[OpenApi\RequestBody(factory: UserCreateRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function create(UserEditRequest $request): JsonResponse
    {
        $dto = $request->getDto();
        $dto->is_admin = 0;
        
        $token = $this->authRepo->register($dto);
        
        return response()->json(['success' => true, 'token' => $token]);
    }
    
    
    /* ----------------------------------------------------------------------------------------------------------------------------------- edit -+- */
    /**
     * Edit logged in user.
     *
     * @param UserEditRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], security: GlobalSecurityScheme::class, method: 'PUT')]
    #[OpenApi\RequestBody(factory: UserEditRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function edit(UserEditRequest $request): JsonResponse
    {
        $this->userRepo->update(Auth::user()->uuid, $request->getDto());
        
        $user = $this->userRepo->getDetails(Auth::user()->uuid);
        
        return response()->json([
            'success' => true,
            'user'    => $user->toArray(),
        ]);
    }
    
    
    /* --------------------------------------------------------------------------------------------------------------------------------- delete -+- */
    /**
     * Delete logged in user.
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['User'], security: GlobalSecurityScheme::class, method: 'DELETE')]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function delete(): JsonResponse
    {
        $this->userRepo->delete(Auth::user());
        
        return response()->json(['success' => true]);
    }
}

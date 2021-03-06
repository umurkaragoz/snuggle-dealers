<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\UserCreateDto;
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
// ------------------------------------------------------------------------------------------------------------------------------ Admin Controller --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
#[OpenApi\PathItem]
class AdminController extends Controller
{
    private AuthRepository $authRepo;
    private UserRepository $userRepo;
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------ construct -+- */
    public function __construct(AuthRepository $authRepo, UserRepository $userRepo)
    {
        $this->authRepo = $authRepo;
        $this->userRepo = $userRepo;
    }
    
    
    /* ---------------------------------------------------------------------------------------------------------------------------------- login -+- */
    /**
     * Login using existing user credentials.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], method: 'POST')]
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
     * Logout an admin account.
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], security: GlobalSecurityScheme::class, method: 'GET')]
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
     * Create a new admin user.
     *
     * @param UserEditRequest $request
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], security: GlobalSecurityScheme::class, method: 'POST')]
    #[OpenApi\RequestBody(factory: UserCreateRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function create(UserEditRequest $request): JsonResponse
    {
        $dto = $request->getDto();
        $dto->is_admin = 1;
        
        $token = $this->authRepo->register($dto);
        
        return response()->json(['success' => true, 'token' => $token]);
    }
    
    
    /* --------------------------------------------------------------------------------------------------------------------------- user Listing -+- */
    /**
     * List user accounts.
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], security: GlobalSecurityScheme::class, method: 'GET')]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function userListing(): JsonResponse
    {
        return response()->json($this->userRepo->userList());
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------ user Edit -+- */
    /**
     * Edit a user entity.
     *
     * @param UserEditRequest $request
     * @param string          $uuid
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], security: GlobalSecurityScheme::class, method: 'PUT')]
    #[OpenApi\RequestBody(factory: UserEditRequestBody::class)]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function userEdit(UserEditRequest $request, string $uuid): JsonResponse
    {
        $this->userRepo->update($uuid, $request->getDto());
        
        return response()->json(['success' => true]);
    }
    
    
    /* ---------------------------------------------------------------------------------------------------------------------------- user Delete -+- */
    /**
     * Delete a user.
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    #[OpenApi\Operation(tags: ['Admin'], security: GlobalSecurityScheme::class, method: 'DELETE')]
    #[OpenApi\Response(factory: OkResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: PageNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: UnprocessableEntityResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: InternalServerErrorResponse::class, statusCode: 500)]
    public function userDelete(string $uuid): JsonResponse
    {
        $this->userRepo->delete($uuid);
        
        return response()->json(['success' => true]);
    }
    
}

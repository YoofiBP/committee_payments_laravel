<?php

namespace App\Http\Controllers\API;

use App\Exceptions\DuplicateUserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService){
        $this->middleware(['web','auth:sanctum'])->except(['login','signup']);
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json($request->user(),200);
    }

    /**
     * Store a user in storage.
     *
     * @param StoreUserRequest $request
     * @return Response;
     */
    public function signup(StoreUserRequest $request)
    {
        $data = $request->validated();
        if($user = $this->userService->add($data)){
            Auth::guard('web')->login($user);
            return response(new UserResource($user), 201);
        } else {
            return response("An error occurred", 500);
        }
    }

    /**
     * Login existing user
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        if($this->userService->validateCredentials($credentials)){
            $request->session()->regenerate();
           return response(new UserResource(Auth::user()), 200);
        }else{
            return response(["message" => "Invalid credentials"],401);
        }
    }

    /**
     * Logout existing user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(["message" => "Successfully logged out"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return void
     */
    public function show(User $user)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $newUser = $this->userService->update($user, $data);
        return response()->json($newUser, 200);
    }
//TODO: Implement policies to prevent updating user that is not current authenticated user
    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return void
     */
    public function destroy(User $user)
    {
        //
    }
}

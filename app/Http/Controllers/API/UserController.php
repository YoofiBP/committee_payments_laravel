<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware(['web','auth:sanctum'])->except(['login','store']);
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
     * @param  StoreUserRequest  $request
     * @return JsonResponse
     */
    //TODO: Enable authentication when user logs in
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        return response()->json(new UserResource($user),201);
    }

    /**
     * Login existing user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        if(Auth::guard('web')->attempt($credentials, true)){
            $request->session()->regenerate();
           return response()->json(Auth::user(),200);
        }else{
            return response()->json(["message" => "Invalid credentials"],401);
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
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function update(Request $request, User $user)
    {
        //
    }

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

<?php

namespace App\Http\Controllers\API\Auth;

use App\Actions\CreateUserProfileAction;
use App\Actions\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticationController extends Controller
{
    /**
     * Register a new user an issue a token.
     *
     * @param  \Illuminate\Http\Requests\UserRegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request, RegisterUserAction $registerUserAction, CreateUserProfileAction $createUserProfileAction): JsonResponse
    {
        try {
            $validatedData = $request->validated();
    
            DB::beginTransaction();
    
            $user = $registerUserAction->execute($validatedData);
            $userProfile = $createUserProfileAction->execute($user);
    
            DB::commit();
    
            $userResource = new UserResource($user);
            $token = $user->createToken('Auth Token')->plainTextToken;
    
            return Response::send(true, 201, 'Registration successful', ['user' => $userResource, 'token' => $token]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::send(false, 500, 'Registration failed. Please try again later.');
        }
    }

    /**
     * Login user and issue a token.
     * 
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return Response::send(false, 401, 'Invalid credentials');
        }

        $user = $request->user();
        $userResource = new UserResource($user);
        $token = $user->createToken('Auth Token')->plainTextToken;

        return Response::send(true, 200, 'Login successful', ['user' => $userResource, 'token' => $token]);
    }

    /**
     * Get the profile for the authenticated user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse
     */
    public function getUserProfile(Request $request)
    {
        $user = $request->user();
        $userResource = new UserResource($user);

        return Response::send(true, 200, 'User profile retrieved successfully', $userResource);
    }

    /**
     * Logout user by revoking tokens.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return Response::send(true, 200, 'User logged out successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
        if (!Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response([
                'message' => "You don't have permission to access this page.",
            ], 401);
        }
        
 /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->is_admin) {
            Auth::logout();
            return response([
                'message' => "You don't have permission to access this page.",
            ], 403);
        }

        $token = $user->createToken('admin')->plainTextToken;
        
        return response([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Logged in successfully',
        ]);
        
    }

    public function logout(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user) { 
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response([
            'message' => 'Logged out successfully',
        ]);
    } else {
        return response([
            'message' => 'No authenticated user found.',
        ], 401);
    }
}

public function getUser(Request $request){
    return new UserResource($request->user());
}
    
}

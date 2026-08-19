<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(Request $request){
        $this->validateLogin($request);

        if(Auth::attempt($request->only('email', 'password'))){
            $user = Auth::user();
            return response()->json([
                'token' => $request->user()->createToken($request->name)->plainTextToken,
                'message' => 'Login successful',
                'user' => new UserResource($user),
            ], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }

    public function validateLogin(Request $request){
        return $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required','min:8',
            'name' => 'required'
        ]);
    }
}
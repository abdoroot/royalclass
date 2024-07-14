<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = [
                "email" => $validatedData['email'],
                "password" => $validatedData['password']
            ];

            if (Auth::attempt($credentials)) {
                //reset throttle
                $request->session()->regenerate();

                $user = User::where(["email" => $validatedData['email']])->first();
                //genetate token
                $token = $user->createToken('login')->plainTextToken;
                $user["api_token"] = $token;

                return response()->json(['user' => $user, 'message' => 'Login successful'], 200);
            } else {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
        } catch (ValidationException $e) {
            // 422 Unprocessable Content
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed. Please try again later.'], 500);
        }
    }
}

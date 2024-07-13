<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class AdminController extends Controller
{
    public function listUsers()
    {
        try {
            $users = User::where("is_admin",0)->get();
            return response()->json(['users' => $users], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch users.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'is_admin' => 'nullable|boolean',
            ]);

            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->is_admin = $request->input('is_admin', false);
            $user->save();

            $token = $user->createToken('login')->plainTextToken;
            $user["api_token"] = $token;

            return response()->json(['user' => $user, 'message' => 'User created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['user' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'User not found.', 'error' => $e->getMessage()], 404);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'is_admin' => 'nullable|boolean',
            ]);

            $user = User::findOrFail($id);
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if ($request->filled('password')) {
                $user->password = bcrypt($validatedData['password']);
            }
            $user->is_admin = $request->input('is_admin', false);
            $user->save();

            return response()->json(['user' => $user, 'message' => 'User updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function postList($id)
    {
        try {
            $posts = Post::all();
            return response()->json(['posts' => $posts], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch post.', 'error' => $e->getMessage()], 500);
        }
    } 
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'string|required|max:255',
            'email' => 'email|required|max:255|unique:users,email',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'userInfo' => $user
        ], 201);
    }

    /**
     * Login the specified user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required|max:255',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        return response()->json([
            'token' => $user->createToken('access_token')->plainTextToken,
            'user'  => $user
        ]);
    }

    /**
     * Logout the specified user in storage.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}

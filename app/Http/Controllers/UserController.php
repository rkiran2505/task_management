<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    public function index()
{
    return view('home');
}

public function register(Request $request)
{
    // Log the registration request data
    Log::info('Registration request:', $request->all());
    
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'], // Ensure unique email
        // 'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
    
    // If validation fails, return validation errors
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }
    
    // Create the user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), // Hash the password
    ]);
    
    // Return a structured JSON response with the user's data
    return response()->json([
        'message' => 'User successfully registered',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ], 201); // 201 Created HTTP status code
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        
        // Generate token for the user
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}
// User Logout method
public function logout(Request $request)
{
    // Revoke the user's tokens
    $request->user()->tokens->each(function ($token) {
        $token->delete();
    });

    // Return a response confirming logout
    return response()->json(['message' => 'Logged out successfully']);
}
}



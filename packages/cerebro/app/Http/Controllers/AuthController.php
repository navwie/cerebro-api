<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email'=> 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'post_back_amount' => $request->post_back_amount,
            'personal_min_req' => $request->personal_min_req,
            'personal_channel_id' => $request->personal_channel_id,
            'personal_password' => $request->personal_password,
            'lead_channel_id' => $request->lead_channel_id,
            'lead_password' => $request->lead_password,
        ]);

        $token = $user->createToken('cerebroclienttoken')->plainTextToken;

        $response = [
            'user' => $user->toArray(),
            'token' => $token
        ];

        return response()->xml(['form' => $response], 201, [], 'response', 'utf-8');;
    }

    public function login(Request $request) {
        $request->validate([
            'email'=> 'required|string',
            'password' => 'required|string'
        ]);

        //Check email
        $user = User::where('email', $request->email)->first();

        //Check password
        if(!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('cerebroclienttoken')->plainTextToken;

        $response = [
            'user' => $user->toArray(),
            'token' => $token
        ];

        return response()->xml(['form' => $response], 201, [], 'response', 'utf-8');
    }

    public function logoutAll(Request $request) {
        auth()->user()->tokens()->delete();

        return ['message' => 'Logged out all forms'];
    }

    public function logout(Request $request, $id) {
        User::find($id)->tokens()->delete();
        return ['message' => 'Logged out'];
    }
}

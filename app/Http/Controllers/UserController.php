<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type) {
            return User::where('type', $request->type)->get();
        }
        return User::all();
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email|unique:users,email',
                'name' => 'required|string|max:255',
                'type' => 'required',
                'password' => 'required|string|min:6',
            ]
        );

        $user = User::create(
            [
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'type' => $request->type,
                'email' => $request->email,
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        $user->token = $token;

        return response(
            [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->token,
            ],
            201
        );
    }

    public function show(User $user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'type' => $user->type,
        ];
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email|',
                'password' => 'required|string|min:6'
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => 'The provided credentials are incorrect.'], 404);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        $user->token = $token;
        
        return response(
            [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->token,
            ],
            201
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email' 
            ]
        );
        
        try {
            $mail = Password::sendResetLink($request->only('email'));

            switch ($mail) {
            case Password::RESET_LINK_SENT:
                return response()->json(['errors' => 'Reset password link sent on your email id.'], 201);
            case Password::INVALID_USER:
                return response()->json(['errors' => 'We can\'t find a user with that email address.'], 404);
            }
            
        } catch (\Swift_TransportException $ex) {
            return response()->json(['errors' => $ex->getMessage(), 500]);
        } catch (Exception $ex) {
            return response()->json(['errors' => $ex->getMessage(), 500]);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]
        );

        if (!Hash::check($request->get('current_password'), $request->user()->password)) {
            return response()->json(['errors' => 'The provided password does not match your current password.'], 404);
        }

        $request->user()->forceFill(
            [
                'password' => Hash::make($request->get('password')),
            ]
        )->save();

        return response(['data' => 'Password set successfully.'], 201);
    }
}

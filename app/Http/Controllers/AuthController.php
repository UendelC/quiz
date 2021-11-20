<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  use ApiResponser;

    public function register(Request $request)
    {
        $attr = $request->validate(
            [
              'name' => 'required|string|max:255',
              'email' => 'required|string|email|unique:users,email',
              'password' => 'required|string|min:6',
              'type' => 'required|in:participant,teacher',
              'subject' => 'required',
            ]
        );

    $user = User::create([
      'name' => $attr['name'],
      'password' => bcrypt($attr['password']),
      'email' => $attr['email'],
      'type' => $attr['type'],
    ]);

    if ($request->type === 'teacher') {
      Subject::create(
          [
              'name' => $request->subject,
              'teacher_id' => $user->id,
          ]
      );
  }

  if ($request->type === 'participant') {
      Subject::find($request->subject)->participants()->attach($user);
  }

    return $this->success([
      'token' => $user->createToken('API Token')->plainTextToken,
      'user' => $user,
    ]);
  }

  public function login(Request $request)
  {
    $attr = $request->validate([
      'email' => 'required|string|email|',
      'password' => 'required|string|min:6'
    ]);

    if (!Auth::attempt($attr)) {
      return $this->error('Credentials not match', 401);
    }

    return $this->success([
      'token' => auth()->user()->createToken('API Token')->plainTextToken,
      'user' => auth()->user(),
    ]);
  }

  public function logout()
  {
    if (auth()->user()) {

      auth()->user()->tokens()->delete();
    }

    return [
      'message' => 'Tokens Revoked'
    ];
  }
}

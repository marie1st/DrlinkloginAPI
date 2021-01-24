<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
   
  public function resendConfirmationEmail(User $user) {
    if ($user->hasVerifiedEmail()) {
      return response()->json(["message" => "คุณได้ทำการยืนยันอีเมลแล้ว"], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json($user);
}

  public function verify($user_id, Request $request) {
    if (!$request->hasValidSignature()) {
      return response()->json(["message" => "Invalid/Expired url provided."], 401);
    }

    $user = User::findOrFail($user_id);

    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
    }

    return redirect()->to(env('GO_THAILAND_HOST'));
}

  public function login(Request $request) {
    try{
      $credentials = $request->only('email', 'password');
      if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('app')->accessToken;
        return response()->json([
          'message' => 'success',
          'token' => $token,
          'user' => $user
        ]);
      }
    }catch(\Exception $exception) {
        return response([
          'message' => $exception-getMessage()
        ], 400);
    }
    return response()->json([
      'message' => 'อีเมล หรือ รหัสผ่าน ไม่ถูกต้อง'
    ], 403);
  }

  public function user()
  {
    return Auth::user();
  }

  public function loginSocial(Request $request) {
    $provider = $request['provider'];
    $user_data = Socialite::driver($provider)->stateless()->userFromToken($request['token']);

    $user = User::where([
      'email' => $user_data->getEmail(),
      // 'login_provider_name' => $provider
    ])->first();

    if (!$user) {
      $api_token = Str::random(32);

      $user = new User([
        'username' => $user_data->getName(),
        'email' => $user_data->getEmail(),
        'password' => Hash::make($api_token),
        'login_provider_name' => $provider,
        'login_provider_token' => $user_data->token,
        'login_provider_user_id' => $user_data->getId(),
        'profile' => [
          'name' => $user_data->getName(),
          'avatar' => $user_data->getAvatar(),
        ]
      ]);

      $user->api_token = $api_token;

      $user->save();
    }

    Auth::login($user);

    return response()->json([
      'user_id' => Auth::id(),
      'token' => Auth::user()->api_token
    ]);
  }

  public function register(RegisterRequest $request) {
    $user = User::create([
      'username' => $request['username'],
      'email' => $request['email'],
      'password' => Hash::make($request['password']),
    ]);

    $user->save();

    return response()->json($user);
  }

  public function me(Request $request) {
    return $request->user();
  }


  public function reset_password_email(Request $request) {
    $user = User::where([
      'email' => $request['email']
    ])->first();

    if (!$user) {
      abort(404);
    }

    $user->password = Hash::make($request['password']);

    $user->save();

    return response()->json([
      'user_id' => $user->id,
      'token' => $user->api_token
    ]);
  }

  public function reset_password_mobile(Request $request) {
    $user = User::where([
      'mobile_number' => $request['mobile_number']
    ])->first();

    if (!$user) {
      abort(404);
    }

    $user->password = Hash::make($request['password']);

    $user->save();

    return response()->json([
      'user_id' => $user->id,
      'token' => $user->api_token
    ]);
  }

  public function updateProfile(Request $request) {
    $user = $request->user();

    if ($request->has('name')) {
      $user->username = $request['name'];
    }

    $user->profile = array_merge((array) $user->profile, $request->all());

    $user->save();

    return $user;
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Google2FA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'password' => 'automatic_generate_password'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token])->withHeaders(['auth-token' => $token]);
    }

    public function logout(Request $request){

        // Logout the user
        Auth::guard('web')->logout();
        // Revoke all tokens for the authenticated user
        $user = auth()->user();
        $user->tokens()->delete();
        Cookie::queue(Cookie::forget('access_token'));

        return [
            'message' => 'You have successfully logged out'
        ];
    }

    public function authenticateUser (Request $request){
        $otpCode = $request->input('otp');
        $userLoginToken = $request->input('userLoginToken');

        //Verify if token matches (so that I know who is trying to sign in and that it's valid)
        $validLoginTokenUser = User::validateLoginToken($userLoginToken);
        if (!$validLoginTokenUser) {
            return response()->json(['error' => 'Invalid login token provided,
            redirect to Google Callback again'], 401);
        }

        $user = User::find($validLoginTokenUser->id);
        $validOTP = User::validateGoogleOTPCode($user, $otpCode);
        if (!$validOTP){
            return response()->json(['message' => 'Código OTP incorrecto, verifica e intenta nuevamente'], 500);
        }
        //Set google2fa_enabled = true so user is redirected to validateOTP screen directly
        User::setGoogle2FAAuthEnabled($user);

        // Revoke all tokens for the user if any previous exist
        $user->tokens()->delete();
        //Authenticate user and set auth_token
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token])->withHeaders(['auth-token' => $token]);
    }

    public function userInfo(Request $request){
        return response()->json(['Message' => "You did it!"]);
    }

}

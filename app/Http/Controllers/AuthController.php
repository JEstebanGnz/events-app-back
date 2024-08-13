<?php

namespace App\Http\Controllers;

use App\Models\Google2FA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{

    public function handleGoogleAuth(Request $request)
    {

        $frontUserInfo = $request->input('user');
        $user = User::where('email', $frontUserInfo["email"])->first();

        if (!$user) {
            $user = User::updateOrCreate(
            [
            'email' => $frontUserInfo["email"]
            ],
            [
                'name' => $frontUserInfo["name"],
                'password' => 'automatic_generate_password',
            ]);
        }

        $accessibleEventsByUser = DB::table('restricted_event_users')
            ->where('user_id','=',$user->id)->get();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'events_available' => $accessibleEventsByUser,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
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

    public function userInfo(Request $request){
        return response()->json(['Message' => "You did it!"]);
    }

}

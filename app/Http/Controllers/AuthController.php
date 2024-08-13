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
use phpseclib3\Crypt\Hash;


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


    public function handleCredentialsAuth(Request $request)
    {
        $frontUserInfo = $request->input('user');

        //1) Verify there's an user associated with that email
        $user = DB::table('users')->where('email','=', $frontUserInfo["email"])->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'There is no user associated with that email'
            ], 404);
        }

        //2) TODO Check if user can actually access this event
        //TODO Send eventId from the frontend and check here

        //3) Compare the password typed with the one stored in the DB.
        if (!\Illuminate\Support\Facades\Hash::check($frontUserInfo['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wrong password'
            ], 401);
        }

        $accessibleEventsByUser = DB::table('restricted_event_users')
            ->where('user_id','=',$user->id)->get();

       //3) User completely validated, return user info
        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful',
            'data' => [
                'user' => $user,
                'events_available' => $accessibleEventsByUser
            ]
        ]);

    }

    public function userInfo(Request $request){
        return response()->json(['Message' => "You did it!"]);
    }

}

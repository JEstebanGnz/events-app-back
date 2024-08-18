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

        $restrictedEventsAccessible = DB::table('restricted_event_users')
            ->where('user_id', '=', $user->id)->get();

        $restrictedEventsAccessibleIds = array_unique(array_column($restrictedEventsAccessible->toArray(), 'event_id'));

        $accessibleEventsByUser = DB::table('events')->orWhereIn('id', $restrictedEventsAccessibleIds)
            ->orWhere('restricted_access', '=', 0)->get();

        $userEventsAdmin = DB::table('event_admin_users')->where('user_id', '=', $user->id)->get();
        $userEventsAdminIds = array_unique(array_column($userEventsAdmin->toArray(), 'event_id'));


        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful',
            'data' => [
                'user' => $user,
                'events_available' => $accessibleEventsByUser,
                'user_events_admin'=> $userEventsAdminIds
            ]
        ]);
    }


    public function handleCredentialsAuth(Request $request)
    {
        $frontUserInfo = $request->input('user');
//        $eventId = $request->input('eventId');

        //1) Verify there's a user associated with that email
        $user = DB::table('users')->where('email', '=', $frontUserInfo["email"])->first();

        if (!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'Your email doesn\'t exist in our records!'
            ], 401);
        }

        //3) Compare the password typed with the one stored in the DB.
        if (!\Illuminate\Support\Facades\Hash::check($frontUserInfo['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wrong password!'
            ], 401);
        }

        //3) User completely validated, return user info
        return response()->json(['email' => $user->email]);
    }

    public function userInfo(Request $request){
        return response()->json(['Message' => "You did it!"]);
    }

}

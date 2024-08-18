<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::with('roles')->get());
    }

    public function userInfo(Request $request)
    {
        $email= $request->input('email');

        $user = DB::table('users')->where('email', '=', $email)->first();

        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 401);
        }

        $restrictedEventsAccessible = DB::table('restricted_event_users')
            ->where('user_id', '=', $user->id)->get();
        $restrictedEventsAccessibleIds = array_unique(array_column($restrictedEventsAccessible->toArray(), 'event_id'));
        $accessibleEventsByUser = DB::table('events')->orWhereIn('id', $restrictedEventsAccessibleIds)
            ->orWhere('restricted_access', '=', 0)->get();
        $userEventsAdmin = DB::table('event_admin_users')->where('user_id', '=', $user->id)->get();
        $userEventsAdminIds = array_unique(array_column($userEventsAdmin->toArray(), 'event_id'));

        // Merge events data into the user object
        $user = (array) $user; // Convert stdClass object to an array
        $user['eventsAvailable'] = $accessibleEventsByUser;
        $user['userEventsAdmin'] = $userEventsAdminIds;

        //3) User completely validated, return user info
        return response()->json([
                'user' => $user,
            ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //assign default role
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

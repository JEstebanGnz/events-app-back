<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;
use phpseclib3\Crypt\Hash;

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

        $email = $request->input('email');

        $user = User::with([
            'restrictedEvents',
            'eventsAdmin',
            'roles'])->where('email', '=', $email)->first();

        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 401);
        }

        $nonRestrictedEvents = Event::where('restricted_access', '=', false)->get();
        $user->accessible_events = $nonRestrictedEvents->merge($user->restrictedEvents);
        return response()->json($user);
    }

    public function hasUnreadMessages(Request $request, $userId){

        $hasUnreadMessages = DB::table('users')->where('id', '=', $userId)
            ->where('has_unread_messages', '=', 1)->first();
        if($hasUnreadMessages){
            return response()->json(['hasUnreadMessages' => true]);
        }
        else{
            return response()->json(['hasUnreadMessages' => false]);
        }
    }

    public function markReadMessages(Request $request, $userId){

        DB::table('users')->where('id', '=', $userId)
            ->where('has_unread_messages', '=', 1)->update(['has_unread_messages' => 0]);
    }

    public function updateRoles(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $roles = $request->input('roles'); // Array of role IDs
        // Detach all roles first
        $user->roles()->detach();

        // Attach the new roles
        foreach ($roles as $roleId) {
            $user->roles()->attach($roleId);
        }
        return response()->json(['message' => 'Roles updated successfully']);
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
        $users = $request->input('users'); // Expecting an array of user objects
        $createdUsers = [];
        $failedUsers = [];

        try {
            foreach ($users as $user) {
                $userExists = DB::table('users')->where('email', '=', $user["email"])->first();

                if ($userExists) {
                    // If user already exists, add to failedUsers
                    $failedUsers[] = [
                        'email' => $user["email"],
                        'message' => 'Email already exists!'
                    ];
                } else {
                    // Create user
                    $userCreated = User::updateOrCreate(
                        ['email' => $user["email"]],
                        [
                            'name' => $user["name"],
                            'password' => \Illuminate\Support\Facades\Hash::make($user["password"])
                        ]
                    );

                    // Assign default role to user
                    $defaultRoleId = Role::getRoleIdByName('user');
                    DB::table('role_user')->updateOrInsert(['user_id' => $userCreated->id, 'role_id' => $defaultRoleId]);

                    // Add successfully created user to the response
                    $createdUsers[] = [
                        'email' => $user["email"],
                        'name' => $user["name"]
                    ];
                }
            }

            return response()->json([
                'message' => 'User creation process completed',
                'created_users' => $createdUsers,
                'failed_users' => $failedUsers
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $email)
    {
        $user = User::with([
            'restrictedEvents',
            'eventsAdmin',
            'roles'])->where('email', '=', $email)->first();

        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 401);
        }

        $nonRestrictedEvents = Event::where('restricted_access', '=', false)->get();
        $user->accessible_events = $nonRestrictedEvents->merge($user->restrictedEvents);
        return response()->json($user);
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

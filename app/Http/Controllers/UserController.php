<?php

namespace App\Http\Controllers;

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
        $user = $request->input('user');

        try{
            $userExists = DB::table('users')->where('email', '=', $user["email"])->first();

            if($userExists){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email already exists!'
                ], 401);
            }

            else{
                $userCreated = User::updateOrCreate(['email' => $user["email"]],
                    ['name' => $user["name"],
                        'password' => \Illuminate\Support\Facades\Hash::make($user["password"])]);

                //Set default role to user
                $defaultRoleId = Role::getRoleIdByName('user');
                DB::table('role_user')->updateOrInsert(['user_id' => $userCreated->id, 'role_id' => $defaultRoleId]);

                return response()->json([
                    'message' => 'User created successfully',
                    'user' => $user
                ], 201);

            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the user',
                'error' => $e->getMessage()
            ], 500);
        }

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

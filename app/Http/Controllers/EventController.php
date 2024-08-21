<?php

namespace App\Http\Controllers;

use App\Models\Event;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class EventController extends Controller
{
    /**
     * Get all the events from the DB.
     */
    public function index(Request $request)
    {
        $restrictedAccess = $request->query('restrictedAccess');
        $query = Event::with(['users.roles', 'admins.roles']);
        if ($restrictedAccess !== null){
            $query->where('restricted_access','=', true);
        }
        $events = $query->get();
        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Assign Users to the event
     */
    public function assignUsers(Request $request, string $id)
    {
        try {
            $event = Event::findOrFail($id);

            $users = $request->input('users');
            $userIds = array_map(function ($user){
                return $user['id'];
            }, $users);
            $event->users()->sync($userIds);

            $admins = $request->input('admins');
            $adminIds = array_map(function ($admin){
                return $admin['id'];
            }, $admins);
            $event->admins()->sync($adminIds);
            return response()->json(['message' => 'Event users updated successfully']);
        }
        catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred when uploading event users'], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
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

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
        Log::info('assignUsers method called', [
            'id' => $id,
            'request' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $validatedData = $request->validate([
                'users' => 'required|array',
                'users.*.id' => 'required|exists:users,id',
                'admins' => 'required|array',
                'admins.*.id' => 'required|exists:users,id',
            ]);

            $event = Event::findOrFail($id);

            $userIds = array_column($validatedData['users'], 'id');
            $event->users()->sync($userIds);

            $adminIds = array_column($validatedData['admins'], 'id');
            $event->admins()->sync($adminIds);

            return response()->json(['message' => 'Event users updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Event not found', ['id' => $id]);
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            Log::error('An error occurred when updating event users', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'An error occurred when updating event users'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $event = Event::where('id', '=', $id)->with(['users'])->first();
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

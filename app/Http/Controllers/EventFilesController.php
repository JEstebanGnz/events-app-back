<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id)
    {
        $event = Event::findOrFail($id);
        $files = DB::table('event_files')
            ->where('event_id','=',$event->id)
            ->where('type','!=', 'logo')
            ->orderBy('created_at','desc')->get();
        $files->transform(function ($file) {
            $file->payload = json_decode($file->payload, true)[0]; // Decode JSON before sending
            return $file;
        });
        return response()->json($files);
    }

    /**
     * Returns the logo associated to the event
     */
    public function getLogo(int $id)
    {
        $event = Event::findOrFail($id);
        $logoFile = DB::table('event_files')
            ->where('event_id','=', $event->id)
            ->where('type', '=','logo') // Assuming 'type' column identifies file types
            ->first();

        if ($logoFile) {
            $logoFile->payload = json_decode($logoFile->payload, true)[0]; // Decode JSON if necessary
        }
        return response()->json($logoFile);
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

<?php

namespace App\Http\Controllers;

use App\Models\EventMeeting;
use Illuminate\Http\Request;

class EventMeetingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id)
    {
        return response()->json(EventMeeting::where('event_id','=', $id)
            ->where('visible','=',true)->orderBy('start_date','asc')
            ->select(['id',
                'name', 'description','location',
                'start_date','end_date','online_link','speaker'])->get());
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

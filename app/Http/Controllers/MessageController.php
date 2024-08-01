<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id)
    {
        $event = Event::findOrFail($id);
        $messages = DB::table('event_messages')
            ->where('event_id','=',$event->id)
            ->orderBy('created_at','desc')->get();
        return response()->json($messages);
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
    public function store(Request $request, string $id)
    {

        $messageContent = $request->input('message');
        try {
            Message::create(['content' => $messageContent,
                'posted_by' => 1, 'event_id' => $id]);
            return response()->json(['message'=> 'Message sent correctly']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred when adding the message'], 500);
        }



    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMeeting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_meetings';
    protected $guarded = [];

    use HasFactory;
}

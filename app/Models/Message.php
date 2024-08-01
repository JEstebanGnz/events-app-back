<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'posted_by', 'event_id'
    ];

    use HasFactory;
}

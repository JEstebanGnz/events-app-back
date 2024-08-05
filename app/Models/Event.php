<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    use HasFactory;
}

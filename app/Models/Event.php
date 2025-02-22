<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $incrementing = false;
    protected $fillable = [
        "id",
        "name",
        "date",
        "description",
        "emails",
        "status",
    ];

    // protected $guarded = ["id"];
}

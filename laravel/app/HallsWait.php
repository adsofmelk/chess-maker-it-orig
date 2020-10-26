<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HallsWait extends Model
{
    protected $fillable = ['channel','hall','user','level',];

    protected $table = 'halls_wait';

    public $timestamps = false;
}

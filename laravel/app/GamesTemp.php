<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GamesTemp extends Model
{
    protected $table = 'games_temp';

    protected $fillable = ['user1', 'user2', 'channel', 'distribution', 'status','user_begin'];

    public $timestamps = false;
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionesAbly extends Model
{
    protected $fillable = ['channel_ably','status','user_id',];

    protected $table = 'sessiones_ably';

    public $timestamps = false;
}

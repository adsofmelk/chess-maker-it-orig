<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = ['payload', 'user_id', 'ip_address', 'id'];
}

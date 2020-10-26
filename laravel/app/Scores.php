<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    protected $fillable = [
        'value_min', 'value_max', 'favor', 'contra',
    ];
}

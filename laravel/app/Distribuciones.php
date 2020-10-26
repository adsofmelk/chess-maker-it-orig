<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distribuciones extends Model
{
    protected $table = 'distribuciones';

    protected $fillable = [
        'distribution', 'hall', 'level', 'status',
    ];

    public $timestamps = false;
}

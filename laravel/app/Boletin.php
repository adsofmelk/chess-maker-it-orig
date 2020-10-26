<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boletin extends Model
{
    protected $table = 'boletin';

    protected $fillable = ['name', 'email', 'token', 'activo'];

    // public $timestamps = false;
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Editorial extends Model
{
    protected $table = 'editorial';

    protected $fillable = ['nombre','texto', 'default', 'reset', 'orden','activo'];

    public $timestamps = false;
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cabezote extends Model
{
    protected $table = 'cabezotes';

    protected $fillable = ['titulo', 'resumen', 'texto_boton', 'enlace_boton', 'foto','orden'];

    public $timestamps = false;
}

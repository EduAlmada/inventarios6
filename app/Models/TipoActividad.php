<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model
{
    protected $fillable = [
        'nombre',
        'modifica_stock',
        'signo_stock',
    ];

    protected $casts = [
        'modifica_stock' => 'boolean',
        'signo_stock' => 'integer',
    ];
}

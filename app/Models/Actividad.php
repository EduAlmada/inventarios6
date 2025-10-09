<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo',
        'descripcion',
        'operador',
        'pedido',
        'bultos',
        'fecha_actividad',
        'zona_id',
        'deposito',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
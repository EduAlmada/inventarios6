<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'articulo_id',
        'user_id',
        'zona_id',
        'tipo',
        'orden',
        'motivo',
        'cliente',
        'cantidad',
    ];

    // Un movimiento pertenece a un artículo
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
    
    // Un movimiento pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un movimiento pertenece a una zona
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'articulo_id',
        'deposito_id', // Si usas deposito_id
        'zona_id',     // O zona_id, dependiendo de la granularidad
        'cantidad',
    ];
    
    // El registro de stock pertenece a un artículo y a una zona
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
    
    public function deposito()
    {
        return $this->belongsTo(Deposito::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}

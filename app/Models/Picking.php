<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Picking extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_id',
        'user_id',
        'articulo_id',
        'zona_id',
        'fecha_inicio',
        'fecha_fin',
        'cantidad',
        'caja',
        'pallet',
        'peso',
    ];

    // Indica que este registro de picking pertenece a una Nota (Cabecera)
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
    
    // Indica quién fue el responsable de este picking
    public function picker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // El artículo que se tomó
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
    
    // La zona de donde se tomó el artículo
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packing extends Model
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

    // Indica que este registro de packing pertenece a una Nota (Cabecera)
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
    
    // Indica quién fue el responsable del embalaje
    public function packer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // El artículo que se embaló
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
    
    // La zona donde se realizó el embalaje
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
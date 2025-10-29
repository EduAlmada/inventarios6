<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descripcion',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'foto',
        'EAN13',
        'DUN14',
        'unidades',
        'precio',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}

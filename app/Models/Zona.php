<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposito_id',
        'nombre',
        'pasillo',
        'descripcion',
    ];

    public function deposito()
    {
        return $this->belongsTo(Deposito::class);
    }

    public function stocks() {
    return $this->hasMany(Stock::class);
    }
}

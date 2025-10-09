<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposito extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
    
    // Un depósito tiene muchos registros de stock
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}

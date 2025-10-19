<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nota', 
        'cliente', 
        'domicilio', 
        'estado', 
        'user_id',
        'fecha_fill_rate',
        'fecha_facturado',
        'fecha_despachado',
        'fecha_entregado',
    ];

    public function items()
    {
        return $this->hasMany(NotaItem::class, 'nota_id');
    }
}
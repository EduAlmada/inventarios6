<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_id', 
        'articulo_id', 
        'cantidad_solicitada', 
        'cantidad_preparada',
        'caja',
        'pallet',
        'peso',
    ];
    
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function stock() {
    // Opcional, para enlazar con la zona/stock desde el item
    return $this->hasManyThrough(Stock::class, Articulo::class, 'id', 'articulo_id', 'articulo_id', 'id');
    }
}
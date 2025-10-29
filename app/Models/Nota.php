<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nota',
        'orden_compra', 
        'cliente', 
        'domicilio',
        'transporte',
        'domicilio_transporte', 
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

        public function pickeados()
    {
        // Suponiendo que tienes un modelo Packing para lo pickeado
        return $this->hasMany(Packing::class);
    }

    public function packeados()
    {
        // Si también usás Packing para lo packeado, filtrá por estado
        return $this->hasMany(Packing::class); 
    }

    // Para obtener solo cantidades por artículo
    public function pickeadosArray()
    {
        return $this->pickeados()->pluck('cantidad', 'articulo_id')->toArray();
    }

    public function packeadosArray()
    {
        return $this->packeados()->pluck('cantidad', 'articulo_id')->toArray();
    }
    // Para relacionar con tabla tareas_activas
    public function tareaActiva()
    {
        return $this->hasOne(TareaActiva::class, 'nota_id'); 
    }

    public function user()
    {
        // Asume que la tabla 'notas' tiene una columna 'user_id'
        return $this->belongsTo(User::class); 
    }

}
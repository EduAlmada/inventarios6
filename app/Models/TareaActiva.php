<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TareaActiva extends Model
{
        protected $table = 'tareas_activas';
    protected $fillable = [
        'user_id',
        'nota_id',
        'tipo_actividad',
        'orden_prioridad',
        'iniciada_en',
    ];

    // ✅ RELACIÓN 1: El usuario que está realizando esta tarea
    public function user()
    {
        // Enlaza la columna user_id de esta tabla con el modelo App\Models\User
        return $this->belongsTo(User::class); 
    }

    // ✅ RELACIÓN 2: La nota asociada a esta tarea
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
}

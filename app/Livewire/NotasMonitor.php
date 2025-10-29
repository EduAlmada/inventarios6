<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Nota;
use App\Models\TareaActiva;
use Livewire\WithPagination;

class NotasMonitor extends Component
{
    use WithPagination;

    public $search = '';
    // Usamos el ID de la Nota para resetear el foco si se activa una tarea
    protected $listeners = ['taskUpdated' => '$refresh']; 
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Cargar notas con la tarea activa y el usuario asociado a esa tarea
        $query = Nota::with(['user', 'tareaActiva.user']) 
            ->orderByRaw("COALESCE(tareas_activas.orden_prioridad, 99999) ASC") // Prioriza las notas con orden_prioridad (1 es la más alta)
            ->orderBy('created_at', 'desc')
            ->leftJoin('tareas_activas', 'notas.id', '=', 'tareas_activas.nota_id') // Necesario para ordenar por la columna de la relación

            // Re-seleccionar todas las columnas de notas para evitar errores de select
            ->select('notas.*'); 

        if ($this->search) {
            $query->where('nota', 'like', '%' . $this->search . '%');
        }

        $notas = $query->paginate($this->perPage);

        return view('livewire.notas-monitor', [
            'notas' => $notas,
        ]);
    }

    /**
     * Permite al administrador establecer o modificar la prioridad de una Nota.
     */
    public function setPriority($notaId, $order)
    {
        if (!auth()->user()->is_admin) {
            // Seguridad: solo el admin puede cambiar la prioridad
            session()->flash('error', 'Permiso denegado para establecer prioridad.');
            return;
        }

        $order = max(0, (int)$order);
        
        // El usuario actual (Admin) se registra como 'asignador' si no hay tarea activa
        $userId = auth()->id(); 
        
        // Actualizar o crear la tarea activa para establecer la prioridad.
        TareaActiva::updateOrCreate(
            ['nota_id' => $notaId],
            [
                // Si ya hay alguien trabajando (ej. picker), mantenemos ese user_id.
                // Si la estamos creando, asignamos temporalmente al admin, o si hay tarea activa, mantenemos su tipo
                'user_id' => $userId, 
                'tipo_actividad' => 'picking', // Valor por defecto si no hay tarea
                'orden_prioridad' => $order,
                'iniciada_en' => now(), 
            ]
        );

        $this->emitSelf('taskUpdated'); // Forzar la actualización del componente
        session()->flash('success', "Prioridad de Nota #{$notaId} establecida a {$order}.");
    }
}
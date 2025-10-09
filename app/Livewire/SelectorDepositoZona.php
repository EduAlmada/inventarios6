<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Deposito;
use App\Models\Zona;
use App\Models\TipoActividad;

class SelectorDepositoZona extends Component
{
    // Propiedades que contienen las listas (vienen del mount)
    public $depositos;
    public $zonas = [];

    // Propiedades que manejan la selección del usuario
    public $selectedDeposito = null;
    public $selectedZona = null;

    // Se ejecuta una vez al inicio
    public function mount()
    {
        // 1. Cargar todos los depósitos para el primer select
        $this->depositos = Deposito::all();
        // 2. Intentar restaurar la selección de la sesión (si la validación falló)
        if (old('zona_id')) {
            // Encuentra la zona seleccionada previamente
            $zonaAntigua = Zona::find(old('zona_id'));

            if ($zonaAntigua) {
                // Restaura la selección del depósito
                $this->selectedDeposito = $zonaAntigua->deposito_id;
                // Forzamos la ejecución de la lógica de actualización para llenar el select de Zonas
                $this->updatedSelectedDeposito($this->selectedDeposito); 
                // Finalmente, restauramos la zona
                $this->selectedZona = old('zona_id');
            }
        }
    }

    // Se ejecuta automáticamente cuando $selectedDeposito cambia
    public function updatedSelectedDeposito($deposito_id)
    {
        // 1. Resetear la zona y la lista de zonas
        $this->zonas = [];
        $this->selectedZona = null;
        
        if ($deposito_id) {
            // 2. Cargar solo las zonas que pertenecen a ese depósito
            $this->zonas = Zona::where('deposito_id', $deposito_id)->get();
            // Si hay un valor antiguo de zona y coincide con el nuevo depósito, lo restauramos
            if (old('zona_id') && $deposito_id == Zona::find(old('zona_id'))->deposito_id) {
                $this->selectedZona = old('zona_id');
            }
        }

        // Emitir el evento para sincronizar con el formulario principal (si es necesario)
        // Puedes emitir aquí la Zona ID si quieres que el formulario la tome automáticamente
    }
    
    public function render()
    {
            return view('livewire.selector-deposito-zona');
    }
}
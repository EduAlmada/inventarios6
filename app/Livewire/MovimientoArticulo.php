<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Articulo;

class MovimientoArticulo extends Component
{
    public $items = []; 

    public $codigo = '';
    public $cantidad = 1;

    protected $rules = [
        'codigo' => 'required|string|exists:articulos,codigo',
        'cantidad' => 'required|integer|min:1',
    ];

    public function mount()
    {
        // Cargar los ítems si la validación falló y se guardaron en la sesión
        if (old('livewireItems')) {
        // Decodifica y usa los datos viejos si existen
        $this->items = json_decode(old('livewireItems'), true);

        // Opcional: Emitir el evento para restaurar el campo oculto inmediatamente
        $this->dispatch('items-updated', items: $this->items);
        }
    }

    public function addItem()
    {
        $this->validate();

        // Busca el artículo en la base de datos
        $articulo = Articulo::where('codigo', $this->codigo)->first();
        
        // Verifica si ya existe en la lista del componente
        $existingItemKey = array_search($articulo->id, array_column($this->items, 'articulo_id'));

        if ($existingItemKey !== false) {
            // Si existe, suma la cantidad
            $this->items[$existingItemKey]['cantidad'] += $this->cantidad;
        } else {
            // Si no existe, agrega un nuevo ítem
            $this->items[] = [
                'articulo_id' => $articulo->id,
                'codigo' => $this->codigo,
                'cantidad' => $this->cantidad
            ];
        }

        // Emite el evento para que Alpine.js actualice el formulario
        $this->dispatch('items-updated', items: $this->items);

        $this->reset(['codigo', 'cantidad']);
    }
    
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        
        // Emite el evento para que Alpine.js actualice el formulario
        $this->dispatch('items-updated', items: $this->items);
    }
    
    public function render()
    {
        return view('livewire.movimiento-articulo');
    }
}
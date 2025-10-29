<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Articulo;
use App\Models\NotaItem;

class NotaItemsAdd extends Component
{
    public $notaId;  // ID de la nota
    public $rows = []; // Lista de filas para agregar ítems

    // Se inicializa con una fila vacía
    public function mount($notaId)
    {
        $this->notaId = $notaId;
        $this->rows = [
            ['codigo' => '', 'cantidad' => 1],
        ];
    }

    // Agregar una nueva fila
    public function addRow()
    {
        $this->rows[] = ['codigo' => '', 'cantidad' => 1];
    }

    // Eliminar fila por índice
    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows); // reindexar
    }

    // Guardar los ítems
    public function save()
    {
        foreach ($this->rows as $row) {
            $validated = $this->validateRow($row);

            $articulo = Articulo::where('codigo', $validated['codigo'])->first();
            if (!$articulo) {
                session()->flash('error', "El artículo con código {$validated['codigo']} no existe.");
                continue;
            }

            NotaItem::create([
                'nota_id' => $this->notaId,
                'articulo_id' => $articulo->id,
                'cantidad_solicitada' => $validated['cantidad'],
                'cantidad_preparada' => 0,
            ]);
        }

        // Reiniciar filas
        $this->rows = [
            ['codigo' => '', 'cantidad' => 1],
        ];

        session()->flash('success', 'Ítems agregados correctamente.');
        $this->emit('itemAdded'); // emit para Livewire si quieres refrescar otras partes
    }

    // Validación de fila
    private function validateRow($row)
    {
        return validator($row, [
            'codigo' => 'required|string|max:50',
            'cantidad' => 'required|integer|min:1',
        ])->validate();
    }

    public function render()
    {
        return view('livewire.nota-items-add');
    }
}

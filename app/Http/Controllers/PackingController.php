<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\Nota;
use App\Models\NotaItem;
use Illuminate\Http\Request;

class PackingController extends Controller
{
    public function show($notaId)
    {
        $nota = Nota::findOrFail($notaId);
        $items = $nota->items; // o como tengas tus items
        $pickeados = $nota->pickeados(); // array id => cantidad
        $packeados = $nota->packeados(); // array id => cantidad

        return view('packing.show', compact('nota', 'items', 'pickeados', 'packeados'));
    }
    
    public function store(Request $request, Nota $nota)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'numeric|min:0',
        ]);

        foreach ($request->input('items') as $itemId => $cantidad) {
            $item = NotaItem::find($itemId);

            if ($item && $cantidad > 0) {
                Packing::create([
                    'nota_id' => $nota->id,
                    'user_id' => auth()->id(),
                    'articulo_id' => $item->articulo_id,
                    'zona_id' => $request->input('zona_id'),
                    'fecha_inicio' => now(),
                    'fecha_fin' => now(),
                    'cantidad' => $cantidad,
                ]);
            }
        }

        // Actualizamos cantidad preparada
        $this->actualizarCantidadPreparada($nota);

        $nota->update([
            'estado' => 'Preparado',
            'fecha_facturado' => now(),
        ]);

        return redirect()->back()->with('success', 'Packing registrado correctamente.');
    }

    protected function actualizarCantidadPreparada(Nota $nota)
    {
        $items = NotaItem::where('nota_id', $nota->id)->get();

        foreach ($items as $item) {
            $total = Packing::where('nota_id', $nota->id)
                ->where('articulo_id', $item->articulo_id)
                ->sum('cantidad');

            $item->update(['cantidad_preparada' => $total]);
        }
    }
}

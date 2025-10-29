<?php
namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\Picking; // Modelo para tabla picking, asumo que existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PickingController extends Controller
{
    public function show(Request $request, Nota $nota)
    {
        \App\Models\TareaActiva::updateOrCreate(
        ['nota_id' => $nota->id],
        [
            'user_id' => auth()->id(),
            'tipo_actividad' => 'picking', // Establecer la actividad como 'picking'
            'iniciada_en' => now(),
            // Se mantiene el 'orden_prioridad' si fue establecido previamente
        ]
        );
    
        event('taskUpdated');

        $nota->load('items.articulo.stocks.zona.deposito');

        $pickings = \App\Models\Picking::select('articulo_id', \DB::raw('SUM(cantidad) as cantidad_pickeada'))
            ->where('nota_id', $nota->id)
            ->groupBy('articulo_id')
            ->pluck('cantidad_pickeada', 'articulo_id')
            ->toArray();

            // 🔹 Cantidad solicitada por artículo (de la nota)
        $cantidadesSolicitadas = $nota->items
            ->pluck('cantidad_solicitada', 'articulo_id')
            ->toArray();

        // 🔹 Calcular tiempos (primer y último picking)
        $primerPicking = \App\Models\Picking::where('nota_id', $nota->id)->min('created_at');
        $ultimoPicking = \App\Models\Picking::where('nota_id', $nota->id)->max('created_at');

        $duracionHoras = null;
        if ($primerPicking && $ultimoPicking) {
            $primerPickingCarbon = Carbon::parse($primerPicking);
            $ultimoPickingCarbon = Carbon::parse($ultimoPicking);
            $duracionHoras = round($ultimoPickingCarbon->diffInSeconds($primerPickingCarbon) / 3600, 2);
        }

        // 🔹 Generar estructura de items
        $itemsConZona = collect();
        $totalSolicitado = 0;
        $totalPickeado = 0;

        foreach ($nota->items as $item) {
            $cantidadSolicitada = $item->cantidad_solicitada;
            $cantidadPickeada = $pickings[$item->articulo_id] ?? 0;
            $porcentaje = $cantidadSolicitada > 0 ? round(($cantidadPickeada / $cantidadSolicitada) * 100, 1) : 0;

            $totalSolicitado += $cantidadSolicitada;
            $totalPickeado += $cantidadPickeada;

            if ($item->articulo && $item->articulo->stocks->count() > 0) {
                foreach ($item->articulo->stocks as $stock) {
                    $itemsConZona->push([
                        'nota_item_id' => $item->id,
                        'articulo_id' => $item->articulo_id,
                        'articulo_codigo' => $item->articulo->codigo ?? 'N/D',
                        'descripcion' => $item->articulo->descripcion ?? 'N/D',
                        'cantidad_solicitada' => $cantidadSolicitada,
                        'cantidad_pickeada' => $cantidadPickeada,
                        'porcentaje' => $porcentaje,
                        'zona_nombre' => $stock->zona->nombre ?? 'Sin zona',
                        'pasillo' => $stock->zona->pasillo ?? 'N/A',
                        'deposito_nombre' => $stock->zona->deposito->nombre ?? 'Sin depósito',
                        'cantidad_stock' => $stock->cantidad,
                    ]);
                }
            } else {
                $itemsConZona->push([
                    'nota_item_id' => $item->id,
                    'articulo_id' => $item->articulo_id,
                    'articulo_codigo' => $item->articulo->codigo ?? 'N/D',
                    'descripcion' => $item->articulo->descripcion ?? 'N/D',
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'cantidad_pickeada' => $cantidadPickeada,
                    'porcentaje' => $porcentaje,
                    'zona_nombre' => 'Sin zona',
                    'pasillo' => 'N/A',
                    'deposito_nombre' => 'Sin depósito',
                    'cantidad_stock' => 0,
                ]);
            }
        }

        // Filtro de búsqueda
        $buscar = $request->input('buscar');
        if ($buscar) {
            $itemsConZona = $itemsConZona->filter(function ($item) use ($buscar) {
                return str_contains(strtolower($item['articulo_codigo']), strtolower($buscar)) ||
                    str_contains(strtolower($item['descripcion']), strtolower($buscar));
            })->values();
        }

        // 🔹 Calcular % global de picking
        $porcentajeGlobal = $totalSolicitado > 0 ? round(($totalPickeado / $totalSolicitado) * 100, 1) : 0;

        // 🔹 Ordenar: depósito → zona → pasillo → código
        $itemsConZona = $itemsConZona
            ->sortBy([
                ['deposito_nombre', 'asc'],
                ['zona_nombre', 'asc'],
                ['pasillo', 'asc'],
                ['articulo_codigo', 'asc'],
            ])
            ->values();

        return view('picking.show', compact('nota', 'pickings', 'cantidadesSolicitadas', 'itemsConZona', 'porcentajeGlobal', 'duracionHoras'));
    }

    public function store(Request $request, Nota $nota)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        $codigo = $request->input('codigo');
        $cantidad = $request->input('cantidad');

        // Buscar artículo por código en los items de la nota
        $item = $nota->items()->whereHas('articulo', function($query) use ($codigo) {
            $query->where('codigo', $codigo);
        })->first();

        if (!$item) {
            return redirect()->back()->withErrors(['codigo' => 'El código no corresponde a ningún artículo de esta nota.'])->withInput();
        }

        // Suma total ya pickeada (sumando registros en tabla picking)
        $totalPickeado = Picking::where('nota_id', $nota->id)
            ->where('articulo_id', $item->articulo_id)
            ->sum('cantidad');

        $cantidadSolicitada = $item->cantidad_solicitada;

        if (($totalPickeado + $cantidad) > $cantidadSolicitada) {
            return redirect()->back()->withErrors(['cantidad' => "La cantidad supera la cantidad solicitada. (Máximo disponible: " . ($cantidadSolicitada - $totalPickeado) . ")"])->withInput();
        }

        // Guardar nuevo registro de picking
        Picking::create([
            'nota_id' => $nota->id,
            'articulo_id' => $item->articulo_id,
            'cantidad' => $cantidad,
            'user_id' => Auth::id(), 
            'fecha_inicio'=> now(),
            'fecha_fin'   => now(),
        ]);

        return redirect()->back()->with('success', 'Picking registrado correctamente.');
    }

    public function updateCantidades(Request $request, Nota $nota)
    {
        $request->validate([
            'pickeados' => 'array',
            'pickeados.*' => 'integer|min:0'
        ]);

        foreach ($request->pickeados ?? [] as $articuloId => $cantidad) {
            // Buscamos el último registro de picking del artículo
            $item = $nota->items()->where('articulo_id', $articuloId)->first();
            if (!$item) continue;

            // Total actual pickeado
            $totalAnterior = \App\Models\Picking::where('nota_id', $nota->id)
                ->where('articulo_id', $articuloId)
                ->sum('cantidad');

            // Diferencia (positivo o negativo)
            $diferencia = $cantidad - $totalAnterior;

            if ($diferencia != 0) {
                // Registramos la corrección como nuevo picking (+ o -)
                \App\Models\Picking::create([
                    'nota_id' => $nota->id,
                    'articulo_id' => $articuloId,
                    'cantidad' => $diferencia,
                    'user_id' => auth()->id(),
                    'fecha_inicio' => now(),
                    'fecha_fin' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Cantidades de picking actualizadas correctamente.');
    }


    // app/Http/Controllers/PickingController.php

    public function liberarPicking(Nota $nota)
    {
        $tareaActiva = \App\Models\TareaActiva::where('nota_id', $nota->id)->first();
        $usuarioActual = auth()->user();

        if ($tareaActiva) {
            // Verifica si el usuario actual es el dueño O un administrador
            $permisoParaLiberar = 
                ($tareaActiva->user_id === $usuarioActual->id) || 
                ($usuarioActual->is_admin);

            if ($permisoParaLiberar) {
                $tareaActiva->delete();
                event('taskUpdated'); 
                return redirect()->route('notas.index')->with('success', "La Nota #{$nota->nota} ha sido liberada del pool de trabajo.");
            } else {
                // Error de permiso
                return redirect()->back()->with('warning', "No tienes permiso para liberar esta Nota...");
            }
        }
        
        // Si no hay tarea, redirige
        return redirect()->route('notas.index')->with('warning', "La Nota #{$nota->nota} no estaba marcada como activa...");
    }

}
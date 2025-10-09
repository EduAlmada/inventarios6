<?php

namespace App\Http\Controllers;

use App\Models\TipoActividad;
use Illuminate\Http\Request;

class TipoActividadController extends Controller
{
    // Muestra la lista de tipos de actividad con el formulario de creación.
    public function index()
    {
        $tipos = TipoActividad::all();
        return view('tipos.index', compact('tipos'));
    }

    // Procesa y guarda un nuevo tipo de actividad.
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:tipo_actividads,nombre|max:255',
            'signo_stock' => 'required|integer|in:-1,0,1', // Nuevo: 1 (Positivo), 0 (Neutro), -1 (Negativo)
        ]);
        
        // El campo modifica_stock se basa en si el signo es diferente de 0.
        // Esto hace la lógica más limpia y consistente.
        $modifica_stock = $request->input('signo_stock') != 0;

        TipoActividad::create([
            'nombre' => $request->input('nombre'),
            'signo_stock' => $request->input('signo_stock'),
            'modifica_stock' => $modifica_stock,
        ]);

        return redirect()->route('tipos.index')->with('success', 'Tipo de actividad creado con éxito.');
    }

    // Muestra el formulario para editar un tipo de actividad existente.
    public function edit(TipoActividad $tipo)
    {
        return view('tipos.edit', compact('tipo'));
    }

    // Procesa y actualiza un tipo de actividad existente.
    public function update(Request $request, TipoActividad $tipo)
    {
        $request->validate([
            'nombre' => 'required|string|unique:tipo_actividads,nombre,' . $tipo->id . '|max:255',
            'signo_stock' => 'required|integer|in:-1,0,1', // Nuevo
        ]);
        
        // El campo modifica_stock se basa en si el signo es diferente de 0.
        $modifica_stock = $request->input('signo_stock') != 0;

        $tipo->update([
            'nombre' => $request->input('nombre'),
            'signo_stock' => $request->input('signo_stock'),
            'modifica_stock' => $modifica_stock, // Actualiza el booleano basado en el signo
        ]);

        return redirect()->route('tipos.index')->with('success', 'Tipo de actividad actualizado con éxito.');
    }

    // Elimina un tipo de actividad.
    public function destroy(TipoActividad $tipo)
    {
        $tipo->delete();
        return redirect()->route('tipos.index')->with('success', 'Tipo de actividad eliminado con éxito.');
    }
}
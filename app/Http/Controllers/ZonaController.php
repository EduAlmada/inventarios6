<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\Deposito;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zonas = Zona::all();
        $depositos = Deposito::all();
        return view('zonas.index', compact('zonas', 'depositos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        // La regla 'unique' verifica que la combinación de nombre y depósito no exista.
        'nombre' => [
            'required',
            'string',
            'max:255',
            \Illuminate\Validation\Rule::unique('zonas')->where(function ($query) use ($request) {
                return $query->where('deposito_id', $request->deposito_id);
            }),
        ],
        'deposito_id' => 'required|exists:depositos,id',
        'pasillo' => 'nullable|string',
        'descripcion' => 'nullable|string',
        ]);

        Zona::create($request->all());

        return redirect()->route('zonas.index')->with('success', 'Zona creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zona $zona)
    {
        $depositos = Deposito::all();

        return view('zonas.edit', compact('zona', 'depositos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zona $zona)
    {
        $request->validate([
        'nombre' => [
            'required',
            'string',
            'max:255',
            \Illuminate\Validation\Rule::unique('zonas')
                ->where(function ($query) use ($request) {
                    return $query->where('deposito_id', $request->deposito_id);
                })
                ->ignore($zona->id), // Importante: Ignorar la zona actual
        ],
        'deposito_id' => 'required|exists:depositos,id',
        'pasillo' => 'nullable|string',
        'descripcion' => 'nullable|string',
        ]);

        $zona->update($request->all());
        
        return redirect()->route('zonas.index')->with('success', 'Zona actualizada con éxito.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zona $zona)
    {
        $zona->delete();
        return redirect()->route('zonas.index')->with('success', 'Zona eliminada con éxito.');
    }
}

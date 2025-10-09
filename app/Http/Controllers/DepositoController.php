<?php

namespace App\Http\Controllers;

use App\Models\Deposito;
use Illuminate\Http\Request;

class DepositoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $depositos = Deposito::all();
        return view('depositos.index', compact('depositos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // La vista de creación es la misma que el formulario en index, por simplicidad.
        return redirect()->route('depositos.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:depositos,nombre|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Deposito::create($request->all());

        return redirect()->route('depositos.index')->with('success', 'Depósito creado con éxito.');
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
    public function edit(Deposito $deposito)
    {
        return view('depositos.edit', compact('deposito'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposito $deposito)
    {
        $request->validate([
            'nombre' => 'required|string|unique:depositos,nombre,' . $deposito->id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $deposito->update($request->all());

        return redirect()->route('depositos.index')->with('success', 'Depósito actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposito $deposito)
    {
        $deposito->delete();

        return redirect()->route('depositos.index')->with('success', 'Depósito eliminado con éxito.');
    }
}

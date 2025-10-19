<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\Articulo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NotasImport;


class NotaController extends Controller
{
    /**
     * Display a listing of the resource (Monitor de Pedidos).
     */
    public function index(Request $request)
    {
        // Obtener todas las notas, ordenadas por la fecha más reciente, con paginación
        $notas = Nota::with('items')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('notas.index', compact('notas'));
    }

    /**
     * Show the form for creating a new resource (Página de Importación/Carga).
     */
    public function create()
    {
        // En un WMS ligero, esta vista se usa para el formulario de importación o carga manual.
        return view('notas.create');
    }

    /**
     * Store a newly created resource in storage (Guardar la Nota y sus Items).
     */
    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,xlsx,xls,txt,plain',
        ]);
        
        try {
            // Llama al importador para procesar el archivo
            Excel::import(new NotasImport, $request->file('archivo'));

            return redirect()->route('notas.index')->with('success', 'Pedidos importados con éxito.');

        } catch (\Throwable $e) {
            // Si la excepción fue lanzada en la importación, la mostramos.
            return redirect()->back()->withErrors(['general' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Nota $nota)
    {
        // No es necesario en este flujo de trabajo
        abort(404);
    }

    /**
     * Show the form for editing the specified resource (Página de Picking).
     */
    public function edit(Nota $nota)
    {
        // Cargar los items del pedido y los datos del artículo para la vista de picking
        $nota->load('items.articulo'); 
        
        return view('notas.edit', compact('nota'));
    }

    /**
     * Update the specified resource in storage (Confirmar el Picking/Packing).
     */
    public function update(Request $request, Nota $nota)
    {
        // Aquí iría la lógica para procesar la confirmación del picking/packing
        // y actualizar las cantidades_preparadas y las fechas de hito en la cabecera.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nota $nota)
    {
        $nota->delete();
        return redirect()->route('notas.index')->with('success', 'Pedido eliminado correctamente.');
    }
}
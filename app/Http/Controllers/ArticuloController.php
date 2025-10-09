<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articulo;
use Illuminate\Support\Facades\Response;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articulos = Articulo::all();
        return view('articulos.index', compact('articulos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articulos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'codigo' => 'required|string|unique:articulos,codigo|max:255',
            'descripcion' => 'required|string',
            'stock' => 'nullable|integer',
            'stock_minimo' => 'nullable|integer',
            'stock_maximo' => 'nullable|integer',
            'foto' => 'nullable|string', // Por ahora, la foto se guarda como string
            'EAN13' => 'nullable|string|size:13',
            'DUN14' => 'nullable|string|size:14',
            'unidades' => 'nullable|integer',
            'precio' => 'nullable|numeric|between:0,999999.99',
        ]);
        // Crear el artículo
        \App\Models\Articulo::create($request->all());
        // Redirigir al índice de artículos
        return redirect()->route('articulos.index')->with('success', 'Artículo creado con éxito.');
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
    public function edit(Articulo $articulo)
    {
        return view('articulos.edit', compact('articulo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'codigo' => 'required|string|unique:articulos,codigo,' . $articulo->id . '|max:255',
            'descripcion' => 'required|string',
            'stock' => 'nullable|integer',
            'stock_minimo' => 'nullable|integer',
            'stock_maximo' => 'nullable|integer',
            'foto' => 'nullable|string',
            'EAN13' => 'nullable|string|size:13',
            'DUN14' => 'nullable|string|size:14',
            'unidades' => 'nullable|integer',
            'precio' => 'nullable|numeric|between:0,999999.99',
        ]);
        
        // 2. Actualizar el artículo con los datos validados
        $articulo->update($request->all());
        // 3. Redirige
        return redirect()->route('articulos.index')->with('success', 'Artículo actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        $articulo->delete();
        return redirect()->route('articulos.index')->with('success', 'Artículo eliminado con éxito.');
    }

    public function download()
    {
        // Obtener todos los artículos de la base de datos
        $articulos = Articulo::all();
        
        // Preparar las cabeceras del archivo
        $filename = "articulos_" . now()->format('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        // Crear el stream de salida para el archivo
        $callback = function() use ($articulos) {
            $file = fopen('php://output', 'w');
            
            // Definir y escribir los encabezados del CSV
            fputcsv($file, ['Código', 'Descripción', 'Stock Actual', 'Stock Mínimo', 'Stock Máximo', 'EAN13', 'DUN14', 'Unidades', 'Precio']);
            
            // Escribir los datos de cada artículo
            foreach ($articulos as $articulo) {
                fputcsv($file, [
                    $articulo->codigo,
                    $articulo->descripcion,
                    $articulo->stock,
                    $articulo->stock_minimo,
                    $articulo->stock_maximo,
                    $articulo->EAN13,
                    $articulo->DUN14,
                    $articulo->unidades,
                    $articulo->precio
                ]);
            }
            fclose($file);
        };
        
        // Retornar la respuesta con el archivo para descargar
        return Response::stream($callback, 200, $headers);
    }
}

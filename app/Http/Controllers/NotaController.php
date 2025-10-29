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
use Illuminate\Validation\ValidationException;


class NotaController extends Controller
{
    /**
     * Display a listing of the resource (Monitor de Pedidos).
     */
    public function index(Request $request)
    {
        $query = Nota::with('items')->orderBy('created_at', 'desc');

        if ($request->filled('buscar')) {
            $query->where('nota', 'like', '%' . $request->buscar . '%');
        }

        $notas = $query->paginate(2)->withQueryString();

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
        
        $import = new NotasImport;
        
        try {
            Excel::import($import, $request->file('archivo'));

            $errors = $import->getErrors();
            
            if (empty($errors)) {
                // Éxito total
                // CAMBIAR: Usar 'redirect()->back()' o 'redirect()->route('notas.create')'
                return redirect()->back()->with('success', 'Todos los pedidos del archivo se importaron con éxito. ✅');
            } else {
                // Éxito parcial con advertencias
                $warningMessage = 'La importación se completó con advertencias. Revisa los detalles:';

                // CAMBIAR: Usar 'redirect()->back()' o 'redirect()->route('notas.create')'
                return redirect()->back()
                                ->with('warning', $warningMessage)
                                ->with('import_errors', $errors);
            }

        // ... el resto del try/catch (ValidationException, Throwable) es correcto
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // ...
            return redirect()->back()->withErrors(['general' => 'Fallo la validación de algunas filas del archivo.'])->with('validation_failures', $errorMessages)->withInput();
        } catch (\Throwable $e) {
            // ...
            return redirect()->back()->withErrors(['general' => 'Ocurrió un error inesperado al importar el archivo: ' . $e->getMessage()])->withInput();
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
        // Cargar items con los artículos para la vista
        $nota->load('items.articulo'); 

        // Obtener suma pickeada por artículo para esta nota (articulo_id => cantidad_pickeada)
        $pickings = DB::table('pickings')
            ->select('articulo_id', DB::raw('SUM(cantidad) as cantidad_pickeada'))
            ->where('nota_id', $nota->id)
            ->groupBy('articulo_id')
            ->pluck('cantidad_pickeada', 'articulo_id') ?? collect();

        return view('notas.edit', compact('nota', 'pickings'));
    }

    /**
     * Update the specified resource in storage (Confirmar el Picking/Packing).
     */
    public function update(Request $request, Nota $nota)
    {
        $action = $request->input('action'); // puede ser edit, picking, packing, despacho

        switch ($action) {
            case 'edit':
                // Validar todos los campos que vienen del formulario
                $validated = $request->validate([
                    'nota' => 'nullable|string|max:255',
                    'orden_compra' => 'nullable|string|max:255',
                    'transporte' => 'nullable|string|max:255',
                    'domicilio_transporte' => 'nullable|string|max:255',
                    'fecha_fill_rate' => 'nullable|date',
                    'cliente' => 'nullable|string|max:255',
                    'domicilio' => 'nullable|string|max:255',
                    'estado' => 'nullable|string|max:100',
                ]);
                // Actualizar la Nota con todos los valores
                $nota->update($validated);
                return redirect()->back()->with('success', 'Cabecera de nota actualizada correctamente.');

            case 'picking':
                foreach ($request->input('items', []) as $itemId => $cant) {
                    NotaItem::where('id', $itemId)->update([
                        'cantidad_preparada' => $cant,
                    ]);
                }
                $nota->update([
                    'estado' => 'Picking',
                    'fecha_fill_rate' => now(),
                ]);
                return redirect()->back()->with('success', 'Picking confirmado.');

            case 'packing':
                $nota->update([
                    'estado' => 'Preparado',
                    'fecha_facturado' => now(),
                ]);
                return redirect()->back()->with('success', 'Preparación registrada.');

            case 'despacho':
                $nota->update([
                    'estado' => 'Despachado',
                    'fecha_despachado' => now(),
                ]);
                return redirect()->back()->with('success', 'Pedido despachado.');

            case 'items':
                $errores = [];

                // 🔹 1. Actualizar items existentes y validar decremento
                if ($request->has('items')) {
                    foreach ($request->items as $id => $data) {
                        $item = NotaItem::find($id);
                        $nuevaCantidadSolicitada = (int)($data['cantidad'] ?? 0);

                        if ($item) {
                            $cantidadPreparada = $item->cantidad_preparada ?? 0;

                            // ⭐ VALIDACIÓN CLAVE: No permitir decremento por debajo de lo preparado
                            if ($nuevaCantidadSolicitada < $cantidadPreparada) {
                                $errores[] = "No se puede reducir la cantidad solicitada de {$item->articulo->codigo} a {$nuevaCantidadSolicitada}. Ya hay {$cantidadPreparada} unidades preparadas.";
                                continue; // Saltar la actualización de este ítem
                            }
                            
                            // Si pasa la validación, actualizar
                            $item->cantidad_solicitada = $nuevaCantidadSolicitada;
                            $item->save();
                        }
                    }
                }

                // 🔹 2. Crear nuevos items (desde las filas agregadas dinámicamente)
                if ($request->has('new_items')) {
                    foreach ($request->new_items as $data) {
                        // Buscar el artículo por su código
                        $articulo = Articulo::where('codigo', $data['codigo'])->first();

                        if (!$articulo) {
                            $errores[] = "El artículo con código {$data['codigo']} no existe en el maestro de artículos.";
                            continue; // No lo creamos
                        }

                        NotaItem::create([
                            'nota_id' => $nota->id,
                            'articulo_id' => $articulo->id,
                            'cantidad_solicitada' => $data['cantidad'] ?? 1,
                            'cantidad_preparada' => 0,
                        ]);
                    }
                }

                // 🔹 3. Eliminar ítems marcados
                $deleteErrors = [];
                if ($request->has('delete_items')) {
                    $itemsToDelete = NotaItem::whereIn('id', $request->delete_items)
                        ->where('nota_id', $nota->id)
                        ->get();
                    
                    foreach ($itemsToDelete as $item) {
                        // 1. Verificar si tiene cantidad preparada > 0 en la tabla NotaItem
                        $preparado = $item->cantidad_preparada ?? 0;
                        // 2. Opcional: Verificar si existen registros en Packing (doble seguridad)
                        $packingExists = \App\Models\Packing::where('nota_id', $nota->id)
                            ->where('articulo_id', $item->articulo_id)
                            ->exists();
                           
                        if ($preparado > 0 || $packingExists) {
                            $deleteErrors[] = "El artículo {$item->articulo->codigo} no puede eliminarse porque ya tiene registros de preparación/packing.";
                            continue; // Saltar la eliminación y continuar con los errores
                        }
                        // Si pasa la validación, eliminar
                        $item->delete();
                    }
                }

                // 🔹 4. Mostrar resultado si hubo errores (combinando errores de nuevo item y errores de borrado)
                $errores = array_merge($errores, $deleteErrors);
                
                if (!empty($errores)) {
                    // Si hay errores, devolver la advertencia con la lista de problemas
                    return redirect()->back()
                        ->with('warning', 'Se actualizaron los datos, pero algunos problemas impidieron la operación de borrado o agregado.')
                        ->with('import_errors', $errores);
                }

                return redirect()->back()->with('success', 'Detalle de artículos actualizado correctamente.');

        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nota $nota)
    {
        $nota->delete();
        return redirect()->route('notas.index')->with('success', 'Pedido eliminado correctamente.');
    }


    /**
     * Descarga a Excel (CSV) el detalle de artículos preparados de una Nota.
     */
    public function descargarPreparado(Nota $nota)
    {
        // 1. Usar la clase de exportación
        $export = new \App\Exports\NotaPreparadaExport($nota->id);
        
        // 2. Definir el nombre del archivo
        $fileName = 'packing_nota_' . $nota->nota . '_' . now()->format('Ymd_His') . '.csv';

        // 3. Descargar el archivo
        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
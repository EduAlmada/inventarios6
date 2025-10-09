<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Stock; 
use App\Models\Deposito;
use App\Models\Zona;
use App\Models\Articulo;
use App\Models\Actividad;
use App\Models\TipoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        // Obtiene la instancia del usuario autenticado
        $user = auth()->user();

        // Inicia la consulta
        $query = Actividad::query();

        // Si el usuario NO es un administrador, filtra por su ID.
        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }
        
        // Aplica los filtros de fecha y el paginado
        $actividades = $query
            ->when($request->has('desde'), function ($query) use ($request) {
            $query->where('fecha_actividad', '>=', \Carbon\Carbon::parse($request->input('desde'))->startOfDay());
            })
            ->when($request->has('hasta'), function ($query) use ($request) {
            $query->where('fecha_actividad', '<=', \Carbon\Carbon::parse($request->input('hasta'))->endOfDay());
            })
            ->orderBy('fecha_actividad', 'desc')
            ->paginate(5)->withQueryString();

        $tipos = TipoActividad::all();
        $articulos = Articulo::all();
        $depositos = Deposito::all();
        $tipos_map = $tipos->pluck('modifica_stock', 'nombre')->toArray();

        return view('actividades.index', compact('actividades', 'tipos','articulos','depositos','tipos_map'));

    }

// En app/Http/Controllers/ActividadController.php

public function store(Request $request)
{
    // 1. Obtener tipo de actividad y definir si modifica stock
    $tipo_actividad = \App\Models\TipoActividad::where('nombre', $request->input('tipo'))->first();
    $modificaStock = $tipo_actividad ? $tipo_actividad->modifica_stock : false;

    // 2. Definición de Reglas Condicionales
    $rules = [
        'tipo' => 'required|string',
        'operador' => 'required|string',
        'bultos' => 'nullable|integer',
        'pedido' => 'nullable|string',
        'fecha_actividad' => 'nullable|date',
        // Zona es REQUERIDA solo si modifica stock, sino es opcional.
        'zona_id' => $modificaStock ? 'required|exists:zonas,id' : 'nullable', 
    ];
    $request->validate($rules);

    // 3. Deserializar los ítems de Livewire (solo necesario si modifica stock)
    $livewireItems = json_decode($request->input('livewireItems', '[]'), true);
    
    // 4. INICIO DE LA TRANSACCIÓN
    \DB::beginTransaction();

    try {
        // CORRECCIÓN CLAVE 1: Limpiar zona_id si no es requerido
        $dataToCreate = $request->only(
            'tipo', 'operador', 'bultos', 'pedido', 'zona_id', 'fecha_actividad'
        );
        
        // Si fecha_actividad no está presente, usa la hora actual.
        if (empty($dataToCreate['fecha_actividad'])) {
            $dataToCreate['fecha_actividad'] = \Carbon\Carbon::now();
        }

        // Si NO modifica stock, aseguramos que zona_id sea NULL
        if (!$modificaStock) {
            $dataToCreate['zona_id'] = null;
        }

        // A. REGISTRAR LA ACTIVIDAD PRINCIPAL SIEMPRE
        $actividad = auth()->user()->actividads()->create($dataToCreate);

        // B. Lógica de Stock: Solo si modifica stock Y hay ítems válidos
        if ($modificaStock) {
            
            // Verificación de la grilla
            if (empty($livewireItems) || !is_array($livewireItems)) {
                 throw new \Exception("Debes agregar al menos un artículo para esta actividad.");
            }
            
            $zona = \App\Models\Zona::find($request->input('zona_id'));
            
            foreach ($livewireItems as $item) {
                
                // ... (toda la lógica de stock y movimiento aquí) ...
                
                $articulo = \App\Models\Articulo::find($item['articulo_id']);
                
                if (!$articulo || !$zona) {
                    throw new \Exception("Artículo o Zona inválida para el movimiento.");
                }

                $cantidad_ingresada = (int) $item['cantidad'];
                $tipo_act = \App\Models\TipoActividad::where('nombre', $actividad->tipo)->first(); 
                $cantidad_a_mover = $cantidad_ingresada * $tipo_act->signo_stock;
                $tipo_movimiento = $tipo_act->signo_stock === 1 ? 'ingreso' : 'salida';

                $stock = \App\Models\Stock::firstOrCreate(
                    ['articulo_id' => $articulo->id, 'zona_id' => $zona->id],
                    ['cantidad' => 0]
                );
                $stock->cantidad += $cantidad_a_mover;
                $stock->save();

                \App\Models\Movimiento::create([
                    'articulo_id' => $articulo->id,
                    'user_id' => auth()->id(),
                    'zona_id' => $zona->id,
                    'tipo' => $tipo_movimiento,
                    'cantidad' => abs($cantidad_a_mover),
                    'orden' => $request->input('pedido'),
                ]);
            }
        }
        
        \DB::commit(); 
        return redirect()->back()->with('success', 'Actividad registrada con éxito.');

    } catch (\Throwable $e) {
        \DB::rollBack();
        return redirect()->back()->withErrors(['general' => 'Ocurrió un error: ' . $e->getMessage()])->withInput();
    }
}
    // Metodo show
    public function show(string $id)
    {
        // No hace nada, pero evita el error de "método no encontrado"
    }

        // Método para mostrar el formulario de edición de una actividad
    public function edit(Actividad $actividad)
    {
        $tipos = TipoActividad::all();
        return view('actividades.edit', compact('actividad', 'tipos'));
    }

    // Método para actualizar un registro de actividad
    public function update(Request $request, Actividad $actividad)
    {
        $request->validate([
            'tipo' => 'required|string',
            'operador' => 'required|string',
            'bultos' => 'nullable|integer',
            'pedido' => 'nullable|string',
            'bultos' => 'nullable|integer',
            'fecha_actividad' => 'nullable|date',
        ]);

        $actividad->update($request->all());

        return redirect()->route('actividades.index')->with('success', 'Actividad actualizada con éxito.');
    }

    // Método para eliminar un registro de actividad
    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('actividades.index')->with('success', 'Actividad eliminada con éxito.');
    }

    public function download(Request $request)
    {
        // Si no hay filtros, no descargar
        if (!$request->has('desde') && !$request->has('hasta')) {
            return redirect()->back()->withErrors(['download' => 'Debes especificar un rango de fechas para descargar.']);
        }
        
        // Obtiene la instancia del usuario autenticado
        $user = auth()->user();

        // Inicia la consulta
        $query = Actividad::query();

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }
        
        // Aplica los filtros de fecha si existen
        $actividades = $query
            ->when($request->has('desde'), function ($query) use ($request) {
                $query->whereDate('fecha_actividad', '>=', $request->input('desde'));
            })
            ->when($request->has('hasta'), function ($query) use ($request) {
                $query->whereDate('fecha_actividad', '<=', $request->input('hasta'));
            })
            ->get();

        // 1. Define el nombre del archivo con una extensión .csv
        $filename = "actividades_" . now()->format('Y-m-d_H-i-s') . ".csv";

        // 2. Define las cabeceras para forzar la descarga
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        // 3. Define la función que escribirá los datos en el archivo
        $callback = function() use ($actividades) {
            $file = fopen('php://output', 'w');
            
            // Define el encabezado del CSV
            fputcsv($file, ['Tipo', 'Operador', 'Pedido', 'Bultos', 'Fecha']);
            
            // Escribe los datos de cada actividad
            foreach ($actividades as $actividad) {
                fputcsv($file, [
                    $actividad->tipo,
                    $actividad->operador,
                    $actividad->pedido,
                    $actividad->bultos,
                    $actividad->fecha_actividad ? \Carbon\Carbon::parse($actividad->fecha_actividad)->format('d/m/Y H:i') : $actividad->created_at->format('d/m/Y H:i')
                ]);
            }
            fclose($file);
        };

        // 4. Retorna la respuesta con el stream y las cabeceras
        return Response::stream($callback, 200, $headers);
    }
}
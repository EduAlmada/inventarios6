<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Articulo;
use App\Models\Deposito;
use App\Models\Zona;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        // Inicia la consulta del Stock
        $query = Stock::query()
                       ->with(['articulo', 'zona.deposito']); // Carga las relaciones necesarias

        // --- Aplicación de Filtros ---

        // 1. Filtro por Código de Artículo
        if ($request->filled('codigo')) {
            $codigo = $request->input('codigo');
            $query->whereHas('articulo', function ($q) use ($codigo) {
                $q->where('codigo', 'like', '%' . $codigo . '%');
            });
        }
        
        // 2. Filtro por Zona
        if ($request->filled('zona_id')) {
            $query->where('zona_id', $request->input('zona_id'));
        }
        
        // 3. Filtro por Depósito
        if ($request->filled('deposito_id')) {
            $depositoId = $request->input('deposito_id');
            $query->whereHas('zona.deposito', function ($q) use ($depositoId) {
                $q->where('depositos.id', $depositoId);
            });
        }

        // Obtiene los datos paginados
        $stocks = $query->paginate(5)->withQueryString();

        // Obtiene datos para llenar los Select de filtro
        $depositos = Deposito::all();
        $zonas = Zona::all();

        return view('stock.index', compact('stocks', 'depositos', 'zonas'));
    }
}
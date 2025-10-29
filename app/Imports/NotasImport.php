<?php

namespace App\Imports;

use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\Articulo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception; // Asegúrate de importar Exception

class NotasImport implements ToCollection, WithHeadingRow
{
    // Propiedad para almacenar los errores
    protected $errors = [];

    // Método para acceder a los errores desde el controlador (si lo necesitas)
    public function getErrors()
    {
        return $this->errors;
    }

    public function collection(Collection $rows)
    {
        $rows->groupBy('nota')->each(function (Collection $pedidoRows) {
            
            $firstRow = $pedidoRows->first();
            $numeroNota = $firstRow['nota']; // Obtener el número de nota

            // 1. **Validación de Nota Duplicada (Nuevo)**
            if (Nota::where('nota', $numeroNota)->exists()) {
                // Si la nota ya existe, la registramos y pasamos al siguiente pedido (continue)
                $this->errors[] = "Nota {$numeroNota} no se importó porque ya existe en el sistema.";
                return; // Pasa a la siguiente nota del `each`
            }

            // 2. Usar DB::transaction para asegurar que si falla por artículos, todo se revierta
            try {
                DB::transaction(function () use ($pedidoRows, $firstRow, $numeroNota) {
                    
                    $usuarioId = auth()->id();
                    
                    // 2.1 Definir el formato D/M/Y y Validar
                    $fechaFillRate = Carbon::createFromFormat('d/m/Y', $firstRow['fecha_fill_rate'])->format('Y-m-d');
                    
                    // 2.2 Validación de Artículos
                    $articuloCodes = $pedidoRows->pluck('articulo_id')->unique()->toArray();
                    $existingArticulos = Articulo::whereIn('codigo', $articuloCodes)->pluck('id', 'codigo');
    
                    if ($existingArticulos->count() < count($articuloCodes)) {
                        $missingCodes = array_diff($articuloCodes, $existingArticulos->keys()->toArray());
                        // Lanzamos una excepción para que el `catch` la capture y revierta la transacción
                        throw new Exception('Los siguientes códigos de artículo no existen: ' . implode(', ', $missingCodes));
                    }
    
                    // 2.3 Crear la Cabecera (Tabla notas)
                    $nota = Nota::create([
                        'nota' => $numeroNota,
                        'orden_compra' => $firstRow['orden_compra'],
                        'cliente' => $firstRow['cliente'],
                        'domicilio' => $firstRow['domicilio'],
                        'transporte' => $firstRow['transporte'],
                        'domicilio_transporte' => $firstRow['domicilio_transporte'],
                        'fecha_fill_rate' => $fechaFillRate,
                        'user_id' => $usuarioId,
                        'estado' => 'Pendiente',
                    ]);
    
                    // 2.4 Crear los Items de la Nota (Tabla nota_items)
                    foreach ($pedidoRows as $row) {
                        $articuloCodigo = $row['articulo_id'];
                        $articuloId = $existingArticulos[$articuloCodigo];
    
                        NotaItem::create([
                            'nota_id' => $nota->id,
                            'articulo_id' => $articuloId,
                            'cantidad_solicitada' => $row['cantidad_solicitada'],
                            'cantidad_preparada' => 0,
                        ]);
                    }
                });

            } catch (Exception $e) {
                // Si falla por artículos, la excepción se captura aquí, se revierte la transacción
                // y se registra el error.
                $this->errors[] = "Nota {$numeroNota}: falló la importación por validación de datos. Mensaje: " . $e->getMessage();
            }
        });
    }

    public function headingRow(): int
    {
        return 1;
    }
}
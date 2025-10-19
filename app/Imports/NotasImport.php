<?php

namespace App\Imports;

use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\Articulo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class NotasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows->groupBy('nota')->each(function (Collection $pedidoRows) {
            
            DB::transaction(function () use ($pedidoRows) {
                
                $firstRow = $pedidoRows->first();
                $usuarioId = auth()->id();
                
                // 1. Definir el formato D/M/Y
                $fechaFillRate = Carbon::createFromFormat('d/m/Y', $firstRow['fecha_fill_rate'])->format('Y-m-d');
                
                // 2. Validación: (El código de validación de artículos va aquí, como lo tenías)
                $articuloCodes = $pedidoRows->pluck('articulo_id')->unique()->toArray();
                $existingArticulos = Articulo::whereIn('codigo', $articuloCodes)->pluck('id', 'codigo');

                if ($existingArticulos->count() < count($articuloCodes)) {
                    $missingCodes = array_diff($articuloCodes, $existingArticulos->keys()->toArray());
                    throw new \Exception('Fallo la importación: Los siguientes códigos de artículo no existen: ' . implode(', ', $missingCodes));
                }

                // 3. Crear la Cabecera (Tabla notas)
                $nota = Nota::create([
                    'nota' => $firstRow['nota'],
                    'orden_compra' => $firstRow['orden_compra'],
                    'cliente' => $firstRow['cliente'],
                    'domicilio' => $firstRow['domicilio'],
                    'transporte' => $firstRow['transporte'],
                    'domicilio_transporte' => $firstRow['domicilio_transporte'],
                    'fecha_fill_rate' => $fechaFillRate, // Usar la fecha ya formateada
                    'user_id' => $usuarioId,
                    'estado' => 'Pendiente',
                ]);

                // 4. Crear los Items de la Nota (Tabla nota_items)
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
        });
    }

    // Opcional: Esto te permite mapear los encabezados de tu CSV directamente
    // Tu CSV tiene: nota, orden_compra, cliente, domicilio, transporte, domicilio_transporte, fecha_fill_rate, articulo_id, cantidad_solicitada
    public function headingRow(): int
    {
        return 1;
    }
}
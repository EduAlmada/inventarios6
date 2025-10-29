<?php

namespace App\Exports;

use App\Models\Nota;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NotaPreparadaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $notaId;

    public function __construct(int $notaId)
    {
        $this->notaId = $notaId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Cargamos los ítems de la nota específica, incluyendo el artículo relacionado.
        return Nota::findOrFail($this->notaId)
            ->items()
            ->with('articulo')
            ->get();
    }
    
    /**
     * Define los encabezados de las columnas del archivo.
     * @return array
     */
    public function headings(): array
    {
        return [
            'NUMERO_NOTA',
            'CODIGO',
            'DESCRIPCION',
            'SOLICITADO',
            'PREPARADO',
        ];
    }
    
    /**
     * Mapea cada fila del Collection a los datos de la exportación.
     * @param mixed $item
     * @return array
     */
    public function map($item): array
    {
        // $item es un objeto NotaItem
        return [
            $item->nota->nota, // Acceso a la cabecera Nota
            $item->articulo->codigo ?? 'N/D',
            $item->articulo->descripcion ?? 'Artículo Eliminado',
            $item->cantidad_solicitada,
            $item->cantidad_preparada ?? 0,
        ];
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\Packing;

class PackingNota extends Component
{
    public $nota;
    public $items = [];
    public $pickeados = [];
    public $packeados = [];
    public $porcentajeGlobal = 0;

    public $codigo;
    public $cantidad;
    public $caja = 1;
    public $pallet = 1;

    // --- NUEVAS PROPIEDADES PARA EL DETALLE ---
    public $mostrarDetalleModal = false;
    public $articuloIdDetalle = null;
    public $articuloCodigoDetalle = '';
    public $packingDetalle = []; // Almacenará la lista de transacciones individuales (Packing::class)

    // --- NUEVAS PROPIEDADES PARA EL CONTROL DE CAJAS ---
    public $maxCaja = 0;
    public $cajaGaps = '(Sin cajas)'; // Almacenará el resumen del control de cajas
    public $mostrarCajaDetalleModal = false;
    public $packingCajaDetalle = []; // Para el nuevo modal

    public function mount(Nota $nota)
    {
        $this->nota = $nota;
        $this->cargarDatos(); // ✅ Usamos el mismo método para garantizar coherencia
    }

    public function cargarDatos()
    {
        // Traer items con su artículo asociado
        $this->items = $this->nota->items()
            ->with('articulo')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'articulo_id' => $item->articulo_id,
                    'codigo' => $item->articulo->codigo ?? '(sin código)',
                    'descripcion' => $item->articulo->descripcion ?? '(sin descripción)',
                    'cantidad_solicitada' => $item->cantidad_solicitada,
                ];
            })
            ->toArray();

        // === PICKING ===
        // Traemos los pickeados reales desde el modelo Picking
        $this->pickeados = \App\Models\Picking::where('nota_id', $this->nota->id)
            ->selectRaw('articulo_id, SUM(cantidad) as total')
            ->groupBy('articulo_id')
            ->pluck('total', 'articulo_id')
            ->toArray();

        // === PACKING ===
        $this->packeados = Packing::where('nota_id', $this->nota->id)
            ->selectRaw('articulo_id, SUM(cantidad) as total')
            ->groupBy('articulo_id')
            ->pluck('total', 'articulo_id')
            ->toArray();

        // === AVANCE GLOBAL ===
        $totalSolicitado = 0;
        $totalPackeado = 0;

        foreach ($this->items as $item) {
            $solicitado = $item['cantidad_solicitada'] ?? 0;
            $packeado = $this->packeados[$item['articulo_id']] ?? 0;

            $totalSolicitado += $solicitado;
            $totalPackeado += $packeado;
        }

        $this->porcentajeGlobal = $totalSolicitado > 0
            ? round(($totalPackeado / $totalSolicitado) * 100, 1)
            : 0;
        
        // === LÓGICA PARA CONTROL DE CAJAS (MÁXIMO Y GAPS) ===
        $packingCajas = \App\Models\Packing::where('nota_id', $this->nota->id)
            ->distinct('caja')
            ->pluck('caja')
            ->map(fn($c) => (int)$c) // Aseguramos que sean enteros
            ->sort()
            ->values()
            ->toArray();

        $this->maxCaja = end($packingCajas) ?: 0;
        
        // Calcular Gaps
        if ($this->maxCaja > 0) {
            $expectedBoxes = range(1, $this->maxCaja);
            $missingBoxes = array_diff($expectedBoxes, $packingCajas);

            if (empty($missingBoxes)) {
                $this->cajaGaps = "($this->maxCaja cajas)"; // Ejemplo: (16 cajas)
            } else {
                // Mostrar los números que faltan o solo una advertencia
                $missingCount = count($missingBoxes);
                if ($missingCount <= 5) {
                    // Si faltan pocos, listar los números
                    $this->cajaGaps = "(Falta caja número: " . implode(', ', $missingBoxes) . ")";
                } else {
                    // Si faltan muchos, solo advertir la cantidad
                    $this->cajaGaps = "(Faltan $missingCount cajas)";
                }
            }
        } else {
            $this->cajaGaps = '(Sin cajas)';
        }
    }


    public function calcularPorcentajeGlobal()
    {
        $totalSolicitado = 0;
        $totalPackeado = 0;

        foreach ($this->items as $item) {
            $solicitado = $item['cantidad_solicitada'] ?? 0;
            $packeado = $this->packeados[$item['articulo_id']] ?? 0;

            $totalSolicitado += $solicitado;
            $totalPackeado += $packeado;
        }

        $this->porcentajeGlobal = $totalSolicitado > 0
            ? round(($totalPackeado / $totalSolicitado) * 100, 1)
            : 0;
    }


    public function abrirDetalleCajas()
    {
        // Cargar TODAS las transacciones de Packing para la nota, ordenadas por caja y pallet
        $this->packingCajaDetalle = \App\Models\Packing::where('nota_id', $this->nota->id)
            ->orderBy('caja', 'asc')
            ->orderBy('pallet', 'asc')
            ->orderBy('articulo_id', 'asc')
            ->get()
            ->toArray();

        $this->mostrarCajaDetalleModal = true;
    }

    public function cerrarDetalleCajas()
    {
        $this->mostrarCajaDetalleModal = false;
    }

    public function agregarPacking()
    {
        if (!$this->codigo || !$this->cantidad) return;

        $item = NotaItem::where('nota_id', $this->nota->id)
            ->whereHas('articulo', fn($q) => $q->where('codigo', $this->codigo))
            ->first();

        if (!$item) {
            session()->flash('error', 'Artículo no encontrado en la nota.');
            return;
        }

        // 🔍 Traer datos actuales de embalado
        $packeadoActual = Packing::where('nota_id', $this->nota->id)
            ->where('articulo_id', $item->articulo_id)
            ->sum('cantidad');

        $cantidadSolicitada = $item->cantidad_solicitada ?? $item->cantidad ?? 0;
        $totalPropuesto = $packeadoActual + $this->cantidad;

        // 🚫 Validar que no supere lo solicitado
        if ($totalPropuesto > $cantidadSolicitada) {
            session()->flash('error', "La cantidad supera lo solicitado ({$cantidadSolicitada}).");
            $this->dispatch('focusCantidad');
            return;
        }

        // ✅ Registrar packing
        Packing::create([
            'nota_id' => $this->nota->id,
            'user_id' => auth()->id(),
            'articulo_id' => $item->articulo_id,
            'caja' => $this->caja,
            'pallet' => $this->pallet,
            'cantidad' => $this->cantidad,
            'fecha_inicio' => now(),
            'fecha_fin' => now(),
        ]);

        // 🌟 Actualizamos la tabla nota_Items
        $this->actualizarNotaItemCantidadPreparada($item->articulo_id); 

        // 🔄 Refrescamos datos
        $this->cargarDatos();

        // 🧮 Actualizar porcentaje
        $this->calcularPorcentajeGlobal();

        // 🔁 Reset de campos y foco
        $this->codigo = '';
        $this->cantidad = '';
        $this->dispatch('focusCodigo');
    }


    public function guardarCambios()
    {
        foreach ($this->packeados as $articuloId => $cantidad) {
            $cantidad = (int) $cantidad;
            $item = collect($this->items)->firstWhere('articulo_id', $articuloId);
            $solicitado = $item['cantidad_solicitada'] ?? 0;

            if ($cantidad > $solicitado) {
                session()->flash('error', "Cantidad embalada de {$item['codigo']} supera lo solicitado ({$solicitado}).");
                $this->dispatch('focusCodigo');
                return;
            }

            Packing::updateOrCreate(
                ['nota_id' => $this->nota->id, 'articulo_id' => $articuloId],
                [
                    'user_id' => auth()->id(),
                    'cantidad' => $cantidad,
                    'fecha_fin' => now(),
                ]
            );
        }

        $this->calcularPorcentajeGlobal();
        session()->flash('success', 'Cambios guardados correctamente.');
    }


    public function eliminarPacking($articuloId)
    {
        Packing::where('nota_id', $this->nota->id)
            ->where('articulo_id', $articuloId)
            ->delete();

        unset($this->packeados[$articuloId]);
    
            // 🌟 Actualizamos la tabla nota_Items
        $this->actualizarNotaItemCantidadPreparada($articuloId);

        $this->calcularPorcentajeGlobal();
        session()->flash('success', 'Packing eliminado.');
        $this->dispatch('focusCodigo');
    }


    // --- NUEVO MÉTODO PARA ABRIR EL DETALLE ---
    public function abrirDetalle($articuloId)
    {
        $this->articuloIdDetalle = $articuloId;
        
        // Obtener el código para el título del modal
        $item = collect($this->items)->firstWhere('articulo_id', $articuloId);
        $this->articuloCodigoDetalle = $item['codigo'] ?? 'Artículo Desconocido';

        // Cargar las transacciones individuales de Packing para este artículo
        $this->packingDetalle = Packing::where('nota_id', $this->nota->id)
            ->where('articulo_id', $articuloId)
            ->orderBy('created_at', 'asc') // Ordenar por fecha de registro
            ->get()
            ->toArray(); // Usar toArray() para evitar problemas de reactividad con Livewire 3

        $this->mostrarDetalleModal = true;
    }

    // --- NUEVO MÉTODO PARA ACTUALIZAR UNA TRANSACCIÓN INDIVIDUAL ---
    public function actualizarTransaccionPacking($packingId, $campo, $valor)
    {
        // 1. Encontrar la transacción
        $transaccion = Packing::find($packingId);

        if (!$transaccion) {
            session()->flash('error', 'Transacción de packing no encontrada.');
            return;
        }

        // 2. Aplicar el cambio
        $transaccion->$campo = max(0, (int)$valor); // Asegurar que el valor es >= 0

        // 3. Validación de Cantidad (la más importante)
        if ($campo === 'cantidad') {
            // Calcular el total actual sin esta transacción
            $totalOtros = Packing::where('nota_id', $this->nota->id)
                ->where('articulo_id', $transaccion->articulo_id)
                ->where('id', '!=', $packingId)
                ->sum('cantidad');
            
            $cantidadSolicitada = collect($this->items)
                ->firstWhere('articulo_id', $transaccion->articulo_id)['cantidad_solicitada'] ?? 0;
            
            $totalPropuesto = $totalOtros + $transaccion->cantidad;

            if ($totalPropuesto > $cantidadSolicitada) {
                session()->flash('error', "La cantidad total ({$totalPropuesto}) supera lo solicitado ({$cantidadSolicitada}). Revise la corrección.");
                // No guardamos el cambio y restauramos la vista
                $this->abrirDetalle($transaccion->articulo_id);
                $this->cargarDatos();
                return;
            }
        }
        
        // 4. Guardar y refrescar
        $transaccion->save();
        $transaccion->fecha_fin = now(); // Marcar la fecha de última edición
        
        // 🌟 Actualizamos la tabla nota_Items
        $this->actualizarNotaItemCantidadPreparada($transaccion->articulo_id);

        session()->flash('success', 'Transacción actualizada.');

        // Refrescar los datos del modal y del componente principal
        $this->abrirDetalle($transaccion->articulo_id); 
        $this->cargarDatos(); // Recalcula la suma total en el componente principal
    }

    // --- NUEVO MÉTODO PARA ELIMINAR UNA TRANSACCIÓN INDIVIDUAL ---
    public function eliminarTransaccionPacking($packingId)
    {
        $transaccion = Packing::find($packingId);
        
        if (!$transaccion) {
            return;
        }
        
        $articuloId = $transaccion->articulo_id;
        $transaccion->delete();
        
        // 🌟 Actualizamos la tabla nota_Items
        $this->actualizarNotaItemCantidadPreparada($articuloId);

        session()->flash('success', 'Transacción individual eliminada.');

        // Refrescar los datos del modal y del componente principal
        $this->abrirDetalle($articuloId);
        $this->cargarDatos(); // Recalcula la suma total en el componente principal
        
        // Si no quedan transacciones para este artículo, cerramos el modal
        if (count($this->packingDetalle) == 0) {
            $this->mostrarDetalleModal = false;
        }
    }

    /**
     * Consolida la cantidad total embalada de un artículo y actualiza NotaItem.
     * @param int $articuloId
     */
    private function actualizarNotaItemCantidadPreparada(int $articuloId)
    {
        // 1. Calcular la cantidad total embalada (SUM) para este artículo en esta nota
        $totalPackeado = \App\Models\Packing::where('nota_id', $this->nota->id)
            ->where('articulo_id', $articuloId)
            ->sum('cantidad');

        // 2. Encontrar el ítem de la nota correspondiente
        $notaItem = \App\Models\NotaItem::where('nota_id', $this->nota->id)
            ->where('articulo_id', $articuloId)
            ->first();

        // 3. Actualizar el campo cantidad_preparada
        if ($notaItem) {
            $notaItem->cantidad_preparada = $totalPackeado;
            $notaItem->save();
        }
    }


    public function render()
    {
        return view('livewire.packing-nota');
    }
}

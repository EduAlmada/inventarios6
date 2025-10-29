<div>
    {{-- Formulario superior --}}
    <form wire:submit.prevent="agregarPacking"
        class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm flex flex-wrap items-end gap-4"
        x-data="{}" {{-- Inicializa un scope Alpine.js --}}
        x-init="$nextTick(() => $refs.codigo.focus())" {{-- Foco inicial garantizado por Alpine --}}
        >
        {{-- Código --}}
        <div class="flex flex-col">
            <x-input-label for="codigo" value="Código de Artículo" />
            <x-text-input id="codigo" wire:model.defer="codigo"
                x-ref="codigo" {{-- Referencia Alpine: $refs.codigo --}}
                x-on:keydown.enter.prevent="$refs.cantidad.focus()" {{-- Enter en Código → Foco a Cantidad --}}
                class="w-40 text-center font-semibold"
                placeholder="Escanear o escribir" autofocus autocomplete="off" />
        </div>
        {{-- Cantidad --}}
        <div class="flex flex-col">
            <x-input-label for="cantidad" value="Cantidad" />
            <x-text-input id="cantidad" type="number" min="1"
                wire:model.defer="cantidad"
                x-ref="cantidad" {{-- Referencia Alpine: $refs.cantidad --}}
                x-on:keydown.enter.prevent="$wire.agregarPacking().then(() => $refs.codigo.focus())" {{-- Enter en Cantidad → Llama a Livewire y luego vuelve a Código --}}
                class="w-24 text-center font-semibold" />
        </div>
        {{-- Caja --}}
        <div class="flex flex-col">
            <x-input-label for="caja" value="Caja" />
            <x-text-input id="caja" type="number" min="1"
                wire:model.defer="caja"
                x-on:keydown.enter.prevent="$refs.codigo.focus()" {{-- Enter → Foco a Código --}}
                class="w-20 text-center" />
        </div>
        {{-- Pallet --}}
        <div class="flex flex-col">
            <x-input-label for="pallet" value="Pallet" />
            <x-text-input id="pallet" type="number" min="1"
                wire:model.defer="pallet"
                x-on:keydown.enter.prevent="$refs.codigo.focus()" {{-- Enter → Foco a Código --}}
                class="w-20 text-center" />
        </div>
        {{-- Botón --}}
        <div class="flex items-end">
            <x-boton color="green" class="px-4 py-2">
                Agregar Packing
            </x-boton>
        </div>
        {{-- Mensajes --}}
        <div class="w-full mt-3">
            @if (session('error'))
                <div class="p-2 bg-red-100 text-red-700 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="p-2 bg-green-100 text-green-700 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </form>

    {{-- Resumen --}}
    <div class="mb-6 p-4 bg-gray-50 rounded flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold mb-1">Avance del Packing</h3>
            
            <p class="text-gray-700 mb-1">
                Completado:
                <span class="font-bold {{ $porcentajeGlobal == 100 ? 'text-green-600' : 'text-blue-600' }}">
                    {{ $porcentajeGlobal }}%
                </span>
            </p>

            {{-- NUEVO: Información de la última caja y el control de gaps --}}
            <p class="text-gray-700 flex items-center gap-2">
                Cajas Embaladas: 
                <span class="font-bold text-gray-800">
                    {{ $maxCaja }} 
                </span>
                <span class="text-sm text-gray-600">
                    {{ $cajaGaps }}
                </span>
                
                {{-- Botón para abrir el detalle de cajas (el ojo de visualizar) --}}
                @if ($maxCaja > 0)
                    <button type="button" 
                        wire:click="abrirDetalleCajas" 
                        class="text-blue-600 hover:text-blue-800 text-xl p-1 rounded transition duration-150 ease-in-out"
                        title="Ver listado completo de Packing por Caja">
                        👁️
                    </button>
                @endif
            </p>
        </div>
        <div class="w-1/2 bg-gray-200 h-4 rounded-full overflow-hidden">
            <div class="h-4 bg-green-500" style="width: {{ $porcentajeGlobal }}%"></div>
        </div>
    </div>

    {{-- Tabla --}}
    <table class="w-full text-sm text-gray-700">
        <thead class="bg-gray-100">
            <tr>
                <th>Código</th>
                <th>Artículo</th>
                <th class="text-right">Solicitado</th>
                <th class="text-right">Pickeado</th>
                <th class="text-right">Embalado</th>
                <th class="text-right">Eliminar</th>
                <th class="text-right">Avance</th>
                <th class="text-right">Detalle</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                @php
                    $id = $item['id'];
                    $cantSolicitada = $item['cantidad_solicitada'] ?? 0;
                    $cantPickeada = $pickeados[$item['articulo_id']] ?? 0;
                    $cantPackeada = $packeados[$item['articulo_id']] ?? 0;
                    $porcentaje = $cantSolicitada > 0 ? round(($cantPackeada / $cantSolicitada) * 100, 1) : 0;
                    $color = $porcentaje == 100 ? 'text-green-600 font-bold' :
                             ($porcentaje > 0 ? 'text-yellow-600 font-semibold' : 'text-gray-500');
                @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td class="text-right">{{ $cantSolicitada }}</td>
                    <td class="text-right">{{ $cantPickeada }}</td>
                    <td class="text-right flex justify-end items-center gap-1">
                        <input type="number" min="0" class="w-20 text-right border rounded px-2 py-1"
                            wire:model.defer="packeados.{{ $item['articulo_id'] }}">
                    </td>
                    <td class="text-center">                        
                        <button type="button"
                            wire:click="eliminarPacking({{ $item['articulo_id'] }})"
                            onclick="confirm('¿Estás seguro de eliminar TODO el embalaje para este artículo? Esto no se puede deshacer.') || event.stopImmediatePropagation()"
                            class="content-end text-red-600 hover:text-red-800 font-bold text-lg leading-none">
                            ×
                        </button>
                    </td>
                    <td class="text-right {{ $color }}">{{ $porcentaje }}%</td>
                    <td class="text-right">
                        <button type="button" 
                            wire:click="abrirDetalle({{ $item['articulo_id'] }})" 
                            class="text-blue-600 hover:text-blue-800 p-1 rounded transition duration-150 ease-in-out"
                            title="Ver Detalle de Packing" {{-- Añadimos un tooltip para accesibilidad --}}
                        >
                            {{-- Ícono SVG de "Ojo" (Heroicon/similar) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.173a1.012 1.012 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.173z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 flex justify-end">
        <x-boton color="green" wire:click="guardarCambios">Guardar Cambios</x-boton>
    </div>

    {{-- Modal de Detalle de Cajas (Packing List Completo) --}}
    <div x-data="{ open: @entangle('mostrarCajaDetalleModal') }">
        <div x-show="open" 
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display: none;" 
            aria-labelledby="modal-title-caja" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                    @click="open = false" aria-hidden="true">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" 
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-caja">
                            Packing List Completo - Nota #{{ $nota->numero }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Listado de todas las transacciones, ordenadas por Caja y Pallet.</p>
                        
                        <div class="mt-2 h-96 overflow-y-auto border rounded">
                            @if (count($packingCajaDetalle) > 0)
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caja</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pallet</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artículo ID</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $currentCaja = null; @endphp
                                        @foreach ($packingCajaDetalle as $detalle)
                                            @php
                                                $cajaChanged = $currentCaja !== $detalle['caja'];
                                                $currentCaja = $detalle['caja'];
                                            @endphp
                                            {{-- Resaltamos la fila cuando cambia el número de caja --}}
                                            <tr class="@if($cajaChanged) bg-blue-50/50 font-semibold @endif">
                                                <td class="px-3 py-1 whitespace-nowrap">{{ $detalle['caja'] }}</td>
                                                <td class="px-3 py-1 whitespace-nowrap">{{ $detalle['pallet'] }}</td>
                                                <td class="px-3 py-1 whitespace-nowrap">{{ $detalle['articulo_id'] }}</td>
                                                <td class="px-3 py-1 whitespace-nowrap text-right">{{ $detalle['cantidad'] }}</td>
                                                <td class="px-3 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($detalle['fecha_fin'])->format('d/m H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500 p-4">Aún no hay transacciones de packing para esta nota.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="cerrarDetalleCajas" @click="open = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Modal popup detalle por artículo--}}

    <div x-data="{ open: @entangle('mostrarDetalleModal') }">
        <div x-show="open" 
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display: none;" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false" aria-hidden="true">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Detalle de Packing - Artículo: {{ $articuloCodigoDetalle }}
                        </h3>
                        <p>Podes bajar cantidades o eliminar líneas. Para agregar ir al form principal.</p>
                        <p>Guardado inmediato al editar un campo.</p>
                        <div class="mt-2">
                            @if (count($packingDetalle) > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Caja</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pallet</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($packingDetalle as $detalle)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($detalle['fecha_fin'])->format('d/m H:i:s') }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-right">
                                                    <input type="number" min="0" 
                                                        class="w-20 text-right border rounded px-1 py-0.5 text-sm"
                                                        wire:model.live.debounce.500ms="packingDetalle.{{ $loop->index }}.cantidad"
                                                        wire:change="actualizarTransaccionPacking({{ $detalle['id'] }}, 'cantidad', $event.target.value)"
                                                    >
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-right">
                                                    <input type="number" min="1" 
                                                        class="w-16 text-right border rounded px-1 py-0.5 text-sm"
                                                        wire:model.live.debounce.500ms="packingDetalle.{{ $loop->index }}.caja"
                                                        wire:change="actualizarTransaccionPacking({{ $detalle['id'] }}, 'caja', $event.target.value)"
                                                    >
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-right">
                                                    <input type="number" min="1" 
                                                        class="w-16 text-right border rounded px-1 py-0.5 text-sm"
                                                        wire:model.live.debounce.500ms="packingDetalle.{{ $loop->index }}.pallet"
                                                        wire:change="actualizarTransaccionPacking({{ $detalle['id'] }}, 'pallet', $event.target.value)"
                                                    >
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                                    <button type="button" 
                                                        wire:click="eliminarTransaccionPacking({{ $detalle['id'] }})" 
                                                        onclick="confirm('¿Estás seguro de eliminar esta línea del embalaje?') || event.stopImmediatePropagation()"
                                                        class="text-red-600 hover:text-red-900 ml-2" title="Eliminar Transacción">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500">No hay registros de packing individuales para este artículo.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="open = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- JS para foco --}}
<script>
    // **OPCIONAL:** Si deseas seguir usando los dispatches de Livewire para forzar el foco 
    // en ciertas condiciones de error (aunque la lógica de Alpine es más directa).
    document.addEventListener('livewire:load', function () {
        Livewire.on('focusCodigo', () => {
            const codigo = document.getElementById('codigo');
            if (codigo) { codigo.focus(); codigo.select(); }
        });
        // Si tienes la directiva 'focusCantidad' en tu Livewire:
        Livewire.on('focusCantidad', () => {
            const cantidad = document.getElementById('cantidad');
            if (cantidad) { cantidad.focus(); cantidad.select(); }
        });
    });
</script>
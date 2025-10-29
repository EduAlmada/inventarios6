<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Nota de Venta #') . $nota->nota }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            {{-- CABECERA --}}
            <h3 class="text-lg font-semibold mb-3">Cabecera del Pedido</h3>
                {{-- Mensajes al grabar --}}
                <div>
                    @if (session('warning'))
                        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded">
                            {{ session('warning') }}
                            @if (session('import_errors'))
                                <ul class="list-disc pl-5 mt-2">
                                    @foreach (session('import_errors') as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
            <form action="{{ route('notas.update', $nota) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- Este campo oculto le indica al controlador que la acción es "edit" --}}
                <input type="hidden" name="action" value="edit">
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <x-input-label for="nota" value="Nota" />
                        <x-text-input id="nota" name="nota" value="{{ old('nota', $nota->nota) }}" class="w-full" />
                    </div>

                   <div>
                        <x-input-label for="orden_compra" value="Orden de compra" />
                        <x-text-input id="orden_compra" name="orden_compra" value="{{ old('orden_compra', $nota->orden_compra) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="transporte" value="Transporte deseado por Cliente" />
                        <x-text-input id="transporte" name="transporte" value="{{ old('transporte', $nota->transporte) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="domicilio_transporte" value="Domicilio del transporte deseado por Cliente" />
                        <x-text-input id="domicilio_transporte" name="domicilio_transporte" value="{{ old('domicilio_transporte', $nota->domicilio_transporte) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fill_rate" value="Fill rate" />
                        <x-text-input id="fecha_fill_rate" name="fecha_fill_rate" value="{{ old('fecha_fill_rate', $nota->fecha_fill_rate) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="cliente" value="Cliente" />
                        <x-text-input id="cliente" name="cliente" value="{{ old('cliente', $nota->cliente) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="domicilio" value="Domicilio" />
                        <x-text-input id="domicilio" name="domicilio" value="{{ old('domicilio', $nota->domicilio) }}" class="w-full" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <x-text-input id="estado" name="estado" value="{{ old('estado', $nota->estado) }}" class="w-full" />
                    </div>
                </div>

                {{-- BOTÓN GUARDAR --}}
                <div class="mt-6 flex justify-end">
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>

            {{-- DETALLE DE ARTÍCULOS --}}
<h3 class="text-lg font-semibold mt-10 mb-3">Artículos del Pedido</h3>

<form id="form-items" action="{{ route('notas.update', $nota) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="items">
    {{--Encapsulado--}}
    <div class="overflow-x-auto">
        <div style="max-height: 400px;" class="overflow-y-auto border rounded-lg shadow-inner">
            <table class="w-full text-sm text-gray-700" id="items-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Código</th>
                        <th class="px-3 py-2 text-left">Descripción</th>
                        <th class="px-3 py-2 text-right">Solicitado</th>
                        <th class="px-3 py-2 text-right">Preparado</th>
                        <th class="px-3 py-2 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    {{-- Items existentes --}}
                    @foreach($nota->items as $item)
                        @php
                            $preparado = $item->cantidad_preparada ?? 0;
                            $canDelete = $preparado == 0; // Solo se puede eliminar si el preparado es igual a cero (0)
                            $buttonClass = $canDelete ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-400 cursor-not-allowed';
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ $item->articulo->codigo ?? 'N/D' }}
                                <input type="hidden" name="items[{{ $item->id }}][codigo]" value="{{ $item->articulo->codigo }}">
                            </td>
                            <td class="px-3 py-2">{{ $item->articulo->descripcion ?? 'N/D' }}</td>
                            <td class="px-3 py-2 text-right">
                                <input type="number" name="items[{{ $item->id }}][cantidad]" value="{{ $item->cantidad_solicitada }}" min="0" class="w-24 text-right border rounded px-1">
                            </td>
                            <td class="px-3 py-2 text-right">{{ $item->cantidad_preparada ?? 0 }}</td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" 
                                    class="remove-row px-2 py-1 text-white rounded {{ $buttonClass }}"
                                    data-item-id="{{ $item->id }}"
                                    data-preparado="{{ $preparado }}" 
                                    {{ $canDelete ? '' : 'disabled' }}> X
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <button type="button" id="add-row" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded">+ Agregar fila</button>

    <div class="mt-6 flex justify-end">
        <x-primary-button>Guardar Cambios del Detalle</x-primary-button>
    </div>
</form>

            

            {{-- BOTONES DE NAVEGACIÓN --}}
            <div class="mt-10 flex gap-4 justify-end">
                <a href="{{ route('notas.descargarPreparado', $nota) }}" 
                class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 flex items-center gap-2"
                title="Descargar detalle de artículos preparados a CSV">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Descargar Detalle
                </a>
                <a href="{{ route('picking.show', $nota) }}" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                    Ir a Picking
                </a>
                <a href="{{ route('packing.show', $nota) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Ir a Packing
                </a>
            </div>

        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addRowButton = document.getElementById('add-row');
    const itemsBody = document.getElementById('items-body');
    const formItems = document.getElementById('form-items'); // ✅ Form correcto

    // Agregar fila nueva
    addRowButton.addEventListener('click', function() {
        const rowCount = itemsBody.children.length;
        const row = document.createElement('tr');
        row.classList.add('border-b', 'hover:bg-gray-50');

        row.innerHTML = `
            <td class="px-3 py-2">
                <input type="text" name="new_items[${rowCount}][codigo]" class="border rounded px-2 py-1 w-full" required>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="new_items[${rowCount}][descripcion]" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2 text-right">
                <input type="number" name="new_items[${rowCount}][cantidad]" class="border rounded px-2 py-1 w-20 text-right" value="1" min="1" required>
            </td>
            <td class="px-3 py-2 text-right">0</td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded">X</button>
            </td>
        `;
        itemsBody.appendChild(row);

        // Eliminar fila nueva (no existe en DB)
        row.querySelector('.remove-row').addEventListener('click', function() {
            row.remove();
        });
    });

    // Eliminar filas existentes (marcar para eliminación en backend)
    document.querySelectorAll('#items-body .remove-row').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            // Si el botón está deshabilitado por el backend/HTML, prevenimos la acción y advertimos
            if (btn.disabled) {
                e.preventDefault();
                alert('🚫 No se puede eliminar este artículo porque ya tiene una cantidad preparada (' + btn.dataset.preparado + '). Primero debe eliminar los registros de Packing/Preparación.');
                return;
            }

            const row = btn.closest('tr');
            const input = row.querySelector('input[name^="items["]');

            if (!input) return;

            // Extraer el ID del item
            const match = input.name.match(/\d+/);
            if (!match) return;
            const itemId = match[0];

            // Crear el hidden input para delete_items[]
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_items[]';
            hiddenInput.value = itemId;

            // ✅ Agregarlo al formulario correcto
            formItems.appendChild(hiddenInput);

            // Eliminar visualmente la fila
            row.remove();
        });
    });
});
</script>

</x-app-layout>

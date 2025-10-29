<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ "Picking Nota #$nota->nota" }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            {{-- 🔹 Formulario para registrar nuevo picking --}}
            <form action="{{ route('picking.store', $nota) }}" method="POST" class="mb-6" id="pickingForm">
                @csrf
                <div class="grid grid-cols-3 gap-4 items-end">
                    <div>
                        <x-input-label for="codigo" value="Código de Artículo" />
                        <x-text-input id="codigo" name="codigo" type="text" autofocus autocomplete="off"
                            value="{{ old('codigo') }}" />
                        @error('codigo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <x-input-label for="cantidad" value="Cantidad" />
                        <x-text-input id="cantidad" name="cantidad" type="number" min="1"
                            value="{{ old('cantidad') }}" />
                        @error('cantidad')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <x-primary-button>Agregar Picking</x-primary-button>
                    </div>
                </div>
            </form>
            {{-- 🔹 Barra de búsqueda --}}
            <form method="GET" action="{{ route('picking.show', $nota) }}" class="mb-4 flex justify-between">
                <div class="flex items-center gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar código o descripción..."
                        class="border-gray-300 rounded px-3 py-2 w-64">

                    <x-primary-button type="submit">Buscar</x-primary-button>

                    @if(request('buscar'))
                        <a href="{{ route('picking.show', $nota) }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- 🔹 Formulario de edición masiva de cantidades --}}
            <form action="{{ route('picking.updateCantidades', $nota) }}" method="POST" id="form-updateCantidades">
                @csrf

                {{-- 🔹 Resumen superior --}}
                <div class="mb-6 p-4 bg-gray-50 rounded flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold mb-1">Avance del Picking</h3>
                        <p class="text-gray-700">
                            Completado: 
                            <span class="font-bold {{ $porcentajeGlobal == 100 ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $porcentajeGlobal }}%
                            </span>
                        </p>
                        @if($duracionHoras)
                            <p class="text-gray-600 text-sm">Tiempo total: {{ $duracionHoras }} h</p>
                        @endif
                    </div>

                    <div class="w-1/2 bg-gray-200 h-4 rounded-full overflow-hidden">
                        <div class="h-4 bg-green-500" style="width: {{ $porcentajeGlobal }}%"></div>
                    </div>
                </div>

                {{-- 🔹 Tabla de detalle --}}
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Depósito</th>
                            <th class="px-3 py-2 text-left">Zona</th>
                            <th class="px-3 py-2 text-left">Pasillo</th>
                            <th class="px-3 py-2 text-left">Código</th>
                            <th class="px-3 py-2 text-left">Artículo</th>
                            <th class="px-3 py-2 text-right">Solicitado</th>
                            <th class="px-3 py-2 text-right">Pickeado</th>
                            <th class="px-3 py-2 text-right">Avance</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 🔹Paginacion de la tabla y calculo de porcentajes--}}
                        @php
                            $perPage = 4;
                            $pagina = request('page', 1);
                            $itemsPaginados = $itemsConZona->forPage($pagina, $perPage);
                        @endphp

                        @foreach($itemsPaginados as $item)
                            @php
                                $cantSolicitada = $cantidadesSolicitadas[$item['articulo_id']] ?? 0;
                                $cantPickeadaTotal = $pickings[$item['articulo_id']] ?? 0;
                                $porcentaje = $cantSolicitada > 0 ? round(($cantPickeadaTotal / $cantSolicitada) * 100, 1) : 0;

                                $color = match(true) {
                                    $porcentaje == 100 => 'text-green-600 font-bold',
                                    $porcentaje > 0 => 'text-yellow-600 font-semibold',
                                    default => 'text-gray-500',
                                };
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $item['deposito_nombre'] }}</td>
                                <td class="px-3 py-2">{{ $item['zona_nombre'] }}</td>
                                <td class="px-3 py-2">{{ $item['pasillo'] }}</td>
                                <td class="px-3 py-2">{{ $item['articulo_codigo'] }}</td>
                                <td class="px-3 py-2">{{ $item['descripcion'] }}</td>
                                <td class="px-3 py-2 text-right">{{ $item['cantidad_solicitada'] }}</td>
                                <td class="px-3 py-2 text-right">
                                    <input type="number" name="pickeados[{{ $item['articulo_id'] }}]"
                                           value="{{ $cantPickeadaTotal }}"
                                           min="0" class="w-20 text-right border rounded px-2 py-1">
                                </td>
                                <td class="px-3 py-2 text-right {{ $color }}">{{ $porcentaje }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- 🔹 Paginación simple --}}
                <div class="mt-4 flex justify-center">
                    @for ($i = 1; $i <= ceil($itemsConZona->count() / $perPage); $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                           class="px-3 py-1 border rounded mx-1 {{ $pagina == $i ? 'bg-blue-500 text-white' : 'bg-gray-100' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            </form>
            {{--Botones--}}
            <div class="mt-4 flex justify-end gap-3">
                
                {{-- Botón Guardar Cambios (Tipo submit que usa el atributo form="...") --}}
                <button type="submit" form="form-updateCantidades" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                    Guardar Cambios
                </button>

                {{-- FORMULARIO DE LIBERACIÓN (AHORA ES UN FORMULARIO SEPARADO Y FUNCIONAL) --}}
                <form action="{{ route('picking.liberar', $nota) }}" method="POST" onsubmit="return confirm('¿Está seguro que desea salir? Esto liberará la Nota del pool de trabajo.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                        Salir del Picking
                    </button>
                </form>
            </div>
        </div>
    </div>
    {{-- 🔹 Script para controlar el flujo de escaneo --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const codigoInput = document.getElementById('codigo');
        const cantidadInput = document.getElementById('cantidad');
        const form = document.getElementById('pickingForm');

        // Cuando el usuario presiona Enter en el campo "codigo"
        codigoInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // evita enviar el formulario
                cantidadInput.focus(); // mueve el foco a "cantidad"
            }
        });

        // Solo se permite enviar el formulario si ambos campos tienen valor
        form.addEventListener('submit', function (e) {
            if (!codigoInput.value.trim() || !cantidadInput.value.trim()) {
                e.preventDefault();
                alert('Debe ingresar el código y la cantidad antes de continuar.');
                codigoInput.focus();
            }
        });
    });
    </script>
</x-app-layout>

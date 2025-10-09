<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de Stock por Zona') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="GET" action="{{ route('stock.index') }}" class="mb-6">
                        <div class="grid grid-cols-4 gap-4 items-end">
                            
                            {{-- Filtro por Código --}}
                            <div>
                                <x-input-label for="codigo" value="Código de Artículo" />
                                <x-text-input type="text" name="codigo" id="codigo" value="{{ request('codigo') }}" class="w-full" />
                            </div>

                            {{-- Filtro por Depósito --}}
                            <div>
                                <x-input-label for="deposito_id" value="Depósito" />
                                <select name="deposito_id" id="deposito_id" class="w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">Todos los depósitos</option>
                                    @foreach ($depositos as $deposito)
                                        <option value="{{ $deposito->id }}" @selected(request('deposito_id') == $deposito->id)>
                                            {{ $deposito->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro por Zona --}}
                            <div>
                                <x-input-label for="zona_id" value="Zona" />
                                <select name="zona_id" id="zona_id" class="w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">Todas las zonas</option>
                                    @foreach ($zonas as $zona)
                                        <option value="{{ $zona->id }}" @selected(request('zona_id') == $zona->id)>
                                            {{ $zona->nombre }} ({{ $zona->deposito->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Botón de Filtrar --}}
                            <div class="col-span-1">
                                <x-primary-button type="submit">
                                    {{ __('Filtrar') }}
                                </x-primary-button>
                                {{-- Botón para limpiar filtros --}}
                                <a href="{{ route('stock.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-900">Limpiar</a>
                            </div>
                        </div>
                    </form>
                    
                    <div class="overflow-x-auto shadow-md rounded-lg mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Depósito</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stock->articulo->codigo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->articulo->descripcion }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->cantidad }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->zona->deposito->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->zona->nombre }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $stocks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
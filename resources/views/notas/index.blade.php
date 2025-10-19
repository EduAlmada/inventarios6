<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitor de Pedidos de Venta (Notas)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Lista de Notas de Venta
                    </h3>
                    
                    {{-- Contenedor para acciones --}}
                    <div class="mb-4 flex justify-between items-center">
                        
                        {{-- Aquí irán los filtros si los agregas --}}
                        <div>
                            {{-- Puedes poner aquí un formulario de filtro de estado/fecha --}}
                        </div>

                        {{-- Botón de IMPORTAR/CREAR --}}
                        <x-primary-link href="{{ route('notas.create') }}">
                            {{ __('Importar Nuevo Pedido') }}
                        </x-primary-link>
                    </div>

                    {{-- TABLA DE NOTAS --}}
                    @if ($notas->isEmpty())
                        <p class="text-gray-500">No hay pedidos registrados.</p>
                    @else
                        <div class="overflow-x-auto shadow-md rounded-lg">
                            <table class="w-full text-sm text-gray-500">
                                <thead>
                                    <tr class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <th class="px-6 py-3 text-left">Número Nota</th>
                                        <th class="px-6 py-3 text-left">Cliente</th>
                                        <th class="px-6 py-3 text-left">Estado</th>
                                        <th class="px-6 py-3 text-left">Creado Por</th>
                                        <th class="px-6 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notas as $nota)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900">{{ $nota->nota }}</td>
                                            <td class="px-6 py-4">{{ $nota->cliente }}</td>
                                            <td class="px-6 py-4 font-bold">{{ $nota->estado }}</td>
                                            <td class="px-6 py-4">{{ $nota->user->name ?? 'Sistema' }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('notas.edit', $nota) }}" class="text-green-600 hover:text-green-900">Editar</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4 p-4">
                                {{ $notas->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
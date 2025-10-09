<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Artículos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Botones en Index de Artículos --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Listado de Artículos
                        </h3>
                        <x-primary-link href="{{ route('articulos.download') }}" class="bg-emerald-600 hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800">
                            {{ __('Descargar Artículos') }}
                        </x-primary-link>
                        <x-primary-link href="{{ route('articulos.create') }}">
                            {{ __('Crear Artículo') }}
                        </x-primary-link>
                    </div>

                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="w-full text-sm text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">Código</th>
                                    <th scope="col" class="px-6 py-3 text-left">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-left">Stock</th>
                                    <th scope="col" class="px-6 py-3 text-left">EAN13</th>
                                    <th scope="col" class="px-6 py-3 text-left">DUN14</th>
                                    <th scope="col" class="px-6 py-3 text-left">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articulos as $articulo)
                                    <tr class="bg-white border-b hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $articulo->codigo }}</td>
                                        <td class="px-6 py-4">{{ $articulo->descripcion }}</td>
                                        <td class="px-6 py-4">{{ $articulo->stock }}</td>
                                        <td class="px-6 py-4">{{ $articulo->EAN13 }}</td>
                                        <td class="px-6 py-4">{{ $articulo->DUN14 }}</td>
                                        <td class="px-6 py-4">{{ $articulo->precio }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('articulos.edit', $articulo) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                            <form action="{{ route('articulos.destroy', $articulo) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar el registro?')">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
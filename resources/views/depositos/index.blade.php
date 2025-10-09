<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Depósitos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    {{-- Formulario de creación --}}
                    <form method="POST" action="{{ route('depositos.store') }}" class="mb-8">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <div>
                                <x-input-label for="nombre" :value="__('Nombre del Depósito')" />
                                <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="descripcion" :value="__('Descripción')" />
                                <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                            </div>
                            <div class="pt-6">
                                <x-primary-button>
                                    {{ __('Crear Depósito') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    {{-- Tabla de depósitos --}}
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="w-full text-sm text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($depositos as $deposito)
                                    <tr class="bg-white border-b hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $deposito->nombre }}</td>
                                        <td class="px-6 py-4">{{ $deposito->descripcion }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('depositos.edit', $deposito) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                            <form action="{{ route('depositos.destroy', $deposito) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
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
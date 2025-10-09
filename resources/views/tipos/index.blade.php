<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Tipos de Actividad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="POST" action="{{ route('tipos.store') }}">
                        @csrf
                        <div class="mt-4">
                            <x-input-label for="nombre" :value="__('Nombre del Tipo de Actividad')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>
                        {{-- Dentro del formulario de creación o edición --}}
                        <div class="mt-4">
                            <x-input-label for="signo_stock" :value="__('Impacto en el Stock')" />
                            <select id="signo_stock" name="signo_stock" required class="block w-full rounded-md shadow-sm border-gray-300">
                                <option value="0" @selected(old('signo_stock', $tipo->signo_stock ?? 0) == 0)>Neutro (No modifica stock)</option>
                                <option value="1" @selected(old('signo_stock', $tipo->signo_stock ?? 0) == 1)>Positivo (+)</option>
                                <option value="-1" @selected(old('signo_stock', $tipo->signo_stock ?? 0) == -1)>Negativo (-)</option>
                            </select>
                            <x-input-error :messages="$errors->get('signo_stock')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <input type="checkbox" id="modifica_stock" name="modifica_stock" value="1" @checked(old('modifica_stock', $tipo->modifica_stock ?? false))>
                            <label for="modifica_stock">{{ __('Esta actividad modifica la cantidad') }}</label>
                        </div>
                        <x-primary-button class="mt-4 bg-emerald-500">
                            {{ __('Guardar Tipo de Actividad') }}
                        </x-primary-button>
                    </form>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Tipos de Actividad
                        </h3>
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Nombre</th>
                                    <th class="py-2 text-left">Modifica Stock</th>
                                    <th class="py-2 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tipos as $tipo)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $tipo->nombre }}</td>
                                        <td class="px-6 py-4 text-left">
                                            @if ($tipo->modifica_stock)
                                                Sí
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <a href="{{ route('tipos.edit', $tipo) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form action="{{ route('tipos.destroy', $tipo) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que quieres eliminar este tipo de actividad?')">Eliminar</button>
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
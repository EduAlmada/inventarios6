<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Tipo de Actividad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Editar: {{ $tipo->nombre }}
                    </h3>

                    <form method="POST" action="{{ route('tipos.update', $tipo) }}">
                        @csrf
                        @method('PUT')
                        <div class="mt-4">
                            <x-input-label for="nombre" :value="__('Nombre del Tipo de Actividad')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $tipo->nombre)" required autofocus />
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
                            <x-input-error :messages="$errors->get('modifica_stock')" class="mt-2" />
                        </div>
                        <x-primary-button class="mt-4">
                            {{ __('Guardar Cambios') }}
                        </x-primary-button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('tipos.index') }}" class="text-indigo-600 hover:text-indigo-900">Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
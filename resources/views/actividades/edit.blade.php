<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Actividad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Editando registro de actividad #{{ $actividad->id }}
                    </h3>

                    <!-- Formulario de edición -->
                    <form method="POST" action="{{ route('actividades.update', $actividad) }}">
                        @csrf
                        @method('PUT')

                        <!-- Campo Tipo de Actividad -->
                        <div class="mt-4">
                            <x-input-label for="tipo" :value="__('Tipo de Actividad')" />
                            <select id="tipo" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="tipo" required>
                                <option value="">Selecciona una opción</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->nombre }}" @selected(old('tipo', $actividad->tipo) == $tipo->nombre)>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="operador" :value="__('Operador')" />
                            <x-text-input id="operador" class="block mt-1 w-full" type="text" name="operador" :value="old('operador', $actividad->operador)" required />
                            <x-input-error :messages="$errors->get('operador')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="pedido" :value="__('Número de Pedido')" />
                            <x-text-input id="pedido" class="block mt-1 w-full" type="text" name="pedido" />
                            <x-input-error :messages="$errors->get('pedido')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="bultos" :value="__('Bultos')" />
                            <x-text-input id="bultos" class="block mt-1 w-full" type="number" name="bultos" :value="old('bultos', $actividad->bultos)" required />
                            <x-input-error :messages="$errors->get('bultos')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="fecha_actividad" :value="__('Fecha y Hora de la Actividad (Opcional)')" />
                            <x-text-input id="fecha_actividad" class="block mt-1 w-full" type="datetime-local" name="fecha_actividad" :value="old('fecha_actividad', \Carbon\Carbon::parse($actividad->fecha_actividad)->format('Y-m-d\TH:i'))" />
                            <x-input-error :messages="$errors->get('fecha_actividad')" class="mt-2" />
                        </div>

                        <x-primary-button class="mt-4">
                            {{ __('Actualizar Actividad') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
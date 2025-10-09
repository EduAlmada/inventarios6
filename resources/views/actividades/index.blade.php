<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Actividades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Registrar nueva actividad
                    </h3>

                    <form method="POST" action="{{ route('actividades.store') }}" 
                        x-data="{ 
                            livewireItems: [],
                            selectedTipo: '{{ old('tipo', '') }}', 
                            tiposMap: @js($tipos_map), 

                            get requiereStock() {
                                // SOLUCIÓN: Usar la coerción de tipos (==) o convertir a entero (parseInt)
                                // Usamos una verificación más flexible para el valor 1.
                                return this.tiposMap[this.selectedTipo] == 1; 
                            }
                        }" 
                        @items-updated.window="livewireItems = $event.detail.items"
                    >
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- 1. CAMPO TIPO DE ACTIVIDAD (Radio Buttons) --}}
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Tipo de Actividad')" class="mb-2" />
                                
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($tipos as $tipo)
                                        @php $inputId = 'tipo-' . Str::slug($tipo->nombre); @endphp
                                        <div class="relative"> 
                                            <input 
                                                type="radio" 
                                                id="{{ $inputId }}" 
                                                name="tipo" 
                                                value="{{ $tipo->nombre }}" 
                                                class="hidden peer"
                                                required
                                                x-model="selectedTipo" 
                                            >
                                            <label for="{{ $inputId }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-500 peer-checked:text-white hover:text-indigo-600 hover:bg-indigo-50 text-gray-700 bg-white border-gray-300 transition duration-200 ease-in-out relative z-10">
                                                {{ $tipo->nombre }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>

                            {{-- 2. CAMPOS GENERALES (Operador, Pedido, Bultos, Fecha) --}}
                            <div class="grid grid-cols-2 gap-4 md:col-span-2 mt-4">
                                <div><x-input-label for="operador" :value="__('Operador')" /><x-text-input id="operador" class="block mt-1 w-full" type="text" name="operador" required value="{{ old('operador') }}" /></div>
                                <div><x-input-label for="bultos" :value="__('Bultos')" /><x-text-input id="bultos" class="block mt-1 w-full" type="number" name="bultos" value="{{ old('bultos') }}" /></div>
                                <div><x-input-label for="pedido" :value="__('Número de Pedido')" /><x-text-input id="pedido" class="block mt-1 w-full" type="text" name="pedido" value="{{ old('pedido') }}" /></div>
                                <div><x-input-label for="fecha_actividad" :value="__('Fecha y Hora de la Actividad (Opcional)')" /><x-text-input id="fecha_actividad" class="block mt-1 w-full" type="datetime-local" name="fecha_actividad" value="{{ old('fecha_actividad') }}" /></div>
                            </div>
                        </div>
                        
                        <hr class="my-6 border-gray-200" x-show="requiereStock" x-transition>

                        {{-- SECCIÓN CONDICIONAL DE STOCK (Depósito, Zona, Artículos) --}}
                        <div x-show="requiereStock" x-transition>
                            <h4 class="text-lg font-medium text-gray-900 mt-4 mb-4">Ubicación y Artículos</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Componente de Selección en Cascada (Depósito y Zona) --}}
                                <livewire:selector-deposito-zona :depositos="$depositos" />
                            </div>
                            
                            {{-- Grilla de Artículos (Livewire Component) --}}
                            <livewire:movimiento-articulo />
                        </div>

                        {{-- Campo oculto para enviar los datos de Livewire --}}
                        <input type="hidden" name="livewireItems" :value="JSON.stringify(livewireItems)">
                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        <x-input-error :messages="$errors->get('zona_id')" class="mt-2" />
                        
                        <x-primary-button class="mt-4">
                            {{ __('Guardar Actividad') }}
                        </x-primary-button>
                    </form>
                    
                    {{-- 1. SECCIÓN DE FILTROS Y DESCARGA --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Filtros y Descarga
                        </h3>

                        <div class="flex items-center space-x-4 mb-4">
                            {{-- Formulario de Filtrado --}}
                            <form method="GET" action="{{ route('actividades.index') }}" class="flex items-center space-x-4">
                                <div>
                                    <x-input-label for="desde" value="Desde" />
                                    <x-text-input type="date" name="desde" id="desde" value="{{ request('desde') }}" />
                                </div>
                                <div>
                                    <x-input-label for="hasta" value="Hasta" />
                                    <x-text-input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}" />
                                </div>
                                <div class="pt-6">
                                    <x-primary-button type="submit">
                                        {{ __('Filtrar') }}
                                    </x-primary-button>
                                    <a href="{{ route('actividades.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-900">
                                        {{ __('Limpiar') }}
                                    </a>
                                </div>
                            </form>

                            {{-- Botón de Descarga CSV --}}
                            <div class="pt-6">
                                <a href="{{ route('actividades.download', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Descargar CSV') }}
                                </a>
                            </div>
                            {{-- Mostrar el mensaje de error de descarga --}}
                            @error('download')
                                <p class="text-sm text-red-600 mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. TABLA DE ACTIVIDADES RECIENTES --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Mis actividades recientes
                        </h3>
                        <div class="overflow-x-auto shadow-md rounded-lg">
                            <table class="w-full text-sm text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left">Operador</th>
                                        <th scope="col" class="px-6 py-3 text-left">Pedido</th>
                                        <th scope="col" class="px-6 py-3 text-left">Bultos</th>
                                        <th scope="col" class="px-6 py-3 text-left">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($actividades as $actividad)
                                        <tr class="bg-white border-b hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $actividad->tipo }}</td>
                                            <td class="px-6 py-4">{{ $actividad->operador }}</td>
                                            <td class="px-6 py-4">{{ $actividad->pedido }}</td>
                                            <td class="px-6 py-4">{{ $actividad->bultos }}</td>
                                            <td class="px-6 py-4">
                                                {{ $actividad->fecha_actividad ? \Carbon\Carbon::parse($actividad->fecha_actividad)->format('d/m/Y H:i') : $actividad->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('actividades.edit', $actividad) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                                <form action="{{ route('actividades.destroy', $actividad) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que quieres eliminar este registro?')">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Paginación --}}
                        <div class="mt-4">
                            {{ $actividades->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Zona') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('zonas.update', $zona) }}" class="mb-8">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center space-x-4">
                            <div>
                                <x-input-label for="deposito_id" :value="__('Depósito')" />
                                <select id="deposito_id" name="deposito_id" required class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Selecciona un depósito</option>
                                    @foreach ($depositos as $deposito)
                                        <option value="{{ $deposito->id }}" @selected(old('deposito_id', $zona->deposito_id) == $deposito->id)>
                                            {{ $deposito->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('deposito_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="nombre" :value="__('Nombre de la Zona')" />
                                <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre', $zona->nombre) }}" required />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="pasillo" :value="__('Pasillo')" />
                                <x-text-input id="pasillo" name="pasillo" type="text" class="mt-1 block w-full" value="{{ old('pasillo', $zona->pasillo) }}" />
                                <x-input-error :messages="$errors->get('pasillo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="descripcion" :value="__('Descripción')" />
                                <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full" value="{{ old('descripcion', $zona->descripcion) }}" />
                                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                            </div>
                            <div class="pt-6">
                                <x-primary-button>
                                    {{ __('Actualizar') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                    
                    <a href="{{ route('zonas.index') }}" class="text-indigo-600 hover:text-indigo-900 mt-4 block">
                        {{ __('Volver a la lista') }}
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
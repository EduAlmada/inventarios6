<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Depósito') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('depositos.update', $deposito) }}" class="mb-8">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center space-x-4">
                            <div>
                                <x-input-label for="nombre" :value="__('Nombre del Depósito')" />
                                <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" value="{{ old('nombre', $deposito->nombre) }}" required autofocus />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="descripcion" :value="__('Descripción')" />
                                <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full" value="{{ old('descripcion', $deposito->descripcion) }}" />
                                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                            </div>
                            <div class="pt-6">
                                <x-primary-button>
                                    {{ __('Actualizar') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                    
                    <a href="{{ route('depositos.index') }}" class="text-indigo-600 hover:text-indigo-900 mt-4 block">
                        {{ __('Volver a la lista') }}
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
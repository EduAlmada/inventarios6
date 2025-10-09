<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Artículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="POST" action="{{ route('articulos.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="id" value="">

                        <div class="mt-4">
                            <x-input-label for="codigo" :value="__('Código')" />
                            <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" required autofocus />
                            <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <x-text-input id="descripcion" class="block mt-1 w-full" type="text" name="descripcion" required />
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stock Actual')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock_minimo" :value="__('Stock Mínimo')" />
                            <x-text-input id="stock_minimo" class="block mt-1 w-full" type="number" name="stock_minimo" />
                            <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock_maximo" :value="__('Stock Máximo')" />
                            <x-text-input id="stock_maximo" class="block mt-1 w-full" type="number" name="stock_maximo" />
                            <x-input-error :messages="$errors->get('stock_maximo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="foto" :value="__('Foto')" />
                            <x-text-input id="foto" class="block mt-1 w-full" type="file" name="foto" />
                            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="EAN13" :value="__('EAN13')" />
                            <x-text-input id="EAN13" class="block mt-1 w-full" type="text" name="EAN13" />
                            <x-input-error :messages="$errors->get('EAN13')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="DUN14" :value="__('DUN14')" />
                            <x-text-input id="DUN14" class="block mt-1 w-full" type="text" name="DUN14" />
                            <x-input-error :messages="$errors->get('DUN14')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="unidades" :value="__('Unidades por Bulto')" />
                            <x-text-input id="unidades" class="block mt-1 w-full" type="number" name="unidades" />
                            <x-input-error :messages="$errors->get('unidades')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" class="block mt-1 w-full" type="number" step="0.01" name="precio" />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button type="button" onclick="history.back()" class="bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700">
                                {{ __('Volver') }}
                            </x-primary-button>
                            <x-primary-button class="ms-4">
                                {{ __('Guardar Artículo') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
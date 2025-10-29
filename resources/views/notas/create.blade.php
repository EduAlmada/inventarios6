<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importar Notas de Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Cargar Archivo de Pedidos (CSV/XLSX)
                    </h3>

                    {{-- CLAVE: enctype="multipart/form-data" para subir archivos --}}
                    <form action="{{ route('notas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mt-4">
                            <x-input-label for="archivo" :value="__('Archivo de Notas (.csv o .xlsx)')" />
                            <x-text-input id="archivo" name="archivo" type="file" required class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('archivo')" class="mt-2" />
                        </div>



                        @if (session('warning'))
                            <div class="alert alert-warning">
                                <p>{{ session('warning') }}</p>
                                @if (session('import_errors'))
                                    <ul>
                                        @foreach (session('import_errors') as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Importar y Procesar Notas') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
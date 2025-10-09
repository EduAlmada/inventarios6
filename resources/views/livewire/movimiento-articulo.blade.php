<div>
    <div class="mt-4">
        <div class="flex space-x-6">
            <div class="w-2/3">
                <x-input-label for="codigo" :value="__('Escanear Código')" />
                <x-text-input wire:model="codigo" wire:keydown.enter.prevent="addItem" id="codigo" class="block mt-1 w-full" type="text" placeholder="Escanea o escribe el código" autofocus />
                @error('codigo') <span class="text-red-500 text-sm mt-2">{{ $message }}</span> @enderror
            </div>
            <div class="w-1/3">
                <x-input-label for="cantidad" :value="__('Cantidad')" />
                <x-text-input wire:model="cantidad" wire:keydown.enter.prevent="addItem" id="cantidad" class="block mt-1 w-full" type="number" min="1" />
                @error('cantidad') <span class="text-red-500 text-sm mt-2">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-4">
            <x-primary-button wire:click="addItem" type="button">
                {{ __('Agregar Artículo') }}
            </x-primary-button>
        </div>
    </div>

    <div class="mt-8">
        @if (!empty($items))
            <h4 class="text-md font-medium text-gray-700 mb-2">Artículos del Movimiento</h4>
            <div class="overflow-x-auto shadow-sm rounded-lg">
                <table class="w-full text-sm text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">Código</th>
                            <th scope="col" class="px-6 py-3 text-left">Cantidad</th>
                            <th scope="col" class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4">{{ $item['codigo'] }}</td>
                                <td class="px-6 py-4">{{ $item['cantidad'] }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="removeItem({{ $index }})" type="button" class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No hay artículos agregados al movimiento.</p>
        @endif    
    </div>
</div>
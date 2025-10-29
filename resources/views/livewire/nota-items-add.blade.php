<div class="border rounded p-4 bg-gray-50">
    <h4 class="font-semibold mb-3">Agregar nuevos ítems</h4>

    @if (session('error'))
        <div class="mb-2 text-red-600">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="mb-2 text-green-600">{{ session('success') }}</div>
    @endif

    <div>
        @foreach ($rows as $index => $row)
            <div class="flex gap-2 mb-2">
                <input type="text" wire:model="rows.{{ $index }}.codigo" placeholder="Código"
                       class="border rounded px-2 py-1 w-40">
                <input type="number" wire:model="rows.{{ $index }}.cantidad" placeholder="Cantidad"
                       class="border rounded px-2 py-1 w-24 text-right">
                <button type="button" wire:click="removeRow({{ $index }})"
                        class="px-2 py-1 bg-red-500 text-white rounded">X</button>
            </div>
        @endforeach
    </div>

    <div class="mt-3 flex gap-2">
        <button type="button" wire:click="addRow"
                class="px-3 py-1 bg-blue-500 text-white rounded">+ Agregar línea</button>

        <button type="button" wire:click="save"
                class="px-3 py-1 bg-green-600 text-white rounded">Guardar ítems</button>
    </div>
</div>

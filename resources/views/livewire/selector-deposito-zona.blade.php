<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="deposito" :value="__('Depósito')" />
        <select wire:model.live="selectedDeposito" id="deposito" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
            <option value="">Selecciona un depósito</option>
            @foreach ($depositos as $deposito)
                <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('selectedDeposito')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="zona_id" :value="__('Zona')" />
        <select wire:model="selectedZona" name="zona_id" id="zona_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" @if($selectedDeposito && $zonas->isEmpty()) disabled @endif>
            <option value="">Selecciona una zona</option>
            @foreach ($zonas as $zona)
                <option value="{{ $zona->id }}">{{ $zona->nombre }} (Pasillo: {{ $zona->pasillo }})</option>
            @endforeach
        </select>
        {{-- Campo oculto para enviar el ID de Zona al controlador principal --}}
        <input type="hidden" name="zona_id" wire:model="selectedZona">
        <x-input-error :messages="$errors->get('selectedZona')" class="mt-2" />
    </div>
</div>
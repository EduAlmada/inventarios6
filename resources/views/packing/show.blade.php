<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ "Packing Nota #$nota->nota" }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            {{-- Aquí montamos Livewire --}}
            <livewire:packing-nota :nota="$nota" />
        </div>
    </div>
</x-app-layout>

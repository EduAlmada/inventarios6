<div class="p-6 bg-white border-b border-gray-200" wire:poll.10s>
    
    {{-- Mensajes de Livewire (ej. al establecer prioridad) --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <h3 class="text-lg font-medium text-gray-900 mb-4">
        Lista de Pedidos (Monitor de Actividad)
    </h3>
    
    {{-- Contenedor de Acciones y Búsqueda --}}
    <div class="mb-4 flex justify-between items-center">
        {{-- Filtro de búsqueda --}}
        <div class="flex items-center gap-2">
            <input 
                wire:model.live="search"
                type="text" 
                placeholder="Buscar por número de nota..." 
                class="w-64 border rounded px-3 py-2 text-sm"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</button>
            @endif
        </div>
        
        {{-- Botón de IMPORTAR/CREAR (Asumo que sigue esta lógica) --}}
        <x-primary-link href="{{ route('notas.create') }}">
            {{ __('Importar Nuevo Pedido') }}
        </x-primary-link>
    </div>

    {{-- TABLA DE NOTAS --}}
    @if ($notas->isEmpty())
        <p class="text-gray-500">No hay pedidos que coincidan con la búsqueda.</p>
    @else
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Número Nota</th>
                        <th class="px-6 py-3 text-left">Cliente</th>
                        <th class="px-6 py-3 text-left">Estado</th>
                        <th class="px-6 py-3 text-left">Creado Por</th>
                        <th class="px-6 py-3 text-left">Actividad Actual</th>
                        <th class="px-6 py-3 text-center">Prioridad</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notas as $nota)
                        @php
                            $task = $nota->tareaActiva;
                            $isAdmin = auth()->user()->is_admin;
                            $priority = $task->orden_prioridad ?? 0;
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $nota->nota }}</td>
                            <td class="px-6 py-4">{{ $nota->cliente }}</td>
                            <td class="px-6 py-4 font-bold">{{ $nota->estado }}</td>
                            <td class="px-6 py-4">{{ $nota->user->name ?? 'Sistema' }}</td>
                            
                            {{-- COLUMNA DE ACTIVIDAD EN TIEMPO REAL --}}
                            <td class="px-6 py-4">
                                @if ($task)
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium 
                                        {{ $task->tipo_actividad === 'picking' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($task->tipo_actividad) }}: {{ $task->user->name ?? 'N/D' }}
                                    </span>
                                @else
                                    <span class="text-gray-500">Libre</span>
                                @endif
                            </td>

                            {{-- COLUMNA DE GESTIÓN DE PRIORIDAD --}}
                            <td class="px-6 py-4 text-center">
                                @if ($isAdmin)
                                    <input type="number" min="0" 
                                           value="{{ $priority }}"
                                           class="w-16 text-center border rounded px-1 text-sm"
                                           wire:change="setPriority({{ $nota->id }}, $event.target.value)" 
                                    />
                                @else
                                    {{ $priority > 0 ? $priority : '-' }}
                                @endif
                            </td>

                            {{-- COLUMNA DE ACCIONES --}}
                            <td class="px-6 py-4 text-right flex gap-2 justify-end">
                                <a href="{{ route('notas.edit', $nota) }}" class="px-3 text-red-600 hover:text-green-900">Editar</a>
                                <a href="{{ route('picking.show', $nota) }}" class="px-3 text-orange-500 hover:text-green-900">Picking</a>
                                <a href="{{ route('packing.show', $nota) }}" class="px-3 text-green-600 hover:text-green-900">Packing</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 p-4">
                {{ $notas->links() }}
            </div>
        </div>
    @endif
</div>
@props(['color' => 'gray'])

@php
    $colors = [
        'gray'    => 'bg-gray-300 text-gray-800 hover:bg-gray-400 focus:ring-gray-500',
        'orange'  => 'bg-orange-500 text-white hover:bg-orange-600 focus:ring-orange-400',
        'green'   => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'blue'    => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'red'     => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'purple'  => 'bg-purple-600 text-white hover:bg-purple-700 focus:ring-purple-500',
        'cyan'    => 'bg-cyan-500 text-white hover:bg-cyan-600 focus:ring-cyan-400',
        'brown'   => 'bg-amber-800 text-white hover:bg-amber-900 focus:ring-amber-700',
        'violet'  => 'bg-violet-600 text-white hover:bg-violet-700 focus:ring-violet-500',
        'yellow'  => 'bg-yellow-400 text-black hover:bg-yellow-500 focus:ring-yellow-300',
        'indigo'  => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'pink'    => 'bg-pink-500 text-white hover:bg-pink-600 focus:ring-pink-400',
    ];

    $classes = $colors[$color] ?? $colors['gray'];
@endphp

<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => "px-4 py-2 font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 $classes"
]) }}>
    {{ $slot }}
</button>

@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-blue-400 text-start text-base font-medium text-blue-300 bg-blue-900/20 focus:outline-none focus:text-blue-200 focus:bg-blue-900/30 focus:border-blue-400 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700/50 hover:border-gray-600 focus:outline-none focus:text-white focus:bg-gray-700/50 focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

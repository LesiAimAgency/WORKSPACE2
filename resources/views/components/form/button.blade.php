@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-[#001B4E] hover:bg-[#002D80] text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    ];
    $colorClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-6 py-2 rounded-lg font-medium transition-colors duration-200 {$colorClass}"]) }}
>
    {{ $slot }}
</button>
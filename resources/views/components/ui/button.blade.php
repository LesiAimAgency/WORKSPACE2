@props([
    'variant' => 'primary', // primary, secondary, outline, ghost, danger
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'rounded' => false
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Size classes
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];
    
    // Variant classes
    $variantClasses = [
        'primary' => 'bg-primary text-white hover:bg-primary-dark focus:ring-primary',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'outline' => 'border border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary',
        'ghost' => 'text-primary hover:bg-primary hover:text-white focus:ring-primary',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500'
    ];
    
    $classes = collect([
        $baseClasses,
        $sizeClasses[$size] ?? $sizeClasses['md'],
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $fullWidth ? 'w-full' : '',
        $rounded ? 'rounded-full' : 'rounded-md',
        $disabled || $loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ])->filter()->implode(' ');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} mr-2"></i>
        @endif
        
        @if($loading)
            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} ml-2"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} mr-2"></i>
        @endif
        
        @if($loading)
            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} ml-2"></i>
        @endif
    </button>
@endif
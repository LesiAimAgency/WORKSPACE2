@props([
    'icon' => 'fa-box-open',
    'title' => null,
    'description' => null,
    'actionText' => null,
    'actionUrl' => null,
    'size' => 'md' // sm, md, lg
])

@php
    $sizeConfig = [
        'sm' => [
            'container' => 'py-8',
            'icon' => 'text-4xl',
            'title' => 'text-lg',
            'description' => 'text-sm'
        ],
        'md' => [
            'container' => 'py-12',
            'icon' => 'text-5xl',
            'title' => 'text-xl',
            'description' => 'text-base'
        ],
        'lg' => [
            'container' => 'py-16',
            'icon' => 'text-6xl',
            'title' => 'text-2xl',
            'description' => 'text-lg'
        ]
    ];
    
    $config = $sizeConfig[$size] ?? $sizeConfig['md'];
@endphp

<div {{ $attributes->merge(['class' => "text-center {$config['container']}"]) }}>
    <div class="max-w-md mx-auto">
        <i class="fa-light {{ $icon }} {{ $config['icon'] }} text-gray-300 mb-4 block"></i>
        
        @if($title)
            <h3 class="{{ $config['title'] }} font-semibold text-gray-700 mb-2">
                {{ $title }}
            </h3>
        @endif
        
        @if($description)
            <p class="{{ $config['description'] }} text-gray-500 mb-6">
                {{ $description }}
            </p>
        @endif
        
        @if($actionText && $actionUrl)
            <x-ui.button 
                variant="primary" 
                href="{{ $actionUrl }}"
                class="inline-flex">
                {{ $actionText }}
            </x-ui.button>
        @endif
        
        {{ $slot }}
    </div>
</div>
@props([
    'stock' => 0,
    'status' => 'in_stock', // in_stock, out_of_stock, pre_order, backorder
    'showText' => true,
    'size' => 'sm' // sm, md, lg
])

@php
    $statusConfig = [
        'in_stock' => [
            'text' => __f('product_in_stock'),
            'color' => 'text-green-600',
            'bg' => 'bg-green-100',
            'border' => 'border-green-200',
            'icon' => 'fa-check-circle'
        ],
        'out_of_stock' => [
            'text' => __('Hết hàng'),
            'color' => 'text-red-600',
            'bg' => 'bg-red-100',
            'border' => 'border-red-200',
            'icon' => 'fa-times-circle'
        ],
        'pre_order' => [
            'text' => __f('product_pre_order'),
            'color' => 'text-blue-600',
            'bg' => 'bg-blue-100',
            'border' => 'border-blue-200',
            'icon' => 'fa-clock'
        ],
        'backorder' => [
            'text' => __f('product_backorder'),
            'color' => 'text-yellow-600',
            'bg' => 'bg-yellow-100',
            'border' => 'border-yellow-200',
            'icon' => 'fa-hourglass-half'
        ]
    ];
    
    // Determine status based on stock if not explicitly set
    if ($stock <= 0 && $status === 'in_stock') {
        $status = 'out_of_stock';
    }
    
    $config = $statusConfig[$status] ?? $statusConfig['in_stock'];
    
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base'
    ];
    
    $classes = collect([
        'inline-flex items-center rounded-full font-medium border',
        $config['color'],
        $config['bg'],
        $config['border'],
        $sizeClasses[$size] ?? $sizeClasses['sm']
    ])->implode(' ');
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    <i class="fa-solid {{ $config['icon'] }} mr-1"></i>
    @if($showText)
        {{ $config['text'] }}
    @endif
</span>
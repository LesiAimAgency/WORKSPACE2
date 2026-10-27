@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'imgClass' => '',
    'lazy' => true
])

@php
    $watermark = setting('watermark', []);
    $enabled = $watermark['enabled'] ?? false;
    $watermarkImage = $watermark['image'] ?? '';
    $position = $watermark['position'] ?? 'bottom-right';
    $offsetX = $watermark['offset_x'] ?? 10;
    $offsetY = $watermark['offset_y'] ?? 10;
    $scale = $watermark['scale'] ?? 20;
    $opacity = $watermark['opacity'] ?? 80;
    
    // Position CSS
    $positionStyles = match($position) {
        'top-left' => "top: {$offsetY}px; left: {$offsetX}px;",
        'top-center' => "top: {$offsetY}px; left: 50%; transform: translateX(-50%);",
        'top-right' => "top: {$offsetY}px; right: {$offsetX}px;",
        'center-left' => "top: 50%; left: {$offsetX}px; transform: translateY(-50%);",
        'center' => "top: 50%; left: 50%; transform: translate(-50%, -50%);",
        'center-right' => "top: 50%; right: {$offsetX}px; transform: translateY(-50%);",
        'bottom-left' => "bottom: {$offsetY}px; left: {$offsetX}px;",
        'bottom-center' => "bottom: {$offsetY}px; left: 50%; transform: translateX(-50%);",
        'bottom-right' => "bottom: {$offsetY}px; right: {$offsetX}px;",
        default => "bottom: {$offsetY}px; right: {$offsetX}px;"
    };
    
    // Build watermark URL
    $watermarkUrl = '';
    if ($watermarkImage) {
        $watermarkUrl = str_starts_with($watermarkImage, 'http') 
            ? $watermarkImage 
            : asset($watermarkImage);
    }
@endphp

@if($enabled && $watermarkUrl)
    <div {{ $attributes->merge(['class' => 'watermark-container relative inline-block ' . $class]) }}
         oncontextmenu="return false;"
         ondragstart="return false;">
        <img src="{{ $src }}" 
             alt="{{ $alt }}" 
             class="{{ $imgClass }}"
             @if($lazy) loading="lazy" @endif
             draggable="false"
             style="pointer-events: none;">
        
        {{-- Watermark overlay --}}
        <img src="{{ $watermarkUrl }}" 
             alt="Watermark" 
             class="watermark-overlay absolute pointer-events-none select-none"
             style="{{ $positionStyles }} width: {{ $scale }}%; opacity: {{ $opacity / 100 }}; max-width: 200px;"
             draggable="false">
        
        {{-- Transparent overlay to prevent right-click on image --}}
        <div class="absolute inset-0 z-10" style="background: transparent;"></div>
    </div>
@else
    <img src="{{ $src }}" 
         alt="{{ $alt }}" 
         {{ $attributes->merge(['class' => $class . ' ' . $imgClass]) }}
         @if($lazy) loading="lazy" @endif>
@endif

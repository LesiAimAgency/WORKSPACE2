@props(['name', 'default' => null, 'class' => '', 'alt' => null, 'style' => '', 'loading' => 'lazy'])
@php
    $resolved = resolve_image($name, $default);
@endphp

<img src="{{ $resolved }}" 
     class="{{ $class }}" 
     alt="{{ $alt }}"
     @if($style) style="{{ $style }}" @endif
     @if($loading) loading="{{ $loading }}" @endif
     {{ $attributes }}>

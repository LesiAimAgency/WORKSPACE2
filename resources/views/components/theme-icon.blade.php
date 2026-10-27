@props(['name', 'default' => null, 'class' => ''])
@php
    $resolved = resolve_icon($name, $default);
    $isFa = \Illuminate\Support\Str::contains($resolved, 'fa-') && !\Illuminate\Support\Str::contains($resolved, '/');
@endphp

@if($isFa)
    <i class="{{ $resolved }} {{ $class }}" {{ $attributes }}></i>
@else
    <img src="{{ filter_var($resolved, FILTER_VALIDATE_URL) ? $resolved : asset($resolved) }}" class="{{ $class }}" alt="{{ $name }}" {{ $attributes }}>
@endif

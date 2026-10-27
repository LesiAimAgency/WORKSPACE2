@props(['name', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'id' => null])

@php
    $id = $id ?? $name;
@endphp

<input 
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $id }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50']) }}
>
@error($name)
    <x-form.error :message="$message" />
@enderror
@props(['name', 'value' => '', 'placeholder' => '', 'required' => false, 'id' => null, 'rows' => 4])

@php
    $id = $id ?? $name;
@endphp

<textarea 
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50']) }}
>{{ old($name, $value) }}</textarea>
@error($name)
    <x-form.error :message="$message" />
@enderror
@props(['name', 'options' => [], 'value' => '', 'required' => false, 'id' => null, 'placeholder' => '-- Chọn --'])

@php
    $id = $id ?? $name;
@endphp

<select 
    name="{{ $name }}"
    id="{{ $id }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    
    @foreach($options as $key => $label)
        <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
    
    {{ $slot }}
</select>
@error($name)
    <x-form.error :message="$message" />
@enderror
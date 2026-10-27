@props(['name', 'value' => ''])

<input id="trix-{{ $name }}" type="hidden" name="{{ $name }}" value="{{ $value }}">
<trix-editor input="trix-{{ $name }}" class="trix-content"></trix-editor>

@once
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
<style>
trix-toolbar .trix-button-group{border-radius:6px;overflow:hidden}
trix-toolbar .trix-button--icon-preview{display:none}
trix-editor{border-radius:8px;min-height:300px;padding:1rem}
.trix-content{border:1px solid #e5e7eb}
.trix-content:focus{outline:2px solid #3b82f6;outline-offset:2px}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush
@endonce

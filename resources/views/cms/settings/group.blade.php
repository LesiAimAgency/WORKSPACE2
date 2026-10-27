@extends('cms.settings.template', ['title' => 'Cấu hình ' . ucfirst($group ?? 'hệ thống')])

@section('form-content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg border">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cấu hình {{ ucfirst($group ?? '') }}</h3>
        <p class="text-sm text-gray-600 mb-6">Quản lý các cài đặt cho nhóm {{ $group ?? '' }}.</p>

        @if(!empty($settingsMap))
            <div class="space-y-4">
                @foreach($settingsMap as $key => $val)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ str_replace('_', ' ', ucfirst($key)) }}</label>
                        <input type="text" name="settings[{{ $key }}]" value="{{ is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                Chưa có cấu hình riêng cho nhóm này.
            </div>
        @endif
    </div>
</div>
@endsection

@extends('cms.layouts.app')

@section('title', 'Thống kê truy cập')
@section('page-title', 'Social & Traffic')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = \Route::has('admin.settings.save')
        ? route('admin.settings.save')
        : (\Route::has('cms.settings.save') ? route('cms.settings.save') : url('/admin/settings'));
@endphp

<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Thống kê truy cập (Số liệu ảo)</h3>
    <p class="text-sm text-gray-600 mb-6">Cấu hình số liệu hiển thị để tăng uy tín website</p>

    <form action="{{ $settingsSaveUrl }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Online -->
            <div>
                <label class="block text-sm font-medium mb-2">Đang online</label>
                <input type="number" name="stats_online" value="{{ setting('stats_online', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Số người đang truy cập</p>
            </div>

            <!-- Hôm nay -->
            <div>
                <label class="block text-sm font-medium mb-2">Hôm nay</label>
                <input type="number" name="stats_today" value="{{ setting('stats_today', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Lượt truy cập hôm nay</p>
            </div>

            <!-- 3 ngày -->
            <div>
                <label class="block text-sm font-medium mb-2">3 ngày qua</label>
                <input type="number" name="stats_3days" value="{{ setting('stats_3days', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Lượt truy cập 3 ngày</p>
            </div>

            <!-- 7 ngày -->
            <div>
                <label class="block text-sm font-medium mb-2">7 ngày qua</label>
                <input type="number" name="stats_7days" value="{{ setting('stats_7days', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Lượt truy cập 7 ngày</p>
            </div>

            <!-- Tháng -->
            <div>
                <label class="block text-sm font-medium mb-2">Tháng này</label>
                <input type="number" name="stats_month" value="{{ setting('stats_month', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Lượt truy cập tháng</p>
            </div>

            <!-- Năm -->
            <div>
                <label class="block text-sm font-medium mb-2">Năm nay</label>
                <input type="number" name="stats_year" value="{{ setting('stats_year', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Lượt truy cập năm</p>
            </div>

            <!-- Tổng -->
            <div>
                <label class="block text-sm font-medium mb-2">Tổng truy cập</label>
                <input type="number" name="stats_total" value="{{ setting('stats_total', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Tổng lượt truy cập</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
    <p class="text-sm text-yellow-800">
        <strong>Lưu ý:</strong> Đây là số liệu ảo chỉ mang tính tham khảo, không phản ánh thực tế. 
        Để có số liệu chính xác, hãy tích hợp Google Analytics.
    </p>
</div>
@endsection

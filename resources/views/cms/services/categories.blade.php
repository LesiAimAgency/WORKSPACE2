@extends('cms.layouts.app')

@section('title', 'Quản lý Danh mục Dịch Vụ (Coverflow) - Dịch Vụ')
@section('page-title', 'Danh mục Dịch Vụ (Coverflow)')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Top Header & Breadcrumbs (Identical to Material Lab Categories) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.services.index') }}" class="hover:text-rose-600 transition-colors">Dịch Vụ (Services)</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Danh mục Dịch vụ (Màn hình Coverflow)</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Quản lý Danh mục Dịch vụ (Màn hình Coverflow)</h2>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý 5 danh mục hiển thị trên thanh điều hướng &amp; vòng xoay Coverflow Trang chủ (Số TT, Tên danh mục, Chip 2 dòng, Ảnh đại diện &amp; Mô tả ngắn).</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.projects.index') }}" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Quản lý Dự Án
            </a>
            <form action="{{ route('admin.services.save-categories') }}" method="POST" onsubmit="return confirm('Khôi phục lại 5 danh mục dịch vụ tiêu chuẩn?');">
                @csrf
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Khôi phục 5 danh mục chuẩn
                </button>
            </form>
            <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Tất cả dịch vụ
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- Cột trái: Form Thêm Danh mục Coverflow mới -->
        <div class="md:col-span-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                Thêm danh mục Coverflow mới
            </h3>

            <form action="{{ route('admin.services.save-categories') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh đại diện Coverflow</label>
                    @include('cms.components.media-picker', [
                        'name' => 'image',
                        'value' => 'themes/workspace/coverflow/frame1.png',
                        'label' => 'Chọn ảnh Coverflow'
                    ])
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên danh mục dịch vụ <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="VD: Fit-Out and Finishing Works" class="w-full px-3 py-2 text-xs font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chip Dòng 1</label>
                        <input type="text" name="chip_line_1" placeholder="VD: Fit-Out and" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chip Dòng 2</label>
                        <input type="text" name="chip_line_2" placeholder="VD: Finishing Works" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả ngắn dưới ảnh Coverflow</label>
                    <textarea name="desc" rows="3" placeholder="Turnkey fit-out construction, advanced MEP engineering, and rigorous site management..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                    + Thêm danh mục
                </button>
            </form>
        </div>

        <!-- Cột phải: Danh sách 5 Danh mục Coverflow hiện có -->
        <div class="md:col-span-8 bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                    Danh sách Danh mục trên Coverflow ({{ count($categories) }})
                </h3>
                <span class="text-[11px] text-gray-500">Nhấp "Sửa nhanh" hoặc "Chi tiết &amp; Dự án"</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($categories as $idx => $cat)
                    @php
                        $cName = $cat['name'] ?? '';
                        $cLine1 = $cat['chip_line_1'] ?? '';
                        $cLine2 = $cat['chip_line_2'] ?? '';
                        $svcItem = $serviceByCat[$cName] ?? null;
                        $svcDesc = $svcItem['desc'] ?? '';
                        $svcImg = $svcItem['image'] ?? 'themes/workspace/coverflow/frame1.png';
                        $imgUrl = str_starts_with($svcImg, 'http') ? $svcImg : asset(ltrim($svcImg, '/'));
                        $introCount = count($svcItem['intro_items'] ?? []);
                        $projCount = count($svcItem['projects'] ?? []);
                    @endphp
                    <div x-data="{ editing: false, newName: @js($cName), line1: @js($cLine1), line2: @js($cLine2), desc: @js($svcDesc), image: @js($svcImg) }" class="p-5 hover:bg-gray-50/70 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4" x-show="!editing">
                            <div class="flex items-start gap-3.5 min-w-0">
                                <!-- Red Box Number + Thumbnail -->
                                <div class="relative w-20 h-16 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 flex-shrink-0">
                                    <img src="{{ $imgUrl }}" alt="{{ $cName }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 left-0 px-1.5 py-0.5 bg-[#9E0B0F] text-white font-bold text-[11px] leading-none">
                                        {{ sprintf('%02d', $idx + 1) }}
                                    </span>
                                </div>

                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-900">{{ $cName }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-2 flex-wrap">
                                        <span>Chip Top: <strong class="text-gray-700">{{ $cLine1 }}</strong> / <strong class="text-gray-700">{{ $cLine2 }}</strong></span>
                                        <span>•</span>
                                        <span class="text-rose-600 font-semibold">{{ $introCount }} mục chi tiết</span>
                                        <span>•</span>
                                        <span class="text-emerald-600 font-semibold">{{ $projCount }} dự án</span>
                                    </div>
                                    @if($svcDesc)
                                        <p class="text-xs text-gray-600 mt-1.5 line-clamp-2 leading-relaxed">
                                            {{ $svcDesc }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                @if($svcItem)
                                    <a href="{{ route('admin.services.edit', $svcItem['id']) }}" class="px-2.5 py-1.5 text-[11px] font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors">
                                        Chi tiết &amp; Dự án
                                    </a>
                                @endif
                                <button type="button" @click="editing = true" class="px-2.5 py-1.5 text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                    Sửa nhanh
                                </button>
                                <form action="{{ route('admin.services.save-categories') }}" method="POST" onsubmit="return confirm('Xóa danh mục này khỏi danh sách?');" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="name" value="{{ $cName }}">
                                    <button type="submit" class="px-2.5 py-1.5 text-[11px] font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-md transition-colors">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Form sửa nhanh thông tin Danh mục Coverflow -->
                        <form x-show="editing" x-cloak action="{{ route('admin.services.save-categories') }}" method="POST" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            @csrf
                            <input type="hidden" name="action" value="rename">
                            <input type="hidden" name="old_name" value="{{ $cName }}">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Tên danh mục (Hiển thị trong Box Đỏ &amp; Tiêu đề)</label>
                                    <input type="text" name="new_name" x-model="newName" required class="w-full px-3 py-1.5 text-xs font-bold border border-rose-400 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Chip Dòng 1</label>
                                    <input type="text" name="chip_line_1" x-model="line1" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Chip Dòng 2</label>
                                    <input type="text" name="chip_line_2" x-model="line2" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Đường dẫn Ảnh Coverflow</label>
                                    <input type="text" name="image" x-model="image" class="w-full px-2.5 py-1.5 text-xs font-mono border border-gray-300 rounded-lg bg-white">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Mô tả ngắn dưới ảnh Coverflow</label>
                                    <textarea name="desc" rows="2" x-model="desc" class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-white"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button" @click="editing = false" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-600 text-xs font-semibold rounded-lg">
                                    Hủy
                                </button>
                                <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg">
                                    Lưu &amp; Đồng bộ Coverflow
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">Chưa có phân loại danh mục nào.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

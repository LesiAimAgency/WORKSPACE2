@extends('cms.layouts.app')

@section('title', 'Cấu hình Material Lab')
@section('page-title', 'Cấu hình Material Lab')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Cấu hình tiêu đề &amp; Ma trận gợi ý</h2>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý tiêu đề hiển thị frontend, lời dẫn các bước wizard và kiểm tra ma trận gợi ý vật liệu theo loại hình &amp; bề mặt.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('material-lab') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.material-lab.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.material-lab.save-config') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Frontend Title & Subtitle -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                1. Tiêu đề &amp; Định vị trang Frontend
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề chính (Title)</label>
                    <input type="text" name="material_lab_title" value="{{ old('material_lab_title', $title) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Xuất hiện trên thanh Header navigation của Material Lab.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phụ đề (Subtitle / Version)</label>
                    <input type="text" name="material_lab_subtitle" value="{{ old('material_lab_subtitle', $subtitle) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Xuất hiện dưới logo tiêu đề hoặc breadcrumb.</p>
                </div>
            </div>
        </div>

        <!-- Wizard Steps Titles & Descriptions -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                2. Lời dẫn &amp; Tiêu đề từng bước trong Wizard
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Step 1 -->
                <div class="p-3.5 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                    <div class="text-xs font-bold text-gray-800">Bước 1: Loại hình công trình</div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Tiêu đề câu hỏi</label>
                        <input type="text" name="material_lab_step1_title" value="{{ old('material_lab_step1_title', $step1Title) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Mô tả hướng dẫn</label>
                        <input type="text" name="material_lab_step1_desc" value="{{ old('material_lab_step1_desc', $step1Desc) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="p-3.5 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                    <div class="text-xs font-bold text-gray-800">Bước 2: Bề mặt / Vị trí</div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Tiêu đề câu hỏi</label>
                        <input type="text" name="material_lab_step2_title" value="{{ old('material_lab_step2_title', $step2Title) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Mô tả hướng dẫn</label>
                        <input type="text" name="material_lab_step2_desc" value="{{ old('material_lab_step2_desc', $step2Desc) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="p-3.5 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                    <div class="text-xs font-bold text-gray-800">Bước 3: Khu vực chức năng</div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Tiêu đề câu hỏi</label>
                        <input type="text" name="material_lab_step3_title" value="{{ old('material_lab_step3_title', $step3Title) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                    <p class="text-[10px] text-gray-400">Khu vực hiển thị động phụ thuộc vào kết hợp Loại hình và Bề mặt đã chọn.</p>
                </div>

                <!-- Step 4 -->
                <div class="p-3.5 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                    <div class="text-xs font-bold text-gray-800">Bước 4: Nhu cầu thực tế</div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Tiêu đề câu hỏi</label>
                        <input type="text" name="material_lab_step4_title" value="{{ old('material_lab_step4_title', $step4Title) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Mô tả hướng dẫn</label>
                        <input type="text" name="material_lab_step4_desc" value="{{ old('material_lab_step4_desc', $step4Desc) }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Lưu toàn bộ cấu hình
                </button>
            </div>
        </div>
    </form>

    <!-- Inspection of Wizard Matrix & Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Typologies -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Các loại hình không gian (Typologies)
            </h3>
            <p class="text-[11px] text-gray-500">Các tùy chọn tại Bước 1 (Loại hình công trình) trong wizard:</p>
            <div class="space-y-2">
                @foreach($typologies as $t)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-start justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-800">
                                <span class="mr-1">{{ $t['icon'] ?? '🏢' }}</span>
                                {{ $t['vi'] ?? ($t['name'] ?? '') }}
                            </div>
                            <div class="text-[11px] text-gray-500 mt-0.5">{{ $t['desc'] ?? '' }}</div>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-mono rounded">
                            {{ $t['en'] ?? ($t['id'] ?? '') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Surfaces -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Các bề mặt phân loại (Surfaces)
            </h3>
            <p class="text-[11px] text-gray-500">Các tùy chọn tại Bước 2 (Bề mặt hoàn thiện) trong wizard:</p>
            <div class="space-y-2">
                @foreach($surfaces as $s)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                        <div class="text-xs font-semibold text-gray-800">{{ $s['vi'] ?? '' }}</div>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono rounded">
                            {{ $s['en'] ?? '' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

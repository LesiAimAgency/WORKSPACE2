@extends('cms.layouts.app')

@section('title', 'Cấu hình Dịch Vụ (Services)')
@section('page-title', 'Cấu hình Dịch Vụ')

@section('content')
@php
    $initialSpecFields = old('spec_fields_json')
        ? (json_decode(old('spec_fields_json'), true) ?: ($specFields ?? []))
        : ($specFields ?? \App\Http\Controllers\Admin\ServiceController::getSpecFields());
@endphp

<div class="max-w-6xl mx-auto space-y-6" x-data="serviceConfigApp(@js($initialSpecFields))">
    <!-- Header & Breadcrumbs (Identical to Material Lab Config) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.services.index') }}" class="hover:text-rose-600 transition-colors">Dịch Vụ (Services)</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Cấu hình hiển thị</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Cấu hình Hiển thị &amp; Nhãn Dịch Vụ (Frontend)</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Quản lý toàn bộ nhãn nút bấm, tiêu đề tab (Introductions / Projects) và bộ nhãn thông số kỹ thuật (Repeater) hiển thị ngoài
                <a href="{{ url('/#coverflow') }}" target="_blank" class="text-rose-600 font-medium hover:underline">Trang chủ (#coverflow) &nearr;</a>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.index') }}" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Quản lý Dự Án
            </a>
            <a href="{{ url('/#coverflow') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Danh sách dịch vụ
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.services.save-config') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="spec_fields_json" :value="JSON.stringify(specFields)">

        <!-- PHẦN 1: MÀN HÌNH COVERFLOW TRANG CHỦ (#coverflow) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] flex items-center justify-center font-bold">1</span>
                    Màn hình Trình diễn Coverflow (#coverflow)
                </h3>
                <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                    Lưu cấu hình
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nút xem chi tiết trên Coverflow (View More Button)</label>
                    <input type="text" name="view_more_text" value="{{ old('view_more_text', $labels['view_more_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Hiển thị dưới phần mô tả ngắn của từng dịch vụ trên vòng xoay Coverflow (Mặc định: <code>View more</code>).</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nhãn Pill đầu trang Chi tiết Dịch vụ (Service Pill)</label>
                    <input type="text" name="pill_text" value="{{ old('pill_text', $labels['pill_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Nút bo tròn viền đỏ phía trên tiêu đề lớn ở màn hình Chi tiết Dịch vụ (Mặc định: <code>Services</code>).</p>
                </div>
            </div>
        </div>

        <!-- PHẦN 2: ĐIỀU HƯỚNG MÀN HÌNH CHI TIẾT DỊCH VỤ (#coverflowService) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] flex items-center justify-center font-bold">2</span>
                    Điều hướng &amp; Nút bấm Màn hình Chi tiết Dịch vụ (#coverflowService)
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên Tab 1: Giới thiệu chi tiết (Introductions Tab)</label>
                    <input type="text" name="tab_intro_text" value="{{ old('tab_intro_text', $labels['tab_intro_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên Tab 2: Danh sách dự án (Projects Tab)</label>
                    <input type="text" name="tab_project_text" value="{{ old('tab_project_text', $labels['tab_project_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nút quay lại danh sách Dịch vụ (Back Button)</label>
                    <input type="text" name="back_text" value="{{ old('back_text', $labels['back_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nút bật chế độ So sánh Thiết kế &amp; Thực tế (Comparison Button)</label>
                    <input type="text" name="compare_button_text" value="{{ old('compare_button_text', $labels['compare_button_text']) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
        </div>

        <!-- PHẦN 3: NHÃN THÔNG SỐ KỸ THUẬT DỰ ÁN (TAB PROJECTS - REPEATER) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b pb-3">
                <div>
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">3</span>
                        Nhãn Thông số Kỹ thuật Dự án (Tab PROJECTS)
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full" x-text="specFields.length + ' nhãn thông số'"></span>
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Thiết lập danh sách các nhãn thông số mặc định hiển thị tại cột trái màn hình chi tiết dự án (Tab <strong>PROJECTS</strong>) và tự động tạo các ô nhập liệu tương ứng trong trang <strong>Quản lý Dự Án</strong>. Bạn có thể thêm mới, sắp xếp hoặc xóa nhãn tùy ý.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="resetDefaultSpecs()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                        Khôi phục 5 nhãn chuẩn
                    </button>
                    <button type="button" @click="addSpecField()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Thêm nhãn thông số
                    </button>
                </div>
            </div>

            <!-- Danh sách Repeater các nhãn thông số -->
            <div class="space-y-3">
                <template x-for="(field, idx) in specFields" :key="idx">
                    <div class="p-3.5 bg-gray-50/80 hover:bg-gray-50 rounded-xl border border-gray-200/90 transition-all">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <!-- STT -->
                            <div class="md:col-span-1 flex items-center gap-2 md:pb-1.5">
                                <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-mono text-xs font-bold flex items-center justify-center shadow-sm" x-text="String(idx + 1).padStart(2, '0')"></span>
                                <span class="md:hidden text-xs font-bold text-gray-700">Thông số #<span x-text="idx + 1"></span></span>
                            </div>

                            <!-- Nhãn hiển thị (Label) -->
                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                    Tên nhãn hiển thị (Label) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       x-model="field.label"
                                       @input="if (!field.key || field.key.startsWith('spec_')) field.key = slugify(field.label, idx)"
                                       required
                                       placeholder="VD: Total Area, Location, Year, Client..."
                                       class="w-full px-3 py-2 text-xs font-bold text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <!-- Mã trường (Key) -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                    Mã trường (Key)
                                </label>
                                <input type="text"
                                       x-model="field.key"
                                       placeholder="area, location..."
                                       class="w-full px-2.5 py-2 text-xs font-mono text-gray-600 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <!-- Gợi ý nhập liệu (Placeholder) -->
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                    Gợi ý nhập (Placeholder)
                                </label>
                                <input type="text"
                                       x-model="field.placeholder"
                                       placeholder="VD: 1,250 m2"
                                       class="w-full px-3 py-2 text-xs text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <!-- Nút điều khiển Sắp xếp / Xóa -->
                            <div class="md:col-span-2 flex items-center justify-end gap-1.5">
                                <button type="button" @click="moveUp(idx)" :disabled="idx === 0" class="p-2 text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg disabled:opacity-30 transition-colors" title="Di chuyển lên">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button type="button" @click="moveDown(idx)" :disabled="idx === specFields.length - 1" class="p-2 text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg disabled:opacity-30 transition-colors" title="Di chuyển xuống">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <button type="button" @click="removeSpecField(idx)" class="p-2 text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 rounded-lg transition-colors" title="Xóa nhãn thông số">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Nút thêm nhanh bên dưới -->
            <div class="pt-1 flex items-center justify-between flex-wrap gap-3">
                <button type="button" @click="addSpecField()" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 border-dashed text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Thêm nhãn thông số kỹ thuật mới
                </button>

                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Lưu toàn bộ cấu hình &amp; Đồng bộ Dự án
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function serviceConfigApp(initialSpecs) {
    const defaultSpecs = [
        { key: 'area', label: 'Total Area', placeholder: 'VD: 1,250 m2', default_value: '850 m2' },
        { key: 'location', label: 'Location', placeholder: 'VD: District 1, Ho Chi Minh City', default_value: 'Ho Chi Minh City' },
        { key: 'typology', label: 'Typology', placeholder: 'VD: Technology Workspace', default_value: 'Commercial Office' },
        { key: 'style', label: 'Style', placeholder: 'VD: Contemporary Minimalist', default_value: 'Modern Minimalist' },
        { key: 'tech', label: 'Tech Solution', placeholder: 'VD: Acoustic Baffles & Integrated MEP', default_value: 'Smart Lighting & Acoustic Control' }
    ];

    return {
        specFields: Array.isArray(initialSpecs) && initialSpecs.length
            ? JSON.parse(JSON.stringify(initialSpecs))
            : JSON.parse(JSON.stringify(defaultSpecs)),

        slugify(str, idx) {
            const s = String(str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
            return s || ('spec_' + (idx + 1));
        },

        addSpecField() {
            const nextNum = this.specFields.length + 1;
            this.specFields.push({
                key: 'spec_' + nextNum,
                label: 'New Specification ' + String(nextNum).padStart(2, '0'),
                placeholder: 'VD: Nhập giá trị thông số...',
                default_value: ''
            });
        },

        removeSpecField(idx) {
            if (this.specFields.length <= 1) {
                alert('Cần giữ lại ít nhất 1 nhãn thông số kỹ thuật.');
                return;
            }
            this.specFields.splice(idx, 1);
        },

        moveUp(idx) {
            if (idx <= 0) return;
            const tmp = this.specFields[idx - 1];
            this.specFields[idx - 1] = this.specFields[idx];
            this.specFields[idx] = tmp;
        },

        moveDown(idx) {
            if (idx >= this.specFields.length - 1) return;
            const tmp = this.specFields[idx + 1];
            this.specFields[idx + 1] = this.specFields[idx];
            this.specFields[idx] = tmp;
        },

        resetDefaultSpecs() {
            if (confirm('Khôi phục về 5 nhãn thông số mặc định (Total Area, Location, Typology, Style, Tech Solution)?')) {
                this.specFields = JSON.parse(JSON.stringify(defaultSpecs));
            }
        }
    };
}
</script>
@endsection

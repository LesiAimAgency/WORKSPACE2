@extends('cms.layouts.app')

@section('title', 'Thêm mới dự án - Quản lý Dự Án (Projects)')
@section('page-title', 'Thêm mới dự án')

@section('content')
@php
    $defaultSpecs = [];
    foreach (($specFields ?? \App\Http\Controllers\Admin\ServiceController::getSpecFields()) as $sf) {
        $defaultSpecs[] = [
            'key' => $sf['key'],
            'label' => $sf['label'],
            'value' => $sf['default_value'] ?? '',
            'placeholder' => $sf['placeholder'] ?? 'Nhập giá trị thông số...',
        ];
    }
    $initialSpecs = old('specs_json')
        ? (json_decode(old('specs_json'), true) ?: $defaultSpecs)
        : $defaultSpecs;
@endphp

<div class="space-y-6" x-data="projectSpecsApp(@js($initialSpecs))">
    <!-- Top Header & Breadcrumbs (Identical to Material Lab Create) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.projects.index') }}" class="hover:text-rose-600 transition-colors">Quản lý Dự Án</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Thêm mới</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Thêm dự án tiêu biểu mới</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Quản lý Dịch Vụ
            </a>
            <a href="{{ url('/#coverflow') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.projects.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc pl-5 space-y-0.5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.projects.store') }}" method="POST">
        @csrf
        <input type="hidden" name="specs_json" :value="JSON.stringify(specs)">

        <!-- Bố cục 2 cột tỷ lệ 4/8 giống hệt Material Lab -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ============================================== -->
            <!-- CỘT TRÁI: 4/12 (Sticky) - Nhóm 1: Thông tin chung -->
            <!-- ============================================== -->
            <div class="lg:col-span-4 lg:sticky lg:top-6 self-start space-y-5">
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        1. Thông tin chung &amp; Liên kết Dịch vụ
                    </h3>

                    <!-- 1. Ảnh bìa dự án -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">1. Ảnh bìa dự án (Cover Image)</label>
                        @include('cms.components.media-picker', [
                            'name' => 'cover_image',
                            'value' => old('cover_image', 'themes/workspace/coverflow/frame1.png'),
                            'label' => 'Chọn ảnh bìa'
                        ])
                        <p class="text-[10px] text-gray-400 mt-1">Ảnh đại diện chính khi xem dự án tại Tab PROJECTS.</p>
                    </div>

                    <!-- Tên dự án & Mã -->
                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tên dự án <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="VD: TECH HUB OFFICE" class="w-full px-3 py-2 text-xs font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Mã dự án</label>
                            <input type="text" name="code" value="{{ old('code', $nextCode) }}" class="w-full px-3 py-2 text-xs font-mono font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <!-- Dịch vụ liên kết (Relationship) -->
                    @php
                        $oldSlugs = (array) old('service_slugs', $preselectedService ? [$preselectedService] : [$services[0]['slug'] ?? '']);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700">
                                Thuộc Dịch Vụ (Relationship) <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('admin.services.index') }}" class="text-[11px] text-indigo-600 hover:text-indigo-700 hover:underline font-medium">
                                Quản lý Dịch vụ &rarr;
                            </a>
                        </div>
                        <div class="space-y-1.5 max-h-56 overflow-y-auto p-2.5 bg-gray-50 rounded-xl border border-gray-200">
                            @foreach($services as $svc)
                                <label class="flex items-center gap-2.5 p-2 bg-white hover:bg-rose-50/40 rounded-lg border border-gray-200/80 cursor-pointer transition-colors">
                                    <input type="checkbox"
                                           name="service_slugs[]"
                                           value="{{ $svc['slug'] }}"
                                           {{ in_array($svc['slug'], $oldSlugs, true) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-gray-800 truncate">{{ $svc['number'] }} — {{ $svc['title'] }}</div>
                                        <div class="text-[10px] text-gray-400 truncate">{{ $svc['category'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Một dự án có thể hiển thị ở 1 hoặc nhiều Dịch vụ cùng lúc.</p>
                    </div>

                    <!-- Active Toggle -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 bg-gray-50 hover:bg-gray-100/80 rounded-lg border border-gray-200 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="text-xs font-semibold text-gray-800 block">Kích hoạt hiển thị</span>
                                <span class="text-[11px] text-gray-500">Hiển thị dự án tại Tab PROJECTS của Dịch vụ</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Bottom Action Button -->
                <div class="flex items-center justify-end pt-1">
                    <button type="submit" class="w-full py-2.5 px-6 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Lưu dự án mới
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CỘT PHẢI: 8/12 (Form cuộn tự nhiên)            -->
            <!-- ============================================== -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Nhóm 2: Bộ thông số Kỹ thuật Dự án (REPEATER) -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b pb-3">
                        <div>
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                2. Thông số Kỹ thuật Dự án (Hiển thị tại Tab PROJECTS)
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 rounded-full" x-text="specs.length + ' thông số'"></span>
                            </h3>
                            <p class="text-[11px] text-gray-500 mt-1">
                                Bộ thông số hiển thị bên trái màn hình chi tiết dự án khi khách hàng chuyển sang tab <strong>PROJECTS</strong>. Có thể thêm mới (repeat), sắp xếp hoặc chỉnh sửa từng dòng.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.services.config') }}" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-semibold rounded-lg transition-colors">
                                Cấu hình nhãn mặc định
                            </a>
                            <button type="button" @click="addSpec()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                + Thêm thông số
                            </button>
                        </div>
                    </div>

                    <!-- Danh sách Repeater Thông số Kỹ thuật -->
                    <div class="space-y-2.5">
                        <template x-for="(spec, idx) in specs" :key="idx">
                            <div class="p-3 bg-gray-50/80 hover:bg-gray-50 rounded-xl border border-gray-200/90 transition-all">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                                    <!-- STT -->
                                    <div class="sm:col-span-1 flex items-center gap-2 sm:pb-1.5">
                                        <span class="w-6 h-6 rounded-md bg-blue-600 text-white font-mono text-[11px] font-bold flex items-center justify-center" x-text="String(idx + 1).padStart(2, '0')"></span>
                                        <span class="sm:hidden text-xs font-bold text-gray-700">Thông số #<span x-text="idx + 1"></span></span>
                                    </div>

                                    <!-- Nhãn thông số (Label) -->
                                    <div class="sm:col-span-4">
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tên nhãn (Label)</label>
                                        <input type="text"
                                               x-model="spec.label"
                                               @input="if (!spec.key || spec.key.startsWith('spec_')) spec.key = slugify(spec.label, idx)"
                                               placeholder="VD: Total Area, Location..."
                                               class="w-full px-3 py-2 text-xs font-bold text-gray-800 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Giá trị thông số (Value) -->
                                    <div class="sm:col-span-5">
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Giá trị hiển thị (Value)</label>
                                        <input type="text"
                                               x-model="spec.value"
                                               :placeholder="spec.placeholder || 'Nhập giá trị thông số...'"
                                               class="w-full px-3 py-2 text-xs text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                                    </div>

                                    <!-- Sắp xếp & Xóa -->
                                    <div class="sm:col-span-2 flex items-center justify-end gap-1">
                                        <button type="button" @click="moveUp(idx)" :disabled="idx === 0" class="p-2 text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg disabled:opacity-30 transition-colors" title="Lên">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button type="button" @click="moveDown(idx)" :disabled="idx === specs.length - 1" class="p-2 text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg disabled:opacity-30 transition-colors" title="Xuống">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button type="button" @click="removeSpec(idx)" class="p-2 text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 rounded-lg transition-colors" title="Xóa thông số">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-1">
                        <button type="button" @click="addSpec()" class="w-full py-2 px-4 bg-blue-50/70 hover:bg-blue-100/80 text-blue-700 border border-blue-200 border-dashed text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Thêm thông số kỹ thuật mới (Repeat)
                        </button>
                    </div>
                </div>

                <!-- Nhóm 3: Hình ảnh So sánh Thiết kế 3D & Thực tế (Before / After Slider) -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="border-b pb-3">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            3. Hình ảnh So sánh Thiết kế 3D &amp; Thực tế Hoàn thiện (Comparison Design &amp; Result)
                        </h3>
                        <p class="text-[11px] text-gray-500 mt-1">
                            Hai hình ảnh dùng cho thanh trượt kéo so sánh trực tiếp khi khách hàng nhấn nút <strong>"{{ $labels['compare_button_text'] ?? 'Comparison Design and Result' }}"</strong>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-blue-50/40 rounded-xl border border-blue-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-blue-900 uppercase">2. Ảnh Thiết kế 3D (Design - Before)</span>
                                <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-[10px]">Bên trái</span>
                            </div>
                            @include('cms.components.media-picker', [
                                'name' => 'design_image',
                                'value' => old('design_image', 'themes/workspace/workflow/design.png'),
                                'label' => 'Chọn ảnh Thiết kế 3D'
                            ])
                        </div>

                        <div class="p-4 bg-emerald-50/40 rounded-xl border border-emerald-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-900 uppercase">3. Ảnh Thực tế (Result - After)</span>
                                <span class="px-2 py-0.5 bg-emerald-600 text-white rounded text-[10px]">Bên phải</span>
                            </div>
                            @include('cms.components.media-picker', [
                                'name' => 'result_image',
                                'value' => old('result_image', 'themes/workspace/workflow/build.png'),
                                'label' => 'Chọn ảnh Thực tế hoàn thiện'
                            ])
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
function projectSpecsApp(initialSpecs) {
    return {
        specs: Array.isArray(initialSpecs) && initialSpecs.length
            ? JSON.parse(JSON.stringify(initialSpecs))
            : [
                { key: 'area', label: 'Total Area', value: '850 m2', placeholder: 'VD: 1,250 m2' },
                { key: 'location', label: 'Location', value: 'Ho Chi Minh City', placeholder: 'VD: District 1, Ho Chi Minh City' },
                { key: 'typology', label: 'Typology', value: 'Commercial Office', placeholder: 'VD: Technology Workspace' },
                { key: 'style', label: 'Style', value: 'Modern Minimalist', placeholder: 'VD: Contemporary Minimalist' },
                { key: 'tech', label: 'Tech Solution', value: 'Smart Lighting & Acoustic Control', placeholder: 'VD: Acoustic Baffles & Integrated MEP' }
            ],

        slugify(str, idx) {
            const s = String(str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
            return s || ('spec_' + (idx + 1));
        },

        addSpec() {
            const nextNum = this.specs.length + 1;
            this.specs.push({
                key: 'spec_' + nextNum,
                label: '',
                value: '',
                placeholder: 'Nhập giá trị thông số...'
            });
        },

        removeSpec(idx) {
            if (this.specs.length <= 1) {
                alert('Cần giữ lại ít nhất 1 dòng thông số kỹ thuật.');
                return;
            }
            this.specs.splice(idx, 1);
        },

        moveUp(idx) {
            if (idx <= 0) return;
            const tmp = this.specs[idx - 1];
            this.specs[idx - 1] = this.specs[idx];
            this.specs[idx] = tmp;
        },

        moveDown(idx) {
            if (idx >= this.specs.length - 1) return;
            const tmp = this.specs[idx + 1];
            this.specs[idx + 1] = this.specs[idx];
            this.specs[idx] = tmp;
        }
    };
}
</script>
@endsection

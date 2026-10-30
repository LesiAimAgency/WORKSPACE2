@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa dịch vụ - Dịch Vụ (Services)')
@section('page-title', 'Chỉnh sửa dịch vụ')

@section('content')
<div class="space-y-6" x-data="serviceEditApp()">
    <!-- Top Header & Breadcrumbs (Identical to Material Lab Edit) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.services.index') }}" class="hover:text-rose-600 transition-colors">Dịch Vụ (Services)</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Chỉnh sửa</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 flex-wrap">
                <span>{{ $service['title'] }}</span>
                <span class="text-xs px-2 py-0.5 rounded font-mono font-semibold {{ !empty($service['is_active']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $service['code'] }}
                </span>
            </h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.projects.index', ['service' => $service['slug']]) }}" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Quản lý Dự Án
            </a>
            <a href="{{ url('/#coverflow') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
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

    <form action="{{ route('admin.services.update', $service['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="intro_items_json" :value="JSON.stringify(introItems)">

        <!-- Bố cục 2 cột tỷ lệ 4/8 giống hệt Material Lab Edit -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ============================================== -->
            <!-- CỘT TRÁI: 4/12 (Sticky) - Nhóm 1: Danh mục Dịch vụ (Màn hình Coverflow) -->
            <!-- ============================================== -->
            <div class="lg:col-span-4 lg:sticky lg:top-6 self-start space-y-5">
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            1. Danh mục Dịch vụ (Coverflow)
                        </h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-gray-100 text-gray-600 rounded">
                            {{ $service['code'] }}
                        </span>
                    </div>

                    <!-- Hình ảnh đại diện Coverflow -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh đại diện Coverflow &amp; Chi tiết</label>
                        @include('cms.components.media-picker', [
                            'name' => 'image',
                            'value' => old('image', $service['image'] ?? ''),
                            'label' => 'Chọn ảnh đại diện'
                        ])
                        <p class="text-[10px] text-gray-400 mt-1">Ảnh hiển thị trên slide Coverflow và bên phải màn hình Chi tiết Dịch vụ.</p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề Danh mục / Dịch vụ <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="title" @input="autoSlug()" required placeholder="VD: Fit-Out and Finishing Works" class="w-full px-3 py-2 text-xs font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <!-- Number & Slug -->
                    <div class="grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Số TT (Box Đỏ)</label>
                            <input type="text" name="number" value="{{ old('number', $service['number']) }}" class="w-full px-3 py-2 text-xs font-mono font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Slug định danh</label>
                            <input type="text" name="slug" x-model="slug" class="w-full px-3 py-2 text-xs font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <!-- Chip Line 1 & 2 -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Chip Dòng 1 (Top Nav)</label>
                            <input type="text" name="chip_line_1" x-model="chipLine1" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Chip Dòng 2 (Top Nav)</label>
                            <input type="text" name="chip_line_2" x-model="chipLine2" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <!-- Short Description on Coverflow -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả ngắn dưới ảnh Coverflow</label>
                        <textarea name="desc" rows="3" placeholder="Turnkey fit-out construction, advanced MEP engineering, and rigorous site management..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">{{ old('desc', $service['desc'] ?? '') }}</textarea>
                    </div>

                    <!-- Active Toggle -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 bg-gray-50 hover:bg-gray-100/80 rounded-lg border border-gray-200 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service['is_active'] ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="text-xs font-semibold text-gray-800 block">Kích hoạt hiển thị</span>
                                <span class="text-[11px] text-gray-500">Hiển thị trên Coverflow Trang chủ</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="if(confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')) document.getElementById('delete-service-form').submit();" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Xóa dịch vụ
                    </button>

                    <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Cập nhật thay đổi
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CỘT PHẢI: 8/12 (Form cuộn tự nhiên)            -->
            <!-- ============================================== -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Nhóm 2: Các Dịch vụ Chi tiết (Màn hình Chi tiết - Chữ Đỏ) -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                        <div>
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                                2. Thông tin Dịch vụ Chi tiết (Tab INTRODUCTIONS)
                            </h3>
                            <p class="text-[11px] text-gray-500 mt-1">
                                Các hạng mục dịch vụ con hiển thị với <strong>Tiêu đề màu đỏ (#EB2026)</strong> và <strong>Mô tả màu đen</strong> khi bấm <em>View more</em>.
                            </p>
                        </div>
                        <button type="button" @click="addIntroItem()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition-colors">
                            + Thêm hạng mục chi tiết
                        </button>
                    </div>

                    <!-- Live Preview Box (Giống hệt giao diện ngoài Trang chủ) -->
                    <div class="p-6 rounded-xl bg-[#f3f3f3] border border-gray-200/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Xem trước trực tiếp màn hình Chi tiết Dịch vụ</span>
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full border border-[#EB2026] text-[#EB2026] text-[11px] font-medium">
                                {{ $labels['pill_text'] ?? 'Services' }}
                            </span>
                        </div>

                        <div class="max-w-md">
                            <div class="text-2xl font-medium text-black leading-tight" x-text="title || 'Fit-Out and Finishing Works'"></div>
                            <div class="mt-3 h-1 w-36 rounded-full" style="background: linear-gradient(90deg, #EB2026 0%, rgba(235, 32, 38, 0) 100%);"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <template x-for="(intro, iIdx) in introItems" :key="'prev-' + iIdx">
                                <div>
                                    <h4 class="text-base font-bold text-[#EB2026] leading-snug" x-text="intro.title || 'Tiêu đề hạng mục...'"></h4>
                                    <p class="text-xs text-black/85 leading-relaxed mt-1.5" x-text="intro.desc || 'Mô tả chi tiết hạng mục...'"></p>
                                </div>
                            </template>
                        </div>

                        <div class="pt-3 border-t border-gray-300/60 flex items-center gap-6 text-[11px] font-semibold">
                            <span class="text-black underline underline-offset-4">{{ strtoupper($labels['tab_intro_text'] ?? 'INTRODUCTIONS') }}</span>
                            <span class="text-gray-400">{{ strtoupper($labels['tab_project_text'] ?? 'PROJECTS') }}</span>
                            <span class="text-gray-600 italic font-normal ml-auto">{{ $labels['back_text'] ?? 'Back to Services' }}</span>
                        </div>
                    </div>

                    <!-- Danh sách nhập liệu các mục Introductions -->
                    <div class="space-y-3">
                        <template x-for="(intro, iIdx) in introItems" :key="iIdx">
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                                <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-rose-600 text-white rounded text-[10px]" x-text="'Mục 0' + (iIdx + 1)"></span>
                                        <span x-text="intro.title || 'Hạng mục dịch vụ chi tiết'"></span>
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="moveIntro(iIdx, -1)" :disabled="iIdx === 0" class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30" title="Lên">▲</button>
                                        <button type="button" @click="moveIntro(iIdx, 1)" :disabled="iIdx === introItems.length - 1" class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30" title="Xuống">▼</button>
                                        <button type="button" @click="removeIntroItem(iIdx)" class="p-1 text-gray-400 hover:text-rose-600" title="Xóa mục">✕</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-semibold text-rose-700 mb-1">Tiêu đề hạng mục con (Chữ Đỏ)</label>
                                        <input type="text" x-model="intro.title" placeholder="VD: Turnkey Fit-Out Execution" class="w-full px-3 py-2 text-xs font-bold text-rose-700 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                                    </div>
                                    <div class="md:col-span-7">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả chi tiết (Chữ Đen)</label>
                                        <textarea rows="2" x-model="intro.desc" placeholder="VD: Executing civil works, MEP, HVAC, drywall, flooring, and ceiling systems with uncompromising precision." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>


            </div>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-service-form" action="{{ route('admin.services.destroy', $service['id']) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
function serviceEditApp() {
    return {
        title: @js(old('title', $service['title'] ?? '')),
        slug: @js(old('slug', $service['slug'] ?? '')),
        chipLine1: @js(old('chip_line_1', $service['chip_line_1'] ?? '')),
        chipLine2: @js(old('chip_line_2', $service['chip_line_2'] ?? '')),
        introItems: @js($service['intro_items'] ?? []),

        autoSlug() {
            this.slug = (this.title || '')
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        },
        addIntroItem() {
            this.introItems.push({ title: '', desc: '' });
        },
        removeIntroItem(idx) {
            if (this.introItems.length <= 1) return;
            this.introItems.splice(idx, 1);
        },
        moveIntro(idx, dir) {
            const target = idx + dir;
            if (target < 0 || target >= this.introItems.length) return;
            const temp = this.introItems[idx];
            this.introItems[idx] = this.introItems[target];
            this.introItems[target] = temp;
        }
    };
}
</script>
@endsection

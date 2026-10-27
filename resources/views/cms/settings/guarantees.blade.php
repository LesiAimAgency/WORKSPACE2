@extends('cms.layouts.app')

@section('title', 'Cấu hình Cam Kết & Bảo Hành Sản Phẩm')
@section('page-title', 'Cam Kết & Bảo Hành')

@section('content')
<x-media-picker-modal />

@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = route('admin.settings.save');
    
    $settingData = setting('product_guarantees');
    if (is_string($settingData)) {
        $settingData = json_decode($settingData, true) ?: [];
    }

    $enabled = isset($settingData['enabled']) ? (bool)$settingData['enabled'] : true;
    
    $defaultItems = [
        [
            'icon' => 'themes/daiphuc68/assets/icons/guarantee.svg',
            'title' => 'HÀNG CHẤT LƯỢNG CHÍNH HÃNG',
            'description' => 'Cam kết xuất xứ rõ ràng, đầy đủ chứng chỉ CO/CQ & tiêu chuẩn EVN.',
            'link' => '',
        ],
        [
            'icon' => 'themes/daiphuc68/assets/icons/rocket.svg',
            'title' => 'GIAO HÀNG THẦN TỐC TRONG VÒNG 24H',
            'description' => 'Giao hàng trong vòng 24h, hỗ trợ vận chuyển toàn quốc tận công trình.',
            'link' => '',
        ],
        [
            'icon' => 'themes/daiphuc68/assets/icons/preferences.svg',
            'title' => 'BẢO HÀNH DÀI HẠN LÊN ĐẾN 36 THÁNG',
            'description' => 'Bảo hành chính hãng lên đến 36 tháng, hỗ trợ kỹ thuật bảo trì tận tâm.',
            'link' => '',
        ],
    ];

    $items = (!empty($settingData['items']) && is_array($settingData['items'])) 
        ? $settingData['items'] 
        : $defaultItems;
@endphp

<div x-data="guaranteesManager()" 
     @media-picker-selected.window="handleMediaSelected($event.detail)"
     class="space-y-6 max-w-7xl mx-auto pb-16">

    @if(session('alert'))
        <div class="p-4 rounded-xl {{ session('alert.type') === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }} flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 {{ session('alert.type') === 'success' ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-medium">{{ session('alert.message') }}</span>
            </div>
        </div>
    @endif

    <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data" id="guaranteesForm">
        @csrf

        <!-- Top Header & Action Banner -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-4 z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Tiêu Chuẩn Bán Hàng &amp; Dịch Vụ
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Cấu Hình Cam Kết &amp; Bảo Hành Sản Phẩm</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Quản lý khối thông tin cam kết chất lượng, giao hàng và bảo hành hiển thị trên trang chi tiết sản phẩm.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="resetDefaults()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Khôi phục mặc định
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Lưu Cấu Hình</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Left: Settings & Items List (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Main Toggle Switch Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-slate-900">Bật hiển thị khối Cam Kết trên trang sản phẩm</h3>
                            <p class="text-xs text-slate-500">
                                Khi bật, khối cam kết sẽ xuất hiện bên dưới thông tin chính của tất cả các sản phẩm trên website.
                            </p>
                        </div>
                        <input type="hidden" name="product_guarantees[enabled]" :value="enabled ? '1' : '0'">
                        <button type="button" 
                                @click="enabled = !enabled" 
                                :class="enabled ? 'bg-emerald-600' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>

                <!-- Items Repeater Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Danh Sách Mục Cam Kết (<span x-text="items.length"></span>)</h3>
                            <p class="text-xs text-slate-500">Thêm, sửa, đổi icon và sắp xếp thứ tự hiển thị các cam kết dịch vụ.</p>
                        </div>
                        <button type="button" @click="addItem()" 
                                class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Thêm Cam Kết Mới</span>
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="border border-slate-200 rounded-xl p-4 sm:p-5 bg-slate-50/60 hover:bg-slate-50 transition-all space-y-4 relative group">
                                <!-- Item Header Bar -->
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200/80 pb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center" x-text="index + 1"></span>
                                        <span class="text-xs font-bold text-slate-700 uppercase" x-text="item.title ? item.title : 'Mục cam kết #' + (index + 1)"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="moveUp(index)" :disabled="index === 0" 
                                                class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded hover:bg-white transition" title="Di chuyển lên">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button type="button" @click="moveDown(index)" :disabled="index === items.length - 1" 
                                                class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded hover:bg-white transition" title="Di chuyển xuống">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button type="button" @click="removeItem(index)" 
                                                class="p-1.5 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition ml-1" title="Xóa mục này">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <!-- Icon Picker Column (4 cols) -->
                                    <div class="md:col-span-4 space-y-2">
                                        <label class="block text-xs font-bold text-slate-700">Icon / Biểu tượng</label>
                                        
                                        <!-- Icon Preview Box -->
                                        <div class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl">
                                            <div class="w-14 h-14 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200/80 overflow-hidden">
                                                <template x-if="isFaIcon(item.icon)">
                                                    <i :class="item.icon" class="text-2xl text-[#051650]"></i>
                                                </template>
                                                <template x-if="!isFaIcon(item.icon)">
                                                    <img :src="resolveIconUrl(item.icon)" class="w-10 h-10 object-contain" onerror="this.src='{{ asset('themes/daiphuc68/assets/icons/guarantee.svg') }}'">
                                                </template>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <input type="text" 
                                                       :name="`product_guarantees[items][${index}][icon]`" 
                                                       x-model="item.icon" 
                                                       placeholder="Đường dẫn icon hoặc fa-..." 
                                                       class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-mono">
                                            </div>
                                        </div>

                                        <!-- Quick Preset Icons Selection -->
                                        <div>
                                            <span class="block text-[11px] font-semibold text-slate-500 mb-1">Chọn nhanh icon mẫu:</span>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <button type="button" @click="item.icon = 'themes/daiphuc68/assets/icons/guarantee.svg'"
                                                        :class="item.icon === 'themes/daiphuc68/assets/icons/guarantee.svg' ? 'ring-2 ring-blue-500 border-blue-500 bg-blue-50' : 'bg-white border-slate-200'"
                                                        class="p-1.5 border rounded-lg hover:border-blue-400 transition" title="Hàng chính hãng (Shield)">
                                                    <img src="{{ asset('themes/daiphuc68/assets/icons/guarantee.svg') }}" class="w-5 h-5 object-contain">
                                                </button>
                                                <button type="button" @click="item.icon = 'themes/daiphuc68/assets/icons/rocket.svg'"
                                                        :class="item.icon === 'themes/daiphuc68/assets/icons/rocket.svg' ? 'ring-2 ring-blue-500 border-blue-500 bg-blue-50' : 'bg-white border-slate-200'"
                                                        class="p-1.5 border rounded-lg hover:border-blue-400 transition" title="Giao hàng thần tốc (Rocket)">
                                                    <img src="{{ asset('themes/daiphuc68/assets/icons/rocket.svg') }}" class="w-5 h-5 object-contain">
                                                </button>
                                                <button type="button" @click="item.icon = 'themes/daiphuc68/assets/icons/preferences.svg'"
                                                        :class="item.icon === 'themes/daiphuc68/assets/icons/preferences.svg' ? 'ring-2 ring-blue-500 border-blue-500 bg-blue-50' : 'bg-white border-slate-200'"
                                                        class="p-1.5 border rounded-lg hover:border-blue-400 transition" title="Bảo hành dài hạn (Cog)">
                                                    <img src="{{ asset('themes/daiphuc68/assets/icons/preferences.svg') }}" class="w-5 h-5 object-contain">
                                                </button>
                                                <button type="button" @click="openMediaPicker(index)" 
                                                        class="px-2 py-1 bg-white border border-slate-200 hover:border-blue-400 text-[11px] font-medium text-slate-700 rounded-lg transition flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Thư viện
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content Fields Column (8 cols) -->
                                    <div class="md:col-span-8 space-y-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                                Tiêu đề cam kết <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   :name="`product_guarantees[items][${index}][title]`" 
                                                   x-model="item.title" 
                                                   required
                                                   placeholder="VD: HÀNG CHẤT LƯỢNG CHÍNH HÃNG" 
                                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold uppercase tracking-tight text-slate-900">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                                Nội dung mô tả cam kết <span class="text-red-500">*</span>
                                            </label>
                                            <textarea :name="`product_guarantees[items][${index}][description]`" 
                                                      x-model="item.description" 
                                                      rows="2" 
                                                      required
                                                      placeholder="VD: Cam kết xuất xứ rõ ràng, đầy đủ chứng chỉ CO/CQ & tiêu chuẩn EVN."
                                                      class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-700 leading-snug"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                                Đường dẫn liên kết (Tùy chọn)
                                            </label>
                                            <input type="text" 
                                                   :name="`product_guarantees[items][${index}][link]`" 
                                                   x-model="item.link" 
                                                   placeholder="VD: /chinh-sach-bao-hanh hoặc https://..." 
                                                   class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-700">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="items.length === 0" class="p-8 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <p class="text-sm text-slate-500 font-medium">Chưa có mục cam kết nào.</p>
                            <button type="button" @click="resetDefaults()" class="mt-2 text-xs font-bold text-blue-600 hover:underline">
                                Bấm vào đây để tải lại 3 mục mặc định của Đại Phúc 68
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Real-time Live Preview & Tips (1 col) -->
            <div class="space-y-6">
                <!-- Live Preview Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm sticky top-28 space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Xem Trước Trực Tiếp (Live Demo)</h3>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md" :class="enabled ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'" x-text="enabled ? 'Đang bật' : 'Đang tắt'"></span>
                    </div>

                    <div :class="!enabled ? 'opacity-40 pointer-events-none' : ''" class="transition-opacity">
                        <p class="text-[11px] text-slate-400 mb-2">Mô phỏng hiển thị trên màn hình máy tính:</p>
                        
                        <!-- Simulated Product Page Section -->
                        <div class="border border-slate-200 rounded-xl bg-white p-4 shadow-2xs space-y-4 overflow-hidden">
                            <div class="space-y-4">
                                <template x-for="(pItem, pIdx) in items" :key="pIdx">
                                    <div class="flex items-start gap-3.5 pb-3 border-b last:border-b-0 border-slate-100">
                                        <div class="w-10 h-10 shrink-0 flex items-center justify-center">
                                            <template x-if="isFaIcon(pItem.icon)">
                                                <i :class="pItem.icon" class="text-xl text-[#051650]"></i>
                                            </template>
                                            <template x-if="!isFaIcon(pItem.icon)">
                                                <img :src="resolveIconUrl(pItem.icon)" class="w-10 h-10 object-contain" onerror="this.src='{{ asset('themes/daiphuc68/assets/icons/guarantee.svg') }}'">
                                            </template>
                                        </div>
                                        <div class="min-w-0 space-y-0.5">
                                            <h5 class="text-xs font-black text-[#051650] uppercase tracking-tight line-clamp-1" x-text="pItem.title || 'Tiêu đề cam kết'"></h5>
                                            <p class="text-[11px] text-neutral-600 leading-snug line-clamp-2" x-text="pItem.description || 'Mô tả cam kết chất lượng...'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Guide Card -->
                    <div class="p-4 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-2 text-xs text-blue-900">
                        <div class="font-bold flex items-center gap-1.5 text-blue-800">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tính năng nâng cao:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-slate-600 text-[11px] leading-relaxed">
                            <li><strong>Toàn hệ thống:</strong> Thiết lập tại đây sẽ tự động áp dụng cho mọi sản phẩm.</li>
                            <li><strong>Riêng từng sản phẩm:</strong> Bạn có thể mở màn hình "Sửa sản phẩm" để chỉnh sửa cam kết riêng biệt (hoặc ẩn) cho sản phẩm đó nếu cần thời gian bảo hành khác nhau.</li>
                            <li><strong>Icon hỗ trợ:</strong> Ảnh SVG, PNG tải lên hoặc nhập mã FontAwesome (vd: <code class="bg-white px-1 py-0.5 rounded text-blue-700">fa-solid fa-truck-fast</code>).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function guaranteesManager() {
    return {
        enabled: {{ $enabled ? 'true' : 'false' }},
        items: @json($items),
        activeMediaTargetIndex: null,

        isFaIcon(icon) {
            if (!icon) return false;
            return icon.startsWith('fa-') || icon.includes(' fa-');
        },

        resolveIconUrl(icon) {
            if (!icon) return '{{ asset("themes/daiphuc68/assets/icons/guarantee.svg") }}';
            if (icon.startsWith('http://') || icon.startsWith('https://')) return icon;
            if (icon.startsWith('/storage') || icon.startsWith('storage/')) {
                return '{{ url("/") }}/' + icon.replace(/^\/+/, '');
            }
            return '{{ asset("") }}' + icon.replace(/^\/+/, '');
        },

        addItem() {
            this.items.push({
                icon: 'themes/daiphuc68/assets/icons/guarantee.svg',
                title: '',
                description: '',
                link: ''
            });
        },

        removeItem(index) {
            if (confirm('Bạn có chắc chắn muốn xóa mục cam kết này không?')) {
                this.items.splice(index, 1);
            }
        },

        moveUp(index) {
            if (index > 0) {
                const temp = this.items[index];
                this.items[index] = this.items[index - 1];
                this.items[index - 1] = temp;
            }
        },

        moveDown(index) {
            if (index < this.items.length - 1) {
                const temp = this.items[index];
                this.items[index] = this.items[index + 1];
                this.items[index + 1] = temp;
            }
        },

        resetDefaults() {
            if (confirm('Bạn có muốn khôi phục về 3 cam kết chuẩn của Đại Phúc 68?')) {
                this.items = @json($defaultItems);
                this.enabled = true;
            }
        },

        openMediaPicker(index) {
            this.activeMediaTargetIndex = index;
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: {
                    type: 'image',
                    target: 'guarantee_icon_' + index
                }
            }));
        },

        handleMediaSelected(detail) {
            const url = detail.url || detail.value || (detail.file ? detail.file.url : '');
            if (url && this.activeMediaTargetIndex !== null && this.items[this.activeMediaTargetIndex]) {
                this.items[this.activeMediaTargetIndex].icon = url;
                this.activeMediaTargetIndex = null;
            }
        }
    };
}
</script>
@endpush
@endsection

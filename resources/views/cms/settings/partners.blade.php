@extends('cms.layouts.app')

@section('title', 'Cấu hình Đối Tác')
@section('page-title', 'Đối Tác')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    [x-cloak] { display: none; }
</style>
@endpush

@section('content')
@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = \Route::has('admin.settings.partners.save')
        ? route('admin.settings.partners.save')
        : (\Route::has('admin.settings.save') ? route('admin.settings.save') : url('/admin/settings'));

    $currentHeading = $partnersHeading ?? setting('partners_heading', 'OUR CUSTOMERS - PARTNERS');

    $rawItems = $partnerItems ?? setting('partners_items', setting('partners'));
    if (is_string($rawItems)) {
        $rawItems = json_decode($rawItems, true) ?: [];
    }

    $defaultItems = [
        [
            'id' => 1,
            'image' => 'themes/workspace/assets/parnert1.png',
            'alt' => 'Strategic Partner 1',
            'link' => '',
            'is_active' => true,
        ],
        [
            'id' => 2,
            'image' => 'themes/workspace/assets/parnert2.png',
            'alt' => 'Strategic Partner 2',
            'link' => '',
            'is_active' => true,
        ],
        [
            'id' => 3,
            'image' => 'themes/workspace/assets/parnert1.png',
            'alt' => 'Strategic Partner 3',
            'link' => '',
            'is_active' => true,
        ],
        [
            'id' => 4,
            'image' => 'themes/workspace/assets/parnert2.png',
            'alt' => 'Strategic Partner 4',
            'link' => '',
            'is_active' => true,
        ],
        [
            'id' => 5,
            'image' => 'themes/workspace/assets/parnert1.png',
            'alt' => 'Strategic Partner 5',
            'link' => '',
            'is_active' => true,
        ],
    ];

    $items = (!empty($rawItems) && is_array($rawItems)) ? array_values($rawItems) : $defaultItems;
@endphp

<div x-data="partnersManager()"
     @media-picker-selected.window="handleMediaSelected($event.detail)"
     @media-selected.window="handleMediaSelected($event.detail)"
     class="space-y-6 max-w-7xl mx-auto pb-16">

    <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="partners_items" :value="JSON.stringify(items)">

        <!-- Top Header Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm mb-6 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20">
                        <i class="fa-solid fa-handshake text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Cấu hình Đối Tác (Partners Marquee)</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span x-text="items.length"></span> đối tác
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Quản lý tiêu đề và danh sách logo khách hàng / đối tác hiển thị đồng bộ tại 2 section: <strong>Why Choosing Workspace</strong> &amp; <strong>Connect Workspace</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <button type="button" @click="resetDefaults()"
                            class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Khôi phục mẫu</span>
                    </button>

                    <button type="button" @click="addItem()"
                            class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition flex items-center gap-1.5 shadow-sm shadow-emerald-600/20">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Thêm Đối Tác</span>
                    </button>

                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition flex items-center gap-2 shadow-sm shadow-blue-600/25">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Lưu Cấu Hình</span>
                    </button>
                </div>
            </div>

            <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-amber-100/40 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- 2 Columns Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Left Column: Heading + Repeater Items (8 cols) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Heading Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span class="w-2 h-5 bg-amber-500 rounded-full inline-block"></span>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tiêu đề khối Đối tác / Khách hàng</h3>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tiêu đề hiển thị (Heading) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="partners_heading"
                               x-model="heading"
                               required
                               placeholder="OUR CUSTOMERS - PARTNERS"
                               class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-slate-900 uppercase tracking-wide shadow-2xs">
                        <p class="text-[11px] text-slate-500 mt-1">
                            Tiêu đề này sẽ hiển thị phía trên thanh trượt logo đối tác ở cả 2 mục <strong>Why Choosing Workspace</strong> và <strong>Connect Workspace</strong>.
                        </p>
                    </div>
                </div>

                <!-- Partners List -->
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="item.id || index">
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-4 relative group transition-all hover:border-slate-300">

                            <!-- Item Header -->
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 text-xs font-black flex items-center justify-center border border-slate-200"
                                          x-text="'#' + (index + 1)"></span>
                                    <span class="text-sm font-bold text-slate-800 tracking-tight"
                                          x-text="item.alt ? item.alt : 'Đối tác #' + (index + 1)"></span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <!-- Active / Hidden Toggle -->
                                    <button type="button" @click="item.is_active = (item.is_active === false ? true : false)"
                                            :class="item.is_active !== false ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-400 border-slate-200'"
                                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg border flex items-center gap-1.5 transition">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active !== false ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                        <span x-text="item.is_active !== false ? 'Hiển thị' : 'Tạm ẩn'"></span>
                                    </button>

                                    <div class="h-4 w-px bg-slate-200 mx-1"></div>

                                    <button type="button" @click="moveUp(index)" :disabled="index === 0"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded-lg hover:bg-slate-100 transition" title="Di chuyển lên">
                                        <i class="fa-solid fa-arrow-up text-xs"></i>
                                    </button>

                                    <button type="button" @click="moveDown(index)" :disabled="index === items.length - 1"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded-lg hover:bg-slate-100 transition" title="Di chuyển xuống">
                                        <i class="fa-solid fa-arrow-down text-xs"></i>
                                    </button>

                                    <button type="button" @click="removeItem(index)"
                                            class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition ml-1" title="Xóa đối tác này">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                                <!-- Left: Logo Preview & Upload (5 cols) -->
                                <div class="md:col-span-5 space-y-3">
                                    <label class="block text-xs font-bold text-slate-700">Hình ảnh Logo đối tác</label>

                                    <div class="flex items-center gap-3 p-3 bg-slate-50/80 border border-slate-200 rounded-xl">
                                        <div class="w-24 h-16 rounded-xl bg-white flex items-center justify-center shrink-0 border border-slate-200/80 overflow-hidden shadow-2xs relative p-2">
                                            <template x-if="uploadingIndex === index">
                                                <div class="absolute inset-0 bg-white/90 flex items-center justify-center">
                                                    <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-lg"></i>
                                                </div>
                                            </template>
                                            <template x-if="item.image">
                                                <img :src="resolveImageUrl(item.image)"
                                                     :alt="item.alt || 'Partner'"
                                                     class="max-w-full max-h-full object-contain"
                                                     onerror="this.src='{{ asset('themes/workspace/assets/parnert1.png') }}'">
                                            </template>
                                            <template x-if="!item.image">
                                                <i class="fa-regular fa-image text-xl text-slate-300"></i>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0 space-y-1">
                                            <p class="text-[11px] font-semibold text-slate-500">Đường dẫn ảnh:</p>
                                            <input type="text"
                                                   x-model="item.image"
                                                   placeholder="themes/workspace/assets/parnert1.png"
                                                   class="w-full px-2.5 py-1.5 text-[11px] font-mono bg-white border border-slate-200 rounded-lg text-slate-700">
                                        </div>
                                    </div>

                                    <!-- Upload & Media Picker Buttons -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <input type="file"
                                               :id="'partner_file_' + index"
                                               accept=".png,.jpg,.jpeg,.webp,.svg"
                                               class="hidden"
                                               @change="uploadFile(index, $event)">

                                        <button type="button" @click="document.getElementById('partner_file_' + index).click()"
                                                class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-semibold rounded-lg transition flex items-center gap-1 shadow-2xs">
                                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                            <span>Tải ảnh lên</span>
                                        </button>

                                        <button type="button" @click="openMediaPicker(index)"
                                                class="px-2.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium rounded-lg transition flex items-center gap-1 shadow-2xs">
                                            <i class="fa-regular fa-images text-xs text-slate-500"></i>
                                            <span>Thư viện Media</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Right: Partner Name & Link (7 cols) -->
                                <div class="md:col-span-7 space-y-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Tên đối tác / Khách hàng (Alt) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               x-model="item.alt"
                                               required
                                               placeholder="VD: Strategic Partner 1"
                                               class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-slate-900 shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Đường dẫn website đối tác (Tùy chọn)
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                                <i class="fa-solid fa-link text-xs"></i>
                                            </div>
                                            <input type="text"
                                                   x-model="item.link"
                                                   placeholder="https://... (Để trống nếu chỉ hiển thị logo)"
                                                   class="w-full pl-8 pr-3.5 py-2 text-xs sm:text-sm font-mono bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 shadow-2xs">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="items.length === 0" x-cloak
                         class="p-10 text-center bg-white border-2 border-dashed border-slate-200 rounded-2xl space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-700">Chưa có đối tác nào trong danh sách</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Bấm nút bên dưới để thêm đối tác mới hoặc khôi phục danh sách mẫu.</p>
                        </div>
                        <div class="flex items-center justify-center gap-2 pt-2">
                            <button type="button" @click="addItem()"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-plus"></i>
                                <span>Thêm đối tác mới</span>
                            </button>
                            <button type="button" @click="resetDefaults()"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Khôi phục mẫu chuẩn</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="pt-2 flex items-center justify-between">
                    <button type="button" @click="addItem()"
                            class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm Đối Tác Mới</span>
                    </button>

                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm shadow-blue-600/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Lưu Cấu Hình</span>
                    </button>
                </div>

            </div>

            <!-- Right Column: Live Preview & Usage Info (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm sticky top-24 space-y-5">
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Xem Trước Trực Tiếp (Live Preview)</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400">2 Sections</span>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-center space-y-3">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider" x-text="heading || 'OUR CUSTOMERS - PARTNERS'"></h4>
                        <div class="grid grid-cols-3 gap-2.5 items-center">
                            <template x-for="(pItem, pIdx) in items.filter(i => i.is_active !== false)" :key="pIdx">
                                <div class="h-12 bg-white border border-slate-200/80 rounded-lg p-2 flex items-center justify-center shadow-2xs" :title="pItem.alt">
                                    <img :src="resolveImageUrl(pItem.image)"
                                         :alt="pItem.alt"
                                         class="max-h-8 max-w-full object-contain"
                                         onerror="this.src='{{ asset('themes/workspace/assets/parnert1.png') }}'">
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-50/70 border border-amber-200/80 rounded-xl space-y-2 text-xs text-amber-900">
                        <div class="font-bold flex items-center gap-1.5 text-amber-800">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            <span>Vị trí áp dụng tự động:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-slate-700 text-[11px] leading-relaxed">
                            <li><strong>Why Choosing Workspace</strong> (Section 5 - chân trang Lý do chọn chúng tôi).</li>
                            <li><strong>Connect Workspace</strong> (Section 6 - chân trang Kết nối tư vấn).</li>
                            <li>Chỉ cần chỉnh sửa tại đây, cả 2 section sẽ tự động đồng bộ danh sách logo đối tác mới nhất.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function partnersManager() {
    return {
        heading: @json($currentHeading),
        items: @json($items),
        activeMediaTargetIndex: null,
        uploadingIndex: null,

        resolveImageUrl(img) {
            if (!img || typeof img !== 'string') return '{{ asset("themes/workspace/assets/parnert1.png") }}';
            if (img.startsWith('http://') || img.startsWith('https://')) return img;
            if (img.startsWith('/storage') || img.startsWith('storage/')) {
                return '{{ url("/") }}/' + img.replace(/^\/+/, '');
            }
            return '{{ asset("") }}' + img.replace(/^\/+/, '');
        },

        addItem() {
            this.items.push({
                id: Date.now(),
                image: 'themes/workspace/assets/parnert1.png',
                alt: 'Strategic Partner ' + (this.items.length + 1),
                link: '',
                is_active: true
            });
        },

        removeItem(index) {
            if (confirm('Bạn có chắc chắn muốn xóa đối tác này không?')) {
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
            if (confirm('Bạn có muốn khôi phục về danh sách 5 đối tác mặc định?')) {
                this.heading = 'OUR CUSTOMERS - PARTNERS';
                this.items = @json($defaultItems);
            }
        },

        openMediaPicker(index) {
            this.activeMediaTargetIndex = index;
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: {
                    type: 'image',
                    mediaType: 'image',
                    multiple: false,
                    field: 'partner_image_' + index,
                    target: 'partner_image_' + index,
                    folder: 'partners'
                }
            }));
        },

        handleMediaSelected(detail) {
            if (!detail) return;
            const url = detail.url
                || detail.value
                || (Array.isArray(detail.urls) ? detail.urls[0] : detail.urls)
                || (detail.file ? detail.file.url : '')
                || (Array.isArray(detail.files) && detail.files[0] ? (detail.files[0].url || detail.files[0].path) : '');
            if (url && this.activeMediaTargetIndex !== null && this.items[this.activeMediaTargetIndex]) {
                this.items[this.activeMediaTargetIndex].image = url;
                this.activeMediaTargetIndex = null;
            }
        },

        async uploadFile(index, event) {
            const file = event.target.files[0];
            if (!file) return;

            this.uploadingIndex = index;
            const formData = new FormData();
            formData.append('path', 'partners');
            formData.append('files[]', file);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value;

            try {
                const uploadUrl = '{{ \Route::has("admin.media.upload") ? route("admin.media.upload") : url("/admin/media/upload") }}';
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.uploaded && result.uploaded.length > 0) {
                        this.items[index].image = result.uploaded[0].url;
                        if (typeof window.showAlert === 'function') {
                            window.showAlert('Tải lên logo đối tác thành công!', 'success');
                        }
                    }
                } else {
                    alert('Không thể tải ảnh lên. Vui lòng thử lại.');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối khi tải ảnh lên.');
            } finally {
                this.uploadingIndex = null;
                event.target.value = '';
            }
        }
    };
}
</script>
@endpush
@endsection

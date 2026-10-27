@extends('cms.layouts.app')

@section('title', 'Cấu hình Mạng Xã Hội')
@section('page-title', 'Mạng Xã Hội')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    [x-cloak] { display: none; }
    .social-svg-preview svg {
        width: 100%;
        height: 100%;
        display: block;
    }
</style>
@endpush

@section('content')
<x-media-picker-modal />

@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = \Route::has('admin.settings.social.save') 
        ? route('admin.settings.social.save') 
        : (\Route::has('admin.settings.save') ? route('admin.settings.save') : url('/admin/settings'));

    // Lấy danh sách mạng xã hội hiện tại từ setting hoặc fallback
    $rawItems = setting('social_items');
    if (is_string($rawItems)) {
        $rawItems = json_decode($rawItems, true) ?: [];
    }

    $defaultItems = [
        [
            'id' => 1,
            'name' => 'Facebook',
            'link' => setting('social_facebook') ?: 'https://facebook.com/',
            'icon' => 'fa-brands fa-facebook',
            'icon_type' => 'fa',
            'color' => '#1877F2',
            'target' => '_blank',
            'is_active' => true,
        ],
        [
            'id' => 2,
            'name' => 'Zalo',
            'link' => setting('social_zalo') ? (str_starts_with(setting('social_zalo'), 'http') ? setting('social_zalo') : 'https://zalo.me/'.setting('social_zalo')) : 'https://zalo.me/',
            'icon' => 'fa-solid fa-comment-dots',
            'icon_type' => 'fa',
            'color' => '#0068FF',
            'target' => '_blank',
            'is_active' => true,
        ],
        [
            'id' => 3,
            'name' => 'YouTube',
            'link' => setting('social_youtube') ?: 'https://youtube.com/',
            'icon' => 'fa-brands fa-youtube',
            'icon_type' => 'fa',
            'color' => '#FF0000',
            'target' => '_blank',
            'is_active' => true,
        ],
        [
            'id' => 4,
            'name' => 'TikTok',
            'link' => setting('social_tiktok') ?: 'https://tiktok.com/',
            'icon' => 'fa-brands fa-tiktok',
            'icon_type' => 'fa',
            'color' => '#000000',
            'target' => '_blank',
            'is_active' => true,
        ],
        [
            'id' => 5,
            'name' => 'Instagram',
            'link' => setting('social_instagram') ?: 'https://instagram.com/',
            'icon' => 'fa-brands fa-instagram',
            'icon_type' => 'fa',
            'color' => '#E4405F',
            'target' => '_blank',
            'is_active' => true,
        ],
        [
            'id' => 6,
            'name' => 'LinkedIn',
            'link' => 'https://linkedin.com/',
            'icon' => 'fa-brands fa-linkedin',
            'icon_type' => 'fa',
            'color' => '#0A66C2',
            'target' => '_blank',
            'is_active' => false,
        ],
    ];

    $items = (!empty($rawItems) && is_array($rawItems)) ? $rawItems : $defaultItems;
@endphp

<div x-data="socialManager()" 
     @media-picker-selected.window="handleMediaSelected($event.detail)"
     class="space-y-6 max-w-7xl mx-auto pb-16">

    <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data" @submit="beforeSubmit">
        @csrf
        <input type="hidden" name="social_items" :value="JSON.stringify(items)">

        <!-- Top Header Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm mb-6 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-sky-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-sky-500/20">
                        <i class="fa-solid fa-share-nodes text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Cấu hình Mạng Xã Hội (Social Media)</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                <span x-text="items.length"></span> kênh
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Quản lý các kênh mạng xã hội, tải lên icon ảnh/SVG hoặc chọn biểu tượng, đặt tên và gắn đường dẫn lên website.
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
                        <span>Thêm Mạng Xã Hội</span>
                    </button>

                    <button type="submit" 
                            class="px-5 py-2 text-xs font-bold text-white bg-[#EC460B] hover:bg-[#d03d09] rounded-xl transition flex items-center gap-2 shadow-sm shadow-[#EC460B]/25">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Lưu Cấu Hình</span>
                    </button>
                </div>
            </div>

            <!-- Background subtle accent -->
            <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-sky-100/40 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- 2 Columns Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left: Repeater Items List (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                
                <template x-for="(item, index) in items" :key="item.id || index">
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-4 relative group transition-all hover:border-slate-300">
                        
                        <!-- Header Bar of Item -->
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 text-xs font-black flex items-center justify-center border border-slate-200" 
                                      x-text="'#' + (index + 1)"></span>
                                <span class="text-sm font-bold text-slate-800 tracking-tight" 
                                      x-text="item.name ? item.name : 'Mạng xã hội #' + (index + 1)"></span>
                                <span :style="{ backgroundColor: item.color || '#64748b' }" 
                                      class="w-2.5 h-2.5 rounded-full inline-block shrink-0 shadow-2xs" :title="'Màu: ' + (item.color || '#64748b')"></span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <!-- Active / Hidden Toggle -->
                                <button type="button" @click="item.is_active = !item.is_active" 
                                        :class="item.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-400 border-slate-200'"
                                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg border flex items-center gap-1.5 transition">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    <span x-text="item.is_active ? 'Hiển thị' : 'Tạm ẩn'"></span>
                                </button>

                                <div class="h-4 w-px bg-slate-200 mx-1"></div>

                                <!-- Move Up -->
                                <button type="button" @click="moveUp(index)" :disabled="index === 0" 
                                        class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded-lg hover:bg-slate-100 transition" title="Di chuyển lên">
                                    <i class="fa-solid fa-arrow-up text-xs"></i>
                                </button>

                                <!-- Move Down -->
                                <button type="button" @click="moveDown(index)" :disabled="index === items.length - 1" 
                                        class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded-lg hover:bg-slate-100 transition" title="Di chuyển xuống">
                                    <i class="fa-solid fa-arrow-down text-xs"></i>
                                </button>

                                <!-- Delete -->
                                <button type="button" @click="removeItem(index)" 
                                        class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition ml-1" title="Xóa mạng xã hội này">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card Body Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            
                            <!-- Left: Icon / SVG Preview & Actions (5 cols) -->
                            <div class="md:col-span-5 space-y-3">
                                <label class="block text-xs font-bold text-slate-700">Biểu tượng (Icon / SVG / Ảnh)</label>

                                <!-- Icon Preview Box -->
                                <div class="flex items-center gap-3 p-3 bg-slate-50/80 border border-slate-200 rounded-xl">
                                    <div class="w-14 h-14 rounded-xl bg-white flex items-center justify-center shrink-0 border border-slate-200/80 overflow-hidden shadow-2xs relative">
                                        <!-- Loading indicator when uploading -->
                                        <template x-if="uploadingIndex === index">
                                            <div class="absolute inset-0 bg-white/90 flex items-center justify-center">
                                                <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-lg"></i>
                                            </div>
                                        </template>

                                        <!-- Preview SVG Code -->
                                        <template x-if="isSvgCode(item.icon)">
                                            <div class="w-8 h-8 flex items-center justify-center social-svg-preview text-slate-800" 
                                                 :style="{ color: item.color || '#1e293b' }"
                                                 x-html="item.icon"></div>
                                        </template>

                                        <!-- Preview FontAwesome -->
                                        <template x-if="!isSvgCode(item.icon) && isFaIcon(item.icon)">
                                            <i :class="item.icon" class="text-2xl" :style="{ color: item.color || '#1e293b' }"></i>
                                        </template>

                                        <!-- Preview Image / SVG URL -->
                                        <template x-if="!isSvgCode(item.icon) && !isFaIcon(item.icon) && item.icon">
                                            <img :src="resolveIconUrl(item.icon)" class="w-9 h-9 object-contain" 
                                                 onerror="this.src='{{ asset('themes/daiphuc68/assets/icons/guarantee.svg') }}'">
                                        </template>

                                        <!-- Empty Placeholder -->
                                        <template x-if="!item.icon">
                                            <i class="fa-solid fa-share-nodes text-xl text-slate-300"></i>
                                        </template>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-semibold text-slate-500 mb-0.5">Nguồn Icon:</p>
                                        <span class="inline-block text-[11px] font-mono px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 truncate max-w-full"
                                              x-text="getIconSourceLabel(item.icon)"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons: Upload file / Media / Paste SVG -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <!-- Hidden File Input for Direct Upload -->
                                    <input type="file" 
                                           :id="'social_file_' + index" 
                                           accept=".png,.jpg,.jpeg,.webp,.svg,.ico" 
                                           class="hidden" 
                                           @change="uploadFile(index, $event)">

                                    <button type="button" @click="document.getElementById('social_file_' + index).click()" 
                                            class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-semibold rounded-lg transition flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                        <span>Tải ảnh / SVG</span>
                                    </button>

                                    <button type="button" @click="openMediaPicker(index)" 
                                            class="px-2.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium rounded-lg transition flex items-center gap-1 shadow-2xs">
                                        <i class="fa-regular fa-images text-xs text-slate-500"></i>
                                        <span>Thư viện Media</span>
                                    </button>

                                    <button type="button" @click="item.showSvgModal = !item.showSvgModal" 
                                            :class="item.showSvgModal ? 'bg-amber-100 border-amber-300 text-amber-800' : 'bg-white border-slate-200 text-slate-700'"
                                            class="px-2.5 py-1.5 border text-xs font-medium rounded-lg transition flex items-center gap-1 shadow-2xs"
                                            title="Dán mã SVG trực tiếp hoặc dán đường dẫn icon">
                                        <i class="fa-solid fa-code text-xs"></i>
                                        <span>Dán SVG / Link</span>
                                    </button>
                                </div>

                                <!-- Collapsible Direct SVG Code or URL Input -->
                                <div x-show="item.showSvgModal" x-cloak class="p-3 bg-amber-50/60 border border-amber-200 rounded-xl space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-amber-900">Dán mã SVG hoặc URL ảnh:</span>
                                        <button type="button" @click="item.showSvgModal = false" class="text-amber-700 hover:text-amber-900 text-xs">Đóng</button>
                                    </div>
                                    <textarea x-model="item.icon" rows="3" 
                                              placeholder="Dán mã <svg ...>...</svg> hoặc link ảnh https://... hoặc icon class fa-brands fa-..."
                                              class="w-full px-2.5 py-1.5 text-[11px] font-mono bg-white border border-amber-300 rounded-lg text-slate-800 focus:ring-1 focus:ring-amber-500 focus:outline-none leading-relaxed"></textarea>
                                    <p class="text-[10px] text-amber-800 leading-tight">
                                        Mẹo: Bạn có thể dán toàn bộ đoạn mã SVG của icon hoặc đường dẫn ảnh hoặc class FontAwesome.
                                    </p>
                                </div>

                                <!-- Quick Presets Row -->
                                <div>
                                    <span class="block text-[11px] font-semibold text-slate-500 mb-1.5">Chọn nhanh biểu tượng mẫu:</span>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <template x-for="p in presets" :key="p.name">
                                            <button type="button" @click="applyPreset(index, p)" 
                                                    :style="{ borderColor: item.name === p.name ? p.color : '' }"
                                                    :class="item.name === p.name ? 'ring-2 ring-blue-400 bg-blue-50/80 font-bold' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
                                                    class="px-2 py-1 border rounded-lg text-[11px] transition flex items-center gap-1 shadow-2xs" 
                                                    :title="'Áp dụng mẫu ' + p.name">
                                                <i :class="p.icon" :style="{ color: p.color }"></i>
                                                <span x-text="p.name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Name, Link, Color & Options (7 cols) -->
                            <div class="md:col-span-7 space-y-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Tên mạng xã hội <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="item.name" 
                                           required
                                           placeholder="VD: Facebook, Zalo OA, Kênh YouTube..." 
                                           class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-slate-900 shadow-2xs">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Đường dẫn liên kết (Link / URL) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-link text-xs"></i>
                                        </div>
                                        <input type="text" 
                                               x-model="item.link" 
                                               required
                                               placeholder="https://facebook.com/daiphuc68 hoặc https://zalo.me/..." 
                                               class="w-full pl-8 pr-3.5 py-2 text-xs sm:text-sm font-mono bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 shadow-2xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <!-- Brand Color -->
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Màu thương hiệu (Hex)</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" x-model="item.color" 
                                                   class="w-9 h-9 p-0.5 rounded-lg border border-slate-200 cursor-pointer bg-white">
                                            <input type="text" x-model="item.color" 
                                                   placeholder="#1877F2" 
                                                   class="w-full px-2.5 py-1.5 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-700">
                                        </div>
                                    </div>

                                    <!-- Target Window -->
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Hành vi click</label>
                                        <select x-model="item.target" 
                                                class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 focus:ring-1 focus:ring-blue-500">
                                            <option value="_blank">Mở tab mới (Khuyên dùng)</option>
                                            <option value="_self">Chuyển tiếp tại tab hiện tại</option>
                                        </select>
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
                        <i class="fa-solid fa-share-nodes"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700">Chưa có mạng xã hội nào được cấu hình</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Bấm nút bên dưới để thêm mạng xã hội mới hoặc khôi phục danh sách mẫu.</p>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-2">
                        <button type="button" @click="addItem()" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i>
                            <span>Thêm kênh mới</span>
                        </button>
                        <button type="button" @click="resetDefaults()" 
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Khôi phục mẫu chuẩn</span>
                        </button>
                    </div>
                </div>

                <!-- Bottom Add Button -->
                <div class="pt-2 flex items-center justify-between">
                    <button type="button" @click="addItem()" 
                            class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm Kênh Mạng Xã Hội Mới</span>
                    </button>

                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#EC460B] hover:bg-[#d03d09] text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm shadow-[#EC460B]/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Lưu Cấu Hình</span>
                    </button>
                </div>

            </div>

            <!-- Right: Real-time Live Preview & Guide (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Live Preview Sticky Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm sticky top-24 space-y-5">
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Xem Trước Trực Tiếp (Live Demo)</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400">Thời gian thực</span>
                    </div>

                    <!-- Demo 1: Footer Style (As shown on Dai Phuc 68 footer) -->
                    <div class="space-y-2">
                        <span class="block text-[11px] font-bold text-slate-600 uppercase tracking-tight">1. Kiểu Chân Trang (Footer Grid)</span>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5">
                            <h4 class="text-xs font-bold text-neutral-900 tracking-wide">Social Media</h4>
                            <div class="grid grid-cols-2 gap-y-2 gap-x-3 text-xs text-neutral-600">
                                <template x-for="(pItem, pIdx) in items.filter(i => i.is_active)" :key="pIdx">
                                    <a :href="pItem.link || '#'" target="_blank" 
                                       class="hover:text-blue-700 flex items-center gap-2 transition-colors truncate" 
                                       :title="pItem.name + ' - ' + pItem.link">
                                        <!-- Render Icon in footer -->
                                        <div class="w-4 h-4 flex items-center justify-center shrink-0">
                                            <template x-if="isSvgCode(pItem.icon)">
                                                <div class="w-4 h-4 social-svg-preview text-slate-700" x-html="pItem.icon"></div>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && isFaIcon(pItem.icon)">
                                                <i :class="pItem.icon" class="text-sm"></i>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && !isFaIcon(pItem.icon) && pItem.icon">
                                                <img :src="resolveIconUrl(pItem.icon)" class="w-3.5 h-3.5 object-contain">
                                            </template>
                                        </div>
                                        <span class="truncate" x-text="pItem.name || 'Social'"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Demo 2: Contact Page / Header Circle Buttons Style -->
                    <div class="space-y-2">
                        <span class="block text-[11px] font-bold text-slate-600 uppercase tracking-tight">2. Kiểu Nút Tròn (Trang Liên Hệ & Đầu Trang)</span>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                            <span class="text-[11px] font-bold text-neutral-900 uppercase tracking-wide">SOCIAL MEDIA</span>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <template x-for="(pItem, pIdx) in items.filter(i => i.is_active)" :key="pIdx">
                                    <a :href="pItem.link || '#'" target="_blank" 
                                       class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center hover:bg-blue-900 transition-all duration-200 shadow-2xs hover:scale-110 active:scale-95" 
                                       :title="pItem.name">
                                        <div class="w-4 h-4 flex items-center justify-center shrink-0">
                                            <template x-if="isSvgCode(pItem.icon)">
                                                <div class="w-3.5 h-3.5 social-svg-preview text-white" x-html="pItem.icon"></div>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && isFaIcon(pItem.icon)">
                                                <i :class="pItem.icon" class="text-xs"></i>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && !isFaIcon(pItem.icon) && pItem.icon">
                                                <img :src="resolveIconUrl(pItem.icon)" class="w-4 h-4 object-contain brightness-0 invert">
                                            </template>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Demo 3: Brand Colored Badges -->
                    <div class="space-y-2">
                        <span class="block text-[11px] font-bold text-slate-600 uppercase tracking-tight">3. Kiểu Huy Hiệu Màu Thương Hiệu (Brand Pills)</span>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-center gap-2 flex-wrap">
                                <template x-for="(pItem, pIdx) in items.filter(i => i.is_active)" :key="pIdx">
                                    <span :style="{ backgroundColor: pItem.color || '#334155' }"
                                          class="px-2.5 py-1 text-white rounded-lg text-[11px] font-bold shadow-2xs flex items-center gap-1.5">
                                        <div class="w-3.5 h-3.5 flex items-center justify-center shrink-0">
                                            <template x-if="isSvgCode(pItem.icon)">
                                                <div class="w-3.5 h-3.5 social-svg-preview text-white" x-html="pItem.icon"></div>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && isFaIcon(pItem.icon)">
                                                <i :class="pItem.icon" class="text-xs"></i>
                                            </template>
                                            <template x-if="!isSvgCode(pItem.icon) && !isFaIcon(pItem.icon) && pItem.icon">
                                                <img :src="resolveIconUrl(pItem.icon)" class="w-3.5 h-3.5 object-contain brightness-0 invert">
                                            </template>
                                        </div>
                                        <span x-text="pItem.name"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Helpful Tips Box -->
                    <div class="p-4 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-2 text-xs text-blue-900">
                        <div class="font-bold flex items-center gap-1.5 text-blue-800">
                            <i class="fa-solid fa-circle-info text-blue-600"></i>
                            <span>Hướng dẫn &amp; Mẹo:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-slate-600 text-[11px] leading-relaxed">
                            <li><strong>Định dạng icon:</strong> Bạn có thể tải lên ảnh <code>.png</code>, <code>.svg</code>, <code>.webp</code>, chọn từ Thư viện Media hoặc dán mã SVG trực tiếp.</li>
                            <li><strong>Đồng bộ tự động:</strong> Khi bạn lưu, toàn bộ các vị trí như Chân trang (Footer) và Trang Liên hệ sẽ lập tức cập nhật theo danh sách này.</li>
                            <li><strong>Sắp xếp thứ tự:</strong> Nhấn nút mũi tên <i class="fa-solid fa-arrow-up text-[10px]"></i> / <i class="fa-solid fa-arrow-down text-[10px]"></i> để đưa kênh ưu tiên lên đầu.</li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </form>
</div>

@push('scripts')
<script>
function socialManager() {
    return {
        items: @json($items),
        activeMediaTargetIndex: null,
        uploadingIndex: null,

        presets: [
            {
                name: 'Facebook',
                link: 'https://facebook.com/',
                icon: 'fa-brands fa-facebook',
                color: '#1877F2'
            },
            {
                name: 'Zalo',
                link: 'https://zalo.me/',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-full h-full fill-current"><path d="M24 4C13 4 4 12.1 4 22c0 5.7 3 10.8 7.8 14.3-.4 2.4-1.6 5.6-3.8 7.7 0 0 4.8-.4 9.1-3.2 2.2.6 4.5.9 6.9.9 11 0 20-8.1 20-18S35 4 24 4z"/></svg>',
                color: '#0068FF'
            },
            {
                name: 'YouTube',
                link: 'https://youtube.com/',
                icon: 'fa-brands fa-youtube',
                color: '#FF0000'
            },
            {
                name: 'TikTok',
                link: 'https://tiktok.com/',
                icon: 'fa-brands fa-tiktok',
                color: '#000000'
            },
            {
                name: 'Instagram',
                link: 'https://instagram.com/',
                icon: 'fa-brands fa-instagram',
                color: '#E4405F'
            },
            {
                name: 'LinkedIn',
                link: 'https://linkedin.com/',
                icon: 'fa-brands fa-linkedin',
                color: '#0A66C2'
            },
            {
                name: 'X (Twitter)',
                link: 'https://x.com/',
                icon: 'fa-brands fa-x-twitter',
                color: '#000000'
            },
            {
                name: 'Telegram',
                link: 'https://t.me/',
                icon: 'fa-brands fa-telegram',
                color: '#26A5E4'
            },
            {
                name: 'WhatsApp',
                link: 'https://wa.me/',
                icon: 'fa-brands fa-whatsapp',
                color: '#25D366'
            },
            {
                name: 'Threads',
                link: 'https://threads.net/',
                icon: 'fa-brands fa-threads',
                color: '#000000'
            },
            {
                name: 'Shopee',
                link: 'https://shopee.vn/',
                icon: 'fa-solid fa-bag-shopping',
                color: '#EE4D2D'
            },
            {
                name: 'Website',
                link: 'https://',
                icon: 'fa-solid fa-globe',
                color: '#051650'
            }
        ],

        isSvgCode(icon) {
            if (!icon || typeof icon !== 'string') return false;
            return icon.trim().startsWith('<svg');
        },

        isFaIcon(icon) {
            if (!icon || typeof icon !== 'string') return false;
            return icon.startsWith('fa-') || icon.includes(' fa-');
        },

        resolveIconUrl(icon) {
            if (!icon || typeof icon !== 'string') return '';
            if (icon.startsWith('http://') || icon.startsWith('https://')) return icon;
            if (icon.startsWith('/storage') || icon.startsWith('storage/')) {
                return '{{ url("/") }}/' + icon.replace(/^\/+/, '');
            }
            return '{{ asset("") }}' + icon.replace(/^\/+/, '');
        },

        getIconSourceLabel(icon) {
            if (!icon) return 'Chưa chọn';
            if (this.isSvgCode(icon)) return 'Mã SVG trực tiếp';
            if (this.isFaIcon(icon)) return 'FontAwesome (' + icon + ')';
            const clean = icon.split('?')[0];
            const ext = clean.split('.').pop();
            return 'File ảnh (' + ext.toUpperCase() + ')';
        },

        addItem() {
            this.items.push({
                id: Date.now(),
                name: '',
                link: '',
                icon: 'fa-solid fa-share-nodes',
                icon_type: 'fa',
                color: '#051650',
                target: '_blank',
                is_active: true,
                showSvgModal: false
            });
        },

        removeItem(index) {
            if (confirm('Bạn có chắc chắn muốn xóa mạng xã hội này không?')) {
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

        applyPreset(index, preset) {
            const item = this.items[index];
            if (!item.name || item.name.startsWith('Mạng xã hội #')) {
                item.name = preset.name;
            }
            if (!item.link || item.link === '#' || item.link === 'https://') {
                item.link = preset.link;
            }
            item.icon = preset.icon;
            item.color = preset.color;
            item.is_active = true;
        },

        resetDefaults() {
            if (confirm('Bạn có muốn khôi phục về danh sách mạng xã hội mẫu chuẩn?')) {
                this.items = @json($defaultItems);
            }
        },

        openMediaPicker(index) {
            this.activeMediaTargetIndex = index;
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: {
                    type: 'image',
                    target: 'social_icon_' + index
                }
            }));
        },

        handleMediaSelected(detail) {
            const url = detail.url || detail.value || (detail.file ? detail.file.url : '');
            if (url && this.activeMediaTargetIndex !== null && this.items[this.activeMediaTargetIndex]) {
                this.items[this.activeMediaTargetIndex].icon = url;
                this.items[this.activeMediaTargetIndex].icon_type = 'image';
                this.activeMediaTargetIndex = null;
            }
        },

        async uploadFile(index, event) {
            const file = event.target.files[0];
            if (!file) return;

            this.uploadingIndex = index;
            const formData = new FormData();
            formData.append('path', 'social');
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
                        this.items[index].icon = result.uploaded[0].url;
                        this.items[index].icon_type = 'image';
                        if (typeof window.showAlert === 'function') {
                            window.showAlert('Tải lên icon thành công!', 'success');
                        }
                    } else if (result.warnings && result.warnings.length > 0) {
                        alert(result.warnings.join("\n"));
                    }
                } else {
                    const err = await response.json();
                    alert('Lỗi tải file: ' + (err.message || response.statusText));
                }
            } catch (error) {
                console.error('Upload error', error);
                alert('Có lỗi xảy ra khi tải file lên máy chủ.');
            } finally {
                this.uploadingIndex = null;
                event.target.value = '';
            }
        },

        beforeSubmit() {
            // Đảm bảo dữ liệu JSON sạch trước khi gửi form
            return true;
        }
    };
}
</script>
@endpush
@endsection

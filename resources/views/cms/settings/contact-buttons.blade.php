@extends('cms.layouts.app')

@section('title', 'Cấu hình Nút Liên Hệ Nổi')
@section('page-title', 'Nút Liên Hệ Nổi')

@section('content')
<x-media-picker-modal />

@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = \Route::has('admin.settings.save')
        ? route('admin.settings.save')
        : (\Route::has('cms.settings.save') ? route('cms.settings.save') : url('/admin/settings'));
    $mediaUploadUrl = \Route::has('admin.media.upload')
        ? route('admin.media.upload')
        : url('/admin/media/upload');

    // 1. Initial Call Button Data
    $savedCallButton = setting('contact_call_button');
    if (is_string($savedCallButton)) {
        $savedCallButton = json_decode($savedCallButton, true);
    }

    // Fallback from legacy contact_buttons if call button not initialized
    $legacyButtons = setting('contact_buttons');
    if (is_string($legacyButtons)) {
        $legacyButtons = json_decode($legacyButtons, true);
    }
    if (!is_array($legacyButtons)) {
        $legacyButtons = [];
    }

    $legacyPhoneItem = collect($legacyButtons)->firstWhere('type', 'phone');

    if (!is_array($savedCallButton) || empty($savedCallButton)) {
        $callButtonData = [
            'enabled' => $legacyPhoneItem ? !empty($legacyPhoneItem['enabled']) : true,
            'phone' => !empty($legacyPhoneItem['value']) ? $legacyPhoneItem['value'] : (setting_string('contact_phone') ?: '0988888888'),
            'label' => $legacyPhoneItem['label'] ?? 'Gọi ngay',
            'color' => $legacyPhoneItem['color'] ?? '#10b981',
            'pulse' => true,
            'icon_type' => (!empty($legacyPhoneItem['icon_url'])) ? 'image' : 'svg',
            'icon_url' => $legacyPhoneItem['icon_url'] ?? '',
        ];
    } else {
        $callButtonData = [
            'enabled' => !empty($savedCallButton['enabled']),
            'phone' => $savedCallButton['phone'] ?? (setting_string('contact_phone') ?: ''),
            'label' => $savedCallButton['label'] ?? 'Gọi ngay',
            'color' => $savedCallButton['color'] ?? '#10b981',
            'pulse' => isset($savedCallButton['pulse']) ? (bool)$savedCallButton['pulse'] : true,
            'icon_type' => $savedCallButton['icon_type'] ?? 'svg',
            'icon_url' => $savedCallButton['icon_url'] ?? '',
        ];
    }

    // 2. Initial Action Buttons Data
    $savedActionButtons = setting('contact_action_buttons');
    if (is_string($savedActionButtons)) {
        $savedActionButtons = json_decode($savedActionButtons, true);
    }

    $defaultActionPresets = [
        [
            'id' => 'zalo',
            'type' => 'zalo',
            'label' => 'Zalo',
            'value' => setting_string('social_zalo') ?: setting_string('contact_phone', ''),
            'color' => '#0068ff',
            'enabled' => true,
            'icon_type' => 'svg',
            'icon_url' => '',
            'open_new_tab' => true,
        ],
        [
            'id' => 'messenger',
            'type' => 'messenger',
            'label' => 'Messenger',
            'value' => setting_string('social_facebook', ''),
            'color' => '#0084ff',
            'enabled' => true,
            'icon_type' => 'svg',
            'icon_url' => '',
            'open_new_tab' => true,
        ],
        [
            'id' => 'sms',
            'type' => 'sms',
            'label' => 'SMS',
            'value' => setting_string('contact_phone', ''),
            'color' => '#f59e0b',
            'enabled' => false,
            'icon_type' => 'svg',
            'icon_url' => '',
            'open_new_tab' => false,
        ],
    ];

    if (!is_array($savedActionButtons) || empty($savedActionButtons)) {
        // Fallback from legacy buttons excluding phone
        $nonPhoneLegacy = collect($legacyButtons)->filter(fn($b) => ($b['type'] ?? '') !== 'phone')->values()->all();
        if (!empty($nonPhoneLegacy)) {
            $actionButtonsData = [];
            foreach ($nonPhoneLegacy as $idx => $b) {
                $type = $b['type'] ?? 'zalo';
                $def = collect($defaultActionPresets)->firstWhere('type', $type) ?? [];
                $actionButtonsData[] = [
                    'id' => $type . '_' . $idx,
                    'type' => $type,
                    'label' => $b['label'] ?? ($def['label'] ?? ucfirst($type)),
                    'value' => $b['value'] ?? ($def['value'] ?? ''),
                    'color' => $b['color'] ?? ($def['color'] ?? '#0068ff'),
                    'enabled' => !empty($b['enabled']),
                    'icon_type' => (!empty($b['icon_url'])) ? 'image' : 'svg',
                    'icon_url' => $b['icon_url'] ?? '',
                    'open_new_tab' => true,
                ];
            }
        } else {
            $actionButtonsData = $defaultActionPresets;
        }
    } else {
        $actionButtonsData = $savedActionButtons;
    }
@endphp

<div x-data="contactButtonsApp()" 
     @media-picker-selected.window="handleMediaSelected($event.detail)"
     @media-selected.window="handleMediaSelected($event.detail)"
     class="max-w-5xl mx-auto px-4 py-6 space-y-6 pb-20">

    {{-- Alert Banner --}}
    @if(session('alert'))
        <div class="p-4 rounded-xl {{ session('alert.type') === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }} flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 {{ session('alert.type') === 'success' ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-semibold">{{ session('alert.message') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Top Header & Quick Save Bar --}}
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-4 z-20 backdrop-blur-md bg-white/95">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    Cấu Hình Nút Liên Hệ Nổi
                    <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Nút Gọi &amp; Action
                    </span>
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Tùy biến Hotline gọi nhanh và các kênh kết nối khách hàng (Zalo, Messenger, SMS...) với Icon &amp; Hình ảnh.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end md:self-auto">
            <button type="button" 
                    @click="submitForm()" 
                    :disabled="saving"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm shadow-emerald-600/30 transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50">
                <template x-if="!saving">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="saving">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </template>
                <span x-text="saving ? 'Đang lưu...' : 'Lưu Cài Đặt'"></span>
            </button>
        </div>
    </div>

    {{-- Main Form --}}
    <form id="contactButtonsForm" action="{{ $settingsSaveUrl }}" method="POST" @submit.prevent="submitForm">
        @csrf
        <input type="hidden" name="page" value="contact-buttons">

        {{-- Hidden JSON storage --}}
        <input type="hidden" name="contact_buttons_desktop_enabled" :value="desktopEnabled ? 1 : 0">
        <input type="hidden" name="contact_buttons_desktop_position" :value="desktopPosition">
        <input type="hidden" name="contact_buttons_desktop_margin_v" :value="desktopMarginV">
        <input type="hidden" name="contact_buttons_desktop_margin_h" :value="desktopMarginH">
        <input type="hidden" name="contact_buttons_mobile_enabled" :value="mobileEnabled ? 1 : 0">
        <input type="hidden" name="contact_buttons_mobile_position" :value="mobilePosition">
        <input type="hidden" name="contact_buttons_mobile_margin_v" :value="mobileMarginV">
        <input type="hidden" name="contact_buttons_mobile_margin_h" :value="mobileMarginH">
        <input type="hidden" name="contact_buttons_style" :value="selectedStyle">

        {{-- Group 1 & Group 2 JSON fields --}}
        <input type="hidden" name="contact_call_button" :value="JSON.stringify(callButton)">
        <input type="hidden" name="contact_action_buttons" :value="JSON.stringify(actionButtons)">
        {{-- Unified contact_buttons for backward compatibility --}}
        <input type="hidden" name="contact_buttons" :value="JSON.stringify(getUnifiedButtons())">
        <input type="hidden" name="contact_phone" :value="callButton.phone || ''">

        {{-- ======================================================== --}}
        {{-- CARD: VỊ TRÍ & HIỂN THỊ CHUNG (COMPACT GENERAL SETTINGS)  --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden mb-6">
            <div class="px-5 py-4 bg-gray-50/70 border-b border-gray-200/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-gray-200 flex items-center justify-center text-gray-700 text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Cài Đặt Vị Trí &amp; Kiểu Dáng Hiển Thị</h3>
                </div>
                <span class="text-xs text-gray-500">Giao diện tinh gọn, tự động tối ưu Desktop &amp; Mobile</span>
            </div>

            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Desktop Config --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Màn Hình Desktop
                            </span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="desktopEnabled" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1" :class="!desktopEnabled ? 'opacity-40 pointer-events-none' : ''">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Vị trí góc</label>
                                <select x-model="desktopPosition" class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white focus:ring-1 focus:ring-blue-500">
                                    <template x-for="pos in positions" :key="pos.value">
                                        <option :value="pos.value" x-text="pos.label"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-1/2">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cách dọc</label>
                                    <input type="number" x-model="desktopMarginV" class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white text-center">
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cách ngang</label>
                                    <input type="number" x-model="desktopMarginH" class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white text-center">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Config --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Điện Thoại (Mobile)
                            </span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="mobileEnabled" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1" :class="!mobileEnabled ? 'opacity-40 pointer-events-none' : ''">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Vị trí góc</label>
                                <select x-model="mobilePosition" class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white focus:ring-1 focus:ring-purple-500">
                                    <template x-for="pos in positions" :key="pos.value">
                                        <option :value="pos.value" x-text="pos.label"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-1/2">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cách dọc</label>
                                    <input type="number" x-model="mobileMarginV" class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white text-center">
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cách ngang</label>
                                    <input type="number" x-model="mobileMarginH" class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white text-center">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Style selector (Shape) --}}
                <div class="flex items-center justify-between flex-wrap gap-3 pt-2 border-t border-gray-100">
                    <span class="text-xs font-semibold text-gray-700">Kiểu dáng nút:</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <template x-for="st in styles" :key="st.value">
                            <button type="button" 
                                    @click="selectedStyle = st.value"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-all cursor-pointer flex items-center gap-2"
                                    :class="selectedStyle === st.value ? 'bg-emerald-50 border-emerald-500 text-emerald-800 ring-1 ring-emerald-500 shadow-2xs' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                                <span class="w-3.5 h-3.5 bg-emerald-500 inline-block shadow-2xs" :class="st.shapeClass"></span>
                                <span x-text="st.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- PHẦN 1: NÚT GỌI ĐIỆN THOẠI / HOTLINE (CALL BUTTON)        --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-2xl border-2 border-emerald-500/30 shadow-sm overflow-hidden mb-6 transition-all">
            {{-- Header card --}}
            <div class="px-5 py-4 bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent border-b border-emerald-500/20 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            1. NÚT GỌI HOTLINE (Call Button)
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                Ưu tiên số 1
                            </span>
                        </h2>
                        <p class="text-xs text-gray-500">Kích hoạt cuộc gọi trực tiếp (tel:) tới số Hotline hỗ trợ khách hàng nhanh nhất.</p>
                    </div>
                </div>

                {{-- Enable / Disable Switch --}}
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold" :class="callButton.enabled ? 'text-emerald-700' : 'text-gray-400'" x-text="callButton.enabled ? 'Đang Bật' : 'Đang Tắt'"></span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="callButton.enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-5 sm:p-6" x-show="callButton.enabled" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Left column: Call button info --}}
                    <div class="lg:col-span-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Số Điện Thoại Hotline <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <input type="text" 
                                       x-model="callButton.phone" 
                                       placeholder="Ví dụ: 0988 888 888" 
                                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Hệ thống sẽ tự động gán đường dẫn <code class="text-emerald-700 font-mono">tel:0988888888</code></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Tiêu đề / Nhãn Tooltip
                            </label>
                            <input type="text" 
                                   x-model="callButton.label" 
                                   placeholder="Ví dụ: Gọi ngay, Hotline 24/7" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        </div>

                        {{-- Ring / Pulse Wave Effect --}}
                        <div class="p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/40 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-emerald-900 block">Hiệu ứng Rung Lắc &amp; Sóng Tỏa</span>
                                <span class="text-[11px] text-emerald-700">Tạo hiệu ứng rung chuông và vòng sóng lan tỏa thu hút người dùng bấm gọi</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="callButton.pulse" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Right column: Visual icon & Image upload component --}}
                    <div class="lg:col-span-6 space-y-4 bg-gray-50/60 p-4 rounded-xl border border-gray-200/80">
                        {{-- Color Picker & Quick presets --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Màu Nền Nút Gọi
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="callButton.color" class="w-10 h-10 p-0.5 rounded-lg border border-gray-300 cursor-pointer bg-white">
                                <input type="text" x-model="callButton.color" class="w-28 text-xs font-mono border border-gray-300 rounded-lg px-2.5 py-2 uppercase bg-white">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="callButton.color = '#10b981'" class="w-6 h-6 rounded-full bg-[#10b981] border border-white shadow-xs cursor-pointer" title="Xanh lá"></button>
                                    <button type="button" @click="callButton.color = '#EC460B'" class="w-6 h-6 rounded-full bg-[#EC460B] border border-white shadow-xs cursor-pointer" title="Cam thương hiệu"></button>
                                    <button type="button" @click="callButton.color = '#0068ff'" class="w-6 h-6 rounded-full bg-[#0068ff] border border-white shadow-xs cursor-pointer" title="Xanh dương"></button>
                                    <button type="button" @click="callButton.color = '#ef4444'" class="w-6 h-6 rounded-full bg-[#ef4444] border border-white shadow-xs cursor-pointer" title="Đỏ nổi bật"></button>
                                </div>
                            </div>
                        </div>

                        {{-- Icon Mode Selector (SVG vs Custom Image) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Biểu Tượng Nút Gọi
                            </label>
                            <div class="flex items-center gap-2 mb-3">
                                <button type="button" 
                                        @click="callButton.icon_type = 'svg'"
                                        class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold border transition-all cursor-pointer flex items-center justify-center gap-1.5"
                                        :class="callButton.icon_type === 'svg' ? 'bg-white border-emerald-500 text-emerald-700 shadow-xs ring-1 ring-emerald-500' : 'border-gray-300 text-gray-500 hover:bg-gray-100'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                    Biểu Tượng SVG Chuẩn
                                </button>
                                <button type="button" 
                                        @click="callButton.icon_type = 'image'"
                                        class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold border transition-all cursor-pointer flex items-center justify-center gap-1.5"
                                        :class="callButton.icon_type === 'image' ? 'bg-white border-emerald-500 text-emerald-700 shadow-xs ring-1 ring-emerald-500' : 'border-gray-300 text-gray-500 hover:bg-gray-100'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Ảnh / SVG Tùy Chỉnh
                                </button>
                            </div>

                            {{-- SVG Preview Mode --}}
                            <div x-show="callButton.icon_type === 'svg'" class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
                                <div class="w-11 h-11 rounded-full flex items-center justify-center text-white shrink-0 shadow-xs"
                                     :style="'background-color: ' + callButton.color">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-bold text-gray-800 block">Vector SVG Điện Thoại Chuẩn</span>
                                    <span>Tự động tối ưu sắc nét, tự co giãn mượt mà trên mọi thiết bị.</span>
                                </div>
                            </div>

                            {{-- Custom Image / SVG Upload Component --}}
                            <div x-show="callButton.icon_type === 'image'" class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
                                    {{-- Image Preview Thumbnail --}}
                                    <div class="w-12 h-12 rounded-xl border border-gray-200 bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden relative"
                                         :style="'background-color: ' + callButton.color">
                                        <template x-if="callButton.icon_url">
                                            <img :src="callButton.icon_url" class="w-8 h-8 object-contain" alt="Call icon">
                                        </template>
                                        <template x-if="!callButton.icon_url">
                                            <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </template>
                                    </div>

                                    {{-- Actions: Upload & Media Picker --}}
                                    <div class="flex-1 space-y-1.5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            {{-- Direct File Upload input --}}
                                            <input type="file" 
                                                   x-ref="callFileInput" 
                                                   @change="uploadCallFile($event)" 
                                                   accept=".png,.jpg,.jpeg,.webp,.svg,.ico" 
                                                   class="hidden">
                                            <button type="button" 
                                                    @click="$refs.callFileInput.click()"
                                                    :disabled="callUploading"
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs disabled:opacity-50">
                                                <template x-if="!callUploading">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                </template>
                                                <template x-if="callUploading">
                                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </template>
                                                <span x-text="callUploading ? 'Đang tải...' : 'Tải ảnh lên'"></span>
                                            </button>

                                            {{-- Media Library Picker --}}
                                            <button type="button" 
                                                    @click="openMediaPicker('call_button')"
                                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer border border-gray-300">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                <span>Thư viện Media</span>
                                            </button>

                                            {{-- Clear image button --}}
                                            <template x-if="callButton.icon_url">
                                                <button type="button" 
                                                        @click="callButton.icon_url = ''"
                                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg text-xs transition cursor-pointer"
                                                        title="Gỡ ảnh này">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        {{-- Link image directly (Link được hình ảnh) --}}
                                        <div class="relative">
                                            <input type="text" 
                                                   x-model="callButton.icon_url" 
                                                   placeholder="Dán link ảnh trực tiếp (vd: /storage/media/hotline.svg)" 
                                                   class="w-full text-[11px] border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white text-gray-700 font-mono">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- PHẦN 2: CÁC NÚT HÀNH ĐỘNG (ACTION BUTTONS LIST)           --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden mb-6">
            {{-- Header card --}}
            <div class="px-5 py-4 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-transparent border-b border-gray-200/80 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            2. CÁC NÚT HÀNH ĐỘNG (Action Buttons)
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-800"
                                  x-text="actionButtons.filter(b => b.enabled).length + ' nút đang bật'">
                            </span>
                        </h2>
                        <p class="text-xs text-gray-500">Các nút kết nối nhanh: Zalo, Messenger, SMS, Telegram, WhatsApp, Email, Fanpage...</p>
                    </div>
                </div>

                {{-- Add Button CTA --}}
                <button type="button" 
                        @click="addActionButton()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-xs cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Thêm Nút Action</span>
                </button>
            </div>

            {{-- Action Buttons List --}}
            <div class="p-5 sm:p-6 space-y-4">
                <template x-for="(btn, index) in actionButtons" :key="btn.id || index">
                    <div class="border rounded-xl transition-all shadow-2xs overflow-hidden"
                         :class="btn.enabled ? 'border-gray-200 bg-white' : 'border-gray-200/60 bg-gray-50/50 opacity-75'">
                        
                        {{-- Button Card Header --}}
                        <div class="px-4 py-3 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3">
                                {{-- Reorder Arrows --}}
                                <div class="flex flex-col gap-0.5 text-gray-400">
                                    <button type="button" 
                                            @click="moveUp(index)" 
                                            :disabled="index === 0"
                                            class="hover:text-blue-600 disabled:opacity-20 cursor-pointer p-0.5" 
                                            title="Di chuyển lên">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" 
                                            @click="moveDown(index)" 
                                            :disabled="index === actionButtons.length - 1"
                                            class="hover:text-blue-600 disabled:opacity-20 cursor-pointer p-0.5" 
                                            title="Di chuyển xuống">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>

                                {{-- Icon Mini Preview --}}
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 shadow-2xs text-white"
                                     :style="'background-color: ' + (btn.color || '#0068ff')">
                                    <template x-if="btn.icon_type === 'image' && btn.icon_url">
                                        <img :src="btn.icon_url" class="w-5 h-5 object-contain" :alt="btn.label">
                                    </template>
                                    <template x-if="btn.icon_type !== 'image' || !btn.icon_url">
                                        <div class="flex items-center justify-center" x-html="getChannelSvg(btn.type)"></div>
                                    </template>
                                </div>

                                {{-- Badge & Label Title --}}
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-800" x-text="btn.label || 'Chưa đặt tên'"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                              :class="getChannelBadgeClass(btn.type)" 
                                              x-text="btn.type"></span>
                                    </div>
                                    <p class="text-[11px] text-gray-400 truncate max-w-xs sm:max-w-md" x-text="btn.value || 'Chưa nhập link/số'"></p>
                                </div>
                            </div>

                            {{-- Right: Toggle Switch & Delete --}}
                            <div class="flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="btn.enabled" class="sr-only peer">
                                    <div class="w-8 h-4.5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>

                                <button type="button" 
                                        @click="deleteActionButton(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer"
                                        title="Xóa nút này">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Button Card Content --}}
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
                                {{-- Channel Type --}}
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                                        Kênh / Loại
                                    </label>
                                    <select x-model="btn.type" 
                                            @change="onChannelTypeChange(btn)"
                                            class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-2 bg-white font-medium focus:ring-1 focus:ring-blue-500">
                                        <option value="zalo">Zalo Chat</option>
                                        <option value="messenger">Facebook Messenger</option>
                                        <option value="sms">Tin nhắn SMS</option>
                                        <option value="telegram">Telegram</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="email">Email</option>
                                        <option value="link">Link Tùy Chỉnh (Custom)</option>
                                    </select>
                                </div>

                                {{-- Label --}}
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                                        Tên Hiển Thị (Tooltip)
                                    </label>
                                    <input type="text" 
                                           x-model="btn.label" 
                                           placeholder="Nhập tên nút..." 
                                           class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-2 bg-white font-medium focus:ring-1 focus:ring-blue-500">
                                </div>

                                {{-- Value / Target Link --}}
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                                        Link / Số Điện Thoại / ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="btn.value" 
                                           :placeholder="getChannelPlaceholder(btn.type)" 
                                           class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-2 bg-white font-medium focus:ring-1 focus:ring-blue-500">
                                </div>

                                {{-- Color --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                                        Màu Nền
                                    </label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="color" x-model="btn.color" class="w-8 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer bg-white shrink-0">
                                        <input type="text" x-model="btn.color" class="w-full text-[11px] font-mono border border-gray-300 rounded-lg px-2 py-1.5 uppercase bg-white">
                                    </div>
                                </div>
                            </div>

                            {{-- Icon / Image Setting for Action Button --}}
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/80 space-y-2.5">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <span class="text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                                        Biểu tượng (Icon / SVG / Hình ảnh):
                                    </span>

                                    <div class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-gray-200">
                                        <button type="button" 
                                                @click="btn.icon_type = 'svg'"
                                                class="px-2.5 py-1 text-[11px] font-semibold rounded transition cursor-pointer"
                                                :class="btn.icon_type !== 'image' ? 'bg-blue-600 text-white shadow-2xs' : 'text-gray-500 hover:text-gray-700'">
                                            SVG Mặc Định
                                        </button>
                                        <button type="button" 
                                                @click="btn.icon_type = 'image'"
                                                class="px-2.5 py-1 text-[11px] font-semibold rounded transition cursor-pointer"
                                                :class="btn.icon_type === 'image' ? 'bg-blue-600 text-white shadow-2xs' : 'text-gray-500 hover:text-gray-700'">
                                            Ảnh / SVG Tùy Chỉnh
                                        </button>
                                    </div>
                                </div>

                                {{-- When Custom Image is chosen --}}
                                <div x-show="btn.icon_type === 'image'" class="flex items-center gap-3 flex-wrap">
                                    {{-- Mini Image Preview --}}
                                    <div class="w-10 h-10 rounded-lg border border-gray-200 bg-white flex items-center justify-center shrink-0 overflow-hidden"
                                         :style="'background-color: ' + (btn.color || '#0068ff')">
                                        <template x-if="btn.icon_url">
                                            <img :src="btn.icon_url" class="w-7 h-7 object-contain" :alt="btn.label">
                                        </template>
                                        <template x-if="!btn.icon_url">
                                            <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </template>
                                    </div>

                                    {{-- Upload button --}}
                                    <input type="file" 
                                           :id="'action_file_' + index" 
                                           @change="uploadActionFile($event, index)" 
                                           accept=".png,.jpg,.jpeg,.webp,.svg,.ico" 
                                           class="hidden">
                                    <button type="button" 
                                            @click="document.getElementById('action_file_' + index).click()"
                                            :disabled="btn.uploading"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs disabled:opacity-50">
                                        <template x-if="!btn.uploading">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </template>
                                        <template x-if="btn.uploading">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </template>
                                        <span x-text="btn.uploading ? 'Đang tải...' : 'Tải ảnh lên'"></span>
                                    </button>

                                    {{-- Media Library Picker --}}
                                    <button type="button" 
                                            @click="openMediaPicker(index)"
                                            class="px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer border border-gray-300">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <span>Thư viện Media</span>
                                    </button>

                                    {{-- Image URL direct input (Link được hình ảnh) --}}
                                    <div class="flex-1 min-w-[200px]">
                                        <input type="text" 
                                               x-model="btn.icon_url" 
                                               placeholder="Dán link ảnh (vd: /storage/media/zalo.png)" 
                                               class="w-full text-[11px] border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white text-gray-700 font-mono">
                                    </div>

                                    {{-- Clear button --}}
                                    <template x-if="btn.icon_url">
                                        <button type="button" 
                                                @click="btn.icon_url = ''"
                                                class="text-xs text-red-500 hover:text-red-700 px-2 py-1 font-medium cursor-pointer"
                                                title="Gỡ ảnh này">
                                            Gỡ ảnh
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="actionButtons.length === 0" class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-gray-400">
                    <p class="text-sm">Chưa có nút hành động nào.</p>
                    <button type="button" @click="addActionButton()" class="mt-2 text-xs font-semibold text-blue-600 hover:underline">
                        + Bấm vào đây để thêm nút hành động đầu tiên
                    </button>
                </div>
            </div>
        </div>

        {{-- Bottom Fixed Save Bar --}}
        <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Hệ thống tự động đồng bộ hóa trên mọi giao diện Desktop &amp; Mobile.</span>
            </div>

            <button type="submit" 
                    :disabled="saving"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-600/30 transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50">
                <template x-if="!saving">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="saving">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </template>
                <span x-text="saving ? 'Đang lưu cài đặt...' : 'Lưu Tất Cả Cài Đặt'"></span>
            </button>
        </div>
    </form>
</div>

<script>
function contactButtonsApp() {
    return {
        saving: false,
        desktopEnabled: {{ setting('contact_buttons_desktop_enabled', true) ? 'true' : 'false' }},
        desktopPosition: '{{ setting('contact_buttons_desktop_position', 'bottom-right') }}',
        desktopMarginV: {{ (int)setting('contact_buttons_desktop_margin_v', 20) }},
        desktopMarginH: {{ (int)setting('contact_buttons_desktop_margin_h', 20) }},
        mobileEnabled: {{ setting('contact_buttons_mobile_enabled', true) ? 'true' : 'false' }},
        mobilePosition: '{{ setting('contact_buttons_mobile_position', 'bottom-right') }}',
        mobileMarginV: {{ (int)setting('contact_buttons_mobile_margin_v', 20) }},
        mobileMarginH: {{ (int)setting('contact_buttons_mobile_margin_h', 20) }},
        selectedStyle: '{{ setting('contact_buttons_style', 'circle') }}',

        // 1. Call Button State
        callButton: @json($callButtonData),
        callUploading: false,

        // 2. Action Buttons State
        actionButtons: @json($actionButtonsData),

        // Media Picker active target
        activeMediaTarget: null,

        positions: [
            { value: 'bottom-right', label: 'Dưới - Phải (Mặc định)' },
            { value: 'bottom-left', label: 'Dưới - Trái' },
            { value: 'top-right', label: 'Trên - Phải' },
            { value: 'top-left', label: 'Trên - Trái' }
        ],

        styles: [
            { value: 'circle', label: 'Tròn (Circle)', shapeClass: 'rounded-full' },
            { value: 'rounded', label: 'Bo Góc (Rounded)', shapeClass: 'rounded-md' },
            { value: 'square', label: 'Vuông (Square)', shapeClass: 'rounded-none' },
            { value: 'shadow', label: 'Đổ Bóng (Shadow)', shapeClass: 'rounded-lg shadow-md' }
        ],

        // Channel SVGs definitions for instant rendering
        channelSvgs: {
            phone: `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>`,
            zalo: `<svg class="w-4 h-4" viewBox="0 0 48 48" fill="none"><path d="M24 4C12.95 4 4 12.28 4 22.5c0 5.86 2.94 11.08 7.56 14.54l-1.92 6.72c-.17.61.43 1.14.99.87l7.73-3.77c1.82.43 3.71.64 5.64.64 11.05 0 20-8.28 20-18.5S35.05 4 24 4z" fill="#0068FF"/><path d="M12.5 28.5l6.5-8.2h-6.2v-2.8h9.6v2.5l-6.5 8.2h6.7v2.8h-10.1v-2.5zm12.3-11h3.1v13.5h-3.1V17.5zm1.5-1.9c1 0 1.8-.8 1.8-1.8s-.8-1.8-1.8-1.8-1.8.8-1.8 1.8.8 1.8 1.8 1.8zm5.1 9.8c0-3.5 2.6-6.3 6.1-6.3s6.1 2.8 6.1 6.3-2.6 6.3-6.1 6.3-6.1-2.8-6.1-6.3zm9.1 0c0-2-1.3-3.6-3-3.6s-3 1.6-3 3.6 1.3 3.6 3 3.6 3-1.6 3-3.6z" fill="#ffffff"/></svg>`,
            messenger: `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.515 3.735 7.199V22l3.39-1.86c.928.257 1.91.396 2.875.396 5.523 0 10-4.145 10-9.258C22 6.145 17.523 2 12 2zm1.066 12.443l-2.583-2.755-5.045 2.755 5.55-5.895 2.646 2.755 4.982-2.755-5.55 5.895z"/></svg>`,
            sms: `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>`,
            telegram: `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>`,
            whatsapp: `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.15C9.34 7.15 9.03 7.22 8.78 7.49C8.53 7.76 7.82 8.43 7.82 9.78C7.82 11.13 8.81 12.43 8.94 12.61C9.08 12.79 10.88 15.57 13.64 16.76C14.3 17.04 14.81 17.21 15.21 17.34C15.87 17.55 16.48 17.52 16.96 17.45C17.5 17.37 18.61 16.78 18.84 16.13C19.08 15.48 19.08 14.93 19.01 14.81C18.94 14.69 18.75 14.62 18.47 14.48C18.18 14.34 16.76 13.64 16.5 13.55C16.24 13.45 16.05 13.41 15.86 13.69C15.67 13.97 15.14 14.62 14.98 14.81C14.81 14.99 14.65 15.02 14.37 14.88C14.09 14.74 13.18 14.44 12.1 13.48C11.26 12.73 10.7 11.8 10.53 11.52C10.37 11.24 10.51 11.09 10.66 10.95C10.79 10.82 10.95 10.61 11.09 10.45C11.23 10.28 11.28 10.16 11.37 9.98C11.47 9.79 11.42 9.63 11.35 9.49C11.28 9.35 10.72 7.98 10.49 7.42C10.26 6.88 10.03 6.95 9.86 6.94C9.7 6.94 9.53 7.15 9.53 7.15Z"/></svg>`,
            email: `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
            link: `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71m-2.47 2.48a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>`
        },

        getChannelSvg(type) {
            return this.channelSvgs[type] || this.channelSvgs.link;
        },

        getChannelBadgeClass(type) {
            switch(type) {
                case 'zalo': return 'bg-blue-100 text-blue-800';
                case 'messenger': return 'bg-sky-100 text-sky-800';
                case 'sms': return 'bg-amber-100 text-amber-800';
                case 'telegram': return 'bg-cyan-100 text-cyan-800';
                case 'whatsapp': return 'bg-emerald-100 text-emerald-800';
                case 'email': return 'bg-red-100 text-red-800';
                default: return 'bg-indigo-100 text-indigo-800';
            }
        },

        getChannelPlaceholder(type) {
            switch(type) {
                case 'zalo': return 'Số ĐT Zalo hoặc link https://zalo.me/...';
                case 'messenger': return 'Username Facebook hoặc link https://m.me/...';
                case 'sms': return 'Số điện thoại nhận tin nhắn SMS';
                case 'telegram': return 'Username Telegram hoặc link https://t.me/...';
                case 'whatsapp': return 'Số ĐT WhatsApp quốc tế';
                case 'email': return 'Địa chỉ email (vd: contact@daiphuc68.vn)';
                default: return 'Nhập đường dẫn web (https://...)';
            }
        },

        onChannelTypeChange(btn) {
            const defaults = {
                zalo: { label: 'Zalo', color: '#0068ff' },
                messenger: { label: 'Messenger', color: '#0084ff' },
                sms: { label: 'SMS', color: '#f59e0b' },
                telegram: { label: 'Telegram', color: '#229ED9' },
                whatsapp: { label: 'WhatsApp', color: '#25D366' },
                email: { label: 'Email', color: '#ea4335' },
                link: { label: 'Liên hệ', color: '#4f46e5' }
            };

            const conf = defaults[btn.type] || defaults.link;
            if (!btn.label || Object.values(defaults).some(d => d.label === btn.label)) {
                btn.label = conf.label;
            }
            btn.color = conf.color;
        },

        addActionButton() {
            this.actionButtons.push({
                id: 'btn_' + Date.now(),
                type: 'zalo',
                label: 'Zalo',
                value: '',
                color: '#0068ff',
                enabled: true,
                icon_type: 'svg',
                icon_url: '',
                open_new_tab: true,
                uploading: false
            });
        },

        deleteActionButton(index) {
            if (confirm('Bạn có chắc muốn xóa nút hành động này?')) {
                this.actionButtons.splice(index, 1);
            }
        },

        moveUp(index) {
            if (index > 0) {
                const temp = this.actionButtons[index];
                this.actionButtons[index] = this.actionButtons[index - 1];
                this.actionButtons[index - 1] = temp;
            }
        },

        moveDown(index) {
            if (index < this.actionButtons.length - 1) {
                const temp = this.actionButtons[index];
                this.actionButtons[index] = this.actionButtons[index + 1];
                this.actionButtons[index + 1] = temp;
            }
        },

        // Upload Call Icon File via AJAX
        async uploadCallFile(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            this.callUploading = true;
            const url = await this.performAjaxUpload(file);
            this.callUploading = false;
            event.target.value = '';

            if (url) {
                this.callButton.icon_url = url;
                this.callButton.icon_type = 'image';
            }
        },

        // Upload Action Button Icon File via AJAX
        async uploadActionFile(event, index) {
            const file = event.target.files && event.target.files[0];
            if (!file || !this.actionButtons[index]) return;

            this.actionButtons[index].uploading = true;
            const url = await this.performAjaxUpload(file);
            this.actionButtons[index].uploading = false;
            event.target.value = '';

            if (url) {
                this.actionButtons[index].icon_url = url;
                this.actionButtons[index].icon_type = 'image';
            }
        },

        async performAjaxUpload(file) {
            const formData = new FormData();
            formData.append('files[]', file);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const response = await fetch('{{ $mediaUploadUrl }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success && data.uploaded && data.uploaded.length > 0) {
                    return data.uploaded[0].url;
                } else {
                    alert('Lỗi tải ảnh: ' + (data.message || 'Không thể upload file'));
                    return null;
                }
            } catch (err) {
                alert('Lỗi khi tải ảnh lên server: ' + err.message);
                return null;
            }
        },

        // Media Picker Trigger
        openMediaPicker(target) {
            this.activeMediaTarget = target;
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: {
                    field: typeof target === 'number' ? 'action_btn_' + target : 'call_btn',
                    multiple: false,
                    mediaType: 'image'
                }
            }));
        },

        // Handle Media Picker result
        handleMediaSelected(detail) {
            const url = detail.urls || detail.url || detail.value || (detail.files && detail.files[0] ? detail.files[0].url : '') || '';
            if (!url) return;

            if (this.activeMediaTarget === 'call_button') {
                this.callButton.icon_url = url;
                this.callButton.icon_type = 'image';
            } else if (typeof this.activeMediaTarget === 'number' && this.actionButtons[this.activeMediaTarget]) {
                this.actionButtons[this.activeMediaTarget].icon_url = url;
                this.actionButtons[this.activeMediaTarget].icon_type = 'image';
            }
            this.activeMediaTarget = null;
        },

        // Merge Call button & Action buttons into unified list for legacy compatibility
        getUnifiedButtons() {
            const list = [];
            if (this.callButton.enabled && this.callButton.phone) {
                list.push({
                    type: 'phone',
                    label: this.callButton.label || 'Điện thoại',
                    value: this.callButton.phone,
                    color: this.callButton.color || '#10b981',
                    enabled: 1,
                    icon_type: this.callButton.icon_type || 'svg',
                    icon_url: this.callButton.icon_url || '',
                    pulse: this.callButton.pulse ? 1 : 0
                });
            }
            this.actionButtons.forEach(btn => {
                list.push({
                    type: btn.type,
                    label: btn.label,
                    value: btn.value,
                    color: btn.color,
                    enabled: btn.enabled ? 1 : 0,
                    icon_type: btn.icon_type || 'svg',
                    icon_url: btn.icon_url || '',
                    open_new_tab: btn.open_new_tab ? 1 : 0
                });
            });
            return list;
        },

        // Submit Form via AJAX or direct submit
        async submitForm() {
            this.saving = true;
            const form = document.getElementById('contactButtonsForm');
            const formData = new FormData(form);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    const resData = await response.json().catch(() => ({}));
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: 'Đã lưu cấu hình Nút Liên Hệ Nổi thành công!', type: 'success' }
                        }));
                    }
                    alert('Lưu cấu hình Nút Liên Hệ Nổi thành công!');
                } else {
                    // Fallback to standard form submit
                    form.submit();
                    return;
                }
            } catch (err) {
                // Fallback to standard form submit if fetch fails
                form.submit();
                return;
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endsection

{{-- Frontend Floating Contact Buttons Widget --}}
@php
    $desktopEnabled = (bool) setting('contact_buttons_desktop_enabled', true);
    $mobileEnabled = (bool) setting('contact_buttons_mobile_enabled', true);

    // 1. Load Call Button
    $callButton = setting('contact_call_button');
    if (is_string($callButton)) {
        $callButton = json_decode($callButton, true);
    }

    // 2. Load Action Buttons
    $actionButtons = setting('contact_action_buttons');
    if (is_string($actionButtons)) {
        $actionButtons = json_decode($actionButtons, true);
    }

    // Fallback from legacy contact_buttons if new format is not yet stored
    if (!is_array($callButton) && !is_array($actionButtons)) {
        $legacyButtons = setting('contact_buttons');
        if (is_string($legacyButtons)) {
            $legacyButtons = json_decode($legacyButtons, true);
        }
        if (!is_array($legacyButtons) || empty($legacyButtons)) {
            $legacyButtons = [
                ['type' => 'phone', 'label' => 'Điện thoại', 'value' => setting_string('contact_phone', ''), 'enabled' => true, 'color' => '#10b981'],
                ['type' => 'zalo', 'label' => 'Zalo', 'value' => setting_string('social_zalo', '') ?: setting_string('contact_phone', ''), 'enabled' => true, 'color' => '#0068ff'],
                ['type' => 'messenger', 'label' => 'Messenger', 'value' => setting_string('social_facebook', ''), 'enabled' => true, 'color' => '#0084ff'],
                ['type' => 'sms', 'label' => 'SMS', 'value' => setting_string('contact_phone', ''), 'enabled' => false, 'color' => '#f59e0b'],
            ];
        }

        $phoneItem = collect($legacyButtons)->firstWhere('type', 'phone');
        if ($phoneItem) {
            $callButton = [
                'enabled' => !empty($phoneItem['enabled']),
                'phone' => $phoneItem['value'] ?? (setting_string('contact_phone', '')),
                'label' => $phoneItem['label'] ?? 'Gọi ngay',
                'color' => $phoneItem['color'] ?? '#10b981',
                'pulse' => true,
                'icon_type' => !empty($phoneItem['icon_url']) ? 'image' : 'svg',
                'icon_url' => $phoneItem['icon_url'] ?? '',
            ];
        }

        $actionButtons = collect($legacyButtons)->filter(fn($b) => ($b['type'] ?? '') !== 'phone')->values()->all();
    }

    // Normalize Call Button
    $hasCallButton = is_array($callButton) 
        && !empty($callButton['enabled']) 
        && !empty(trim($callButton['phone'] ?? ''));

    // Normalize Action Buttons
    $activeActionButtons = [];
    if (is_array($actionButtons)) {
        foreach ($actionButtons as $btn) {
            $val = trim($btn['value'] ?? '');
            $type = $btn['type'] ?? 'zalo';
            if (!empty($btn['enabled']) && ($type === 'modal' || !empty($val))) {
                $activeActionButtons[] = $btn;
            }
        }

        // If no action buttons explicitly enabled yet, enable default Zalo & Messenger if they have values
        if (empty($activeActionButtons)) {
            foreach ($actionButtons as $btn) {
                $val = trim($btn['value'] ?? '');
                $type = $btn['type'] ?? 'zalo';
                if (in_array($type, ['zalo', 'messenger']) && !empty($val)) {
                    $btn['enabled'] = true;
                    $activeActionButtons[] = $btn;
                }
            }
        }
    }

    $desktopPosition = setting('contact_buttons_desktop_position', 'bottom-right');
    $mobilePosition = setting('contact_buttons_mobile_position', 'bottom-right');
    $desktopMarginV = (int) setting('contact_buttons_desktop_margin_v', 20);
    $desktopMarginH = (int) setting('contact_buttons_desktop_margin_h', 20);
    $mobileMarginV = (int) setting('contact_buttons_mobile_margin_v', 20);
    $mobileMarginH = (int) setting('contact_buttons_mobile_margin_h', 20);
    $style = setting('contact_buttons_style', 'circle');

    $posCSS = [
        'top-left' => "top: {$desktopMarginV}px; left: {$desktopMarginH}px;",
        'top-right' => "top: {$desktopMarginV}px; right: {$desktopMarginH}px;",
        'bottom-left' => "bottom: {$desktopMarginV}px; left: {$desktopMarginH}px;",
        'bottom-right' => "bottom: {$desktopMarginV}px; right: {$desktopMarginH}px;",
    ];

    $mobilePosCSS = [
        'top-left' => "top: {$mobileMarginV}px; left: {$mobileMarginH}px;",
        'top-right' => "top: {$mobileMarginV}px; right: {$mobileMarginH}px;",
        'bottom-left' => "bottom: {$mobileMarginV}px; left: {$mobileMarginH}px;",
        'bottom-right' => "bottom: {$mobileMarginV}px; right: {$mobileMarginH}px;",
    ];

    $shapeClass = match($style) {
        'circle' => 'rounded-full',
        'square' => 'rounded-none',
        'rounded' => 'rounded-xl',
        'pill' => 'rounded-full px-3',
        'minimal' => 'rounded-full border-2 bg-white',
        'shadow' => 'rounded-xl shadow-2xl',
        default => 'rounded-full',
    };

    $isPosRight = str_contains($desktopPosition, 'right');
@endphp

@if(($desktopEnabled || $mobileEnabled) && ($hasCallButton || count($activeActionButtons) > 0))
<style>
    .frontend-contact-buttons {
        position: fixed;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 9999;
        pointer-events: auto;
    }
    .frontend-contact-btn {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
    }
    .frontend-contact-btn:hover {
        transform: scale(1.12);
        box-shadow: 0 8px 22px rgba(0,0,0,0.32);
    }
    .frontend-contact-btn .btn-pulse {
        position: absolute;
        inset: -4px;
        border-radius: inherit;
        background: inherit;
        opacity: 0.45;
        animation: contactBtnPulse 2s infinite ease-in-out;
        z-index: -1;
    }
    .frontend-contact-btn .btn-pulse-wave {
        position: absolute;
        inset: -10px;
        border-radius: inherit;
        background: inherit;
        opacity: 0.25;
        animation: contactBtnPulse 2s 0.6s infinite ease-in-out;
        z-index: -2;
    }
    @keyframes contactBtnPulse {
        0% { transform: scale(0.95); opacity: 0.6; }
        50% { transform: scale(1.3); opacity: 0; }
        100% { transform: scale(1.3); opacity: 0; }
    }
    @keyframes contactShake {
        0%, 100% { transform: rotate(0deg); }
        10%, 30%, 50%, 70%, 90% { transform: rotate(-8deg); }
        20%, 40%, 60%, 80% { transform: rotate(8deg); }
    }
    .frontend-contact-btn.animate-ring svg,
    .frontend-contact-btn.animate-ring img {
        animation: contactShake 2.5s infinite ease-in-out;
    }
    .frontend-contact-tooltip {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 600;
        padding: 5px 11px;
        border-radius: 6px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
        z-index: 10000;
    }
    .frontend-contact-buttons.pos-right .frontend-contact-tooltip {
        right: calc(100% + 10px);
    }
    .frontend-contact-buttons.pos-left .frontend-contact-tooltip {
        left: calc(100% + 10px);
    }
    .frontend-contact-btn:hover .frontend-contact-tooltip {
        opacity: 1;
        visibility: visible;
    }
    /* Desktop */
    @media (min-width: 769px) {
        .frontend-contact-buttons {
            display: {{ $desktopEnabled ? 'flex' : 'none' }};
            {{ $posCSS[$desktopPosition] ?? $posCSS['bottom-right'] }}
        }
    }
    /* Mobile */
    @media (max-width: 768px) {
        .frontend-contact-buttons {
            display: {{ $mobileEnabled ? 'flex' : 'none' }};
            {{ $mobilePosCSS[$mobilePosition] ?? $mobilePosCSS['bottom-right'] }}
        }
        .frontend-contact-btn {
            width: 44px;
            height: 44px;
        }
    }
</style>

<div class="frontend-contact-buttons {{ $isPosRight ? 'pos-right' : 'pos-left' }}" aria-label="Kênh liên hệ nhanh">
    {{-- 1. Call Button (Hotline) --}}
    @if($hasCallButton)
        @php
            $callPhone = preg_replace('/[^0-9+]/', '', $callButton['phone'] ?? '');
            $callLabel = $callButton['label'] ?? 'Gọi ngay';
            $callColor = $callButton['color'] ?? '#10b981';
            $callPulse = !empty($callButton['pulse']);
            $callIconType = $callButton['icon_type'] ?? 'svg';
            $callIconUrl = $callButton['icon_url'] ?? '';
        @endphp
        <a href="tel:{{ $callPhone }}" 
           class="frontend-contact-btn {{ $shapeClass }} {{ $callPulse ? 'animate-ring' : '' }}"
           style="background-color: {{ $callColor }}; color: #ffffff;"
           title="{{ $callLabel }}"
           aria-label="{{ $callLabel }}">
            @if($callPulse)
                <span class="btn-pulse" style="background-color: {{ $callColor }};"></span>
                <span class="btn-pulse-wave" style="background-color: {{ $callColor }};"></span>
            @endif

            @if($callIconType === 'image' && !empty($callIconUrl))
                <img src="{{ $callIconUrl }}" class="w-6 h-6 object-contain p-0.5" alt="{{ $callLabel }}">
            @else
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            @endif

            <span class="frontend-contact-tooltip">{{ $callLabel }}</span>
        </a>
    @endif

    {{-- 2. Action Buttons (Zalo, Messenger, Modal, SMS, etc.) --}}
    @foreach($activeActionButtons as $btn)
        @php
            $type = $btn['type'] ?? 'zalo';
            $val = trim($btn['value'] ?? '');
            $label = $btn['label'] ?? ucfirst($type);
            $color = $btn['color'] ?? match($type) {
                'zalo' => '#0068ff',
                'messenger' => '#0084ff',
                'modal' => '#051650',
                'sms' => '#f59e0b',
                'telegram' => '#229ED9',
                'whatsapp' => '#25D366',
                'email' => '#ea4335',
                default => '#4f46e5',
            };
            $href = match($type) {
                'modal' => 'javascript:void(0)',
                'phone' => 'tel:' . preg_replace('/[^0-9+]/', '', $val),
                'sms' => 'sms:' . preg_replace('/[^0-9+]/', '', $val),
                'zalo' => str_starts_with($val, 'http') ? $val : 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $val),
                'messenger' => str_starts_with($val, 'http') ? $val : 'https://m.me/' . $val,
                'telegram' => str_starts_with($val, 'http') ? $val : 'https://t.me/' . ltrim($val, '@'),
                'whatsapp' => str_starts_with($val, 'http') ? $val : 'https://wa.me/' . preg_replace('/[^0-9]/', '', $val),
                'email' => str_starts_with($val, 'mailto:') ? $val : 'mailto:' . $val,
                default => $val,
            };
            $isModal = ($type === 'modal');
            $openNewTab = !$isModal && !in_array($type, ['phone', 'sms']) && (!isset($btn['open_new_tab']) || !empty($btn['open_new_tab']));
            $iconType = $btn['icon_type'] ?? 'svg';
            $iconUrl = $btn['icon_url'] ?? '';
        @endphp
        <a href="{{ $href }}" 
           {!! $isModal ? 'data-contact-modal-toggle' : '' !!}
           target="{{ $openNewTab ? '_blank' : '_self' }}" 
           rel="{{ $openNewTab ? 'noopener noreferrer' : '' }}"
           class="frontend-contact-btn {{ $shapeClass }}"
           style="background-color: {{ $color }}; color: #ffffff;"
           title="{{ $label }}"
           aria-label="{{ $label }}">
            
            <span class="btn-pulse" style="background-color: {{ $color }};"></span>

            @if($iconType === 'image' && !empty($iconUrl))
                <img src="{{ $iconUrl }}" class="w-6 h-6 object-contain p-0.5" alt="{{ $label }}">
            @elseif($type === 'zalo')
                <svg class="w-5 h-5" viewBox="0 0 48 48" fill="none">
                    <path d="M24 4C12.95 4 4 12.28 4 22.5c0 5.86 2.94 11.08 7.56 14.54l-1.92 6.72c-.17.61.43 1.14.99.87l7.73-3.77c1.82.43 3.71.64 5.64.64 11.05 0 20-8.28 20-18.5S35.05 4 24 4z" fill="#0068FF"/>
                    <path d="M12.5 28.5l6.5-8.2h-6.2v-2.8h9.6v2.5l-6.5 8.2h6.7v2.8h-10.1v-2.5zm12.3-11h3.1v13.5h-3.1V17.5zm1.5-1.9c1 0 1.8-.8 1.8-1.8s-.8-1.8-1.8-1.8-1.8.8-1.8 1.8.8 1.8 1.8 1.8zm5.1 9.8c0-3.5 2.6-6.3 6.1-6.3s6.1 2.8 6.1 6.3-2.6 6.3-6.1 6.3-6.1-2.8-6.1-6.3zm9.1 0c0-2-1.3-3.6-3-3.6s-3 1.6-3 3.6 1.3 3.6 3 3.6 3-1.6 3-3.6z" fill="#ffffff"/>
                </svg>
            @elseif($type === 'messenger')
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.515 3.735 7.199V22l3.39-1.86c.928.257 1.91.396 2.875.396 5.523 0 10-4.145 10-9.258C22 6.145 17.523 2 12 2zm1.066 12.443l-2.583-2.755-5.045 2.755 5.55-5.895 2.646 2.755 4.982-2.755-5.55 5.895z"/>
                </svg>
            @elseif($type === 'modal')
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            @elseif($type === 'sms')
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            @elseif($type === 'telegram')
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                </svg>
            @elseif($type === 'whatsapp')
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.15C9.34 7.15 9.03 7.22 8.78 7.49C8.53 7.76 7.82 8.43 7.82 9.78C7.82 11.13 8.81 12.43 8.94 12.61C9.08 12.79 10.88 15.57 13.64 16.76C14.3 17.04 14.81 17.21 15.21 17.34C15.87 17.55 16.48 17.52 16.96 17.45C17.5 17.37 18.61 16.78 18.84 16.13C19.08 15.48 19.08 14.93 19.01 14.81C18.94 14.69 18.75 14.62 18.47 14.48C18.18 14.34 16.76 13.64 16.5 13.55C16.24 13.45 16.05 13.41 15.86 13.69C15.67 13.97 15.14 14.62 14.98 14.81C14.81 14.99 14.65 15.02 14.37 14.88C14.09 14.74 13.18 14.44 12.1 13.48C11.26 12.73 10.7 11.8 10.53 11.52C10.37 11.24 10.51 11.09 10.66 10.95C10.79 10.82 10.95 10.61 11.09 10.45C11.23 10.28 11.28 10.16 11.37 9.98C11.47 9.79 11.42 9.63 11.35 9.49C11.28 9.35 10.72 7.98 10.49 7.42C10.26 6.88 10.03 6.95 9.86 6.94C9.7 6.94 9.53 7.15 9.53 7.15Z"/>
                </svg>
            @elseif($type === 'email')
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71m-2.47 2.48a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                </svg>
            @endif

            <span class="frontend-contact-tooltip">{{ $label }}</span>
        </a>
    @endforeach
</div>
@endif

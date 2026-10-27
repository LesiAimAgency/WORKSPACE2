<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('site_name', 'Preview Website') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            @if(setting('bg_type') === 'color')
                background-color: {{ setting('bg_color', '#ffffff') }};
            @elseif(setting('bg_type') === 'gradient')
                background: linear-gradient({{ setting('bg_gradient_direction', 'to right') }}, {{ setting('bg_gradient_start', '#ffffff') }}, {{ setting('bg_gradient_end', '#000000') }});
            @elseif(setting('bg_type') === 'image')
                background-image: url('{{ setting('bg_image') }}');
                background-size: {{ setting('bg_image_size', 'cover') }};
                background-position: {{ setting('bg_image_position', 'center') }};
                background-repeat: {{ setting('bg_image_repeat', 'no-repeat') }};
            @endif
        }
        <?php
            $Top_bg_color = setting('topbar_bg_color');
            dd($Top_bg_color);
        
        ?>
    </style>
</head>
<body class="min-h-screen">
    <!-- Topbar -->
    @if(setting('topbar_enabled'))
    <div class="topbar" style="background-color: {{ setting('topbar_bg_color', '#000000') }}; color: {{ setting('topbar_text_color', '#ffffff') }};">
        <div class="container mx-auto px-4 py-2">
            <div class="flex items-center justify-between text-sm">
                <div>{{ setting('topbar_text', 'Chào mừng đến với website') }}</div>
                <div class="flex items-center gap-4">
                    @if(setting('contact_phone'))
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                            {{ setting('contact_phone') }}
                        </span>
                    @endif
                    @if(setting('contact_email'))
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                            {{ setting('contact_email') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <header class="{{ setting('header_sticky') ? 'sticky top-0 z-50' : '' }}" 
            style="background-color: {{ setting('header_bg_color', '#ffffff') }}; color: {{ setting('header_text_color', '#000000') }};">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="logo">
                    @if(setting('site_logo'))
                        <img src="{{ setting('site_logo') }}" alt="{{ setting('site_name') }}" class="h-12">
                    @else
                        <h1 class="text-2xl font-bold">{{ setting('site_name', 'Website') }}</h1>
                    @endif
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-4">
                    @if(setting('show_search'))
                        <button class="p-2 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    @endif
                    @if(setting('show_cart'))
                        <button class="p-2 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </button>
                    @endif
                    @if(setting('show_account'))
                        <button class="p-2 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav style="background-color: {{ setting('nav_bg_color', '#f3f4f6') }}; color: {{ setting('nav_text_color', '#000000') }};">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-6 py-3">
                @php
                    $menuId = setting('topbar_menu_id');
                    $menu = $menuId ? \App\Models\Menu::find($menuId) : null;
                @endphp
                @if($menu)
                    @foreach($menu->items as $item)
                        <a href="{{ $item->url }}" class="hover:opacity-75 transition">{{ $item->title }}</a>
                    @endforeach
                @else
                    <a href="#" class="hover:opacity-75">Trang chủ</a>
                    <a href="#" class="hover:opacity-75">Sản phẩm</a>
                    <a href="#" class="hover:opacity-75">Giới thiệu</a>
                    <a href="#" class="hover:opacity-75">Liên hệ</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-4" style="color: {{ setting('theme_color', '#000000') }};">
                Preview Website Configuration
            </h2>
            <p class="text-gray-600 mb-6">Đây là giao diện preview dựa trên cấu hình bạn đã thiết lập.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Sản phẩm mẫu 1</h3>
                    <div class="bg-gray-200 h-40 rounded mb-2"></div>
                    <p class="text-sm text-gray-600">Mô tả sản phẩm</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Sản phẩm mẫu 2</h3>
                    <div class="bg-gray-200 h-40 rounded mb-2"></div>
                    <p class="text-sm text-gray-600">Mô tả sản phẩm</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Sản phẩm mẫu 3</h3>
                    <div class="bg-gray-200 h-40 rounded mb-2"></div>
                    <p class="text-sm text-gray-600">Mô tả sản phẩm</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Map -->
    @if(setting('map_enabled') && setting('map_iframe'))
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6 text-center">Vị trí của chúng tôi</h2>
            <div class="rounded-lg overflow-hidden">
                {!! setting('map_iframe') !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer style="background-color: {{ setting('footer_bg_color', '#1f2937') }}; color: {{ setting('footer_text_color', '#ffffff') }};">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-{{ setting('footer_layout') === '4-columns' ? '4' : '3' }} gap-8">
                <div>
                    <h3 class="font-bold mb-4">Về chúng tôi</h3>
                    <p class="text-sm opacity-80">{{ setting('footer_about', 'Thông tin về công ty') }}</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Liên kết</h3>
                    <ul class="text-sm space-y-2 opacity-80">
                        <li><a href="#" class="hover:opacity-100">Trang chủ</a></li>
                        <li><a href="#" class="hover:opacity-100">Sản phẩm</a></li>
                        <li><a href="#" class="hover:opacity-100">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Liên hệ</h3>
                    <ul class="text-sm space-y-2 opacity-80">
                        @if(setting('contact_phone'))
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                                {{ setting('contact_phone') }}
                            </li>
                        @endif
                        @if(setting('contact_email'))
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                                {{ setting('contact_email') }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm opacity-80">
                {{ setting('footer_copyright', '© 2024 All rights reserved') }}
            </div>
        </div>
    </footer>

    <!-- Floating Cart -->
    @if(setting('floating_cart_enabled'))
    <button class="fixed {{ setting('floating_cart_position', 'bottom-right') === 'bottom-right' ? 'bottom-6 right-6' : (setting('floating_cart_position') === 'bottom-left' ? 'bottom-6 left-6' : 'top-6 right-6') }} w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-white"
            style="background-color: {{ setting('floating_cart_color', '#ef4444') }};">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    </button>
    @endif

    <!-- Back to Config -->
    @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
    <a href="{{ $isProject ? route('project.admin.website-config.index', $projectCode) : route('cms.website-config.index') }}" 
       class="fixed bottom-6 left-6 px-4 py-2 bg-blue-600 text-white rounded-lg shadow-lg hover:bg-blue-700 transition">
        ← Quay lại cấu hình
    </a>
</body>
</html>

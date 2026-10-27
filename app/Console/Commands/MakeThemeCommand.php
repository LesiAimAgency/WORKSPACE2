<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Theme\ThemeManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:theme 
                            {name : Tên định danh của theme (vd: bakery, realestate, spa, travel)}
                            {--title= : Tên hiển thị đầy đủ của theme}
                            {--type=landing : Loại theme (landing|ecommerce|portal|corporate)}
                            {--force : Ghi đè nếu theme đã tồn tại}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo cấu trúc hoàn chỉnh cho một Theme mới theo chuẩn Theme-First VGT';

    public function handle(): int
    {
        $rawName = trim((string) $this->argument('name'));
        $slug = Str::slug($rawName);
        $studly = Str::studly($slug);
        $title = (string) ($this->option('title') ?: Str::headline($slug));
        $type = (string) ($this->option('type') ?: 'landing');
        $force = (bool) $this->option('force');

        if (empty($slug)) {
            $this->error('❌ Tên theme không hợp lệ!');

            return self::FAILURE;
        }

        $themeViewDir = resource_path("views/themes/{$slug}");

        if (File::isDirectory($themeViewDir) && ! $force) {
            $this->error("❌ Theme [{$slug}] đã tồn tại tại: {$themeViewDir}");
            $this->line('👉 Sử dụng flag --force nếu bạn muốn ghi đè.');

            return self::FAILURE;
        }

        $this->info("🚀 Đang khởi tạo VGT Theme: <comment>{$title}</comment> (slug: <info>{$slug}</info>)...");

        // 1. Tạo các thư mục
        $dirs = [
            $themeViewDir,
            "{$themeViewDir}/partials",
            resource_path("views/widgets/{$slug}"),
            public_path("themes/{$slug}/css"),
            public_path("themes/{$slug}/js"),
            public_path("themes/{$slug}/images"),
            app_path("Widgets/{$studly}"),
            app_path("Http/Controllers/Themes/{$studly}"),
        ];

        foreach ($dirs as $dir) {
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
        }

        // 2. Tạo theme.json
        $this->createThemeManifest($themeViewDir, $slug, $studly, $title, $type);

        // 3. Tạo Blade Views (Layout, Home, Header, Footer)
        $this->createBladeViews($themeViewDir, $slug, $studly, $title);

        // 4. Tạo Public Assets (CSS, JS)
        $this->createPublicAssets($slug, $title);

        // 5. Tạo Controller & Routes
        $this->createControllerAndRoutes($slug, $studly, $title);

        // 6. Tạo Widgets & Views
        $this->createThemeWidgets($slug, $studly, $title);

        // 7. Tạo Database Seeder
        $this->createDatabaseSeeder($slug, $studly, $title);

        // 8. Làm mới Theme Cache
        app(ThemeManager::class)->clearCache();

        $this->newLine();
        $this->info("🎉 CHÚC MỪNG! THEME [{$title}] ĐÃ ĐƯỢC KHỞI TẠO HOÀN TẤT THEO CHUẨN THEME-FIRST!");
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('📂 <comment>Cấu trúc các file đã tạo:</comment>');
        $this->line("   ├── <info>Manifest:</info>   resources/views/themes/{$slug}/theme.json");
        $this->line("   ├── <info>Views:</info>      resources/views/themes/{$slug}/ (layout, home, header, footer)");
        $this->line("   ├── <info>Assets:</info>     public/themes/{$slug}/ (css/style.css, js/main.js)");
        $this->line("   ├── <info>Routes:</info>     routes/{$slug}.php");
        $this->line("   ├── <info>Controller:</info> app/Http/Controllers/Themes/{$studly}/{$studly}Controller.php");
        $this->line("   ├── <info>Widgets:</info>    app/Widgets/{$studly}/ ({$studly}ThemeWidget, HeroWidget)");
        $this->line("   └── <info>Seeder:</info>     database/seeders/{$studly}ThemeSeeder.php");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->line('🌐 <comment>Xem trước giao diện ngay:</comment>');
        $this->line("   👉 URL Trực tiếp: <info>http://localhost/{$slug}</info>");
        $this->newLine();
        $this->line('🎨 <comment>Các bước nhân sự bắt đầu code giao diện:</comment>');
        $this->line("   1. Dán mã HTML/Blade trang chủ vào: <info>resources/views/themes/{$slug}/home.blade.php</info>");
        $this->line("   2. Thêm CSS của theme vào: <info>public/themes/{$slug}/css/style.css</info>");
        $this->line("   3. Thêm JS/hiệu ứng vào: <info>public/themes/{$slug}/js/main.js</info>");
        $this->line('   4. Tạo thêm widget tùy biến bằng lệnh: <info>php artisan widget:make {TênWidget}</info>');
        $this->line("   5. Nạp dữ liệu mẫu ban đầu: <info>php artisan db:seed --class={$studly}ThemeSeeder</info>");
        $this->newLine();

        return self::SUCCESS;
    }

    protected function createThemeManifest(string $dir, string $slug, string $studly, string $title, string $type): void
    {
        $manifest = [
            '$schema' => 'https://vgt.local/schemas/theme.v1.json',
            'name' => $slug,
            'title' => $title,
            'version' => '1.0.0',
            'description' => "Theme {$title} được xây dựng trên nền tảng VGT Platform chuẩn Theme-First",
            'author' => 'VGT Team',
            'engine_compatibility' => '>=1.0.0',
            'type' => $type,
            'features' => [
                'widgets',
                'menus',
                'settings',
                'contact',
                'seo',
            ],
            'database' => [
                'tables' => [
                    'widgets',
                    'settings',
                    'menus',
                    'menu_items',
                    'form_submissions',
                ],
                'seeders' => [
                    "{$studly}ThemeSeeder",
                ],
            ],
            'widgets' => [
                "{$slug}_theme",
                "{$slug}_hero",
            ],
            'menus' => [
                'header' => [
                    'name' => "{$title} Navigation",
                    'items' => [
                        ['title' => 'Trang chủ', 'url' => '#'],
                        ['title' => 'Giới thiệu', 'url' => '#about'],
                        ['title' => 'Dịch vụ', 'url' => '#services'],
                        ['title' => 'Liên hệ', 'url' => '#contact'],
                    ],
                ],
                'footer' => [
                    'name' => "{$title} Footer Links",
                    'items' => [
                        ['title' => 'Chính sách bảo mật', 'url' => '#privacy'],
                        ['title' => 'Điều khoản sử dụng', 'url' => '#terms'],
                    ],
                ],
            ],
            'default_settings' => [
                'site_title' => "{$title} - Website Chuyên Nghiệp",
                'site_name' => $title,
                'site_tagline' => 'Giải pháp nền tảng giao diện hiện đại & chuyên nghiệp',
                'site_description' => "Trải nghiệm website {$title} tối ưu tốc độ và chuyển đổi",
                'site_email' => "contact@{$slug}.local",
                'site_phone' => '0909 000 000',
                'theme_primary_color' => '#4F46E5',
                'site_copyright' => "Copyright © 2026 {$title}. All rights reserved.",
            ],
            'routes_file' => "routes/{$slug}.php",
            'view_path' => "resources/views/themes/{$slug}",
            'asset_path' => "public/themes/{$slug}",
        ];

        File::put("{$dir}/theme.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->line("   [+] Đã tạo manifest: <info>resources/views/themes/{$slug}/theme.json</info>");
    }

    protected function createBladeViews(string $dir, string $slug, string $studly, string $title): void
    {
        // 1. layout.blade.php
        $layout = <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', setting('site_title', '{$title}'))</title>
    <meta name="description" content="@yield('meta_description', setting('site_description', ''))">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="{{ asset('themes/{$slug}/css/style.css') }}">
    @stack('styles')
</head>
<body class="theme-{$slug} font-sans antialiased text-gray-800 bg-white min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">
    @include('themes.{$slug}.partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes.{$slug}.partials.footer')

    <!-- Theme Scripts -->
    <script src="{{ asset('themes/{$slug}/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
BLADE;
        File::put("{$dir}/layout.blade.php", $layout);

        // 2. home.blade.php
        $home = <<<BLADE
@extends('themes.{$slug}.layout')

@section('title', setting('site_title', '{$title}'))

@section('content')
    {{-- VGT Theme-First: Render Homepage Main Widget Area --}}
    {!! render_widget_area('homepage-main') !!}
@endsection
BLADE;
        File::put("{$dir}/home.blade.php", $home);

        // 3. partials/header.blade.php
        $header = <<<BLADE
@php
    \$menus = \\App\\Services\\MenuService::getMenusByLocation('header');
    \$headerMenu = \$menus->first();
@endphp
<header class="theme-header sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="brand-logo flex items-center gap-2 group">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-xl shadow-md group-hover:scale-105 transition-transform">
                    {{ substr('{$studly}', 0, 1) }}
                </span>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900 group-hover:text-indigo-600 transition-colors">
                    {{ setting('site_name', '{$title}') }}
                </span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8">
                @if(\$headerMenu && \$headerMenu->items)
                    @foreach(\$headerMenu->items as \$item)
                        <a href="{{ \$item->url }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">
                            {{ \$item->title }}
                        </a>
                    @endforeach
                @else
                    <a href="#about" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Về chúng tôi</a>
                    <a href="#services" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Dịch vụ</a>
                    <a href="#contact" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Liên hệ</a>
                @endif
            </nav>

            <!-- Action Button -->
            <div class="hidden md:flex items-center">
                <a href="#contact" class="px-6 py-2.5 rounded-full bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 active:scale-95 transition shadow-sm hover:shadow-indigo-200">
                    Bắt đầu ngay
                </a>
            </div>

            <!-- Mobile Hamburger Toggle -->
            <button type="button" id="mobile-menu-toggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition focus:outline-none" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 pt-3 pb-6 space-y-2">
        @if(\$headerMenu && \$headerMenu->items)
            @foreach(\$headerMenu->items as \$item)
                <a href="{{ \$item->url }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                    {{ \$item->title }}
                </a>
            @endforeach
        @endif
        <div class="pt-4">
            <a href="#contact" class="block w-full text-center px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold text-sm">
                Bắt đầu ngay
            </a>
        </div>
    </div>
</header>
BLADE;
        File::put("{$dir}/partials/header.blade.php", $header);

        // 4. partials/footer.blade.php
        $footer = <<<BLADE
<footer class="theme-footer bg-gray-950 text-gray-300 pt-16 pb-12 border-t border-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            <!-- Column 1: Brand Info -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg">
                        {{ substr('{$studly}', 0, 1) }}
                    </span>
                    <span class="text-xl font-black text-white tracking-tight">
                        {{ setting('site_name', '{$title}') }}
                    </span>
                </div>
                <p class="text-sm text-gray-400 max-w-sm mb-6 leading-relaxed">
                    {{ setting('site_tagline', 'Nền tảng giao diện chuẩn Theme-First của VGT Platform, sẵn sàng triển khai & xuất bản độc lập.') }}
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Liên kết nhanh</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#about" class="hover:text-white transition-colors">Về chúng tôi</a></li>
                    <li><a href="#services" class="hover:text-white transition-colors">Dịch vụ & Tiện ích</a></li>
                    <li><a href="#contact" class="hover:text-white transition-colors">Liên hệ tư vấn</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact Details -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Thông tin liên hệ</h4>
                <ul class="space-y-2.5 text-sm text-gray-400">
                    <li>Email: <span class="text-gray-200">{{ setting('site_email', 'contact@domain.com') }}</span></li>
                    <li>Hotline: <span class="text-gray-200">{{ setting('site_phone', '0909 000 000') }}</span></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-gray-900 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <p>{{ setting('site_copyright', 'Copyright © 2026 ' . setting('site_name', '{$title}') . '. All rights reserved.') }}</p>
            <p class="text-gray-600">Powered by VGT Theme-First Engine</p>
        </div>
    </div>
</footer>
BLADE;
        File::put("{$dir}/partials/footer.blade.php", $footer);

        $this->line('   [+] Đã tạo views: <info>layout.blade.php, home.blade.php, header.blade.php, footer.blade.php</info>');
    }

    protected function createPublicAssets(string $slug, string $title): void
    {
        // 1. CSS
        $css = <<<CSS
/* Theme: {$title} ({$slug}) */
:root {
    --theme-primary: #4f46e5;
    --theme-primary-hover: #4338ca;
    --theme-text: #1f2937;
    --theme-bg: #ffffff;
    --theme-font: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
}

body {
    font-family: var(--theme-font);
    color: var(--theme-text);
    background-color: var(--theme-bg);
}

.hero-gradient {
    background: radial-gradient(100% 100% at 50% 0%, rgba(79, 70, 229, 0.12) 0%, rgba(255, 255, 255, 0) 100%);
}

.card-hover {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
}
CSS;
        File::put(public_path("themes/{$slug}/css/style.css"), $css);

        // 2. JS
        $js = <<<JS
/**
 * Theme: {$title} ({$slug})
 * Scripts
 */
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const toggleBtn = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (toggleBtn && mobileMenu) {
        toggleBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            }
        });
    });
});
JS;
        File::put(public_path("themes/{$slug}/js/main.js"), $js);

        $this->line("   [+] Đã tạo assets: <info>public/themes/{$slug}/css/style.css, js/main.js</info>");
    }

    protected function createControllerAndRoutes(string $slug, string $studly, string $title): void
    {
        // 1. Controller
        $controller = <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Themes\\{$studly};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {$studly}Controller extends Controller
{
    /**
     * Render Theme Homepage
     */
    public function index(Request \$request): View
    {
        return view('themes.{$slug}.home');
    }
}
PHP;
        File::put(app_path("Http/Controllers/Themes/{$studly}/{$studly}Controller.php"), $controller);

        // 2. Routes File
        $routes = <<<PHP
<?php

use App\Http\Controllers\Themes\\{$studly}\\{$studly}Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Theme {$title} Routes
|--------------------------------------------------------------------------
|
| Tuyến đường dành riêng cho Theme {$title}.
| Tự động được gắn middleware project.context và nạp động khi chạy hệ thống.
|
*/

Route::middleware(['web', 'project.context'])->group(function () {
    Route::get('/', [{$studly}Controller::class, 'index'])->name('home');
});
PHP;
        File::put(base_path("routes/{$slug}.php"), $routes);

        $this->line("   [+] Đã tạo controller & route: <info>app/Http/Controllers/Themes/{$studly}/{$studly}Controller.php, routes/{$slug}.php</info>");
    }

    protected function createThemeWidgets(string $slug, string $studly, string $title): void
    {
        // 1. Hero Widget Class
        $heroWidgetClass = <<<PHP
<?php

declare(strict_types=1);

namespace App\Widgets\\{$studly};

use App\Widgets\BaseWidget;

class HeroWidget extends BaseWidget
{
    public static string \$label = 'Hero Banner {$title}';
    public static string \$description = 'Banner chào mừng chính trang chủ theme {$title}';
    public static string \$icon = 'hero';

    public function render(): string
    {
        \$title = \$this->settings['title'] ?? setting('site_title', 'Chào mừng bạn đến với {$title}');
        \$subtitle = \$this->settings['subtitle'] ?? setting('site_tagline', 'Khám phá giải pháp công nghệ và giao diện đỉnh cao.');
        \$ctaText = \$this->settings['cta_text'] ?? 'Tìm hiểu thêm';
        \$ctaUrl = \$this->settings['cta_url'] ?? '#about';

        return view('widgets.{$slug}.hero', [
            'title' => \$title,
            'subtitle' => \$subtitle,
            'ctaText' => \$ctaText,
            'ctaUrl' => \$ctaUrl,
            'settings' => \$this->settings,
        ])->render();
    }
}
PHP;
        File::put(app_path("Widgets/{$studly}/HeroWidget.php"), $heroWidgetClass);

        // 2. Master Theme Widget Class
        $themeWidgetClass = <<<PHP
<?php

declare(strict_types=1);

namespace App\Widgets\\{$studly};

use App\Widgets\BaseWidget;

class {$studly}ThemeWidget extends BaseWidget
{
    public static string \$label = '{$title} Master Theme Widget';
    public static string \$description = 'Widget chủ đạo hiển thị toàn bộ landing page cho {$title}';
    public static string \$icon = 'cube';

    public function render(): string
    {
        return view('widgets.{$slug}.theme', [
            'settings' => \$this->settings,
        ])->render();
    }
}
PHP;
        File::put(app_path("Widgets/{$studly}/{$studly}ThemeWidget.php"), $themeWidgetClass);

        // 3. Widget Views
        $heroView = <<<'BLADE'
<section class="theme-hero hero-gradient py-20 lg:py-32 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-6 animate-pulse">
            🌟 Giao diện thế hệ mới
        </span>
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-gray-900 tracking-tight leading-tight max-w-4xl mx-auto mb-6">
            {{ $title }}
        </h1>
        <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
            {{ $subtitle }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ $ctaUrl }}" class="px-8 py-4 rounded-full bg-indigo-600 text-white font-bold text-base hover:bg-indigo-700 active:scale-95 transition shadow-lg shadow-indigo-200">
                {{ $ctaText }}
            </a>
            <a href="#contact" class="px-8 py-4 rounded-full bg-white text-gray-800 font-bold text-base border border-gray-200 hover:bg-gray-50 active:scale-95 transition shadow-sm">
                Liên hệ ngay
            </a>
        </div>
    </div>
</section>
BLADE;
        File::put(resource_path("views/widgets/{$slug}/hero.blade.php"), $heroView);

        $themeView = <<<BLADE
<div class="{$slug}-master-wrapper">
    @include('widgets.{$slug}.hero', [
        'title' => setting('site_title', '{$title} - Sẵn sàng đột phá'),
        'subtitle' => setting('site_tagline', 'Trải nghiệm website thế hệ mới theo mô hình Theme-First của VGT.'),
        'ctaText' => 'Khám phá ngay',
        'ctaUrl' => '#services',
    ])

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4">Về Chúng Tôi</h2>
                <p class="text-base text-gray-600">Chúng tôi cung cấp giải pháp chuyển đổi số toàn diện và chuyên nghiệp.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-white border border-gray-100 card-hover shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl mb-6">01</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Hiệu Năng Vượt Trội</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Tối ưu tốc độ tải trang, nâng cao thứ hạng tìm kiếm và trải nghiệm khách hàng.</p>
                </div>
                <div class="p-8 rounded-2xl bg-white border border-gray-100 card-hover shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold text-xl mb-6">02</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tùy Biến Linh Hoạt</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Cấu trúc Widget độc lập, dễ dàng thay đổi nội dung trực tiếp qua trang quản trị.</p>
                </div>
                <div class="p-8 rounded-2xl bg-white border border-gray-100 card-hover shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mb-6">03</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Xuất Bản Độc Lập</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Đóng gói toàn bộ website thành bộ cài độc lập chỉ với một cú nhấp chuột.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA Section -->
    <section id="contact" class="py-20 bg-indigo-600 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Sẵn Sàng Phát Triển Dự Án Của Bạn?</h2>
            <p class="text-indigo-100 text-lg mb-8">Hãy liên hệ ngay hôm nay để nhận tư vấn giải pháp giao diện tối ưu nhất.</p>
            <a href="mailto:{{ setting('site_email', 'contact@domain.com') }}" class="inline-block px-8 py-4 rounded-full bg-white text-indigo-600 font-extrabold hover:bg-gray-100 transition shadow-lg">
                Gửi Yêu Cầu Tư Vấn
            </a>
        </div>
    </section>
</div>
BLADE;
        File::put(resource_path("views/widgets/{$slug}/theme.blade.php"), $themeView);

        $this->line("   [+] Đã tạo widgets: <info>app/Widgets/{$studly}/ ({$studly}ThemeWidget.php, HeroWidget.php)</info>");
    }

    protected function createDatabaseSeeder(string $slug, string $studly, string $title): void
    {
        $seeder = <<<PHP
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Project;
use App\Models\Widget;
use App\Services\MenuService;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class {$studly}ThemeSeeder extends Seeder
{
    /**
     * Run the {$title} theme database seeds.
     */
    public function run(?int \$projectId = null, ?int \$tenantId = null): void
    {
        \$project = \$projectId ? Project::find(\$projectId) : null;
        if (! \$project && function_exists('current_project')) {
            \$project = current_project();
        }
        if (! \$project) {
            \$project = Project::where('code', '{$slug}')->first() ?? Project::first();
        }

        \$projId = \$project?->id ?? \$projectId;
        \$tenId = \$project?->tenant_id ?? \$tenantId;

        // 1. Seed Default Settings
        \$settings = [
            'site_title' => '{$title} - Website Chuyên Nghiệp',
            'site_name' => '{$title}',
            'site_tagline' => 'Giải pháp nền tảng giao diện hiện đại & chuyên nghiệp',
            'site_description' => 'Trải nghiệm website {$title} tối ưu tốc độ và chuyển đổi',
            'site_email' => 'contact@{$slug}.local',
            'site_phone' => '0909 000 000',
            'theme_primary_color' => '#4F46E5',
            'site_copyright' => 'Copyright © 2026 {$title}. All rights reserved.',
        ];

        \$settingsService = SettingsService::getInstance();
        foreach (\$settings as \$key => \$value) {
            try {
                \$settingsService->set(\$key, \$value, 'general');
            } catch (\\Throwable \$e) {
            }
        }

        // 2. Seed Default Navigation Menu
        try {
            \$menu = Menu::withoutGlobalScopes()->firstOrCreate(
                [
                    'slug' => '{$slug}-header',
                    'location' => 'header',
                    'project_id' => \$projId,
                ],
                [
                    'name' => '{$title} Header Navigation',
                    'tenant_id' => \$tenId,
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            \$items = [
                ['title' => 'Trang chủ', 'url' => '#', 'order' => 1],
                ['title' => 'Giới thiệu', 'url' => '#about', 'order' => 2],
                ['title' => 'Dịch vụ', 'url' => '#services', 'order' => 3],
                ['title' => 'Liên hệ', 'url' => '#contact', 'order' => 4],
            ];

            foreach (\$items as \$itemData) {
                MenuItem::withoutGlobalScopes()->firstOrCreate(
                    [
                        'menu_id' => \$menu->id,
                        'title' => \$itemData['title'],
                    ],
                    [
                        'url' => \$itemData['url'],
                        'order' => \$itemData['order'],
                        'is_active' => true,
                    ]
                );
            }

            MenuService::clearMenuCache(\$projId);
        } catch (\\Throwable \$e) {
        }

        // 3. Seed Homepage Master Widget
        try {
            Widget::withoutGlobalScopes()->firstOrCreate(
                [
                    'area' => 'homepage-main',
                    'type' => '{$slug}_theme',
                    'project_id' => \$projId,
                ],
                [
                    'name' => '{$title} Homepage Main',
                    'tenant_id' => \$tenId,
                    'variant' => 'default',
                    'settings' => [
                        'title' => '{$title} - Sẵn sàng đột phá',
                        'subtitle' => 'Trải nghiệm website thế hệ mới theo mô hình Theme-First của VGT.',
                    ],
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );
        } catch (\\Throwable \$e) {
        }
    }
}
PHP;
        File::put(database_path("seeders/{$studly}ThemeSeeder.php"), $seeder);

        $this->line("   [+] Đã tạo database seeder: <info>database/seeders/{$studly}ThemeSeeder.php</info>");
    }
}

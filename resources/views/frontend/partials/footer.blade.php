{{-- Tailwind UI Modern Footer Component --}}
@php
    $getSettingValue = function($key, $default = '') {
        return setting_string($key, $default);
    };

    $footerBg     = $getSettingValue('footer_bg_color', '#000000');
    $footerText   = $getSettingValue('footer_text_color', '#000000');
    $footerLayout = $getSettingValue('footer_layout', '3-columns');
    $footerAbout  = $getSettingValue('footer_about', 'Đoạn Văn Giới thiệu');
    $copyright    = $getSettingValue('footer_copyright', 'Design By AimAgency');
    $siteName     = $getSettingValue('site_name', 'Website');
    $siteLogo     = $getSettingValue('site_logo', '');
    $hotline      = $getSettingValue('hotline', '1900 1234');
    $address      = $getSettingValue('address', 'Hà Nội, Việt Nam');
    $email        = $getSettingValue('email', 'info@example.com');
    $projectCode  = request()->route('projectCode') ?? request()->segment(1);
    $isProject    = $projectCode && $projectCode !== 'cms';
    $currentProj = function_exists('current_project') ? current_project() : null;
    $currentProjId = $currentProj?->id ?? session('current_project_id');
    $footerMenuId = $getSettingValue('footer_menu_id', null);
    $footerMenu = null;
    try {
        if (!$footerMenuId && $currentProjId) {
            $footerMenuId = \App\Models\Menu::where('project_id', $currentProjId)
                ->where(function($q) { $q->where('location', 'footer')->orWhere('slug', 'footer-menu'); })
                ->value('id');
        }
        $footerMenu = $footerMenuId ? \App\Models\Menu::with(['items' => fn($q) => $q->whereNull('parent_id')->orderBy('order')])->find($footerMenuId) : null;
    } catch (\Exception $e) {}
@endphp

<footer style="background-color: {{ $footerBg }}; color: {{ $footerText }};" class="pt-16 pb-12 border-t border-slate-800">
    <div class="container mx-auto px-4 lg:px-8">
        
        <!-- Layout 1: 4 Columns (Standard Tailwind UI) -->
        @if($footerLayout === '4-columns')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Col 1: About & Logo -->
            <div class="space-y-4">
                @if($siteLogo)
                    <a href="{{ $homeUrl }}" class="inline-block"><img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain"></a>
                @else
                    <a href="{{ $homeUrl }}" class="text-xl font-bold text-white tracking-tight">{{ $siteName }}</a>
                @endif
                <p class="text-sm leading-relaxed">
                    {{ $footerAbout ?: 'Thương hiệu uy tín cung cấp giải pháp sản phẩm & dịch vụ chất lượng cao, đem lại trải nghiệm tuyệt vời cho khách hàng.' }}
                </p>
                @if($hotline)
                <div class="pt-2">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">Hotline tư vấn</span>
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" class="text-lg font-bold text-emerald-400 hover:underline">{{ $hotline }}</a>
                </div>
                @endif
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h3 class="font-bold text-white text-base mb-4 tracking-wide uppercase text-xs">{{ $footerMenu?->name ?? 'Về Chúng Tôi' }}</h3>
                <ul class="space-y-2.5 text-sm">
                    @if($footerMenu && $footerMenu->items->isNotEmpty())
                        @foreach($footerMenu->items as $item)
                            <li><a href="{{ $item->url }}" class="hover:text-white transition">{{ $item->title }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ $homeUrl }}" class="hover:text-white transition">Trang chủ</a></li>
                        <li><a href="{{ $homeUrl }}/page/gioi-thieu" class="hover:text-white transition">Giới thiệu công ty</a></li>
                        <li><a href="{{ $homeUrl }}/blog" class="hover:text-white transition">Tin tức & Sự kiện</a></li>
                        <li><a href="{{ $homeUrl }}/contact" class="hover:text-white transition">Liên hệ & Báo giá</a></li>
                    @endif
                </ul>
            </div>

            <!-- Col 3: Categories & Products -->
            <div>
                <h3 class="font-bold text-white text-base mb-4 tracking-wide uppercase text-xs">Sản Phẩm Main</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ $homeUrl }}/products" class="hover:text-white transition">Tất cả sản phẩm</a></li>
                    <li><a href="{{ $homeUrl }}/products?featured=1" class="hover:text-white transition">Sản phẩm nổi bật</a></li>
                    <li><a href="{{ $homeUrl }}/products?discount=1" class="hover:text-white transition">Chương trình khuyến mãi</a></li>
                    <li><a href="{{ $homeUrl }}/cart" class="hover:text-white transition">Giỏ hàng của bạn</a></li>
                </ul>
            </div>

            <!-- Col 4: Newsletter & Contact -->
            <div class="space-y-4">
                <h3 class="font-bold text-white text-base mb-4 tracking-wide uppercase text-xs">Nhận Bản TinKhuyến Mãi</h3>
                <p class="text-xs text-slate-400">Đăng ký email để nhận mã giảm giá 10% cho đơn hàng đầu tiên.</p>
                <form action="#" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký!');" class="space-y-2">
                    <input type="email" placeholder="Nhập email của bạn..." required 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg transition">Đăng Ký Khuyến Mãi</button>
                </form>
            </div>
        </div>

        <!-- Layout 2: 3 Columns -->
        @elseif($footerLayout === '3-columns')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Col 1: Thông tin doanh nghiệp -->
            <div class="space-y-4">
                @if($siteLogo)
                    <a href="{{ $homeUrl }}" class="inline-block"><img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain"></a>
                @else
                    <a href="{{ $homeUrl }}" class="text-xl font-bold tracking-tight" style="color: {{ $footerText }};">{{ $siteName }}</a>
                @endif
                <p class="text-sm leading-relaxed" style="color: {{ $footerText }};">
                    {{ $footerAbout }}
                </p>
            </div>

            <!-- Col 2: Danh mục -->
            <div>
                <h3 class="font-bold text-base mb-4 tracking-wide uppercase text-xs" style="color: {{ $footerText }};">Danh mục</h3>
                <ul class="space-y-2.5 text-sm" style="color: {{ $footerText }};">
                    <li><a href="{{ $homeUrl }}" class="hover:opacity-80 transition">Trang chủ</a></li>
                    <li><a href="{{ $homeUrl }}/products" class="hover:opacity-80 transition">Sản phẩm</a></li>
                    <li><a href="{{ $homeUrl }}/blog" class="hover:opacity-80 transition">Tin tức</a></li>
                    <li><a href="{{ $homeUrl }}/contact" class="hover:opacity-80 transition">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Col 3: Chi nhánh -->
            <div>
                <h3 class="font-bold text-base mb-4 tracking-wide uppercase text-xs" style="color: {{ $footerText }};">Chi nhánh</h3>
                <ul class="space-y-2.5 text-sm" style="color: {{ $footerText }};">
                    <li><strong>Trụ sở chính:</strong> {{ $address }}</li>
                    @if($hotline)
                    <li><strong>Hotline:</strong> <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" class="hover:opacity-80">{{ $hotline }}</a></li>
                    @endif
                    @if($email)
                    <li><strong>Email:</strong> <a href="mailto:{{ $email }}" class="hover:opacity-80">{{ $email }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Layout 3: Minimal -->
        @else
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div>
                <span class="font-bold text-white text-lg block">{{ $siteName }}</span>
                <p class="text-xs text-slate-400 mt-1">{{ $footerAbout ?: 'Website chính thức - Đem lại trải nghiệm tuyệt vời cho bạn.' }}</p>
            </div>
            <div class="flex items-center gap-6 text-sm">
                <a href="{{ $homeUrl }}" class="hover:text-white">Trang chủ</a>
                <a href="{{ $homeUrl }}/products" class="hover:text-white">Sản phẩm</a>
                <a href="{{ $homeUrl }}/blog" class="hover:text-white">Tin tức</a>
                <a href="{{ $homeUrl }}/contact" class="hover:text-white">Liên hệ</a>
            </div>
        </div>
        @endif

        <!-- Widget Areas -->
        @if(function_exists('render_widgets'))
            <div class="mt-8">
                {!! render_widgets('footer') !!}
            </div>
        @endif

        <!-- Bottom Copyright Row -->
        <div class="border-t border-slate-800/80 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>{!! $copyright !!}</p>
            <div class="flex items-center gap-4">
                <span>Bảo mật thông tin</span>
                <span>•</span>
                <span>Điều khoản sử dụng</span>
            </div>
        </div>
    </div>
</footer>

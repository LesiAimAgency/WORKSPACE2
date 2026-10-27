@php
    // Helper để lấy giá trị string từ setting (có thể là array hoặc string)
    $getSettingValue = function($key, $default = '') {
        return setting_string($key, $default);
    };
    
    // Lấy màu từ website-config (topbar_bg_color) hoặc fallback
    $topbarBg = $getSettingValue('topbar_bg_color', '#1a1a1a');
    $topbarText = $getSettingValue('topbar_text_color', '#ffffff');
    $topbarTextContent = $getSettingValue('topbar_text', '');
    $headerBg = $getSettingValue('header_bg_color', '#ffffff');
    $headerText = $getSettingValue('header_text_color', '#000000');
    $headerSticky = (bool) $getSettingValue('header_sticky', 1);
    $headerLayout = $getSettingValue('header_layout', 'default');
    $showSearch = (bool) $getSettingValue('show_search', 1);
    $showCart = (bool) $getSettingValue('show_cart', 1);
    $showAccount = (bool) $getSettingValue('show_account', 1);
    $showHotlineBadge = (bool) $getSettingValue('show_hotline_badge', 1);
    $showMobileMenuText = (bool) $getSettingValue('show_mobile_menu_text', 0);
    $mobileMenuBtnText = $getSettingValue('mobile_menu_button_text', 'MENU');
    $showMobileMenuText = (bool) $getSettingValue('show_mobile_menu_text', 0);
    $mobileMenuBtnText = $getSettingValue('mobile_menu_button_text', 'MENU');
    $logo = $getSettingValue('site_logo', '');
    $siteName = $getSettingValue('site_name', 'Website');
    $hotline = $getSettingValue('hotline', '1900 1234');
    $navBgColor = $getSettingValue('nav_bg_color', '#98191F');
    $navTextColor = $getSettingValue('nav_text_color', '#ffffff');
    $navHoverColor = $getSettingValue('nav_hover_color', '#c0392b');
    
    // Get project context
    $currentProj = function_exists('current_project') ? current_project() : null;
    $currentProjId = $currentProj?->id ?? session('current_project_id');

    // Get menus - load tất cả items với children (submenu)
    $topbarMenuId = $getSettingValue('topbar_menu_id', null);
    $navMenuId = $getSettingValue('navigation_menu_id', null);

    // Load topbar menu với tất cả items
    $topbarMenu = null;
    try {
        if (!$topbarMenuId && $currentProjId) {
            $topbarMenuId = \App\Models\Menu::where('project_id', $currentProjId)
                ->where(function($q) { $q->where('location', 'topbar')->orWhere('slug', 'topbar-menu'); })
                ->value('id');
        }
        $topbarMenu = $topbarMenuId ? \App\Models\Menu::with(['items' => function($query) {
            $query->whereNull('parent_id')
                  ->orderBy('order')
                  ->with(['children' => function($q) {
                      $q->orderBy('order');
                  }]);
        }])->find($topbarMenuId) : null;
    } catch (\Exception $e) {
        // Bỏ qua lỗi nếu bảng menus không tồn tại
    }

    // Load navigation menu với tất cả items và children (submenu đa cấp)
    $navMenu = null;
    try {
        if (!$navMenuId && $currentProjId) {
            $navMenuId = \App\Models\Menu::where('project_id', $currentProjId)
                ->where(function($q) { $q->where('location', 'header')->orWhere('slug', 'main-menu'); })
                ->value('id') ?? \App\Models\Menu::where('project_id', $currentProjId)->value('id');
        }
        if (!$navMenuId) {
            $navMenuId = \App\Models\Menu::whereNull('project_id')->first()?->id;
        }
        $navMenu = $navMenuId ? \App\Models\Menu::with(['items' => function($query) {
            $query->whereNull('parent_id')
                  ->orderBy('order')
                  ->with(['children' => function($q) {
                      $q->orderBy('order')
                        ->with(['children' => function($q2) {
                            $q2->orderBy('order');
                        }]);
                  }]);
        }])->find($navMenuId) : null;
    } catch (\Exception $e) {
        // Bỏ qua lỗi nếu bảng menus không tồn tại
    }
    
    // Get styles from website-config first, then fallback to old theme options
    $topbarStyleConfig = $getSettingValue('topbar_style', '');
    if (!empty($topbarStyleConfig)) {
        $topbarStyle = $topbarStyleConfig;
    } else {
        $themeTopbar = setting('theme_option_topbar', []);
        $topbarStyle = is_array($themeTopbar) ? ($themeTopbar['topbar_style'] ?? 'style-1') : 'style-1';
    }
    
    $layoutMap = [
        'style-1' => 'style-1',
        'style-2' => 'style-2',
        'style-3' => 'style-3',
        'style-4' => 'style-4',
        'default' => 'style-1',
        'centered' => 'style-2',
        'minimal' => 'style-3',
        'fullwidth_glass' => 'style-4',
    ];

    $headerLayoutConfig = $getSettingValue('header_layout', 'style-1');
    $headerStyle = $layoutMap[$headerLayoutConfig] ?? 'style-1';



    
    // Project code for URLs
    $projectCode = request()->route('projectCode');
@endphp

<!-- Topbar -->
@php
    // Check topbar enabled từ website-config HOẶC từ theme_option_topbar
    $topbarEnabled = $getSettingValue('topbar_enabled', 0);
    $isTopbarEnabled = $topbarEnabled == 1 || $topbarEnabled === true || $topbarEnabled === '1' || $topbarEnabled === 'on';
    
    // Nếu đã chọn topbar style trong theme options thì cũng hiển thị
    if (!$isTopbarEnabled && !empty($topbarStyle) && $topbarStyle !== 'none') {
        $isTopbarEnabled = true;
    }
@endphp
@if($isTopbarEnabled)
    @if(view()->exists("frontend.partials.topbars.{$topbarStyle}"))
        @include("frontend.partials.topbars.{$topbarStyle}", [
            'topbarBg' => $topbarBg,
            'topbarText' => $topbarText,
            'topbarTextContent' => $topbarTextContent,
            'topbarMenu' => $topbarMenu
        ])
    @else
        @include('frontend.partials.topbars.style-1', [
            'topbarBg' => $topbarBg,
            'topbarText' => $topbarText,
            'topbarTextContent' => $topbarTextContent,
            'topbarMenu' => $topbarMenu
        ])
    @endif
@endif

<!-- Header -->
@php
    $headerHeight = (int) $getSettingValue('header_height', 60);
    if ($headerHeight <= 0) {
        $headerHeight = 60;
    }

    // Fallback to style-1 if selected style doesn't exist
    $headerViewPath = 'frontend.partials.headers.' . $headerStyle;
    if (!view()->exists($headerViewPath)) {
        $headerViewPath = 'frontend.partials.headers.style-1';
    }
@endphp
@include($headerViewPath, [
    'headerBg'      => $headerBg,
    'headerText'    => $headerText,
    'headerSticky'  => $headerSticky,
    'headerHeight'  => $headerHeight,
    'headerLayout'  => $headerLayout,
    'showSearch'    => $showSearch,
    'showCart'      => $showCart,
    'showAccount'   => $showAccount,
    'showHotlineBadge' => $showHotlineBadge,
    'showMobileMenuText' => $showMobileMenuText,
    'mobileMenuBtnText'  => $mobileMenuBtnText,
    'logo'          => $logo,
    'siteName'      => $siteName,
    'hotline'       => $hotline,
    'navMenu'       => $navMenu,
    'navBgColor'    => $navBgColor,
    'navTextColor'  => $navTextColor,
    'navHoverColor' => $navHoverColor,
    'projectCode'   => $projectCode
])

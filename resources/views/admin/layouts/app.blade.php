{{-- MODIFIED: 2025-01-21 --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans text-gray-800" x-data="{ showAlert: false, alertMessage: '', alertType: 'success' }">
    <!-- Global Alert -->
    <div x-show="showAlert" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-md"
         style="display: none;">
        <div :class="{
            'bg-green-50 border-green-500 text-green-800': alertType === 'success',
            'bg-red-50 border-red-500 text-red-800': alertType === 'error',
            'bg-blue-50 border-[blue-600] text-blue-800': alertType === 'info',
            'bg-yellow-50 border-yellow-500 text-yellow-800': alertType === 'warning'
        }" class="border-l-4 p-4 rounded-lg shadow-lg flex items-start gap-3">
            <svg x-show="alertType === 'success'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'error'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'info'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'warning'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium" x-text="alertMessage"></p>
            </div>
            <button @click="showAlert = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    @php
        $rawProject = $currentProject ?? (request()->attributes->get('project') ?? (function_exists('current_project') ? current_project() : session('current_project')));
        if (is_string($rawProject) && class_exists(\App\Models\Project::class)) {
            $currentProject = \App\Models\Project::where('code', $rawProject)->orWhere('id', $rawProject)->first()
                ?? ((object) ['code' => $rawProject, 'name' => $rawProject, 'id' => session('current_project_id')]);
        } elseif (is_array($rawProject)) {
            $currentProject = (object) $rawProject;
        } else {
            $currentProject = $rawProject;
        }
        $projectCode = is_object($currentProject) ? ($currentProject->code ?? null) : (is_string($currentProject) ? $currentProject : request()->route('projectCode'));
        if (!$projectCode && request()->route('projectCode')) {
            $projectCode = request()->route('projectCode');
        }
        $projectTheme = is_object($currentProject) ? ($currentProject->features['theme'] ?? '') : '';

        $isVtm = ($projectCode === 'viettinmart-eco') 
            || (is_string($projectCode) && str_contains($projectCode, 'viettinmart'))
            || ($projectTheme === 'viettinmartdemo') 
            || request()->is('viettinmart-eco/*')
            || request()->is('viettinmart*');

        $isWk = ($projectCode === 'wkcomputer')
            || (is_string($projectCode) && str_contains($projectCode, 'wkcomputer'))
            || ($projectTheme === 'wkcomputerdemo')
            || request()->is('wkcomputer/*')
            || request()->is('wkcomputer*');

        $isEhenho = ($projectCode === 'ehenho')
            || (is_string($projectCode) && (str_contains(strtolower($projectCode), 'ehenho') || str_contains(strtoupper($projectCode), 'DA010')))
            || ($projectTheme === 'ehenho')
            || request()->is('ehenho/*')
            || request()->is('ehenho*')
            || request()->is('DA010*')
            || request()->is('da010*');

        $isInbetween = ($projectCode === 'inbetween_v2' || $projectCode === 'inbetween' || $projectCode === 'DA005')
            || (is_string($projectCode) && str_contains(strtolower($projectCode), 'inbetween'))
            || ($projectTheme === 'inbetween_v2' || $projectTheme === 'inbetween')
            || request()->is('inbetween_v2/*')
            || request()->is('inbetween/*')
            || request()->is('DA005*')
            || request()->is('da005*');

        $isDaiphuc = ($projectCode === 'daiphuc68')
            || ($projectTheme === 'daiphuc68')
            || (is_string($projectCode) && str_contains(strtolower($projectCode), 'daiphuc'))
            || (! $isVtm && ! $isWk && ! $isEhenho);

        $hasCustomSidebar = $isVtm || $isWk || $isEhenho || $isInbetween || $isDaiphuc;
    @endphp

    <div class="min-h-screen flex w-full relative">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" onclick="toggleAdminSidebar(false)" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

        @if($isVtm)
            <div id="admin-sidebar-wrapper" class="fixed top-0 left-0 h-screen z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
                @include('frontend.themes.viettinmartdemo.admin.layouts.sidebar')
            </div>
        @elseif($isWk)
            <div id="admin-sidebar-wrapper" class="fixed top-0 left-0 h-screen z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
                @include('frontend.themes.wkcomputerdemo.admin.layouts.sidebar')
            </div>
        @elseif($isEhenho)
            <div id="admin-sidebar-wrapper" class="fixed top-0 left-0 h-screen z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
                @include('themes.ehenho.admin.layouts.sidebar')
            </div>
        @elseif($isInbetween || $isDaiphuc)
            <div id="admin-sidebar-wrapper" class="fixed top-0 left-0 h-screen z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
                @include('admin.layouts.sidebar')
            </div>
        @else
        <!-- Sidebar -->
        <div id="sidebar" class="w-72 bg-[#001B4E] shadow-2xl transition-all duration-300 fixed h-screen overflow-y-auto z-50 transform -translate-x-full lg:translate-x-0">
            <!-- Logo -->
            <div class="p-6 border-b border-[#002D80] bg-[#001235]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="sidebar-text flex justify-center w-full">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="max-h-24 w-auto object-contain">
                        </div>
                    </div>
                    <button id="sidebarToggle" class="p-2 text-slate-400 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <!-- Dashboard -->
                <a href="{{ route('project.admin.dashboard', request()->route('projectCode')) }}" class="nav-item flex items-center px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.dashboard') ? 'bg-[blue-600] text-white shadow-lg' : '' }}">
                    <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                    </svg>
                    <span class="font-medium nav-text ml-3">Dashboard</span>
                </a>

                <!-- E-Commerce -->
                @if(isset($currentProject) && ($currentProject->hasFeature('commerce') || $currentProject->hasFeature('product_listing')))
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">E-Commerce</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            <a href="{{ route('project.admin.products.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.products.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span class="text-sm nav-text">Sản phẩm</span>
                            </a>
                            <a href="{{ route('project.admin.categories.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.categories.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="text-sm nav-text">Danh mục</span>
                            </a>
                            <a href="{{ route('project.admin.brands.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.brands.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-sm nav-text">Thương hiệu</span>
                            </a>
                            <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.attributes.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                                </svg>
                                <span class="text-sm nav-text">Thuộc tính</span>
                            </a>
                            <a href="{{ route('project.admin.orders.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.orders.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span class="text-sm nav-text">Đơn hàng</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Content Management -->
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Nội dung</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            <a href="{{ route('project.admin.posts.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.posts.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                <span class="text-sm nav-text">Bài viết</span>
                            </a>
                            <a href="{{ route('project.admin.pages.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.pages.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm nav-text">Trang</span>
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm nav-text">FAQ</span>
                            </a>
                            <a href="{{ route('project.admin.media.list', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.media.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm nav-text">Media</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dự Án -->
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Dự Án</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            <a href="{{ route('admin.posts.index', ['type' => 'project']) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                                <span class="text-sm nav-text">• Tất cả dự án</span>
                            </a>
                            <a href="{{ route('admin.posts.create', ['type' => 'project']) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                                <span class="text-sm nav-text">• Thêm dự án</span>
                            </a>
                            <a href="{{ route('admin.categories.index', ['type' => 'project_cat']) }}" class="flex items-center justify-between px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                                <span class="text-sm nav-text">• Danh mục dự án</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">project_cat</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bất động sản -->
                @if(isset($currentProject) && ($currentProject->hasFeature('property') || $currentProject->hasFeature('agent') || $currentProject->hasFeature('gallery_360')))
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Bất động sản</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            @if($currentProject->hasFeature('property') && Route::has('project.admin.properties.index'))
                            <a href="{{ route('project.admin.properties.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.properties.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Danh sách BĐS</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('agent') && Route::has('project.admin.agents.index'))
                            <a href="{{ route('project.admin.agents.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.agents.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Môi giới</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('gallery_360') && Route::has('project.admin.gallery-360.index'))
                            <a href="{{ route('project.admin.gallery-360.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.gallery-360.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Thư viện ảnh 360</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Khách sạn & Lưu trú -->
                @if(isset($currentProject) && ($currentProject->hasFeature('room') || $currentProject->hasFeature('hotel_booking') || $currentProject->hasFeature('amenity')))
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Khách sạn</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            @if($currentProject->hasFeature('room'))
                            <a href="{{ route('project.admin.rooms.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.rooms.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Quản lý Phòng</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('hotel_booking'))
                            <a href="{{ route('project.admin.hotel-bookings.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.hotel-bookings.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Đặt phòng</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('amenity'))
                            <a href="{{ route('project.admin.amenities.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.amenities.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Tiện nghi</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Y tế & Phòng khám -->
                @if(isset($currentProject) && ($currentProject->hasFeature('doctor') || $currentProject->hasFeature('patient') || $currentProject->hasFeature('prescription') || $currentProject->hasFeature('booking')))
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Y tế</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            @if($currentProject->hasFeature('booking'))
                            <a href="{{ route('project.admin.appointments.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.appointments.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Đặt lịch hẹn</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('doctor'))
                            <a href="{{ route('project.admin.doctors.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.doctors.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Bác sĩ</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('patient'))
                            <a href="{{ route('project.admin.patients.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.patients.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Bệnh nhân</span>
                            </a>
                            @endif
                            @if($currentProject->hasFeature('prescription'))
                            <a href="{{ route('project.admin.prescriptions.index', request()->route('projectCode')) }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.prescriptions.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <span class="text-sm nav-text">Đơn thuốc</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Page Builder -->
                <div class="mb-4">
                    <div class="dropdown-parent">
                        <button class="nav-item flex items-center justify-between w-full px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                                </svg>
                                <span class="font-medium nav-text ml-3">Page Builder</span>
                            </div>
                            <svg class="h-4 w-4 nav-text dropdown-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu ml-4 space-y-1 max-h-0 overflow-hidden transition-all duration-300">
                            <a href="{{ isset($currentProject) ? route('project.admin.widgets.index', $currentProject->code) : route('cms.widgets.index') }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.widgets.*') || request()->routeIs('cms.widgets.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                <span class="text-sm nav-text">Widgets</span>
                            </a>
                            <a href="{{ isset($currentProject) ? route('project.admin.menus.index', $currentProject->code) : route('cms.menus.index') }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.menus.*') || request()->routeIs('cms.menus.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <span class="text-sm nav-text">Menus</span>
                            </a>

                            <!-- <a href="{{ isset($currentProject) ? route('project.admin.website-config.index', $currentProject->code) : route('cms.website-config.index') }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.website-config.*') || request()->routeIs('cms.website-config.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <span class="text-sm nav-text">Cấu hình Website</span>
                            </a>
                             <a href="{{ isset($currentProject) ? route('project.admin.theme-options.index', $currentProject->code) : route('cms.theme-options.index') }}" class="flex items-center px-4 py-2 text-slate-400 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.theme-options.*') || request()->routeIs('cms.theme-options.*') ? 'bg-[blue-600] text-white' : '' }}">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                <span class="text-sm nav-text">Theme Options</span>
                            </a> -->
                        </div>
                    </div>
                </div>

             

                <!-- Cài đặt -->
                <a href="{{ isset($currentProject) && $currentProject && \Route::has('project.admin.settings.index') ? route('project.admin.settings.index', $currentProject->code) : route('admin.settings.index') }}" class="nav-item flex items-center px-4 py-3 mb-2 text-slate-300 hover:bg-[blue-600] hover:text-white rounded-lg transition-all duration-200 {{ request()->routeIs('project.admin.settings.*') || request()->routeIs('admin.settings.*') ? 'bg-[blue-600] text-white shadow-lg' : '' }}">
                    <svg class="h-5 w-5 nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium nav-text ml-3">Cài đặt</span>
                </a>
            </nav>
            
            <!-- Footer Info -->
            <div class="mt-auto p-4 border-t border-[#002D80]">
                <div class="text-slate-400 text-xs text-center space-y-1">
                    <p class="font-semibold">Version 2.0.0</p>
                    <p> ©2026 VNEXT GLOBAL TECH</p>
                    <p class="text-slate-500">All rights reserved</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 {{ $hasCustomSidebar ? 'lg:ml-[250px] ml-0' : 'lg:ml-72 ml-0' }} transition-all duration-200">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="flex justify-between items-center px-4 lg:px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleAdminSidebar(true)" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg focus:outline-none" title="Mở menu điều hướng">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-lg lg:text-2xl font-bold text-gray-900 truncate">@yield('page-title', 'Dashboard')</h1>
                        <div class="hidden sm:inline-block ml-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                            Online
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                       
                        
                        <!-- User Menu -->
                        @php
                            $currentUser = $authUser ?? auth()->user();
                        @endphp
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $currentUser->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <div class="h-8 w-8 bg-gradient-to-r from-blue-400 to-pink-400 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($currentUser->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 bg-gray-50">
                @if(session('alert'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showAlert('{{ session('alert')['message'] }}', '{{ session('alert')['type'] }}');
                        });
                    </script>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

<script>
// Global Alert Function
window.showAlert = function(message, type = 'success') {
    const event = new CustomEvent('show-alert', {
        detail: { message, type }
    });
    window.dispatchEvent(event);
};

document.addEventListener('alpine:init', () => {
    window.addEventListener('show-alert', (e) => {
        const body = document.querySelector('body');
        if (body.__x) {
            body.__x.$data.alertMessage = e.detail.message;
            body.__x.$data.alertType = e.detail.type;
            body.__x.$data.showAlert = true;
            
            setTimeout(() => {
                body.__x.$data.showAlert = false;
            }, 3000);
        }
    });
});

// Mobile Sidebar Toggle
function toggleAdminSidebar(open) {
    const wrapper = document.getElementById('admin-sidebar-wrapper') || document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!wrapper) return;
    
    const isClosed = wrapper.classList.contains('-translate-x-full');
    const shouldOpen = (open !== undefined) ? open : isClosed;
    
    if (shouldOpen) {
        wrapper.classList.remove('-translate-x-full');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        wrapper.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        toggleAdminSidebar(false);
    }
});

// Sidebar Toggle (Desktop default theme)
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarTexts = document.querySelectorAll('.sidebar-text, .nav-text');
const navIcons = document.querySelectorAll('.nav-icon');
const mainContent = document.querySelector('.flex-1.flex.flex-col.ml-72') || document.querySelector('.flex-1.flex.flex-col');

if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('w-72');
        sidebar.classList.toggle('w-16');
        
        if (mainContent) {
            mainContent.classList.toggle('ml-72');
            mainContent.classList.toggle('ml-16');
        }
        
        sidebarTexts.forEach(text => {
            text.classList.toggle('hidden');
        });
        
        navIcons.forEach(icon => {
            icon.classList.toggle('mr-3');
            icon.classList.toggle('mx-auto');
        });
    });
}

// Dropdown Menus
document.querySelectorAll('.dropdown-parent').forEach(parent => {
    const button = parent.querySelector('button');
    const menu = parent.querySelector('.dropdown-menu');
    const arrow = parent.querySelector('.dropdown-arrow');
    
    button.addEventListener('click', function() {
        const isOpen = menu.style.maxHeight && menu.style.maxHeight !== '0px';
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
            if (otherMenu !== menu) {
                otherMenu.style.maxHeight = '0px';
                otherMenu.parentElement.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
            }
        });
        
        if (isOpen) {
            menu.style.maxHeight = '0px';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            menu.style.maxHeight = menu.scrollHeight + 'px';
            arrow.style.transform = 'rotate(180deg)';
        }
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@stack('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>




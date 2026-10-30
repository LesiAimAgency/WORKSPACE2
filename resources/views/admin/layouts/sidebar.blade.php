@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentType = request('type') ?? request('post_type') ?? request('taxonomy') ?? '';
    $isProjectCat = (request()->routeIs('*categories*') && in_array(request('type') ?? request('taxonomy'), ['project', 'project_cat']));
    $isPostCat = (request()->routeIs('*categories*') && in_array(request('type') ?? request('taxonomy'), ['post', 'category', 'post_cat', 'news', 'news_cat']));
    $isProductCat = (request()->routeIs('*categories*') && !in_array(request('type') ?? request('taxonomy'), ['project', 'project_cat', 'post', 'category', 'post_cat', 'news', 'news_cat']));
    $newSubmissionsCount = 0;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('form_submissions')) {
            $newSubmissionsCount = \App\Models\FormSubmission::where('status', 'new')->orWhere('status', 'unread')->count();
        }
    } catch (\Throwable $e) {}

    $sidebarAppName = config('app.name') ?: env('APP_NAME', 'Admin');
    $nameWords = array_values(array_filter(explode(' ', trim($sidebarAppName))));
    $sidebarAppInitials = count($nameWords) >= 2 
        ? mb_strtoupper(mb_substr($nameWords[0], 0, 1) . mb_substr($nameWords[1], 0, 1))
        : mb_strtoupper(mb_substr($sidebarAppName, 0, 2));
@endphp

<aside id="admin-sidebar" class="w-72 bg-[#0B1528] text-slate-300 h-screen flex flex-col border-r border-slate-800 shadow-2xl overflow-hidden select-none">
    <!-- Brand / Header -->
    <div class="p-5 border-b border-slate-800/80 bg-[#080E1C] flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-rose-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-rose-950/40 text-white font-extrabold text-lg tracking-wider group-hover:scale-105 transition-transform duration-300">
                {{ $sidebarAppInitials }}
            </div>
            <div>
                <h2 class="text-base font-bold text-white tracking-wide leading-tight group-hover:text-amber-400 transition-colors">{{ $sidebarAppName }}</h2>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] font-medium text-emerald-400 uppercase tracking-wider">Admin Portal</span>
                </div>
            </div>
        </a>
        <a href="{{ url('/') }}" target="_blank" title="Xem trang chủ ngoài giao diện" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/60 rounded-lg transition-all" data-tooltip="Xem Web">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Navigation Scroll Area -->
    <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6 text-sm custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #1e293b transparent;">
        
        <!-- SECTION 1: TỔNG QUAN -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                <span>Tổng Quan</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/') }}" target="_blank"
                       class="flex items-center justify-between px-3.5 py-2 rounded-lg text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 transition-all text-xs">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Trang Chủ (Frontend)
                        </span>
                        <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-400">/</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- SECTION 2: QUẢN LÝ NỘI DUNG (POSTS & TAXONOMIES) -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Quản Lý Nội Dung CRUD</span>
            </div>
            
            <div class="space-y-1" x-data="{ 
                openProducts: {{ ($currentType === 'product' || request()->routeIs('admin.products.*') || $isProductCat || request()->routeIs('admin.attributes.*')) ? 'true' : 'true' }},
                openProjects: {{ ($currentType === 'project' || $isProjectCat) ? 'true' : 'true' }},
                openNews: {{ ($currentType === 'post' || $isPostCat || ($currentRoute === 'admin.posts.index' && !$currentType)) ? 'true' : 'true' }},
                openPages: {{ $currentType === 'page' || request()->routeIs('admin.pages.*') ? 'true' : 'false' }},
                openFaqs: {{ $currentType === 'faq' ? 'true' : 'false' }}
            }">

                <!-- 1. SẢN PHẨM (Products: Route #3 & #4) -->
                <!-- <div>
                    <button @click="openProducts = !openProducts" 
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800/70 hover:text-white transition-all {{ ($currentType === 'product' || request()->routeIs('admin.products.*') || $isProductCat || request()->routeIs('admin.attributes.*')) ? 'bg-slate-800/80 text-white font-medium' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Sản Phẩm</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openProducts }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openProducts" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.products.index') }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ (request()->routeIs('admin.products.index') || ($currentType === 'product' && !request()->routeIs('admin.products.create') && !request()->routeIs('admin.posts.create'))) ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Tất cả sản phẩm
                        </a>
                        <a href="{{ route('admin.products.create') }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ (request()->routeIs('admin.products.create') || ($currentType === 'product' && request()->routeIs('admin.posts.create'))) ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thêm sản phẩm mới
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'product_cat']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ $isProductCat ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Danh mục sản phẩm
                        </a>
                        <a href="{{ route('admin.attributes.index') }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.attributes.*') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thuộc tính sản phẩm
                        </a>
                        <a href="{{ route('admin.settings.group', 'filter') }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ (request()->route('group') === 'filter' || request()->route('group') === 'fillter' || request()->routeIs('admin.settings.filter') || request()->routeIs('admin.settings.fillter')) ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Cấu hình bộ lọc (Filter)
                        </a>
                        <a href="{{ route('products.index') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Xem /products</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div> -->

                <!-- 2. DỰ ÁN (Projects: Route #5 & #6) -->
                <!-- <div>
                    <button @click="openProjects = !openProjects" 
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800/70 hover:text-white transition-all {{ ($currentType === 'project' || $isProjectCat) ? 'bg-slate-800/80 text-white font-medium' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Dự Án</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openProjects }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openProjects" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.posts.index', ['type' => 'project']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'project' && !request()->routeIs('admin.posts.create') && !request()->routeIs('*categories*')) ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Tất cả dự án
                        </a>
                        <a href="{{ route('admin.posts.create', ['type' => 'project']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'project' && request()->routeIs('admin.posts.create')) ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thêm dự án mới
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'project_cat']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ $isProjectCat ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Danh mục dự án
                        </a>
                        <a href="{{ route('projects.index') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Xem /projects</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div> -->

                <!-- 3. TIN TỨC (News: Route #7 & #8) -->
                <!-- <div>
                    <button @click="openNews = !openNews" 
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800/70 hover:text-white transition-all {{ ($currentType === 'post' || $isPostCat || ($currentRoute === 'admin.posts.index' && !$currentType)) ? 'bg-slate-800/80 text-white font-medium' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span>Tin Tức</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openNews }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openNews" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.posts.index', ['type' => 'post']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'post' && !request()->routeIs('admin.posts.create') && !request()->routeIs('*categories*')) ? 'text-cyan-400 font-semibold bg-cyan-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Tất cả bài viết
                        </a>
                        <a href="{{ route('admin.posts.create', ['type' => 'post']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'post' && request()->routeIs('admin.posts.create')) ? 'text-cyan-400 font-semibold bg-cyan-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Viết bài mới
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'category']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ $isPostCat ? 'text-cyan-400 font-semibold bg-cyan-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Chuyên mục tin tức
                        </a>
                        <a href="{{ route('news.index') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Xem /news</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div> -->

                <!-- 4. TRANG TĨNH & GIỚI THIỆU (Pages: Route #1, #2, #9) -->
                <!-- <div>
                    <button @click="openPages = !openPages" 
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800/70 hover:text-white transition-all {{ ($currentType === 'page' || request()->routeIs('admin.pages.*')) ? 'bg-slate-800/80 text-white font-medium' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Trang Tĩnh (Pages)</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openPages }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openPages" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.posts.index', ['type' => 'page']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'page' && !request()->routeIs('admin.posts.create')) ? 'text-purple-400 font-semibold bg-purple-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Quản lý danh sách trang
                        </a>
                        <a href="{{ route('admin.posts.create', ['type' => 'page']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'page' && request()->routeIs('admin.posts.create')) ? 'text-purple-400 font-semibold bg-purple-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thêm trang mới
                        </a>
                        <a href="{{ route('about-us') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Về Chúng Tôi (/about-us)</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <a href="{{ route('contact.page') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Liên Hệ & FAQs (/contact)</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div> -->

                <!-- 5. CÂU HỎI THƯỜNG GẶP (FAQ: Phục vụ trang /contact) -->
                <!-- <div>
                    <button @click="openFaqs = !openFaqs" 
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800/70 hover:text-white transition-all {{ $currentType === 'faq' ? 'bg-slate-800/80 text-white font-medium' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-amber-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Hỏi Đáp (FAQ)</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openFaqs }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openFaqs" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.posts.index', ['type' => 'faq']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'faq' && !request()->routeIs('admin.posts.create')) ? 'text-amber-300 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Tất cả câu hỏi FAQ
                        </a>
                        <a href="{{ route('admin.posts.create', ['type' => 'faq']) }}" 
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ ($currentType === 'faq' && request()->routeIs('admin.posts.create')) ? 'text-amber-300 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thêm câu hỏi mới
                        </a>
                        <a href="{{ url('/contact#faq') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1 rounded-md text-[11px] text-slate-500 hover:text-slate-300">
                            <span>Xem /contact (Tab FAQ)</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div> -->

            </div>
        </div>

        <!-- SECTION 3: KHÁCH HÀNG & BIỂU MẪU LIÊN HỆ (Route #10) -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                <span>Khách Hàng & CRM</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.form-submissions.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.form-submissions.*') ? 'bg-rose-600 text-white font-semibold shadow-md shadow-rose-900/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Biểu Mẫu Liên Hệ</span>
                        </div>
                        @if($newSubmissionsCount > 0)
                            <span class="px-2 py-0.5 text-xs font-bold bg-rose-500 text-white rounded-full animate-bounce">
                                {{ $newSubmissionsCount }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <!-- SECTION: WORKSPACE MATERIAL LAB & SURVEY -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Material Lab &amp; Khảo Sát</span>
            </div>
            <ul class="space-y-1" x-data="{ openMaterialLab: true }">
                <li>
                    <button @click="openMaterialLab = !openMaterialLab"
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.material-lab.*') ? 'bg-gradient-to-r from-rose-600 to-amber-600 text-white font-semibold shadow-md shadow-rose-950/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('admin.material-lab.*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Thư Viện Vật Liệu</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] bg-slate-800/80 px-1.5 py-0.5 rounded text-amber-300 font-mono">Lab</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMaterialLab }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openMaterialLab" x-collapse class="pl-9 pr-2 py-1.5 space-y-1">
                        <a href="{{ route('admin.material-lab.index') }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.material-lab.index') || request()->routeIs('admin.material-lab.edit') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Tất cả vật liệu
                        </a>
                        <a href="{{ route('admin.material-lab.create') }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.material-lab.create') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thêm vật liệu mới
                        </a>
                        <a href="{{ route('admin.material-lab.brands') }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.material-lab.brands') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Thương hiệu / Nhà cung cấp
                        </a>
                        <a href="{{ route('admin.material-lab.categories') }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.material-lab.categories') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Phân loại danh mục
                        </a>
                        <a href="{{ route('admin.material-lab.config') }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ request()->routeIs('admin.material-lab.config') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Cấu hình các bước Wizard
                        </a>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.surveys.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.surveys.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md shadow-blue-950/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>Khảo Sát Khách Hàng</span>
                        </div>
                        <span class="text-[10px] bg-slate-800/80 px-1.5 py-0.5 rounded text-cyan-300 font-mono">NPS</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- SECTION: WORKSPACE DỊCH VỤ & COVERFLOW -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Dịch Vụ &amp; Trình Diễn</span>
            </div>
            <ul class="space-y-1" x-data="{ openServicesMenu: true }">
                <li>
                    <button @click="openServicesMenu = !openServicesMenu"
                            type="button"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ (request()->is('coverflow*') || (request()->routeIs('admin.widgets.*') && request('search') === 'Dịch Vụ')) ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Dịch Vụ (Services)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] bg-slate-800/80 px-1.5 py-0.5 rounded text-emerald-300 font-mono">5 SV</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openServicesMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="openServicesMenu" x-collapse class="pl-9 pr-2 py-1.5 space-y-1">
                        <a href="{{ route('admin.widgets.index', ['search' => 'Dịch Vụ']) }}"
                           class="block px-3 py-1.5 rounded-md text-xs transition-colors {{ (request()->routeIs('admin.widgets.*') && request('search') === 'Dịch Vụ') ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            • Cấu hình Widget Dịch Vụ
                        </a>
                        <a href="{{ route('coverflow.index') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1.5 rounded-md text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-800/40">
                            <span>• Coverflow Tổng Quan</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <a href="{{ route('coverflow.service') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1.5 rounded-md text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-800/40">
                            <span>• Giới Thiệu (Introductions)</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <a href="{{ route('coverflow.project') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-1.5 rounded-md text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-800/40">
                            <span>• Dự Án & So Sánh (Projects)</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </li>
            </ul>
        </div>

        <!-- SECTION 4: GIAO DIỆN & TIỆN ÍCH -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Giao Diện & Tiện Ích</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.widgets.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.widgets.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        <span>Quản Lý Widgets (CMS)</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.menus.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.menus.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span>Menu Điều Hướng</span>
                    </a>
                </li>
               
                <li>
                    <a href="{{ route('admin.media.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.media.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Thư Viện Media</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- SECTION 5: CẤU HÌNH & HỆ THỐNG -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Cấu Hình & Hệ Thống</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ (request()->routeIs('admin.settings.index') || (request()->routeIs('admin.settings.*') && !in_array(request()->route('group'), ['contact', 'partners']) && !request()->routeIs('admin.settings.partners'))) ? 'bg-slate-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cài Đặt Hệ Thống</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.group', 'partners') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ ((request()->routeIs('admin.settings.group') && request()->route('group') === 'partners') || request()->routeIs('admin.settings.partners')) ? 'bg-amber-600 text-white font-semibold shadow-md' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Đối Tác</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.group', 'contact') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ (request()->routeIs('admin.settings.group') && request()->route('group') === 'contact') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>Thông Tin Liên Hệ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Tài Khoản Quản Trị</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.replace-domain.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.replace-domain.*') ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold shadow-md shadow-indigo-950/40' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span>Đổi Domain Database</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- User & Logout Footer -->
    <div class="p-4 border-t border-slate-800 bg-[#080E1C] flex items-center justify-between">
        <div class="flex items-center space-x-3 overflow-hidden">
            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-xs">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="truncate">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@daiphuc68.com' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</aside>

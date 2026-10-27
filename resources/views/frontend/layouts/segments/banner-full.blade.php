<div class="relative bg-cover bg-center py-16" style="background-image: url('{{ $banner ?? ($defaultImage ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200') }}')">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="container mx-auto px-4 relative z-10">
        <nav class="text-white text-sm mb-2">
            <a href="/" class="hover:underline">Trang chủ</a> / <span>@yield('page-title', $defaultTitle ?? 'Trang')</span>
        </nav>
        <h1 class="text-5xl font-bold text-white">@yield('page-title', $defaultTitle ?? 'Trang')</h1>
    </div>
</div>

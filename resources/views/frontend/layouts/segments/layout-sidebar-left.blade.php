<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <aside class="lg:col-span-1">
        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
            @yield('sidebar')
            {!! render_widgets($widgetArea ?? 'sidebar') !!}
        </div>
    </aside>
    <main class="lg:col-span-3">
        @if($hasBanner && $bannerStyle !== 'style-2')
            @include('frontend.layouts.segments.banner-contained', ['defaultTitle' => $defaultTitle ?? 'Trang', 'defaultImage' => $defaultImage ?? null])
        @endif
        <div class="bg-white rounded-lg shadow-sm border p-8">
            @yield($contentSection ?? 'content')
        </div>
    </main>
</div>

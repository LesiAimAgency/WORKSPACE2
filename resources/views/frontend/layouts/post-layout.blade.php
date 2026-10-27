@php
    $layoutType = get_theme_layout('post');
    $config = get_layout_config($layoutType);
    $hasSidebar = $config['sidebar'] ?? false;
    $hasBanner = $config['banner'] ?? false;
    $bannerStyle = $config['banner_style'] ?? null;
@endphp

@extends('frontend.themes.storefront.layout')

@section('content')
    {{-- Full Width Banner (style 2 - above container) --}}
    @if($hasBanner && $bannerStyle === 'style-2')
        <div class="kalles-section page_section_heading">
            <div class="page-head tc pr oh cat_bg_img page_head_">
                <div class="parallax-inner nt_parallax_false lazyload t__0 l__0" data-bgset="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200" data-sizes="auto" data-parent-fit="cover"></div>
                <div class="container pr z_100">
                    <h1 class="mb__5 cw">Bài viết</h1>
                </div>
            </div>
        </div>
    @endif

    {{-- Content Container --}}
    <div class="container container_cat mb__60">
        <div class="row pt__30">
            @if(!$hasSidebar)
                <div class="col-12">
                    @yield('post-content')
                </div>
            @elseif($config['sidebar'] === 'right')
                <div class="col-lg-9 col-12">
                    @yield('post-content')
                </div>
                <div class="col-lg-3 col-12">
                    @yield('sidebar')
                    {!! render_widgets('post-sidebar') !!}
                </div>
            @else
                <div class="col-lg-3 col-12 order-2 order-lg-1">
                    @yield('sidebar')
                    {!! render_widgets('post-sidebar') !!}
                </div>
                <div class="col-lg-9 col-12 order-1 order-lg-2">
                    @yield('post-content')
                </div>
            @endif
        </div>
    </div>
@endsection

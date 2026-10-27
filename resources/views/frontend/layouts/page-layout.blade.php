@php
    $layoutType = get_theme_layout('page');
    $config = get_layout_config($layoutType);
    $hasSidebar = $config['sidebar'] ?? false;
    $hasBanner = $config['banner'] ?? false;
    $bannerStyle = $config['banner_style'] ?? null;
@endphp

@extends('frontend.layouts.kalles')

@section('content')
    {{-- Full Width Banner (style 2 - above container) --}}
    @if($hasBanner && $bannerStyle === 'style-2')
        @include('frontend.layouts.segments.banner-full', ['defaultTitle' => 'Trang'])
    @endif

    {{-- Content Container --}}
    <div class="container mx-auto px-4 py-12">
        @if(!$hasSidebar)
            @include('frontend.layouts.segments.layout-full', ['contentSection' => 'page-content'])
        @elseif($config['sidebar'] === 'right')
            @include('frontend.layouts.segments.layout-sidebar-right', [
                'contentSection' => 'page-content', 
                'widgetArea' => 'page-sidebar',
                'defaultTitle' => 'Trang'
            ])
        @else
            @include('frontend.layouts.segments.layout-sidebar-left', [
                'contentSection' => 'page-content', 
                'widgetArea' => 'page-sidebar',
                'defaultTitle' => 'Trang'
            ])
        @endif
    </div>
@endsection

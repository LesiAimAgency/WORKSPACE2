@php
    $sProj = session('current_project');
    $features = is_object($sProj) ? ($sProj->features ?? []) : [];
    $theme = ($features['theme'] ?? null) ?: (setting('theme') ?: 'viettinmartdemo');
    $themeAppearanceView = "frontend.themes.{$theme}.admin.settings.appearance";
@endphp

@if(view()->exists($themeAppearanceView))
    @include($themeAppearanceView)
@elseif(view()->exists('frontend.themes.viettinmartdemo.admin.settings.appearance'))
    @include('frontend.themes.viettinmartdemo.admin.settings.appearance')
@else
    @extends('cms.settings.template', ['title' => 'Cấu hình Giao diện'])
    @section('form-content')
        <div class="bg-white p-6 rounded-lg border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cấu hình Giao diện & Design System</h3>
            <p class="text-sm text-gray-600">Cấu hình giao diện chuẩn cho hệ thống.</p>
        </div>
    @endsection
@endif

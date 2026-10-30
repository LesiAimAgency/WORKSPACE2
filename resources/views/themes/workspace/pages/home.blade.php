@extends('themes.workspace.layouts.app')

@section('content')
  @php
    $workspaceActiveWidgets = \App\Models\Widget::withoutGlobalScopes()
        ->where('area', 'homepage-main')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();
  @endphp

  @if($workspaceActiveWidgets->isNotEmpty())
    @foreach($workspaceActiveWidgets as $widget)
      {!! $widget->getRenderedContent() !!}
    @endforeach
  @else
    {{-- 1. Home Banner (Logo Workspace) --}}
    @include('themes.workspace.widgets.home')

    {{-- 2. Giá trị cốt lõi (Core Values) --}}
    @include('themes.workspace.widgets.core')

    {{-- 3. Dịch vụ (Service - Coverflow & Detail Flow) --}}
    @include('themes.workspace.widgets.coverflow')
    @include('themes.workspace.widgets.coverflow_service')

    {{-- 4. Chuyên gia (About Expert) --}}
    @include('themes.workspace.widgets.expert')

    {{-- 5. Why Choose Us_1 --}}
    @include('themes.workspace.widgets.why')

    {{-- 6. Connect Us --}}
    @include('themes.workspace.widgets.connect')
  @endif
@endsection

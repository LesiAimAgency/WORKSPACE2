@extends('themes.workspace.layouts.app')

@section('content')
  @php
    $workspaceActiveWidgets = \App\Models\Widget::where('area', 'homepage-main')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
  @endphp

  @if($workspaceActiveWidgets->isNotEmpty())
    @foreach($workspaceActiveWidgets as $widget)
      {!! $widget->getRenderedContent() !!}
    @endforeach
  @else
    {{-- Fallback: Render chuẩn chuỗi widget của Workspace với dữ liệu mặc định --}}
    @include('themes.workspace.widgets.home')
    @include('themes.workspace.widgets.core')
    @include('themes.workspace.widgets.service')
    @include('themes.workspace.widgets.workflow')
    @include('themes.workspace.widgets.project')
    @include('themes.workspace.widgets.expert')
    @include('themes.workspace.widgets.why')
    @include('themes.workspace.widgets.connect')
  @endif
@endsection

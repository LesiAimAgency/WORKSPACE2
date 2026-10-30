@extends('themes.workspace.layouts.app')

@section('content')
  {{-- 1. Home Banner (Logo Workspace) --}}
  @include('themes.workspace.widgets.home')

  {{-- 2. Giá trị cốt lõi (Core Values) --}}
  @include('themes.workspace.widgets.core')

  {{-- 3. Bộ sưu tập Dịch vụ dạng Coverflow (Overview Services) --}}
  @include('themes.workspace.widgets.coverflow')

  {{-- Bỏ include id="about" và id="design-project" theo yêu cầu người dùng --}}
  {{-- @include('themes.workspace.widgets.workflow') --}}
  {{-- @include('themes.workspace.widgets.project') --}}

  {{-- Thay thế bằng widget Coverflow: Service Introductions & Projects (Mở khi bấm View More, cuộn ngang giữa 2 view) --}}
  @include('themes.workspace.widgets.coverflow_service')

  {{-- 4. Why Choosing Workspace & Connect Us --}}
  @include('themes.workspace.widgets.why')
@endsection

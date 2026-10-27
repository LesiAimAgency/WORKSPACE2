@extends('frontend.layouts.page-layout')

@section('page-title', 'Trang chủ')

@section('page-content')
<h1 class="text-3xl font-bold mb-8">Welcome</h1>

<div class="mb-8">
    {!! render_widgets('homepage-top') !!}
</div>

<div class="mb-8">
    {!! render_widgets('homepage-main') !!}
</div>
@endsection

@section('sidebar')
<div class="space-y-6">
    {!! render_widgets('sidebar') !!}
    @include('frontend.partials.sidebar-sample')
</div>
@endsection

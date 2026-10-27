@extends('frontend.layouts.page-layout')

@section('page-title', $page->title ?? 'Page')

@section('page-content')
    {!! $page->getRenderedContent() !!}
@endsection

@section('sidebar')
<div class="space-y-6">
    {!! render_widgets('page-sidebar') !!}
</div>
@endsection

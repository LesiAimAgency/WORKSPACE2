@extends('frontend.layouts.kalles')

@section('title', $page->title ?? 'Trang')

@section('content')
    {!! $page->getRenderedContent() !!}
@endsection

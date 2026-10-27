@extends('cms.layouts.app')

@section('title', 'Quản lý Media')
@section('page-title', 'Thư viện Media')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    @include('cms.components.media-manager', ['inline' => true])
</div>
@endsection

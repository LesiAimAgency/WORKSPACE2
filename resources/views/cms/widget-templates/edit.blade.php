@extends('cms.layouts.app')

@section('title', 'Sửa Widget Template')
@section('page-title', 'Sửa Widget Template')

@section('content')
    <livewire:admin.widget-template-builder :id="$id" />
@endsection

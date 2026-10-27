@extends('cms.layouts.app')

@section('title', 'Chi tiết thương hiệu')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chi tiết thương hiệu</h1>
            <p class="text-gray-600">Xem thông tin chi tiết của thương hiệu</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('project.admin.brands.edit', [request()->route('projectCode'), $brand->id]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('project.admin.brands.index', request()->route('projectCode')) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Brand Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <!-- Brand Header -->
            <div class="flex items-start space-x-6 mb-8">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    @if($brand->logo)
                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" 
                             class="h-24 w-24 rounded-lg object-cover border-2 border-gray-200">
                    @else
                        <div class="h-24 w-24 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ substr($brand->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Brand Info -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h2 class="text-3xl font-bold text-gray-900">{{ $brand->name }}</h2>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $brand->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-2">
                        <span class="font-medium">Slug:</span> 
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $brand->slug }}</code>
                    </p>
                    <p class="text-gray-500 text-sm">
                        Tạo lúc: {{ $brand->created_at->format('d/m/Y H:i') }}
                        @if($brand->updated_at != $brand->created_at)
                            • Cập nhật: {{ $brand->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên thương hiệu</label>
                            <p class="text-gray-900">{{ $brand->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <p class="text-gray-900 font-mono text-sm bg-gray-50 px-3 py-2 rounded">{{ $brand->slug }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $brand->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $brand->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                            </span>
                        </div>

                        @if($brand->logo)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
                            <p class="text-gray-900 text-sm break-all">{{ $brand->logo }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- SEO Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin SEO</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <p class="text-gray-900">{{ $brand->meta_title ?: 'Chưa có' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <p class="text-gray-900">{{ $brand->meta_description ?: 'Chưa có' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($brand->description)
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $brand->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

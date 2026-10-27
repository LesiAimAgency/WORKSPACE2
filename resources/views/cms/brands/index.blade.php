{{-- MODIFIED: 2025-12-20 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý thương hiệu')
@section('page-title', 'Thương hiệu')

@section('content')
@include('cms.components.alert')

<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Tìm kiếm thương hiệu..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả trạng thái</option>
            <option value="trashed" {{ request('status') == 'trashed' ? 'selected' : '' }}>Thùng rác</option>
        </select>
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Tìm kiếm
        </button>
    </form>
    
    <a href="{{ route('project.admin.brands.create', request()->route('projectCode')) }}" 
       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Thêm thương hiệu
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($brands as $brand)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="h-10 w-10 rounded-lg mr-3 object-cover border">
                        @else
                            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg mr-3 flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ substr($brand->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $brand->name }}</div>
                            @if($brand->description)
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit(strip_tags($brand->description), 50) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <code class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $brand->slug }}</code>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ $brand->products()->count() }} sản phẩm
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 text-xs leading-5 font-semibold rounded-full 
                        {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $brand->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-3">
                        @if(request('status') === 'trashed')
                            <form method="POST" action="{{ route('project.admin.brands.restore', [request()->route('projectCode'), $brand->id]) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-900 flex items-center gap-1 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="{{ route('project.admin.brands.force-delete', [request()->route('projectCode'), $brand->id]) }}" 
                                  class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn thương hiệu này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center gap-1 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Xóa vĩnh viễn
                                </button>
                            </form>
                        @else
                            <a href="{{ route('project.admin.brands.edit', [request()->route('projectCode'), $brand->id]) }}" 
                               class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Sửa
                            </a>
                            <form method="POST" action="{{ route('project.admin.brands.destroy', [request()->route('projectCode'), $brand->id]) }}" 
                                  class="inline" onsubmit="return confirm('Bạn có chắc muốn chuyển thương hiệu này vào thùng rác?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center gap-1 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Xóa
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Chưa có thương hiệu nào</p>
                        <p class="text-gray-400 text-sm mt-1">Hãy thêm thương hiệu đầu tiên để bắt đầu</p>
                        <a href="{{ route('project.admin.brands.create', request()->route('projectCode')) }}" 
                           class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Thêm thương hiệu
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($brands->hasPages())
<div class="mt-6">
    {{ $brands->links() }}
</div>
@endif
@endsection

{{-- MODIFIED: 2025-01-22 Professional Nested Category Management --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Danh mục sản phẩm')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Danh mục sản phẩm</h1>
                <p class="text-gray-600 text-sm mt-1">Quản lý danh mục cấp 1 và cấp 2 một cách dễ dàng</p>
            </div>
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.create', $currentProject->code) : route('cms.property-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm danh mục
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="mt-6 flex space-x-3">
            <form method="GET" class="flex-1 flex space-x-2">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm danh mục..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <!-- Categories Table (Nested View) -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Danh mục</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cấp độ</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sản phẩm</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition" style="border-left: {{ $category->level * 4 }}px solid #3b82f6;">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3" style="margin-left: {{ $category->level * 24 }}px;">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $category->level == 0 ? '#dbeafe' : '#f3f4f6' }};">
                                    @if($category->image)
                                        <img src="{{ $category->image }}" class="w-full h-full object-cover rounded-lg" alt="">
                                    @else
                                        <svg class="w-6 h-6" style="color: {{ $category->level == 0 ? '#2563eb' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ ['#dbeafe', '#dcfce7', '#fef3c7', '#fce7f3'][$category->level % 4] ?? '#f3f4f6' }}; color: {{ ['#1e40af', '#166534', '#92400e', '#9f1239'][$category->level % 4] ?? '#374151' }};">
                                Cấp {{ $category->level + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $category->products_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $category->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.edit', [$currentProject->code, $category->id]) : route('cms.property-categories.edit', $category->id) }}" title="Sửa" class="p-2 text-gray-600 hover:bg-blue-100 hover:text-blue-600 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.destroy', [$currentProject->code, $category->id]) : route('cms.property-categories.destroy', $category->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Xóa" class="p-2 text-gray-600 hover:bg-red-100 hover:text-red-600 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="font-medium">Chưa có danh mục nào</p>
                                <p class="text-sm">Hãy bắt đầu bằng cách thêm danh mục gốc mới</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

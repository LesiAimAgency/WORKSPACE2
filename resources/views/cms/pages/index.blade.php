@extends('cms.layouts.app')

@section('title', 'Quản lý trang')
@section('page-title', 'Trang')

@section('content')
<div class="space-y-6">
    <!-- Header với nút thêm mới -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Quản lý trang</h1>
                <p class="text-sm text-gray-500">Quản lý các trang tĩnh của website</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.posts.create', ['type' => 'page']) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + Thêm trang mới
                </a>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm trang..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách trang -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pages as $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($page->featured_image)
                                <img src="{{ $page->featured_image }}" alt="" class="w-10 h-10 rounded object-cover mr-3">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $page->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $page->template ?: 'Mặc định' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($page->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $page->status === 'published' ? 'Đã xuất bản' : 
                                   ($page->status === 'draft' ? 'Nháp' : 'Lưu trữ') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $page->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ url('/' . $page->slug) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 text-xs px-2.5 py-1 bg-emerald-50 rounded border border-emerald-200 hover:bg-emerald-100 transition">Xem web</a>
                                <a href="{{ route('admin.posts.edit', $page) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Sửa</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $page) }}" class="inline" 
                                      onsubmit="return confirm('Bạn có chắc muốn xóa trang này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Chưa có trang nào</p>
                                <p class="text-sm">Bắt đầu tạo trang đầu tiên của bạn</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

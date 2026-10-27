@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Quản lý ' . ($config['name'] ?? 'dữ liệu'))
@section('page-title', 'Danh sách ' . ($config['name'] ?? 'dữ liệu'))

@section('content')
@php
    $projCode = is_object($currentProject ?? null) ? ($currentProject->code ?? null) : (is_string($currentProject ?? null) ? $currentProject : null);

    $createUrl = Route::has('admin.posts.create') 
        ? route('admin.posts.create', ['type' => $postType]) 
        : ($projCode && Route::has('project.admin.posts.create') 
            ? route('project.admin.posts.create', ['projectCode' => $projCode, 'type' => $postType]) 
            : url('/admin/posts/create?type=' . $postType));
@endphp

<div class="bg-white rounded-lg shadow-sm">
    <!-- Toolbar -->
    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="type" value="{{ $postType }}">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm kiếm..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="trashed" {{ request('status') == 'trashed' ? 'selected' : '' }}>Thùng rác</option>
            </select>
            
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Lọc
            </button>
        </form>

        <div class="flex gap-2">
            <a href="{{ $createUrl }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm mới
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500">
                    <th class="p-4 font-medium">{{ ($postType ?? '') === 'faq' ? 'Câu hỏi (Tiêu đề)' : 'Tiêu đề' }}</th>
                    @if(($postType ?? '') === 'faq')
                        <th class="p-4 font-medium text-center w-24">Thứ tự</th>
                    @endif
                    <th class="p-4 font-medium">Trạng thái</th>
                    <th class="p-4 font-medium">Ngày tạo</th>
                    <th class="p-4 font-medium text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($posts as $post)
                    @php
                        $editUrl = Route::has('admin.posts.edit')
                            ? route('admin.posts.edit', $post)
                            : ($projCode && Route::has('project.admin.posts.edit')
                                ? route('project.admin.posts.edit', ['projectCode' => $projCode, 'post' => $post])
                                : url('/admin/posts/' . $post->id . '/edit'));
                            
                        $destroyUrl = Route::has('admin.posts.destroy')
                            ? route('admin.posts.destroy', $post)
                            : ($projCode && Route::has('project.admin.posts.destroy')
                                ? route('project.admin.posts.destroy', ['projectCode' => $projCode, 'post' => $post])
                                : url('/admin/posts/' . $post->id));
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                @if(in_array('featured_image', $config['supports'] ?? []) && $post->featured_image)
                                    <img src="{{ $post->featured_image }}" class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ $editUrl }}" class="font-medium text-gray-900 hover:text-blue-600 block mb-0.5">
                                        {{ $post->title }}
                                    </a>
                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                        <span class="font-mono text-gray-400">/{{ $post->slug }}</span>
                                        <a href="{{ $post->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-0.5 font-medium" title="Xem trên website">
                                            <span>Xem trang</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @if(($postType ?? '') === 'faq')
                            <td class="p-4 text-center font-mono text-xs font-semibold text-slate-600">
                                <span class="inline-block px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                    {{ $post->order ?? 0 }}
                                </span>
                            </td>
                        @endif
                        <td class="p-4">
                            @if(request('status') === 'trashed' || $post->trashed())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Thùng rác
                                </span>
                            @elseif($post->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Đã xuất bản
                                </span>
                            @elseif($post->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Bản nháp
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Lưu trữ
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">
                            {{ $post->created_at->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(request('status') === 'trashed' || $post->trashed())
                                    <form method="POST" action="{{ Route::has('admin.posts.restore') ? route('admin.posts.restore', $post) : ($projCode ? route('project.admin.posts.restore', ['projectCode' => $projCode, 'post' => $post]) : url('/admin/posts/' . $post->id . '/restore')) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:text-green-700 transition bg-white rounded-lg border border-gray-200 hover:border-green-100 shadow-sm" title="Khôi phục">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ Route::has('admin.posts.force-delete') ? route('admin.posts.force-delete', $post) : ($projCode ? route('project.admin.posts.force-delete', ['projectCode' => $projCode, 'post' => $post]) : url('/admin/posts/' . $post->id . '/force-delete')) }}" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition bg-white rounded-lg border border-gray-200 hover:border-red-100 shadow-sm" title="Xóa vĩnh viễn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ $post->url }}" target="_blank" class="p-2 text-gray-400 hover:text-emerald-600 transition bg-white rounded-lg border border-gray-200 hover:border-emerald-100 shadow-sm" title="Xem ngoài website">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <a href="{{ $editUrl }}" class="p-2 text-gray-400 hover:text-blue-600 transition bg-white rounded-lg border border-gray-200 hover:border-blue-100 shadow-sm" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <form method="POST" action="{{ $destroyUrl }}" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển vào thùng rác?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition bg-white rounded-lg border border-gray-200 hover:border-red-100 shadow-sm" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            Chưa có dữ liệu nào. <a href="{{ $createUrl }}" class="text-blue-600 hover:underline">Tạo mới ngay</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

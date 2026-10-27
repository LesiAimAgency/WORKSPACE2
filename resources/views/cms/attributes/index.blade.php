@extends('cms.layouts.app')

@section('title', 'Quản lý bộ thuộc tính sản phẩm')
@section('page-title', 'Bộ Thuộc Tính Sản Phẩm')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('admin.attributes.index') }}" class="flex flex-wrap items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm thuộc tính (Màu sắc, Kích thước...)..." 
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Tìm
            </button>
            @if(request('search'))
                <a href="{{ route('admin.attributes.index') }}" class="px-3 py-2 text-xs text-gray-600 hover:text-gray-900 border rounded-lg bg-gray-50">
                    Xóa lọc
                </a>
            @endif
        </form>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.attributes.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-xs transition gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Thêm thuộc tính mới
            </a>
            <a href="{{ route('admin.products.index') }}" 
               class="px-3 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition font-medium">
                Về kho sản phẩm
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('alert'))
        <div class="p-4 rounded-xl text-sm font-medium {{ session('alert')['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
            {{ session('alert')['message'] }}
        </div>
    @endif

    <!-- Attributes Table -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/75 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Danh sách bộ thuộc tính dùng cho sản phẩm biến thể ({{ $attributes->total() }})
            </h3>
            <span class="text-xs text-gray-500">Thuộc tính được chia sẻ và tái sử dụng cho tất cả sản phẩm biến thể</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Tên thuộc tính</th>
                        <th class="px-6 py-3.5">Slug</th>
                        <th class="px-6 py-3.5">Loại hiển thị</th>
                        <th class="px-6 py-3.5 min-w-[280px]">Các giá trị cấu thành</th>
                        <th class="px-6 py-3.5 text-center">Số sản phẩm dùng</th>
                        <th class="px-6 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($attributes as $attr)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <a href="{{ route('admin.attributes.edit', $attr->id) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $attr->name }}
                                </a>
                            </div>
                            @if($attr->description)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1 pl-4">{{ $attr->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            {{ $attr->slug }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $type = $attr->meta_data['type'] ?? 'select';
                            @endphp
                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $type === 'color' ? 'bg-pink-100 text-pink-800' : ($type === 'button' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $type === 'color' ? 'Màu sắc (Color)' : ($type === 'button' ? 'Nút chọn (Button)' : 'Danh sách (Select)') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-1.5 max-w-lg">
                                @forelse($attr->children as $val)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        @if(!empty($val->meta_data['color_code']))
                                            <span class="w-2.5 h-2.5 rounded-full border border-gray-300 shadow-2xs" style="background-color: {{ $val->meta_data['color_code'] }}"></span>
                                        @endif
                                        <span>{{ $val->name }}</span>
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Chưa có giá trị nào</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full font-bold text-xs">
                                {{ $attr->posts_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.attributes.edit', $attr->id) }}" 
                                   class="px-2.5 py-1 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition">
                                    Sửa
                                </a>
                                <form method="POST" action="{{ route('admin.attributes.destroy', $attr->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bộ thuộc tính này và toàn bộ các giá trị con?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="max-w-sm mx-auto space-y-3">
                                <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <p class="font-medium text-gray-700">Chưa có thuộc tính nào được định nghĩa</p>
                                <p class="text-xs text-gray-400">Tạo các thuộc tính chung như Màu sắc, Kích thước, Cấp điện áp... để dễ dàng thêm sản phẩm có biến thể.</p>
                                <a href="{{ route('admin.attributes.create') }}" class="inline-block px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-xs">
                                    + Tạo thuộc tính đầu tiên
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attributes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attributes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@extends('cms.layouts.app')

@section('title', 'Thương hiệu / Nhà cung cấp - Material Lab')
@section('page-title', 'Thương hiệu / Nhà cung cấp')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.material-lab.index') }}" class="hover:text-rose-600 transition-colors">Thư Viện Vật Liệu</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Thương hiệu / Nhà cung cấp</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Quản lý Thương hiệu / Nhà cung cấp</h2>
            <p class="text-xs text-gray-500 mt-0.5">Thêm, đổi tên hoặc xóa các thương hiệu / nhà cung cấp dùng chung cho toàn bộ vật liệu.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.material-lab.categories') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                Phân loại danh mục
            </a>
            <a href="{{ route('admin.material-lab.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Tất cả vật liệu
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-xs font-medium rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-medium rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Cột trái: Form thêm mới -->
        <div class="lg:col-span-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-3 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                Thêm thương hiệu mới
            </h3>

            <form action="{{ route('admin.material-lab.save-brands') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="add">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Tên Thương hiệu / Nhà cung cấp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Ví dụ: An Cường / Vietnam, Saint-Gobain..." class="w-full px-3 py-2.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <p class="text-[11px] text-gray-400 mt-1">Tên sẽ hiển thị trực tiếp trên thẻ sản phẩm tại bước 5 của Material Lab.</p>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm vào danh sách
                </button>
            </form>
        </div>

        <!-- Cột phải: Danh sách thương hiệu -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                    Danh sách Thương hiệu / Nhà cung cấp ({{ count($brands) }})
                </h3>
                <span class="text-[11px] text-gray-400">Đổi tên sẽ tự động cập nhật cho tất cả vật liệu đang dùng</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($brands as $brand)
                    @php
                        $usedCount = $counts[$brand] ?? 0;
                    @endphp
                    <div class="p-3.5 hover:bg-gray-50/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                         x-data="{ editing: false, newName: @js($brand) }">
                        
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ mb_strtoupper(mb_substr($brand, 0, 1)) }}
                            </span>

                            <!-- Chế độ xem -->
                            <div x-show="!editing" class="min-w-0 flex-1 flex items-center gap-2.5">
                                <span class="text-xs font-semibold text-gray-900 truncate">{{ $brand }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $usedCount > 0 ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $usedCount }} vật liệu
                                </span>
                            </div>

                            <!-- Chế độ sửa tên -->
                            <form x-show="editing" x-cloak action="{{ route('admin.material-lab.save-brands') }}" method="POST" class="flex-1 flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="action" value="rename">
                                <input type="hidden" name="old_name" value="{{ $brand }}">
                                <input type="text" name="new_name" x-model="newName" required class="flex-1 px-2.5 py-1.5 text-xs border border-rose-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg">
                                    Lưu
                                </button>
                                <button type="button" @click="editing = false; newName = @js($brand)" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg">
                                    Hủy
                                </button>
                            </form>
                        </div>

                        <!-- Nút thao tác -->
                        <div x-show="!editing" class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="editing = true" class="px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                Sửa tên
                            </button>

                            @if($usedCount === 0)
                                <form action="{{ route('admin.material-lab.save-brands') }}" method="POST" onsubmit="return confirm('Xóa thương hiệu này khỏi danh sách?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="name" value="{{ $brand }}">
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-medium text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        Xóa
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.material-lab.index', ['search' => $brand]) }}" class="px-2.5 py-1.5 text-[11px] font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    Xem SP &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        Chưa có thương hiệu nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

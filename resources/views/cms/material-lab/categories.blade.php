@extends('cms.layouts.app')

@section('title', 'Phân loại danh mục - Material Lab')
@section('page-title', 'Phân loại danh mục')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.material-lab.index') }}" class="hover:text-rose-600 transition-colors">Thư Viện Vật Liệu</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Phân loại danh mục</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Quản lý Phân loại danh mục vật liệu</h2>
            <p class="text-xs text-gray-500 mt-0.5">Thêm, đổi tên hoặc xóa các nhóm chất liệu / danh mục (Vinyl, Terrazzo, Porcelain, HPL...).</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.material-lab.brands') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                Thương hiệu / Nhà cung cấp
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
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                Thêm phân loại danh mục mới
            </h3>

            <form action="{{ route('admin.material-lab.save-categories') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="add">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Tên Phân loại danh mục <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Ví dụ: Terrazzo, Porcelain, HPL, Acoustic..." class="w-full px-3 py-2.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-[11px] text-gray-400 mt-1">Tên danh mục hiển thị trên ô mẫu (swatch) góc trái của mỗi thẻ sản phẩm.</p>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm vào danh sách
                </button>
            </form>
        </div>

        <!-- Cột phải: Danh sách danh mục -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                    Danh sách Phân loại danh mục ({{ count($categories) }})
                </h3>
                <span class="text-[11px] text-gray-400">Đổi tên sẽ tự động cập nhật cho tất cả vật liệu đang dùng</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                    @php
                        $usedCount = $counts[$cat] ?? 0;
                    @endphp
                    <div class="p-3.5 hover:bg-gray-50/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                         x-data="{ editing: false, newName: @js($cat) }">
                        
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ mb_strtoupper(mb_substr($cat, 0, 1)) }}
                            </span>

                            <!-- Chế độ xem -->
                            <div x-show="!editing" class="min-w-0 flex-1 flex items-center gap-2.5">
                                <span class="text-xs font-semibold text-gray-900 truncate">{{ $cat }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $usedCount > 0 ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $usedCount }} vật liệu
                                </span>
                            </div>

                            <!-- Chế độ sửa tên -->
                            <form x-show="editing" x-cloak action="{{ route('admin.material-lab.save-categories') }}" method="POST" class="flex-1 flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="action" value="rename">
                                <input type="hidden" name="old_name" value="{{ $cat }}">
                                <input type="text" name="new_name" x-model="newName" required class="flex-1 px-2.5 py-1.5 text-xs border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                                    Lưu
                                </button>
                                <button type="button" @click="editing = false; newName = @js($cat)" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg">
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
                                <form action="{{ route('admin.material-lab.save-categories') }}" method="POST" onsubmit="return confirm('Xóa danh mục này khỏi danh sách?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="name" value="{{ $cat }}">
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-medium text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        Xóa
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.material-lab.index', ['category' => $cat]) }}" class="px-2.5 py-1.5 text-[11px] font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    Xem SP &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        Chưa có danh mục nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

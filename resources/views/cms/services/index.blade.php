@extends('cms.layouts.app')

@section('title', 'Quản lý Dịch Vụ (Services)')
@section('page-title', 'Dịch Vụ (Services)')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Links (Identical to Material Lab) -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-rose-600 to-amber-600 flex items-center justify-center text-white shadow-md shadow-rose-500/20 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg font-bold text-gray-900">Thư viện Dịch Vụ (Services)</h2>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 rounded-full">
                            {{ $totalCount }} dịch vụ hiện có
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Quản lý danh mục dịch vụ, thông tin Coverflow, các hạng mục dịch vụ chi tiết (Introductions) và dự án (Projects) hiển thị tại
                        <a href="{{ url('/#coverflow') }}" target="_blank" class="text-rose-600 font-medium hover:underline">Trang chủ (#coverflow) &nearr;</a>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.services.categories') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Danh mục ({{ count($categories ?? []) }})
                </a>
                <a href="{{ route('admin.projects.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Quản lý Dự Án
                </a>
                <a href="{{ route('admin.services.config') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Cấu hình hiển thị
                </a>
                <a href="{{ url('/#coverflow') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem Trang chủ
                </a>
                <a href="{{ route('admin.services.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm dịch vụ mới
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.services.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Tìm theo tên dịch vụ, mã, hạng mục chi tiết..."
                       class="w-full px-3.5 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
            </div>

            <div>
                <select name="category" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-rose-500 bg-white">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        @php $catName = is_array($cat) ? ($cat['name'] ?? '') : $cat; @endphp
                        <option value="{{ $catName }}" {{ request('category') === $catName ? 'selected' : '' }}>{{ $catName }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-rose-500 bg-white">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                    Lọc dữ liệu
                </button>
                @if(request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 text-xs text-gray-600 hover:text-gray-900">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12">#</th>
                        <th class="py-3.5 px-4 min-w-[260px]">Dịch vụ (Service)</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Danh mục &amp; Chip Coverflow</th>
                        <th class="py-3.5 px-4 min-w-[280px]">Dịch vụ chi tiết (Introductions)</th>
                        <th class="py-3.5 px-4 min-w-[160px]">Dự án (Projects)</th>
                        <th class="py-3.5 px-4 text-center w-24">Hiển thị</th>
                        <th class="py-3.5 px-4 text-right w-28">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($services as $item)
                        @php
                            $rawImg = $item['image'] ?? '';
                            $imgSrc = str_starts_with($rawImg, 'http') ? $rawImg : asset(ltrim($rawImg, '/'));
                            $introList = $item['intro_items'] ?? [];
                            $projectList = $item['projects'] ?? [];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-500">
                                {{ $item['number'] }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        @if($rawImg)
                                            <img src="{{ $imgSrc }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] font-bold text-gray-400">SVC</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.services.edit', $item['id']) }}" class="font-bold text-gray-900 hover:text-rose-600 transition-colors line-clamp-1">
                                            {{ $item['title'] }}
                                        </a>
                                        <div class="text-gray-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $item['code'] }}</span>
                                            <span>•</span>
                                            <span class="font-mono text-[10px] text-gray-400">/{{ $item['slug'] }}</span>
                                        </div>
                                        @if(!empty($item['desc']))
                                            <div class="text-gray-400 text-[11px] mt-1 line-clamp-1">
                                                {{ $item['desc'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded-md font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $item['category'] }}
                                </span>
                                <div class="mt-1.5 text-[11px] text-gray-500 leading-tight bg-gray-50 border border-gray-200/80 rounded px-2 py-1 inline-block">
                                    <div class="font-semibold text-gray-700">{{ $item['chip_line_1'] }}</div>
                                    <div class="text-gray-500">{{ $item['chip_line_2'] }}</div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="space-y-1.5">
                                    @forelse($introList as $intro)
                                        <div class="p-2 rounded-lg bg-rose-50/50 border border-rose-100">
                                            <div class="font-bold text-[#EB2026] text-[11px] leading-snug">
                                                {{ $intro['title'] ?? '' }}
                                            </div>
                                            @if(!empty($intro['desc']))
                                                <div class="text-[10px] text-gray-600 line-clamp-1 mt-0.5">
                                                    {{ $intro['desc'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-gray-400 italic text-[11px]">Chưa có mục chi tiết</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900">
                                    {{ count($projectList) }} dự án
                                </div>
                                @if(!empty($projectList[0]['title']))
                                    <div class="text-gray-500 text-[11px] mt-0.5 line-clamp-1">
                                        • {{ $projectList[0]['title'] }}
                                    </div>
                                @endif
                                @if(!empty($projectList[1]['title']))
                                    <div class="text-gray-400 text-[11px] line-clamp-1">
                                        • {{ $projectList[1]['title'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           {{ !empty($item['is_active']) ? 'checked' : '' }}
                                           onchange="toggleServiceActive('{{ $item['id'] }}', this)"
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-600"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.services.edit', $item['id']) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa dịch vụ">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $item['id']) }}" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này không?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="text-base font-medium">Chưa có dịch vụ nào phù hợp</div>
                                <p class="text-xs mt-1">Nhấp vào "Thêm dịch vụ mới" để bắt đầu nhập liệu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleServiceActive(id, el) {
    fetch('{{ route("admin.services.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            el.checked = !el.checked;
            alert('Không thể cập nhật trạng thái dịch vụ');
        }
    })
    .catch(err => {
        el.checked = !el.checked;
        alert('Lỗi kết nối máy chủ');
    });
}
</script>
@endsection

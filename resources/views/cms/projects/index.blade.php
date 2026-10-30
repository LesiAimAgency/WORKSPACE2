@extends('cms.layouts.app')

@section('title', 'Quản lý Dự Án Tiêu Biểu (Projects)')
@section('page-title', 'Quản lý Dự Án (Projects)')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Links (Identical to Material Lab) -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg font-bold text-gray-900">Quản lý Dự Án Tiêu Biểu (Projects)</h2>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full">
                            {{ $totalCount }} dự án hiện có
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Quản lý bộ thông số kỹ thuật, 3 hình ảnh (Ảnh bìa, Thiết kế 3D, Thực tế) và liên kết (Relationship) trực tiếp với
                        <a href="{{ route('admin.services.index') }}" class="text-rose-600 font-medium hover:underline">Dịch Vụ (Services) &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.services.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Dịch Vụ ({{ count($services ?? []) }})
                </a>
                <a href="{{ route('admin.services.config') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    Cấu hình nhãn
                </a>
                <a href="{{ url('/#coverflow') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem Trang chủ
                </a>
                <a href="{{ route('admin.projects.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm dự án mới
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Tìm theo tên dự án, mã, vị trí, loại hình, phong cách..."
                       class="w-full px-3.5 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
            </div>

            <div>
                <select name="service" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-rose-500 bg-white">
                    <option value="">-- Lọc theo Dịch vụ liên kết --</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc['slug'] }}" {{ request('service') === $svc['slug'] ? 'selected' : '' }}>
                            {{ $svc['number'] }} - {{ $svc['title'] }}
                        </option>
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
                @if(request()->anyFilled(['search', 'service', 'status']))
                    <a href="{{ route('admin.projects.index') }}" class="px-3 py-2 text-xs text-gray-600 hover:text-gray-900">
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
                        <th class="py-3.5 px-4 w-16">Mã</th>
                        <th class="py-3.5 px-4 min-w-[240px]">Dự án (Project)</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Dịch vụ liên kết (Relationship)</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Thông số Kỹ thuật</th>
                        <th class="py-3.5 px-4 min-w-[160px]">Bộ 3 Ảnh (Bìa / 3D / Thực tế)</th>
                        <th class="py-3.5 px-4 text-center w-24">Hiển thị</th>
                        <th class="py-3.5 px-4 text-right w-28">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($projects as $item)
                        @php
                            $coverUrl = str_starts_with($item['cover_image'] ?? '', 'http') ? $item['cover_image'] : asset(ltrim($item['cover_image'] ?? '', '/'));
                            $designUrl = str_starts_with($item['design_image'] ?? '', 'http') ? $item['design_image'] : asset(ltrim($item['design_image'] ?? '', '/'));
                            $resultUrl = str_starts_with($item['result_image'] ?? '', 'http') ? $item['result_image'] : asset(ltrim($item['result_image'] ?? '', '/'));
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-500">
                                {{ $item['code'] }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        @if(!empty($item['cover_image']))
                                            <img src="{{ $coverUrl }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] font-bold text-gray-400">PRJ</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.projects.edit', $item['id']) }}" class="font-bold text-gray-900 hover:text-rose-600 transition-colors line-clamp-1">
                                            {{ $item['title'] }}
                                        </a>
                                        <div class="text-gray-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <span>{{ $item['location'] ?: 'Chưa cập nhật vị trí' }}</span>
                                        </div>
                                        @if(!empty($item['typology']))
                                            <div class="text-gray-400 text-[11px] mt-0.5 line-clamp-1">
                                                Loại hình: {{ $item['typology'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse(($item['service_slugs'] ?? []) as $sSlug)
                                        @php $linkedSvc = $serviceMap[$sSlug] ?? null; @endphp
                                        @if($linkedSvc)
                                            <a href="{{ route('admin.services.edit', $linkedSvc['id']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition-colors">
                                                <span class="font-mono font-bold text-[10px]">{{ $linkedSvc['number'] }}</span>
                                                <span>{{ $linkedSvc['title'] }}</span>
                                            </a>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[11px] bg-gray-100 text-gray-600">{{ $sSlug }}</span>
                                        @endif
                                    @empty
                                        <span class="text-gray-400 italic text-[11px]">Chưa gắn dịch vụ</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $itemSpecs = !empty($item['specs']) && is_array($item['specs'])
                                        ? $item['specs']
                                        : [
                                            ['label' => $labels['label_area'] ?? 'Area', 'value' => $item['area'] ?? ''],
                                            ['label' => $labels['label_style'] ?? 'Style', 'value' => $item['style'] ?? ''],
                                            ['label' => $labels['label_tech'] ?? 'Tech', 'value' => $item['tech'] ?? ''],
                                        ];
                                @endphp
                                <div class="space-y-0.5 text-[11px]">
                                    @foreach(array_slice($itemSpecs, 0, 3) as $sp)
                                        <div class="line-clamp-1">
                                            <span class="text-gray-400">{{ $sp['label'] ?? 'Spec' }}:</span>
                                            <span class="font-semibold text-gray-800">{{ !empty($sp['value']) ? $sp['value'] : '—' }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($itemSpecs) > 3)
                                        <div class="text-[10px] font-semibold text-blue-600">+{{ count($itemSpecs) - 3 }} thông số khác</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-10 h-10 rounded border border-gray-200 overflow-hidden bg-gray-100" title="1. Ảnh bìa dự án">
                                        <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="w-10 h-10 rounded border border-blue-200 overflow-hidden bg-gray-100" title="2. Ảnh Thiết kế 3D (Before)">
                                        <img src="{{ $designUrl }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="w-10 h-10 rounded border border-emerald-200 overflow-hidden bg-gray-100" title="3. Ảnh Thực tế (After)">
                                        <img src="{{ $resultUrl }}" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           {{ !empty($item['is_active']) ? 'checked' : '' }}
                                           onchange="toggleProjectActive('{{ $item['id'] }}', this)"
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-600"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.projects.edit', $item['id']) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa dự án">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $item['id']) }}" onsubmit="return confirm('Bạn có chắc muốn xóa dự án này không?');" class="inline">
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
                                <div class="text-base font-medium">Chưa có dự án nào phù hợp</div>
                                <p class="text-xs mt-1">Nhấp vào "Thêm dự án mới" để bắt đầu nhập liệu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleProjectActive(id, el) {
    fetch('{{ route("admin.projects.toggle") }}', {
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
            alert('Không thể cập nhật trạng thái dự án');
        }
    })
    .catch(err => {
        el.checked = !el.checked;
        alert('Lỗi kết nối máy chủ');
    });
}
</script>
@endsection

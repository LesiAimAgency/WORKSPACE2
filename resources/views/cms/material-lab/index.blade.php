@extends('cms.layouts.app')

@section('title', 'Quản lý Thư viện vật liệu (Material Lab)')
@section('page-title', 'Thư viện vật liệu (Material Lab)')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Links -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Danh mục vật liệu &amp; Tiêu chuẩn kỹ thuật</h2>
            <p class="text-xs text-gray-500 mt-1">Quản lý toàn bộ 15+ mẫu vật liệu hiển thị trong wizard gợi ý vật liệu tại <a href="{{ route('material-lab') }}" target="_blank" class="text-rose-600 font-semibold hover:underline">/material-lab &nearr;</a></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('material-lab') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.material-lab.config') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                Cấu hình Tiêu đề &amp; Bộ lọc
            </a>
            <a href="{{ route('admin.material-lab.create') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm vật liệu mới
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.material-lab.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã, hãng..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <select name="category" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <option value="">Tất cả phân loại</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="origin" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <option value="">Tất cả xuất xứ</option>
                    <option value="Vietnam" {{ request('origin') === 'Vietnam' ? 'selected' : '' }}>Hàng Việt</option>
                    <option value="Import" {{ request('origin') === 'Import' ? 'selected' : '' }}>Nhập khẩu</option>
                    <option value="Both" {{ request('origin') === 'Both' ? 'selected' : '' }}>Liên doanh / Cả hai</option>
                </select>
            </div>
            <div>
                <select name="budget_level" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <option value="">Mọi mức ngân sách</option>
                    <option value="1" {{ request('budget_level') == '1' ? 'selected' : '' }}>1: Tiết kiệm</option>
                    <option value="2" {{ request('budget_level') == '2' ? 'selected' : '' }}>2: Cân bằng</option>
                    <option value="3" {{ request('budget_level') == '3' ? 'selected' : '' }}>3: Cao cấp</option>
                    <option value="4" {{ request('budget_level') == '4' ? 'selected' : '' }}>4: Siêu cao cấp</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded-lg hover:bg-black transition-colors">
                    Lọc dữ liệu
                </button>
                @if(request()->anyFilled(['search', 'category', 'origin', 'budget_level']))
                    <a href="{{ route('admin.material-lab.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-colors" title="Xóa lọc">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Materials Table -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 font-semibold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4">Vật liệu</th>
                        <th class="py-3.5 px-4">Phân loại</th>
                        <th class="py-3.5 px-4">Vị trí áp dụng</th>
                        <th class="py-3.5 px-4">Xuất xứ &amp; Tồn kho</th>
                        <th class="py-3.5 px-4">Độ bền &amp; Giá</th>
                        <th class="py-3.5 px-4 text-center">Hiển thị</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($materials as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-4 text-center text-gray-400 font-mono">
                                {{ $item->sort_order }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0"
                                         style="background: radial-gradient(circle at 30% 30%, #E8E2D8 0%, white 70%);">
                                        <span class="text-[10px] font-bold text-gray-600">{{ substr($item->category, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-[13px]">{{ $item->name }}</div>
                                        <div class="text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span>{{ $item->brand }}</span>
                                            <span class="font-mono text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $item->code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-gray-800">
                                <span class="px-2 py-1 bg-gray-100 rounded text-gray-700">{{ $item->category }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @if(is_array($item->suitable_locations))
                                        @foreach($item->suitable_locations as $loc)
                                            <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-medium">{{ $loc }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div>{{ $item->origin_label ?: $item->origin }}</div>
                                <div class="text-gray-500 text-[11px] mt-0.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full {{ $item->availability === 'Stock' ? 'bg-emerald-500' : 'bg-amber-500' }} mr-1"></span>
                                    {{ $item->availability }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900">{{ $item->cost_label ?: 'Theo báo giá' }}</div>
                                <div class="text-gray-500 text-[11px]">Độ bền: {{ $item->durability_years }} năm (Cấp {{ $item->budget_level }})</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           {{ $item->is_active ? 'checked' : '' }} 
                                           onchange="toggleMaterialActive({{ $item->id }}, this)" 
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-600"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.material-lab.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.material-lab.destroy', $item->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa vật liệu này không?');" class="inline">
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
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <div class="text-base font-medium">Chưa có vật liệu nào trong cơ sở dữ liệu</div>
                                <p class="text-xs mt-1">Nhấp vào "Thêm vật liệu mới" để bắt đầu nhập liệu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($materials->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $materials->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleMaterialActive(id, el) {
    fetch('{{ route("admin.material-lab.toggle") }}', {
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
            alert('Không thể cập nhật trạng thái');
        }
    })
    .catch(err => {
        el.checked = !el.checked;
        alert('Lỗi kết nối máy chủ');
    });
}
</script>
@endsection

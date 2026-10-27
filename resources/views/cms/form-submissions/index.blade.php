@extends('cms.layouts.app')

@section('title', 'Form Liên hệ')
@section('page-title', 'Quản lý Form liên hệ')

@section('content')

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Form liên hệ</h1>
                <p class="text-gray-600 text-sm mt-1">Danh sách thông tin khách hàng đã gửi form (Tổng cộng: {{ $submissions->total() }})</p>
            </div>
            @if($submissions->total() > 0)
            <div class="flex items-center gap-2 flex-wrap">
                @if(request()->anyFilled(['search','status','form_name']))
                    <button type="button" onclick="confirmDeleteAll(true)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors cursor-pointer"
                            title="Xóa tất cả các form theo kết quả đang lọc">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Xóa kết quả lọc ({{ $submissions->total() }})
                    </button>
                @endif
                <button type="button" onclick="confirmDeleteAll(false)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors cursor-pointer"
                        title="Xóa toàn bộ form liên hệ trong hệ thống">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa tất cả
                </button>
            </div>
            @endif
        </div>

        <form method="GET" class="mt-4 flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email, nội dung..."
                   class="px-4 py-2 border border-gray-300 rounded-lg flex-1 min-w-48">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã xử lý</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
            @if($formNames->count() > 1)
            <select name="form_name" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả form</option>
                @foreach($formNames as $fname)
                    <option value="{{ $fname }}" {{ request('form_name') === $fname ? 'selected' : '' }}>{{ $fname }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg cursor-pointer">Lọc</button>
            @if(request()->anyFilled(['search','status','form_name']))
                <a href="{{ url()->current() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Xóa lọc</a>
            @endif
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="hidden items-center justify-between bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Đã chọn <strong id="selectedCount" class="font-bold">0</strong> form liên hệ</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="submitBulkDelete()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Xóa các mục đã chọn
            </button>
            <button type="button" id="clearSelectionBtn" class="px-2.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-md transition-colors cursor-pointer">
                Bỏ chọn
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-10 px-4 py-3 text-center">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer" title="Chọn tất cả">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thông tin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày gửi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="w-10 px-4 py-4 text-center">
                            <input type="checkbox" name="ids[]" value="{{ $submission->id }}" class="row-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                {{ $submission->form_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm max-w-md">
                            @php
                                $d = (array) ($submission->data ?? []);
                                $fullname = $d['fullname'] ?? $d['name'] ?? null;
                                $phone = $d['phone'] ?? null;
                                $email = $d['email'] ?? null;
                                $service = $d['service'] ?? null;
                                $prodTitle = $d['product_title'] ?? null;
                                $prodSku = $d['product_sku'] ?? null;
                                $prodPrice = $d['product_price'] ?? null;
                                $prodQty = $d['product_quantity'] ?? null;
                                $pageUrl = $d['page_url'] ?? null;
                                $msg = $d['message'] ?? null;
                                $handledKeys = ['fullname', 'name', 'phone', 'email', 'service', 'product_title', 'product_sku', 'product_price', 'product_quantity', 'page_url', 'message', 'product_variation', 'source'];
                            @endphp

                            <!-- Khách hàng -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-gray-900 text-sm">{{ $fullname ?: 'Khách hàng' }}</span>
                                    @if($phone)
                                        <a href="tel:{{ $phone }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-0.5 rounded flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $phone }}
                                        </a>
                                    @endif
                                </div>
                                @if($email)
                                    <div class="text-xs text-gray-500">
                                        <span class="text-gray-400">Email:</span> {{ $email }}
                                    </div>
                                @endif
                            </div>

                            <!-- Sản phẩm báo giá (nếu có) -->
                            @if($prodTitle)
                                <div class="mt-2 p-2 bg-amber-50/80 border border-amber-200/90 rounded-lg text-xs space-y-1">
                                    <div class="font-semibold text-amber-950 flex items-start gap-1">
                                        <span class="shrink-0">x</span>
                                        <span class="line-clamp-2" title="{{ $prodTitle }}">{{ $prodTitle }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-amber-900 flex-wrap">
                                        @if($prodSku)
                                            <span class="bg-white px-1.5 py-0.5 rounded border border-amber-300 font-mono">Mã: {{ $prodSku }}</span>
                                        @endif
                                        @if($prodPrice)
                                            <span>Giá: <strong>{{ $prodPrice }}</strong></span>
                                        @endif
                                        @if($prodQty)
                                            <span>SL: <strong>{{ $prodQty }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Dịch vụ & Link -->
                            <div class="mt-2 flex items-center gap-2 flex-wrap text-xs">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-md text-[11px] font-medium">
                                    <strong class="font-bold text-indigo-900">Dịch vụ:</strong>
                                    <span>{{ $submission->getFormattedFieldValue('service', $service ?: 'bao-gia-san-pham') }}</span>
                                </span>
                                @if($pageUrl && filter_var($pageUrl, FILTER_VALIDATE_URL))
                                    <a href="{{ $pageUrl }}" target="_blank" class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-0.5" title="{{ $pageUrl }}">
                                        <span>Xem trang web</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>

                            <!-- Lời nhắn nếu có (chỉ hiện khi không trùng product info) -->
                            @if($msg && !$prodTitle)
                                <div class="mt-1 text-xs text-gray-600 italic line-clamp-2" title="{{ $msg }}">
                                    "{{ Str::limit($msg, 90) }}"
                                </div>
                            @endif

                            <!-- Các trường bổ sung khác -->
                            @foreach($d as $extraKey => $extraVal)
                                @if(!in_array($extraKey, $handledKeys) && !empty($extraVal))
                                    <div class="text-[11px] text-gray-500 mt-1">
                                        <span class="font-medium text-gray-700">{{ $submission->getFieldLabel($extraKey) }}:</span>
                                        {{ is_array($extraVal) ? json_encode($extraVal, JSON_UNESCAPED_UNICODE) : $submission->getFormattedFieldValue($extraKey, $extraVal) }}
                                    </div>
                                @endif
                            @endforeach

                            @if($submission->admin_note)
                                <div class="mt-1.5 p-1.5 bg-purple-50 border border-purple-200 rounded text-xs text-purple-700">
                                    <strong class="font-semibold">Ghi chú admin:</strong> {{ $submission->admin_note }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $sc = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                                $sl = ['pending' => 'Chờ xử lý', 'approved' => 'Đã xử lý', 'rejected' => 'Từ chối'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $sc[$submission->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $sl[$submission->status] ?? $submission->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $submission->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST"
                                      action="{{ route('admin.form-submissions.update-status', $submission->id) }}"
                                      class="inline flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs border rounded px-2 py-1">
                                        <option value="pending"  {{ $submission->status === 'pending'  ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="approved" {{ $submission->status === 'approved' ? 'selected' : '' }}>Đã xử lý</option>
                                        <option value="rejected" {{ $submission->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                    </select>
                                </form>
                                <form method="POST"
                                      action="{{ route('admin.form-submissions.destroy', $submission->id) }}"
                                      class="inline"
                                      onsubmit="return confirm('Xóa submission này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900 text-xs cursor-pointer">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p>Chưa có submission nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="p-6 border-t">{{ $submissions->links() }}</div>
        @endif
    </div>
</div>

{{-- Hidden Form for Delete All --}}
<form id="deleteAllForm" method="POST" action="{{ route('admin.form-submissions.destroy-all') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="filtered" id="deleteAllFiltered" value="0">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="hidden" name="form_name" value="{{ request('form_name') }}">
</form>

{{-- Hidden Form for Bulk Delete --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.form-submissions.bulk-delete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds" value="">
</form>

@endsection

@push('scripts')
<script>
function confirmDeleteAll(filtered) {
    var message = filtered
        ? '⚠️ BẠN CÓ CHẮC CHẮN MUỐN XÓA TẤT CẢ form liên hệ theo kết quả lọc hiện tại không?\n\nThao tác này sẽ xóa vĩnh viễn dữ liệu và không thể hoàn tác!'
        : '⚠️ CẢNH BÁO NGUY HIỂM:\n\nBạn có chắc chắn muốn XÓA TOÀN BỘ TẤT CẢ form liên hệ trong hệ thống không?\n\nToàn bộ dữ liệu gửi form sẽ bị xóa vĩnh viễn và không thể khôi phục!';
    
    if (confirm(message)) {
        document.getElementById('deleteAllFiltered').value = filtered ? '1' : '0';
        document.getElementById('deleteAllForm').submit();
    }
}

function submitBulkDelete() {
    var checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) {
        alert('Vui lòng chọn ít nhất một form liên hệ để xóa.');
        return;
    }
    if (confirm('Bạn có chắc chắn muốn xóa ' + checked.length + ' form liên hệ đã chọn không?\n\nThao tác này không thể hoàn tác!')) {
        var ids = Array.from(checked).map(function(cb) { return cb.value; });
        document.getElementById('bulkDeleteIds').value = ids.join(',');
        document.getElementById('bulkDeleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.row-checkbox');
    var bulkActionBar = document.getElementById('bulkActionBar');
    var selectedCount = document.getElementById('selectedCount');
    var clearSelectionBtn = document.getElementById('clearSelectionBtn');

    function updateBulkBar() {
        var checked = document.querySelectorAll('.row-checkbox:checked');
        if (selectedCount) selectedCount.textContent = checked.length;
        if (bulkActionBar) {
            if (checked.length > 0) {
                bulkActionBar.classList.remove('hidden');
                bulkActionBar.classList.add('flex');
            } else {
                bulkActionBar.classList.add('hidden');
                bulkActionBar.classList.remove('flex');
            }
        }
        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            checkboxes.forEach(function(cb) { cb.checked = false; });
            if (selectAll) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
            updateBulkBar();
        });
    }
});
</script>
@endpush

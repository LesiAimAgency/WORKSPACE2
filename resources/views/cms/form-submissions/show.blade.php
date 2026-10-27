@extends('cms.layouts.app')

@section('title', 'Chi tiết Form liên hệ')
@section('page-title', 'Chi tiết Form liên hệ #' . $submission->id)

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.form-submissions.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại danh sách
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
        <div class="border-b pb-4 mb-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Form: {{ $submission->form_name ?? 'Liên hệ' }}</h2>
                <p class="text-xs text-gray-500 mt-1">Gửi lúc: {{ $submission->created_at ? $submission->created_at->format('d/m/Y H:i:s') : 'N/A' }} | IP: {{ $submission->ip_address ?? 'N/A' }}</p>
            </div>
            <div>
                @php
                    $sc = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                    $sl = ['pending' => 'Chờ xử lý', 'approved' => 'Đã xử lý', 'rejected' => 'Từ chối'];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$submission->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $sl[$submission->status] ?? $submission->status }}
                </span>
            </div>
        </div>

        @php
            $d = is_array($submission->data) ? $submission->data : [];
            $fullname = $d['fullname'] ?? $d['name'] ?? null;
            $phone = $d['phone'] ?? null;
            $email = $d['email'] ?? null;
            $company = $d['company'] ?? $d['company_name'] ?? null;
            $address = $d['address'] ?? null;
            $service = $d['service'] ?? null;
            $prodTitle = $d['product_title'] ?? null;
            $prodSku = $d['product_sku'] ?? null;
            $prodPrice = $d['product_price'] ?? null;
            $prodQty = $d['product_quantity'] ?? null;
            $prodVar = $d['product_variation'] ?? null;
            $pageUrl = $d['page_url'] ?? null;
            $msg = $d['message'] ?? null;
            $handledKeys = ['fullname', 'name', 'phone', 'email', 'company', 'company_name', 'address', 'service', 'product_title', 'product_sku', 'product_price', 'product_quantity', 'product_variation', 'page_url', 'message', 'source', 'privacy_consent', 'newsletter'];
        @endphp

        <div class="space-y-6">
            <!-- 1. Thông tin khách hàng -->
            <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Thông tin khách hàng
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs text-gray-500">Họ và tên:</span>
                        <span class="font-bold text-gray-900 text-base">{{ $fullname ?: '(Không có)' }}</span>
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs text-gray-500">Số điện thoại:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">{{ $phone ?: '(Không có)' }}</span>
                            @if($phone)
                                <a href="tel:{{ $phone }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-0.5 rounded font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    Gọi ngay
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs text-gray-500">Địa chỉ Email:</span>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-900">{{ $email ?: '(Không có)' }}</span>
                            @if($email)
                                <a href="mailto:{{ $email }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-0.5 rounded font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Gửi email
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($company)
                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs text-gray-500">Tên doanh nghiệp / Đơn vị:</span>
                        <span class="text-gray-900 font-medium">{{ $company }}</span>
                    </div>
                    @endif

                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs text-gray-500">Dịch vụ yêu cầu:</span>
                        <div>
                            <span class="inline-block px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-lg font-semibold text-xs">
                                {{ $submission->getFormattedFieldValue('service', $service ?: 'bao-gia-san-pham') }}
                            </span>
                        </div>
                    </div>

                    @if($address)
                    <div class="flex flex-col gap-0.5 md:col-span-2">
                        <span class="text-xs text-gray-500">Địa chỉ:</span>
                        <span class="text-gray-900">{{ $address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 2. Thông tin sản phẩm yêu cầu báo giá (nếu có) -->
            @if($prodTitle || $prodSku)
            <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-5">
                <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Sản phẩm yêu cầu báo giá
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-amber-800/80 block">Tên sản phẩm:</span>
                        <span class="font-bold text-gray-900 text-base leading-snug">{{ $prodTitle }}</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-1">
                        @if($prodSku)
                        <div>
                            <span class="text-xs text-amber-800/80 block">Mã sản phẩm (SKU):</span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 bg-white border border-amber-300 font-mono font-bold text-amber-900 rounded text-xs">
                                {{ $prodSku }}
                            </span>
                        </div>
                        @endif

                        @if($prodPrice)
                        <div>
                            <span class="text-xs text-amber-800/80 block">Đơn giá tham khảo:</span>
                            <span class="font-bold text-red-600 text-sm mt-0.5 inline-block">{{ $prodPrice }}</span>
                        </div>
                        @endif

                        @if($prodQty)
                        <div>
                            <span class="text-xs text-amber-800/80 block">Số lượng yêu cầu:</span>
                            <span class="font-bold text-gray-900 text-sm mt-0.5 inline-block">{{ $prodQty }}</span>
                        </div>
                        @endif

                        @if($prodVar)
                        <div class="sm:col-span-3">
                            <span class="text-xs text-amber-800/80 block">Phân loại / Biến thể:</span>
                            <span class="text-gray-900 font-medium">{{ $prodVar }}</span>
                        </div>
                        @endif
                    </div>

                    @if($pageUrl && filter_var($pageUrl, FILTER_VALIDATE_URL))
                    <div class="pt-2 border-t border-amber-200/70 flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs text-amber-800/90 font-medium">Link trang sản phẩm trên website:</span>
                        <a href="{{ $pageUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-amber-100/60 border border-amber-300 rounded-lg text-xs font-semibold text-blue-700 transition-colors shadow-2xs">
                            <span>Mở xem trang sản phẩm</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- 3. Nội dung yêu cầu & Chi tiết khác -->
            <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-5 space-y-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Nội dung yêu cầu & Ghi chú
                </h3>

                @if($service)
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500 min-w-32">Dịch vụ quan tâm:</span>
                    <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-lg font-semibold text-xs">
                        {{ $submission->getFormattedFieldValue('service', $service) }}
                    </span>
                </div>
                @endif

                @if($msg)
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Nội dung / Ghi chú từ khách:</span>
                    <div class="bg-white border border-gray-200 rounded-lg p-3 text-sm text-gray-800 whitespace-pre-line leading-relaxed">
                        {{ $msg }}
                    </div>
                </div>
                @endif

                @if($pageUrl && !$prodTitle && filter_var($pageUrl, FILTER_VALIDATE_URL))
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500 min-w-32">Trang gửi:</span>
                    <a href="{{ $pageUrl }}" target="_blank" class="text-blue-600 hover:underline text-xs inline-flex items-center gap-1 font-mono">
                        <span>{{ $pageUrl }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                @endif

                <!-- Các trường bổ sung khác -->
                @foreach($d as $k => $v)
                    @if(!in_array($k, $handledKeys) && !empty($v))
                    <div class="flex items-start gap-4 text-sm border-t border-gray-200/60 pt-2">
                        <span class="text-gray-500 min-w-32 font-medium">{{ $submission->getFieldLabel($k) }}:</span>
                        <span class="text-gray-900 flex-1">
                            @if(is_string($v) && filter_var($v, FILTER_VALIDATE_URL))
                                <a href="{{ $v }}" target="_blank" class="text-blue-600 hover:underline text-xs font-mono inline-flex items-center gap-1">
                                    <span>{{ $v }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @else
                                {{ is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $submission->getFormattedFieldValue($k, $v) }}
                            @endif
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

            <div class="mt-6 border-t pt-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Cập nhật trạng thái & Ghi chú</h3>
                <form method="POST" action="{{ route('admin.form-submissions.update-status', $submission->id) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái xử lý</label>
                        <select name="status" class="w-full max-w-xs border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="pending" {{ $submission->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="approved" {{ $submission->status === 'approved' ? 'selected' : '' }}>Đã xử lý</option>
                            <option value="rejected" {{ $submission->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú nội bộ</label>
                        <textarea name="admin_note" rows="3" class="w-full border border-gray-300 rounded-lg p-3 text-sm" placeholder="Nhập ghi chú xử lý...">{{ old('admin_note', $submission->admin_note) }}</textarea>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        Lưu cập nhật
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

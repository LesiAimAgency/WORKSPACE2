@extends('cms.layouts.app')

@section('title', 'Tạo đơn hàng mới')
@section('page-title', 'Tạo đơn hàng mới')

@section('content')
<div class="mb-6">
    <a href="{{ request()->route('projectCode') ? route('project.admin.orders.index', request()->route('projectCode')) : route('cms.orders.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Quay lại Danh sách Đơn hàng
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <form action="{{ request()->route('projectCode') ? route('project.admin.orders.store', request()->route('projectCode')) : route('cms.orders.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Thông tin khách hàng -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold border-b pb-2">Thông tin khách hàng</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên khách hàng <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="customer_email" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                
                <!-- Địa chỉ & Thanh toán -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold border-b pb-2">Giao hàng & Thanh toán</h3>
                    
                    @include('cms.components.address-selector', [
                        'label' => 'Giao hàng',
                        'prefix' => 'shipping_address'
                    ])

                    <div class="mt-6 mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="same_as_shipping" class="rounded text-blue-600">
                            <span class="ml-2 text-sm text-gray-600">Thanh toán giống địa chỉ giao hàng</span>
                        </label>
                    </div>
                    <div id="billing_address_section">
                        @include('cms.components.address-selector', [
                            'label' => 'Thanh toán',
                            'prefix' => 'billing_address'
                        ])
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                            <option value="momo">Ví MoMo</option>
                            <option value="vnpay">VNPay</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4 mb-6">
                <h3 class="text-lg font-semibold border-b pb-2">Thông tin thêm</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú của khách hàng</label>
                    <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng tiền tạm tính (VNĐ)</label>
                    <input type="number" name="total_amount" value="0" min="0" class="w-full md:w-1/3 border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Bạn có thể chỉnh sửa chi tiết sản phẩm sau khi tạo đơn hàng.</p>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ request()->route('projectCode') ? route('project.admin.orders.index', request()->route('projectCode')) : route('cms.orders.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tạo Đơn Hàng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Same as shipping checkbox
    $('#same_as_shipping').change(function() {
        if($(this).is(':checked')) {
            $('#billing_address_section').hide();
        } else {
            $('#billing_address_section').show();
        }
    });
});
</script>
@endpush

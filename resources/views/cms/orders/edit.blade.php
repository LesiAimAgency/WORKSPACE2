{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng')
@section('page-title', 'Chỉnh sửa đơn hàng ' . $order->order_number)

@section('content')

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <strong>Lỗi:</strong>
        <ul class="list-disc pl-5 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.update', [$currentProject->code, $order]) : route('cms.orders.update', $order) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mã đơn hàng</label>
                <input type="text" name="order_number" value="{{ old('order_number', $order->order_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái đơn hàng</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="shipped" {{ old('status', $order->status) === 'shipped' ? 'selected' : '' }}>Đã gửi</option>
                    <option value="delivered" {{ old('status', $order->status) === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                    <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
        </div>

        <div class="mb-6 border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin khách hàng</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên khách hàng</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="mb-6 border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Địa chỉ giao hàng & thanh toán</h3>
            <div class="grid grid-cols-2 gap-6">
                <!-- Shipping -->
                @include('cms.components.address-selector', [
                    'label' => 'Giao hàng',
                    'prefix' => 'shipping_address',
                    'address' => $order->shipping_address ?? []
                ])

                <!-- Billing -->
                @include('cms.components.address-selector', [
                    'label' => 'Thanh toán',
                    'prefix' => 'billing_address',
                    'address' => $order->billing_address ?? []
                ])
            </div>
        </div>

        <div class="mb-6 border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Chi tiết tài chính</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tạm tính</label>
                    <input type="number" name="subtotal" value="{{ old('subtotal', $order->subtotal) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thuế</label>
                    <input type="number" name="tax_amount" value="{{ old('tax_amount', $order->tax_amount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phí vận chuyển</label>
                    <input type="number" name="shipping_amount" value="{{ old('shipping_amount', $order->shipping_amount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giảm giá</label>
                    <input type="number" name="discount_amount" value="{{ old('discount_amount', $order->discount_amount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tổng tiền</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-bold">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phương thức thanh toán</label>
                <input type="text" name="payment_method" value="{{ old('payment_method', $order->payment_method) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái thanh toán</label>
                <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ old('payment_status', $order->payment_status) === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="paid" {{ old('payment_status', $order->payment_status) === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ old('payment_status', $order->payment_status) === 'failed' ? 'selected' : '' }}>Thất bại</option>
                    <option value="refunded" {{ old('payment_status', $order->payment_status) === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                </select>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú của khách hàng</label>
                <textarea name="customer_notes" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('customer_notes', $order->customer_notes) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú nội bộ</label>
                <textarea name="internal_notes" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('internal_notes', $order->internal_notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.show', [$currentProject->code, $order]) : route('cms.orders.show', $order) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật toàn bộ đơn hàng
            </button>
        </div>
    </form>
</div>
@endsection

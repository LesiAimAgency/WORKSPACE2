{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng ' . $order->order_number)

@section('content')
<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Trạng thái</h3>
        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Thanh toán</h3>
        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
            {{ ucfirst($order->payment_status) }}
        </span>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Tổng cộng</h3>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($order->total_amount) }}₫</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-6 mb-6">
    <!-- Thông tin khách hàng -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin khách hàng</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">Tên</p>
                <p class="font-medium">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-medium">{{ $order->customer_email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Điện thoại</p>
                <p class="font-medium">{{ $order->customer_phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Địa chỉ giao hàng -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Địa chỉ giao hàng</h2>
        <div class="space-y-2 text-sm">
            <p>{{ $order->shipping_address['address'] ?? '' }}</p>
            <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }}</p>
            <p>{{ $order->shipping_address['postal_code'] ?? '' }} - {{ $order->shipping_address['country'] ?? '' }}</p>
        </div>
    </div>
</div>

<!-- Chi tiết sản phẩm -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Chi tiết sản phẩm</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Giá</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tổng</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($order->items as $item)
            <tr>
                <td class="px-6 py-4">
                    <p class="font-medium">{{ $item->product_name }}</p>
                    @if($item->product_attributes)
                        <p class="text-sm text-gray-600">{{ implode(', ', $item->product_attributes) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">{{ $item->product_sku }}</td>
                <td class="px-6 py-4 text-right font-medium">{{ number_format($item->unit_price) }}₫</td>
                <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                <td class="px-6 py-4 text-right font-medium">{{ number_format($item->total_price) }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Tóm tắt tiền -->
<div class="bg-white rounded-lg shadow p-6 max-w-xs ml-auto mb-6">
    <div class="space-y-3">
        <div class="flex justify-between">
            <span>Tạm tính:</span>
            <span>{{ number_format($order->subtotal) }}₫</span>
        </div>
        @if($order->tax_amount > 0)
        <div class="flex justify-between">
            <span>Thuế:</span>
            <span>{{ number_format($order->tax_amount) }}₫</span>
        </div>
        @endif
        @if($order->shipping_amount > 0)
        <div class="flex justify-between">
            <span>Vận chuyển:</span>
            <span>{{ number_format($order->shipping_amount) }}₫</span>
        </div>
        @endif
        @if($order->discount_amount > 0)
        <div class="flex justify-between text-red-600">
            <span>Giảm giá:</span>
            <span>-{{ number_format($order->discount_amount) }}₫</span>
        </div>
        @endif
        <div class="border-t pt-3 flex justify-between font-bold text-lg">
            <span>Tổng cộng:</span>
            <span>{{ number_format($order->total_amount) }}₫</span>
        </div>
    </div>
</div>

<div class="flex gap-4">
    <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.index', $currentProject->code) : route('cms.orders.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
        Quay lại
    </a>
    <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.print', [$currentProject->code, $order]) : route('cms.orders.print', $order) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        In hóa đơn
    </a>
    <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.edit', [$currentProject->code, $order]) : route('cms.orders.edit', $order) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Chỉnh sửa
    </a>
</div>
@endsection

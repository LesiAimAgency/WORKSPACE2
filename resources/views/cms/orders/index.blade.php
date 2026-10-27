{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Đơn hàng')

@section('content')

@if(session('debug_info'))
    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
        <strong>Debug Info:</strong> {{ session('debug_info') }}
    </div>
@endif

<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Tìm kiếm đơn hàng..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã gửi</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>

        <select name="payment_status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả thanh toán</option>
            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
        </select>
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Lọc
        </button>
    </form>

    <div class="flex space-x-2">
        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.create', $currentProject->code) : route('cms.orders.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            + Tạo đơn hàng
        </a>
        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.reports', $currentProject->code) : route('cms.orders.reports') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Báo cáo
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số đơn</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ number_format($order->total_amount) }}₫
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $order->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.show', [$currentProject->code, $order]) : route('cms.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.edit', [$currentProject->code, $order]) : route('cms.orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.print', [$currentProject->code, $order]) : route('cms.orders.print', $order) }}" target="_blank" class="text-gray-600 hover:text-gray-900">In</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Không có đơn hàng nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection

{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Báo cáo đơn hàng')
@section('page-title', 'Báo cáo đơn hàng')

@section('content')
<div class="mb-6 flex gap-4">
    <form method="GET" class="flex gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="px-4 py-2 border border-gray-300 rounded-lg">
        <input type="date" name="date_to" value="{{ $dateTo }}" class="px-4 py-2 border border-gray-300 rounded-lg">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lọc</button>
    </form>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Tổng doanh thu</h3>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($total_sales) }}₫</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Tổng đơn hàng</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $total_orders }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Trung bình</h3>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($total_orders > 0 ? $total_sales / $total_orders : 0) }}₫</p>
    </div>
</div>

<!-- Đơn hàng theo trạng thái -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Đơn hàng theo trạng thái</h2>
    <div class="grid grid-cols-3 gap-4">
        @foreach($orders_by_status as $status => $count)
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
            <p class="text-sm text-blue-600 font-medium">{{ ucfirst($status) }}</p>
            <p class="text-2xl font-bold text-blue-900">{{ $count }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Top sản phẩm bán chạy -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Top 10 sản phẩm bán chạy</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số lượng bán</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Doanh thu</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số đơn</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($top_products as $product)
            <tr>
                <td class="px-6 py-4 text-sm font-medium">{{ $product->product_name }}</td>
                <td class="px-6 py-4 text-right">{{ $product->total_quantity }}</td>
                <td class="px-6 py-4 text-right font-medium">{{ number_format($product->total_revenue) }}₫</td>
                <td class="px-6 py-4 text-right">{{ $product->order_count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@extends('cms.layouts.app')

@section('title', 'Dashboard - ' . ($currentProject->name ?? 'Project'))
@section('page-title', $currentProject->name ?? 'Project Dashboard')

@section('content')
<div class="mb-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-medium text-blue-900">Dự án: {{ $currentProject->name ?? 'N/A' }}</p>
                <p class="text-sm text-blue-700">Mã: {{ $currentProject->code ?? 'N/A' }} | Subdomain: {{ $currentProject->subdomain ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Đơn hàng hôm nay</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $today_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($today_revenue) }}đ</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Tổng sản phẩm</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $total_products }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Đơn chờ xử lý</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pending_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Thông tin dự án</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Khách hàng</p>
            <p class="font-medium">{{ $currentProject->client_name ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Trạng thái</p>
            <p class="font-medium">{{ $currentProject->status ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Ngày bắt đầu</p>
            <p class="font-medium">{{ optional($currentProject->start_date)->format('d/m/Y') ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Deadline</p>
            <p class="font-medium">{{ optional($currentProject->deadline)->format('d/m/Y') ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection

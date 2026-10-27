@extends('cms.layouts.app')

@section('title', 'Trung tâm Điều hành & Doanh số' . ($currentProject ? ' - ' . $currentProject->name : ''))
@section('page-title', 'Bảng Điều Khiển Kinh Doanh')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .hub-card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    .pipeline-badge {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush

@section('content')
@php
    $projectCode = $currentProject?->code ?? request()->route('projectCode');
    $orderIndexUrl = $projectCode ? route('project.admin.orders.index', $projectCode) : '#';
    $st = $stats ?? [];
    $api = $st['api_integrations'] ?? ['ai' => false, 'vietqr' => false, 'ghn' => false, 'telegram' => false];
@endphp


<!-- 5-Stage Order Pipeline Hub (From public_html) -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-route text-blue-600 text-sm"></i>
            Tiến độ Vận hành Đơn hàng (Order Pipeline)
        </h3>
        <span class="text-xs text-gray-400">Thời gian thực: {{ now()->format('H:i - d/m/Y') }}</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <!-- Stage 1: Chờ duyệt (Pending) -->
        <div class="hub-card bg-white p-4 rounded-xl border-l-4 border-amber-500 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-clock-rotate-left text-base"></i>
                </div>
                <div>
                    <p class="pipeline-badge text-gray-400">Chờ xác nhận</p>
                    <p class="text-2xl font-black text-amber-600 leading-tight">{{ $st['pending_orders'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ $orderIndexUrl }}?status=pending" class="mt-3 text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center justify-between">
                <span>Duyệt đơn</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Stage 2: Chưa phân công / Cần xử lý gấp -->
        <div class="hub-card bg-white p-4 rounded-xl border-l-4 border-red-500 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div>
                    <p class="pipeline-badge text-gray-400">Cần phân bổ</p>
                    <p class="text-2xl font-black text-red-600 leading-tight">{{ $st['unassigned_orders'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ $orderIndexUrl }}?status=pending" class="mt-3 text-xs font-bold text-red-600 hover:text-red-700 flex items-center justify-between">
                <span>Phân công ngay</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Stage 3: Đang đóng gói / Xử lý -->
        <div class="hub-card bg-white p-4 rounded-xl border-l-4 border-blue-500 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-box-open text-base"></i>
                </div>
                <div>
                    <p class="pipeline-badge text-gray-400">Đang xử lý</p>
                    <p class="text-2xl font-black text-blue-600 leading-tight">{{ $st['processing_orders'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ $orderIndexUrl }}?status=processing" class="mt-3 text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center justify-between">
                <span>Kiểm hàng kho</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Stage 4: Đang giao hàng -->
        <div class="hub-card bg-white p-4 rounded-xl border-l-4 border-purple-500 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-truck-fast text-base"></i>
                </div>
                <div>
                    <p class="pipeline-badge text-gray-400">Đang giao</p>
                    <p class="text-2xl font-black text-purple-600 leading-tight">{{ $st['shipping_orders'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ $orderIndexUrl }}?status=shipping" class="mt-3 text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center justify-between">
                <span>Vận đơn</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Stage 5: Hoàn tất hôm nay -->
        <div class="hub-card bg-white p-4 rounded-xl border-l-4 border-emerald-500 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-check-double text-base"></i>
                </div>
                <div>
                    <p class="pipeline-badge text-gray-400">Xong hôm nay</p>
                    <p class="text-2xl font-black text-emerald-600 leading-tight">{{ $st['completed_today'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ $orderIndexUrl }}?status=completed" class="mt-3 text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center justify-between">
                <span>Xem đối soát</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>

<!-- Primary Financial & Operations KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
    <!-- Doanh thu hôm nay -->
    <div class="hub-card bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-15">
            <i class="fa-solid fa-money-bill-wave text-7xl"></i>
        </div>
        <p class="text-xs font-bold text-blue-200 uppercase tracking-wider">Doanh thu hôm nay</p>
        <p class="text-2xl font-black mt-2 leading-none">{{ number_format($today_revenue) }}₫</p>
        <div class="mt-3 flex items-center gap-2 text-xs text-blue-100">
            <span class="px-2 py-0.5 rounded-full bg-white/20 font-bold">{{ $today_orders }} đơn</span>
            <span>phát sinh trong ngày</span>
        </div>
    </div>

    <!-- Doanh thu tháng này -->
    <div class="hub-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between text-xs text-gray-400 uppercase font-bold tracking-wider">
                <span>Tháng {{ now()->format('m/Y') }}</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 mt-2 leading-none">
                {{ number_format($st['monthly_revenue'] ?? $today_revenue) }}₫
            </p>
        </div>
        <p class="text-xs text-emerald-600 font-semibold mt-3 flex items-center gap-1">
            <i class="fa-solid fa-chart-line"></i> Toàn kỳ: {{ number_format($total_revenue) }}₫
        </p>
    </div>

    <!-- Tổng số Đơn hàng -->
    <div class="hub-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between text-xs text-gray-400 uppercase font-bold tracking-wider">
                <span>Tổng đơn hàng</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 mt-2 leading-none">
                {{ number_format($st['total_orders'] ?? 0) }}
            </p>
        </div>
        <p class="text-xs text-amber-600 font-semibold mt-3">
            {{ $pending_orders }} đơn đang chờ xử lý
        </p>
    </div>

    <!-- Khách hàng & Thành viên -->
    <div class="hub-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between text-xs text-gray-400 uppercase font-bold tracking-wider">
                <span>Khách hàng / CRM</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 mt-2 leading-none">
                {{ number_format($total_users) }}
            </p>
        </div>
        <p class="text-xs text-gray-400 font-medium mt-3">
            +{{ $new_users_today }} khách hàng mới hôm nay
        </p>
    </div>

    <!-- Sản phẩm & Cảnh báo tồn kho -->
    <div class="hub-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between text-xs text-gray-400 uppercase font-bold tracking-wider">
                <span>Sản phẩm & Kho</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-gray-900 mt-2 leading-none">
                {{ number_format($total_products) }}
            </p>
        </div>
        @if($out_of_stock_products > 0)
            <p class="text-xs font-bold text-red-600 mt-3 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $out_of_stock_products }} mặt hàng sắp hết/hết
            </p>
        @else
            <p class="text-xs text-emerald-600 font-semibold mt-3 flex items-center gap-1">
                <i class="fa-solid fa-check"></i> Kho hàng dồi dào
            </p>
        @endif
    </div>
</div>


<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart (7 Days) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    Biểu đồ Doanh thu (7 ngày gần nhất)
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Xu hướng doanh thu bán hàng thực nhận</p>
            </div>
            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">VNĐ</span>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Orders Volume Chart (7 Days) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Khối lượng Đơn hàng (7 ngày gần nhất)
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Số lượng đơn được tạo thành công</p>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Đơn</span>
        </div>
        <div class="relative h-64">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>

<!-- Core Business Tables Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
    <!-- Recent Orders (8 cols) -->
    <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-blue-600"></i>
                        Đơn hàng Mới Nhất
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Cần chú ý xử lý để kịp tiến độ giao vận</p>
                </div>
                <a href="{{ $orderIndexUrl }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                    Xem tất cả đơn <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3 px-4">Mã đơn</th>
                            <th class="py-3 px-4">Khách hàng</th>
                            <th class="py-3 px-4 text-right">Tổng tiền</th>
                            <th class="py-3 px-4 text-center">Trạng thái</th>
                            <th class="py-3 px-4">Thời gian</th>
                            <th class="py-3 px-4 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($recent_orders as $ro)
                        @php
                            $statusMap = [
                                'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Chờ duyệt'],
                                'processing' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Đang xử lý'],
                                'shipping' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => 'Đang giao'],
                                'shipped' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => 'Đang giao'],
                                'completed' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Hoàn thành'],
                                'delivered' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Đã giao'],
                                'cancelled' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Đã hủy'],
                                'refunded' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Hoàn tiền'],
                            ];
                            $sb = $statusMap[$ro->status] ?? ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $ro->status];
                            $orderEditUrl = $projectCode ? route('project.admin.orders.edit', [$projectCode, $ro->id]) : '#';
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                #{{ $ro->order_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-gray-800">{{ $ro->customer_name ?: 'Khách vãng lai' }}</p>
                                @if($ro->customer_phone)
                                    <p class="text-[11px] text-gray-400">{{ $ro->customer_phone }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-gray-900">
                                {{ number_format($ro->total_amount ?? $ro->total) }}₫
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $sb['bg'] }}">
                                    {{ $sb['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-400 text-[11px]">
                                {{ $ro->created_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ $orderEditUrl }}" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 font-bold text-gray-600 transition inline-flex items-center gap-1">
                                    <span>Chi tiết</span>
                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 italic">
                                Chưa có đơn hàng nào phát sinh trong hệ thống.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Selling & Inventory Alert (4 cols) -->
    <div class="lg:col-span-4 flex flex-col gap-6">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-fire text-amber-500"></i>
                    Bán Chạy Nhất
                </h3>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md uppercase">TOP 5</span>
            </div>
            <div class="space-y-3">
                @forelse($top_products as $idx => $p)
                <div class="flex items-center justify-between p-2.5 rounded-xl border border-gray-100 hover:border-blue-200 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-black text-xs flex items-center justify-center">
                            {{ $idx + 1 }}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-gray-800 line-clamp-1">{{ $p->product_name ?? $p->name }}</p>
                            <p class="text-[10px] text-gray-400 uppercase font-mono">{{ $p->product_sku ?? $p->sku ?? 'NO-SKU' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700">
                            {{ $p->total_sold ?? $p->total_qty ?? 0 }} bán
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center py-6 text-xs text-gray-400 italic">Chưa có dữ liệu bán hàng</p>
                @endforelse
            </div>
        </div>

        <!-- Low Stock / Inventory Alert -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-boxes-packing text-red-500"></i>
                    Cảnh Báo Tồn Kho
                </h3>
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md uppercase">&le; 5 sp</span>
            </div>
            <div class="space-y-2.5">
                @forelse($st['low_selling'] ?? [] as $lp)
                <div class="flex items-center justify-between p-2.5 rounded-xl border border-red-50 bg-red-50/30">
                    <div>
                        <p class="text-xs font-bold text-gray-800 line-clamp-1">{{ $lp->name }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $lp->sku ?? 'NO-SKU' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-red-100 text-red-700">
                        Còn: {{ $lp->stock_quantity ?? 0 }}
                    </span>
                </div>
                @empty
                <p class="text-center py-6 text-xs text-emerald-600 font-medium">Tất cả sản phẩm đều đủ tồn kho an toàn!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- VIP Top Buyers (From public_html CRM) -->
@if(!empty($st['top_buyers']) && count($st['top_buyers']) > 0)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-crown text-amber-500"></i>
                Khách Hàng Thân Thiết (Top Buyers CRM)
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Dành cho bộ phận Sale / Chăm sóc khách hàng VIP</p>
        </div>
        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">Chăm sóc đặc biệt</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Khách hàng</th>
                    <th class="py-3 px-4">Liên hệ</th>
                    <th class="py-3 px-4 text-center">Số đơn</th>
                    <th class="py-3 px-4 text-right">Tổng chi tiêu</th>
                    <th class="py-3 px-4">Đơn gần nhất</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @foreach($st['top_buyers'] as $i => $buyer)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 font-bold text-gray-400">{{ $i + 1 }}</td>
                    <td class="py-3 px-4 font-bold text-gray-900">{{ $buyer->customer_name ?: 'Khách hàng' }}</td>
                    <td class="py-3 px-4">
                        @if($buyer->customer_phone)
                            <a href="tel:{{ $buyer->customer_phone }}" class="text-emerald-600 font-bold hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-phone text-[10px]"></i> {{ $buyer->customer_phone }}
                            </a>
                        @endif
                        @if($buyer->customer_email)
                            <p class="text-gray-400 text-[11px]">{{ $buyer->customer_email }}</p>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700">
                            {{ $buyer->order_count }} đơn
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right font-black text-gray-900">
                        {{ number_format($buyer->total_spent) }}₫
                    </td>
                    <td class="py-3 px-4 text-gray-400 text-[11px]">
                        {{ \Carbon\Carbon::parse($buyer->last_order_at)->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Revenue Chart
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const revenueCtx = revenueCanvas.getContext('2d');
        const revGradient = revenueCtx.createLinearGradient(0, 0, 0, 240);
        revGradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
        revGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenue_chart->pluck('date')) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($revenue_chart->pluck('revenue')) !!},
                    borderColor: '#2563EB',
                    backgroundColor: revGradient,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#2563EB',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return new Intl.NumberFormat('vi-VN').format(ctx.raw) + ' ₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            font: { size: 10 },
                            callback: function (val) {
                                return (val >= 1000000) ? (val / 1000000) + 'M' : ((val >= 1000) ? (val / 1000) + 'K' : val);
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 2. Orders Volume Chart
    const ordersCanvas = document.getElementById('ordersChart');
    if (ordersCanvas) {
        const ordersCtx = ordersCanvas.getContext('2d');
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($orders_chart->pluck('date')) !!},
                datasets: [{
                    label: 'Số đơn',
                    data: {!! json_encode($orders_chart->pluck('orders')) !!},
                    backgroundColor: '#10B981',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { precision: 0, font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

});
</script>
@endpush
@endsection

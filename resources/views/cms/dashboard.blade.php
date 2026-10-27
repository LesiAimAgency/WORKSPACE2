@extends('cms.layouts.app')

@section('title', 'Trung tâm Điều hành & Quản lý Truy cập - ' . ($currentProject->name ?? 'INBETWEEN V2'))
@section('page-title', 'Bảng Điều Khiển')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
    .stat-card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }
    .traffic-bar {
        transition: height 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
@php
    $vs = $visitor_stats ?? [];
    $fs = $form_stats ?? [];
    $recentSubs = $recent_submissions ?? collect();
    $recentVis = $recent_visitors ?? collect();
    $deviceData = $device_chart ?? collect();
    $trafficData = $traffic_chart ?? collect();
    $dailyVisits = $vs['daily_visits'] ?? [];
    $liveUrl = url('/');
@endphp

<div class="space-y-6 max-w-7xl mx-auto pb-12" x-data="{
    detailModal: false,
    currentSubmission: null,
    viewDetails(sub) {
        this.currentSubmission = sub;
        this.detailModal = true;
    },
    getFieldLabel(key) {
        const labels = {
            'fullname': 'Họ và tên',
            'name': 'Họ và tên',
            'phone': 'Số điện thoại',
            'email': 'Địa chỉ Email',
            'service': 'Dịch vụ quan tâm',
            'subject': 'Tiêu đề / Dịch vụ',
            'message': 'Nội dung ghi chú',
            'note': 'Ghi chú',
            'page_url': 'Trang gửi (URL)',
            'product_title': 'Tên sản phẩm',
            'product_sku': 'Mã sản phẩm (SKU)',
            'product_price': 'Đơn giá tham khảo',
            'product_quantity': 'Số lượng yêu cầu',
            'product_variation': 'Biến thể / Thuộc tính',
            'company': 'Công ty / Đơn vị',
            'company_name': 'Công ty / Đơn vị',
            'address': 'Địa chỉ',
            'source': 'Nguồn gửi',
            'privacy_consent': 'Đồng ý chính sách',
            'newsletter': 'Đăng ký nhận tin',
            'form_name': 'Tên biểu mẫu'
        };
        return labels[key] || key.replace(/_/g, ' ');
    },
    getFieldValue(key, val) {
        if (key === 'service') {
            const services = {
                'bao-gia-san-pham': 'Yêu cầu báo giá sản phẩm',
                'vat-tu-thiet-bi-dien': 'Vật tư thiết bị điện',
                'cot-be-tong-ly-tam': 'Cột bê tông ly tâm',
                'thi-cong-xay-lap': 'Thi công xây lắp điện',
                'thiet-bi-nang-luong-mat-troi': 'Năng lượng mặt trời',
                'tu-van-thiet-ke': 'Tư vấn thiết kế'
            };
            return services[val] || val;
        }
        if (typeof val === 'boolean') return val ? 'Có (Đã đồng ý)' : 'Không';
        if (key === 'newsletter' || key === 'privacy_consent') {
            if (val === 1 || val === '1' || val === 'on') return 'Có (Đã đồng ý)';
        }
        return val;
    }
}">

   

    <!-- 2. Core KPI Cards (4 metrics focused on visits & forms) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Tổng lượt truy cập -->
        <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Lượt Truy Cập</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ number_format($vs['total_visits'] ?? 0) }}</span>
                    @php $growth = $vs['visit_growth'] ?? 0; @endphp
                    <span class="text-xs font-bold {{ $growth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        <i class="fa-solid {{ $growth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Hôm nay: <strong class="text-slate-800">{{ number_format($vs['visits_today'] ?? 0) }}</strong></span>
                <span>Hôm qua: {{ number_format($vs['visits_yesterday'] ?? 0) }}</span>
            </div>
        </div>

        <!-- Card 2: Khách truy cập độc nhất -->
        <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Khách Độc Nhất (IPs)</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ number_format($vs['unique_ips_total'] ?? ($vs['unique_ips'] ?? 0)) }}</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        {{ number_format($vs['unique_ips'] ?? 0) }} hôm nay
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Địa chỉ IP truy cập duy nhất</span>
                <i class="fa-solid fa-shield-halved text-emerald-500"></i>
            </div>
        </div>

        <!-- Card 3: Form đã nhận -->
        <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Form Liên Hệ / Input</span>
                    <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ number_format($fs['total_submissions'] ?? 0) }}</span>
                    @if(($fs['submissions_pending'] ?? 0) > 0)
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full animate-pulse">
                        {{ $fs['submissions_pending'] }} chờ duyệt
                    </span>
                    @endif
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Mới hôm nay: <strong class="text-violet-700">+{{ $fs['submissions_today'] ?? 0 }}</strong></span>
                <span>Đã duyệt: {{ $fs['submissions_approved'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Card 4: Tỷ lệ chuyển đổi / Tiếp cận -->
        <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tỷ Lệ Tương Tác Form</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ $fs['conversion_rate'] ?? 0 }}%</span>
                    <span class="text-xs text-slate-400">leads/traffic</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Khách gửi form tư vấn</span>
                <i class="fa-solid fa-chart-pie text-amber-500"></i>
            </div>
        </div>
    </div>

    <!-- 3. Traffic Trend & Device Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- 7-Day Traffic Trend Bar Chart (Col 8) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-blue-600"></i>
                        Xu Hướng Lưu Lượng Truy Cập 7 Ngày Gần Nhất
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Số lượt truy cập website phân bổ theo từng ngày</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                    7 Ngày qua
                </span>
            </div>

            <!-- Visual Bar Chart -->
            <div class="h-52 flex items-end justify-between gap-3 pt-6 px-2">
                @forelse($dailyVisits as $d)
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="relative w-full flex justify-center items-end h-full">
                        <!-- Tooltip on hover -->
                        <div class="absolute -top-8 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-[11px] font-bold px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap z-20">
                            {{ $d['visits'] }} lượt
                        </div>
                        <div class="w-full max-w-[42px] bg-gradient-to-t from-blue-600 to-indigo-500 rounded-t-lg traffic-bar group-hover:from-blue-500 group-hover:to-indigo-400 transition-all shadow-sm"
                             style="height: {{ max($d['percentage'] ?? 0, 6) }}%;">
                        </div>
                    </div>
                    <div class="text-center">
                        <span class="block text-[11px] font-bold text-slate-700">{{ $d['date'] }}</span>
                        <span class="block text-[10px] text-slate-400 uppercase font-medium">{{ $d['day'] }}</span>
                    </div>
                </div>
                @empty
                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                    Chưa có đủ dữ liệu theo ngày
                </div>
                @endforelse
            </div>
        </div>

        <!-- Devices & Sources Breakdown (Col 4) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-mobile-screen-button text-indigo-600"></i>
                    Thiết Bị & Nguồn Truy Cập
                </h3>
                <p class="text-xs text-slate-400 mb-5">Tỷ lệ truy cập từ thiết bị của người dùng</p>

                <!-- Devices List -->
                <div class="space-y-4">
                    @forelse($deviceData as $dev)
                    <div>
                        <div class="flex justify-between items-center text-xs font-bold text-slate-700 mb-1.5">
                            <span class="flex items-center gap-2">
                                @if($dev['device'] === 'Mobile')
                                    <i class="fa-solid fa-mobile-screen text-slate-400 text-sm"></i>
                                @elseif($dev['device'] === 'Tablet')
                                    <i class="fa-solid fa-tablet-screen-button text-slate-400 text-sm"></i>
                                @else
                                    <i class="fa-solid fa-desktop text-slate-400 text-sm"></i>
                                @endif
                                <span>{{ $dev['device'] }}</span>
                            </span>
                            <span>{{ $dev['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $dev['percentage'] }}%; background-color: {{ $dev['color'] ?? '#3b82f6' }};">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-400">
                        Chưa có dữ liệu phân tích thiết bị
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Sources -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nguồn lưu lượng</span>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($trafficData as $src)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200/80 text-[11px] font-medium text-slate-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>{{ $src['source'] }} ({{ $src['percentage'] }}%)</span>
                    </span>
                    @empty
                    <span class="text-xs text-slate-400">Direct Traffic (100%)</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 4. CORE SECTION: Danh sách User gửi thông tin qua form & Dữ liệu input đã lưu trữ -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">
                        Dữ Liệu Khách Hàng Gửi Qua Form (User Form Submissions)
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                        {{ $fs['total_submissions'] ?? count($recentSubs) }} dữ liệu đã lưu
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Toàn bộ thông tin user điền từ modal hoặc form liên hệ website đều được tự động lưu trữ đầy đủ vào cơ sở dữ liệu.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.form-submissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    <span>Xem tất cả</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Table of Submissions -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3.5 px-5">ID & Thời Gian</th>
                        <th class="py-3.5 px-5">Khách Hàng (Tên, SĐT, Email)</th>
                        <th class="py-3.5 px-5">Dịch Vụ Quan Tâm</th>
                        <th class="py-3.5 px-5">Nội Dung / Ghi Chú</th>
                        <th class="py-3.5 px-5">Địa Chỉ IP</th>
                        <th class="py-3.5 px-5 text-center">Trạng Thái</th>
                        <th class="py-3.5 px-5 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentSubs as $sub)
                    @php
                        $d = is_array($sub->data) ? $sub->data : [];
                        $name = $d['fullname'] ?? $d['Full_Name'] ?? $d['name'] ?? $d['Name'] ?? 'Chưa rõ';
                        $phone = $d['phone'] ?? $d['Phone_number'] ?? $d['phone_number'] ?? $d['sdt'] ?? '—';
                        $email = $d['email'] ?? $d['Email'] ?? '—';
                        $service = $d['service'] ?? $d['Company'] ?? $d['service_needed'] ?? $d['dich_vu'] ?? 'Tư vấn chung';
                        $message = $d['message'] ?? $d['Message'] ?? $d['noi_dung'] ?? '—';
                        $status = $sub->status ?? 'pending';
                        
                        // Status badge colors
                        $statusClass = match($status) {
                            'approved', 'contacted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                        };
                        $statusLabel = match($status) {
                            'approved' => 'Đã duyệt',
                            'contacted' => 'Đã liên hệ',
                            'rejected' => 'Từ chối',
                            default => 'Chờ xử lý',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- ID & Time -->
                        <td class="py-3.5 px-5 font-mono text-slate-500 whitespace-nowrap">
                            <span class="font-bold text-slate-900 block">#{{ $sub->id }}</span>
                            <span class="text-[11px] text-slate-400">
                                {{ $sub->created_at ? $sub->created_at->format('H:i d/m/Y') : ($sub->submitted_at ? $sub->submitted_at->format('H:i d/m/Y') : '—') }}
                            </span>
                        </td>

                        <!-- Customer Info -->
                        <td class="py-3.5 px-5">
                            <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-user text-slate-400"></i>
                                <span>{{ $name }}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-600">
                                @if($phone !== '—')
                                <a href="tel:{{ $phone }}" class="hover:text-blue-600 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-[10px] text-emerald-600"></i>
                                    <span>{{ $phone }}</span>
                                </a>
                                @endif
                                @if($email !== '—')
                                <a href="mailto:{{ $email }}" class="hover:text-blue-600 text-slate-500 flex items-center gap-1">
                                    <i class="fa-regular fa-envelope text-[10px]"></i>
                                    <span>{{ $email }}</span>
                                </a>
                                @endif
                            </div>
                        </td>

                        <!-- Service -->
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-medium text-[11px] border border-indigo-100">
                                <i class="fa-solid fa-briefcase text-[10px]"></i>
                                <span>{{ $sub->getFormattedFieldValue('service', $service) }}</span>
                            </span>
                        </td>

                        <!-- Message -->
                        <td class="py-3.5 px-5 max-w-xs">
                            <p class="truncate text-slate-600" title="{{ $message }}">
                                {{ $message }}
                            </p>
                            @if(isset($d['newsletter']) && $d['newsletter'])
                            <span class="inline-block mt-0.5 text-[10px] text-blue-600 font-semibold">
                                ✓ Đăng ký nhận bản tin
                            </span>
                            @endif
                        </td>

                        <!-- IP & Source -->
                        <td class="py-3.5 px-5 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                            <span>{{ $sub->ip_address ?? '—' }}</span>
                            <span class="block text-[10px] text-slate-400 font-sans">
                                Nguồn: {{ $sub->source ?? 'modal' }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3.5 px-5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status === 'approved' ? 'bg-emerald-500' : ($status === 'rejected' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                <span>{{ $statusLabel }}</span>
                            </span>
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button"
                                        @click="viewDetails({{ json_encode($sub) }})"
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 font-semibold transition-colors flex items-center gap-1 text-[11px]"
                                        title="Xem toàn bộ dữ liệu user input">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Xem chi tiết</span>
                                </button>
                                
                                <form action="{{ route('admin.form-submissions.update-status', $sub->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($status === 'pending')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="Đánh dấu đã xử lý / duyệt">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </button>
                                    @else
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition-colors" title="Đưa về chờ xử lý">
                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                    </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-lg">
                                    <i class="fa-regular fa-folder-open"></i>
                                </div>
                                <p class="font-bold text-slate-600">Chưa có thông tin gửi qua form</p>
                               
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Bottom Row: Top Pages & Recent Visitor Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Top Pages Viewed (Col 5) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-fire text-amber-500"></i>
                    Top Trang Được Xem Nhiều Nhất
                </h3>
                <p class="text-xs text-slate-400 mb-4">Các đường dẫn được truy cập nhiều nhất 30 ngày qua</p>

                <div class="space-y-3">
                    @forelse($vs['top_pages'] ?? [] as $page)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/70 border border-slate-100 text-xs">
                        <span class="font-mono text-slate-700 truncate max-w-[220px]" title="{{ $page->url }}">
                            {{ $page->url ?: '/' }}
                        </span>
                        <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md whitespace-nowrap">
                            {{ number_format($page->visits) }} views
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-xs text-slate-400">
                        Chưa có thống kê trang xem
                    </div>
                    @endforelse
                </div>
            </div>

         
        </div>

        <!-- Recent Visitors Activity (Col 7) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-500"></i>
                        Nhật Ký Truy Cập Thời Gian Thực
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">10 phiên truy cập gần nhất được hệ thống ghi nhận</p>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-2.5">Địa chỉ IP</th>
                            <th class="pb-2.5">URL Truy cập</th>
                            <th class="pb-2.5">Thiết bị</th>
                            <th class="pb-2.5 text-right">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentVis as $vis)
                        @php
                            $ua = $vis->user_agent ?? '';
                            $isMobile = str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone');
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 font-mono text-slate-600 whitespace-nowrap">
                                <i class="fa-solid fa-globe text-slate-300 mr-1"></i>
                                {{ $vis->ip_address }}
                            </td>
                            <td class="py-2.5 font-mono text-slate-800 truncate max-w-[180px]" title="{{ $vis->url }}">
                                {{ $vis->url ?: '/' }}
                            </td>
                            <td class="py-2.5 text-slate-500 whitespace-nowrap">
                                @if($isMobile)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-medium">
                                        <i class="fa-solid fa-mobile-screen"></i> Mobile
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-medium">
                                        <i class="fa-solid fa-desktop"></i> Desktop
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 text-right font-mono text-slate-400 whitespace-nowrap">
                                {{ $vis->visited_at ? $vis->visited_at->diffForHumans() : 'Vừa xong' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400">
                                Chưa có nhật ký truy cập gần đây
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 6. Input Detail Modal (Alpine.js) -->
    <div x-show="detailModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="detailModal = false">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 transform transition-all"
             @click.away="detailModal = false">
            
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-300">
                        <i class="fa-solid fa-file-lines text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base leading-tight">Chi Tiết Form Input Đã Lưu Trữ</h3>
                        <p class="text-xs text-slate-300">Mã đơn: <span class="font-mono text-blue-300" x-text="'#' + (currentSubmission ? currentSubmission.id : '')"></span></p>
                    </div>
                </div>
                <button type="button" @click="detailModal = false" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Content: All saved key-values -->
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar text-xs">
                <template x-if="currentSubmission && currentSubmission.data">
                    <div class="space-y-3">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Loại Form</span>
                            <span class="font-bold text-slate-800 text-sm" x-text="currentSubmission.form_name || 'Inbetween V2 Contact'"></span>
                        </div>

                        <!-- Dynamic list of all fields in submission.data -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Dữ liệu input người dùng gửi</span>
                            
                            <template x-for="(value, key) in currentSubmission.data" :key="key">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1">
                                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400" x-text="getFieldLabel(key)"></span>
                                    <template x-if="typeof value === 'string' && (value.startsWith('http://') || value.startsWith('https://'))">
                                        <a :href="value" target="_blank" class="text-blue-600 hover:underline font-mono text-xs break-all inline-flex items-center gap-1">
                                            <span x-text="value"></span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    </template>
                                    <template x-if="!(typeof value === 'string' && (value.startsWith('http://') || value.startsWith('https://')))">
                                        <span class="font-medium text-slate-900 text-xs break-words whitespace-pre-line" x-text="getFieldValue(key, value)"></span>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Technical info -->
                        <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px] text-slate-500">
                            <div>
                                <span class="block text-slate-400">Địa chỉ IP:</span>
                                <span class="font-mono text-slate-700" x-text="currentSubmission.ip_address || '—'"></span>
                            </div>
                            <div>
                                <span class="block text-slate-400">Thời gian gửi:</span>
                                <span class="font-mono text-slate-700" x-text="currentSubmission.created_at || currentSubmission.submitted_at || '—'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <button type="button" @click="detailModal = false" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-colors">
                    Đóng
                </button>
                <span class="text-[11px] text-slate-400">Dữ liệu được lưu trữ vĩnh viễn trong CSDL</span>
            </div>
        </div>
    </div>

</div>
@endsection

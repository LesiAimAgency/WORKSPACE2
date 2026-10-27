@extends('cms.layouts.app')

@section('title', 'Chi tiết phản hồi khảo sát - ' . $response->customer_name)
@section('page-title', 'Chi tiết phản hồi khảo sát')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900">{{ $response->customer_name }}</h2>
                <span class="text-xs text-gray-500">|</span>
                <span class="text-xs font-semibold text-gray-700">Dự án: {{ $response->project_name }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Gửi lúc: {{ $response->created_at->format('d/m/Y H:i:s') }} (IP: {{ $response->ip_address ?: 'N/A' }})</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                In phiếu
            </button>
            <a href="{{ route('admin.surveys.responses.index', $response->survey_id) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Overview Score Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Part A Score -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Phần A: Thi công</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600">{{ $response->score_a ? number_format((float)$response->score_a, 2) : 'N/A' }}</span>
                <span class="text-xs text-gray-400">/ 5.0</span>
            </div>
            <div class="mt-2 text-[11px] text-gray-500">Chất lượng hoàn thiện &amp; kỹ thuật</div>
        </div>

        <!-- Part B Score -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Phần B: Dịch vụ &amp; Quản lý</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-teal-600">{{ $response->score_b ? number_format((float)$response->score_b, 2) : 'N/A' }}</span>
                <span class="text-xs text-gray-400">/ 5.0</span>
            </div>
            <div class="mt-2 text-[11px] text-gray-500">Tiến độ, an toàn, phản hồi PM</div>
        </div>

        <!-- NPS Score -->
        @php
            $nps = (int) $response->nps_score;
            $npsBadge = $nps >= 9 ? 'bg-emerald-100 text-emerald-800' : ($nps >= 7 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800');
            $npsDesc = $nps >= 9 ? 'Promoter (Khách hàng tích cực)' : ($nps >= 7 ? 'Passive (Khách hàng trung tính)' : 'Detractor (Cần chú ý cải thiện)');
        @endphp
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mức độ sẵn lòng giới thiệu (NPS)</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-rose-600">{{ $nps }}</span>
                <span class="text-xs text-gray-400">/ 10</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ml-2 {{ $npsBadge }}">
                    {{ $nps >= 9 ? 'Promoter' : ($nps >= 7 ? 'Passive' : 'Detractor') }}
                </span>
            </div>
            <div class="mt-2 text-[11px] text-gray-500">{{ $npsDesc }}</div>
        </div>
    </div>

    <!-- Project & Customer Details -->
    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-3">
            Thông tin dự án &amp; Khách hàng
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block text-[11px]">Tên khách hàng:</span>
                <span class="font-bold text-gray-900">{{ $response->customer_name }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Dự án:</span>
                <span class="font-bold text-gray-900">{{ $response->project_name }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Người phụ trách (PM):</span>
                <span class="font-medium text-gray-800">{{ $response->pm_name ?: 'Chưa cập nhật' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Ngày bàn giao:</span>
                <span class="font-medium text-gray-800">
                    {{ $response->handover_date ? \Carbon\Carbon::parse($response->handover_date)->format('d/m/Y') : 'Chưa cập nhật' }}
                </span>
            </div>
            @if($response->customer_phone)
                <div>
                    <span class="text-gray-400 block text-[11px]">Số điện thoại:</span>
                    <span class="font-medium text-gray-800">{{ $response->customer_phone }}</span>
                </div>
            @endif
            @if($response->customer_email)
                <div>
                    <span class="text-gray-400 block text-[11px]">Email:</span>
                    <span class="font-medium text-gray-800">{{ $response->customer_email }}</span>
                </div>
            @endif
            @if($response->invitation)
                <div>
                    <span class="text-gray-400 block text-[11px]">Thư mời gốc:</span>
                    <span class="font-mono text-indigo-600 text-[11px]">Token: {{ substr($response->invitation->token, 0, 10) }}...</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Answers Breakdown -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden p-6 space-y-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            Chi tiết từng câu trả lời
        </h3>

        @php
            $ratingLabels = [
                1 => ['label' => '1 - Rất kém', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'active_dot' => 'bg-rose-600 text-white ring-2 ring-rose-400 shadow-sm'],
                2 => ['label' => '2 - Kém', 'class' => 'bg-orange-50 text-orange-700 border-orange-200', 'active_dot' => 'bg-orange-500 text-white ring-2 ring-orange-400 shadow-sm'],
                3 => ['label' => '3 - Trung bình', 'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'active_dot' => 'bg-amber-500 text-white ring-2 ring-amber-400 shadow-sm'],
                4 => ['label' => '4 - Tốt', 'class' => 'bg-teal-50 text-teal-700 border-teal-200', 'active_dot' => 'bg-teal-600 text-white ring-2 ring-teal-400 shadow-sm'],
                5 => ['label' => '5 - Xuất sắc', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'active_dot' => 'bg-emerald-600 text-white ring-2 ring-emerald-400 shadow-sm'],
            ];
        @endphp

        <!-- Section A Answers -->
        <div class="space-y-3">
            <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider bg-emerald-50 px-3 py-1.5 rounded-lg">
                Phần A: Đánh giá chất lượng thi công
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($response->answers as $ans)
                    @if($ans->question && $ans->question->section === 'A')
                        @php
                            $rating = (int) ($ans->rating_value ?? $ans->numeric_value ?? 0);
                            $badge = $ratingLabels[$rating] ?? ['label' => 'Chưa chấm', 'class' => 'bg-gray-50 text-gray-400 border-gray-200', 'active_dot' => 'bg-gray-400 text-white'];
                        @endphp
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="text-xs text-gray-800 font-medium">
                                <span class="font-mono font-bold text-emerald-700 mr-1.5">[{{ $ans->question->code }}]</span>
                                {{ $ans->question->question_text }}
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <div class="flex items-center gap-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all {{ $rating === $i ? ($ratingLabels[$i]['active_dot'] . ' scale-105') : 'bg-white border border-gray-200 text-gray-300' }}">
                                            {{ $i }}
                                        </span>
                                    @endfor
                                </div>
                                <span class="inline-flex items-center justify-center text-[11px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Section B Answers -->
        <div class="space-y-3 pt-2">
            <div class="text-xs font-bold text-teal-800 uppercase tracking-wider bg-teal-50 px-3 py-1.5 rounded-lg">
                Phần B: Đánh giá dịch vụ &amp; Quản lý dự án
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($response->answers as $ans)
                    @if($ans->question && $ans->question->section === 'B')
                        @php
                            $rating = (int) ($ans->rating_value ?? $ans->numeric_value ?? 0);
                            $badge = $ratingLabels[$rating] ?? ['label' => 'Chưa chấm', 'class' => 'bg-gray-50 text-gray-400 border-gray-200', 'active_dot' => 'bg-gray-400 text-white'];
                        @endphp
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="text-xs text-gray-800 font-medium">
                                <span class="font-mono font-bold text-teal-700 mr-1.5">[{{ $ans->question->code }}]</span>
                                {{ $ans->question->question_text }}
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <div class="flex items-center gap-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all {{ $rating === $i ? ($ratingLabels[$i]['active_dot'] . ' scale-105') : 'bg-white border border-gray-200 text-gray-300' }}">
                                            {{ $i }}
                                        </span>
                                    @endfor
                                </div>
                                <span class="inline-flex items-center justify-center text-[11px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Section NPS Answer -->
        <div class="space-y-3 pt-2">
            <div class="text-xs font-bold text-amber-800 uppercase tracking-wider bg-amber-50 px-3 py-1.5 rounded-lg flex items-center justify-between">
                <span>Chỉ số Net Promoter Score (NPS)</span>
                <span class="text-[11px] font-normal text-amber-700">Mức độ sẵn sàng giới thiệu cho đối tác, bạn bè</span>
            </div>
            @foreach($response->answers as $ans)
                @if($ans->question && $ans->question->section === 'NPS')
                    @php
                        $npsVal = $ans->rating_value !== null ? (int) $ans->rating_value : (is_numeric($ans->numeric_value) ? (int) $ans->numeric_value : (is_numeric($ans->text_value) ? (int) $ans->text_value : null));
                    @endphp
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold text-gray-900">
                                {{ $ans->question->question_text }}
                            </div>
                            @if($npsVal !== null)
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md shadow-sm {{ $npsVal >= 9 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($npsVal >= 7 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300') }}">
                                    {{ $npsVal }}/10 • {{ $npsVal >= 9 ? 'Promoter (Rất hài lòng)' : ($npsVal >= 7 ? 'Passive (Hài lòng)' : 'Detractor (Cần cải thiện)') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap pt-1">
                            @for($i = 0; $i <= 10; $i++)
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all {{ $npsVal !== null && $npsVal === $i ? 'bg-rose-600 text-white shadow-md ring-2 ring-rose-400 scale-105' : 'bg-white border border-gray-200 text-gray-400' }}">
                                    {{ $i }}
                                </span>
                            @endfor
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Open Answers -->
        <div class="space-y-3 pt-2">
            <div class="text-xs font-bold text-gray-800 uppercase tracking-wider bg-gray-100 px-3 py-1.5 rounded-lg">
                Ý kiến đóng góp &amp; Chia sẻ bổ sung
            </div>
            <div class="space-y-3">
                @foreach($response->answers as $ans)
                    @if($ans->question && $ans->question->section === 'OPEN')
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-1.5">
                            <div class="text-xs font-bold text-gray-800">
                                {{ $ans->question->question_text }}
                            </div>
                            <div class="text-xs text-gray-700 whitespace-pre-line bg-white p-3 rounded-lg border border-gray-100">
                                {{ $ans->text_value ?: '(Khách hàng không để lại ý kiến cho mục này)' }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions Footer -->
    <div class="flex items-center justify-between pt-2">
        <form action="{{ route('admin.surveys.responses.destroy', $response->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi khảo sát này? Thao tác không thể hoàn tác.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa phản hồi này
            </button>
        </form>

        <a href="{{ route('admin.surveys.responses.index', $response->survey_id) }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
            Quay lại danh sách
        </a>
    </div>
</div>
@endsection

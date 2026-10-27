@extends('cms.layouts.app')

@section('title', 'Quản lý Khảo sát khách hàng (Customer Survey)')
@section('page-title', 'Khảo sát khách hàng')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Links -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Quản trị Biểu mẫu &amp; Phản hồi Khảo sát</h2>
            <p class="text-xs text-gray-500 mt-1">Theo dõi chỉ số mức độ hài lòng khách hàng (CSAT &amp; NPS), quản lý câu hỏi và gửi thư mời khảo sát.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('customer-survey.show') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Form Khảo Sát
            </a>
            <a href="{{ route('admin.settings.notifications') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Cấu hình SMTP Mailer
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Responses -->
        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tổng phản hồi</div>
                <div class="text-xl font-bold text-gray-900 mt-0.5">{{ number_format($totalResponses) }}</div>
            </div>
        </div>

        <!-- Score A -->
        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                A
            </div>
            <div>
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Điểm TB Phần A (Thi công)</div>
                <div class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $avgScoreA ? number_format((float)$avgScoreA, 2) . ' / 5.0' : 'Chưa có' }}
                </div>
            </div>
        </div>

        <!-- Score B -->
        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                B
            </div>
            <div>
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Điểm TB Phần B (Dịch vụ)</div>
                <div class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $avgScoreB ? number_format((float)$avgScoreB, 2) . ' / 5.0' : 'Chưa có' }}
                </div>
            </div>
        </div>

        <!-- Avg NPS -->
        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⭐
            </div>
            <div>
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Điểm Giới thiệu (NPS)</div>
                <div class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $avgNps ? number_format((float)$avgNps, 1) . ' / 10' : 'Chưa có' }}
                </div>
            </div>
        </div>

        <!-- NPS Index -->
        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg {{ $npsIndex >= 50 ? 'bg-emerald-50 text-emerald-600' : ($npsIndex >= 0 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600') }} flex items-center justify-center font-bold">
                %
            </div>
            <div>
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Chỉ số NPS Net</div>
                <div class="text-xl font-bold {{ $npsIndex >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-0.5">
                    {{ $totalResponses > 0 ? ($npsIndex > 0 ? "+{$npsIndex}" : $npsIndex) : 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Templates Table -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Danh sách biểu mẫu khảo sát</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Biểu mẫu khảo sát</th>
                        <th class="py-3 px-4">Số câu hỏi</th>
                        <th class="py-3 px-4">Lượt phản hồi</th>
                        <th class="py-3 px-4">Thư mời đã gửi</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4 text-right">Thao tác quản lý</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($surveys as $survey)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900 text-sm">{{ $survey->title }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">{{ $survey->subtitle ?: 'Khảo sát chất lượng bàn giao' }}</div>
                                <div class="mt-1 font-mono text-[10px] text-blue-600">{{ route('customer-survey.show') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 bg-gray-100 font-semibold rounded-md text-gray-800">
                                    {{ $survey->questions_count }} câu hỏi
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.surveys.responses.index', $survey->id) }}" class="font-bold text-rose-600 hover:underline">
                                    {{ $survey->responses_count }} phản hồi
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.surveys.invitations.index', $survey->id) }}" class="font-semibold text-indigo-600 hover:underline">
                                    {{ $survey->invitations_count }} thư mời
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                @if($survey->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Đang mở tiếp nhận
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">
                                        Đang tạm đóng
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.surveys.responses.index', $survey->id) }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-md transition-colors flex items-center gap-1" title="Xem danh sách phản hồi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Phản hồi ({{ $survey->responses_count }})
                                    </a>
                                    <a href="{{ route('admin.surveys.invitations.index', $survey->id) }}" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-md transition-colors flex items-center gap-1" title="Gửi thư mời email">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Gửi email
                                    </a>
                                    <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-md transition-colors flex items-center gap-1" title="Chỉnh sửa câu hỏi & biểu mẫu">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Sửa câu hỏi
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                Chưa có biểu mẫu khảo sát nào. Hãy khởi tạo từ cơ sở dữ liệu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

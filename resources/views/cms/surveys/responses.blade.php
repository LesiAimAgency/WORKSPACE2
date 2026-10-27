@extends('cms.layouts.app')

@section('title', 'Danh sách phản hồi khảo sát - ' . $survey->title)
@section('page-title', 'Danh sách phản hồi khảo sát')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Danh sách ý kiến &amp; Phản hồi khảo sát</h2>
            <p class="text-xs text-gray-500 mt-1">Biểu mẫu: <span class="font-bold text-gray-800">{{ $survey->title }}</span> • Tổng số: <span class="font-bold text-rose-600">{{ $responses->total() }}</span> phản hồi</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Quản lý câu hỏi
            </a>
            <a href="{{ route('admin.surveys.invitations.index', $survey->id) }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Gửi thư mời email
            </a>
            <a href="{{ route('admin.surveys.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.surveys.responses.index', $survey->id) }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên khách hàng, dự án, PM, email..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <select name="nps_filter" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <option value="">Tất cả phân nhóm NPS</option>
                    <option value="promoter" {{ request('nps_filter') === 'promoter' ? 'selected' : '' }}>Promoters (Điểm 9 - 10: Rất hài lòng)</option>
                    <option value="passive" {{ request('nps_filter') === 'passive' ? 'selected' : '' }}>Passives (Điểm 7 - 8: Trung tính)</option>
                    <option value="detractor" {{ request('nps_filter') === 'detractor' ? 'selected' : '' }}>Detractors (Điểm 0 - 6: Cần cải thiện)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Lọc dữ liệu
                </button>
                @if(request()->anyFilled(['search', 'nps_filter']))
                    <a href="{{ route('admin.surveys.responses.index', $survey->id) }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Responses Table -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Khách hàng &amp; Dự án</th>
                        <th class="py-3 px-4">Phần A (Thi công)</th>
                        <th class="py-3 px-4">Phần B (Dịch vụ)</th>
                        <th class="py-3 px-4">NPS (Sẵn lòng giới thiệu)</th>
                        <th class="py-3 px-4">Người quản lý (PM)</th>
                        <th class="py-3 px-4">Thời gian gửi</th>
                        <th class="py-3 px-4 text-right">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($responses as $resp)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900 text-sm">{{ $resp->customer_name }}</div>
                                <div class="text-[11px] text-gray-600 mt-0.5">Dự án: <span class="font-medium text-gray-800">{{ $resp->project_name }}</span></div>
                                @if($resp->customer_email)
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $resp->customer_email }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">
                                    {{ $resp->score_a ? number_format((float)$resp->score_a, 1) : 'N/A' }} / 5.0
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center gap-1 font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded text-xs">
                                    {{ $resp->score_b ? number_format((float)$resp->score_b, 1) : 'N/A' }} / 5.0
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $nps = (int) $resp->nps_score;
                                    $npsClass = $nps >= 9 ? 'bg-emerald-100 text-emerald-800' : ($nps >= 7 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800');
                                    $npsLabel = $nps >= 9 ? 'Promoter' : ($nps >= 7 ? 'Passive' : 'Detractor');
                                @endphp
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded font-bold text-xs {{ $npsClass }}">
                                    <span>{{ $nps }}/10</span>
                                    <span class="text-[10px] font-normal uppercase">({{ $npsLabel }})</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-xs font-medium text-gray-800">{{ $resp->pm_name ?: 'Chưa cập nhật' }}</div>
                                @if($resp->handover_date)
                                    <div class="text-[10px] text-gray-400">Bàn giao: {{ \Carbon\Carbon::parse($resp->handover_date)->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                <div>{{ $resp->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $resp->created_at->format('H:i') }}</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.surveys.responses.show', $resp->id) }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-md transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </a>
                                    <form action="{{ route('admin.surveys.responses.destroy', $resp->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi khảo sát này?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 hover:bg-red-50 text-red-600 rounded-md transition-colors" title="Xóa phản hồi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Chưa có phản hồi nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($responses->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $responses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

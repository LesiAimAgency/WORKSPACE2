


@extends('cms.layouts.app')

@section('title', 'Gửi thư mời khảo sát - ' . $survey->title)
@section('page-title', 'Thư mời khảo sát &amp; Nhật ký gửi')

@section('content')
<div class="space-y-6" x-data="{ showPreview: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Gửi thư mời khảo sát khách hàng qua Email</h2>
            <p class="text-xs text-gray-500 mt-1">Gửi link khảo sát kèm token bảo mật trực tiếp tới hòm thư của khách hàng. Theo dõi trạng thái đã gửi / phản hồi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.settings.notifications') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                Cấu hình SMTP
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

    @if (session('warning'))
        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 text-xs rounded-r-lg font-medium">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 text-xs rounded-r-lg font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg space-y-1">
            <div class="font-bold">Có lỗi xảy ra:</div>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Sending Form -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            1. Soạn và Gửi thư mời khảo sát
        </h3>

        <form action="{{ route('admin.surveys.invitations.send', $survey->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Danh sách Email người nhận <span class="text-rose-500">*</span>
                        <span class="text-[11px] text-gray-400 font-normal ml-1">(Hỗ trợ nhiều email cách nhau bởi dấu phẩy, chấm phẩy hoặc xuống dòng)</span>
                    </label>
                    <textarea name="emails" rows="2" required placeholder="khachhang1@gmail.com, khachhang2@congty.vn..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 font-mono">{{ old('emails') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên khách hàng / Đại diện (Tùy chọn)</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="Ví dụ: Anh Nam / Chị Linh..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên dự án công trình (Tùy chọn)</label>
                    <input type="text" name="project_name" value="{{ old('project_name') }}" placeholder="Ví dụ: Văn phòng Techcombank Tầng 12..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề thư mời (Subject) <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $defaultSubject) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nội dung thư mời <span class="text-rose-500">*</span></label>
                    <textarea name="custom_message" rows="5" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 leading-relaxed">{{ old('custom_message', $defaultTemplate) }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Hệ thống sẽ tự động chèn nút bấm "Bắt đầu làm bài khảo sát" kèm liên kết bảo mật có token riêng cho từng khách hàng.</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t">
                <button type="button" @click="showPreview = !showPreview" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span x-text="showPreview ? 'Ẩn xem trước email' : 'Xem trước định dạng email'"></span>
                </button>

                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Gửi thư mời ngay
                </button>
            </div>
        </form>

        <!-- Preview Modal / Card -->
        <div x-show="showPreview" class="p-6 bg-gray-50 rounded-xl border border-gray-200 space-y-4 text-xs" x-cloak>
            <div class="font-bold text-gray-800 text-sm border-b pb-2">Bản xem trước giao diện thư mời gửi tới khách hàng:</div>
            <div class="bg-white p-6 rounded-xl border border-gray-200 max-w-lg mx-auto shadow-sm space-y-4">
                <div class="text-center border-b pb-4">
                    <div class="text-base font-black text-rose-600 tracking-wider">WORKSPACE</div>
                    <div class="text-[10px] text-gray-400 uppercase mt-0.5">Khảo sát ý kiến khách hàng</div>
                </div>
                <div class="text-gray-800 space-y-2 whitespace-pre-line leading-relaxed">
                    {{ $defaultTemplate }}
                </div>
                <div class="text-center py-3">
                    <span class="inline-block px-6 py-3 bg-rose-600 text-white font-bold rounded-lg shadow-sm text-xs cursor-default">
                        Thực hiện khảo sát ngay &rarr;
                    </span>
                </div>
                <div class="text-[11px] text-gray-400 text-center border-t pt-3">
                    Link trực tiếp: <span class="font-mono text-blue-600">{{ route('customer-survey.show') }}?token=demo_token_12345</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitations Log Table -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Nhật ký gửi thư mời (Invitation Logs)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Lịch sử gửi thư mời và tình trạng phản hồi của khách hàng.</p>
            </div>

            <!-- Filter -->
            <form method="GET" action="{{ route('admin.surveys.invitations.index', $survey->id) }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm email, tên, dự án..." class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">
                <select name="status" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">
                    <option value="">Tất cả trạng thái</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Đã gửi thành công</option>
                    <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>Đã hoàn thành</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Lỗi gửi mail</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg">
                    Lọc
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Email người nhận</th>
                        <th class="py-3 px-4">Khách hàng / Dự án</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4">Thời gian gửi</th>
                        <th class="py-3 px-4">Link khảo sát</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($invitations as $inv)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900">{{ $inv->recipient_email }}</div>
                                @if($inv->attempts > 1)
                                    <div class="text-[10px] text-gray-400">Đã thử: {{ $inv->attempts }} lần</div>
                                @endif
                                @if($inv->error_message)
                                    <div class="text-[10px] text-red-600 mt-0.5 line-clamp-1" title="{{ $inv->error_message }}">
                                        Lỗi: {{ $inv->error_message }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-800">{{ $inv->recipient_name ?: 'Chưa đặt tên' }}</div>
                                @if($inv->project_name)
                                    <div class="text-[10px] text-gray-400">{{ $inv->project_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($inv->status === 'responded')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Đã phản hồi
                                    </span>
                                @elseif($inv->status === 'sent')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Đã gửi mail
                                    </span>
                                @elseif($inv->status === 'failed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">
                                        Lỗi gửi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                        Chờ xử lý
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                @if($inv->sent_at)
                                    <div>{{ $inv->sent_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <div class="text-gray-400">Chưa gửi</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5">
                                    <input type="text" readonly value="{{ $inv->getSurveyUrl() }}" class="w-36 px-2 py-1 bg-gray-50 border border-gray-200 rounded text-[10px] font-mono text-gray-600 focus:outline-none" onclick="this.select();">
                                    <a href="{{ $inv->getSurveyUrl() }}" target="_blank" class="p-1 hover:bg-gray-100 text-blue-600 rounded" title="Mở link">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    @if($inv->status === 'failed' || $inv->status === 'pending')
                                        <form action="{{ route('admin.surveys.invitations.retry', $inv->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded text-[11px] transition-colors" title="Gửi lại email">
                                                Gửi lại
                                            </button>
                                        </form>
                                    @endif

                                    @if($inv->response)
                                        <a href="{{ route('admin.surveys.responses.show', $inv->response->id) }}" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded text-[11px] transition-colors">
                                            Xem kết quả
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.surveys.invitations.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bản ghi thư mời này?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 hover:bg-red-50 text-red-600 rounded transition-colors" title="Xóa thư mời">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Chưa có thư mời nào được gửi. Hãy nhập email ở biểu mẫu phía trên để gửi thư mời đầu tiên.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invitations->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $invitations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

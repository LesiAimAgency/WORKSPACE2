@extends(!empty($isStandalone) ? 'admin.tools.layouts.standalone' : 'admin.layouts.app')

@section('page-title', 'Công Cụ Đổi Domain Database')

@section('content')
<div class="space-y-6 {{ !empty($isStandalone) ? '' : 'max-w-6xl mx-auto' }}">

    @if(!empty($isStandalone) && empty($isAuthenticated))
        <!-- Màn hình khóa bảo mật khi truy cập standalone chưa đăng nhập -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 max-w-md mx-auto shadow-2xl text-center text-slate-100">
            <div class="w-16 h-16 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-500/20 text-3xl font-bold">
                🔒
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Yêu Cầu Khóa Xác Thực</h2>
            <p class="text-slate-400 text-sm mb-6">
                Bạn đang truy cập công cụ can thiệp Database trực tiếp. Vui lòng nhập Secret Key (hoặc đăng nhập Admin) để mở khóa.
            </p>

            @if($message)
                <div class="mb-5 p-3 rounded-xl text-xs bg-rose-950/60 border border-rose-500/40 text-rose-300 text-left">
                    <strong>{{ $message['title'] }}:</strong> {{ $message['content'] }}
                </div>
            @endif

            <form method="POST" action="{{ route('tools.replace-domain') }}" class="space-y-4">
                @csrf
                <div>
                    <input type="password" name="key" placeholder="Nhập Secret Key..." required autofocus
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl text-sm transition shadow-lg shadow-indigo-600/30">
                    Xác Thực & Mở Khóa
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-5">
                Mẹo: Key mặc định có thể là <code class="text-indigo-400 font-mono">daiphuc68</code> hoặc kiểm tra biến <code class="text-slate-400 font-mono">APP_KEY</code> trong file .env.
            </p>
        </div>
    @else
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-500/20 rounded-2xl p-6 shadow-xl text-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 tracking-wider">
                        VGT Core Tool
                    </span>
                    <span class="px-2.5 py-0.5 text-xs rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                        DB: <strong class="text-white font-mono">{{ $dbName }}</strong>
                    </span>
                    <span class="px-2.5 py-0.5 text-xs rounded-md bg-emerald-950/60 text-emerald-300 border border-emerald-800/40">
                        Host hiện tại: <strong class="text-white font-mono">{{ $currentUrl }}</strong>
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-white">Tìm & Thay Thế Domain / URL Database</h1>
                <p class="text-slate-400 text-xs md:text-sm mt-1">
                    Tự động tìm kiếm và đổi URL cũ sang domain mới trên tất cả các bảng. Hỗ trợ Plain Text, JSON Escaped (<code class="text-slate-300 font-mono">\/</code>), và PHP Serialized data an toàn tuyệt đối.
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if(!empty($isStandalone))
                    <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 px-3 py-2 rounded-xl text-xs font-medium transition">
                        Vào Admin Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('tools.replace-domain') }}" target="_blank" title="Mở trang độc lập khi đổi hosting" class="bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 px-3 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Mở link trực tiếp
                    </a>
                @endif
            </div>
        </div>

        <!-- Alert Thông Báo Kết Quả -->
        @if($message)
            <div class="rounded-2xl p-5 border {{ $message['type'] === 'success' ? 'bg-emerald-950/70 border-emerald-500/50 text-emerald-100' : ($message['type'] === 'error' ? 'bg-rose-950/70 border-rose-500/50 text-rose-100' : ($message['type'] === 'warning' ? 'bg-amber-950/70 border-amber-500/50 text-amber-100' : 'bg-indigo-950/70 border-indigo-500/50 text-indigo-100')) }} shadow-lg">
                <div class="flex items-start gap-3">
                    <span class="text-xl font-bold flex-shrink-0">
                        @if($message['type'] === 'success') ✓
                        @elseif($message['type'] === 'error') ✗
                        @elseif($message['type'] === 'warning') ⚠
                        @else ℹ @endif
                    </span>
                    <div>
                        <h3 class="font-bold text-base">{{ $message['title'] }}</h3>
                        <p class="text-sm mt-0.5 opacity-90">{{ $message['content'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Cấu Hình Thay Thế -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl text-slate-100">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                Thiết lập Chuyển đổi Tên Miền / URL
            </h2>

            <form method="POST" action="{{ !empty($isStandalone) ? route('tools.replace-domain') : route('admin.replace-domain.run') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- URL CŨ (FROM) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase text-slate-400 tracking-wider">
                            1. Chuỗi / Domain CŨ cần tìm <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="from" id="input-from" value="{{ $searchFrom }}" required
                                placeholder="http://localhost:8000"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-indigo-500 shadow-inner">
                        </div>
                        <p class="text-xs text-slate-400">
                            Chuỗi hiện đang lưu trong database (ví dụ: <code class="text-indigo-400 font-mono">http://localhost:8000</code>).
                        </p>

                        <!-- Gợi ý domain tìm thấy trong DB -->
                        @if(!empty($detectedDomains))
                            <div class="pt-2">
                                <span class="text-[11px] font-semibold text-slate-400 block mb-1.5">Gợi ý từ hệ thống & DB:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($detectedDomains as $d)
                                        <button type="button" onclick="setFromDomain('{{ $d }}')" 
                                            class="px-2.5 py-1 text-xs font-mono rounded-lg bg-slate-800 hover:bg-indigo-600/40 text-slate-300 hover:text-white border border-slate-700 transition">
                                            {{ $d }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- URL MỚI (TO) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase text-slate-400 tracking-wider">
                            2. Chuỗi / Domain MỚI cần thay thế
                        </label>
                        <div class="relative">
                            <input type="text" name="to" id="input-to" value="{{ $replaceTo }}"
                                placeholder="{{ $currentUrl }}"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-indigo-500 shadow-inner">
                        </div>
                        <p class="text-xs text-slate-400">
                            Tên miền mới (ví dụ: <code class="text-emerald-400 font-mono">{{ $currentUrl }}</code>) hoặc để relative path (<code class="text-emerald-400 font-mono">/storage</code>).
                        </p>

                        <!-- Phím tắt nhanh cho URL mới -->
                        <div class="pt-2">
                            <span class="text-[11px] font-semibold text-slate-400 block mb-1.5">Phím tắt nhanh URL mới:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="setToDomain('{{ $currentUrl }}')" 
                                    class="px-2.5 py-1 text-xs font-mono rounded-lg bg-emerald-950/50 hover:bg-emerald-700/50 text-emerald-300 hover:text-white border border-emerald-700/50 transition">
                                    ★ Lấy URL đang chạy ({{ $currentUrl }})
                                </button>
                                <button type="button" onclick="setToDomain('/storage')" 
                                    class="px-2.5 py-1 text-xs font-mono rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">
                                    Relative /storage
                                </button>
                                <button type="button" onclick="setToDomain('')" 
                                    class="px-2.5 py-1 text-xs font-mono rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700 transition">
                                    Để rỗng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tùy chọn nâng cao -->
                <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-4 space-y-3">
                    <span class="text-xs font-bold uppercase text-slate-400 tracking-wider block">Tùy Chọn Thực Thi</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                            <input type="checkbox" name="skip_logs" value="1" checked 
                                class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                            <span>Bỏ qua bảng log (visitor_logs, activity_logs, sessions...)</span>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                            <input type="checkbox" name="update_env" value="1" checked 
                                class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                            <span>Tự động cập nhật <code class="text-indigo-400 font-mono">APP_URL</code> vào file .env</span>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                            <input type="checkbox" name="clear_cache" value="1" checked 
                                class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                            <span>Dọn sạch cache Laravel sau khi hoàn tất</span>
                        </label>
                    </div>
                </div>

                <!-- Nút Hành Động -->
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                    <button type="submit" name="action" value="preview" 
                        class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-semibold px-6 py-3.5 rounded-xl text-sm transition border border-slate-700 flex items-center justify-center gap-2 shadow">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Bước 1: Quét Thử & Xem Trước (Dry-Run)
                    </button>

                    <button type="submit" name="action" value="execute" 
                        onclick="return confirm('CẢNH BÁO XÁC NHẬN:\n\nBạn có chắc chắn muốn THỰC THI THAY THẾ trực tiếp vào Database?\nThao tác này sẽ cập nhật dữ liệu vĩnh viễn!')"
                        class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold px-8 py-3.5 rounded-xl text-sm transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Bước 2: THỰC THI THAY THẾ DATABASE
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng Kết Quả Chi Tiết -->
        @if($scanResult)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4 text-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span>Chi tiết kết quả quét ({{ $scanResult['columns_matched'] }} cột có dữ liệu khớp)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Quét trên {{ $scanResult['tables_scanned'] }} bảng &bull; Tìm kiếm: <code class="text-indigo-400 font-mono">{{ $scanResult['from'] }}</code> &bull; Thời gian: {{ $scanResult['execution_time'] }}s
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs rounded-lg bg-slate-800 border border-slate-700">
                            Khớp: <strong class="text-amber-400">{{ $scanResult['total_matched'] }}</strong> dòng
                        </span>
                        @if(!$scanResult['is_dry_run'])
                            <span class="px-3 py-1 text-xs rounded-lg bg-emerald-950 border border-emerald-800 text-emerald-300">
                                Đã sửa: <strong class="text-white">{{ $scanResult['total_updated'] }}</strong> dòng
                            </span>
                        @endif
                    </div>
                </div>

                @if(empty($scanResult['details']))
                    <div class="bg-slate-950 rounded-xl p-8 text-center text-slate-400 text-sm border border-slate-800">
                        Không tìm thấy bất kỳ dòng nào chứa chuỗi <code class="text-indigo-400 font-mono">{{ $scanResult['from'] }}</code> trong các bảng đã quét.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                                <tr>
                                    <th class="py-3 px-4">Bảng (Table)</th>
                                    <th class="py-3 px-4">Cột (Column)</th>
                                    <th class="py-3 px-4 text-center">Số dòng khớp</th>
                                    <th class="py-3 px-4 text-center">Trạng thái</th>
                                    <th class="py-3 px-4">Mẫu Trước ➔ Sau khi đổi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-300">
                                @foreach($scanResult['details'] as $row)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-indigo-300">{{ $row['table'] }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-200">{{ $row['column'] }}</td>
                                        <td class="py-3 px-4 text-center font-bold text-amber-400">{{ $row['matched'] }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($scanResult['is_dry_run'])
                                                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-semibold">
                                                    Xem trước
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-semibold">
                                                    Đã đổi: {{ $row['updated'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 space-y-1 font-mono text-[11px] max-w-md">
                                            <div class="text-rose-400 truncate bg-rose-950/30 px-2 py-1 rounded border border-rose-900/40">
                                                - {{ $row['before'] }}
                                            </div>
                                            <div class="text-emerald-400 truncate bg-emerald-950/30 px-2 py-1 rounded border border-emerald-900/40">
                                                + {{ $row['after'] }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- Card Hướng dẫn & Thông tin thêm -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 text-xs text-slate-400 space-y-2">
            <h4 class="font-bold text-slate-200 text-sm flex items-center gap-2">
                <span>💡 Lưu ý an toàn khi đổi Domain:</span>
            </h4>
            <ul class="list-disc list-inside space-y-1 text-slate-400 leading-relaxed">
                <li>Luôn nên chạy <strong>Bước 1 (Quét Thử - Dry-Run)</strong> trước để kiểm tra số lượng bản ghi và xem trước mẫu chuyển đổi.</li>
                <li>Hệ thống tự động nhận diện cả URL dạng mã hóa JSON (ví dụ trong widgets hay cấu hình) và tự cập nhật chuẩn xác.</li>
                <li>Nếu cần chạy qua dòng lệnh Terminal/SSH, bạn vẫn có thể dùng lệnh: <code class="text-indigo-400 font-mono">php artisan db:replace-domain --from="..." --to="..."</code>.</li>
            </ul>
        </div>
    @endif

</div>

<script>
    function setFromDomain(domain) {
        const input = document.getElementById('input-from');
        if (input) {
            input.value = domain;
            input.focus();
        }
    }

    function setToDomain(domain) {
        const input = document.getElementById('input-to');
        if (input) {
            input.value = domain;
            input.focus();
        }
    }
</script>
@endsection

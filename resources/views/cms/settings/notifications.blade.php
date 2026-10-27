@extends('cms.settings.template', ['title' => 'Cấu hình Email SMTP'])

@section('form-content')
<div class="space-y-6">
    {{-- SMTP Server Settings --}}
    <div class="bg-gradient-to-r from-blue-50/80 to-indigo-50/80 p-6 rounded-xl border border-blue-200/80">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            1. Máy Chủ Gửi Email (SMTP Server)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Host *</label>
                <input type="text" name="mail_host" value="{{ old('mail_host', setting('mail_host', env('MAIL_HOST'))) }}" placeholder="smtp.gmail.com" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Gmail: <code class="bg-gray-100 px-1 py-0.5 rounded text-blue-600">smtp.gmail.com</code> | Mailtrap: <code class="bg-gray-100 px-1 py-0.5 rounded text-blue-600">smtp.mailtrap.io</code></p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Port *</label>
                <input type="number" name="mail_port" value="{{ old('mail_port', setting('mail_port', env('MAIL_PORT', 587))) }}" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Cổng TLS: <code class="bg-gray-100 px-1 py-0.5 rounded text-blue-600">587</code> | Cổng SSL: <code class="bg-gray-100 px-1 py-0.5 rounded text-blue-600">465</code></p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mã hóa (Encryption) *</label>
                <select name="mail_encryption" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    @php $currentEnc = old('mail_encryption', setting('mail_encryption', env('MAIL_ENCRYPTION', 'tls'))); @endphp
                    <option value="tls" {{ $currentEnc === 'tls' ? 'selected' : '' }}>TLS (Khuyến nghị cho Port 587)</option>
                    <option value="ssl" {{ $currentEnc === 'ssl' ? 'selected' : '' }}>SSL (Khuyến nghị cho Port 465)</option>
                    <option value="none" {{ $currentEnc === 'none' ? 'selected' : '' }}>Không mã hóa (None)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tài khoản SMTP (Username / Email) *</label>
                <input type="text" name="mail_username" value="{{ old('mail_username', setting('mail_username', env('MAIL_USERNAME'))) }}" placeholder="your-email@gmail.com" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu SMTP (Password / App Password) *</label>
                <input type="password" name="mail_password" value="{{ old('mail_password', setting('mail_password', env('MAIL_PASSWORD'))) }}" placeholder="••••••••••••••••" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-amber-700 mt-1.5 flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Nếu dùng Gmail cá nhân: Bạn phải bật <strong>Xác thực 2 bước</strong> và tạo <strong>Mật khẩu ứng dụng (App Password 16 ký tự)</strong>. Không dùng mật khẩu đăng nhập tài khoản thông thường.
                </p>
            </div>
        </div>
    </div>

    {{-- Sender & Receiver Info --}}
    <div class="bg-gradient-to-r from-emerald-50/80 to-teal-50/80 p-6 rounded-xl border border-emerald-200/80">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
            2. Thông Tin Người Gửi & Người Nhận
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email gửi đi (From Address) *</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', setting('mail_from_address', env('MAIL_FROM_ADDRESS', 'contact@inbetween.vn'))) }}" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Thường trùng với tài khoản SMTP hoặc email tên miền riêng</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tên người gửi (From Name) *</label>
                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', setting('mail_from_name', env('MAIL_FROM_NAME', 'in • between'))) }}" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Tên thương hiệu hiển thị trong hộp thư người nhận</p>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email nhận thông báo khi có khách gửi form (Admin Receiver Email) *</label>
                <input type="email" name="admin_email" value="{{ old('admin_email', setting('admin_email', setting('mail_admin_receiver', 'contact@inbetween.vn'))) }}" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="admin@inbetween.vn">
                <p class="text-xs text-gray-500 mt-1">Khi khách hàng gửi form trên website, hệ thống sẽ gửi email báo cáo chi tiết đến địa chỉ này</p>
            </div>
        </div>

        {{-- Toggles --}}
        <div class="mt-5 pt-4 border-t border-emerald-200/60 space-y-3">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="mail_admin_notification_enabled" value="0">
                <input type="checkbox" name="mail_admin_notification_enabled" value="1" {{ setting('mail_admin_notification_enabled', '1') !== '0' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                <span class="text-sm font-medium text-gray-800">Gửi email thông báo cho Quản trị viên khi có yêu cầu liên hệ / form mới</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="mail_auto_reply_enabled" value="0">
                <input type="checkbox" name="mail_auto_reply_enabled" value="1" {{ setting('mail_auto_reply_enabled', '1') !== '0' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                <span class="text-sm font-medium text-gray-800">Tự động gửi email xác nhận &amp; cảm ơn đến khách hàng (nếu khách có nhập email)</span>
            </label>
        </div>
    </div>

    {{-- 3. Customer Survey Mailer Defaults --}}
    <div class="bg-gradient-to-r from-rose-50/80 to-amber-50/80 p-6 rounded-xl border border-rose-200/80">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            3. Cấu Hình Thư Mời &amp; Thông Báo Khảo Sát Khách Hàng (Customer Survey Mailer)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tiêu đề thư mời khảo sát mặc định</label>
                <input type="text" name="survey_mail_subject" value="{{ old('survey_mail_subject', setting('survey_mail_subject', 'Thư mời đánh giá chất lượng dịch vụ & công trình - Workspace')) }}" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Xuất hiện làm tiêu đề mặc định khi Admin mở màn hình gửi email mời khách hàng làm khảo sát</p>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nội dung thư mời mẫu mặc định</label>
                <textarea name="survey_mail_template" rows="4" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 text-sm leading-relaxed">{{ old('survey_mail_template', setting('survey_mail_template', "Kính gửi Quý Khách hàng,\n\nNhằm không ngừng nâng cao chất lượng thi công và dịch vụ bảo hành, Workspace trân trọng kính mời Quý Khách hàng dành ít phút đánh giá sự hài lòng về dự án vừa qua.\n\nÝ kiến đóng góp quý báu của Quý vị là kim chỉ nam giúp chúng tôi hoàn thiện hơn mỗi ngày.\n\nTrân trọng cảm ơn!")) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Hệ thống sẽ tự động ghép nút bấm liên kết bảo mật có token riêng cho khách khi gửi</p>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email nhận thông báo khi có phản hồi khảo sát mới</label>
                <input type="email" name="survey_admin_receiver" value="{{ old('survey_admin_receiver', setting('survey_admin_receiver', setting('admin_email', 'contact@inbetween.vn'))) }}" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 text-sm" placeholder="survey-admin@workspace.vn">
                <p class="text-xs text-gray-500 mt-1">Khi khách hàng hoàn tất gửi bài đánh giá, hệ thống sẽ gửi bảng điểm và nhận xét ngay đến email này</p>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-rose-200/60">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="survey_notify_on_response" value="0">
                <input type="checkbox" name="survey_notify_on_response" value="1" {{ setting('survey_notify_on_response', '1') !== '0' ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded border-gray-300 focus:ring-rose-500">
                <span class="text-sm font-medium text-gray-800">Tự động gửi email thông báo điểm số &amp; chi tiết khi khách nộp bài khảo sát</span>
            </label>
        </div>
    </div>

    {{-- Test Email Section --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm" x-data="{
        targetEmail: '{{ setting('admin_email', 'contact@inbetween.vn') }}',
        loading: false,
        responseMsg: '',
        responseStatus: '',
        sendTest() {
            if (!this.targetEmail) {
                alert('Vui lòng nhập email nhận thử nghiệm');
                return;
            }
            this.loading = true;
            this.responseMsg = '';
            this.responseStatus = '';

            fetch('{{ route('admin.settings.test-email') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: this.targetEmail })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.responseStatus = 'success';
                    this.responseMsg = data.message;
                } else {
                    this.responseStatus = 'error';
                    this.responseMsg = data.message || 'Lỗi gửi email kiểm tra';
                }
            })
            .catch(err => {
                this.loading = false;
                this.responseStatus = 'error';
                this.responseMsg = 'Lỗi kết nối máy chủ: ' + err.message;
            });
        }
    }">
        <h3 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#EC460B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            3. Kiểm Tra Gửi Email (Test SMTP Connection)
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <em>Lưu ý: Bạn nên bấm <strong>"Lưu cấu hình"</strong> trước khi gửi thử để hệ thống lưu lại các thông số mới nhất.</em>
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="email" x-model="targetEmail" placeholder="Nhập địa chỉ email nhận thư test..." class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
            <button type="button" @click="sendTest()" :disabled="loading" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center justify-center gap-2 disabled:opacity-60">
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </template>
                <span x-text="loading ? 'Đang gửi...' : 'Gửi Thử Email Ngay'"></span>
            </button>
        </div>

        <template x-if="responseStatus === 'success'">
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm flex items-start gap-2">
                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <div x-text="responseMsg"></div>
            </div>
        </template>

        <template x-if="responseStatus === 'error'">
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <div class="space-y-1">
                    <p class="font-semibold">Gửi email kiểm tra thất bại:</p>
                    <p class="font-mono text-xs text-red-700" x-text="responseMsg"></p>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

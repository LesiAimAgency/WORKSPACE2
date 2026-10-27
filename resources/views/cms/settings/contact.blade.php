@extends('cms.settings.template', ['title' => 'Thông tin liên hệ & Công ty'])

@section('form-content')
<div class="space-y-6">
    <!-- Header Thông báo & Nút xem trang thực tế -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i class="fa-solid fa-building text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Quản lý Thông Tin Liên Hệ & Doanh Nghiệp</h3>
                <p class="text-xs text-gray-600">Thông tin cấu hình tại đây sẽ đồng bộ tự động ra trang <span class="font-semibold text-blue-700">/contact</span>, Chân trang (Footer) và Nút liên hệ.</p>
            </div>
        </div>
        <a href="{{ route('contact.page') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-blue-50 text-blue-700 border border-blue-300 rounded-lg text-xs font-semibold shadow-xs transition-all shrink-0">
            <span>Xem trang /contact</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
        </a>
    </div>

    <!-- Hidden sync fields -->
    <input type="hidden" name="company_phone" id="hidden_company_phone" value="{{ old('contact_phone', setting('contact_phone', setting('company_phone', '0988 888 888'))) }}">
    <input type="hidden" name="company_address" id="hidden_company_address" value="{{ old('contact_address', setting('contact_address', setting('company_address', '67 Huỳnh Thiện Lộc, Phường Tân Phú, TP. Hồ Chí Minh, Việt Nam'))) }}">

    <!-- KHỐI 1: Thông tin pháp nhân & Địa chỉ trụ sở -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/90 shadow-2xs space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
            <span class="w-2 h-5 bg-[#EC460B] rounded-full inline-block"></span>
            <h4 class="font-bold text-gray-900 text-sm uppercase tracking-wide">1. Thông tin Doanh Nghiệp &amp; Pháp Lý</h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-800 mb-1">Tên Công Ty / Doanh Nghiệp *</label>
                <input type="text" name="company_name" 
                       value="{{ old('company_name', setting('company_name', 'CÔNG TY TNHH ĐẠI PHÚC 68')) }}" 
                       placeholder="CÔNG TY TNHH ĐẠI PHÚC 68" 
                       required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-[11px] text-gray-500 mt-1">Hiển thị trong phần thông tin pháp lý của trang liên hệ, chân trang và hoá đơn chứng từ.</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-800 mb-1">Địa Chỉ Đầy Đủ Trụ Sở Chính *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </span>
                    <input type="text" name="contact_address" id="contact_address_input"
                           value="{{ old('contact_address', setting('contact_address', setting('company_address', '67 Huỳnh Thiện Lộc, Phường Tân Phú, TP. Hồ Chí Minh, Việt Nam'))) }}" 
                           placeholder="67 Huỳnh Thiện Lộc, Phường Tân Phú, TP. Hồ Chí Minh, Việt Nam" 
                           required
                           oninput="document.getElementById('hidden_company_address').value = this.value"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-900 focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Hỗ trợ tính năng sao chép 1 chạm và hiển thị xuyên suốt trang chủ, chân trang, trang liên hệ.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Mã Số Thuế (MST)</label>
                <input type="text" name="tax_code" 
                       value="{{ old('tax_code', setting('tax_code', '0000999988')) }}" 
                       placeholder="0000999988" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-gray-500 mt-1">Người xem có thể bấm sao chép nhanh mã số thuế.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Khẩu Hiệu / Slogan Tiêu Đề (Trang Liên Hệ) *</label>
                <input type="text" name="contact_slogan" 
                       value="{{ old('contact_slogan', setting('contact_slogan', "XÂY DỰNG GIÁ TRỊ\nKIẾN TẠO TƯƠNG LAI")) }}" 
                       placeholder="XÂY DỰNG GIÁ TRỊ" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-gray-500 mt-1">Dòng chữ lớn ở đầu trang liên hệ.</p>
            </div>
        </div>
    </div>

    <!-- KHỐI 2: Kênh Liên Hệ Trực Tiếp & Thời Gian Hoạt Động -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/90 shadow-2xs space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
            <span class="w-2 h-5 bg-blue-600 rounded-full inline-block"></span>
            <h4 class="font-bold text-gray-900 text-sm uppercase tracking-wide">2. Kênh Liên Hệ Trực Tiếp &amp; Giờ Hoạt Động</h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Hotline / Số Điện Thoại Tư Vấn *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-phone text-xs"></i>
                    </span>
                    <input type="text" name="contact_phone" id="contact_phone_input"
                           value="{{ old('contact_phone', setting('contact_phone', setting('company_phone', '0988 888 888'))) }}" 
                           placeholder="0988 888 888" 
                           required
                           oninput="document.getElementById('hidden_company_phone').value = this.value"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-900 focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Dùng để khách bấm gọi trực tiếp trên trang liên hệ và chân trang.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Email Nhận Báo Giá / Liên Hệ *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </span>
                    <input type="email" name="contact_email" 
                           value="{{ old('contact_email', setting('contact_email', 'contact@daiphuc68.vn')) }}" 
                           placeholder="contact@daiphuc68.vn" 
                           required
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Email chính thức để khách hàng gửi thư hoặc nhận thông báo yêu cầu.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Thời Gian Hoạt Động / Giờ Làm Việc</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-clock text-xs"></i>
                    </span>
                    <input type="text" name="contact_working_hours" 
                           value="{{ old('contact_working_hours', setting('contact_working_hours', 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật: Nghỉ)')) }}" 
                           placeholder="Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật: Nghỉ)" 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Giúp đối tác và khách hàng nắm rõ thời gian tiếp nhận yêu cầu.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Nút Kêu Gọi Báo Giá (CTA)</label>
                <input type="text" name="contact_quote_cta" 
                       value="{{ old('contact_quote_cta', setting('contact_quote_cta', 'GỬI YÊU CẦU BÁO GIÁ')) }}" 
                       placeholder="GỬI YÊU CẦU BÁO GIÁ" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-gray-500 mt-1">Tiêu đề nút mở Modal Box điền yêu cầu báo giá.</p>
            </div>
        </div>
    </div>
</div>
@endsection

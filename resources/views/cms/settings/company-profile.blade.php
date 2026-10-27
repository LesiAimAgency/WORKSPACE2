@extends('cms.settings.template', ['title' => 'Cấu hình Hồ Sơ Năng Lực (Heyzine Flipbook)'])

@section('form-content')
<div class="space-y-6">
    <!-- Header Thông báo & Nút xem trang thực tế -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i class="fa-solid fa-book-open-reader text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Quản lý Hồ Sơ Năng Lực &amp; Heyzine Flipbook</h3>
                <p class="text-xs text-gray-600">Cấu hình mã nhúng iframe Heyzine hoặc đường dẫn xem hồ sơ năng lực tại trang <span class="font-semibold text-blue-700">/ho-so-nang-luc</span> và nút trên Hero Banner.</p>
            </div>
        </div>
        <a href="{{ route('company.profile') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-blue-50 text-blue-700 border border-blue-300 rounded-lg text-xs font-semibold shadow-xs transition-all shrink-0">
            <span>Xem trang /ho-so-nang-luc</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
        </a>
    </div>

    <!-- KHỐI 1: Mã Nhúng Heyzine Flipbook (iFrame / Link) -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/90 shadow-2xs space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
            <span class="w-2 h-5 bg-blue-600 rounded-full inline-block"></span>
            <h4 class="font-bold text-gray-900 text-sm uppercase tracking-wide">1. Cấu Hình Mã Nhúng iFrame / Link Heyzine</h4>
        </div>

        <div class="space-y-3 pt-1">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">
                    Mã nhúng iFrame Heyzine hoặc Link Flipbook *
                </label>
                <textarea name="company_profile_heyzine_embed" 
                          id="company_profile_heyzine_embed"
                          rows="4" 
                          placeholder='<iframe allowfullscreen="allowfullscreen" scrolling="no" class="fp-iframe" src="https://heyzine.com/flip-book/xxxxx.html" style="border: 1px solid lightgray; width: 100%; height: 600px;"></iframe>'
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('company_profile_heyzine_embed', setting('company_profile_heyzine_embed', setting('heyzine_embed', ''))) }}</textarea>
                
                <div class="mt-2 text-xs text-gray-500 space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="font-semibold text-gray-700"><i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Hướng dẫn nhập:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-gray-600 pl-1">
                        <li><strong>Cách 1 (Khuyên dùng):</strong> Dán mã <code>&lt;iframe ...&gt;&lt;/iframe&gt;</code> được cung cấp bởi Heyzine sau khi bạn tải PDF lên <a href="https://heyzine.com" target="_blank" class="text-blue-600 underline">heyzine.com</a>.</li>
                        <li><strong>Cách 2:</strong> Dán trực tiếp đường dẫn dạng <code>https://heyzine.com/flip-book/xxxxx.html</code>.</li>
                        <li>Hệ thống sẽ tự động tối ưu hiển thị responsive toàn màn hình, hỗ trợ chế độ lật sách 3D và phóng to.</li>
                    </ul>
                </div>
            </div>

            <!-- Nút kiểm tra xem thử (Preview) -->
            <div class="pt-2">
                <button type="button" 
                        id="btn-preview-heyzine"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                    <i class="fa-solid fa-eye text-xs"></i>
                    <span>Xem thử khung Heyzine bên dưới</span>
                </button>
            </div>

            <!-- Khung xem thử (Live Preview) -->
            <div id="heyzine-preview-container" class="mt-4 hidden p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-200">
                    <span class="text-xs font-bold text-gray-700 uppercase">Khung Xem Thử Trực Tiếp (Live Preview)</span>
                    <button type="button" id="btn-close-preview" class="text-xs text-gray-500 hover:text-gray-800">Đóng</button>
                </div>
                <div id="heyzine-preview-frame" class="w-full min-h-[400px] flex items-center justify-center bg-white rounded-lg border overflow-hidden">
                    <span class="text-xs text-gray-400">Đang tải...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KHỐI 2: File PDF & Thông Tin Hiển Thị -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/90 shadow-2xs space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
            <span class="w-2 h-5 bg-[#EC460B] rounded-full inline-block"></span>
            <h4 class="font-bold text-gray-900 text-sm uppercase tracking-wide">2. Đường Dẫn File PDF Gốc &amp; Tiêu Đề Trang</h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-800 mb-1">Đường dẫn file PDF hồ sơ năng lực (Nút Tải về)</label>
                <input type="text" name="company_profile_pdf" 
                       value="{{ old('company_profile_pdf', setting('company_profile_pdf', 'themes/daiphuc68/assets/ho-so-nang-luc.pdf')) }}" 
                       placeholder="themes/daiphuc68/assets/ho-so-nang-luc.pdf hoặc https://..." 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-gray-500 mt-1">Đường dẫn file khi khách bấm nút "Tải File PDF Gốc" trên trang hồ sơ năng lực.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Tiêu Đề Trang (SEO &amp; Header)</label>
                <input type="text" name="company_profile_title" 
                       value="{{ old('company_profile_title', setting('company_profile_title', 'HỒ SƠ NĂNG LỰC - CÔNG TY TNHH ĐẠI PHÚC 68')) }}" 
                       placeholder="HỒ SƠ NĂNG LỰC - CÔNG TY TNHH ĐẠI PHÚC 68" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1">Đoạn Mô Tả Ngắn</label>
                <input type="text" name="company_profile_desc" 
                       value="{{ old('company_profile_desc', setting('company_profile_desc', 'Xem trực tuyến tài liệu hồ sơ năng lực, chứng chỉ xuất xưởng, năng lực thi công và cung cấp vật tư thiết bị điện Đại Phúc 68.')) }}" 
                       placeholder="Xem trực tuyến tài liệu hồ sơ năng lực Đại Phúc 68..." 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('btn-preview-heyzine');
    const closeBtn = document.getElementById('btn-close-preview');
    const container = document.getElementById('heyzine-preview-container');
    const frame = document.getElementById('heyzine-preview-frame');
    const textarea = document.getElementById('company_profile_heyzine_embed');

    if (previewBtn && container && frame && textarea) {
        previewBtn.addEventListener('click', function() {
            const raw = textarea.value.trim();
            if (!raw) {
                alert('Vui lòng dán mã nhúng iframe hoặc link Heyzine trước!');
                return;
            }

            container.classList.remove('hidden');
            let src = '';
            if (raw.startsWith('http')) {
                src = raw;
            } else {
                const match = raw.match(/src=["']([^"']+)["']/i);
                if (match) {
                    src = match[1];
                }
            }

            if (src) {
                frame.innerHTML = `<iframe src="${src}" allowfullscreen style="width: 100%; height: 500px; border: none; border-radius: 8px;"></iframe>`;
            } else if (raw.includes('<iframe')) {
                frame.innerHTML = raw;
                const ifr = frame.querySelector('iframe');
                if (ifr) {
                    ifr.style.width = '100%';
                    ifr.style.height = '500px';
                }
            } else {
                frame.innerHTML = `<div class="p-4 text-center text-sm text-amber-600">Định dạng chưa đúng, vui lòng kiểm tra lại mã nhúng hoặc link.</div>`;
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                container.classList.add('hidden');
                frame.innerHTML = '';
            });
        }
    }
});
</script>
@endsection

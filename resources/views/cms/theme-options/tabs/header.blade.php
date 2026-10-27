@php
    $headerStyles = [
        'style-1' => [
            'label' => '1. Modern Light Header', 
            'desc' => 'Logo bên trái, Menu điều hướng căn giữa, Icon giỏ hàng & tài khoản bên phải',
            'badge' => 'Tailwind UI Standard',
            'preview' => '<div class="flex justify-between items-center p-2.5 bg-white rounded-xl border border-gray-200 text-xs text-gray-700 font-medium">
                <span class="font-bold text-blue-600">LOGO</span>
                <span class="space-x-1"><span>Trang chủ</span><span>Sản phẩm</span></span>
                <div class="flex items-center gap-1 text-gray-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>'
        ],
        'style-2' => [
            'label' => '2. Centered Stacked Header', 
            'desc' => 'Logo thương hiệu căn chính giữa nổi bật, danh mục menu được xếp thanh bên dưới',
            'badge' => 'Tailwind Centered',
            'preview' => '<div class="p-2.5 bg-white rounded-xl border border-gray-200 text-xs text-gray-700 text-center space-y-1">
                <div class="font-bold text-gray-900">BRAND LOGO</div>
                <div class="border-t pt-1 space-x-2 text-[11px] text-gray-500"><span>Trang chủ</span><span>Sản phẩm</span><span>Liên hệ</span></div>
            </div>'
        ],
        'style-3' => [
            'label' => '3. Minimal Search Header', 
            'desc' => 'Thanh tìm kiếm mở rộng trực tiếp ở trung tâm, thiết kế tối giản tinh tế',
            'badge' => 'Tailwind Search Bar',
            'preview' => '<div class="flex justify-between items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 text-xs text-gray-700">
                <span class="font-bold text-gray-800">LOGO</span>
                <div class="flex-1 bg-gray-100 border rounded-lg px-2 py-0.5 text-[11px] text-gray-400">Nhập từ khóa tìm kiếm...</div>
                <div><svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            </div>'
        ],
        'style-4' => [
            'label' => '4. Glassmorphism Sticky Header', 
            'desc' => 'Khung kính mờ xuyên thấu dính cố định trên đầu trang khi cuộn (Backdrop Blur)',
            'badge' => 'Tailwind Glass',
            'preview' => '<div class="flex justify-between items-center p-2.5 bg-white/80 backdrop-blur-md rounded-xl border border-blue-200 text-xs text-gray-800 shadow-xs">
                <span class="font-bold text-blue-600">GLASS</span>
                <span class="space-x-1 font-medium"><span>Menu</span><span>Cửa hàng</span></span>
                <div class="flex items-center gap-1 text-blue-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>'
        ],
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span>Cấu hình Header (Chuẩn Tailwind UI)</span>
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">4 Mẫu UI</span>
            </h3>
            <p class="text-sm text-gray-500 mt-1">Lựa chọn các kiểu Header giao diện chuẩn Tailwind CSS nhẹ nhàng, responsive mượt mà</p>
        </div>
        
        @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
        <a href="{{ $isProject ? route('project.admin.website-config.index', $projectCode) : route('cms.website-config.index') }}?tab=header" 
           class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
            <span>Mở Cấu Hình Chi Tiết</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php $selectedHeader = $data['header_style'] ?? 'style-1'; @endphp
        @foreach($headerStyles as $key => $header)
        <label class="header-option block p-5 border-2 rounded-2xl cursor-pointer transition-all duration-200 relative {{ $selectedHeader === $key ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
            <input type="radio" name="header_style" value="{{ $key }}" 
                   {{ $selectedHeader === $key ? 'checked' : '' }} class="hidden header-radio">
            
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-gray-800 text-sm">{{ $header['label'] }}</span>
                <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $header['badge'] }}</span>
            </div>
            
            <p class="text-xs text-gray-500 mb-3">{{ $header['desc'] }}</p>
            
            <div class="mt-2">
                {!! $header['preview'] !!}
            </div>
        </label>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.header-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.header-option').forEach(opt => {
                opt.classList.remove('border-blue-600', 'bg-blue-50/50', 'shadow-md', 'ring-2', 'ring-blue-200');
                opt.classList.add('border-gray-200', 'bg-white');
            });
            
            const label = this.closest('.header-option');
            label.classList.remove('border-gray-200', 'bg-white');
            label.classList.add('border-blue-600', 'bg-blue-50/50', 'shadow-md', 'ring-2', 'ring-blue-200');
        });
    });
});
</script>

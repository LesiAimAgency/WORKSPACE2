@extends('frontend.layouts.product-layout')

@section('page-title', 'Máy lọc nước biển, cs 377l/h')

@section('product-content')
<div class="container mx-auto">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6 font-medium">
        <a href="#" class="hover:text-blue-600 transition">Trang chủ</a> / 
        <a href="#" class="hover:text-blue-600 transition">Sản phẩm</a> / 
        <a href="#" class="hover:text-blue-600 transition">Máy lọc nước biển</a> / 
        <span class="text-gray-900">CS 377L/h</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16 mb-16">
        <!-- Product Images -->
        <div class="product-gallery flex flex-col gap-4">
            <div class="main-image relative rounded-2xl overflow-hidden bg-gray-50 aspect-[4/3] flex items-center justify-center border border-gray-100 shadow-sm group">
                <img src="https://xulynuoctrungdieptin.com/uploads/source/may-loc-nuoc-bien/he-thong-loc-nuoc-bien-trung-diep-tin.png" alt="Máy lọc nước biển cs 377l/h" class="w-full h-full object-contain p-8 transform transition duration-500 group-hover:scale-110">
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full shadow-md uppercase tracking-wide">Mới</span>
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-md uppercase tracking-wide">ISO 9001:2015</span>
                </div>
            </div> 
            
            <!-- Thumbnails -->
            <div class="grid grid-cols-4 gap-4">
                <button class="rounded-xl overflow-hidden border-2 border-blue-600 aspect-square bg-gray-50 p-2">
                    <img src="https://xulynuoctrungdieptin.com/uploads/source/may-loc-nuoc-bien/he-thong-loc-nuoc-bien-trung-diep-tin.png" class="w-full h-full object-contain" alt="Thumb 1">
                </button>
                <button class="rounded-xl overflow-hidden border border-gray-200 hover:border-blue-400 transition aspect-square bg-gray-50 p-2">
                    <img src="https://xulynuoctrungdieptin.com/uploads/source/may-loc-nuoc-bien/he-thong-loc-nuoc-bien-trung-diep-tin.png" class="w-full h-full object-contain opacity-70 hover:opacity-100" alt="Thumb 2">
                </button>
            </div>
        </div>

        <!-- Product Info -->
        <div class="product-info flex flex-col justify-start">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex text-yellow-400 text-sm">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <span class="text-sm text-gray-500">(8 đánh giá)</span>
                <span class="px-2 text-gray-300">|</span>
                <span class="text-sm text-green-600 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                    Tình trạng: Hàng có sẵn
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">Máy lọc nước biển, công suất 377L/h</h1>
            
            <div class="text-3xl font-bold text-blue-700 mb-6 flex items-end gap-2">
                Liên hệ nhận báo giá
            </div>
            
            <div class="prose prose-sm text-gray-600 mb-8 pb-8 border-b border-gray-100">
                <p class="leading-relaxed">Máy lọc nước biển công suất 377L/h là giải pháp tối ưu cung cấp nước ngọt cho tàu thuyền, giàn khoan dầu khí và khu vực hải đảo xa bờ. Với công nghệ RO khử mặn hiệu quả, thiết kế nhỏ gọn và độ bền cao, sản phẩm đáp ứng tốt nhu cầu xử lý nước trong môi trường biển khắc nghiệt.</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                        <span><strong>Công nghệ:</strong> Thẩm thấu ngược RO màng lọc chuyên dụng cho nước biển.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                        <span><strong>Ứng dụng:</strong> Tàu cá, tàu du lịch, giàn khoan, hải đảo.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                        <span><strong>Ưu điểm:</strong> Vận hành êm ái, tiết kiệm điện năng, linh kiện chống ăn mòn.</span>
                    </li>
                </ul>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <button class="flex-1 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-blue-500/30 transform transition hover:-translate-y-1 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    THÊM VÀO GIỎ
                </button>
                <button class="flex-1 bg-white hover:bg-gray-50 text-blue-700 font-bold py-4 px-8 rounded-xl shadow-md border border-gray-200 transform transition hover:-translate-y-1 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    TƯ VẤN MIỄN PHÍ
                </button>
            </div>
            
            <!-- Trust badges -->
            <div class="mt-10 grid grid-cols-3 gap-4 border-t border-gray-100 pt-8">
                <div class="flex flex-col items-center text-center gap-2 group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 uppercase">Chính hãng 100%</span>
                </div>
                <div class="flex flex-col items-center text-center gap-2 group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 uppercase">Thương hiệu uy tín</span>
                </div>
                <div class="flex flex-col items-center text-center gap-2 group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 uppercase">Giao hàng toàn quốc</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="mt-16 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-100 overflow-x-auto hide-scrollbar">
            <button class="px-8 py-5 text-sm font-bold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap bg-blue-50/50">NỘI DUNG CHI TIẾT</button>
            <button class="px-8 py-5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">THÔNG SỐ KỸ THUẬT</button>
            <button class="px-8 py-5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">CHÍNH SÁCH BẢO HÀNH</button>
            <button class="px-8 py-5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">ĐÁNH GIÁ (8)</button>
        </div>
        
        <div class="p-8 lg:p-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Giải pháp nước sạch cho tàu thuyền, dầu khí và hải đảo</h3>
            <div class="prose max-w-none text-gray-700 leading-loose">
                <p>Nhu cầu cấp bách về máy lọc nước tại vùng biển và hải đảo. Ở các khu vực như tàu cá, tàu du lịch, giàn khoan dầu khí và các đảo tiền tiêu, nguồn nước ngọt luôn là vấn đề nan giải. Nước biển dồi dào nhưng không thể dùng trực tiếp cho sinh hoạt hoặc sản xuất. Chính vì vậy, máy lọc nước biển trở thành thiết bị không thể thiếu để chuyển đổi nước mặn thành nước ngọt an toàn và tiết kiệm.</p>
                <p>Trong đó, máy lọc nước biển công suất 377L/h là lựa chọn lý tưởng cho các đơn vị cần nguồn nước ổn định, liên tục và đảm bảo tiêu chuẩn chất lượng.</p>
                
                <h4 class="text-lg font-bold text-gray-900 mt-8 mb-4">Ưu điểm vượt trội của máy lọc nước biển công suất 377L/h</h4>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong>Hiệu suất ổn định, phù hợp nhiều ứng dụng:</strong> Với công suất 377 lít/giờ, máy có thể cung cấp đủ nước sinh hoạt cho các khu vực có nhu cầu trung bình.</li>
                    <li><strong>Công nghệ lọc RO tiên tiến:</strong> Loại bỏ 99.9% muối, vi khuẩn, virus và các kim loại nặng có trong nước biển.</li>
                    <li><strong>Vật liệu chống ăn mòn:</strong> Khung máy và các linh kiện tiếp xúc với nước được làm từ thép không gỉ 316, nhựa uPVC chịu áp lực cao.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('sidebar')
    <!-- Sidebar content updated for water purifier context -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="font-bold text-gray-900 mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg> 
            Danh mục sản phẩm
        </h3>
        <ul class="space-y-3 text-sm text-gray-600">
            <li>
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" checked class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out border-gray-300 rounded">
                    <span class="group-hover:text-blue-600 transition font-medium text-blue-600">Máy lọc nước biển</span>
                </label>
            </li>
            <li>
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out border-gray-300 rounded">
                    <span class="group-hover:text-blue-600 transition">Hệ thống lọc RO công nghiệp</span>
                </label>
            </li>
            <li>
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out border-gray-300 rounded">
                    <span class="group-hover:text-blue-600 transition">Thiết bị xử lý nước mặn</span>
                </label>
            </li>
        </ul>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl shadow-sm p-6 text-white text-center">
        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
        </div>
        <h3 class="font-bold mb-2 text-lg">Cần tư vấn giải pháp?</h3>
        <p class="text-blue-100 text-sm mb-6 opacity-90">Kỹ sư của chúng tôi sẵn sàng khảo sát và tư vấn tận nơi.</p>
        <a href="tel:0901234567" class="block w-full bg-white text-blue-700 font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1">
            090 123 4567
        </a>
    </div>
@endsection

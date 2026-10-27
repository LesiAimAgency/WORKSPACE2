@extends('frontend.layouts.page-layout')

@section('page-title', 'Demo Trang tĩnh')

@section('page-content')
    <h2 class="text-2xl font-bold mb-4">Nội dung trang tĩnh</h2>
    <p class="text-gray-600 mb-4">Đây là đoạn nội dung mẫu dành cho giao diện trang tĩnh. Bạn có thể thay đổi thiết lập Layout trong CMS để xem sự thay đổi.</p>
    <div class="h-64 bg-gray-100 rounded border flex items-center justify-center text-gray-400">
        Khối nội dung chính
    </div>
@endsection

@section('sidebar')
    <div class="mb-6">
        <h3 class="font-bold mb-4 text-lg border-b pb-2">Danh mục trang</h3>
        <ul class="space-y-2 text-gray-600">
            <li><a href="#" class="hover:text-blue-500">Giới thiệu</a></li>
            <li><a href="#" class="hover:text-blue-500">Tầm nhìn sứ mệnh</a></li>
            <li><a href="#" class="hover:text-blue-500">Chính sách bảo mật</a></li>
            <li><a href="#" class="hover:text-blue-500">Tuyển dụng</a></li>
        </ul>
    </div>
    
    <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="font-semibold text-blue-800 mb-2">Hỗ trợ khách hàng</h4>
        <p class="text-sm text-blue-600">Hotline: 1900 1234</p>
    </div>
@endsection

@extends('frontend.layouts.post-layout')

@section('page-title', 'Demo Chi tiết bài viết')

@section('post-content')
    <div class="prose max-w-none">
        <h2 class="text-3xl font-bold mb-4 text-gray-800">Tiêu đề bài viết nổi bật</h2>
        <div class="flex items-center text-sm text-gray-500 mb-8 space-x-4">
            <span>Đăng ngày: 15/05/2026</span>
            <span>Lượt xem: 1,234</span>
            <span>Tác giả: Admin</span>
        </div>
        
        <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800" alt="Post thumbnail" class="w-full h-80 object-cover rounded-lg mb-6">
        
        <p class="text-gray-700 leading-relaxed mb-4">
            Đây là đoạn nội dung mẫu dành cho bài viết. Bạn có thể thay đổi thiết lập Layout của Post trong CMS (Theme Options > Layout) để xem sự thay đổi (Sidebar Trái, Phải hoặc không có Sidebar).
        </p>
        <p class="text-gray-700 leading-relaxed mb-4">
            Dữ liệu này hoàn toàn là mô phỏng để bạn dễ hình dung cách hiển thị cuối cùng của giao diện.
        </p>
    </div>
@endsection

@section('sidebar')
    <div class="mb-8">
        <h3 class="font-bold mb-4 text-lg border-b pb-2">Danh mục tin tức</h3>
        <ul class="space-y-2 text-gray-600">
            <li><a href="#" class="hover:text-blue-500 flex justify-between"><span>Tin mới nhất</span> <span class="bg-gray-200 px-2 rounded text-xs">12</span></a></li>
            <li><a href="#" class="hover:text-blue-500 flex justify-between"><span>Khuyến mãi</span> <span class="bg-gray-200 px-2 rounded text-xs">5</span></a></li>
            <li><a href="#" class="hover:text-blue-500 flex justify-between"><span>Kinh nghiệm</span> <span class="bg-gray-200 px-2 rounded text-xs">8</span></a></li>
        </ul>
    </div>
    
    <div class="mb-8">
        <h3 class="font-bold mb-4 text-lg border-b pb-2">Bài viết nổi bật</h3>
        <div class="space-y-4">
            @for($i=1; $i<=3; $i++)
            <a href="#" class="flex items-center gap-3 group">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=100&h=100&fit=crop" class="w-16 h-16 rounded object-cover">
                <div>
                    <h4 class="text-sm font-semibold group-hover:text-blue-500">Bài viết mẫu số {{ $i }}</h4>
                    <p class="text-xs text-gray-500">12/05/2026</p>
                </div>
            </a>
            @endfor
        </div>
    </div>
@endsection

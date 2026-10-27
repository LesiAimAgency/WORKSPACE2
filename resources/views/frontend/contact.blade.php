<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 py-8">
        <div class="container mx-auto px-4">
            <nav class="text-white text-sm mb-2">
                <a href="/" class="hover:underline">Trang chủ</a> / <span>Liên hệ</span>
            </nav>
            <h1 class="text-4xl font-bold text-white">Liên hệ</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Thông tin liên hệ</h2>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-orange-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold">Địa chỉ</h3>
                            <p class="text-gray-600">123 Đường ABC, Quận 1, TP.HCM</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-orange-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold">Điện thoại</h3>
                            <p class="text-gray-600">0123 456 789</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-orange-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <h3 class="font-bold">Email</h3>
                            <p class="text-gray-600">contact@example.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Gửi tin nhắn</h2>
                <form action="/contact" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Họ tên</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tin nhắn</label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 font-bold">Gửi tin nhắn</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-indigo-600 mb-4">@yield('code')</h1>
            <div class="w-24 h-1 bg-indigo-600 mx-auto mb-8"></div>
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">@yield('title')</h2>
            <p class="text-gray-600 text-lg mb-8">@yield('message')</p>
        </div>

        @if(config('app.debug') && isset($exception))
        <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 text-left rounded-lg">
            <h3 class="text-lg font-semibold text-red-800 mb-2">Debug Information:</h3>
            <p class="text-sm text-red-700 mb-2"><strong>Exception:</strong> {{ get_class($exception) }}</p>
            <p class="text-sm text-red-700 mb-2"><strong>Message:</strong> {{ $exception->getMessage() }}</p>
            <p class="text-sm text-red-700"><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
            <a href="/" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Về trang chủ
            </a>
            <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-lg hover:shadow-xl border-2 border-indigo-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </button>
        </div>
    </div>
</body>
</html>

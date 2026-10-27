<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - {{ $project->name ?? 'Project' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex flex-col items-center">
                    <div class="w-16 h-16 bg-[#001B4E] rounded-2xl flex items-center justify-center mb-3 shadow-lg">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" />
                            <circle cx="12" cy="12" r="4" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-[#001B4E] tracking-wider uppercase mb-1">Aim Agency</h1>
                </div>
                <h2 class="text-lg font-bold text-gray-900 border-t border-gray-100 pt-4">{{ $project->name ?? 'Project Login' }}</h2>
                <p class="text-sm text-gray-600 mt-2">Mã dự án: <span class="font-mono font-semibold text-[#001B4E]">{{ $project->code ?? 'N/A' }}</span></p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('project.login.post', $project->code ?? 'default') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tên đăng nhập</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#001B4E] focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#001B4E] focus:border-transparent transition">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-[#001B4E] focus:ring-[#001B4E]">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</label>
                </div>


                <button type="submit" class="w-full bg-[#001B4E] text-white py-3 rounded-lg hover:bg-[#001235] font-bold tracking-wide transition transform hover:scale-[1.02]">
                    ĐĂNG NHẬP
                </button>
            </form>

         
        </div>
    </div>

</body>
</html>

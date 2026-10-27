<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex w-full">
        <div class="w-64 bg-gradient-to-b from-blue-900 to-blue-700 shadow-2xl fixed h-screen overflow-y-auto">
            <div class="p-6 border-b border-blue-600">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-yellow-400 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h2 class="text-white font-bold">EMPLOYEE</h2>
                        <p class="text-blue-200 text-xs">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6 px-3">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-3 mb-2 text-blue-100 hover:bg-blue-600 rounded-lg {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>

                <a href="{{ route('employee.tasks.index') }}" class="flex items-center px-4 py-3 mb-2 text-blue-100 hover:bg-blue-600 rounded-lg {{ request()->routeIs('employee.tasks.*') ? 'bg-blue-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span class="ml-3 font-medium">Task của tôi</span>
                </a>

                <a href="{{ route('employee.contracts.index') }}" class="flex items-center px-4 py-3 mb-2 text-blue-100 hover:bg-blue-600 rounded-lg {{ request()->routeIs('employee.contracts.*') ? 'bg-blue-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Hợp đồng</span>
                </a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col ml-64">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6 bg-gray-50">
                @if(session('alert'))
                <div class="mb-6 p-4 rounded-lg {{ session('alert.type') === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ session('alert.message') }}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

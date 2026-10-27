<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy danh sách ngôn ngữ từ settings
        $languages = setting('languages', []);
        $availableLocales = collect($languages)->pluck('code')->toArray();
        $defaultLocale = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Kiểm tra locale từ URL (ví dụ: /en/products)
        $locale = $request->segment(1);

        if (in_array($locale, $availableLocales)) {
            // Nếu URL có locale hợp lệ
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Nếu không có locale trong URL, dùng locale từ session hoặc mặc định
            $sessionLocale = Session::get('locale', $defaultLocale);
            if (in_array($sessionLocale, $availableLocales)) {
                App::setLocale($sessionLocale);
            } else {
                App::setLocale($defaultLocale);
                Session::put('locale', $defaultLocale);
            }
        }

        return $next($request);
    }
}

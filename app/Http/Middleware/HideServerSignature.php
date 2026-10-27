<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HideServerSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Ẩn thông tin máy chủ & công nghệ
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('X-Powered-By', 'VGT System');

        // 2. Bảo mật HSTS (HTTP Strict Transport Security)
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https' || config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 3. Security Headers chuẩn
        if (! $response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }
        if (! $response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }
        if (! $response->headers->has('Referrer-Policy')) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // 4. Đồng bộ X-Robots-Tag với chiến lược Indexing (Giải quyết triệt để lỗi X-Robots-Tag: none)
        // Xóa bất kỳ header X-Robots-Tag conflicting từ web server / php runtime
        if (function_exists('header_remove')) {
            @header_remove('X-Robots-Tag');
        }
        $response->headers->remove('X-Robots-Tag');

        $path = $request->path();
        $isPrivatePath = $request->is('admin*', 'superadmin*', 'login*', 'register*', 'cart*', 'checkout*', 'api*', 'livewire*', 'cms*');

        if ($isPrivatePath) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        } else {
            // Kiểm tra nếu nội dung trang HTML có khai báo noindex
            $content = $response->getContent();
            $isHtml = is_string($content) && str_contains($response->headers->get('Content-Type', ''), 'text/html');

            if ($isHtml && str_contains($content, 'name="robots" content="noindex')) {
                $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            } elseif ($isHtml && $response->isOk()) {
                // Trang công khai indexable: Đồng bộ chính xác với HTML meta robots
                $response->headers->set('X-Robots-Tag', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1');
            }
        }

        // 5. Nén GZIP tự động cho phản hồi HTML / JSON (Giảm kích thước tải trang 75-85%)
        if (function_exists('gzencode')
            && $request->headers->has('Accept-Encoding')
            && str_contains((string) $request->header('Accept-Encoding'), 'gzip')
            && ! $response->headers->has('Content-Encoding')
            && $response->isOk()
        ) {
            $contentType = $response->headers->get('Content-Type', '');
            if (str_contains($contentType, 'text/html') || str_contains($contentType, 'application/json')) {
                $rawContent = $response->getContent();
                if (is_string($rawContent) && strlen($rawContent) > 1024) {
                    $compressed = gzencode($rawContent, 6);
                    if ($compressed !== false) {
                        $response->setContent($compressed);
                        $response->headers->set('Content-Encoding', 'gzip');
                        $response->headers->set('Vary', 'Accept-Encoding', false);
                        $response->headers->set('Content-Length', (string) strlen($compressed));
                    }
                }
            }
        }

        return $response;
    }
}

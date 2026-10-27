<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CMSMiddleware;
use App\Http\Middleware\DisableHttpCache;
use App\Http\Middleware\HandleDatabaseErrors;
use App\Http\Middleware\HideServerSignature;
use App\Http\Middleware\LogVisitor;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Ensure finfo class exists even if PHP fileinfo extension is disabled on host server
if (! defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (! class_exists('finfo')) {
    class finfo
    {
        public function __construct(int $flags = 16, ?string $magicFile = null) {}

        public function file(string $filename, int $flags = 16, $context = null): string|false
        {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mimes = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
                'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo', 'mkv' => 'video/x-matroska',
                'pdf' => 'application/pdf', 'txt' => 'text/plain', 'json' => 'application/json',
                'css' => 'text/css', 'js' => 'application/javascript',
            ];

            return $mimes[$ext] ?? 'application/octet-stream';
        }

        public function buffer(string $string, int $flags = 16, $context = null): string|false
        {
            return 'application/octet-stream';
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            DisableHttpCache::class,
            HideServerSignature::class,
            LogVisitor::class,
            HandleDatabaseErrors::class,
        ]);

        // Exclude specific internal preview and upload routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'admin/media/upload',
            'admin/widgets/preview',
            'widgets/preview',
            'contact',
            'inbetween_v2/contact',
            'api/form-submit',
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'admin' => AdminMiddleware::class,
            'cms' => CMSMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Xử lý lỗi database QueryException
        $exceptions->render(function (QueryException $e, $request) {
            // Xử lý lỗi numeric overflow
            if (str_contains($e->getMessage(), 'Out of range value')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Giá trị nhập vào quá lớn! Vui lòng nhập giá không vượt quá 9,999,999,999,999.99 VNĐ.',
                        'message' => 'Validation Error',
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Giá trị nhập vào quá lớn! Vui lòng nhập giá không vượt quá 9,999,999,999,999.99 VNĐ.',
                    ]);
            }

            // Xử lý lỗi duplicate entry
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Dữ liệu đã tồn tại trong hệ thống.',
                        'message' => 'Duplicate Entry Error',
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Dữ liệu đã tồn tại trong hệ thống. Vui lòng kiểm tra lại.',
                    ]);
            }

            // Không xử lý ở đây, để Laravel xử lý mặc định
            return null;
        });

        // Xử lý lỗi CSRF Token Mismatch (419 Page Expired)
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Phiên làm việc đã hết hạn do bạn để trang quá lâu. Vui lòng gửi lại yêu cầu.');
        });

        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
                        'csrf_token' => csrf_token(),
                    ], 419);
                }

                return redirect()->back()
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with('error', 'Phiên làm việc đã hết hạn do bạn để trang quá lâu. Vui lòng gửi lại yêu cầu.');
            }

            return null;
        });
    })->create();

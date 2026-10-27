<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCrudAlerts
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add alerts for successful redirects
        if ($response->isRedirect() && $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            $method = $request->method();
            $route = $request->route();

            if ($route) {
                $routeName = $route->getName();

                // Auto-generate alerts based on route patterns if no alert is already set
                if (! session()->has('alert')) {
                    $message = $this->generateAlertMessage($method, $routeName);
                    if ($message) {
                        session()->flash('alert', [
                            'type' => 'success',
                            'message' => $message,
                        ]);
                    }
                }
            }
        }

        return $response;
    }

    private function generateAlertMessage(string $method, ?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        // Extract resource name from route
        $resourceName = $this->extractResourceName($routeName);

        switch ($method) {
            case 'POST':
                if (str_contains($routeName, '.store')) {
                    return "Thêm {$resourceName} thành công!";
                }
                break;

            case 'PUT':
            case 'PATCH':
                if (str_contains($routeName, '.update')) {
                    return "Cập nhật {$resourceName} thành công!";
                }
                break;

            case 'DELETE':
                if (str_contains($routeName, '.destroy')) {
                    return "Xóa {$resourceName} thành công!";
                }
                break;
        }

        return null;
    }

    private function extractResourceName(string $routeName): string
    {
        $resourceMap = [
            'products' => 'sản phẩm',
            'brands' => 'thương hiệu',
            'categories' => 'danh mục',
            'attributes' => 'thuộc tính',
            'orders' => 'đơn hàng',
            'users' => 'người dùng',
            'posts' => 'bài viết',
            'pages' => 'trang',
            'menus' => 'menu',
            'widgets' => 'widget',
            'settings' => 'cài đặt',
        ];

        foreach ($resourceMap as $key => $value) {
            if (str_contains($routeName, $key)) {
                return $value;
            }
        }

        // Fallback: extract from route name
        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            return $parts[count($parts) - 2];
        }

        return 'mục';
    }
}

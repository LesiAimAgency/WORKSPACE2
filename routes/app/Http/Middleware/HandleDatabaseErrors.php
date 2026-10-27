<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class HandleDatabaseErrors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (QueryException $e) {
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

            // Xử lý lỗi foreign key constraint
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Không thể thực hiện thao tác này do có dữ liệu liên quan.',
                        'message' => 'Foreign Key Constraint Error',
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Không thể thực hiện thao tác này do có dữ liệu liên quan. Vui lòng xóa dữ liệu liên quan trước.',
                    ]);
            }

            // Xử lý các lỗi database khác
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Có lỗi xảy ra với cơ sở dữ liệu.',
                    'message' => 'Database Error',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'error',
                    'message' => 'Có lỗi xảy ra với cơ sở dữ liệu. Vui lòng thử lại sau.',
                ]);
        }
    }
}

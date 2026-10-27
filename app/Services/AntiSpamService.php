<?php

namespace App\Services;

use Illuminate\Http\Request;

class AntiSpamService
{
    /**
     * Vô hiệu hóa hoàn toàn antispam theo yêu cầu người dùng:
     * Bỏ hết mọi ràng buộc, người dùng nhập form là cho qua ngay ("bấm phát ăn luôn").
     */
    public static function verifyCaptcha(Request $request): array
    {
        return [true, null];
    }

    public static function renderHoneypotHtml(): string
    {
        return '';
    }

    public static function checkHoneypot(Request $request): bool
    {
        return false;
    }

    public static function checkTimeGate(Request $request, int $minSeconds = 2): bool
    {
        return false;
    }

    public static function containsLinks(?string $text): bool
    {
        return false;
    }

    public static function containsSpamKeywords(?string $text): bool
    {
        return false;
    }

    public static function isValidPhone(?string $phone): bool
    {
        return true;
    }

    public static function generateTimeToken(): string
    {
        return '';
    }

    public static function inspect(Request $request): array
    {
        return [false, null, false];
    }
}

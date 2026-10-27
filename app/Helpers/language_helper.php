<?php

if (! function_exists('switchLanguageUrl')) {
    /**
     * Tạo URL chuyển đổi ngôn ngữ
     */
    function switchLanguageUrl($locale)
    {
        $currentUrl = request()->url();
        $currentPath = request()->path();

        // Lấy danh sách ngôn ngữ
        $languages = setting('languages', []);
        $availableLocales = collect($languages)->pluck('code')->toArray();
        $defaultLocale = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Kiểm tra xem URL hiện tại có locale không
        $segments = request()->segments();
        $hasLocaleInUrl = ! empty($segments) && in_array($segments[0], $availableLocales);

        if ($hasLocaleInUrl) {
            // Thay thế locale hiện tại
            $segments[0] = $locale;
            $newPath = implode('/', $segments);
        } else {
            // Thêm locale vào đầu
            if ($locale !== $defaultLocale) {
                $newPath = $locale.'/'.$currentPath;
            } else {
                $newPath = $currentPath;
            }
        }

        // Nếu chuyển về ngôn ngữ mặc định, bỏ locale khỏi URL
        if ($locale === $defaultLocale && $hasLocaleInUrl) {
            array_shift($segments); // Bỏ locale đầu tiên
            $newPath = implode('/', $segments);
        }

        $baseUrl = request()->root();
        $queryString = request()->getQueryString();

        $url = $baseUrl.'/'.ltrim($newPath, '/');

        if ($queryString) {
            $url .= '?'.$queryString;
        }

        return $url;
    }
}

if (! function_exists('change_locale_url')) {
    /**
     * Chuyển đổi ngôn ngữ trên URL hiện tại (hỗ trợ cả projectCode và standalone)
     */
    function change_locale_url(string $locale): string
    {
        $locale = strtolower($locale);
        $project = request()->attributes->get('project');
        $projectCode = $project ? $project->code : request()->route('projectCode');

        $defaultLocale = strtolower(setting('default_language', 'vi'));
        $rawLangs = setting('languages', []);
        if (is_string($rawLangs)) {
            $rawLangs = json_decode($rawLangs, true) ?: [];
        }
        $activeCodes = array_map('strtolower', array_column($rawLangs, 'code')) ?: ['vi', 'en', 'zh'];

        $segments = request()->segments();

        // If in project context, segment 0 is projectCode
        $prefix = '';
        if ($projectCode && ! empty($segments) && $segments[0] === $projectCode) {
            $prefix = array_shift($segments);
        }

        // Remove existing locale from segments if present
        if (! empty($segments) && in_array(strtolower($segments[0]), $activeCodes)) {
            array_shift($segments);
        }

        // Add new locale if not default
        if ($locale !== $defaultLocale) {
            array_unshift($segments, $locale);
        }

        // Put back projectCode
        if ($prefix) {
            array_unshift($segments, $prefix);
        }

        $path = '/'.implode('/', $segments);
        $queryString = request()->getQueryString();

        return url($path).($queryString ? '?'.$queryString : '');
    }
}

if (! function_exists('localizedRoute')) {
    /**
     * Tạo route có locale
     */
    function localizedRoute($name, $parameters = [], $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $languages = setting('languages', []);
        $defaultLocale = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        if ($locale !== $defaultLocale) {
            $parameters = array_merge(['locale' => $locale], $parameters);
        }

        return route($name, $parameters);
    }
}

if (! function_exists('__t')) {
    /**
     * Translate với fallback
     */
    function __t($key, $parameters = [], $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        // Thử dịch với locale hiện tại
        $translation = trans($key, $parameters, $locale);

        // Nếu không tìm thấy, thử với locale mặc định
        if ($translation === $key) {
            $languages = setting('languages', []);
            $defaultLocale = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

            if ($locale !== $defaultLocale) {
                $translation = trans($key, $parameters, $defaultLocale);
            }
        }

        return $translation;
    }
}

<?php

if (! function_exists('setting_json')) {
    function setting_json(string $key, array $default = []): array
    {
        $value = setting($key);
        if (empty($value)) {
            return $default;
        }
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true) ?? $default;
    }
}

if (! function_exists('seo_enabled')) {
    function seo_enabled(string $type, string $feature): bool
    {
        $config = setting_json("seo_{$type}_config", []);
        if (isset($config[$feature])) {
            return (bool) $config[$feature];
        }

        return true;
    }
}

if (! function_exists('sanitize_numeric')) {
    function sanitize_numeric($value, $min = null, $max = null)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = filter_var($value, FILTER_VALIDATE_FLOAT);

        if ($numeric === false) {
            return null;
        }

        if ($min !== null && $numeric < $min) {
            return $min;
        }

        if ($max !== null && $numeric > $max) {
            return $max;
        }

        return $numeric;
    }
}

if (! function_exists('sanitize_slug')) {
    function sanitize_slug($slug)
    {
        if ($slug === null || $slug === '') {
            return null;
        }

        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);

        return mb_substr($sanitized, 0, 255);
    }
}

if (! function_exists('sanitize_user_input')) {
    function sanitize_user_input($input)
    {
        if ($input === null) {
            return null;
        }

        return trim(strip_tags($input));
    }
}

if (! function_exists('sanitize_search_query')) {
    function sanitize_search_query($query)
    {
        if ($query === null || $query === '') {
            return null;
        }

        $sanitized = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $query);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return mb_substr($sanitized, 0, 200);
    }
}

if (! function_exists('sanitize_json_data')) {
    function sanitize_json_data($data)
    {
        if (is_array($data)) {
            return array_map('sanitize_json_data', $data);
        }

        if (is_string($data)) {
            return sanitize_user_input($data);
        }

        return $data;
    }
}

if (! function_exists('Lang')) {
    function Lang($key, $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: app()->getLocale();

        $frontendKey = "frontend.{$key}";
        if (trans()->has($frontendKey, $locale)) {
            return trans($frontendKey, $replace, $locale);
        }

        $commonKey = "common.{$key}";
        if (trans()->has($commonKey, $locale)) {
            return trans($commonKey, $replace, $locale);
        }

        if (trans()->has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        if ($fallback && $locale !== 'vi') {
            return Lang($key, $replace, 'vi', false);
        }

        return $key;
    }
}

if (! function_exists('__f')) {
    function __f($key, $replace = [], $locale = null)
    {
        return Lang($key, $replace, $locale);
    }
}

if (! function_exists('translate_price_preset')) {
    function translate_price_preset($label)
    {
        $normalizedLabel = trim($label);

        $presetMap = [
            'Dưới 200k' => __f('preset_under_200k'),
            '200k – 500k' => __f('preset_200k_500k'),
            '500k – 1tr' => __f('preset_500k_1m'),
            'Trên 1tr' => __f('preset_over_1m'),
            'Dưới 500k' => __f('preset_under_500k'),
            '200k - 500k' => __f('preset_200k_500k'),
            '500k - 1tr' => __f('preset_500k_1m'),
            '1tr – 2tr' => __f('preset_1m_2m'),
            '1tr - 2tr' => __f('preset_1m_2m'),
            '2tr – 5tr' => __f('preset_2m_5m'),
            '2tr - 5tr' => __f('preset_2m_5m'),
            'Trên 2tr' => __f('preset_over_2m'),
            'Trên 5tr' => __f('preset_over_5m'),
            'Under 200k' => __f('preset_under_200k'),
            'Under 500k' => __f('preset_under_500k'),
            '$200k – $500k' => __f('preset_200k_500k'),
            '$500k – $1M' => __f('preset_500k_1m'),
            '$1M – $2M' => __f('preset_1m_2m'),
            '$2M – $5M' => __f('preset_2m_5m'),
            'Over $1M' => __f('preset_over_1m'),
            'Over $2M' => __f('preset_over_2m'),
            'Over $5M' => __f('preset_over_5m'),
        ];

        return $presetMap[$normalizedLabel] ?? $normalizedLabel;
    }
}

if (! function_exists('honeypot_fields')) {
    function honeypot_fields(): string
    {
        $fields = [
            'website' => 'Website',
            'url' => 'URL',
            'homepage' => 'Homepage',
            'phone_number' => 'Phone Number',
        ];

        $html = '';
        foreach ($fields as $name => $label) {
            $html .= '<div style="position:absolute;left:-9999px;top:-9999px;visibility:hidden;">';
            $html .= '<label for="'.$name.'">'.$label.'</label>';
            $html .= '<input type="text" name="'.$name.'" id="'.$name.'" value="" autocomplete="off" tabindex="-1">';
            $html .= '</div>';
        }

        return $html;
    }
}

if (! function_exists('form_timestamp')) {
    function form_timestamp(): string
    {
        return '<input type="hidden" name="_form_start_time" value="'.time().'">';
    }
}

if (! function_exists('simple_captcha')) {
    function simple_captcha(): array
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $answer = $num1 + $num2;

        session(['captcha_answer' => $answer]);

        return [
            'question' => "What is {$num1} + {$num2}?",
            'field' => '<input type="number" name="captcha_answer" required placeholder="Enter answer" class="form-control" style="width:100px;display:inline-block;">',
        ];
    }
}

if (! function_exists('recaptcha_field')) {
    function recaptcha_field(): string
    {
        $siteKey = function_exists('setting') ? setting('recaptcha_site_key') : null;

        if (! $siteKey) {
            return '<!-- reCAPTCHA not configured -->';
        }

        return '<div class="g-recaptcha" data-sitekey="'.$siteKey.'"></div>';
    }
}

if (! function_exists('hcaptcha_field')) {
    function hcaptcha_field(): string
    {
        $siteKey = function_exists('setting') ? setting('hcaptcha_site_key') : null;

        if (! $siteKey) {
            return '<!-- hCaptcha not configured -->';
        }

        return '<div class="h-captcha" data-sitekey="'.$siteKey.'"></div>';
    }
}

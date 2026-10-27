<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CaptchaService
{
    /**
     * Google reCAPTCHA verification endpoint.
     */
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Get Site Key (Supports project/tenant setting fallback to global config).
     */
    public function getSiteKey(): ?string
    {
        $projectKey = function_exists('setting') ? setting('recaptcha_site_key') : null;

        $key = ! empty($projectKey) ? (string) $projectKey : (string) config('services.recaptcha.site_key');

        return ! empty($key) ? trim($key) : null;
    }

    /**
     * Get Secret Key (Supports project/tenant setting fallback to global config).
     * Server-side only! Never expose to client.
     */
    protected function getSecretKey(): ?string
    {
        $projectSecret = function_exists('setting') ? setting('recaptcha_secret_key') : null;

        $secret = ! empty($projectSecret) ? (string) $projectSecret : (string) config('services.recaptcha.secret_key');

        return ! empty($secret) ? trim($secret) : null;
    }

    /**
     * Determine if CAPTCHA is enabled.
     */
    public function isEnabled(): bool
    {
        $projectEnabled = function_exists('setting') ? setting('recaptcha_enabled') : null;
        if ($projectEnabled !== null && $projectEnabled !== '') {
            return $projectEnabled === '1' || $projectEnabled === true || $projectEnabled === 'true';
        }

        return (bool) config('services.recaptcha.enabled', true);
    }

    /**
     * Verify reCAPTCHA token with Google server.
     *
     * Note: Per security checklist, Secret Key and response token are NEVER logged.
     */
    public function verify(?string $token, ?string $ip = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $secretKey = $this->getSecretKey();
        if (empty($secretKey)) {
            return true;
        }

        $endpoints = [
            self::VERIFY_URL,
            'https://www.recaptcha.net/recaptcha/api/siteverify',
        ];

        $payload = [
            'secret' => $secretKey,
            'response' => $token,
        ];

        if (! empty($ip)) {
            $payload['remoteip'] = $ip;
        }

        $verifySsl = ! app()->environment('local') && ! empty(ini_get('curl.cainfo')) && file_exists((string) ini_get('curl.cainfo'));

        foreach ($endpoints as $endpoint) {
            try {
                $http = Http::asForm()->timeout(6);
                if (! $verifySsl) {
                    $http = $http->withoutVerifying();
                }

                $response = $http->withOptions([
                    'force_ip_resolve' => 'v4',
                ])->post($endpoint, $payload);

                if ($response->successful()) {
                    $data = $response->json();

                    return isset($data['success']) && $data['success'] === true;
                }
            } catch (\Throwable) {
                // Continue to next mirror endpoint
            }
        }

        return false;
    }
}

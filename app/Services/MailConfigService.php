<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\CustomerAutoReplyMail;
use App\Mail\FormSubmissionAdminMail;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailConfigService
{
    /**
     * Apply runtime mail configuration from DB settings / env
     */
    public static function applyConfig(): void
    {
        $host = setting('mail_host') ?: env('MAIL_HOST');
        $port = setting('mail_port') ?: env('MAIL_PORT', 587);
        $encryption = setting('mail_encryption') ?: env('MAIL_ENCRYPTION', 'tls');
        $username = setting('mail_username') ?: env('MAIL_USERNAME');
        $password = setting('mail_password') ?: env('MAIL_PASSWORD');
        $fromAddress = setting('mail_from_address') ?: env('MAIL_FROM_ADDRESS', 'contact@inbetween.vn');
        $fromName = setting('mail_from_name') ?: env('MAIL_FROM_NAME', 'in • between');

        // If credentials exist, configure SMTP
        if (! empty($host) && ! empty($username)) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', (string) $host);
            Config::set('mail.mailers.smtp.port', (int) $port);
            Config::set('mail.mailers.smtp.encryption', $encryption === 'none' ? null : (string) $encryption);
            Config::set('mail.mailers.smtp.username', (string) $username);
            Config::set('mail.mailers.smtp.password', (string) $password);
            Config::set('mail.mailers.smtp.timeout', 5);
        } else {
            // Fallback to configured default (e.g. log or env)
            Config::set('mail.default', env('MAIL_MAILER', 'log'));
        }

        if (! empty($fromAddress)) {
            Config::set('mail.from.address', (string) $fromAddress);
            Config::set('mail.from.name', (string) $fromName);
        }

        // Reset the mailer manager instance in container so fresh config takes effect
        app()->forgetInstance('mail.manager');
        app()->forgetInstance('mailer');
    }

    /**
     * Get the configured admin receiver email
     */
    public static function getAdminReceiverEmail(): string
    {
        return (string) (
            setting('admin_email') 
            ?: setting('mail_admin_receiver') 
            ?: env('MAIL_TO_ADDRESS') 
            ?: setting('mail_from_address') 
            ?: env('MAIL_FROM_ADDRESS', 'contact@inbetween.vn')
        );
    }

    /**
     * Send email notifications for a form submission
     */
    public static function handleFormSubmission(FormSubmission $submission): array
    {
        $results = [
            'admin_sent' => false,
            'customer_sent' => false,
            'errors' => [],
        ];

        try {
            if ($submission->status === 'spam') {
                Log::warning("MailConfigService: Suppressed email dispatch because submission #{$submission->id} is flagged as SPAM.");
                return $results;
            }

            if (\Illuminate\Support\Facades\Cache::get('smtp_sending_limit_exceeded')) {
                Log::warning("MailConfigService: Suppressed email sending because SMTP daily sending limit was previously exceeded.");
                $results['errors'][] = 'SMTP daily limit exceeded';
                return $results;
            }

            self::applyConfig();

            $adminEmail = self::getAdminReceiverEmail();
            $data = is_array($submission->data) ? $submission->data : [];
            $customerEmail = $data['email'] ?? $submission->email ?? null;
            $customerName = $data['fullname'] ?? $data['name'] ?? 'Khách hàng';

            // 1. Send notification to Admin (enabled by default)
            $enableAdminNotification = setting('mail_admin_notification_enabled', '1') !== '0';
            if ($enableAdminNotification && ! empty($adminEmail)) {
                try {
                    Mail::to($adminEmail)->send(new FormSubmissionAdminMail($submission));
                    $results['admin_sent'] = true;
                    Log::info("Admin form notification sent to {$adminEmail} for submission #{$submission->id}");
                } catch (\Throwable $e) {
                    $results['errors'][] = 'Admin mail error: ' . $e->getMessage();
                    Log::error("Failed sending admin form notification: " . $e->getMessage(), [
                        'submission_id' => $submission->id,
                        'admin_email' => $adminEmail,
                    ]);

                    if (str_contains($e->getMessage(), 'Daily user sending limit exceeded') || str_contains($e->getMessage(), '5.4.5')) {
                        \Illuminate\Support\Facades\Cache::put('smtp_sending_limit_exceeded', true, now()->addMinutes(60));
                    }
                }
            }

            // If admin mail failed due to SMTP sending limit or connection error, skip customer email to prevent cascading timeouts
            if (! empty($results['errors'])) {
                Log::warning("MailConfigService: Skipping customer auto-reply due to prior admin notification error.");
                return $results;
            }

            // 2. Send Auto-Reply to Customer if email is provided
            $enableAutoReply = setting('mail_auto_reply_enabled', '1') !== '0';
            if ($enableAutoReply && ! empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($customerEmail)->send(new CustomerAutoReplyMail($submission));
                    $results['customer_sent'] = true;
                    Log::info("Customer auto-reply sent to {$customerEmail} for submission #{$submission->id}");
                } catch (\Throwable $e) {
                    $results['errors'][] = 'Customer mail error: ' . $e->getMessage();
                    Log::error("Failed sending customer auto-reply: " . $e->getMessage(), [
                        'submission_id' => $submission->id,
                        'customer_email' => $customerEmail,
                    ]);

                    if (str_contains($e->getMessage(), 'Daily user sending limit exceeded') || str_contains($e->getMessage(), '5.4.5')) {
                        \Illuminate\Support\Facades\Cache::put('smtp_sending_limit_exceeded', true, now()->addMinutes(60));
                    }
                }
            }
        } catch (\Throwable $e) {
            $results['errors'][] = 'General mail service error: ' . $e->getMessage();
            Log::error("MailConfigService::handleFormSubmission exception: " . $e->getMessage());
        }

        return $results;
    }

    /**
     * Dispatch form submission mail safely in background without blocking response
     */
    public static function dispatchAsync(FormSubmission $submission): void
    {
        try {
            dispatch(new \App\Jobs\SendFormSubmissionMailJob($submission))->afterResponse();
        } catch (\Throwable $e) {
            Log::warning("MailConfigService::dispatchAsync error, falling back to direct send: " . $e->getMessage());
            try {
                self::handleFormSubmission($submission);
            } catch (\Throwable $fallbackEx) {
                Log::warning("MailConfigService::dispatchAsync fallback error: " . $fallbackEx->getMessage());
            }
        }
    }

    /**
     * Send test email to verify SMTP configuration
     */
    public static function sendTestEmail(string $targetEmail): array
    {
        try {
            self::applyConfig();

            $mailer = Config::get('mail.default');
            $host = Config::get('mail.mailers.smtp.host');
            $port = Config::get('mail.mailers.smtp.port');

            // Create a fake test submission
            $testSubmission = new FormSubmission([
                'form_name' => 'Test Email Verification',
                'data' => [
                    'fullname' => 'Kiểm Tra Hệ Thống',
                    'phone' => '0901234567',
                    'email' => $targetEmail,
                    'service' => 'Kiểm tra cấu hình SMTP',
                    'message' => 'Đây là email kiểm tra được gửi từ hệ thống quản trị website in • between. Nếu bạn nhận được email này, cấu hình SMTP của bạn đã hoạt động hoàn hảo!',
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'Test Agent',
                'submitted_at' => now(),
            ]);

            Mail::to($targetEmail)->send(new FormSubmissionAdminMail($testSubmission, true));

            return [
                'success' => true,
                'message' => "Email kiểm tra đã được gửi thành công đến: {$targetEmail} (Mailer: {$mailer}, Host: {$host}:{$port})",
            ];
        } catch (\Throwable $e) {
            Log::error('SMTP Test email failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gửi email thất bại: ' . $e->getMessage(),
            ];
        }
    }
}

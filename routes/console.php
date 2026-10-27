<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email?}', function ($email = null) {
    \Illuminate\Support\Facades\Cache::forget('smtp_sending_limit_exceeded');
    $target = $email ?: \App\Services\MailConfigService::getAdminReceiverEmail();
    $this->info("Testing SMTP sending to: {$target} ...");
    $result = \App\Services\MailConfigService::sendTestEmail($target);
    $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

<?php

namespace App\Console\Commands;

use App\Models\FormSubmission;
use App\Services\MailConfigService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendContactFormMailCommand extends Command
{
    protected $signature = 'contact:send-mail {id : The FormSubmission ID}';
    protected $description = 'Asynchronously send email notifications for a contact form submission';

    public function handle(): int
    {
        $id = (int) $this->argument('id');
        $submission = FormSubmission::find($id);

        if (! $submission) {
            $this->error("Form submission #{$id} not found.");
            return self::FAILURE;
        }

        try {
            MailConfigService::handleFormSubmission($submission);
            $this->info("Mail sent successfully for submission #{$id}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error("SendContactFormMailCommand failed for submission #{$id}: " . $e->getMessage());
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\FormSubmission;
use App\Services\MailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFormSubmissionMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of attempts
     */
    public int $tries = 2;

    /**
     * Timeout for the job in seconds
     */
    public int $timeout = 30;

    /**
     * The form submission instance
     */
    public FormSubmission $submission;

    /**
     * Create a new job instance.
     */
    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (! $this->submission || ! $this->submission->exists) {
                Log::warning('SendFormSubmissionMailJob: Submission record not found or already deleted.');
                return;
            }

            Log::info("SendFormSubmissionMailJob: Processing email notifications for submission #{$this->submission->id}");
            $results = MailConfigService::handleFormSubmission($this->submission);

            Log::info("SendFormSubmissionMailJob: Completed for submission #{$this->submission->id}", [
                'admin_sent' => $results['admin_sent'] ?? false,
                'customer_sent' => $results['customer_sent'] ?? false,
                'errors' => $results['errors'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::error("SendFormSubmissionMailJob failed for submission #{$this->submission->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

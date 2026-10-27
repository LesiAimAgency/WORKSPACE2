<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public FormSubmission $submission;

    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        $data = is_array($this->submission->data) ? $this->submission->data : [];
        $fullname = $data['fullname'] ?? $data['name'] ?? 'Quý khách';
        $siteName = setting('site_name') ?: setting('mail_from_name') ?: 'in • between';

        $subject = "Cảm ơn bạn đã liên hệ với {$siteName}";

        return $this->subject($subject)
            ->view('emails.customer-auto-reply')
            ->with([
                'submission' => $this->submission,
                'data' => $data,
                'fullname' => $fullname,
                'siteName' => $siteName,
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public FormSubmission $submission;
    public bool $isTest;

    public function __construct(FormSubmission $submission, bool $isTest = false)
    {
        $this->submission = $submission;
        $this->isTest = $isTest;
    }

    public function build(): self
    {
        $data = is_array($this->submission->data) ? $this->submission->data : [];
        $fullname = $data['fullname'] ?? $data['name'] ?? 'Khách hàng mới';
        $phone = $data['phone'] ?? '';
        $customerEmail = $data['email'] ?? null;
        $siteName = setting('site_name') ?: setting('mail_from_name') ?: 'in • between';

        $prefix = $this->isTest ? '[TEST SMTP] ' : "[{$siteName}] ";
        $subject = $prefix . "Yêu cầu liên hệ mới từ: {$fullname}" . ($phone ? " ({$phone})" : '');

        $email = $this->subject($subject)
            ->view('emails.form-submission-admin')
            ->with([
                'submission' => $this->submission,
                'data' => $data,
                'fullname' => $fullname,
                'phone' => $phone,
                'customerEmail' => $customerEmail,
                'isTest' => $this->isTest,
                'siteName' => $siteName,
            ]);

        // If customer provided an email, set it as reply-to so admin can reply directly
        if (! empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $email->replyTo($customerEmail, $fullname);
        }

        return $email;
    }
}

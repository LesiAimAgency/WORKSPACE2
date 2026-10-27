<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SurveyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public SurveyInvitation $invitation;
    public string $surveyUrl;
    public ?string $customMessage;
    public string $mailSubject;

    public function __construct(SurveyInvitation $invitation, ?string $customMessage = null, ?string $mailSubject = null)
    {
        $this->invitation = $invitation;
        $this->customMessage = $customMessage;
        $this->mailSubject = $mailSubject ?: setting('survey_mail_subject', 'Thư mời đánh giá chất lượng dịch vụ & công trình - Workspace');
        $this->surveyUrl = route('customer-survey.show', ['invitation' => $invitation->token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.survey-invitation',
            with: [
                'invitation' => $this->invitation,
                'surveyUrl' => $this->surveyUrl,
                'customMessage' => $this->customMessage,
                'subject' => $this->mailSubject,
            ]
        );
    }
}

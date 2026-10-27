<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurveyInvitation extends Model
{
    use HasFactory;

    protected $table = 'survey_invitations';

    protected $fillable = [
        'survey_id',
        'recipient_email',
        'recipient_name',
        'project_name',
        'token',
        'status',
        'attempts',
        'error_message',
        'sent_at',
        'responded_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(SurveyResponse::class, 'invitation_id');
    }

    public function getSurveyUrl(): string
    {
        return route('customer-survey.show', ['invitation' => $this->token]);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'survey_responses';

    protected $fillable = [
        'survey_id',
        'invitation_id',
        'customer_name',
        'project_name',
        'handover_date',
        'pm_name',
        'customer_email',
        'customer_phone',
        'score_a',
        'score_b',
        'nps_score',
        'open_feedback_best',
        'open_feedback_improve',
        'ip_address',
        'user_agent',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'score_a' => 'float',
        'score_b' => 'float',
        'nps_score' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(SurveyInvitation::class, 'invitation_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    public function getNpsCategoryAttribute(): string
    {
        if ($this->nps_score === null) {
            return 'N/A';
        }
        if ($this->nps_score <= 6) {
            return 'Phản đối (Detractor)';
        }
        if ($this->nps_score <= 8) {
            return 'Trung lập (Passive)';
        }
        return 'Quảng bá (Promoter)';
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $table = 'survey_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'question_code',
        'rating_value',
        'text_value',
    ];

    protected $casts = [
        'rating_value' => 'integer',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    public function getNumericValueAttribute(): ?int
    {
        return $this->rating_value;
    }
}

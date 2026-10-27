<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'surveys';

    protected $fillable = [
        'code',
        'title',
        'subtitle',
        'intro_note',
        'thank_you_note',
        'footer_text',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id')->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id')->latest();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(SurveyInvitation::class, 'survey_id')->latest();
    }

    public function getQuestionsBySection(string $section)
    {
        return $this->questions()->where('section', $section)->orderBy('sort_order')->get();
    }
}

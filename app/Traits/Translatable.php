<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Support\Facades\App;

trait Translatable
{
    protected static $translatableFields = [];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        if (! $this->exists || empty($this->getKey())) {
            return (string) ($this->getAttributeFromArray($field) ?? $this->getOriginal($field) ?? '');
        }

        $locale = $locale ?: App::getLocale();

        try {
            $translation = $this->translations()
                ->where('field', $field)
                ->where('locale', $locale)
                ->first();

            return $translation ? $translation->value : ($this->getOriginal($field) ?? $this->getAttributeFromArray($field));
        } catch (\Throwable $e) {
            return (string) ($this->getOriginal($field) ?? $this->getAttributeFromArray($field) ?? '');
        }
    }

    public function setTranslation(string $field, string $value, ?string $locale = null): void
    {
        if (! $this->exists || empty($this->getKey())) {
            $this->attributes[$field] = $value;
            return;
        }

        $locale = $locale ?: App::getLocale();

        try {
            $this->translations()->updateOrCreate([
                'field' => $field,
                'locale' => $locale,
            ], [
                'value' => $value,
            ]);
        } catch (\Throwable $e) {
            $this->attributes[$field] = $value;
        }
    }

    public function getTranslatedAttribute(string $field): string
    {
        if (! $this->exists || empty($this->getKey())) {
            return (string) ($this->getAttributeFromArray($field) ?? $this->getOriginal($field) ?? '');
        }

        try {
            return $this->getTranslation($field) ?: (string) ($this->getOriginal($field) ?: $this->getAttributeFromArray($field) ?: '');
        } catch (\Throwable $e) {
            return (string) ($this->getOriginal($field) ?? $this->getAttributeFromArray($field) ?? '');
        }
    }

    // Magic method để tự động lấy bản dịch
    public function __get($key)
    {
        // Nếu field có trong danh sách translatable
        if (in_array($key, $this->getTranslatableFields())) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::__get($key);
    }

    public function getTranslatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function getAllTranslations(): array
    {
        $translations = [];
        $locales = $this->getAvailableLocales();

        foreach ($locales as $locale) {
            $translations[$locale] = [];
            foreach ($this->getTranslatableFields() as $field) {
                $translations[$locale][$field] = $this->getTranslation($field, $locale);
            }
        }

        return $translations;
    }

    public function saveTranslations(array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, $this->getTranslatableFields()) && ! empty($value)) {
                    if (in_array($field, ['content', 'description', 'short_description', 'excerpt']) && is_string($value) && function_exists('sanitize_editor_html')) {
                        $value = sanitize_editor_html($value);
                    }
                    $this->setTranslation($field, $value, $locale);
                }
            }
        }
    }

    protected function getAvailableLocales(): array
    {
        $languages = setting('languages', []);

        return collect($languages)->pluck('code')->toArray() ?: ['vi', 'en'];
    }
}

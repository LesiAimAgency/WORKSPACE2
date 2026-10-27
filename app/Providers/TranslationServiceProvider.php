<?php

namespace App\Providers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Lang::addNamespace('custom', function ($locale, $group, $key) {
            $translations = setting('translations', []);
            $fullKey = "{$group}.{$key}";

            foreach ($translations as $trans) {
                if ($trans['key'] === $fullKey) {
                    return $trans['values'][$locale] ?? $fullKey;
                }
            }

            return $fullKey;
        });
    }
}

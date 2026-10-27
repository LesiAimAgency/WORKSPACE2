<?php

namespace App\Providers;

use App\Services\ShortcodeService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ShortcodeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ShortcodeService::class, function ($app) {
            return new ShortcodeService;
        });
    }

    public function boot(): void
    {
        // Blade directive: @shortcode('products', ['limit' => 6])
        Blade::directive('shortcode', function ($expression) {
            return "<?php echo shortcode({$expression}); ?>";
        });

        // Blade directive: @shortcodes($content)
        Blade::directive('shortcodes', function ($expression) {
            return "<?php echo parse_shortcodes({$expression}); ?>";
        });

        // Blade directive: @archive('product', $data)
        Blade::directive('archive', function ($expression) {
            return "<?php echo render_archive({$expression}); ?>";
        });

        // Register custom shortcodes from config/shortcodes.php
        $this->registerCustomShortcodes();
    }

    protected function registerCustomShortcodes(): void
    {
        // You can add custom shortcodes here or load from config
        $shortcodeService = $this->app->make(ShortcodeService::class);

        // Example: Register a custom shortcode
        // $shortcodeService->register('my_shortcode', function($attrs) {
        //     return view('shortcodes.my-shortcode', $attrs)->render();
        // });
    }
}

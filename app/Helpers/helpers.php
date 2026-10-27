<?php

use App\Models\Menu;
use App\Models\Project;
use App\Services\Tenancy\ProjectResolver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                set_config($k, $v);
            }

            return true;
        }

        $value = get_config($key, $default);

        // Return arrays for complex settings like watermark
        if (is_array($value)) {
            return $value;
        }

        return $value;
    }
}

if (! function_exists('setting_string')) {
    function setting_string($key, $default = '')
    {
        $value = setting($key, $default);

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return $default;
        }

        if (is_null($value) || $value === '') {
            return $default;
        }

        return (string) $value;
    }
}

if (! function_exists('trans_db')) {
    function trans_db($key, $replace = [], $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translations = setting('translations', []);
        if (is_string($translations)) {
            $translations = json_decode($translations, true) ?: [];
        }

        if (is_array($translations)) {
            foreach ($translations as $trans) {
                if (isset($trans['key']) && $trans['key'] === $key) {
                    $text = $trans['values'][$locale] ?? ($trans['values']['vi'] ?? null);
                    if (! empty($text)) {
                        foreach ($replace as $search => $value) {
                            $text = str_replace(":$search", $value, $text);
                        }

                        return $text;
                    }
                }
            }
        }

        return $key;
    }
}

if (! function_exists('current_project')) {
    function current_project()
    {
        $project = request()->attributes->get('project');
        if ($project instanceof Project) {
            return $project;
        }

        try {
            $resolved = app(ProjectResolver::class)->resolve(request());
            if ($resolved instanceof Project) {
                request()->attributes->set('project', $resolved);

                return $resolved;
            }
        } catch (Throwable $e) {
            // Ignore during DB initialization or testing
        }

        $sess = session('current_project');
        if ($sess instanceof Project) {
            request()->attributes->set('project', $sess);

            return $sess;
        }
        if (is_string($sess) && $sess !== '') {
            try {
                $p = Project::where('code', $sess)->orWhere('id', $sess)->first();
                if ($p) {
                    request()->attributes->set('project', $p);

                    return $p;
                }
            } catch (Throwable $e) {}
        }
        if (session('current_project_id')) {
            try {
                $p = Project::find(session('current_project_id'));
                if ($p) {
                    request()->attributes->set('project', $p);

                    return $p;
                }
            } catch (Throwable $e) {}
        }

        return null;
    }
}

if (! function_exists('can_project')) {
    function can_project($module, $action = 'view')
    {
        $project = current_project();
        if (! $project) {
            return true; // Default admin panel
        }

        return $project->hasPermission($module, $action);
    }
}

if (! function_exists('render_menu')) {
    function render_menu($location = 'header')
    {
        $menu = null;
        try {
            $project = function_exists('current_project') ? current_project() : null;
            $projectId = $project?->id ?? session('current_project_id');

            $query = Menu::where('is_active', true);
            if ($projectId) {
                $query->where('project_id', $projectId);
            }

            // 1. Try finding by location
            $menu = (clone $query)->where('location', $location)
                ->with(['items' => function ($query) {
                    $query->whereNull('parent_id')->with('children')->orderBy('order');
                }])
                ->first();

            // 2. Try finding by slug
            if (! $menu) {
                $menu = (clone $query)->where('slug', $location)
                    ->with(['items' => function ($query) {
                        $query->whereNull('parent_id')->with('children')->orderBy('order');
                    }])
                    ->first();
            }

            // 3. Fallback to global menu if project menu is not configured
            if (! $menu && $projectId) {
                $menu = Menu::whereNull('project_id')
                    ->where('is_active', true)
                    ->where(function ($q) use ($location) {
                        $q->where('location', $location)->orWhere('slug', $location);
                    })
                    ->with(['items' => function ($query) {
                        $query->whereNull('parent_id')->with('children')->orderBy('order');
                    }])
                    ->first();
            }
        } catch (Exception $e) {
            // Bỏ qua lỗi nếu bảng menus không tồn tại
        }

        if (! $menu || $menu->items->isEmpty()) {
            return '';
        }

        return view('components.menu-simple', compact('menu'))->render();
    }
}

if (! function_exists('menu_item_url')) {
    function menu_item_url($item)
    {
        if ($item->url) {
            return $item->url;
        }

        if ($item->linkable) {
            switch ($item->linkable_type) {
                case 'App\\Models\\Post':
                    return route('frontend.page', $item->linkable->slug ?? $item->linkable->id);
                case 'App\\Models\\ProductCategory':
                    return route('frontend.category', $item->linkable->slug ?? $item->linkable->id);
                default:
                    return '#';
            }
        }

        return '#';
    }
}

if (! function_exists('setting_json')) {
    function setting_json(string $key, array $default = []): array
    {
        $value = setting($key);
        if (empty($value)) {
            return $default;
        }
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true) ?? $default;
    }
}

if (! function_exists('seo_enabled')) {
    function seo_enabled(string $type, string $feature): bool
    {
        $config = setting_json("seo_{$type}_config", []);
        if (isset($config[$feature])) {
            return (bool) $config[$feature];
        }

        return true;
    }
}

if (! function_exists('locale_route')) {
    function locale_route($name, $params = [])
    {
        // Ensure it has project prefix if it's not already prefixed
        if (! str_starts_with($name, 'project.')) {
            $name = 'project.'.$name;
        }

        // Clean 1-level SEO URL for Categories and Products: /{projectCode}/{slug} or /{slug}
        $oneLevelRoutes = [
            'project.shop.category', 'shop.category',
            'project.shop.show', 'shop.show',
            'project.products.show', 'products.show',
            'project.product.show', 'product.show',
            'project.frontend.product', 'frontend.product',
        ];

        if (in_array($name, $oneLevelRoutes, true)) {
            if (is_object($params)) {
                $slug = $params->slug ?? ($params->id ?? '');
                $projectCode = $params->project?->code ?? null;
            } elseif (is_array($params)) {
                $slug = $params['slug'] ?? ($params[0] ?? '');
                $projectCode = $params['projectCode'] ?? null;
            } else {
                $slug = (string) $params;
                $projectCode = null;
            }

            $sessProj = session('current_project');
            $sessCode = is_array($sessProj) ? ($sessProj['code'] ?? null) : (is_object($sessProj) ? ($sessProj->code ?? null) : (is_string($sessProj) ? $sessProj : null));

            $projectCode = $projectCode
                ?? (request()->route('projectCode')
                ?? ($sessCode
                ?? (function_exists('current_project') ? current_project()?->code : null)));

            $slug = ltrim((string) $slug, '/');

            return $projectCode ? "/{$projectCode}/{$slug}" : "/{$slug}";
        }

        // Shop index URL: /{projectCode}/cua-hang
        if (in_array($name, ['project.shop.index', 'shop.index'], true)) {
            $sessProj = session('current_project');
            $sessCode = is_array($sessProj) ? ($sessProj['code'] ?? null) : (is_object($sessProj) ? ($sessProj->code ?? null) : (is_string($sessProj) ? $sessProj : null));

            $projectCode = (is_array($params) && isset($params['projectCode']))
                ? $params['projectCode']
                : (request()->route('projectCode')
                ?? ($sessCode
                ?? (function_exists('current_project') ? current_project()?->code : null)));

            return $projectCode ? "/{$projectCode}/cua-hang" : '/cua-hang';
        }

        $aliases = [
            'project.admin.media.index' => 'project.admin.media.list',
            'admin.media.index' => 'project.admin.media.list',
            'project.admin.media.list' => 'project.admin.media.list',
            'admin.media.list' => 'project.admin.media.list',
            'project.admin.media.store' => 'project.admin.media.upload',
            'admin.media.store' => 'project.admin.media.upload',
            'project.admin.media.upload' => 'project.admin.media.upload',
            'admin.media.upload' => 'project.admin.media.upload',
            'project.admin.media.create-folder' => 'project.admin.media.folder.create',
            'admin.media.create-folder' => 'project.admin.media.folder.create',
            'project.admin.media.delete-folder' => 'project.admin.media.folder.delete',
            'admin.media.delete-folder' => 'project.admin.media.folder.delete',
            'project.admin.media.picker' => 'project.admin.media.list',
            'admin.media.picker' => 'project.admin.media.list',
            'project.admin.media.move' => 'project.admin.media.move',
            'admin.media.move' => 'project.admin.media.move',
            'project.admin.media.destroy' => 'project.admin.media.destroy',
            'admin.media.destroy' => 'project.admin.media.destroy',
            'project.admin.media.bulk-delete' => 'project.admin.media.bulk-delete',
            'admin.media.bulk-delete' => 'project.admin.media.bulk-delete',
            'project.admin.languages.index' => 'project.admin.settings.languages',
            'admin.languages.index' => 'project.admin.settings.languages',
            'project.admin.seo.index' => 'project.admin.settings.seo',
            'admin.seo.index' => 'project.admin.settings.seo',
            'project.admin.logs.index' => 'project.admin.settings.logs',
            'admin.logs.index' => 'project.admin.settings.logs',
            'project.admin.logout' => 'project.logout',
            'admin.logout' => 'project.logout',
            'project.admin.form-submissions.overview' => 'project.admin.form-submissions.index',
            'admin.form-submissions.overview' => 'project.admin.form-submissions.index',
            'project.admin.form-templates.index' => 'project.admin.widget-templates.index',
            'admin.form-templates.index' => 'project.admin.widget-templates.index',
            'project.admin.modules.index' => 'project.admin.settings.index',
            'admin.modules.index' => 'project.admin.settings.index',
            'project.admin.spam.dashboard' => 'project.admin.settings.index',
            'admin.spam.dashboard' => 'project.admin.settings.index',
        ];
        if (isset($aliases[$name])) {
            $name = $aliases[$name];
        }

        $sessProj = session('current_project');
        $sessCode = is_array($sessProj) ? ($sessProj['code'] ?? null) : (is_object($sessProj) ? ($sessProj->code ?? null) : (is_string($sessProj) ? $sessProj : null));

        $projectCode = request()->route('projectCode')
            ?? $sessCode
            ?? (function_exists('current_project') ? current_project()?->code : null);

        if (! $projectCode && request()->segment(1) && request()->segment(2) === 'admin') {
            $projectCode = request()->segment(1);
        }

        if (! Route::has($name)) {
            if ($projectCode && Route::has('project.'.$name)) {
                $name = 'project.'.$name;
            } else {
                $cleanName = str_replace('project.', '', $name);
                if (Route::has($cleanName)) {
                    $name = $cleanName;
                } else {
                    return '#';
                }
            }
        }

        if (! is_array($params)) {
            if (str_contains($name, 'group')) {
                $params = ['group' => $params];
            } else {
                $params = ['slug' => $params];
            }
        } elseif (isset($params[0])) {
            if (str_contains($name, 'group')) {
                if (! isset($params['group'])) {
                    $params['group'] = $params[0];
                    unset($params[0]);
                }
            } else {
                if (! isset($params['slug'])) {
                    $params['slug'] = $params[0];
                    unset($params[0]);
                }
            }
        }

        if ($projectCode && ! isset($params['projectCode'])) {
            $params['projectCode'] = $projectCode;
        }

        try {
            return route($name, $params);
        } catch (Throwable $e) {
            return '#';
        }
    }
}
if (! function_exists('Lang')) {
    function Lang($key, $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: app()->getLocale();

        // 1. Check custom database translation
        $dbTrans = trans_db($key, $replace, $locale);
        if ($dbTrans !== $key && ! empty($dbTrans)) {
            return $dbTrans;
        }

        // 2. Check frontend.{key}
        $frontendKey = "frontend.{$key}";
        if (trans()->has($frontendKey, $locale)) {
            return trans($frontendKey, $replace, $locale);
        }

        // 3. Check common.{key}
        $commonKey = "common.{$key}";
        if (trans()->has($commonKey, $locale)) {
            return trans($commonKey, $replace, $locale);
        }

        // 4. Check footer.{key}
        $footerKey = "footer.{$key}";
        if (trans()->has($footerKey, $locale)) {
            return trans($footerKey, $replace, $locale);
        }

        // 5. Check direct key
        if (trans()->has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        // 6. Fallback to Vietnamese ('vi') if current locale is not 'vi'
        if ($fallback && $locale !== 'vi') {
            return Lang($key, $replace, 'vi', false);
        }

        return $key;
    }
}
if (! function_exists('clean_asset_url')) {
    function clean_asset_url(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // 1. Remove duplicate slashes (except protocol ://)
        $url = preg_replace('#(?<!:)//+#', '/', $url);

        // 2. Normalize repeated storage prefixes
        while (str_contains($url, '/storage/storage/')) {
            $url = str_replace('/storage/storage/', '/storage/', $url);
        }
        if (str_starts_with($url, 'storage/storage/')) {
            $url = preg_replace('#^(storage/)+#', 'storage/', $url);
        }

        return $url;
    }
}

if (! function_exists('resolve_image')) {
    function resolve_image($name, $default = null)
    {
        if (empty($name)) {
            return clean_asset_url($default ? asset($default) : asset('theme/images/logo/logo-01.svg'));
        }
        if (Str::contains($name, '://')) {
            return clean_asset_url($name);
        }

        $cleanName = ltrim($name, '/');
        while (str_contains($cleanName, 'storage/storage/')) {
            $cleanName = str_replace('storage/storage/', 'storage/', $cleanName);
        }

        if (str_starts_with($cleanName, 'media/')) {
            return clean_asset_url(Storage::disk('public')->url($cleanName));
        }
        if (str_starts_with($cleanName, 'storage/')) {
            return clean_asset_url(asset($cleanName));
        }
        if (str_starts_with($cleanName, 'theme/') || str_starts_with($cleanName, 'themes/') || str_starts_with($cleanName, 'assets/')) {
            return clean_asset_url(asset($cleanName));
        }

        $dbKey = match ($name) {
            'logo' => 'site_logo',
            'favicon' => 'site_favicon',
            default => "image_{$name}"
        };
        $dbValue = setting($dbKey);
        if ($dbValue) {
            if (Str::contains($dbValue, '://')) {
                return clean_asset_url($dbValue);
            }
            $cleanDbValue = ltrim($dbValue, '/');
            while (str_contains($cleanDbValue, 'storage/storage/')) {
                $cleanDbValue = str_replace('storage/storage/', 'storage/', $cleanDbValue);
            }
            if (str_starts_with($cleanDbValue, 'media/')) {
                return clean_asset_url(Storage::disk('public')->url($cleanDbValue));
            }
            if (str_starts_with($cleanDbValue, 'storage/')) {
                return clean_asset_url(asset($cleanDbValue));
            }

            return clean_asset_url(asset($cleanDbValue));
        }
        if ($default) {
            if (Str::contains($default, '://')) {
                return clean_asset_url($default);
            }
            $cleanDefault = ltrim($default, '/');
            while (str_contains($cleanDefault, 'storage/storage/')) {
                $cleanDefault = str_replace('storage/storage/', 'storage/', $cleanDefault);
            }
            if (str_starts_with($cleanDefault, 'theme/') || str_starts_with($cleanDefault, 'themes/') || str_starts_with($cleanDefault, 'assets/') || str_starts_with($cleanDefault, 'storage/')) {
                return clean_asset_url(asset($cleanDefault));
            }
            if (str_starts_with($cleanDefault, 'media/')) {
                return clean_asset_url(Storage::disk('public')->url($cleanDefault));
            }

            return clean_asset_url(asset("frontend/themes/viettinmartdemo/assets/images/{$cleanDefault}"));
        }

        return clean_asset_url(asset('theme/images/logo/logo-01.svg'));
    }
}
if (! function_exists('resolve_icon')) {
    function resolve_icon($name, $default = null)
    {
        $dbValue = setting('icon_'.$name);
        if ($dbValue) {
            if (Str::contains($dbValue, '://')) {
                return clean_asset_url($dbValue);
            }
            $cleanDbValue = ltrim($dbValue, '/');
            while (str_contains($cleanDbValue, 'storage/storage/')) {
                $cleanDbValue = str_replace('storage/storage/', 'storage/', $cleanDbValue);
            }
            if (str_starts_with($cleanDbValue, 'media/')) {
                return clean_asset_url(Storage::disk('public')->url($cleanDbValue));
            }
            if (str_contains($dbValue, 'fa-') && ! str_contains($dbValue, '/')) {
                return $dbValue;
            }

            return clean_asset_url(asset($cleanDbValue));
        }

        if ($default) {
            if (Str::contains($default, '://')) {
                return clean_asset_url($default);
            }
            $cleanDefault = ltrim($default, '/');
            while (str_contains($cleanDefault, 'storage/storage/')) {
                $cleanDefault = str_replace('storage/storage/', 'storage/', $cleanDefault);
            }
            if (str_starts_with($cleanDefault, 'theme/') || str_starts_with($cleanDefault, 'themes/') || str_starts_with($cleanDefault, 'assets/') || str_starts_with($cleanDefault, 'storage/')) {
                return clean_asset_url(asset($cleanDefault));
            }
            if (str_starts_with($cleanDefault, 'media/')) {
                return clean_asset_url(Storage::disk('public')->url($cleanDefault));
            }
            if (str_contains($default, 'fa-') && ! str_contains($default, '/')) {
                return $default;
            }
            if (file_exists(public_path('theme/images/icons/'.$default))) {
                return clean_asset_url(asset('theme/images/icons/'.$default));
            }
        }

        return match ($name) {
            'user' => 'fa-light fa-user',
            'wishlist' => 'fa-regular fa-heart',
            'cart' => 'fa-sharp fa-regular fa-cart-shopping',
            'category', 'category_bar', 'bar-1' => 'theme/images/icons/bar-1.svg',
            'plus' => 'fa-regular fa-plus',
            'clock' => 'fa-regular fa-clock',
            'folder' => 'fa-regular fa-folder',
            'arrow-right' => 'fa-light fa-arrow-right',
            'arrow-left' => 'fa-light fa-arrow-left',
            default => 'fa-regular fa-circle',
        };
    }
}

if (! function_exists('media_url')) {
    function media_url($path, $default = '')
    {
        if (empty($path)) {
            return clean_asset_url($default ? asset($default) : asset('theme/images/grocery/01.jpg'));
        }

        // 1. Clean corrupt prefixes like /storage/https://... or hardcoded domains
        if (is_string($path)) {
            $path = preg_replace('#^/storage/https?://[^/]+#', '', $path);
            if (Str::contains($path, '127.0.0.1:8000') || Str::contains($path, 'localhost:8000') || Str::contains($path, 'viettinmart.vnglobaltech.com')) {
                $path = preg_replace('#^https?://[^/]+#', '', $path);
            }
            while (str_contains($path, '/storage/storage/')) {
                $path = str_replace('/storage/storage/', '/storage/', $path);
            }
            while (str_contains($path, 'storage/storage/')) {
                $path = str_replace('storage/storage/', 'storage/', $path);
            }
        }

        if (Str::contains($path, '://')) {
            return clean_asset_url($path);
        }

        $cleanPath = ltrim($path, '/');
        while (str_contains($cleanPath, 'storage/storage/')) {
            $cleanPath = str_replace('storage/storage/', 'storage/', $cleanPath);
        }

        // 2. Direct existence check in public_path
        if (file_exists(public_path($cleanPath))) {
            return clean_asset_url(asset($cleanPath));
        }

        // 3. Check crossover between media-files and storage
        if (str_starts_with($cleanPath, 'storage/')) {
            $relativeMedia = preg_replace('#^storage/#', '', $cleanPath);
            // Check in public/media-files/...
            if (file_exists(public_path('media-files/'.$relativeMedia))) {
                return clean_asset_url(asset('media-files/'.$relativeMedia));
            }
            // Check in storage/app/public/...
            if (file_exists(storage_path('app/public/'.$relativeMedia))) {
                return clean_asset_url(asset($cleanPath));
            }
        } elseif (str_starts_with($cleanPath, 'media-files/')) {
            $relativeStorage = preg_replace('#^media-files/#', '', $cleanPath);
            // Check in public/storage/...
            if (file_exists(public_path('storage/'.$relativeStorage))) {
                return clean_asset_url(asset('storage/'.$relativeStorage));
            }
            // Check in storage/app/public/...
            if (file_exists(storage_path('app/public/'.$relativeStorage))) {
                return clean_asset_url(asset('storage/'.$relativeStorage));
            }
        } elseif (str_starts_with($cleanPath, 'media/')) {
            // Check in public/media-files/media/...
            if (file_exists(public_path('media-files/'.$cleanPath))) {
                return clean_asset_url(asset('media-files/'.$cleanPath));
            }
            // Check in public/storage/media/...
            if (file_exists(public_path('storage/'.$cleanPath))) {
                return clean_asset_url(asset('storage/'.$cleanPath));
            }
            // Check in storage/app/public/media/...
            if (file_exists(storage_path('app/public/'.$cleanPath))) {
                return clean_asset_url(Storage::disk('public')->url($cleanPath));
            }
        }

        // 4. Default asset or fallback
        if (! empty($default)) {
            return clean_asset_url(asset($default));
        }

        return clean_asset_url(asset($cleanPath));
    }
}

if (! function_exists('get_setting')) {
    function get_setting(string $key, mixed $default = null): mixed
    {
        if (function_exists('setting')) {
            return setting($key, $default);
        }

        return $default;
    }
}

if (! function_exists('sanitize_editor_html')) {
    /**
     * Sanitize rich text / CKEditor HTML output so only clean HTML and CSS styles/classes remain.
     * Strictly strips out all JavaScript (<script>, inline on* event attributes, javascript: pseudo-protocols, embeds).
     */
    function sanitize_editor_html(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Remove script tags and their content
        $clean = preg_replace('#<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>#is', '', $html);

        // 2. Remove any remaining stray script tags
        $clean = preg_replace('#<\/?script\b[^>]*>#i', '', $clean);

        // 3. Remove dangerous executable/embed tags (iframe, object, embed, applet, noscript, base, meta)
        $clean = preg_replace('#<\/?(iframe|object|embed|applet|noscript|base|meta)\b[^>]*>#i', '', $clean);

        // 4. Remove inline event handlers (onclick, onload, onerror, onmouseover, onfocus, onblur, etc.)
        $clean = preg_replace('#\s+on[a-zA-Z]+\s*=\s*(["\'])[\s\S]*?\1#i', '', $clean);
        $clean = preg_replace('#\s+on[a-zA-Z]+\s*=\s*[^ >\t\r\n]+#i', '', $clean);

        // 5. Remove javascript: pseudo-protocol in href, src, action, or data attributes
        $clean = preg_replace('#(href|src|action|data)\s*=\s*(["\'])\s*javascript:[^"\'\r\n]*\2#i', '$1="#"', $clean);
        $clean = preg_replace('#(href|src|action|data)\s*=\s*javascript:[^ >\t\r\n]+#i', '$1="#"', $clean);

        return $clean;
    }
}


<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainReplacerController;
use App\Http\Controllers\Admin\FormSubmissionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\SurveyInvitationController;
use App\Http\Controllers\MaterialLabController;
use App\Http\Controllers\CustomerSurveyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Themes\Daiphuc68\Daiphuc68Controller;
use App\Http\Controllers\Themes\Workspace\WorkspaceThemeController;
use App\Livewire\Admin\WidgetEditor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ĐẠI PHÚC 68 (Single Project Independent)
|--------------------------------------------------------------------------
*/

// ============================================
// 1. FRONTEND STOREFRONT ROUTES - WORKSPACE
// ============================================
Route::get('/', [WorkspaceThemeController::class, 'home'])->name('home');
Route::get('/theme/workspace', [WorkspaceThemeController::class, 'home'])->name('theme.workspace.home');

// Material Lab Frontend
Route::get('/material-lab', [MaterialLabController::class, 'index'])->name('material-lab');

// Customer Survey Frontend
Route::get('/customer-survey', [CustomerSurveyController::class, 'show'])->name('customer-survey.show');
Route::post('/customer-survey', [CustomerSurveyController::class, 'submit'])->name('customer-survey.submit');

Route::controller(Daiphuc68Controller::class)->group(function () {
    Route::get('/daiphuc68', 'home')->name('daiphuc68.home');

    // Về chúng tôi
    Route::get('/about-us', 'aboutUs')->name('about-us');
    Route::get('/ve-chung-toi', fn () => redirect()->route('about-us'));

    // Sản phẩm
    Route::get('/products', 'products')->name('products.index');
    Route::get('/products/{slug}', 'productDetail')->name('products.show');
    Route::get('/san-pham', fn () => redirect()->route('products.index'));
    Route::get('/san-pham/{slug}', fn (string $slug) => redirect()->route('products.show', $slug, 301));

    // Dự án
    Route::get('/projects', 'projects')->name('projects.index');
    Route::get('/du-an', fn () => redirect()->route('projects.index'));
    Route::get('/projects/{slug}', fn (string $slug) => redirect()->route('projects.show', $slug, 301));
    Route::get('/du-an/{slug}', fn (string $slug) => redirect()->route('projects.show', $slug, 301));

    // Tin tức
    Route::get('/news', 'news')->name('news.index');
    Route::get('/news/{slug}', 'newsDetail')->name('news.show');
    Route::get('/tin-tuc', fn () => redirect()->route('news.index'));
    Route::get('/tin-tuc/{slug}', fn (string $slug) => redirect()->route('news.show', $slug, 301));

    // Liên hệ & Báo giá
    Route::get('/contact', 'contactPage')->name('contact.page');
    Route::post('/contact', 'contactSubmit')->name('contact.submit');
    Route::get('/lien-he', fn () => redirect()->route('contact.page'));
    Route::post('/api/form-submit', 'contactSubmit')->name('api.form.submit');

    // Hồ sơ năng lực (Heyzine Flipbook)
    Route::get('/ho-so-nang-luc', 'companyProfile')->name('company.profile');
    Route::get('/profile', fn () => redirect()->route('company.profile'));
    Route::get('/company-profile', fn () => redirect()->route('company.profile'));

    // Trang tĩnh (Pages)
    Route::get('/pages/{slug}', 'pageDetail')->name('pages.show');
    Route::get('/page/{slug}', fn (string $slug) => redirect()->route('pages.show', $slug, 301));

});



// Fallback for public storage files
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/'.$path);
    if (! file_exists($fullPath)) {
        if (file_exists(storage_path('app/public/media/'.$path))) {
            $fullPath = storage_path('app/public/media/'.$path);
        } else {
            abort(404);
        }
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.fallback');

// ============================================
// XML SITEMAP & ROBOTS.TXT (SEO Standards)
// ============================================
Route::get('/robots.txt', function () {
    $defaultRobots = "User-agent: *\n"
        . "Disallow: /admin/\n"
        . "Disallow: /superadmin/\n"
        . "Disallow: /login/\n"
        . "Disallow: /register/\n"
        . "Disallow: /cart/\n"
        . "Disallow: /checkout/\n"
        . "Disallow: /search/\n"
        . "Disallow: /?s=\n"
        . "Disallow: /*?orderby=\n"
        . "Disallow: /*?filter_\n\n"
        . "Sitemap: " . url('/sitemap.xml');

    $robots = setting_string('robots_txt', $defaultRobots);
    return response($robots, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->name('0.');

Route::controller(SitemapController::class)->group(function () {
    Route::get('/sitemap.xml', 'index')->name('sitemap.xml');
    Route::get('/sitemap', 'index')->name('sitemap');
    Route::get('/sitemap-index.xml', 'sitemapIndex')->name('sitemap.index');
    Route::get('/sitemap-products.xml', 'products')->name('sitemap.products');
    Route::get('/sitemap-news.xml', 'news')->name('sitemap.news');
    Route::get('/sitemap-projects.xml', 'projects')->name('sitemap.projects');
    Route::get('/sitemap-pages.xml', 'pages')->name('sitemap.pages');
    Route::get('/sitemap-categories.xml', 'categories')->name('sitemap.categories');
    Route::get('/sitemap.html', 'htmlIndex')->name('sitemap.html');
});

Route::get('/media/{path}', function (string $path) {
    $fullPath = storage_path('app/public/'.$path);
    if (! file_exists($fullPath)) {
        $fullPath = storage_path('app/public/media/'.$path);
    }
    if (! file_exists($fullPath)) {
        $fullPath = public_path('themes/inbetween_v2/images/'.$path);
    }
    if (! file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('watermark.image');

// ============================================
// 2. AUTHENTICATION ROUTES
// ============================================

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']);


Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Widgets Management
    Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::get('widgets/{id}/edit', WidgetEditor::class)->name('widgets.edit');
    Route::put('widgets/{widget}', [WidgetController::class, 'update'])->name('widgets.update');
    Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('widgets/save-all', [WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
    Route::post('widgets/reorder', [WidgetController::class, 'reorder'])->name('widgets.reorder');
    Route::match(['get', 'post'], 'widgets/preview', [WidgetController::class, 'preview'])->name('widgets.preview');
    Route::post('widgets/toggle', [WidgetController::class, 'toggleWidget'])->name('widgets.toggle');
    Route::post('widgets/clear-cache', [WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
    Route::match(['get', 'post'], 'widgets/fields', [WidgetController::class, 'getFields'])->name('widgets.fields');

    // Menus Management
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::put('menus/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
    Route::delete('menus/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
    Route::post('menus/{menu}/update-tree', [MenuController::class, 'updateTree'])->name('menus.update-tree');

    // Form Submissions Management
    Route::match(['delete', 'post'], 'form-submissions/destroy-all', [FormSubmissionController::class, 'destroyAll'])->name('form-submissions.destroy-all');
    Route::post('form-submissions/bulk-delete', [FormSubmissionController::class, 'bulkDelete'])->name('form-submissions.bulk-delete');
    Route::get('form-submissions', [FormSubmissionController::class, 'index'])->name('form-submissions.index');
    Route::get('form-submissions/{submission}', [FormSubmissionController::class, 'show'])->name('form-submissions.show');
    Route::patch('form-submissions/{submission}/status', [FormSubmissionController::class, 'updateStatus'])->name('form-submissions.update-status');
    Route::delete('form-submissions/{submission}', [FormSubmissionController::class, 'destroy'])->name('form-submissions.destroy');

    // Media Library
    Route::get('media', [MediaController::class, 'list'])->name('media.index');
    Route::get('media/list', [MediaController::class, 'list'])->name('media.list');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/folder', [MediaController::class, 'createFolder'])->name('media.folder.create');
    Route::delete('media/folder', [MediaController::class, 'deleteFolder'])->name('media.folder.delete');
    Route::post('media/folder/delete', [MediaController::class, 'deleteFolder'])->name('media.folder.delete.post');
    Route::post('media/move', [MediaController::class, 'move'])->name('media.move');
    Route::post('media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::delete('media/{id}', [MediaController::class, 'destroy'])->name('media.destroy')->where('id', '.*');

    // Settings Management
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/seo', [SettingsController::class, 'seo'])->name('settings.seo');
    Route::post('settings', [SettingsController::class, 'save'])->name('settings.save');
    Route::post('settings/seo', [SettingsController::class, 'save'])->name('settings.seo.save');
    Route::get('settings/modal-box', [SettingsController::class, 'modalBox'])->name('settings.modal-box');
    Route::post('settings/modal-box', [SettingsController::class, 'save'])->name('settings.modal-box.save');
    Route::get('settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('settings/notifications', [SettingsController::class, 'save'])->name('settings.notifications.save');
    Route::get('settings/filter', [SettingsController::class, 'filter'])->name('settings.filter');
    Route::get('settings/fillter', [SettingsController::class, 'filter'])->name('settings.fillter');
    Route::post('settings/filter', [SettingsController::class, 'save'])->name('settings.filter.save');
    Route::post('settings/fillter', [SettingsController::class, 'save'])->name('settings.fillter.save');
    Route::get('settings/contact-buttons', [SettingsController::class, 'contactButtons'])->name('settings.contact-buttons');
    Route::post('settings/contact-buttons', [SettingsController::class, 'save'])->name('settings.contact-buttons.save');
    Route::get('settings/appearance', [SettingsController::class, 'appearance'])->name('settings.appearance');
    Route::match(['put', 'post'], 'settings/appearance', [SettingsController::class, 'save'])->name('settings.appearance.update');
    Route::get('settings/scan-translations', [SettingsController::class, 'scanTranslations'])->name('settings.scan-translations');
    Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
    Route::get('settings/social', [SettingsController::class, 'social'])->name('settings.social');
    Route::post('settings/social', [SettingsController::class, 'save'])->name('settings.social.save');
    Route::get('settings/{group}', [SettingsController::class, 'group'])->name('settings.group');
    Route::match(['put', 'post'], 'settings/{group}', [SettingsController::class, 'updateGroup'])->name('settings.group.update');

    // AI Content Generator & SEO Routes
    Route::post('ai/test', [AiController::class, 'test'])->name('ai.test');
    Route::post('ai/list-models', [AiController::class, 'listModels'])->name('ai.list-models');
    Route::post('ai/generate', [AiController::class, 'generate'])->name('ai.generate');
    Route::post('ai/optimize-seo', [AiController::class, 'optimizeSeo'])->name('ai.optimize-seo');

    // User Management
    Route::resource('users', UserController::class);

    // ============================================
    // ĐẠI PHÚC 68 - CRUD CONTENT MANAGEMENT
    // (Single Posts Table Architecture)
    // ============================================

    // Quản lý Nội dung Chung (Universal CRUD: products, projects, news, pages, faqs)
    Route::resource('posts', PostController::class);
    Route::post('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('posts/{post}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');

    // Quản lý Sản phẩm (post_type = product)
    Route::post('products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('products/update-order', [ProductController::class, 'updateOrder'])->name('products.update-order');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{product}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

    // Quản lý Danh mục (Taxonomies: product_cat, category)
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');

    // Quản lý Bộ Thuộc Tính Sản Phẩm (Taxonomies: product_attribute)
    Route::resource('attributes', ProductAttributeController::class);
    Route::post('attributes/{attribute}/values', [ProductAttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/values/{value}', [ProductAttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    // Quản lý Trang tĩnh (post_type = page: About Us, Contact, etc.)
    Route::resource('pages', PageController::class);

    // Quản lý Hỏi Đáp / FAQs alias
    Route::get('faqs', fn () => redirect()->route('admin.posts.index', ['type' => 'faq']))->name('faqs.index');

    // Công cụ tìm & thay thế Domain / URL Database
    Route::get('replace-domain', [DomainReplacerController::class, 'index'])->name('replace-domain.index');
    Route::post('replace-domain', [DomainReplacerController::class, 'execute'])->name('replace-domain.run');

    // ============================================
    // MATERIAL LAB & CUSTOMER SURVEY MANAGEMENT
    // ============================================
    // Material Lab
    Route::get('material-lab/config', [MaterialController::class, 'config'])->name('material-lab.config');
    Route::post('material-lab/config', [MaterialController::class, 'saveConfig'])->name('material-lab.save-config');
    Route::post('material-lab/toggle', [MaterialController::class, 'toggleActive'])->name('material-lab.toggle');
    Route::post('material-lab/toggle-active', [MaterialController::class, 'toggleActive'])->name('material-lab.toggle-active');
    Route::resource('material-lab', MaterialController::class)->parameters([
        'material-lab' => 'material',
    ])->except(['show']);

    // Customer Survey
    Route::resource('surveys', SurveyController::class)->only(['index', 'edit', 'update']);
    Route::post('surveys/{survey}/questions', [SurveyController::class, 'storeQuestion'])->name('surveys.questions.store');
    Route::put('surveys/questions/{question}', [SurveyController::class, 'updateQuestion'])->name('surveys.questions.update');
    Route::delete('surveys/questions/{question}', [SurveyController::class, 'destroyQuestion'])->name('surveys.questions.destroy');
    Route::post('surveys/questions/reorder', [SurveyController::class, 'reorderQuestions'])->name('surveys.questions.reorder');
    Route::get('surveys/{survey}/responses', [SurveyController::class, 'responses'])->name('surveys.responses.index');
    Route::get('surveys/responses/{response}', [SurveyController::class, 'showResponse'])->name('surveys.responses.show');
    Route::delete('surveys/responses/{response}', [SurveyController::class, 'destroyResponse'])->name('surveys.responses.destroy');
    Route::get('surveys/{survey}/invitations', [SurveyInvitationController::class, 'index'])->name('surveys.invitations.index');
    Route::post('surveys/{survey}/invitations', [SurveyInvitationController::class, 'send'])->name('surveys.invitations.send');
    Route::post('surveys/invitations/{invitation}/retry', [SurveyInvitationController::class, 'retry'])->name('surveys.invitations.retry');
    Route::delete('surveys/invitations/{invitation}', [SurveyInvitationController::class, 'destroy'])->name('surveys.invitations.destroy');
});

// ============================================
// 3. CMS COMPATIBILITY ALIAS ROUTES
// ============================================
Route::prefix('cms')->name('cms.')->middleware('auth')->group(function () {
    Route::resource('posts', PostController::class);
    Route::post('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('posts/{post}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');
    Route::post('products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('products/update-order', [ProductController::class, 'updateOrder'])->name('products.update-order');
    Route::resource('products', ProductController::class);
    // Legacy /cms/admin compatibility aliases
    Route::post('admin/products/bulk-edit', [ProductController::class, 'bulkEdit']);
    Route::post('admin/products/bulk-update', [ProductController::class, 'bulkUpdate']);
    Route::post('admin/products/update-order', [ProductController::class, 'updateOrder']);
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::resource('attributes', ProductAttributeController::class);
    Route::resource('pages', PageController::class);
    Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::get('media', [MediaController::class, 'list'])->name('media.index');
    Route::get('media/list', [MediaController::class, 'list'])->name('media.list');
    Route::get('form-submissions', [FormSubmissionController::class, 'index'])->name('form-submissions.index');
    Route::get('settings/contact-buttons', [SettingsController::class, 'contactButtons'])->name('settings.contact-buttons');
    Route::post('settings', [SettingsController::class, 'save'])->name('settings.save');
    Route::post('settings/save', [SettingsController::class, 'save']);
});

// Project admin compatibility alias routes
Route::middleware('auth')->group(function () {
    Route::post('{projectCode}/admin/settings', [SettingsController::class, 'save'])->name('project.admin.settings.save');
    Route::get('{projectCode}/admin/settings', [SettingsController::class, 'index'])->name('project.admin.settings.index');
});
// Standalone Domain Replacer tool (hỗ trợ truy cập trực tiếp bằng Secret Key khi chuyển hosting)
Route::match(['get', 'post'], 'tools/replace-domain', [DomainReplacerController::class, 'standalone'])->name('tools.replace-domain');
Route::match(['get', 'post'], 'replace-domain', [DomainReplacerController::class, 'standalone']);

// ============================================
// 4. FALLBACK DYNAMIC SLUG ROUTE (PROJECTS & PAGES)
// ============================================
Route::get('/{slug}', [Daiphuc68Controller::class, 'projectDetail'])
    ->where('slug', '^(?!(admin|cms|login|logout|superadmin|tools|replace-domain|storage|media|api|san-pham|du-an|tin-tuc|lien-he|about-us|products|projects|news|contact|pages|page|ho-so-nang-luc|profile|company-profile|material-lab|customer-survey)$).*$')
    ->name('projects.show');


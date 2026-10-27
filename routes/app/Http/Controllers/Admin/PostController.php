<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    use HasAlerts;

    private function resolvePost($projectCodeOrPost, $postId = null): Post
    {
        if ($projectCodeOrPost instanceof Post) {
            return $projectCodeOrPost;
        }

        if ($postId instanceof Post) {
            return $postId;
        }

        $param = $postId ?? $projectCodeOrPost;
        $projectCode = is_string($projectCodeOrPost) && ! is_numeric($projectCodeOrPost) && $postId !== null
            ? $projectCodeOrPost
            : (request()->route('projectCode') ?? (function_exists('project_code') ? project_code() : null));

        $query = Post::withTrashed();

        if ($projectCode) {
            $project = Project::where('code', $projectCode)->first();
            if ($project) {
                $query->where(function ($q) use ($project) {
                    $q->where('project_id', $project->id);
                    if ($project->tenant_id) {
                        $q->orWhere('tenant_id', $project->tenant_id);
                    }
                });
            }
        }

        if (is_numeric($param)) {
            return (clone $query)->where('id', $param)->first()
                ?? Post::withTrashed()->where('id', $param)->firstOrFail();
        }

        return (clone $query)->where(function ($q) use ($param) {
            $q->where('slug', $param)->orWhere('id', $param);
        })->first() ?? Post::withTrashed()->where(function ($q) use ($param) {
            $q->where('slug', $param)->orWhere('id', $param);
        })->firstOrFail();
    }

    public function index(Request $request, $projectCode = null)
    {
        $postType = $request->route('post_type') ?? $request->route('type') ?? $request->query('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $query = Post::with('author')
            ->where('post_type', $postType);

        if ($request->status === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->when($request->status, fn ($q) => $q->where('status', $request->status));
        }

        if ($postType === 'faq') {
            $posts = $query->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->paginate(20);
        } else {
            $posts = $query->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
                ->latest()
                ->paginate(20);
        }

        return view('cms.posts.index', compact('posts', 'postType', 'config'));
    }

    public function create(Request $request, $projectCode = null)
    {
        $postType = $request->route('post_type') ?? $request->route('type') ?? $request->get('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
                'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery'],
            ];
        } else {
            if (! isset($config['supports'])) {
                $config['supports'] = ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery'];
            } elseif (! in_array('seo', $config['supports'])) {
                $config['supports'][] = 'seo';
            }
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        $hasTaxonomies = \Illuminate\Support\Facades\Schema::hasTable('taxonomies');
        $taxonomyType = ($postType === 'project') ? 'project_cat' : (($postType === 'product') ? 'product_cat' : 'category');
        $categories = $hasTaxonomies ? Taxonomy::where('taxonomy', $taxonomyType)->orderBy('order')->get() : collect();
        $availableTags = $hasTaxonomies ? Taxonomy::whereIn('taxonomy', ['post_tag', 'tag'])->orderBy('name')->get() : collect();

        return view('cms.posts.create', compact('postType', 'config', 'currentLang', 'categories', 'availableTags'));
    }

    public function store(Request $request, $projectCode = null)
    {
        $postType = $request->input('post_type', $request->route('post_type') ?? $request->route('type') ?? 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $projectCode = request()->route('projectCode') ?? (function_exists('project_code') ? project_code() : null);
        $project = $projectCode ? Project::where('code', $projectCode)->first() : request()->attributes->get('project');
        $projectId = $project?->id ?? session('current_project_id') ?? (function_exists('project_id') ? project_id() : null);
        $tenantId = $project?->tenant_id ?? session('current_tenant_id') ?? (function_exists('tenant_id') ? tenant_id() : null);

        $slugRule = Rule::unique('posts', 'slug')
            ->where(function ($query) use ($projectId, $tenantId, $postType) {
                if ($projectId) {
                    $query->where('project_id', $projectId);
                } elseif ($tenantId) {
                    $query->where('tenant_id', $tenantId);
                }
                $query->where('post_type', $postType);
            });

        // Validate basic fields
        $rules = [
            'slug' => ['nullable', 'string', $slugRule],
            'template' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'post_type' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'focus_keyword' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:500',
            'schema_type' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|string|max:500',
            'custom_schema' => 'nullable|string',
            'noindex' => 'nullable',
            'order' => 'nullable|integer',
            'meta_data' => 'nullable|array',
        ];

        // Add dynamic validation from config
        if (isset($config['fields'])) {
            foreach ($config['fields'] as $key => $field) {
                $rules["meta_data.{$key}"] = 'nullable';
            }
        }

        $validated = $request->validate($rules);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        $defaultTitle = $request->input("translations.{$defaultLang}.title") ?? $request->input('title');
        if (empty($defaultTitle)) {
            $defaultTitle = $request->input('name') ?? $request->input('meta_title') ?? 'Bài viết mới';
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($defaultTitle ?: 'bai-viet-'.time());
        $validated['title'] = $defaultTitle;

        $rawContent = (string) ($request->input("translations.{$defaultLang}.content") ?? $request->input('content') ?? '');
        $rawExcerpt = (string) ($request->input("translations.{$defaultLang}.excerpt") ?? $request->input('excerpt') ?? '');
        if (function_exists('sanitize_editor_html')) {
            $rawContent = sanitize_editor_html($rawContent);
            $rawExcerpt = sanitize_editor_html($rawExcerpt);
        }
        $validated['content'] = $rawContent;
        $validated['excerpt'] = $rawExcerpt;
        $validated['author_id'] = auth()->id();

        // Process SEO fields
        $inputMetaTitle = trim((string) $request->input('meta_title', ''));
        $transMetaTitle = trim((string) $request->input("translations.{$defaultLang}.meta_title", ''));
        $metaTitle = $inputMetaTitle !== '' ? $inputMetaTitle : ($transMetaTitle !== '' ? $transMetaTitle : $defaultTitle);

        $inputMetaDesc = trim((string) $request->input('meta_description', ''));
        $transMetaDesc = trim((string) $request->input("translations.{$defaultLang}.meta_description", ''));
        $metaDescription = $inputMetaDesc !== '' ? $inputMetaDesc : ($transMetaDesc !== '' ? $transMetaDesc : Str::limit(strip_tags($rawExcerpt ?: $rawContent ?: $defaultTitle), 160));

        $validated['meta_title'] = $metaTitle;
        $validated['meta_description'] = $metaDescription;

        $inputKeyword = trim((string) $request->input('focus_keyword', ''));
        $inputKeywords = trim((string) $request->input('meta_keywords', ''));
        $inputSchema = trim((string) $request->input('schema_type', ''));
        $inputCanonical = trim((string) $request->input('canonical_url', ''));
        $inputCustomSchema = trim((string) $request->input('custom_schema', ''));
        $defaultSchema = $postType === 'product' ? 'Product' : ($postType === 'page' ? 'Organization' : 'Article');

        $validated['seo_data'] = [
            'focus_keyword' => $inputKeyword !== '' ? $inputKeyword : null,
            'meta_keywords' => $inputKeywords !== '' ? $inputKeywords : null,
            'schema_type' => $inputSchema !== '' ? $inputSchema : $defaultSchema,
            'canonical_url' => $inputCanonical !== '' ? $inputCanonical : null,
            'custom_schema' => $inputCustomSchema !== '' ? $inputCustomSchema : null,
            'noindex' => $request->boolean('noindex'),
        ];

        // Process meta data
        $inputMeta = (array) $request->input('meta_data', []);
        if (!empty($inputMeta['year']) && empty($inputMeta['completion_year'])) {
            $inputMeta['completion_year'] = $inputMeta['year'];
        } elseif (!empty($inputMeta['completion_year']) && empty($inputMeta['year'])) {
            $inputMeta['year'] = $inputMeta['completion_year'];
        }
        if (isset($inputMeta['gallery'])) {
            $inputMeta['gallery'] = array_values(array_filter(array_map('trim', (array) $inputMeta['gallery'])));
        }
        if ($request->has('gallery')) {
            $inputMeta['gallery'] = array_values(array_filter(array_map('trim', (array) $request->input('gallery', []))));
        }
        $validated['meta_data'] = $inputMeta;

        if ($request->has('template')) {
            $validated['template'] = $request->input('template');
        }

        if ($request->has('meta_data.order')) {
            $validated['order'] = (int) $request->input('meta_data.order', 0);
        } elseif ($request->has('order')) {
            $validated['order'] = (int) $request->input('order', 0);
        }

        $post = Post::create($validated);

        // Process categories
        $taxonomyType = ($postType === 'project') ? 'project_cat' : (($postType === 'product') ? 'product_cat' : 'category');
        if ($request->has('categories')) {
            $categoryIds = array_filter((array) $request->input('categories', []));
            $allCategoryTaxIds = Taxonomy::where('taxonomy', $taxonomyType)->pluck('id')->toArray();
            DB::table('term_relationships')
                ->where('object_id', $post->id)
                ->whereIn('term_taxonomy_id', $allCategoryTaxIds)
                ->delete();

            foreach ($categoryIds as $catId) {
                DB::table('term_relationships')->insert([
                    'object_id' => $post->id,
                    'term_taxonomy_id' => $catId,
                    'order' => 0,
                ]);
            }

            if (!empty($categoryIds)) {
                $primaryCat = Taxonomy::find($categoryIds[0]);
                if ($primaryCat) {
                    $metaData = $post->meta_data ?: [];
                    $metaData['category'] = $primaryCat->name;
                    $post->update(['meta_data' => $metaData]);
                }
            }
        }

        // Process tags
        if ($request->has('tags')) {
            $rawTags = $request->input('tags');
            $tagNames = is_array($rawTags) ? $rawTags : explode(',', (string) $rawTags);
            $tagNames = array_unique(array_filter(array_map('trim', $tagNames)));

            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (! empty($tagName)) {
                    $tagTax = Taxonomy::firstOrCreate(
                        [
                            'project_id' => $post->project_id ?? (function_exists('project_id') ? project_id() : null),
                            'taxonomy' => 'post_tag',
                            'slug' => Str::slug($tagName),
                        ],
                        [
                            'tenant_id' => $post->tenant_id ?? 3,
                            'name' => $tagName,
                            'status' => 'published',
                        ]
                    );
                    $tagIds[] = $tagTax->id;
                }
            }

            $allTagTaxIds = Taxonomy::whereIn('taxonomy', ['post_tag', 'tag'])->pluck('id')->toArray();
            DB::table('term_relationships')
                ->where('object_id', $post->id)
                ->whereIn('term_taxonomy_id', $allTagTaxIds)
                ->delete();

            foreach ($tagIds as $tagId) {
                DB::table('term_relationships')->insert([
                    'object_id' => $post->id,
                    'term_taxonomy_id' => $tagId,
                    'order' => 0,
                ]);
            }
        }

        if ($request->has('translations') || !empty($metaTitle)) {
            $translations = $request->input('translations', []);
            if (!isset($translations[$defaultLang])) {
                $translations[$defaultLang] = [];
            }
            if (!empty($metaTitle)) {
                $translations[$defaultLang]['meta_title'] = $metaTitle;
            }
            if (!empty($metaDescription)) {
                $translations[$defaultLang]['meta_description'] = $metaDescription;
            }
            if (!empty($defaultTitle)) {
                $translations[$defaultLang]['title'] = $defaultTitle;
            }
            if (!empty($rawContent)) {
                $translations[$defaultLang]['content'] = $rawContent;
            }
            if (!empty($rawExcerpt)) {
                $translations[$defaultLang]['excerpt'] = $rawExcerpt;
            }
            $post->saveTranslations($translations);
        }

        $route = Route::has('admin.posts.index') 
            ? route('admin.posts.index', ['type' => $postType]) 
            : (Route::has('cms.posts.index') ? route('cms.posts.index', ['type' => $postType]) : url('/admin/posts?type=' . $postType));

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Thêm '.($config['name'] ?? 'nội dung').' thành công!',
        ]);
    }

    public function show(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);

        return view('cms.posts.show', compact('post'));
    }

    public function edit(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $post->load('taxonomies');
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
                'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery'],
            ];
        } else {
            if (! isset($config['supports'])) {
                $config['supports'] = ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery'];
            } elseif (! in_array('seo', $config['supports'])) {
                $config['supports'][] = 'seo';
            }
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        $hasTaxonomies = \Illuminate\Support\Facades\Schema::hasTable('taxonomies');
        $taxonomyType = ($postType === 'project') ? 'project_cat' : (($postType === 'product') ? 'product_cat' : 'category');
        $categories = $hasTaxonomies ? Taxonomy::where('taxonomy', $taxonomyType)->orderBy('order')->get() : collect();
        $availableTags = $hasTaxonomies ? Taxonomy::whereIn('taxonomy', ['post_tag', 'tag'])->orderBy('name')->get() : collect();
        $selectedCategories = ($hasTaxonomies && $post->taxonomies) ? $post->taxonomies->where('taxonomy', $taxonomyType)->pluck('id')->toArray() : [];
        $selectedTags = ($hasTaxonomies && $post->taxonomies) ? $post->taxonomies->whereIn('taxonomy', ['post_tag', 'tag'])->pluck('name')->implode(', ') : '';

        return view('cms.posts.edit', compact(
            'post',
            'postType',
            'config',
            'currentLang',
            'categories',
            'availableTags',
            'selectedCategories',
            'selectedTags'
        ));
    }

    public function update(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $projectCode = request()->route('projectCode') ?? (function_exists('project_code') ? project_code() : null);
        $project = $projectCode ? Project::where('code', $projectCode)->first() : request()->attributes->get('project');
        $projectId = $post->project_id ?? $project?->id ?? session('current_project_id') ?? (function_exists('project_id') ? project_id() : null);
        $tenantId = $post->tenant_id ?? $project?->tenant_id ?? session('current_tenant_id') ?? (function_exists('tenant_id') ? tenant_id() : null);

        $slugRule = Rule::unique('posts', 'slug')
            ->ignore($post->id)
            ->where(function ($query) use ($projectId, $tenantId, $postType) {
                if ($projectId) {
                    $query->where('project_id', $projectId);
                } elseif ($tenantId) {
                    $query->where('tenant_id', $tenantId);
                }
                $query->where('post_type', $postType);
            });

        $rules = [
            'slug' => ['nullable', 'string', $slugRule],
            'template' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'focus_keyword' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:500',
            'schema_type' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|string|max:500',
            'custom_schema' => 'nullable|string',
            'noindex' => 'nullable',
            'order' => 'nullable|integer',
            'meta_data' => 'nullable|array',
        ];

        // Add dynamic validation from config
        if (isset($config['fields'])) {
            foreach ($config['fields'] as $key => $field) {
                $rules["meta_data.{$key}"] = 'nullable';
            }
        }

        $validated = $request->validate($rules);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        $defaultTitle = $request->input("translations.{$defaultLang}.title") ?? $request->input('title', $post->title);
        if (empty($defaultTitle)) {
            $defaultTitle = $post->title ?: ($request->input('meta_title') ?? 'Bài viết');
        }
        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : ($post->slug ?: Str::slug($defaultTitle));

        $validated['title'] = $defaultTitle;

        $rawContent = (string) ($request->input("translations.{$defaultLang}.content") ?? $request->input('content') ?? ($post->content ?? ''));
        $rawExcerpt = (string) ($request->input("translations.{$defaultLang}.excerpt") ?? $request->input('excerpt') ?? ($post->excerpt ?? ''));
        if (function_exists('sanitize_editor_html')) {
            $rawContent = sanitize_editor_html($rawContent);
            $rawExcerpt = sanitize_editor_html($rawExcerpt);
        }
        $validated['content'] = $rawContent;
        $validated['excerpt'] = $rawExcerpt;

        // Process SEO fields
        $inputMetaTitle = trim((string) $request->input('meta_title', ''));
        $transMetaTitle = trim((string) $request->input("translations.{$defaultLang}.meta_title", ''));
        $metaTitle = $inputMetaTitle !== '' ? $inputMetaTitle : ($transMetaTitle !== '' ? $transMetaTitle : ($post->meta_title ?: $defaultTitle));

        $inputMetaDesc = trim((string) $request->input('meta_description', ''));
        $transMetaDesc = trim((string) $request->input("translations.{$defaultLang}.meta_description", ''));
        $metaDescription = $inputMetaDesc !== '' ? $inputMetaDesc : ($transMetaDesc !== '' ? $transMetaDesc : ($post->meta_description ?: Str::limit(strip_tags($rawContent ?: $rawExcerpt ?: $defaultTitle), 160)));

        $validated['meta_title'] = $metaTitle;
        $validated['meta_description'] = $metaDescription;

        $currentSeo = is_array($post->seo_data) ? $post->seo_data : [];
        $inputKeyword = $request->has('focus_keyword') ? trim((string) $request->input('focus_keyword', '')) : ($currentSeo['focus_keyword'] ?? '');
        $inputKeywords = $request->has('meta_keywords') ? trim((string) $request->input('meta_keywords', '')) : ($currentSeo['meta_keywords'] ?? '');
        $inputSchema = $request->has('schema_type') ? trim((string) $request->input('schema_type', '')) : ($currentSeo['schema_type'] ?? '');
        $inputCanonical = $request->has('canonical_url') ? trim((string) $request->input('canonical_url', '')) : ($currentSeo['canonical_url'] ?? '');
        $inputCustomSchema = $request->has('custom_schema') ? trim((string) $request->input('custom_schema', '')) : ($currentSeo['custom_schema'] ?? '');
        $defaultSchema = $postType === 'product' ? 'Product' : ($postType === 'page' ? 'Organization' : 'Article');

        $validated['seo_data'] = [
            'focus_keyword' => $inputKeyword !== '' ? $inputKeyword : null,
            'meta_keywords' => $inputKeywords !== '' ? $inputKeywords : null,
            'schema_type' => $inputSchema !== '' ? $inputSchema : ($currentSeo['schema_type'] ?? $defaultSchema),
            'canonical_url' => $inputCanonical !== '' ? $inputCanonical : null,
            'custom_schema' => $inputCustomSchema !== '' ? $inputCustomSchema : null,
            'noindex' => $request->boolean('noindex'),
        ];

        $existingMeta = is_array($post->meta_data) ? $post->meta_data : (json_decode($post->meta_data ?? '[]', true) ?: []);
        $inputMeta = (array) $request->input('meta_data', []);
        if (!empty($inputMeta['year']) && empty($inputMeta['completion_year'])) {
            $inputMeta['completion_year'] = $inputMeta['year'];
        } elseif (!empty($inputMeta['completion_year']) && empty($inputMeta['year'])) {
            $inputMeta['year'] = $inputMeta['completion_year'];
        }
        if (isset($inputMeta['gallery'])) {
            $inputMeta['gallery'] = array_values(array_filter(array_map('trim', (array) $inputMeta['gallery'])));
        }
        if ($request->has('gallery')) {
            $inputMeta['gallery'] = array_values(array_filter(array_map('trim', (array) $request->input('gallery', []))));
        }
        $validated['meta_data'] = array_merge($existingMeta, $inputMeta);

        if ($request->has('template')) {
            $validated['template'] = $request->input('template');
        }

        if ($request->has('meta_data.order')) {
            $validated['order'] = (int) $request->input('meta_data.order', 0);
        } elseif ($request->has('order')) {
            $validated['order'] = (int) $request->input('order', 0);
        }

        $post->update($validated);

        // Process categories
        $taxonomyType = ($postType === 'project') ? 'project_cat' : (($postType === 'product') ? 'product_cat' : 'category');
        if ($request->has('categories')) {
            $categoryIds = array_filter((array) $request->input('categories', []));
            $allCategoryTaxIds = Taxonomy::where('taxonomy', $taxonomyType)->pluck('id')->toArray();
            DB::table('term_relationships')
                ->where('object_id', $post->id)
                ->whereIn('term_taxonomy_id', $allCategoryTaxIds)
                ->delete();

            foreach ($categoryIds as $catId) {
                DB::table('term_relationships')->insert([
                    'object_id' => $post->id,
                    'term_taxonomy_id' => $catId,
                    'order' => 0,
                ]);
            }

            if (!empty($categoryIds)) {
                $primaryCat = Taxonomy::find($categoryIds[0]);
                if ($primaryCat) {
                    $metaData = $post->meta_data ?: [];
                    $metaData['category'] = $primaryCat->name;
                    $post->update(['meta_data' => $metaData]);
                }
            }
        }

        // Process tags
        if ($request->has('tags')) {
            $rawTags = $request->input('tags');
            $tagNames = is_array($rawTags) ? $rawTags : explode(',', (string) $rawTags);
            $tagNames = array_unique(array_filter(array_map('trim', $tagNames)));

            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (! empty($tagName)) {
                    $tagTax = Taxonomy::firstOrCreate(
                        [
                            'project_id' => $post->project_id ?? (function_exists('project_id') ? project_id() : null),
                            'taxonomy' => 'post_tag',
                            'slug' => Str::slug($tagName),
                        ],
                        [
                            'tenant_id' => $post->tenant_id ?? 3,
                            'name' => $tagName,
                            'status' => 'published',
                        ]
                    );
                    $tagIds[] = $tagTax->id;
                }
            }

            $allTagTaxIds = Taxonomy::whereIn('taxonomy', ['post_tag', 'tag'])->pluck('id')->toArray();
            DB::table('term_relationships')
                ->where('object_id', $post->id)
                ->whereIn('term_taxonomy_id', $allTagTaxIds)
                ->delete();

            foreach ($tagIds as $tagId) {
                DB::table('term_relationships')->insert([
                    'object_id' => $post->id,
                    'term_taxonomy_id' => $tagId,
                    'order' => 0,
                ]);
            }
        }

        if ($request->has('translations') || !empty($metaTitle)) {
            $translations = $request->input('translations', []);
            if (!isset($translations[$defaultLang])) {
                $translations[$defaultLang] = [];
            }
            if (!empty($metaTitle)) {
                $translations[$defaultLang]['meta_title'] = $metaTitle;
            }
            if (!empty($metaDescription)) {
                $translations[$defaultLang]['meta_description'] = $metaDescription;
            }
            if (!empty($defaultTitle)) {
                $translations[$defaultLang]['title'] = $defaultTitle;
            }
            if (!empty($rawContent)) {
                $translations[$defaultLang]['content'] = $rawContent;
            }
            if (!empty($rawExcerpt)) {
                $translations[$defaultLang]['excerpt'] = $rawExcerpt;
            }
            $post->saveTranslations($translations);
        }

        $route = Route::has('admin.posts.edit')
            ? route('admin.posts.edit', $post->slug ?: $post->id)
            : (Route::has('cms.posts.edit') ? route('cms.posts.edit', $post->slug ?: $post->id) : url('/admin/posts/' . $post->id . '/edit'));

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật '.($config['name'] ?? 'nội dung').' thành công!',
        ]);
    }

    public function destroy(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        $post->delete();

        $route = Route::has('admin.posts.index')
            ? route('admin.posts.index', ['type' => $postType])
            : (Route::has('cms.posts.index') ? route('cms.posts.index', ['type' => $postType]) : url('/admin/posts?type=' . $postType));

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Xóa '.($config['name'] ?? 'dữ liệu').' thành công!',
        ]);
    }

    public function restore(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $post->restore();
        $this->alertSuccess('Đã khôi phục dữ liệu thành công.');

        return redirect()->back();
    }

    public function forceDelete(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $post->forceDelete();
        $this->alertSuccess('Đã xóa vĩnh viễn dữ liệu thành công.');

        return redirect()->back();
    }
}

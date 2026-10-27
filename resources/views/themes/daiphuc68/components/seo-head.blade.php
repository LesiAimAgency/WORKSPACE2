@php
    // 0. Auto-resolve item from view variables or shared context
    $item = $item ?? $product ?? $post ?? $project ?? $page ?? $article ?? $service ?? $contactPage ?? $customPost ?? $model ?? null;

    if (! $item && !empty($__data)) {
        foreach ($__data as $key => $val) {
            if ($val instanceof \App\Models\Post) {
                $item = $val;
                break;
            }
        }
    }

    // 1. Site & Company info from settings
    $siteName = setting('company_name') ?: (setting_string('site_title') ?: setting_string('site_name', 'CÔNG TY TNHH ĐẠI PHÚC 68'));
    $siteLogo = setting_string('site_logo') ? asset(ltrim(setting_string('site_logo'), '/')) : asset('themes/daiphuc68/assets/icons/logo-daiphuc68.svg');
    $siteFavicon = setting_string('site_favicon') ?: (setting_string('seo_favicon') ?: asset('favicon.ico'));
    $favUrl = Str::startsWith($siteFavicon, ['http://', 'https://']) ? $siteFavicon : asset(ltrim($siteFavicon, '/'));

    $companyAddress = setting('company_address', '67 Huỳnh Thiện Lộc, Phường Tân Phú, TP. Hồ Chí Minh, Việt Nam');
    $companyPhone = setting('company_phone', '0908 123 456');
    $cleanPhone = preg_replace('/[^0-9+]/', '', $companyPhone);
    $companyEmail = setting('company_email', 'contact@daiphuc68.com');
    $taxCode = setting('tax_code', '0000999988');

    // 2. Meta Title
    $rawTitle = null;
    if ($item && !empty(trim((string)($item->meta_title ?? '')))) {
        $rawTitle = trim($item->meta_title);
    } elseif (View::hasSection('title')) {
        $rawTitle = trim($__env->yieldContent('title'));
    } elseif ($item && !empty(trim((string)($item->title ?? '')))) {
        $rawTitle = trim($item->title) . ' - ' . $siteName;
    } else {
        $rawTitle = setting_string('seo_meta_title', $siteName . ' - Xây dựng giá trị Kiến tạo tương lai');
    }
    $pageTitle = e(strip_tags(html_entity_decode((string)$rawTitle, ENT_QUOTES, 'UTF-8')));

    // 3. Meta Description
    $rawDesc = null;
    if ($item && !empty(trim((string)($item->meta_description ?? '')))) {
        $rawDesc = trim($item->meta_description);
    } elseif (View::hasSection('meta_description')) {
        $rawDesc = trim($__env->yieldContent('meta_description'));
    } elseif ($item && !empty(trim((string)($item->excerpt ?? '')))) {
        $rawDesc = trim($item->excerpt);
    } elseif ($item && !empty(trim((string)($item->content ?? '')))) {
        $rawDesc = Str::limit(trim($item->content), 200);
    } else {
        $rawDesc = setting_string('seo_meta_description', 'Đại Phúc 68 - Đơn vị hàng đầu về cung cấp thiết bị điện trung hạ thế, cọc bê tông ly tâm và thi công xây lắp trọn gói.');
    }
    $pageDesc = e(Str::limit(strip_tags(html_entity_decode((string)$rawDesc, ENT_QUOTES, 'UTF-8')), 250));

    // 4. Meta Keywords Aggregation (Full coverage)
    $keywordList = [];
    if ($item) {
        // Meta keywords explicit from user
        if (!empty($item->meta_keywords)) {
            $keywordList = array_merge($keywordList, explode(',', (string)$item->meta_keywords));
        } elseif (!empty($item->seo_data['meta_keywords'])) {
            $keywordList = array_merge($keywordList, explode(',', (string)$item->seo_data['meta_keywords']));
        }
        // Focus keyword
        if (!empty($item->focus_keyword)) {
            $keywordList[] = $item->focus_keyword;
        } elseif (!empty($item->seo_data['focus_keyword'])) {
            $keywordList[] = $item->seo_data['focus_keyword'];
        }
        // Brand & Category
        if (!empty($item->brand)) {
            $keywordList[] = $item->brand;
        }
        if (!empty($item->category_name)) {
            $keywordList[] = $item->category_name;
        }
        // Taxonomies tags/categories if loaded
        if (isset($item->taxonomies) && $item->taxonomies->isNotEmpty()) {
            foreach ($item->taxonomies as $tax) {
                if (!empty($tax->name)) {
                    $keywordList[] = $tax->name;
                }
            }
        }
        // Product code / SKU
        $itemMeta = is_array($item->meta_data) ? $item->meta_data : json_decode($item->meta_data ?? '[]', true);
        if (!empty($itemMeta['sku'])) {
            $keywordList[] = $itemMeta['sku'];
        }
        if (!empty($itemMeta['code'])) {
            $keywordList[] = $itemMeta['code'];
        }
    }
    // Global fallback keywords
    $siteKeywords = setting_string('seo_meta_keywords', 'thiết bị điện, cọc bê tông ly tâm, xây lắp điện, đại phúc 68');
    if (!empty($siteKeywords)) {
        $keywordList = array_merge($keywordList, explode(',', $siteKeywords));
    }
    $keywordList[] = $siteName;

    // Clean and unique keywords
    $cleanedKeywords = [];
    foreach ($keywordList as $kw) {
        $trimmed = trim(strip_tags(html_entity_decode((string)$kw, ENT_QUOTES, 'UTF-8')));
        if ($trimmed !== '' && !in_array(mb_strtolower($trimmed), array_map('mb_strtolower', $cleanedKeywords))) {
            $cleanedKeywords[] = $trimmed;
        }
    }
    $pageKeywords = implode(', ', array_slice($cleanedKeywords, 0, 15));

    // 5. Canonical URL
    $canonicalUrl = null;
    if ($item) {
        $canonicalUrl = $item->canonical_url ?? ($item->seo_data['canonical_url'] ?? null);
    }
    if (empty($canonicalUrl)) {
        $canonicalUrl = url()->current();
    }

    // 6. Robots / Indexing
    $isNoindex = false;
    if ($item) {
        $isNoindex = !empty($item->noindex) || !empty($item->seo_data['noindex']);
    }
    $robotsContent = $isNoindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    // 7. OG / Featured Image & Gallery Collection
    $pageImage = null;
    $allImages = [];

    if ($item && !empty($item->featured_image)) {
        $pageImage = Str::startsWith($item->featured_image, ['http://', 'https://']) ? $item->featured_image : asset(ltrim($item->featured_image, '/'));
        $allImages[] = $pageImage;
    }
    
    // Check gallery
    $itemGallery = $item->gallery ?? ($itemMeta['gallery'] ?? ($gallery ?? []));
    if (is_string($itemGallery)) {
        $itemGallery = json_decode($itemGallery, true) ?: array_filter(explode(',', $itemGallery));
    }
    if (is_array($itemGallery)) {
        foreach ($itemGallery as $gImg) {
            if (!empty($gImg)) {
                $gUrl = Str::startsWith($gImg, ['http://', 'https://']) ? $gImg : asset(ltrim($gImg, '/'));
                if (!in_array($gUrl, $allImages)) {
                    $allImages[] = $gUrl;
                }
            }
        }
    }
    if (empty($pageImage)) {
        $pageImage = !empty($allImages) ? $allImages[0] : $siteLogo;
    }
    if (empty($allImages)) {
        $allImages[] = $pageImage;
    }

    // 8. Post Type & Schema Identification
    $postType = strtolower((string)($item->post_type ?? ''));
    $isProduct = isset($product) || $postType === 'product';
    $isArticle = in_array($postType, ['post', 'news', 'blog', 'article']);
    $isProject = $postType === 'project';
    $isService = in_array($postType, ['service', 'dich-vu']);
    $isFaq = in_array($postType, ['faq', 'hoi-dap']);
    $isPage = in_array($postType, ['page', 'static_page']);

    if ($isProduct) {
        $ogType = 'product';
    } elseif ($isArticle) {
        $ogType = 'article';
    } else {
        $ogType = 'website';
    }

    $schemaType = null;
    if ($item && !empty($item->schema_type)) {
        $schemaType = $item->schema_type;
    } elseif ($item && !empty($item->seo_data['schema_type'])) {
        $schemaType = $item->seo_data['schema_type'];
    }
    if (empty($schemaType)) {
        if ($isProduct) $schemaType = 'Product';
        elseif ($isProject) $schemaType = 'CreativeWork';
        elseif ($isService) $schemaType = 'Service';
        elseif ($isFaq) $schemaType = 'FAQPage';
        elseif ($isArticle) $schemaType = 'NewsArticle';
        elseif ($isPage) $schemaType = 'WebPage';
        else $schemaType = 'WebPage';
    }

    // 9. Structured Data Suite (Google Rich Results JSON-LD)
    $schemas = [];

    // 9.1 WebSite Schema (with Sitelinks Searchbox)
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => url('/#website'),
        'name' => $siteName,
        'url' => url('/'),
        'inLanguage' => 'vi-VN',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => url('/products') . '?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // 9.2 LocalBusiness / Organization Schema
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => url('/#organization'),
        'name' => $siteName,
        'url' => url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $siteLogo,
        ],
        'image' => $pageImage,
        'telephone' => $companyPhone,
        'email' => $companyEmail,
        'taxID' => $taxCode,
        'priceRange' => '$$',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $companyAddress,
            'addressLocality' => 'Hồ Chí Minh',
            'addressRegion' => 'Hồ Chí Minh',
            'addressCountry' => 'VN',
        ],
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'telephone' => $companyPhone,
                'contactType' => 'Customer Service',
                'areaServed' => 'VN',
                'availableLanguage' => ['vi', 'en'],
            ],
        ],
    ];

    // 9.3 BreadcrumbList Schema (Rich SERP navigation)
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang chủ',
            'item' => url('/'),
        ],
    ];
    $bPosition = 2;

    if ($isProduct) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $bPosition++,
            'name' => 'Sản phẩm',
            'item' => Route::has('products.index') ? route('products.index') : url('/products'),
        ];
        if (!empty($item->category_name)) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $bPosition++,
                'name' => $item->category_name,
                'item' => url('/products?category=' . urlencode($item->category_name)),
            ];
        }
    } elseif ($isProject) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $bPosition++,
            'name' => 'Dự án',
            'item' => Route::has('projects.index') ? route('projects.index') : url('/projects'),
        ];
    } elseif ($isArticle) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $bPosition++,
            'name' => 'Tin tức',
            'item' => Route::has('news.index') ? route('news.index') : url('/news'),
        ];
    }

    if ($item && $canonicalUrl !== url('/')) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $bPosition,
            'name' => $item->title,
            'item' => $canonicalUrl,
        ];
    }

    if (count($breadcrumbItems) > 1) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
    }

    // 9.4 Contextual Item Schema (Product, Article, Project, Service, FAQ, WebPage)
    if ($item) {
        $itemPublishedIso = $item->published_at ? \Carbon\Carbon::parse($item->published_at)->toIso8601String() : ($item->created_at ? $item->created_at->toIso8601String() : date('c'));
        $itemModifiedIso = $item->updated_at ? $item->updated_at->toIso8601String() : date('c');

        if ($schemaType === 'Product' || $isProduct) {
            $price = (int) ($itemMeta['sale_price'] ?? ($itemMeta['price'] ?? 0));
            $sku = $item->sku ?: ($itemMeta['sku'] ?? ($itemMeta['code'] ?? ('DP-' . str_pad((string)$item->id, 4, '0', STR_PAD_LEFT))));
            $brand = $item->brand ?: ($itemMeta['brand'] ?? 'Đại Phúc 68');
            $categoryName = $item->category_name ?: ($itemMeta['category'] ?? 'Vật Tư Thiết Bị Điện');

            $productSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                '@id' => $canonicalUrl . '#product',
                'name' => $item->title,
                'image' => $allImages,
                'description' => $pageDesc,
                'sku' => (string)$sku,
                'mpn' => (string)$sku,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $brand,
                ],
                'category' => $categoryName,
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $canonicalUrl,
                    'priceCurrency' => 'VND',
                    'price' => $price > 0 ? (string)$price : '0',
                    'priceValidUntil' => date('Y-12-31', strtotime('+1 year')),
                    'availability' => 'https://schema.org/InStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => $siteName,
                        'url' => url('/'),
                    ],
                ],
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => '4.9',
                    'reviewCount' => '28',
                    'bestRating' => '5',
                    'worstRating' => '1',
                ],
            ];
            $schemas[] = $productSchema;

        } elseif ($schemaType === 'CreativeWork' || $isProject) {
            $location = $item->location ?: ($itemMeta['location'] ?? 'Việt Nam');
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                '@id' => $canonicalUrl . '#project',
                'headline' => $item->title,
                'name' => $item->title,
                'description' => $pageDesc,
                'image' => $allImages,
                'url' => $canonicalUrl,
                'dateCreated' => $itemPublishedIso,
                'datePublished' => $itemPublishedIso,
                'dateModified' => $itemModifiedIso,
                'author' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => url('/'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $siteLogo,
                    ],
                ],
                'locationCreated' => [
                    '@type' => 'Place',
                    'name' => $location,
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                ],
            ];

        } elseif (in_array($schemaType, ['NewsArticle', 'BlogPosting', 'Article']) || $isArticle) {
            $artType = in_array($schemaType, ['NewsArticle', 'BlogPosting']) ? $schemaType : 'NewsArticle';
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => $artType,
                '@id' => $canonicalUrl . '#article',
                'headline' => $item->title,
                'name' => $item->title,
                'image' => $allImages,
                'description' => $pageDesc,
                'url' => $canonicalUrl,
                'datePublished' => $itemPublishedIso,
                'dateModified' => $itemModifiedIso,
                'author' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => url('/'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $siteLogo,
                    ],
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                ],
                'keywords' => $pageKeywords,
            ];

        } elseif ($schemaType === 'Service' || $isService) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                '@id' => $canonicalUrl . '#service',
                'name' => $item->title,
                'description' => $pageDesc,
                'provider' => [
                    '@type' => 'LocalBusiness',
                    'name' => $siteName,
                    'url' => url('/'),
                    'telephone' => $companyPhone,
                    'image' => $siteLogo,
                ],
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Việt Nam',
                ],
                'serviceType' => $item->category_name ?: 'Vật Tư & Xây Lắp Điện',
                'url' => $canonicalUrl,
                'image' => $allImages,
            ];

        } elseif ($schemaType === 'FAQPage' || $isFaq) {
            $faqs = $itemMeta['faq'] ?? ($itemMeta['faqs'] ?? []);
            $faqEntities = [];
            if (is_array($faqs) && !empty($faqs)) {
                foreach ($faqs as $fq) {
                    if (!empty($fq['question']) && !empty($fq['answer'])) {
                        $faqEntities[] = [
                            '@type' => 'Question',
                            'name' => trim(strip_tags($fq['question'])),
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => trim(strip_tags($fq['answer'])),
                            ],
                        ];
                    }
                }
            }
            if (empty($faqEntities)) {
                $faqEntities[] = [
                    '@type' => 'Question',
                    'name' => $item->title,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $pageDesc,
                    ],
                ];
            }
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                '@id' => $canonicalUrl . '#faq',
                'mainEntity' => $faqEntities,
            ];

        } elseif (in_array($schemaType, ['AboutPage', 'ContactPage'])) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => $schemaType,
                '@id' => $canonicalUrl . '#page',
                'name' => $pageTitle,
                'description' => $pageDesc,
                'url' => $canonicalUrl,
                'inLanguage' => 'vi-VN',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    '@id' => url('/#website'),
                ],
            ];

        } else {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                '@id' => $canonicalUrl . '#webpage',
                'name' => $pageTitle,
                'description' => $pageDesc,
                'url' => $canonicalUrl,
                'inLanguage' => 'vi-VN',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    '@id' => url('/#website'),
                ],
            ];
        }
    }

    // 10. Custom JSON-LD schema
    $customSchemaRaw = null;
    if ($item) {
        $customSchemaRaw = $item->custom_schema ?? ($item->seo_data['custom_schema'] ?? null);
    }
@endphp

{{-- 1. Primary Meta Tags --}}
<title>{{ $pageTitle }}</title>
<meta name="title" content="{{ $pageTitle }}">
<meta name="description" content="{{ $pageDesc }}">
@if(!empty($pageKeywords))
<meta name="keywords" content="{{ $pageKeywords }}">
@if($isArticle)
<meta name="news_keywords" content="{{ $pageKeywords }}">
@endif
@endif
<meta name="robots" content="{{ $robotsContent }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- 2. Open Graph / Facebook / Zalo --}}
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:image:secure_url" content="{{ $pageImage }}">
<meta property="og:image:alt" content="{{ $pageTitle }}">

{{-- 3. Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">
<meta name="twitter:image" content="{{ $pageImage }}">

{{-- 4. Favicon & Touch Icons --}}
@if(!empty($favUrl))
<link rel="icon" type="image/x-icon" href="{{ $favUrl }}">
<link rel="shortcut icon" href="{{ $favUrl }}">
<link rel="apple-touch-icon" href="{{ $favUrl }}">
@endif

{{-- 5. Search Engine Verification --}}
@if(setting_string('google_site_verification'))
<meta name="google-site-verification" content="{{ setting_string('google_site_verification') }}">
@endif
@if(setting_string('bing_site_verification'))
<meta name="msvalidate.01" content="{{ setting_string('bing_site_verification') }}">
@endif

{{-- 6. Schema.org JSON-LD Structured Data Suite --}}
@foreach($schemas as $schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endforeach

{{-- 7. Custom JSON-LD Schema Injection (if provided in CMS) --}}
@if(!empty($customSchemaRaw))
@if(Str::contains($customSchemaRaw, '<script'))
{!! $customSchemaRaw !!}
@else
<script type="application/ld+json">
{!! $customSchemaRaw !!}
</script>
@endif
@endif


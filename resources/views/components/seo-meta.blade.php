@php
$metaTitle = $title ?? setting_string('seo_meta_title', config('app.name', ''));
$metaDescription = $description ?? setting_string('seo_meta_description', 'Chào mừng đến với ' . config('app.name'));
$metaKeywords = $keywords ?? setting_string('seo_meta_keywords');
$metaImage = $image ?? (setting_string('site_logo') ? asset(setting_string('site_logo')) : asset('images/default-og.jpg'));
$metaUrl = $url ?? url()->current();
$siteName = setting_string('site_name', config('app.name', ''));
$locale = app()->getLocale();
$robotsContent = setting_string('seo_robots', 'index, follow');
$twitterUsername = setting_string('twitter_username');
$siteFavicon = setting_string('site_favicon') ?: setting_string('seo_favicon');
$googleSiteVerification = setting_string('google_site_verification');
$bingSiteVerification = setting_string('bing_site_verification');
$yandexVerification = setting_string('yandex_verification');
$customHeaderCode = setting_string('custom_header_code');
$customBoddyCode = setting_string('custom_boddy_code');
$customfooterCode = setting_string('custom_footer_code');

$organizationData = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => url('/'),
    'logo' => $metaImage,
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => setting_string('hotline', ''),
        'contactType' => 'Customer Service',
        'areaServed' => 'VN',
        'availableLanguage' => ['vi', 'en'],
    ],
    'sameAs' => array_filter([
        setting_string('facebook_url'),
        setting_string('twitter_url'),
        setting_string('instagram_url'),
        setting_string('youtube_url'),
        setting_string('linkedin_url'),
    ]),
];

$breadcrumbJson = '';
if (isset($breadcrumbs) && count($breadcrumbs) > 0) {
    $items = [];
    foreach ($breadcrumbs as $index => $crumb) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ];
    }
    $breadcrumbJson = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
@endphp

{{-- Primary Meta Tags --}}
<title>{{ $metaTitle }}</title>
<meta name="title" content="{{ $metaTitle }}">
<meta name="description" content="{{ $metaDescription }}">
@if($metaKeywords)
<meta name="keywords" content="{{ $metaKeywords }}">
@endif
<meta name="author" content="{{ $siteName }}">
<meta name="robots" content="{{ $robotsContent }}">
<meta name="language" content="{{ $locale }}">
<meta name="revisit-after" content="7 days">
<link rel="canonical" href="{{ $metaUrl }}">

{{-- Open Graph --}}
<meta property="og:locale" content="{{ $locale == 'vi' ? 'vi_VN' : ($locale == 'en' ? 'en_US' : 'zh_CN') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:secure_url" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $metaTitle }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $metaUrl }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
@if($twitterUsername)
<meta name="twitter:site" content="@{{ $twitterUsername }}">
<meta name="twitter:creator" content="@{{ $twitterUsername }}">
@endif

{{-- Favicon --}}
@if($siteFavicon)
@php
    $favUrl = Str::startsWith($siteFavicon, ['http://', 'https://']) ? $siteFavicon : asset(ltrim($siteFavicon, '/'));
@endphp
<link rel="icon" type="image/x-icon" href="{{ $favUrl }}">
<link rel="shortcut icon" href="{{ $favUrl }}">
<link rel="apple-touch-icon" href="{{ $favUrl }}">
@endif

{{-- Search Engine Verification --}}
@if($googleSiteVerification)
<meta name="google-site-verification" content="{{ $googleSiteVerification }}">
@endif
@if($bingSiteVerification)
<meta name="msvalidate.01" content="{{ $bingSiteVerification }}">
@endif
@if($yandexVerification)
<meta name="yandex-verification" content="{{ $yandexVerification }}">
@endif

{{-- Structured Data (JSON-LD) --}}
<script type="application/ld+json">
{!! json_encode($organizationData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@if($breadcrumbJson)
<script type="application/ld+json">
{!! $breadcrumbJson !!}
</script>
@endif

{{-- Custom Header Code from Settings --}}
@if($customHeaderCode)
{!! $customHeaderCode !!}
@endif

<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/' => 'Đại Phúc 68 - Homepage (Onepage Showcase)',
    '/about-us' => 'Đại Phúc 68 - About Us',
    '/products' => 'Đại Phúc 68 - Products Catalog',
    '/products/cau-chi-tu-roi-vina-electric' => 'Đại Phúc 68 - Product Detail',
    '/projects' => 'Đại Phúc 68 - Projects Catalog',
    '/projects/khach-san-marriott-ha-noi' => 'Đại Phúc 68 - Project Detail',
    '/news' => 'Đại Phúc 68 - News Catalog',
    '/news/dai-phuc-68-hop-tac-schneider-electric' => 'Đại Phúc 68 - News Detail',
    '/contact' => 'Đại Phúc 68 - Contact & FAQs',
];

echo "====================================================\n";
echo "VERIFYING PURE ĐẠI PHÚC 68 SINGLE PROJECT ROUTES:\n";
echo "====================================================\n";

$allPass = true;
foreach ($urls as $url => $name) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();
    
    $statusText = $status === 200 ? "PASS (200 OK)" : "FAIL ({$status})";
    echo sprintf("[%-13s] %-45s | URL: %s\n", $statusText, $name, $url);
    
    if ($status !== 200) {
        $allPass = false;
        echo "Error preview: " . substr($content, 0, 400) . "\n\n";
    }
}


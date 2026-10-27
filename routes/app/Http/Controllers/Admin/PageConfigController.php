<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PageConfigController extends Controller
{
    public function index()
    {
        $pages = [
            'home' => 'Trang chủ',
            'products' => 'Sản phẩm',
            'categories' => 'Danh mục',
            'blog' => 'Blog',
            'contact' => 'Liên hệ',
            'about' => 'Giới thiệu',
        ];

        return view('cms.page-config.index', compact('pages'));
    }

    public function edit($page)
    {
        $config = Setting::where('key', "page_config_{$page}")->first();
        $settings = $config ? $config->payload : [];

        return view('cms.page-config.edit', compact('page', 'settings'));
    }

    public function update(Request $request, $page)
    {
        $validated = $request->validate([
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
        ]);

        $tenantId = session('current_tenant_id');

        Setting::updateOrCreate(
            ['key' => "page_config_{$page}", 'tenant_id' => $tenantId],
            ['payload' => $validated]
        );

        return redirect()->back()->with('success', 'Cấu hình đã được lưu!');
    }
}

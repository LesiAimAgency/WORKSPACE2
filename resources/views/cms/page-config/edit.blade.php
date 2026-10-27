@extends('cms.layouts.app')

@section('title', 'Cấu hình ' . ucfirst($page))
@section('page-title', 'Cấu hình ' . ucfirst($page))

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($currentProject) ? route('project.admin.page-config.update', [$currentProject->code, $page]) : route('cms.page-config.update', $page) }}">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium mb-2">SEO Title</label>
                <input type="text" name="seo_title" value="{{ $settings['seo_title'] ?? '' }}" 
                       class="w-full px-3 py-2 border rounded-lg" placeholder="Tiêu đề SEO">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">SEO Description</label>
                <textarea name="seo_description" rows="3" class="w-full px-3 py-2 border rounded-lg" 
                          placeholder="Mô tả SEO">{{ $settings['seo_description'] ?? '' }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">SEO Keywords</label>
                <input type="text" name="seo_keywords" value="{{ $settings['seo_keywords'] ?? '' }}" 
                       class="w-full px-3 py-2 border rounded-lg" placeholder="keyword1, keyword2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Banner Image URL</label>
                <input type="text" name="banner_image" value="{{ $settings['banner_image'] ?? '' }}" 
                       class="w-full px-3 py-2 border rounded-lg" placeholder="https://...">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Custom CSS</label>
                <textarea name="custom_css" rows="5" class="w-full px-3 py-2 border rounded-lg font-mono text-sm" 
                          placeholder=".custom-class { }">{{ $settings['custom_css'] ?? '' }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Custom JS</label>
                <textarea name="custom_js" rows="5" class="w-full px-3 py-2 border rounded-lg font-mono text-sm" 
                          placeholder="console.log('hello');">{{ $settings['custom_js'] ?? '' }}</textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ isset($currentProject) ? route('project.admin.page-config.index', $currentProject->code) : route('cms.page-config.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Quay lại</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>
@endsection

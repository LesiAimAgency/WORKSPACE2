@extends('cms.layouts.app')

@section('title', 'Custom CSS')
@section('page-title', 'Custom CSS')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 font-medium">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Quay lại
    </a>
</div>

<form method="POST" action="{{ route('admin.settings.save') }}" class="space-y-6">
    @csrf
    
    <!-- Theme Colors -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Theme Colors</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                <input type="color" name="settings[primary_color]" value="{{ $settings['primary_color'] ?? '#98191F' }}" class="w-full h-12 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                <input type="color" name="settings[secondary_color]" value="{{ $settings['secondary_color'] ?? '#1a1a1a' }}" class="w-full h-12 rounded border">
            </div>
        </div>
    </div>

    <!-- Custom CSS -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Custom CSS & JavaScript</h3>
        <p class="text-sm text-gray-600 mb-4">Viết CSS tùy chỉnh cho project này. CSS sẽ được load sau Tailwind.</p>
        
        <textarea name="settings[custom_css]" rows="20" class="w-full px-4 py-2 border rounded-lg font-mono text-sm" placeholder="/* Your custom CSS here */
.hero-section-1 {
    background: linear-gradient(135deg, var(--project-primary), var(--project-secondary));
}

.custom-button {
    background-color: var(--project-primary);
    padding: 12px 24px;
    border-radius: 8px;
}">{{ $settings['custom_css'] ?? '' }}</textarea>

        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800"><strong>Tip:</strong> Sử dụng CSS variables:</p>
            <ul class="text-sm text-blue-700 mt-2 space-y-1">
                <li>• <code class="bg-blue-100 px-2 py-1 rounded">var(--project-primary)</code> - Primary color</li>
                <li>• <code class="bg-blue-100 px-2 py-1 rounded">var(--project-secondary)</code> - Secondary color</li>
            </ul>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Custom JavaScript</h3>
        <p class="text-sm text-gray-600 mb-4">Viết JavaScript tùy chỉnh cho project này.</p>
        
        <textarea name="settings[custom_js]" rows="15" class="w-full px-4 py-2 border rounded-lg font-mono text-sm" placeholder="// Your custom JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom JS loaded for project');
    
    // Your code here
});">{{ $settings['custom_js'] ?? '' }}</textarea>

        <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
            <p class="text-sm text-yellow-800"><strong>Note:</strong> JavaScript sẽ được load trước Alpine.js</p>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.settings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419]">
            Lưu CSS
        </button>
    </div>
</form>

@push('scripts')
<script>
// Preview color changes
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('change', function() {
        const varName = this.name.includes('primary') ? '--project-primary' : '--project-secondary';
        document.documentElement.style.setProperty(varName, this.value);
    });
});
</script>
@endpush
@endsection

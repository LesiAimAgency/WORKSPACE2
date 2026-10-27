<div class="space-y-6">
    <!-- Banner Height -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Height</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Chiều cao banner (px)</label>
            <input type="number" name="banner_height" value="{{ $data['banner_height'] ?? '220' }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <!-- Banner Style -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Style</h3>
        <div class="flex gap-4">
            @foreach(['left' => 'Banner Left', 'center' => 'Banner Center', 'right' => 'Banner Right'] as $key => $label)
            <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400 {{ ($data['banner_style'] ?? 'center') == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                <input type="radio" name="banner_style" value="{{ $key }}" {{ ($data['banner_style'] ?? 'center') == $key ? 'checked' : '' }} class="hidden">
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Banner Page -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Page</h3>
        <div class="flex gap-4">
            @foreach(['container' => 'Container Banner', 'full-width' => 'Full Width Banner'] as $key => $label)
            <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400 {{ ($data['banner_page'] ?? 'container') == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                <input type="radio" name="banner_page" value="{{ $key }}" {{ ($data['banner_page'] ?? 'container') == $key ? 'checked' : '' }} class="hidden">
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Banner Post Category -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Post Category</h3>
        <div class="flex gap-4">
            @foreach(['container' => 'Container Banner', 'full-width' => 'Full Width Banner'] as $key => $label)
            <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400 {{ ($data['banner_post_category'] ?? 'container') == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                <input type="radio" name="banner_post_category" value="{{ $key }}" {{ ($data['banner_post_category'] ?? 'container') == $key ? 'checked' : '' }} class="hidden">
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Banner Post -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Post</h3>
        <div class="flex gap-4">
            @foreach(['container' => 'Container Banner', 'full-width' => 'Full Width Banner'] as $key => $label)
            <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400 {{ ($data['banner_post'] ?? 'container') == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                <input type="radio" name="banner_post" value="{{ $key }}" {{ ($data['banner_post'] ?? 'container') == $key ? 'checked' : '' }} class="hidden">
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Banner Product Category -->
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-3">Banner Product Category</h3>
        <div class="flex gap-4">
            @foreach(['container' => 'Container Banner', 'full-width' => 'Full Width Banner'] as $key => $label)
            <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400 {{ ($data['banner_product_category'] ?? 'container') == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                <input type="radio" name="banner_product_category" value="{{ $key }}" {{ ($data['banner_product_category'] ?? 'container') == $key ? 'checked' : '' }} class="hidden">
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const name = this.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const label = r.closest('label');
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-300');
            });
            
            const label = this.closest('label');
            label.classList.remove('border-gray-300');
            label.classList.add('border-blue-500', 'bg-blue-50');
        });
    });
});
</script>

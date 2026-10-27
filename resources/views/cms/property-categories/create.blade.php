@extends('cms.layouts.app')

@section('title', 'Thêm danh mục mới')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Thêm danh mục mới</h1>
            <p class="text-gray-600">Tạo danh mục sản phẩm mới cho hệ thống</p>
        </div>
        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.index', $currentProject->code) : route('cms.property-categories.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại
        </a>
    </div>

    <!-- Alert Component -->
    @include('cms.components.alert')

    <!-- Form -->
    <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.store', $currentProject->code) : route('cms.property-categories.store') }}" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" 
          x-data="{ 
              image: '{{ old('image') }}',
              updateImage(url) {
                  this.image = url;
                  console.log('Image updated via Alpine method:', url);
              }
          }"
          @update-image.window="updateImage($event.detail.url)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
            <!-- Left Column: Main Information -->
            <div class="lg:col-span-2 p-6 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h2>
                    
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tên danh mục <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Nhập tên danh mục">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" 
                                   name="slug" 
                                   value="{{ old('slug') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Tự động tạo từ tên (có thể để trống)">
                            <p class="text-sm text-gray-500 mt-1">Để trống để tự động tạo từ tên danh mục</p>
                        </div>

                        <!-- Parent property_category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục cha</label>
                            <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Danh mục gốc (Cấp 1)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ str_repeat('—', $parent->level) }} {{ $parent->name }} (Cấp {{ $parent->level + 1 }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">Hệ thống hỗ trợ tối đa 4 cấp độ (0, 1, 2, 3)</p>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thứ tự sắp xếp</label>
                            <input type="number" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', 0) }}" 
                                   min="0" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-sm text-gray-500 mt-1">Số thứ tự hiển thị (0 = đầu tiên)</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <x-quill-editor name="description" :value="old('description')" height="200px" />
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SEO Fields -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Tối ưu SEO</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề SEO</label>
                            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('meta_title') border-red-500 @enderror"
                                   maxlength="255" placeholder="Tiêu đề tối ưu cho công cụ tìm kiếm">
                            @error('meta_title')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả SEO</label>
                            <textarea id="meta_description" name="meta_description" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('meta_description') border-red-500 @enderror"
                                      maxlength="500" placeholder="Mô tả ngắn gọn về danh mục cho kết quả tìm kiếm">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image & Settings -->
            <div class="lg:col-span-1 bg-gray-50 p-6 space-y-6">
                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh danh mục</label>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-white aspect-video flex items-center justify-center mb-3">
                        <template x-if="image">
                            <img :src="image" class="w-full h-full object-contain p-4">
                        </template>
                        <template x-if="!image">
                            <div class="text-center text-gray-400 p-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có hình ảnh</p>
                                <p class="text-xs text-gray-500 mt-1">Chọn hình ảnh từ thư viện</p>
                            </div>
                        </template>
                    </div>
                    
                    <input type="hidden" name="image" x-model="image">
                    
                    <div class="flex gap-2">
                        @include('cms.components.media-manager', ['slot' => 'Chọn hình ảnh từ thư viện'])
                        <button type="button" x-show="image" @click="image = ''; console.log('Image cleared:', image);" class="px-3 py-2 text-red-600 border border-red-300 rounded-lg hover:bg-red-50 text-sm">
                            Xóa hình ảnh
                        </button>
                    </div>
                    
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }} 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Kích hoạt danh mục</span>
                            <span class="block text-xs text-gray-500">Danh mục sẽ hiển thị trên website</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 px-6 py-4 bg-gray-50 border-t">
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.property-categories.index', $currentProject->code) : route('cms.property-categories.index') }}" 
               class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Thêm danh mục
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('property_category create page loaded');
    
    // Listen for media selection from media manager
    window.addEventListener('media-selected', function(e) {
        console.log('Media selected event received:', e.detail);
        
        const items = e.detail.files || e.detail.items || [];
        console.log('Items:', items);
        
        if (items.length > 0) {
            const imageUrl = items[0].url;
            console.log('Selected image URL:', imageUrl);
            
            // Try multiple methods to update the image
            
            // Method 1: Direct Alpine.js update
            try {
                const form = document.querySelector('form[x-data]');
                if (form && form.__x) {
                    form.__x.$data.image = imageUrl;
                    console.log('Updated via Alpine.js __x');
                }
            } catch (error) {
                console.error('Method 1 failed:', error);
            }
            
            // Method 2: Update hidden input directly
            try {
                const hiddenInput = document.querySelector('input[name="image"]');
                if (hiddenInput) {
                    hiddenInput.value = imageUrl;
                    hiddenInput.dispatchEvent(new Event('input'));
                    console.log('Updated hidden input directly');
                }
            } catch (error) {
                console.error('Method 2 failed:', error);
            }
            
            // Method 3: Dispatch Alpine event
            try {
                window.dispatchEvent(new CustomEvent('update-image', {
                    detail: { url: imageUrl }
                }));
                console.log('Dispatched update-image event');
            } catch (error) {
                console.error('Method 3 failed:', error);
            }
        }
    });
    
    // Handle image clearing
    const clearImageButton = document.querySelector('button[x-show="image"]');
    if (clearImageButton) {
        clearImageButton.addEventListener('click', function() {
            console.log('Clear image button clicked');
            
            // Clear Alpine.js data
            const form = document.querySelector('form[x-data]');
            if (form && form.__x) {
                form.__x.$data.image = '';
                console.log('Cleared Alpine.js image data');
            }
            
            // Clear hidden input
            const hiddenInput = document.querySelector('input[name="image"]');
            if (hiddenInput) {
                hiddenInput.value = '';
                console.log('Cleared hidden input value');
            }
        });
    }
});
</script>
@endpush

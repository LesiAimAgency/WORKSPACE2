{{-- MODIFIED: 2025-12-20 --}}
@extends('cms.layouts.app')

@section('title', 'Thêm thương hiệu')
@section('page-title', 'Thêm thương hiệu mới')

@section('content')
@include('cms.components.alert')

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6" x-data="brandForm()">
    <form method="POST" action="{{ route('project.admin.brands.store', request()->route('projectCode')) }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Cột trái: Thông tin cơ bản -->
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên thương hiệu *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required oninput="generateSlug()">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug (tự động tạo nếu để trống)</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
                    @error('slug')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <x-quill-editor name="description" :value="old('description')" height="200px" />
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Kích hoạt thương hiệu</span>
                    </label>
                </div>
            </div>

            <!-- Cột phải: Logo và SEO -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo thương hiệu</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 aspect-video flex items-center justify-center mb-3">
                        <template x-if="logo">
                            <img :src="logo" class="w-full h-full object-contain p-4">
                        </template>
                        <template x-if="!logo">
                            <div class="text-center text-gray-400 p-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Chưa có logo</p>
                                <p class="text-xs text-gray-500 mt-1">Chọn hình ảnh từ thư viện</p>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="logo" x-model="logo">
                    <div class="flex gap-2">
                        @include('cms.components.media-manager', ['slot' => 'Chọn logo từ thư viện'])
                        <button type="button" x-show="logo" @click="logo = ''" class="px-3 py-2 text-red-600 border border-red-300 rounded-lg hover:bg-red-50 text-sm">
                            Xóa logo
                        </button>
                    </div>
                    @error('logo')
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
                                      maxlength="500" placeholder="Mô tả ngắn gọn về thương hiệu cho kết quả tìm kiếm">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
            <a href="{{ route('project.admin.brands.index', request()->route('projectCode')) }}" class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Thêm thương hiệu
            </button>
        </div>
    </form>
</div>

<script>
function brandForm() {
    return {
        logo: '{{ old('logo') }}',
        
        init() {
            window.addEventListener('media-selected', (e) => {
                const items = e.detail.files || e.detail.items || [];
                if (items.length > 0) {
                    this.logo = items[0].url;
                }
            });
        }
    }
}

function generateSlug() {
    const name = document.getElementById('name').value;
    const slugField = document.getElementById('slug');
    if (!slugField.value) {
        slugField.value = name.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd').replace(/Đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    }
}
</script>
@endsection

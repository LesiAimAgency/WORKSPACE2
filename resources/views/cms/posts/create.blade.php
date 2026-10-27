@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Thêm ' . ($config['name'] ?? 'dữ liệu') . ' mới')
@section('page-title', 'Tạo ' . ($config['name'] ?? 'dữ liệu'))

@section('content')
@php
    $projCode = is_object($currentProject ?? null) ? ($currentProject->code ?? null) : (is_string($currentProject ?? null) ? $currentProject : null);

    $actionUrl = Route::has('admin.posts.store') 
        ? route('admin.posts.store') 
        : ($projCode && Route::has('project.admin.posts.store') 
            ? route('project.admin.posts.store', $projCode) 
            : url('/admin/posts'));
    
    $indexUrl = Route::has('admin.posts.index') 
        ? route('admin.posts.index', ['type' => $postType]) 
        : ($projCode && Route::has('project.admin.posts.index') 
            ? route('project.admin.posts.index', ['projectCode' => $projCode, 'type' => $postType]) 
            : url('/admin/posts?type=' . $postType));

    $projectGalleryCreate = old('meta_data.gallery', []);
    if (!is_array($projectGalleryCreate)) {
        $projectGalleryCreate = array_values(array_filter(array_map('trim', explode("\n", (string)$projectGalleryCreate))));
    }
@endphp

<form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data" novalidate>
    @csrf
    <input type="hidden" name="post_type" value="{{ $postType }}">
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tạo {{ $config['name'] ?? 'nội dung' }} mới</h1>
                <p class="text-sm text-gray-500">Nhập thông tin {{ $config['name'] ?? 'nội dung' }}</p>
            </div>
            <div class="flex items-center gap-3">
                @include('cms.components.ai-modal', ['postType' => $postType, 'currentLang' => $currentLang ?? 'vi'])
                <a href="{{ $indexUrl }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lưu {{ $config['name'] ?? 'dữ liệu' }}
                </button>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            <div class="font-bold flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>Có lỗi xảy ra, vui lòng kiểm tra lại:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái: Form chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" x-model="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Để trống để tự động tạo từ tiêu đề</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung chính</h2>
                
                @if(in_array('title', $config['supports'] ?? []))
                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ ($postType ?? '') === 'faq' ? 'Câu hỏi (Tiêu đề) *' : 'Tiêu đề *' }}
                    </label>
                    <input type="text" name="translations[{{ $currentLang ?? 'vi' }}][title]" value="{{ old('translations.'.($currentLang ?? 'vi').'.title') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="{{ ($postType ?? '') === 'faq' ? 'Nhập câu hỏi thường gặp...' : 'Nhập tiêu đề...' }}">
                </div>
                @endif

                @if(in_array('excerpt', $config['supports'] ?? []))
                <!-- Tóm tắt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ ($postType ?? '') === 'faq' ? 'Tóm tắt câu trả lời (Ngắn gọn)' : 'Tóm tắt' }}</label>
                    <textarea name="translations[{{ $currentLang ?? 'vi' }}][excerpt]" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="{{ ($postType ?? '') === 'faq' ? 'Tóm tắt ngắn gọn câu trả lời 1-2 câu...' : 'Nhập tóm tắt...' }}">{{ old('translations.'.($currentLang ?? 'vi').'.excerpt') }}</textarea>
                </div>
                @endif

                @if(in_array('content', $config['supports'] ?? []))
                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ ($postType ?? '') === 'faq' ? 'Câu trả lời chi tiết *' : 'Nội dung chi tiết' }}
                    </label>
                    <div class="ckeditor-container">
                        <textarea name="translations[{{ $currentLang ?? 'vi' }}][content]" id="post_content_{{ $currentLang ?? 'vi' }}" class="ckeditor" data-height="400px">{{ old('translations.'.($currentLang ?? 'vi').'.content') }}</textarea>
                    </div>
                </div>
                @endif
            </div>

            <!-- Dynamic Meta Fields -->
            @if(isset($config['fields']) && count($config['fields']) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Dữ liệu mở rộng</h2>
                @foreach($config['fields'] as $key => $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($field['options'] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}" {{ old('meta_data.'.$key, $field['default'] ?? '') == $optVal ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'user_select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn nhân sự...</option>
                                <!-- Populate users dynamically later -->
                                <option value="1">Admin</option>
                            </select>
                        @elseif($field['type'] === 'textarea')
                            <textarea name="meta_data[{{ $key }}]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="{{ $field['label'] }}">{{ old('meta_data.'.$key, $field['default'] ?? '') }}</textarea>
                        @else
                            <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" 
                                   name="meta_data[{{ $key }}]" 
                                   value="{{ old('meta_data.'.$key, $field['default'] ?? '') }}"
                                   placeholder="{{ $field['label'] }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @endif
                    </div>
                @endforeach
            </div>
            @endif

            @if(in_array('gallery', $config['supports'] ?? []) || ($type ?? $postType ?? '') === 'project')
            <!-- Album ảnh thực tế dự án (Gallery) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 space-y-4"
                 x-data="galleryManagerComponent()"
                 @media-selected.window="handleMediaSelected($event.detail)">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h2 class="font-semibold text-gray-900">Hình Ảnh Thực Tế Dự Án (Gallery)</h2>
                        <span x-show="galleryItems.length > 0" class="text-xs font-semibold px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200" x-text="`${galleryItems.length} ảnh`"></span>
                    </div>
                    <button type="button" 
                            x-show="galleryItems.length > 0"
                            @click.prevent="if (confirm('Bạn có chắc muốn xóa tất cả ảnh trong album?')) galleryItems = []"
                            class="text-xs text-red-500 hover:text-red-700 hover:underline">
                        Xóa tất cả
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Hidden input to ensure gallery field is sent even when empty -->
                    <input type="hidden" name="meta_data[gallery]" value="" x-show="galleryItems.length === 0">

                    <!-- Hidden inputs sending array -->
                    <template x-for="(gUrl, gIdx) in galleryItems" :key="gIdx">
                        <input type="hidden" name="meta_data[gallery][]" :value="gUrl">
                    </template>

                    <!-- Grid thumbnails of gallery images -->
                    <div x-show="galleryItems.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <template x-for="(gUrl, gIdx) in galleryItems" :key="gIdx">
                            <div class="group relative aspect-[4/3] rounded-xl border border-gray-200 bg-gray-50 overflow-hidden shadow-xs hover:shadow-md transition">
                                <img :src="formatImg(gUrl)" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='/assets/img/placeholder-images-image_large.webp';">
                                
                                <div class="absolute top-1.5 left-1.5 w-5 h-5 bg-black/60 text-white text-[10px] font-bold rounded flex items-center justify-center pointer-events-none"
                                     x-text="gIdx + 1"></div>

                                <button type="button" 
                                        @click.stop.prevent="removePhoto(gIdx)" 
                                        title="Xóa ảnh này" 
                                        class="absolute top-1.5 right-1.5 w-7 h-7 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-md transition cursor-pointer z-20">
                                    <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>

                                <div class="absolute inset-x-0 bottom-0 bg-black/60 p-1 backdrop-blur-xs opacity-0 group-hover:opacity-100 transition flex items-center justify-between z-10">
                                    <button type="button" @click.stop.prevent="movePhoto(gIdx, -1)" :disabled="gIdx === 0"
                                            :class="gIdx === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/20'"
                                            title="Di chuyển sang trái"
                                            class="text-white p-1 rounded">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <span class="text-[10px] text-white/80 truncate px-1" x-text="gUrl.split('/').pop()"></span>
                                    <button type="button" @click.stop.prevent="movePhoto(gIdx, 1)" :disabled="gIdx === galleryItems.length - 1"
                                            :class="gIdx === galleryItems.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/20'"
                                            title="Di chuyển sang phải"
                                            class="text-white p-1 rounded">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Button open Media Picker modal (multi-select) -->
                    <button type="button" 
                            @click.prevent="openMediaModal()"
                            class="w-full py-3.5 border-2 border-dashed border-indigo-300 hover:border-indigo-500 rounded-xl text-indigo-600 bg-indigo-50/60 hover:bg-indigo-50 font-semibold text-sm transition flex items-center justify-center gap-2 group shadow-xs cursor-pointer">
                        <svg class="w-5 h-5 text-indigo-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Chọn nhiều ảnh từ thư viện Media</span>
                    </button>

                    <!-- Manual URL Add Option -->
                    <div class="text-center pt-1">
                        <button type="button" @click="showUrlInput = !showUrlInput" class="text-xs text-gray-500 hover:text-gray-700 underline">
                            <span x-text="showUrlInput ? 'Đóng nhập link URL' : '+ Hoặc dán đường dẫn ảnh thủ công'"></span>
                        </button>
                        <div x-show="showUrlInput" x-cloak class="mt-2 flex gap-2">
                            <input type="text" x-model="manualUrl" 
                                   @keydown.enter.prevent="addManual()" 
                                   placeholder="Dán URL ảnh (VD: https://... hoặc storage/media/projects/...)" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500">
                            <button type="button" 
                                    @click="addManual()" 
                                    class="px-4 py-2 bg-gray-800 hover:bg-black text-white rounded-lg text-xs font-semibold transition">
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(($type ?? $postType ?? '') === 'property')
            <!-- Thư viện ảnh 360 cho Bất động sản -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Thư viện ảnh 360
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Các liên kết ảnh 360 (Mỗi link một dòng)</label>
                        <textarea name="meta_data[gallery_360]" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg">{{ old('meta_data.gallery_360') }}</textarea>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Analyzer -->
            @if(in_array('seo', $config['supports'] ?? []))
                @include('cms.components.seo-analyzer', ['contentType' => $config['name'] ?? 'nội dung'])
            @endif
        </div>

        <!-- Cột phải: Sidebar -->
        <div class="space-y-6">
            <!-- Đặt lịch đăng -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Xuất bản</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đặt lịch đăng</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Để trống để đăng ngay</p>
                    </div>
                </div>
            </div>

            @if(in_array($postType, ['post', 'project', 'news']))
            <!-- Chuyên mục bài viết / Danh mục dự án -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ $postType === 'project' ? 'Danh mục dự án' : 'Chuyên mục' }}</h2>
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                    @foreach($categories as $cat)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1.5 rounded text-sm text-gray-700">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                               {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                               class="rounded text-blue-600 focus:ring-blue-500">
                        <span>{{ $cat->name }}</span>
                    </label>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">Chưa có {{ $postType === 'project' ? 'danh mục' : 'chuyên mục' }} nào.</p>
                @endif
            </div>
            @endif

            @if(in_array($postType, ['post', 'news']))
            <!-- Thẻ bài viết -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Thẻ bài viết (Tags)</h2>
                <div class="space-y-3">
                    <input type="text" name="tags" value="{{ old('tags', '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                           placeholder="Ví dụ: Hải sản tươi, Mẹo vặt, Bò Úc">
                    <p class="text-xs text-gray-500">Nhập các thẻ cách nhau bằng dấu phẩy (,)</p>
                    
                    @if(isset($availableTags) && $availableTags->isNotEmpty())
                    <div class="pt-2 border-t">
                        <span class="text-xs font-medium text-gray-500 block mb-1.5">Gợi ý thẻ phổ biến:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($availableTags->take(8) as $tag)
                            <button type="button" 
                                    onclick="addTag('{{ $tag->name }}')" 
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded transition">
                                + {{ $tag->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(in_array('featured_image', $config['supports'] ?? []))
            <!-- Ảnh đại diện -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Ảnh đại diện</h2>
                
                <div class="space-y-3">
                    @include('cms.components.media-picker', ['name' => 'featured_image', 'value' => old('featured_image')])
                </div>
            </div>
            @endif

            @if(in_array('template', $config['supports'] ?? []))
            <!-- Template cho trang -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Template</h2>
                <select name="template" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Mặc định</option>
                    <option value="contact">Liên hệ</option>
                    <option value="about">Giới thiệu</option>
                    <option value="landing">Landing Page</option>
                </select>
            </div>
            @endif
        </div>
    </div>
</form>

<script>
function galleryManagerComponent() {
    const initGallery = @json($projectGalleryCreate);
    return {
        galleryItems: Array.isArray(initGallery) ? initGallery : [],
        manualUrl: '',
        showUrlInput: false,
        formatImg(url) {
            if (!url) return '';
            if (url.startsWith('http://') || url.startsWith('https://')) return url;
            if (url.startsWith('/')) return url;
            return '/' + url;
        },
        openMediaModal() {
            window.openMediaModal();
        },
        handleMediaSelected(detail) {
            if (!detail || detail.field !== 'gallery') return;
            const urls = Array.isArray(detail.urls) ? detail.urls : [detail.urls];
            urls.filter(Boolean).forEach(url => {
                const clean = (url || '').trim();
                if (clean && !this.galleryItems.includes(clean)) {
                    this.galleryItems.push(clean);
                }
            });
        },
        removePhoto(idx) {
            this.galleryItems.splice(idx, 1);
        },
        movePhoto(idx, dir) {
            const newIdx = idx + dir;
            if (newIdx < 0 || newIdx >= this.galleryItems.length) return;
            const item = this.galleryItems.splice(idx, 1)[0];
            this.galleryItems.splice(newIdx, 0, item);
        },
        addManual() {
            const val = this.manualUrl.trim();
            if (val && !this.galleryItems.includes(val)) {
                this.galleryItems.push(val);
            }
            this.manualUrl = '';
        }
    };
}

// Global fallback helper so openMediaModal() never throws ReferenceError
window.openMediaModal = function() {
    window.dispatchEvent(new CustomEvent('open-media-picker', {
        detail: {
            field: 'gallery',
            multiple: true,
            mediaType: 'image'
        }
    }));
};

function postForm() {
    return galleryManagerComponent();
}

function addTag(tagName) {
    const input = document.querySelector('input[name="tags"]');
    if (!input) return;
    const current = input.value.split(',').map(s => s.trim()).filter(Boolean);
    if (!current.includes(tagName)) {
        current.push(tagName);
        input.value = current.join(', ');
    }
}
</script>
@endsection

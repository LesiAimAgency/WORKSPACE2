@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Sửa ' . ($config['name'] ?? 'dữ liệu'))
@section('page-title', 'Sửa ' . ($config['name'] ?? 'dữ liệu'))

@section('content')
@php
    $projCode = is_object($currentProject ?? null) ? ($currentProject->code ?? null) : (is_string($currentProject ?? null) ? $currentProject : null);

    $actionUrl = Route::has('admin.posts.update') 
        ? route('admin.posts.update', $post) 
        : ($projCode && Route::has('project.admin.posts.update') 
            ? route('project.admin.posts.update', ['projectCode' => $projCode, 'post' => $post]) 
            : url('/admin/posts/' . $post->id));
        
    $indexUrl = Route::has('admin.posts.index') 
        ? route('admin.posts.index', ['type' => $postType]) 
        : ($projCode && Route::has('project.admin.posts.index') 
            ? route('project.admin.posts.index', ['projectCode' => $projCode, 'type' => $postType]) 
            : url('/admin/posts?type=' . $postType));

    $projectGallery = old('meta_data.gallery', $post->meta_data['gallery'] ?? []);
    if (!is_array($projectGallery)) {
        $projectGallery = array_values(array_filter(array_map('trim', explode("\n", (string)$projectGallery))));
    }
@endphp

<form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    
    <input type="hidden" name="post_type" value="{{ $postType }}">
    
    <!-- Header với nút Lưu/Hủy -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Sửa {{ $config['name'] ?? 'nội dung' }}: {{ $post->title }}</h1>
                <p class="text-sm text-gray-500">Cập nhật thông tin {{ $config['name'] ?? 'nội dung' }}</p>
            </div>
            <div class="flex items-center gap-3">
                @include('cms.components.ai-modal', ['postType' => $postType, 'currentLang' => $currentLang ?? 'vi'])
                <a href="{{ $post->url }}" target="_blank" class="px-3.5 py-2 border border-emerald-300 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition inline-flex items-center gap-1.5 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    <span>Xem ngoài web</span>
                </a>
                <a href="{{ $indexUrl }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Cập nhật
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
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nội dung chính</h2>
                
                @if(in_array('title', $config['supports'] ?? []))
                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ ($postType ?? '') === 'faq' ? 'Câu hỏi (Tiêu đề) *' : 'Tiêu đề *' }}
                    </label>
                    <input type="text" name="translations[{{ $currentLang ?? 'vi' }}][title]" 
                           value="{{ old('translations.'.($currentLang ?? 'vi').'.title', $post->getTranslation('title', $currentLang ?? 'vi', false)) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="{{ ($postType ?? '') === 'faq' ? 'Nhập câu hỏi thường gặp...' : 'Nhập tiêu đề...' }}">
                </div>
                @endif

                <!-- Slug (Đường dẫn tĩnh) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (Đường dẫn tĩnh)</label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nhập slug đường dẫn...">
                    <p class="text-xs text-gray-500 mt-1">Đường dẫn slug cho bài viết (chỉnh sửa độc lập, không tự động thay đổi theo tiêu đề).</p>
                </div>

                @if(in_array('excerpt', $config['supports'] ?? []))
                <!-- Tóm tắt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ ($postType ?? '') === 'faq' ? 'Tóm tắt câu trả lời (Ngắn gọn)' : 'Tóm tắt' }}</label>
                    <textarea name="translations[{{ $currentLang ?? 'vi' }}][excerpt]" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="{{ ($postType ?? '') === 'faq' ? 'Tóm tắt ngắn gọn câu trả lời 1-2 câu...' : 'Nhập tóm tắt...' }}">{{ old('translations.'.($currentLang ?? 'vi').'.excerpt', $post->getTranslation('excerpt', $currentLang ?? 'vi', false)) }}</textarea>
                </div>
                @endif

                @if(in_array('content', $config['supports'] ?? []))
                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ ($postType ?? '') === 'faq' ? 'Câu trả lời chi tiết *' : 'Nội dung chi tiết' }}
                    </label>
                    <div class="ckeditor-container">
                        <textarea name="translations[{{ $currentLang ?? 'vi' }}][content]" id="post_content_{{ $currentLang ?? 'vi' }}" class="ckeditor" data-height="400px">{{ old('translations.'.($currentLang ?? 'vi').'.content', $post->getTranslation('content', $currentLang ?? 'vi', false)) }}</textarea>
                    </div>
                </div>
                @endif
            </div>

            <!-- Dynamic Meta Fields -->
            @if(isset($config['fields']) && count($config['fields']) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                <h2 class="font-semibold text-gray-900 mb-4">Dữ liệu mở rộng</h2>
                @foreach($config['fields'] as $key => $field)
                    @php
                        $metaValue = isset($post->meta_data[$key]) ? $post->meta_data[$key] : ($field['default'] ?? '');
                        if ($key === 'year' && empty($metaValue) && !empty($post->meta_data['completion_year'])) {
                            $metaValue = $post->meta_data['completion_year'];
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($field['options'] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}" {{ old('meta_data.'.$key, $metaValue) == $optVal ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'user_select')
                            <select name="meta_data[{{ $key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn nhân sự...</option>
                                <!-- Populate users dynamically later -->
                                <option value="1" {{ old('meta_data.'.$key, $metaValue) == '1' ? 'selected' : '' }}>Admin</option>
                            </select>
                        @elseif($field['type'] === 'textarea')
                            <textarea name="meta_data[{{ $key }}]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="{{ $field['label'] }}">{{ old('meta_data.'.$key, $metaValue) }}</textarea>
                        @else
                            <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" 
                                   name="meta_data[{{ $key }}]" 
                                   value="{{ old('meta_data.'.$key, $metaValue) }}"
                                   placeholder="{{ $field['label'] }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @endif
                    </div>
                @endforeach
            </div>
            @endif

            @if(in_array('gallery', $config['supports'] ?? []) || $postType === 'project')
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
            @if($post->post_type === 'property')
            <!-- Thư viện ảnh 360 cho Bất động sản -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Thư viện ảnh 360
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Các liên kết ảnh 360 (Mỗi link một dòng)</label>
                        <textarea name="meta_data[gallery_360]" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg">{{ old('meta_data.gallery_360', $post->meta_data['gallery_360'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            @endif

            @if($post->post_type === 'product')
            @php
                $customGuaranteesPost = old('meta_data.custom_guarantees', $post->meta_data['custom_guarantees'] ?? [
                    [
                        'icon' => 'themes/daiphuc68/assets/icons/guarantee.svg',
                        'title' => 'HÀNG CHẤT LƯỢNG CHÍNH HÃNG',
                        'description' => 'Cam kết xuất xứ rõ ràng, đầy đủ chứng chỉ CO/CQ & tiêu chuẩn EVN.',
                        'link' => '',
                    ],
                    [
                        'icon' => 'themes/daiphuc68/assets/icons/rocket.svg',
                        'title' => 'GIAO HÀNG THẦN TỐC TRONG VÒNG 24H',
                        'description' => 'Giao hàng trong vòng 24h, hỗ trợ vận chuyển toàn quốc tận công trình.',
                        'link' => '',
                    ],
                    [
                        'icon' => 'themes/daiphuc68/assets/icons/preferences.svg',
                        'title' => 'BẢO HÀNH DÀI HẠN LÊN ĐẾN 36 THÁNG',
                        'description' => 'Bảo hành chính hãng lên đến 36 tháng, hỗ trợ kỹ thuật bảo trì tận tâm.',
                        'link' => '',
                    ],
                ]);
            @endphp
            <!-- Cấu hình Cam kết & Bảo hành sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6" x-data="{
                guaranteeOverride: '{{ old('meta_data.guarantee_override', $post->meta_data['guarantee_override'] ?? 'default') }}',
                customGuarantees: @json($customGuaranteesPost)
            }">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Cam Kết &amp; Bảo Hành Sản Phẩm</span>
                    </h2>
                    <a href="{{ route('admin.settings.group', 'guarantees') }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                        Cấu hình chung hệ thống →
                    </a>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Chế độ hiển thị:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-start gap-2.5 p-3 border rounded-lg cursor-pointer transition"
                                   :class="guaranteeOverride === 'default' ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="meta_data[guarantee_override]" value="default" x-model="guaranteeOverride" class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-gray-900">Mặc định hệ thống</div>
                                    <div class="text-gray-500">Dùng 3 cam kết chuẩn trong Cài đặt</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 border rounded-lg cursor-pointer transition"
                                   :class="guaranteeOverride === 'custom' ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="meta_data[guarantee_override]" value="custom" x-model="guaranteeOverride" class="mt-0.5 text-blue-600">
                                <div class="text-xs">
                                    <div class="font-bold text-gray-900">Tùy chỉnh riêng</div>
                                    <div class="text-gray-500">Chỉnh sửa nội dung cho sản phẩm này</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 border rounded-lg cursor-pointer transition"
                                   :class="guaranteeOverride === 'hide' ? 'border-red-500 bg-red-50/50' : 'border-gray-200 bg-white'">
                                <input type="radio" name="meta_data[guarantee_override]" value="hide" x-model="guaranteeOverride" class="mt-0.5 text-red-600">
                                <div class="text-xs">
                                    <div class="font-bold text-gray-900">Ẩn cam kết</div>
                                    <div class="text-gray-500">Không hiển thị trên sản phẩm này</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Custom items list -->
                    <div x-show="guaranteeOverride === 'custom'" class="space-y-3 pt-3 border-t">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-700">Các mục cam kết:</span>
                            <button type="button" @click="customGuarantees.push({ icon: 'themes/daiphuc68/assets/icons/guarantee.svg', title: '', description: '', link: '' })" class="px-2.5 py-1 text-xs font-bold bg-emerald-600 text-white rounded hover:bg-emerald-700">
                                + Thêm mục
                            </button>
                        </div>

                        <template x-for="(cItem, cIdx) in customGuarantees" :key="cIdx">
                            <div class="p-3 bg-gray-50 border rounded-lg space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-700" x-text="'Mục #' + (cIdx + 1)"></span>
                                    <button type="button" @click="customGuarantees.splice(cIdx, 1)" class="text-xs text-red-500 hover:text-red-700">Xóa</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[11px] text-gray-600 mb-0.5">Icon (URL hoặc file)</label>
                                        <input type="text" :name="`meta_data[custom_guarantees][${cIdx}][icon]`" x-model="cItem.icon" class="w-full px-2 py-1.5 text-xs border rounded bg-white">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] text-gray-600 mb-0.5">Tiêu đề cam kết</label>
                                        <input type="text" :name="`meta_data[custom_guarantees][${cIdx}][title]`" x-model="cItem.title" class="w-full px-2 py-1.5 text-xs border rounded bg-white font-bold uppercase">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-600 mb-0.5">Nội dung mô tả</label>
                                    <textarea :name="`meta_data[custom_guarantees][${cIdx}][description]`" x-model="cItem.description" rows="2" class="w-full px-2 py-1.5 text-xs border rounded bg-white"></textarea>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-600 mb-0.5">Link liên kết (tùy chọn)</label>
                                    <input type="text" :name="`meta_data[custom_guarantees][${cIdx}][link]`" x-model="cItem.link" class="w-full px-2 py-1.5 text-xs border rounded bg-white" placeholder="/chinh-sach-bao-hanh">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Analyzer -->
            @if(in_array('seo', $config['supports'] ?? []))
                @include('cms.components.seo-analyzer', ['contentType' => $config['name'] ?? 'nội dung', 'model' => $post])
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
                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đặt lịch đăng</label>
                        <input type="datetime-local" name="published_at" 
                               value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" 
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
                               {{ in_array($cat->id, old('categories', $selectedCategories ?? [])) ? 'checked' : '' }}
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
                    <input type="text" name="tags" value="{{ old('tags', $selectedTags ?? '') }}"
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
                    @include('cms.components.media-picker', ['name' => 'featured_image', 'value' => old('featured_image', $post->featured_image)])
                </div>
            </div>
            @endif

            @if(in_array('template', $config['supports'] ?? []))
            <!-- Template cho trang -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Template</h2>
                <select name="template" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Mặc định</option>
                    <option value="contact" {{ old('template', $post->template) == 'contact' ? 'selected' : '' }}>Liên hệ</option>
                    <option value="about" {{ old('template', $post->template) == 'about' ? 'selected' : '' }}>Giới thiệu</option>
                    <option value="landing" {{ old('template', $post->template) == 'landing' ? 'selected' : '' }}>Landing Page</option>
                </select>
            </div>
            @endif
        </div>
    </div>
</form>

<script>
function galleryManagerComponent() {
    const initGallery = @json($projectGallery);
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

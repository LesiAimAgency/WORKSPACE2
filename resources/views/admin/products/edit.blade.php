@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<form method="POST" action="{{ route('project.admin.products.update', [request()->route('projectCode'), $product]) }}" enctype="multipart/form-data" x-data="productForm()" @media-selected.window="handleMediaSelected($event)">
    @csrf
    @method('PUT')
    
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 sticky top-0 z-10 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
                <p class="text-sm text-gray-500 mt-1">ID: {{ $product->id }} | SKU: {{ $product->sku }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('project.admin.products.index', request()->route('projectCode')) }}" 
                   class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    Hủy
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2.5 bg-[#98191F] text-white rounded-xl hover:bg-[#7a1419] transition-colors font-medium shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Cập nhật
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Left Column: Main Form -->
        <div class="xl:col-span-3 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="font-bold text-lg mb-5 text-gray-900 pb-3 border-b border-gray-100">Thông tin cơ bản</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên sản phẩm *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                               @input="generateSlug($event.target.value)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] @error('name') border-red-500 @enderror transition-all">
                        @error('name')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] @error('sku') border-red-500 @enderror transition-all">
                            @error('sku')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" x-model="slug" value="{{ old('slug', $product->slug) }}"
                                   placeholder="Tự động tạo từ tên sản phẩm..."
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] bg-gray-50 transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Content -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="font-bold text-lg mb-5 text-gray-900 pb-3 border-b border-gray-100">Nội dung sản phẩm</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả ngắn</label>
                        <div class="ckeditor-container">
                            <textarea name="short_description" id="short_description" class="ckeditor">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả đầy đủ *
                        </label>
                        <div class="ckeditor-container">
                            <textarea name="description" id="description" class="ckeditor">{{ old('description', $product->description) }}</textarea>
                        </div>
                        @error('description')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Product Data Tabs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Tabs Header -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between px-6 py-4">
                        <nav class="flex -mb-px gap-1">
                            <button type="button" @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'bg-white border-t-2 border-[#98191F] text-[#98191F] shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200'"
                                    class="px-6 py-3 border-b-2 font-medium text-sm rounded-t-lg transition-colors">
                                General
                            </button>
                            <button type="button" @click="activeTab = 'inventory'" 
                                    :class="activeTab === 'inventory' ? 'bg-white border-t-2 border-[#98191F] text-[#98191F] shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200'"
                                    class="px-6 py-3 border-b-2 font-medium text-sm rounded-t-lg transition-colors">
                                Inventory
                            </button>
                        </nav>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Product Type:</label>
                            <select name="product_type" x-model="productType" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F]">
                                <option value="simple" {{ $product->product_type == 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="variable" {{ $product->product_type == 'variable' ? 'selected' : '' }}>Variable</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- General Tab -->
                    <div x-show="activeTab === 'general'" class="space-y-4" x-transition>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Regular Price (₫)</label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" x-model="basePrice" @input="validateSalePrice()"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sale Price (₫)</label>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" x-model="salePrice" @input="validateSalePrice()"
                                       :max="basePrice" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] transition-all">
                                <p x-show="salePriceError" class="text-red-600 text-sm mt-2" x-text="salePriceError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Tab -->
                    <div x-show="activeTab === 'inventory'" class="space-y-4" x-transition>
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="manage_stock" value="1" {{ $product->manage_stock ? 'checked' : '' }} class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-3">
                                <span class="text-sm text-gray-700">Manage Stock</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                            <select name="stock_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] transition-all">
                                <option value="in_stock" {{ $product->stock_status == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="out_of_stock" {{ $product->stock_status == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Product Settings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100">Product Settings</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#98191F] focus:border-[#98191F] transition-all">
                            <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $product->status == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ $product->status == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-3">
                            <span class="text-sm text-gray-700">Featured Product</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-3">
                            <span class="text-sm text-gray-700">Bestseller</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100">Product Images</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image *</label>
                    <div class="border-2 border-dashed rounded-xl overflow-hidden bg-gray-50 h-48 flex items-center justify-center mb-3 transition-colors hover:border-[#98191F] hover:bg-gray-100 cursor-pointer" @click="selectFeaturedImage()">
                        <template x-if="featuredImage">
                            <img :src="featuredImage" class="w-full h-full object-cover rounded-xl">
                        </template>
                        <template x-if="!featuredImage">
                            <div class="text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Click to upload</p>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="featured_image" x-model="featuredImage">
                    @error('featured_image')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Gallery (<span x-text="gallery.length"></span>)
                    </label>
                    <div class="border-2 border-dashed rounded-xl p-3 bg-gray-50 h-48 overflow-y-auto mb-3 transition-colors hover:border-[#98191F] hover:bg-gray-100 cursor-pointer" @click="selectGallery()">
                        <div class="grid grid-cols-2 gap-2" x-show="gallery.length > 0">
                            <template x-for="(img, index) in gallery" :key="index">
                                <div class="relative aspect-square border rounded-lg overflow-hidden group">
                                    <img :src="img" class="w-full h-full object-cover">
                                    <button type="button" @click.stop="removeGalleryImage(index)" 
                                            class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div x-show="gallery.length === 0" class="h-full flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <p class="text-xs">Click to add images</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="gallery" :value="JSON.stringify(gallery)">
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-900">Categories</h2>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Multi-select</span>
                </div>
                
                @if($parentCategories->isEmpty())
                    <div class="text-center py-6 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-500 mb-3">No categories yet</p>
                        <a href="{{ route('project.admin.categories.create', request()->route('projectCode')) }}" class="text-sm text-[#98191F] hover:text-[#7a1419] font-medium">
                            + Create Category
                        </a>
                    </div>
                @else
                    <div class="max-h-80 overflow-y-auto border rounded-xl p-3 space-y-1">
                        @php
                            $selectedCategories = $product->categories->pluck('id')->toArray();
                            if (empty($selectedCategories) && $product->product_category_id) {
                                $selectedCategories = [$product->product_category_id];
                            }
                        @endphp
                        
                        @foreach($parentCategories as $parent)
                            <div class="mb-2">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer group transition-colors">
                                    <input type="checkbox" name="categories[]" value="{{ $parent->id }}" 
                                           {{ in_array($parent->id, $selectedCategories) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-3">
                                    <span class="text-sm text-gray-800 group-hover:text-[#98191F] flex-1 font-medium">{{ $parent->name }}</span>
                                </label>
                                
                                @php
                                    $children = $parent->children ?? collect();
                                @endphp
                                
                                @if($children->count() > 0)
                                    <div class="ml-6 mt-1 space-y-1 pl-2 border-l border-gray-200">
                                        @foreach($children as $child)
                                            <label class="flex items-center p-1.5 hover:bg-gray-50 rounded-lg cursor-pointer group transition-colors">
                                                <input type="checkbox" name="categories[]" value="{{ $child->id }}" 
                                                       {{ in_array($child->id, $selectedCategories) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-2">
                                                <span class="text-sm text-gray-600 group-hover:text-[#98191F] flex-1">{{ $child->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @error('categories')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
            </div>

            <!-- Brands -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-900">Brands</h2>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Multi-select</span>
                </div>
                
                @if($brands->isEmpty())
                    <div class="text-center py-6 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-500 mb-3">No brands yet</p>
                        <a href="{{ route('project.admin.brands.create', request()->route('projectCode')) }}" class="text-sm text-[#98191F] hover:text-[#7a1419] font-medium">
                            + Create Brand
                        </a>
                    </div>
                @else
                    <div class="max-h-80 overflow-y-auto border rounded-xl p-3 space-y-1">
                        @php
                            $selectedBrands = $product->brands->pluck('id')->toArray();
                            if (empty($selectedBrands) && $product->brand_id) {
                                $selectedBrands = [$product->brand_id];
                            }
                        @endphp
                        
                        @foreach($brands as $brand)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer group transition-colors">
                                <input type="checkbox" name="brands[]" value="{{ $brand->id }}" 
                                       {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-[#98191F] focus:ring-[#98191F] mr-3">
                                <div class="flex items-center flex-1">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-6 h-6 rounded mr-2 object-cover">
                                    @endif
                                    <span class="text-sm text-gray-700 group-hover:text-[#98191F]">{{ $brand->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                @error('brands')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function productForm() {
    return {
        slug: '{{ $product->slug }}',
        activeTab: 'general',
        basePrice: {{ $product->price ?? 0 }},
        salePrice: {{ $product->sale_price ?? 0 }},
        salePriceError: '',
        productType: '{{ $product->product_type }}',
        featuredImage: '{{ $product->featured_image ?? '' }}',
        gallery: @json($product->gallery ?? []),
        mediaTarget: null,
        
        generateSlug(name) {
            if (!name) return;
            this.slug = name.toLowerCase()
                .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
                .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
                .replace(/[ìíịỉĩ]/g, 'i')
                .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
                .replace(/[ùúụủũưừứựửữ]/g, 'u')
                .replace(/[ỳýỵỷỹ]/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .replace(/-+/g, '-')
                .trim('-');
        },
        
        validateSalePrice() {
            if (this.salePrice && this.basePrice && parseFloat(this.salePrice) > parseFloat(this.basePrice)) {
                this.salePriceError = 'Sale price must be less than regular price';
            } else {
                this.salePriceError = '';
            }
        },
        
        selectFeaturedImage() {
            this.mediaTarget = 'featured';
        },
        
        selectGallery() {
            this.mediaTarget = 'gallery';
        },
        
        handleMediaSelected(event) {
            const selectedMedia = event.detail;
            
            if (this.mediaTarget === 'featured') {
                this.featuredImage = selectedMedia.files[0]?.url || selectedMedia[0]?.url;
            } else if (this.mediaTarget === 'gallery') {
                const newImages = selectedMedia.files ? selectedMedia.files.map(f => f.url) : selectedMedia.map(f => f.url || f);
                this.gallery = [...this.gallery, ...newImages];
            }
            
            this.mediaTarget = null;
        },
        
        removeGalleryImage(index) {
            this.gallery.splice(index, 1);
        },
        
        init() {
            this.initCKEditor();
            if (!this.featuredImage && '{{ $product->featured_image ?? '' }}') {
                this.featuredImage = '{{ $product->featured_image ?? '' }}';
            }
        },
        
        initCKEditor() {
            if (typeof window.initCKEditor === 'function') {
                window.initCKEditor(document);
                return;
            }
            if (typeof ClassicEditor !== 'undefined') {
                setTimeout(() => {
                    ['short_description', 'description'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el && !el.dataset.ckeditorInitialized) {
                            el.dataset.ckeditorInitialized = 'true';
                            ClassicEditor
                                .create(el, {
                                    toolbar: {
                                        items: [
                                            'heading', '|',
                                            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                                            'blockQuote', 'insertTable', '|',
                                            'undo', 'redo'
                                        ]
                                    },
                                    htmlSupport: {
                                        allow: [{ name: /.*/, attributes: true, classes: true, styles: true }],
                                        disallow: [
                                            { name: /^(script|iframe|embed|object|noscript|applet|meta|link)$/i },
                                            { name: /.*/, attributes: { key: /^on/i } },
                                            { name: /.*/, attributes: { key: /^(href|src|action|data)$/i, value: /^javascript:/i } }
                                        ]
                                    },
                                    language: 'vi'
                                })
                                .catch(error => {
                                    console.error('CKEditor initialization failed on #' + id, error);
                                });
                        }
                    });
                }, 100);
            }
        }
    }
}
</script>
@endsection
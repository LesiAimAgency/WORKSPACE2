<div class="max-w-6xl mx-auto" x-data="{ showModal: @entangle('showFieldModal').live, activeTab: @entangle('activeTab').live }">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($mode === 'code')
                        Sửa Code-based Widget: {{ $name }}
                    @elseif(isset($template) && $template)
                        Sửa Widget Template
                    @else
                        Tạo Widget Template
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">
                    @if($mode === 'code')
                        <span class="font-mono text-sm bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $codeWidgetClass }}</span>
                    @else
                        Định nghĩa fields và code template cho widget
                    @endif
                </p>
            </div>
            @if($mode === 'code')
                <div class="flex items-center gap-2">
                    @if($hasJsonMetadata)
                        <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">JSON Metadata</span>
                    @else
                        <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">PHP Config</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- Basic Info --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Thông tin cơ bản</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Widget *</label>
                    <input type="text" wire:model.live="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg" {{ $mode === 'code' ? '' : '' }}>
                    @if(isset($type) && $type)
                        <p class="text-xs text-gray-500 mt-1">Slug: <code class="bg-gray-100 px-1 rounded">{{ $type }}</code></p>
                    @endif
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select wire:model="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($categories ?? [] as $catValue => $catLabel)
                            <option value="{{ $catValue }}">{{ $catLabel }}</option>
                        @endforeach
                    </select>
                </div>
                @if($mode === 'code')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                    <input type="text" wire:model="version" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1.0.0">
                </div>
                @endif
                <div class="{{ $mode === 'code' ? '' : 'col-span-2' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea wire:model="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                @if($mode !== 'code')
                <div class="col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Kích hoạt</span>
                    </label>
                </div>
                @endif
            </div>
            
            @if($mode === 'code' && $viewPath)
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                <p><strong>View Path:</strong> <code class="bg-blue-100 px-1 rounded">{{ $viewPath }}</code></p>
                @if($phpPath)
                <p class="mt-1"><strong>PHP Class:</strong> <code class="bg-blue-100 px-1 rounded">{{ $phpPath }}</code></p>
                @endif
            </div>
            @endif
        </div>

        {{-- Tabs: Fields / Code / CSS / JS --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="border-b flex">
                <button type="button" @click="activeTab = 'fields'" 
                        :class="activeTab === 'fields' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Fields ({{ count($fields ?? []) }})
                    </span>
                </button>
                <button type="button" @click="activeTab = 'code'" 
                        :class="activeTab === 'code' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        Template Code
                    </span>
                </button>
                <button type="button" @click="activeTab = 'css'" 
                        :class="activeTab === 'css' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        CSS
                    </span>
                </button>
                <button type="button" @click="activeTab = 'js'" 
                        :class="activeTab === 'js' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        JavaScript
                    </span>
                </button>
            </div>

            {{-- Fields Tab --}}
            <div x-show="activeTab === 'fields'" class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Cấu hình Fields</h2>
                    <button type="button" @click="showModal = true; $wire.openAddFieldModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Thêm Field
                    </button>
                </div>

                @if(empty($fields ?? []))
                    <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Chưa có field nào. Nhấn "Thêm Field" để bắt đầu.</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($fields ?? [] as $idx => $fld)
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border hover:border-blue-300 transition" wire:key="field-{{ $idx }}">
                                <div class="flex flex-col gap-1">
                                    <button type="button" wire:click="moveFieldUp({{ $idx }})" class="p-1 text-gray-400 hover:text-gray-600 {{ $idx === 0 ? 'opacity-30' : '' }}" {{ $idx === 0 ? 'disabled' : '' }}>▲</button>
                                    <button type="button" wire:click="moveFieldDown({{ $idx }})" class="p-1 text-gray-400 hover:text-gray-600 {{ $idx === count($fields ?? []) - 1 ? 'opacity-30' : '' }}" {{ $idx === count($fields ?? []) - 1 ? 'disabled' : '' }}>▼</button>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $fld['label'] }}</span>
                                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded">{{ $fld['type'] }}</span>
                                        @if($fld['required'] ?? false)
                                            <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">Required</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 font-mono">${{ '{' }}settings['{{ $fld['name'] }}']{{ '}' }}</p>
                                </div>
                                <button type="button" @click="showModal = true; $wire.openEditFieldModal({{ $idx }})" class="px-3 py-1 text-blue-600 hover:bg-blue-50 rounded">Sửa</button>
                                <button type="button" wire:click="removeField({{ $idx }})" wire:confirm="Bạn có chắc muốn xóa field này?" class="px-3 py-1 text-red-600 hover:bg-red-50 rounded">Xóa</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Code Tab --}}
            <div x-show="activeTab === 'code'" x-data="codeEditor('blade')" class="p-6">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">Template Code (Blade/PHP)</h2>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="formatCode()" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                                Format Code
                            </button>
                            <button type="button" @click="insertSnippet('foreach')" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">
                                @@foreach
                            </button>
                            <button type="button" @click="insertSnippet('if')" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">
                                @@if
                            </button>
                            <button type="button" @click="insertSnippet('products')" class="px-3 py-1 text-sm bg-green-100 text-green-700 hover:bg-green-200 rounded">
                                Products
                            </button>
                        </div>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm">
                        <strong>Biến có sẵn:</strong>
                        <code class="bg-yellow-100 px-1 rounded ml-2">$settings</code>
                        <code class="bg-yellow-100 px-1 rounded ml-2">$products($limit)</code>
                        <code class="bg-yellow-100 px-1 rounded ml-2">$posts($limit)</code>
                        <code class="bg-yellow-100 px-1 rounded ml-2">$categories()</code>
                    </div>
                </div>
                <div class="border rounded-lg overflow-hidden" wire:ignore>
                    <div id="code-editor-blade" class="code-editor" style="height: 500px;"></div>
                    <textarea wire:model.blur="template_code" id="template_code_input" class="hidden"></textarea>
                </div>
            </div>

            {{-- CSS Tab --}}
            <div x-show="activeTab === 'css'" x-data="codeEditor('css')" class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Custom CSS</h2>
                    <p class="text-sm text-gray-500">CSS sẽ được inject vào trang khi widget được render</p>
                </div>
                <div class="border rounded-lg overflow-hidden" wire:ignore>
                    <div id="code-editor-css" class="code-editor" style="height: 400px;"></div>
                    <textarea wire:model.blur="template_css" id="template_css_input" class="hidden"></textarea>
                </div>
            </div>

            {{-- JS Tab --}}
            <div x-show="activeTab === 'js'" x-data="codeEditor('javascript')" class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Custom JavaScript</h2>
                    <p class="text-sm text-gray-500">JavaScript sẽ được inject vào trang khi widget được render</p>
                </div>
                <div class="border rounded-lg overflow-hidden" wire:ignore>
                    <div id="code-editor-javascript" class="code-editor" style="height: 400px;"></div>
                    <textarea wire:model.blur="template_js" id="template_js_input" class="hidden"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                {{ isset($template) && $template ? 'Cập nhật' : 'Tạo Widget Template' }}
            </button>
        </div>
    </form>

    {{-- Field Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false; $wire.closeFieldModal()"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        {{ ($editingFieldIndex ?? -1) >= 0 ? 'Sửa Field' : 'Thêm Field mới' }}
                    </h3>
                    <button type="button" @click="showModal = false; $wire.closeFieldModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    {{-- Field Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại Field *</label>
                        <select wire:model.live="currentField.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @foreach($fieldTypes ?? [] as $typeKey => $typeInfo)
                                <option value="{{ $typeKey }}">{{ ucfirst($typeInfo['name'] ?? $typeKey) }}</option>
                            @endforeach
                        </select>
                        @if(isset($fieldTypes[($currentField['type'] ?? '')]['description']))
                            <p class="text-xs text-gray-500 mt-1">{{ $fieldTypes[($currentField['type'] ?? '')]['description'] }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Field Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Field (slug) *</label>
                            <input type="text" wire:model="currentField.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono" placeholder="field_name">
                            <p class="text-xs text-gray-500 mt-1">Chỉ dùng chữ thường, số và dấu _</p>
                            @error('currentField.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        {{-- Field Label --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nhãn hiển thị *</label>
                            <input type="text" wire:model="currentField.label" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Tiêu đề">
                            @error('currentField.label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Default Value --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị mặc định</label>
                        <input type="text" wire:model="currentField.default" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>

                    {{-- Help Text --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả / Hướng dẫn</label>
                        <input type="text" wire:model="currentField.help" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Hướng dẫn cho người dùng">
                    </div>

                    {{-- Required --}}
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="currentField.required" class="rounded border-gray-300">
                            <span class="text-sm font-medium text-gray-700">Bắt buộc</span>
                        </label>
                    </div>

                    {{-- Type-specific settings --}}
                    @if(in_array(($currentField['type'] ?? ''), ['text', 'email', 'url', 'textarea', 'number', 'select']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder</label>
                            <input type="text" wire:model="currentField.placeholder" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    @endif

                    @if(($currentField['type'] ?? '') === 'textarea')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số dòng</label>
                            <input type="number" wire:model="currentField.rows" min="2" max="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    @endif

                    @if(($currentField['type'] ?? '') === 'wysiwyg')
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Toolbar</label>
                                <select wire:model="currentField.toolbar" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="basic">Basic</option>
                                    <option value="full">Full</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chiều cao (px)</label>
                                <input type="number" wire:model="currentField.height" min="100" max="800" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    @endif

                    @if(in_array(($currentField['type'] ?? ''), ['number', 'range', 'gallery', 'relationship', 'repeatable', 'repeater']))
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị tối thiểu</label>
                                <input type="number" wire:model="currentField.min" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị tối đa</label>
                                <input type="number" wire:model="currentField.max" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    @endif

                    @if(($currentField['type'] ?? '') === 'range')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bước nhảy</label>
                            <input type="number" wire:model="currentField.step" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    @endif

                    {{-- Select/Radio Options --}}
                    @if(in_array(($currentField['type'] ?? ''), ['select', 'radio']))
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Các lựa chọn</label>
                                <button type="button" wire:click="addSelectOption" class="text-sm text-blue-600 hover:text-blue-700">+ Thêm</button>
                            </div>
                            <div class="space-y-2">
                                @foreach($currentField['options'] ?? [] as $optIdx => $opt)
                                    <div class="flex gap-2" wire:key="opt-{{ $optIdx }}">
                                        <input type="text" wire:model="currentField.options.{{ $optIdx }}.value" placeholder="Giá trị" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <input type="text" wire:model="currentField.options.{{ $optIdx }}.label" placeholder="Nhãn" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <button type="button" wire:click="removeSelectOption({{ $optIdx }})" class="px-2 text-red-500 hover:text-red-700">×</button>
                                    </div>
                                @endforeach
                            </div>
                            @if(($currentField['type'] ?? '') === 'select')
                                <label class="flex items-center gap-2 mt-2">
                                    <input type="checkbox" wire:model="currentField.multiple" class="rounded border-gray-300">
                                    <span class="text-sm text-gray-600">Cho phép chọn nhiều</span>
                                </label>
                            @endif
                        </div>
                    @endif

                    {{-- Image/Gallery settings --}}
                    @if(in_array(($currentField['type'] ?? ''), ['image', 'gallery']))
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Định dạng trả về</label>
                                <select wire:model="currentField.return_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="url">URL</option>
                                    <option value="id">ID</option>
                                    <option value="array">Array</option>
                                </select>
                            </div>
                            @if(($currentField['type'] ?? '') === 'image')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kích thước preview</label>
                                    <select wire:model="currentField.preview_size" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="thumbnail">Thumbnail</option>
                                        <option value="medium">Medium</option>
                                        <option value="large">Large</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Relationship/Post Object settings --}}
                    @if(in_array(($currentField['type'] ?? ''), ['relationship', 'post_object']))
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loại Post</label>
                                <select wire:model="currentField.post_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="product">Sản phẩm</option>
                                    <option value="post">Bài viết</option>
                                    <option value="page">Trang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Định dạng trả về</label>
                                <select wire:model="currentField.return_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="object">Object</option>
                                    <option value="id">ID</option>
                                </select>
                            </div>
                        </div>
                        @if(($currentField['type'] ?? '') === 'post_object')
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="currentField.multiple" class="rounded border-gray-300">
                                <span class="text-sm text-gray-600">Cho phép chọn nhiều</span>
                            </label>
                        @endif
                    @endif

                    {{-- Taxonomy settings --}}
                    @if(($currentField['type'] ?? '') === 'taxonomy')
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Taxonomy</label>
                                <select wire:model="currentField.taxonomy" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="category">Danh mục</option>
                                    <option value="tag">Tag</option>
                                    <option value="product_category">Danh mục sản phẩm</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kiểu hiển thị</label>
                                <select wire:model="currentField.field_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio</option>
                                </select>
                            </div>
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="currentField.multiple" class="rounded border-gray-300">
                            <span class="text-sm text-gray-600">Cho phép chọn nhiều</span>
                        </label>
                    @endif

                    {{-- Repeatable/Repeater settings --}}
                    @if(in_array(($currentField['type'] ?? ''), ['repeatable', 'repeater']))
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Sub-fields</label>
                                <button type="button" wire:click="addRepeatableField" class="text-sm text-blue-600 hover:text-blue-700">+ Thêm field</button>
                            </div>
                            <div class="space-y-2 bg-gray-50 p-3 rounded-lg">
                                @forelse($currentField['fields'] ?? [] as $subIdx => $subField)
                                    <div class="flex gap-2 items-center" wire:key="sub-{{ $subIdx }}">
                                        <input type="text" wire:model="currentField.fields.{{ $subIdx }}.name" placeholder="Tên field" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                                        <input type="text" wire:model="currentField.fields.{{ $subIdx }}.label" placeholder="Nhãn" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <select wire:model="currentField.fields.{{ $subIdx }}.type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="number">Number</option>
                                            <option value="image">Image</option>
                                            <option value="url">URL</option>
                                            <option value="email">Email</option>
                                        </select>
                                        <button type="button" wire:click="removeRepeatableField({{ $subIdx }})" class="px-2 text-red-500 hover:text-red-700">×</button>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">Chưa có sub-field nào</p>
                                @endforelse
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Layout</label>
                                    <select wire:model="currentField.layout" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="table">Table</option>
                                        <option value="block">Block</option>
                                        <option value="row">Row</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhãn nút thêm</label>
                                    <input type="text" wire:model="currentField.button_label" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Add Row">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Date settings --}}
                    @if(($currentField['type'] ?? '') === 'date')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Định dạng hiển thị</label>
                            <select wire:model="currentField.display_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                    <button type="button" @click="showModal = false; $wire.closeFieldModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">Hủy</button>
                    <button type="button" wire:click="saveField" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ ($editingFieldIndex ?? -1) >= 0 ? 'Cập nhật' : 'Thêm Field' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/show-hint.min.css">
<style>
    .CodeMirror {
        height: 100%;
        font-size: 14px;
        line-height: 1.6;
        font-family: 'Fira Code', 'Monaco', 'Consolas', 'Courier New', monospace;
    }
    .CodeMirror-gutters {
        background: #1e1e2e;
        border-right: 1px solid #44475a;
        width: 60px;
    }
    .CodeMirror-linenumber {
        color: #6272a4;
        padding: 0 12px;
        min-width: 50px;
        text-align: right;
    }
    .CodeMirror-lines {
        padding: 12px 0;
    }
    .CodeMirror pre.CodeMirror-line,
    .CodeMirror pre.CodeMirror-line-like {
        padding-left: 16px;
    }
    .CodeMirror-sizer {
        margin-left: 60px;
    }
    .CodeMirror-linenumber{
        text-align:center;
    }
    .CodeMirror-cursor {
        border-left: 2px solid #f8f8f2;
    }
    .CodeMirror-selected {
        background: #44475a;
    }
    .CodeMirror-activeline-background {
        background: #2d2d3a;
    }
    .CodeMirror-hints {
        z-index: 9999;
        font-family: 'Fira Code', 'Monaco', 'Consolas', monospace;
        font-size: 13px;
    }
    .CodeMirror-hint {
        padding: 4px 8px;
    }
    .CodeMirror-hint-active {
        background: #44475a;
        color: #f8f8f2;
    }
    .code-editor {
        border-radius: 8px;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/show-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/xml-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/html-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/css-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/javascript-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/brace-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/xml-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>

@verbatim
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('codeEditor', (language) => ({
        editor: null,
        language: language,
        
        init() {
            this.$nextTick(() => {
                this.initEditor();
            });
        },
        
        initEditor() {
            const container = document.getElementById('code-editor-' + this.language);
            const textarea = document.getElementById(this.getTextareaId());
            
            if (!container || this.editor) return;
            
            const modeMap = {
                'blade': 'application/x-httpd-php',
                'css': 'text/css',
                'javascript': 'text/javascript'
            };
            
            this.editor = CodeMirror(container, {
                value: textarea ? textarea.value : '',
                mode: modeMap[this.language] || 'htmlmixed',
                theme: 'dracula',
                lineNumbers: true,
                lineWrapping: true,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                styleActiveLine: true,
                foldGutter: true,
                gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                extraKeys: {
                    'Ctrl-Space': 'autocomplete',
                    'Ctrl-/': (cm) => cm.toggleComment(),
                    'Tab': (cm) => {
                        if (cm.somethingSelected()) {
                            cm.indentSelection('add');
                        } else {
                            cm.replaceSelection('    ', 'end');
                        }
                    }
                },
                hintOptions: {
                    completeSingle: false
                }
            });
            
            // Sync to Livewire on change
            this.editor.on('change', () => {
                const value = this.editor.getValue();
                if (textarea) {
                    textarea.value = value;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
                // Also update Livewire directly
                this.syncToLivewire(value);
            });
            
            // Auto-complete on typing
            this.editor.on('inputRead', (cm, change) => {
                if (change.text[0].match(/[a-zA-Z<@$]/)) {
                    cm.showHint({ completeSingle: false });
                }
            });
        },
        
        getTextareaId() {
            const map = {
                'blade': 'template_code_input',
                'css': 'template_css_input',
                'javascript': 'template_js_input'
            };
            return map[this.language];
        },
        
        syncToLivewire(value) {
            const propMap = {
                'blade': 'template_code',
                'css': 'template_css',
                'javascript': 'template_js'
            };
            const prop = propMap[this.language];
            if (prop && this.$wire) {
                this.$wire.set(prop, value);
            }
        },
        
        insertSnippet(type) {
            if (!this.editor) return;
            
            const snippets = {
                'foreach': `@foreach($items as $item)
    <div>{{ $item->name }}</div>
@endforeach`,
                'if': `@if(!empty($settings['field_name']))
    {{ $settings['field_name'] }}
@endif`,
                'products': `@php
    $products = $products(6);
@endphp

<div class="grid grid-cols-3 gap-4">
    @foreach($products as $product)
        <div class="border rounded-lg p-4">
            @if($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded">
            @endif
            <h3 class="font-semibold mt-2">{{ $product->name }}</h3>
            <p class="text-red-600 font-bold">{{ number_format($product->price) }}đ</p>
        </div>
    @endforeach
</div>`,
                'posts': `@php
    $posts = $posts(4);
@endphp

<div class="space-y-4">
    @foreach($posts as $post)
        <article class="border-b pb-4">
            <h3 class="text-lg font-semibold">{{ $post->title }}</h3>
            <p class="text-gray-600">{{ Str::limit($post->excerpt, 100) }}</p>
        </article>
    @endforeach
</div>`
            };
            
            const snippet = snippets[type];
            if (snippet) {
                const cursor = this.editor.getCursor();
                this.editor.replaceRange(snippet, cursor);
                this.editor.focus();
            }
        },
        
        formatCode() {
            if (!this.editor) return;
            // Simple auto-indent
            const totalLines = this.editor.lineCount();
            for (let i = 0; i < totalLines; i++) {
                this.editor.indentLine(i, 'smart');
            }
        }
    }));
});

// Blade/PHP autocomplete hints
CodeMirror.registerHelper('hint', 'blade', function(editor) {
    const cur = editor.getCursor();
    const token = editor.getTokenAt(cur);
    const word = token.string;
    
    const bladeDirectives = [
        '@if', '@else', '@elseif', '@endif',
        '@foreach', '@endforeach', '@forelse', '@empty', '@endforelse',
        '@for', '@endfor', '@while', '@endwhile',
        '@switch', '@case', '@break', '@default', '@endswitch',
        '@include', '@extends', '@section', '@endsection', '@yield',
        '@push', '@endpush', '@stack', '@prepend', '@endprepend',
        '@php', '@endphp', '@isset', '@endisset', '@empty', '@endempty',
        '@auth', '@endauth', '@guest', '@endguest',
        '@can', '@cannot', '@endcan', '@endcannot',
        '@env', '@endenv', '@production', '@endproduction',
        '@once', '@endonce', '@verbatim', '@endverbatim'
    ];
    
    const variables = [
        '$settings', '$products', '$posts', '$categories', '$widget',
        '$settings[\'title\']', '$settings[\'image\']', '$settings[\'content\']'
    ];
    
    const allHints = [...bladeDirectives, ...variables];
    const matches = allHints.filter(h => h.toLowerCase().startsWith(word.toLowerCase()));
    
    return {
        list: matches.length ? matches : allHints,
        from: CodeMirror.Pos(cur.line, token.start),
        to: CodeMirror.Pos(cur.line, token.end)
    };
});
</script>
@endverbatim
@endpush

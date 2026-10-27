<div class="max-w-7xl mx-auto" x-data="{ showModal: @entangle('showFieldModal').live, activeTab: @entangle('activeTab').live }">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Code-based Widget: {{ $name }}
                </h1>
                <p class="text-gray-600 mt-1">
                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $widgetClass }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($hasJsonMetadata)
                    <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">JSON Metadata</span>
                @else
                    <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">PHP Config</span>
                @endif
            </div>
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
            <h2 class="text-lg font-semibold mb-4">Thông tin Widget</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Widget</label>
                    <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select wire:model="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($categories as $catValue => $catLabel)
                            <option value="{{ $catValue }}">{{ $catLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                    <input type="text" wire:model="version" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1.0.0">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea wire:model="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="border-b flex">
                <button type="button" @click="activeTab = 'fields'" 
                        :class="activeTab === 'fields' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Fields ({{ count($fields) }})
                    </span>
                </button>
                <button type="button" @click="activeTab = 'view'" 
                        :class="activeTab === 'view' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        View (Blade)
                    </span>
                </button>
                <button type="button" @click="activeTab = 'php'" 
                        :class="activeTab === 'php' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        PHP Class
                    </span>
                </button>
                <button type="button" @click="activeTab = 'css'" 
                        :class="activeTab === 'css' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    CSS
                </button>
                <button type="button" @click="activeTab = 'js'" 
                        :class="activeTab === 'js' ? 'border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-6 py-3 font-medium transition">
                    JavaScript
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

                @if(empty($fields))
                    <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Chưa có field nào. Nhấn "Thêm Field" để bắt đầu.</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($fields as $idx => $fld)
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border hover:border-blue-300 transition" wire:key="field-{{ $idx }}">
                                <div class="flex flex-col gap-1">
                                    <button type="button" wire:click="moveFieldUp({{ $idx }})" class="p-1 text-gray-400 hover:text-gray-600 {{ $idx === 0 ? 'opacity-30' : '' }}" {{ $idx === 0 ? 'disabled' : '' }}>▲</button>
                                    <button type="button" wire:click="moveFieldDown({{ $idx }})" class="p-1 text-gray-400 hover:text-gray-600 {{ $idx === count($fields) - 1 ? 'opacity-30' : '' }}" {{ $idx === count($fields) - 1 ? 'disabled' : '' }}>▼</button>
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

            {{-- View Tab --}}
            <div x-show="activeTab === 'view'" class="p-6">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">View Template (Blade)</h2>
                        @if($viewPath)
                            <span class="text-xs text-gray-500 font-mono">{{ $viewPath }}</span>
                        @endif
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm">
                        <strong>Biến có sẵn:</strong>
                        @foreach($fields as $field)
                            <code class="bg-yellow-100 px-1 rounded ml-2">${{ $field['name'] }}</code>
                        @endforeach
                    </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <textarea wire:model="viewCode" rows="25" class="w-full px-4 py-3 font-mono text-sm bg-gray-900 text-gray-100" style="tab-size: 4;"></textarea>
                </div>
            </div>

            {{-- PHP Tab --}}
            <div x-show="activeTab === 'php'" class="p-6">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">PHP Widget Class</h2>
                        <span class="text-xs text-gray-500 font-mono">{{ $phpPath }}</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm">
                        <strong>Lưu ý:</strong> Chỉnh sửa PHP class cần cẩn thận. Thay đổi fields sẽ tự động cập nhật getConfig() hoặc widget.json.
                    </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <textarea wire:model="phpCode" rows="30" class="w-full px-4 py-3 font-mono text-sm bg-gray-900 text-gray-100" style="tab-size: 4;" readonly></textarea>
                </div>
            </div>

            {{-- CSS Tab --}}
            <div x-show="activeTab === 'css'" class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Custom CSS</h2>
                    @if($cssPath)
                        <span class="text-xs text-gray-500 font-mono">{{ $cssPath }}</span>
                    @else
                        <p class="text-sm text-gray-500">CSS sẽ được tạo mới khi lưu</p>
                    @endif
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <textarea wire:model="cssCode" rows="20" class="w-full px-4 py-3 font-mono text-sm bg-gray-900 text-gray-100" style="tab-size: 4;"></textarea>
                </div>
            </div>

            {{-- JS Tab --}}
            <div x-show="activeTab === 'js'" class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Custom JavaScript</h2>
                    @if($jsPath)
                        <span class="text-xs text-gray-500 font-mono">{{ $jsPath }}</span>
                    @else
                        <p class="text-sm text-gray-500">JavaScript sẽ được tạo mới khi lưu</p>
                    @endif
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <textarea wire:model="jsCode" rows="20" class="w-full px-4 py-3 font-mono text-sm bg-gray-900 text-gray-100" style="tab-size: 4;"></textarea>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center">
            <a href="{{ $projectCode ? route('project.admin.code-widgets.index', $projectCode) : route('cms.code-widgets.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                ← Quay lại
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Lưu thay đổi
            </button>
        </div>
    </form>

    {{-- Field Modal --}}
    @include('livewire.admin.partials.field-modal')
</div>

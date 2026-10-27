<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Code-based Widgets</h1>
                <p class="text-gray-600 mt-1">Quản lý các widgets được định nghĩa trong code (app/Widgets)</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Tìm kiếm widget..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <select wire:model.live="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Widget List by Category --}}
    @forelse($codeWidgets as $category => $widgets)
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 border-b bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-800 capitalize">{{ $category }}</h2>
                <p class="text-sm text-gray-500">{{ count($widgets) }} widgets</p>
            </div>
            
            <div class="divide-y">
                @foreach($widgets as $widget)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $widget['metadata']['name'] ?? $widget['type'] }}</h3>
                                        <p class="text-sm text-gray-500">{{ $widget['metadata']['description'] ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $widget['type'] }}</span>
                                    <span>{{ count($widget['metadata']['fields'] ?? []) }} fields</span>
                                    <span class="text-gray-400">{{ $widget['class'] ?? '' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ $projectCode ? route('project.admin.code-widgets.edit', [$projectCode, $widget['type']]) : route('cms.code-widgets.edit', $widget['type']) }}" 
                                   class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Widget
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy widget</h3>
            <p class="text-gray-500">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
        </div>
    @endforelse

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Code-based Widgets vs Custom Widget Templates</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li><strong>Code-based Widgets:</strong> Định nghĩa trong <code class="bg-blue-100 px-1 rounded">app/Widgets/</code>, có PHP class riêng</li>
                    <li><strong>Custom Widget Templates:</strong> Tạo qua UI, lưu trong database và <code class="bg-blue-100 px-1 rounded">resources/views/widgets/custom/</code></li>
                    <li>Cả hai đều có thể edit fields và code template trực tiếp</li>
                </ul>
            </div>
        </div>
    </div>
</div>

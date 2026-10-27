<div class="max-w-6xl mx-auto" x-data="widgetEditor()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $widget ? 'Sửa Widget' : 'Tạo Widget' }}
        </h1>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Form --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Thông tin cơ bản
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên widget *</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại widget *</label>
                        <select wire:model.live="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" {{ $widget ? 'disabled' : '' }}>
                            <option value="">-- Chọn loại widget --</option>
                            @php $currentCategory = ''; @endphp
                            @foreach($templates as $tpl)
                                @if($tpl['category'] !== $currentCategory)
                                    @if($currentCategory !== '') </optgroup> @endif
                                    <optgroup label="{{ ucfirst($tpl['category']) }}">
                                    @php $currentCategory = $tpl['category']; @endphp
                                @endif
                                <option value="{{ $tpl['type'] }}">{{ $tpl['name'] }}</option>
                            @endforeach
                            @if($currentCategory !== '') </optgroup> @endif
                        </select>
                        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Khu vực *</label>
                        <select wire:model="area" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="homepage">Homepage</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="footer">Footer</option>
                            <option value="header">Header</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                        <input type="number" wire:model="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Kích hoạt</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Dynamic Fields --}}
            @if(!empty($fields))
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Nội dung Widget
                    </h2>
                    
                    <div class="space-y-6">
                        @foreach($fields as $field)
                            @include('livewire.admin.partials.widget-field', ['field' => $field, 'settings' => $settings])
                        @endforeach
                    </div>
                </div>
            @elseif(empty($type))
                <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <p class="text-lg font-medium">Chọn loại widget để bắt đầu</p>
                    <p class="text-sm mt-1">Các trường cấu hình sẽ hiển thị sau khi chọn loại widget</p>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-700">
                    <p>Widget template "{{ $type }}" chưa có cấu hình fields.</p>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex justify-between items-center">
                <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    ← Quay lại
                </a>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    {{ $widget ? 'Cập nhật Widget' : 'Tạo Widget' }}
                </button>
            </div>
        </div>

        {{-- Right: Preview --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview
                </h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50 min-h-[200px]">
                    <div class="transform scale-75 origin-top-left" style="width: 133.33%;">
                        {!! $this->getPreview() !!}
                    </div>
                </div>
                
                @if($template)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <p class="font-medium text-gray-700">{{ $template['name'] ?? '' }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $template['description'] ?? '' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Media Picker Modal Component --}}
    <x-media-picker-modal />
</div>

@push('scripts')
<script>
function widgetEditor() {
    return {
        init() {
            // Listen for media selection
            window.addEventListener('media-selected', (event) => {
                const { field, urls } = event.detail;
                if (field) {
                    @this.set('settings.' + field, urls);
                }
            });
        }
    }
}
</script>
@endpush

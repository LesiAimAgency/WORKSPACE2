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
                    </div>
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
                                    </select>
                                    <button type="button" wire:click="removeRepeatableField({{ $subIdx }})" class="px-2 text-red-500 hover:text-red-700">×</button>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-2">Chưa có sub-field nào</p>
                            @endforelse
                        </div>
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

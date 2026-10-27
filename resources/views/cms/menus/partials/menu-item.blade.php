<div class="menu-item depth-{{ $depth ?? 0 }} bg-white border border-gray-200 rounded-lg mb-2 shadow-xs transition-all overflow-hidden" data-id="{{ $item->id }}" data-depth="{{ $depth ?? 0 }}">
    {{-- Header hàng ngang của Menu Item --}}
    <div class="flex items-center justify-between p-2.5 bg-gray-50/70 hover:bg-gray-100/70 cursor-pointer select-none" onclick="toggleItemEdit({{ $item->id }}, event)">
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <div class="drag-handle w-5 h-5 flex flex-col justify-center items-center text-gray-400 cursor-grab hover:text-gray-600 shrink-0" title="Kéo thả để sắp xếp" onclick="event.stopPropagation()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
            </div>
            
            <div class="flex items-center gap-2 flex-1 min-w-0">
                @if(!empty($item->icon))
                    <i class="{{ $item->icon }} text-gray-600 text-xs shrink-0" id="item-icon-preview-{{ $item->id }}"></i>
                @endif
                @if(!empty($item->image))
                    <img src="{{ $item->image }}" class="w-4 h-4 object-contain rounded shrink-0" id="item-image-preview-{{ $item->id }}">
                @endif

                <span class="font-medium text-sm text-gray-800 truncate" id="item-title-display-{{ $item->id }}">{{ $item->title }}</span>
                
                @if(!empty($item->badge))
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded text-white shrink-0" id="item-badge-preview-{{ $item->id }}" style="background-color: {{ $item->badge_color ?: '#ef4444' }};">
                        {{ $item->badge }}
                    </span>
                @else
                    <span class="hidden text-[10px] font-bold px-1.5 py-0.5 rounded text-white shrink-0" id="item-badge-preview-{{ $item->id }}"></span>
                @endif

                <span class="text-xs text-gray-400 truncate max-w-[200px]" id="item-url-display-{{ $item->id }}">
                    @if($item->linkable_type)
                        ({{ class_basename($item->linkable_type) }})
                    @elseif($item->url)
                        → {{ $item->url }}
                    @endif
                </span>
            </div>
        </div>

        <div class="flex items-center gap-1 shrink-0 ml-2" onclick="event.stopPropagation()">
            <!-- Edit Toggle Button -->
            <button type="button" onclick="toggleItemEdit({{ $item->id }}, event)" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Chỉnh sửa chi tiết">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </button>
            <!-- Move Up -->
            <button type="button" onclick="moveUp({{ $item->id }})" class="p-1 hover:bg-blue-100 text-blue-600 rounded transition" title="Di chuyển lên">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
            <!-- Move Down -->
            <button type="button" onclick="moveDown({{ $item->id }})" class="p-1 hover:bg-blue-100 text-blue-600 rounded transition" title="Di chuyển xuống">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <!-- Indent Right -->
            @if(($depth ?? 0) < 3)
            <button type="button" onclick="indentRight({{ $item->id }})" class="p-1 hover:bg-green-100 text-green-600 rounded transition" title="Tạo menu con">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            @endif
            <!-- Indent Left -->
            @if(($depth ?? 0) > 0)
            <button type="button" onclick="indentLeft({{ $item->id }})" class="p-1 hover:bg-orange-100 text-orange-600 rounded transition" title="Hủy phân cấp">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            @endif
            <!-- Delete -->
            <button type="button" onclick="deleteItem({{ $item->id }})" class="p-1 hover:bg-red-100 text-red-600 rounded transition" title="Xóa">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    </div>

    {{-- Form mở rộng chỉnh sửa (Accordion Edit Panel) --}}
    <div id="item-edit-panel-{{ $item->id }}" class="hidden p-4 border-t border-gray-200 bg-white space-y-3">
        <form onsubmit="saveItemDetails({{ $item->id }}, event)" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tên nhãn hiển thị *</label>
                    <input type="text" id="edit-title-{{ $item->id }}" value="{{ $item->title }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Đường dẫn URL *</label>
                    <input type="text" id="edit-url-{{ $item->id }}" value="{{ $item->url }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500" placeholder="/ hoặc https://..." required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cách mở link</label>
                    <select id="edit-target-{{ $item->id }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="_self" {{ ($item->target ?? '_self') === '_self' ? 'selected' : '' }}>Mở trong tab hiện tại (_self)</option>
                        <option value="_blank" {{ ($item->target ?? '_self') === '_blank' ? 'selected' : '' }}>Mở trong tab mới (_blank)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Icon class (FontAwesome)</label>
                    <input type="text" id="edit-icon-{{ $item->id }}" value="{{ $item->icon }}" placeholder="vd: fa-solid fa-house" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-3 sm:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ảnh icon/Thumbnail (URL)</label>
                    <input type="text" id="edit-image-{{ $item->id }}" value="{{ $item->image }}" placeholder="/media/... hoặc URL" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="col-span-3 sm:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nhãn nổi bật (Badge)</label>
                    <input type="text" id="edit-badge-{{ $item->id }}" value="{{ $item->badge }}" placeholder="vd: Hot, Sale, Mới" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="col-span-3 sm:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Màu Badge</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="edit-badge-color-{{ $item->id }}" value="{{ $item->badge_color ?: '#ef4444' }}" class="w-8 h-8 p-0 border border-gray-300 rounded cursor-pointer">
                        <span class="text-xs text-gray-500">Màu nền</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <button type="button" onclick="deleteItem({{ $item->id }})" class="text-xs text-red-600 hover:text-red-700 font-medium">
                    Xóa mục này
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleItemEdit({{ $item->id }}, event)" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded">
                        Hủy
                    </button>
                    <button type="submit" id="save-item-btn-{{ $item->id }}" class="px-4 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded hover:bg-blue-700 shadow-xs flex items-center gap-1">
                        <span>Lưu thay đổi mục</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@extends('cms.layouts.app')

@section('title', 'Quản lý Menu')
@section('page-title', 'Quản lý Menu')

@section('content')
@php
    $urlPrefix = '';
@endphp
<!-- Alert Container -->
<div id="alert-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-md"></div>

<div class="grid grid-cols-12 gap-4">
    <!-- Cột 1: Danh sách menu của dự án -->
    <div class="col-span-12 lg:col-span-3 bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 text-base">Menu Dự Án</h3>
            <button type="button" onclick="openCreateMenuModal()" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 flex items-center gap-1 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo Menu
            </button>
        </div>
        
        <div class="space-y-2">
            @forelse($menus as $menu)
            @php
                $isActiveMenu = $selectedMenu && $selectedMenu->id == $menu->id;
                $menuUrl = route('admin.menus.show', $menu->id);
            @endphp
            <div class="flex items-center gap-2 group">
                <a href="{{ $menuUrl }}" class="flex-1 p-3 rounded-lg hover:bg-gray-50 transition {{ $isActiveMenu ? 'bg-blue-50 border-l-4 border-blue-600 text-blue-900 font-semibold' : 'border-l-4 border-transparent text-gray-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="truncate">{{ $menu->name }}</span>
                        @if($menu->location)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 text-gray-700 font-normal">
                                {{ strtoupper($menu->location) }}
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 font-normal mt-0.5">
                        {{ $menu->allItems ? $menu->allItems->count() : 0 }} mục
                    </div>
                </a>
                <button onclick="deleteMenu({{ $menu->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition opacity-0 group-hover:opacity-100" title="Xóa menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
            @empty
            <div class="p-4 text-center text-sm text-gray-400 border border-dashed rounded-lg">
                Chưa có menu nào. Hãy bấm "Tạo Menu" ở trên!
            </div>
            @endforelse
        </div>
    </div>


    <!-- Cột 3: Cấu trúc menu & Sắp xếp -->
    <div class="col-span-12 lg:col-span-9 bg-white rounded-lg shadow-sm p-4">
        @if($selectedMenu)
        <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $selectedMenu->name }}</h3>
                    <button type="button" onclick="openEditMenuModal()" class="text-gray-400 hover:text-blue-600 p-1 rounded" title="Cài đặt tên & vị trí menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                </div>
                <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                    <span>Vị trí: <strong>{{ strtoupper($selectedMenu->location ?? 'Chưa gán') }}</strong></span>
                    <span>•</span>
                    <span>Slug: <code>{{ $selectedMenu->slug }}</code></span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" onclick="openQuickAddModal()" class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold text-xs shadow-sm flex items-center gap-1.5 transition cursor-pointer" title="Thêm mục trực tiếp vào menu này">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span> Thêm mục</span>
                </button>
                <button onclick="saveMenuOrder()" class="px-3.5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-xs shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span>Lưu thay đổi</span>
                </button>
                <button onclick="deleteMenu({{ $selectedMenu->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
        
        <div id="menu-structure" class="min-h-[420px] sortable-menu space-y-1">
            @php
                if (!function_exists('renderMenuItemsRecursive')) {
                    function renderMenuItemsRecursive($items, $depth = 0) {
                        foreach ($items as $item) {
                            echo view('cms.menus.partials.menu-item', ['item' => $item, 'depth' => $depth])->render();
                            if ($item->children && $item->children->count() > 0) {
                                renderMenuItemsRecursive($item->children, $depth + 1);
                            }
                        }
                    }
                }
            @endphp

            @if($selectedMenu->items && $selectedMenu->items->count() > 0)
                @php renderMenuItemsRecursive($selectedMenu->items, 0); @endphp
            @else
                <div class="text-gray-400 text-center py-12 border-2 border-dashed rounded-lg">
                    <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    <p class="text-sm font-medium text-gray-600">Chưa có mục nào trong menu này</p>
                    <p class="text-xs text-gray-400 mt-1">Chọn từ cột bên trái hoặc bấm nút bên dưới để thêm nhanh</p>
                    <button type="button" onclick="openQuickAddModal()" class="mt-3 px-3.5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 inline-flex items-center gap-1.5 shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Thêm mục đầu tiên</span>
                    </button>
                </div>
            @endif
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Chọn menu để xem cấu trúc</p>
        @endif
    </div>
</div>

<!-- Modal tạo menu mới -->
<div id="createMenuModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Tạo menu mới</h3>
            <button onclick="closeCreateMenuModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form onsubmit="createMenu(event)" class="p-4 space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tên menu *</label>
                <input type="text" id="menu-name" name="name" placeholder="vd: Menu chính, Menu phụ..." class="w-full px-3 py-2 border rounded-lg text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Vị trí hiển thị (Location)</label>
                <select id="menu-location" name="location" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
                    <option value="header">Header (Menu thanh điều hướng trên)</option>
                    <option value="footer">Footer (Menu chân trang)</option>
                    <option value="topbar">Topbar (Menu thanh trên cùng)</option>
                    <option value="mobile">Mobile (Menu di động)</option>
                    <option value="mobile_account">Mobile Account (Menu tài khoản mobile)</option>
                    <option value="custom">Tùy chỉnh khác</option>
                </select>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeCreateMenuModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700">Tạo menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal chỉnh sửa menu hiện tại -->
@if($selectedMenu)
<div id="editMenuModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Cài đặt Menu: {{ $selectedMenu->name }}</h3>
            <button onclick="closeEditMenuModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form onsubmit="updateMenuMeta(event)" class="p-4 space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tên menu *</label>
                <input type="text" id="edit-menu-name" value="{{ $selectedMenu->name }}" class="w-full px-3 py-2 border rounded-lg text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Vị trí hiển thị (Location)</label>
                <select id="edit-menu-location" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
                    <option value="header" {{ $selectedMenu->location === 'header' ? 'selected' : '' }}>Header (Menu thanh điều hướng trên)</option>
                    <option value="footer" {{ $selectedMenu->location === 'footer' ? 'selected' : '' }}>Footer (Menu chân trang)</option>
                    <option value="topbar" {{ $selectedMenu->location === 'topbar' ? 'selected' : '' }}>Topbar (Menu thanh trên cùng)</option>
                    <option value="mobile" {{ $selectedMenu->location === 'mobile' ? 'selected' : '' }}>Mobile (Menu di động)</option>
                    <option value="mobile_account" {{ $selectedMenu->location === 'mobile_account' ? 'selected' : '' }}>Mobile Account (Menu tài khoản mobile)</option>
                    <option value="custom" {{ !in_array($selectedMenu->location, ['header', 'footer', 'topbar', 'mobile', 'mobile_account']) ? 'selected' : '' }}>Tùy chỉnh khác</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="edit-menu-active" {{ $selectedMenu->is_active ? 'checked' : '' }} class="rounded text-blue-600">
                <label for="edit-menu-active" class="text-xs font-medium text-gray-700">Đang kích hoạt (Active)</label>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeEditMenuModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Đóng</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700">Lưu cài đặt</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal thêm mục nhanh vào menu -->
<div id="quickAddModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Thêm mục vào: {{ $selectedMenu->name }}</span>
            </h3>
            <button onclick="closeQuickAddModal()" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
        </div>
        <form onsubmit="submitQuickAdd(event)" class="p-4 space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tên mục hiển thị (Title) *</label>
                <input type="text" id="quick-add-title" placeholder="VD: About Us, What We Do, Dịch vụ..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Đường dẫn / Hash Anchor (URL) *</label>
                <input type="text" id="quick-add-url" placeholder="VD: #inbetween-hero, /gioi-thieu, https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                <p class="text-[11px] text-gray-400 mt-1">Hỗ trợ liên kết hash (#section-id), đường dẫn nội bộ (/url) hoặc liên kết ngoài (https://).</p>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="quick-add-target" class="rounded border-gray-300 text-emerald-600">
                <label for="quick-add-target" class="text-xs font-medium text-gray-700 cursor-pointer">Mở trong tab mới (_blank)</label>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeQuickAddModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg text-sm hover:bg-emerald-700 shadow-sm transition">Thêm mục</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
// Alert Notification System
function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    const alert = document.createElement('div');
    alert.id = alertId;
    alert.className = `${colors[type]} p-3 rounded-lg shadow-lg flex items-center justify-between text-xs font-medium transition-all duration-300 transform translate-x-full opacity-0`;
    alert.innerHTML = `
        <span>${message}</span>
        <button onclick="closeAlert('${alertId}')" class="ml-3 text-white/80 hover:text-white font-bold">✕</button>
    `;
    
    container.appendChild(alert);
    
    setTimeout(() => {
        alert.classList.remove('translate-x-full', 'opacity-0');
    }, 10);
    
    setTimeout(() => {
        closeAlert(alertId);
    }, 4000);
}

function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => alert.remove(), 300);
    }
}

function toggleSection(section) {
    const el = document.getElementById(section + '-section');
    if (el) el.classList.toggle('hidden');
}

function openCreateMenuModal() {
    document.getElementById('createMenuModal').classList.remove('hidden');
}

function closeCreateMenuModal() {
    document.getElementById('createMenuModal').classList.add('hidden');
}

function openEditMenuModal() {
    const el = document.getElementById('editMenuModal');
    if (el) el.classList.remove('hidden');
}

function closeEditMenuModal() {
    const el = document.getElementById('editMenuModal');
    if (el) el.classList.add('hidden');
}

function openQuickAddModal() {
    const el = document.getElementById('quickAddModal');
    if (el) {
        el.classList.remove('hidden');
        const titleInput = document.getElementById('quick-add-title');
        if (titleInput) {
            setTimeout(() => titleInput.focus(), 100);
        }
    }
}

function closeQuickAddModal() {
    const el = document.getElementById('quickAddModal');
    if (el) el.classList.add('hidden');
}

function generateSlug(name) {
    return name.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/Đ/g, 'D')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

function createMenu(e) {
    e.preventDefault();
    const name = document.getElementById('menu-name').value.trim();
    const locationVal = document.getElementById('menu-location').value;
    
    if (!name) {
        showAlert('Vui lòng nhập tên menu', 'warning');
        return;
    }
    
    const slug = generateSlug(name);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang tạo...';
    
    const currentPath = window.location.pathname;
    const storeUrl = currentPath.replace(/\/menus.*/, '/menus');
    
    fetch(storeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name, slug, location: locationVal })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert('Menu đã được tạo thành công!', 'success');
            closeCreateMenuModal();
            setTimeout(() => {
                window.location.href = `${storeUrl}/${data.menu.id}`;
            }, 400);
        } else {
            showAlert(data.message || 'Không thể tạo menu', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tạo menu';
        }
    })
    .catch(err => {
        showAlert('Lỗi kết nối khi tạo menu', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Tạo menu';
    });
}

function updateMenuMeta(e) {
    e.preventDefault();
    const name = document.getElementById('edit-menu-name').value.trim();
    const locationVal = document.getElementById('edit-menu-location').value;
    const isActive = document.getElementById('edit-menu-active').checked ? 1 : 0;
    
    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    
    fetch(`${baseUrl}/{{ $selectedMenu->id ?? 0 }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name, location: locationVal, is_active: isActive })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert('Đã lưu cài đặt menu thành công!', 'success');
            closeEditMenuModal();
            setTimeout(() => location.reload(), 400);
        } else {
            showAlert(data.message || 'Lỗi cập nhật menu', 'error');
        }
    })
    .catch(err => showAlert('Lỗi kết nối', 'error'));
}

function deleteMenu(id) {
    if (confirm('Bạn có chắc chắn muốn xóa menu này? Tất cả các mục bên trong sẽ bị xóa!')) {
        const currentPath = window.location.pathname;
        const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
        
        fetch(`${baseUrl}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('Đã xóa menu thành công!', 'success');
                setTimeout(() => {
                    window.location.href = baseUrl;
                }, 400);
            } else {
                showAlert(data.message || 'Lỗi khi xóa menu', 'error');
            }
        })
        .catch(err => showAlert('Lỗi kết nối khi xóa menu', 'error'));
    }
}

// Thêm các mục được chọn từ cột trái
function addSelectedItems(type) {
    const checkboxes = document.querySelectorAll(`input[data-type="${type}"]:checked`);
    if (checkboxes.length === 0) {
        showAlert('Vui lòng chọn ít nhất một mục để thêm', 'warning');
        return;
    }

    checkboxes.forEach(cb => {
        let modelName = 'Post';
        if (type === 'productcategory') modelName = 'ProductCategory';
        else if (type === 'postcategory') modelName = 'Taxonomy';
        else if (type === 'product') modelName = 'Product';
        else if (type === 'brand') modelName = 'Brand';
        else if (type === 'media') modelName = null;

        const itemPayload = {
            title: cb.dataset.title,
            url: cb.dataset.url || null,
            linkable_type: modelName ? ('App\\Models\\' + modelName) : null,
            linkable_id: modelName ? cb.dataset.id : null,
            target: '_self'
        };

        addMenuItem(itemPayload);
        cb.checked = false;
    });
}

function addCustomLink(e) {
    e.preventDefault();
    const title = document.getElementById('link-title').value.trim();
    const slug = document.getElementById('link-slug').value.trim();
    const isBlank = document.getElementById('link-target') && document.getElementById('link-target').checked;
    
    let url = slug;
    if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('#') && !url.startsWith('/')) {
        url = '/' + url;
    }

    addMenuItem({
        title: title,
        url: url,
        target: isBlank ? '_blank' : '_self'
    });

    e.target.reset();
}

function submitQuickAdd(e) {
    e.preventDefault();
    const title = document.getElementById('quick-add-title').value.trim();
    const urlVal = document.getElementById('quick-add-url').value.trim();
    const isBlank = document.getElementById('quick-add-target') && document.getElementById('quick-add-target').checked;

    if (!title || !urlVal) {
        showAlert('Vui lòng nhập đầy đủ tiêu đề và liên kết', 'warning');
        return;
    }

    let url = urlVal;
    if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('#') && !url.startsWith('/')) {
        url = '/' + url;
    }

    addMenuItem({
        title: title,
        url: url,
        target: isBlank ? '_blank' : '_self'
    });

    closeQuickAddModal();
    e.target.reset();
}

function addMenuItem(data) {
    @if($selectedMenu)
    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    
    fetch(`${baseUrl}/{{ $selectedMenu->id }}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        let result = {};
        try {
            result = await res.json();
        } catch(e) {
            result = { success: false, message: 'Lỗi phản hồi từ máy chủ (' + res.status + ')' };
        }
        if (res.ok && result.success) {
            showAlert('Đã thêm mục menu thành công!', 'success');
            setTimeout(() => location.reload(), 400);
        } else {
            showAlert(result.message || 'Không thể thêm mục menu', 'error');
        }
    })
    .catch(err => {
        showAlert('Lỗi kết nối khi thêm mục menu: ' + err.message, 'error');
    });
    @else
    showAlert('Vui lòng chọn hoặc tạo menu trước', 'warning');
    @endif
}

function toggleItemEdit(id, event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById(`item-edit-panel-${id}`);
    if (panel) {
        panel.classList.toggle('hidden');
    }
}

function saveItemDetails(id, event) {
    event.preventDefault();
    const btn = document.getElementById(`save-item-btn-${id}`);
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>Đang lưu...</span>';

    const title = document.getElementById(`edit-title-${id}`).value.trim();
    const url = document.getElementById(`edit-url-${id}`).value.trim();
    const target = document.getElementById(`edit-target-${id}`).value;
    const icon = document.getElementById(`edit-icon-${id}`).value.trim();
    const image = document.getElementById(`edit-image-${id}`).value.trim();
    const badge = document.getElementById(`edit-badge-${id}`).value.trim();
    const badgeColor = document.getElementById(`edit-badge-color-${id}`).value;

    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');

    fetch(`${baseUrl}/items/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            title: title,
            url: url,
            target: target,
            icon: icon,
            image: image,
            badge: badge,
            badge_color: badgeColor
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (data.success) {
            showAlert('Đã cập nhật mục menu!', 'success');
            
            // Update display elements
            const titleDisplay = document.getElementById(`item-title-display-${id}`);
            if (titleDisplay) titleDisplay.textContent = title;
            
            const urlDisplay = document.getElementById(`item-url-display-${id}`);
            if (urlDisplay) urlDisplay.textContent = `→ ${url}`;
            
            const badgePreview = document.getElementById(`item-badge-preview-${id}`);
            if (badgePreview) {
                if (badge) {
                    badgePreview.textContent = badge;
                    badgePreview.style.backgroundColor = badgeColor || '#ef4444';
                    badgePreview.classList.remove('hidden');
                } else {
                    badgePreview.classList.add('hidden');
                }
            }

            toggleItemEdit(id);
        } else {
            showAlert(data.message || 'Lỗi cập nhật mục menu', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        showAlert('Lỗi kết nối khi cập nhật mục menu', 'error');
    });
}

function deleteItem(id) {
    if (confirm('Xác nhận xóa mục menu này?')) {
        const currentPath = window.location.pathname;
        const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
        
        fetch(`${baseUrl}/items/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('Đã xóa mục menu!', 'success');
                const el = document.querySelector(`.menu-item[data-id="${id}"]`);
                if (el) el.remove();
                markAsChanged();
            } else {
                showAlert('Không thể xóa mục menu', 'error');
            }
        })
        .catch(err => showAlert('Lỗi kết nối khi xóa', 'error'));
    }
}

// Di chuyển thứ tự và cấp độ
function moveUp(id) {
    const item = document.querySelector(`.menu-item[data-id="${id}"]`);
    const prev = item.previousElementSibling;
    if (prev && prev.classList.contains('menu-item')) {
        item.parentNode.insertBefore(item, prev);
        markAsChanged();
    }
}

function moveDown(id) {
    const item = document.querySelector(`.menu-item[data-id="${id}"]`);
    const next = item.nextElementSibling;
    if (next && next.classList.contains('menu-item')) {
        item.parentNode.insertBefore(next, item);
        markAsChanged();
    }
}

function indentRight(id) {
    const item = document.querySelector(`.menu-item[data-id="${id}"]`);
    const currentDepth = parseInt(item.dataset.depth || 0);
    if (currentDepth < 3) {
        updateItemDepth(item, currentDepth + 1);
        markAsChanged();
    }
}

function indentLeft(id) {
    const item = document.querySelector(`.menu-item[data-id="${id}"]`);
    const currentDepth = parseInt(item.dataset.depth || 0);
    if (currentDepth > 0) {
        updateItemDepth(item, currentDepth - 1);
        markAsChanged();
    }
}

function updateItemDepth(item, depth) {
    item.classList.remove(...Array.from(item.classList).filter(cls => cls.startsWith('depth-')));
    item.classList.add(`depth-${depth}`);
    item.dataset.depth = depth;
    updateIndentButtons(item, depth);
}

function updateIndentButtons(item, depth) {
    const btnContainer = item.querySelector('.flex.items-center.gap-1');
    if (!btnContainer) return;
    
    // Refresh indent buttons
    const oldIndentRight = btnContainer.querySelector('[title="Tạo menu con"]');
    const oldIndentLeft = btnContainer.querySelector('[title="Hủy phân cấp"]');
    if (oldIndentRight) oldIndentRight.remove();
    if (oldIndentLeft) oldIndentLeft.remove();
    
    const deleteBtn = btnContainer.querySelector('[title="Xóa"]');
    
    if (depth < 3) {
        const rBtn = document.createElement('button');
        rBtn.type = 'button';
        rBtn.onclick = () => indentRight(item.dataset.id);
        rBtn.className = 'p-1 hover:bg-green-100 text-green-600 rounded transition';
        rBtn.title = 'Tạo menu con';
        rBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
        btnContainer.insertBefore(rBtn, deleteBtn);
    }
    
    if (depth > 0) {
        const lBtn = document.createElement('button');
        lBtn.type = 'button';
        lBtn.onclick = () => indentLeft(item.dataset.id);
        lBtn.className = 'p-1 hover:bg-orange-100 text-orange-600 rounded transition';
        lBtn.title = 'Hủy phân cấp';
        lBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>';
        btnContainer.insertBefore(lBtn, deleteBtn);
    }
}

function markAsChanged() {
    const saveBtn = document.querySelector('button[onclick="saveMenuOrder()"]');
    if (saveBtn) {
        saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        saveBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
        saveBtn.querySelector('span').textContent = 'Có thay đổi - Bấm để lưu';
    }
}

function buildMenuTree() {
    const menuStructure = document.getElementById('menu-structure');
    const items = [];
    const parentStack = [];
    
    Array.from(menuStructure.children).forEach((item, index) => {
        if (item.classList && item.classList.contains('menu-item')) {
            const depth = parseInt(item.dataset.depth || 0);
            const itemId = parseInt(item.dataset.id);
            
            parentStack.length = depth;
            
            let parentId = null;
            if (depth > 0 && parentStack.length > 0) {
                parentId = parentStack[depth - 1];
            }
            
            parentStack[depth] = itemId;
            
            items.push({
                id: itemId,
                order: index,
                depth: depth,
                parent_id: parentId
            });
        }
    });
    
    return items;
}

function saveMenuOrder() {
    const saveBtn = document.querySelector('button[onclick="saveMenuOrder()"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.querySelector('span').textContent = 'Đang lưu...';
    }

    const currentPath = window.location.pathname;
    const baseUrl = currentPath.replace(/\/menus.*/, '/menus');
    const tree = buildMenuTree();

    fetch(`${baseUrl}/{{ $selectedMenu->id ?? 0 }}/update-tree`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tree: tree })
    })
    .then(res => res.json())
    .then(data => {
        if (saveBtn) {
            saveBtn.disabled = false;
        }
        if (data.success) {
            showAlert('Cấu trúc menu đã được lưu thành công!', 'success');
            if (saveBtn) {
                saveBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                saveBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                saveBtn.querySelector('span').textContent = 'Đã lưu!';
                setTimeout(() => {
                    saveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    saveBtn.querySelector('span').textContent = 'Lưu thay đổi';
                }, 2000);
            }
        } else {
            showAlert(data.message || 'Lỗi khi lưu cấu trúc menu', 'error');
            if (saveBtn) {
                saveBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                saveBtn.querySelector('span').textContent = 'Lưu thay đổi';
            }
        }
    })
    .catch(err => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.querySelector('span').textContent = 'Lưu thay đổi';
        }
        showAlert('Lỗi kết nối khi lưu cấu trúc', 'error');
    });
}

// Khởi tạo SortableJS khi tải trang
document.addEventListener('DOMContentLoaded', function() {
    const menuStructure = document.getElementById('menu-structure');
    if (menuStructure && typeof Sortable !== 'undefined') {
        new Sortable(menuStructure, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'opacity-40',
            chosenClass: 'bg-blue-50',
            onEnd: function() {
                markAsChanged();
            }
        });
    }
});
</script>

<style>
/* Indentation styling based on depth */
.depth-0 { margin-left: 0; border-left: 4px solid #3b82f6; }
.depth-1 { margin-left: 28px; border-left: 4px solid #10b981; }
.depth-2 { margin-left: 56px; border-left: 4px solid #f59e0b; }
.depth-3 { margin-left: 84px; border-left: 4px solid #8b5cf6; }

.menu-item {
    transition: transform 0.15s ease, margin-left 0.2s ease, box-shadow 0.15s ease;
}
.menu-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
</style>
@endsection

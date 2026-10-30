@php
    $isInline = $inline ?? false;
    $hideTrigger = $hideTrigger ?? false;
    $managerBaseUrl = url('admin');
@endphp
<!-- Media Manager Modal -->
<div x-data="mediaManager({{ $isInline ? 'true' : 'false' }})" x-cloak @submit.prevent @keydown.window.delete="handleGlobalDelete($event)">
    <!-- Trigger Button -->
    @if(!$isInline && !$hideTrigger)
    <button type="button" @click.prevent.stop="openModal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        {{ $slot ?? 'Chọn từ thư viện' }}
    </button>
    @endif

    <!-- Modal -->
    <div x-show="isOpen" class="{{ $isInline ? '' : 'fixed inset-0 z-[99999] overflow-y-auto' }}" style="{{ $isInline ? '' : 'display: none;' }}" @if(!$isInline) @click.stop @endif>
        <div class="{{ $isInline ? 'h-full' : 'flex items-center justify-center min-h-screen px-4' }}">
            @if(!$isInline)
            <div x-show="isOpen" @click.prevent.stop="closeModal()" class="fixed inset-0 bg-black bg-opacity-50"></div>
            @endif

            <div x-show="isOpen" class="relative bg-white rounded-lg {{ $isInline ? 'shadow-sm border w-full h-[calc(100vh-8rem)]' : 'shadow-xl max-w-7xl w-full h-[95vh]' }} flex flex-col">
                <!-- Header -->
                @if(!$isInline)
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">Quản lý Media</h3>
                    <button type="button" @click.prevent.stop="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @endif

                <!-- Toolbar -->
                <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                    <input type="file" x-ref="fileInput" @change="uploadFiles($event)" multiple accept="image/*" class="hidden">
                    <input type="file" x-ref="folderInput" @change="uploadFiles($event)" webkitdirectory directory multiple class="hidden">
                    <button type="button" @click.prevent.stop="$refs.fileInput.click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload
                    </button>
                    <button type="button" @click.prevent.stop="$refs.folderInput.click()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m-3-3h6"></path>
                        </svg>
                        Upload Folder
                    </button>
                    <button type="button" @click.prevent.stop="showCreateFolder = true" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Tạo thư mục
                    </button>
                    <div class="flex-1"></div>
                    <input type="text" x-model="searchQuery" @input.stop="filterMedia()" placeholder="Tìm kiếm..." class="px-4 py-2 border rounded-lg w-64">
                </div>

                <!-- Breadcrumb -->
                <div class="px-4 py-2 bg-gray-50 border-b flex items-center gap-2 text-sm">
                    <button type="button" @click.prevent.stop="navigateToFolder('')" class="text-blue-600 hover:underline">Root</button>
                    <template x-for="(part, index) in currentPath.split('/').filter(p => p)" :key="index">
                        <div class="flex items-center gap-2">
                            <span>/</span>
                            <button type="button" @click.prevent.stop="navigateToFolder(currentPath.split('/').filter(p => p).slice(0, index + 1).join('/'))" 
                                    class="text-blue-600 hover:underline" x-text="part"></button>
                        </div>
                    </template>
                </div>

                <!-- Create Folder Modal -->
                <div x-show="showCreateFolder" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10" @click.stop>
                    <div @click.stop class="bg-white rounded-lg p-6 w-96">
                        <h4 class="font-semibold mb-4">Tạo thư mục mới</h4>
                        <input type="text" x-model="newFolderName" @keyup.enter.prevent.stop="createFolder()" 
                               placeholder="Tên thư mục" class="w-full px-4 py-2 border rounded-lg mb-4">
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click.prevent.stop="showCreateFolder = false; newFolderName = ''" 
                                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                            <button type="button" @click.prevent.stop="createFolder()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tạo</button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4"
                     @dragover.prevent="isDraggingOver = true"
                     @dragleave.prevent="isDraggingOver = false"
                     @drop.prevent="handleFileDrop($event)"
                     :class="{'bg-blue-50 border-2 border-dashed border-blue-400': isDraggingOver}">
                    <div x-show="loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-gray-600">Đang tải...</p>
                    </div>

                    <div x-show="!loading && folders.length === 0 && filteredMedia.length === 0" class="text-center py-12 text-gray-500">
                        Thư mục trống. Kéo thả file hoặc thư mục vào đây để tải lên.
                    </div>

                    <div x-show="!loading && (folders.length > 0 || filteredMedia.length > 0)" class="space-y-4">
                        <!-- Folders -->
                        <div x-show="folders.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <template x-for="folder in folders" :key="folder.name">
                                <div @click="navigateToFolder(folder.path)"
                                     @dblclick="navigateToFolder(folder.path)"
                                     draggable="true"
                                     @dragstart="dragStartFolder($event, folder)"
                                     @dragover.prevent="folder.dragOver = true"
                                     @dragleave="folder.dragOver = false"
                                     @drop.prevent="handleDrop($event, folder.path); folder.dragOver = false"
                                     :class="{'border-blue-500 bg-blue-50 scale-105': folder.dragOver, 'opacity-50': folder.dragging}"
                                     class="relative border-2 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-all duration-200 group">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                        <span class="mt-2 text-sm text-center truncate w-full" x-text="folder.name"></span>
                                    </div>
                                    <button type="button" @click.stop="deleteFolder(folder.path)" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Files -->
                        <div x-show="filteredMedia.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <template x-for="item in filteredMedia" :key="item.id">
                                <div @click="selectMedia(item)"
                                     @dblclick.prevent.stop="selectMedia(item); if (!isInline) confirmSelection($event)"
                                     draggable="true"
                                     @dragstart="dragStart($event, item)"
                                     @dragend="item.dragging = false"
                                     :class="{'ring-4 ring-blue-500 border-blue-600': isSelected(item.id), 'opacity-50 scale-95': item.dragging}"
                                     class="relative aspect-square border-2 rounded-lg overflow-hidden cursor-pointer hover:border-blue-500 hover:scale-105 transition-all duration-200 group">
                                    <img :src="item.url" :alt="item.name" draggable="false" class="w-full h-full object-contain p-1 select-none pointer-events-none">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center pointer-events-none"
                                         :class="{'bg-blue-600/15': isSelected(item.id)}">
                                        <div x-show="isSelected(item.id)" class="bg-blue-600 text-white rounded-full p-1 shadow-md">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <button type="button" @click.stop="deleteMedia(item.id)" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                @if(!$isInline)
                <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span x-text="selectedItems.length"></span> ảnh đã chọn
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click.prevent.stop="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</button>
                        <button type="button" @click.prevent.stop="confirmSelection()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Chọn ảnh</button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Upload Progress Modal -->
        <div x-show="uploading" style="display: none;" class="fixed inset-0 z-[100] bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Đang tải lên... (<span x-text="uploadCurrent"></span>/<span x-text="uploadTotal"></span>)
                    </h3>
                    <button x-show="uploadCurrent === uploadTotal" @click="uploading = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 border-b">
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" :style="`width: ${uploadTotal > 0 ? (uploadCurrent / uploadTotal) * 100 : 0}%`"></div>
                    </div>
                </div>

                <!-- List of files -->
                <div class="p-4 overflow-y-auto flex-1">
                    <div class="space-y-2">
                        <template x-for="item in uploadItems" :key="item.name">
                            <div class="flex justify-between items-center py-2 border-b text-sm">
                                <div class="flex items-center gap-2 truncate max-w-[70%]">
                                    <svg x-show="item.status === 'success'" class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <svg x-show="item.status === 'error'" class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <svg x-show="item.status === 'uploading'" class="w-4 h-4 text-blue-500 animate-spin flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <svg x-show="item.status === 'pending'" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="item.name" class="truncate text-gray-700" :title="item.name"></span>
                                </div>
                                <span x-text="item.status === 'success' ? 'Hoàn thành' : (item.status === 'error' ? 'Lỗi' : (item.status === 'uploading' ? 'Đang tải...' : 'Chờ...'))"
                                      :class="item.status === 'success' ? 'text-green-600 font-medium' : (item.status === 'error' ? 'text-red-600 font-medium' : (item.status === 'uploading' ? 'text-blue-600' : 'text-gray-500'))" class="text-xs"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-[60] flex items-center gap-3"
             :class="notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             style="display: none;">
            <svg x-show="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg x-show="notification.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span x-text="notification.message"></span>
        </div>

        <!-- Confirm Delete Modal -->
        <div x-show="showConfirmDelete" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;" @click.stop>
            <div x-show="showConfirmDelete"
                 @click.away="showConfirmDelete = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
                
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="deleteType === 'folder' ? 'Xóa thư mục?' : 'Bạn có chắc chắn?'"></h3>
                <p class="text-gray-500 mb-6" x-text="deleteMessage"></p>
                
                <div class="flex gap-3 justify-center">
                    <button @click.prevent.stop="showConfirmDelete = false" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">Hủy bỏ</button>
                    <button @click.prevent.stop="executeDelete()" class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 shadow-sm shadow-red-600/30 transition">
                        <span x-show="!loading">Đồng ý, Xóa</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Đang xóa...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mediaManager(isInline = false) {
    return {
        isInline: isInline,
        isOpen: isInline,
        loading: false,
        isDraggingOver: false,
        currentPath: '',
        folders: [],
        mediaItems: [],
        filteredMedia: [],
        selectedItems: [],
        searchQuery: '',
        showCreateFolder: false,
        isDraggingOver: false,
        newFolderName: '',
        baseUrl: '{{ $managerBaseUrl }}',
        notification: { show: false, message: '', type: 'success' },
        showConfirmDelete: false,
        deleteType: 'file', // 'file' or 'folder'
        deleteTarget: null,
        deleteMessage: '',

        showNotification(message, type = 'success') {
            this.notification = { show: true, message, type };
            setTimeout(() => this.notification.show = false, 3000);
        },
        
        targetField: null,
        multiple: true,

        init() {
            if (this.isInline) {
                this.loadMedia();
            }
            window.addEventListener('open-media-manager', (e) => {
                const detail = e.detail || {};
                this.targetField = detail.field || detail.target || 'ckeditor';
                this.multiple = detail.multiple !== undefined ? !!detail.multiple : false;
                if (detail.folder !== undefined && detail.folder !== null) {
                    this.currentPath = String(detail.folder).replace(/^\/+|\/+$/g, '');
                }
                this.openModal();
            });
            window.addEventListener('open-media-picker', (e) => {
                const detail = e.detail || {};
                this.targetField = detail.field || detail.target || 'ckeditor';
                this.multiple = detail.multiple !== undefined ? !!detail.multiple : false;
                if (detail.folder !== undefined && detail.folder !== null) {
                    this.currentPath = String(detail.folder).replace(/^\/+|\/+$/g, '');
                }
                this.openModal();
            });
        },

        openModal(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.isOpen = true;
            this.loadMedia();
        },
        
        closeModal(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.isOpen = false;
            this.selectedItems = [];
        },
        
        async loadMedia() {
            this.loading = true;
            try {
                const response = await fetch(`${this.baseUrl}/media/list?path=${encodeURIComponent(this.currentPath)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.folders = data.folders || [];
                this.mediaItems = data.files || [];
                this.filteredMedia = this.mediaItems;
                this.loading = false;
            } catch (error) {
                console.error('Error loading media:', error);
                this.loading = false;
            }
        },
        
        navigateToFolder(path) {
            this.currentPath = path;
            this.selectedItems = [];
            this.loadMedia();
        },
        
        async createFolder() {
            if (!this.newFolderName) return;
            
            try {
                const response = await fetch(`${this.baseUrl}/media/folder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        path: this.currentPath,
                        name: this.newFolderName
                    })
                });
                
                if (response.ok) {
                    this.showCreateFolder = false;
                    this.newFolderName = '';
                    this.showNotification('Tạo thư mục thành công!');
                    this.loadMedia();
                } else {
                    this.showNotification('Tạo thư mục thất bại', 'error');
                }
            } catch (error) {
                console.error('Create folder error:', error);
                this.showNotification('Lỗi: ' + error.message, 'error');
            }
        },
        
        async deleteFolder(path) {
            this.deleteType = 'folder';
            this.deleteTarget = path;
            this.deleteMessage = 'Bạn có chắc chắn muốn xóa thư mục này và tất cả nội dung bên trong? Hành động này không thể hoàn tác.';
            this.showConfirmDelete = true;
        },
        
        filterMedia() {
            if (!this.searchQuery) {
                this.filteredMedia = this.mediaItems;
                return;
            }
            this.filteredMedia = this.mediaItems.filter(item => 
                item.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
            this.selectedItems = [];
        },
        
        selectMedia(item) {
            if (!this.multiple) {
                this.selectedItems = [item];
                return;
            }
            const index = this.selectedItems.findIndex(i => i.id === item.id);
            if (index > -1) {
                this.selectedItems.splice(index, 1);
            } else {
                this.selectedItems.push(item);
            }
        },
        
        isSelected(id) {
            return this.selectedItems.some(item => item.id === id);
        },
        
        uploading: false,
        uploadTotal: 0,
        uploadCurrent: 0,
        uploadItems: [],
        
        async handleFileDrop(event) {
            this.isDraggingOver = false;
            const items = event.dataTransfer.items;
            let allFiles = [];
            const maxFiles = 1000;
            let fileCount = 0;
            let overLimit = false;

            const traverse = (item, path = '') => {
                return new Promise((resolve) => {
                    if (overLimit) return resolve([]);
                    if (item.isFile) {
                        fileCount++;
                        if (fileCount > maxFiles) {
                            overLimit = true;
                            return resolve([]);
                        }
                        item.file((file) => {
                            file.customPath = path + file.name;
                            resolve([file]);
                        });
                    } else if (item.isDirectory) {
                        const dirReader = item.createReader();
                        let files = [];
                        const readEntries = () => {
                            if (overLimit) return resolve(files);
                            dirReader.readEntries(async (entries) => {
                                if (entries.length === 0) {
                                    resolve(files);
                                } else {
                                    for (let i = 0; i < entries.length; i++) {
                                        if (overLimit) break;
                                        const nestedFiles = await traverse(entries[i], path + item.name + "/");
                                        files = files.concat(nestedFiles);
                                    }
                                    if (!overLimit) readEntries();
                                    else resolve(files);
                                }
                            });
                        };
                        readEntries();
                    } else {
                        resolve([]);
                    }
                });
            };

            if (items) {
                const queue = [];
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (item.kind === 'file') {
                        const entry = item.webkitGetAsEntry();
                        if (entry) {
                            queue.push(traverse(entry));
                        }
                    }
                }
                const results = await Promise.all(queue);
                results.forEach(arr => {
                    allFiles = allFiles.concat(arr);
                });
            } else {
                allFiles = Array.from(event.dataTransfer.files || []);
                if (allFiles.length > maxFiles) {
                    overLimit = true;
                    allFiles = allFiles.slice(0, maxFiles);
                }
            }
            
            if (overLimit) {
                this.showNotification(`Đã chặn vì vượt quá giới hạn ${maxFiles} file một lần để tránh treo trình duyệt. Vui lòng chia nhỏ thư mục!`, 'error');
                return;
            }
            
            if (allFiles.length > 0) {
                const allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'mp4', 'mp3'];
                const dangerousExts = ['.php', '.js', '.sh', '.exe', '.bat', '.py', '.pl', '.jsp', '.asp'];
                
                const safeFiles = allFiles.filter(file => {
                    const name = file.name.toLowerCase();
                    const ext = name.split('.').pop();
                    const isDangerous = dangerousExts.some(d => name.includes(d));
                    return allowedExts.includes(ext) && !isDangerous;
                });
                
                if (safeFiles.length > 0) {
                    if (safeFiles.length < allFiles.length) {
                        this.showNotification(`Đã loại bỏ ${allFiles.length - safeFiles.length} file không hợp lệ hoặc có nguy cơ bảo mật.`, 'error');
                    }
                    this.executeUploadFiles(safeFiles);
                } else {
                    this.showNotification('Không có file hợp lệ nào để tải lên. Chỉ hỗ trợ hình ảnh và tài liệu.', 'error');
                }
            }
        },

        async uploadFiles(event) {
            const files = Array.from(event.target.files);
            if (files.length === 0) return;
            this.executeUploadFiles(files);
            event.target.value = ''; // Reset input
        },

        async executeUploadFiles(files) {
            const allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'mp4', 'mp3'];
            const dangerousExts = ['.php', '.js', '.sh', '.exe', '.bat', '.py', '.pl', '.jsp', '.asp'];
            
            const safeFiles = files.filter(file => {
                const name = file.name.toLowerCase();
                const ext = name.split('.').pop();
                const isDangerous = dangerousExts.some(d => name.includes(d));
                return allowedExts.includes(ext) && !isDangerous;
            });
            
            if (safeFiles.length === 0) {
                this.showNotification('Không có file hợp lệ nào để tải lên. Chỉ hỗ trợ hình ảnh và tài liệu.', 'error');
                return;
            }
            
            if (safeFiles.length < files.length) {
                this.showNotification(`Đã loại bỏ ${files.length - safeFiles.length} file không hợp lệ hoặc có nguy cơ bảo mật.`, 'error');
            }

            this.uploadTotal = safeFiles.length;
            this.uploadCurrent = 0;
            this.uploadItems = safeFiles.map(f => ({ name: f.customPath || f.webkitRelativePath || f.name, status: 'pending' }));
            this.uploading = true;
            
            const uploadUrl = `${this.baseUrl}/media/upload`;
            let hasError = false;
            let allWarnings = [];
            let newlyUploaded = [];

            for (let i = 0; i < safeFiles.length; i++) {
                const file = safeFiles[i];
                this.uploadItems[i].status = 'uploading';
                
                const formData = new FormData();
                formData.append('path', this.currentPath);
                formData.append('files[]', file);
                formData.append('paths[]', file.customPath || file.webkitRelativePath || '');

                try {
                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.uploadItems[i].status = 'success';
                        try {
                            const result = await response.json();
                            if (result.uploaded && result.uploaded.length > 0) {
                                newlyUploaded = newlyUploaded.concat(result.uploaded);
                            }
                            if (result.warnings && result.warnings.length > 0) {
                                allWarnings = allWarnings.concat(result.warnings);
                            }
                        } catch(e) {}
                    } else {
                        this.uploadItems[i].status = 'error';
                        hasError = true;
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    this.uploadItems[i].status = 'error';
                    hasError = true;
                }
                this.uploadCurrent++;
            }

            if (hasError) {
                this.showNotification('Quá trình tải lên có lỗi!', 'error');
            } else if (allWarnings.length > 0) {
                this.showNotification('Tải lên hoàn tất nhưng có cảnh báo!', 'warning');
                setTimeout(() => {
                    alert("CẢNH BÁO BẢO MẬT:\n" + allWarnings.join("\n"));
                }, 500);
            } else {
                this.showNotification('Tải lên hoàn tất!');
            }
            
            await this.loadMedia();

            // Ưu tiên hiển thị vị trí đầu và tự động chọn file vừa tải lên
            if (newlyUploaded.length > 0) {
                const uploadedPaths = new Set(newlyUploaded.map(u => u.path || u.id));
                const uploadedNames = new Set(newlyUploaded.map(u => u.name));
                const matched = this.mediaItems.filter(f => uploadedPaths.has(f.path) || uploadedNames.has(f.name) || uploadedPaths.has(f.id));
                const others = this.mediaItems.filter(f => !uploadedPaths.has(f.path) && !uploadedNames.has(f.name) && !uploadedPaths.has(f.id));
                if (matched.length > 0) {
                    this.mediaItems = [...matched, ...others];
                    this.filteredMedia = this.mediaItems;
                    this.selectedItems = [matched[0]];
                }
            }

            setTimeout(() => {
                this.uploading = false;
            }, 600);
        },
        
        async deleteMedia(id) {
            this.deleteType = 'single';
            this.deleteTarget = id;
            this.deleteMessage = 'Bạn có chắc chắn muốn xóa hình ảnh này? Hành động này không thể hoàn tác.';
            this.showConfirmDelete = true;
        },
        
        async executeDelete() {
            this.loading = true;
            try {
                if (this.deleteType === 'folder') {
                    const response = await fetch(`${this.baseUrl}/media/folder`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ path: this.deleteTarget })
                    });
                    if (response.ok) {
                        this.showNotification('Xóa thư mục thành công!');
                        this.loadMedia();
                    } else {
                        this.showNotification('Lỗi khi xóa thư mục', 'error');
                    }
                } else if (this.deleteType === 'single') {
                    const response = await fetch(`${this.baseUrl}/media/${this.deleteTarget}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (response.ok) {
                        this.showNotification('Xóa hình ảnh thành công!');
                        this.loadMedia();
                    } else {
                        this.showNotification('Lỗi khi xóa hình ảnh', 'error');
                    }
                } else if (this.deleteType === 'multiple') {
                    const deletePromises = this.selectedItems.map(item => 
                        fetch(`${this.baseUrl}/media/${item.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                    );
                    
                    const results = await Promise.allSettled(deletePromises);
                    const failed = results.filter(r => r.status === 'rejected' || (r.status === 'fulfilled' && !r.value.ok));
                    
                    if (failed.length > 0) {
                        this.showNotification(`Không thể xóa ${failed.length} ảnh.`, 'error');
                    } else {
                        this.showNotification(`Xóa thành công ${this.selectedItems.length} ảnh!`);
                    }
                    
                    this.selectedItems = [];
                    this.lastSelectedId = null;
                    this.loadMedia();
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showNotification('Đã xảy ra lỗi khi xóa', 'error');
            }
            this.loading = false;
            this.showConfirmDelete = false;
        },
        
        handleGlobalDelete(event) {
            if (!this.isOpen) return;
            if (['INPUT', 'TEXTAREA'].includes(event.target.tagName)) return;
            this.deleteSelectedMedia();
        },
        
        async deleteSelectedMedia() {
            if (this.selectedItems.length === 0) return;
            this.deleteType = 'multiple';
            this.deleteTarget = null;
            this.deleteMessage = `Bạn đang chuẩn bị xóa ${this.selectedItems.length} hình ảnh đã chọn. Hành động này không thể hoàn tác!`;
            this.showConfirmDelete = true;
        },
        
        dragStart(event, item) {
            item.dragging = true;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('mediaItem', JSON.stringify(item));
            event.dataTransfer.setData('type', 'file');
            
            // Custom drag image
            const dragImage = event.target.cloneNode(true);
            dragImage.style.opacity = '0.8';
            dragImage.style.transform = 'rotate(5deg)';
            document.body.appendChild(dragImage);
            event.dataTransfer.setDragImage(dragImage, 50, 50);
            setTimeout(() => document.body.removeChild(dragImage), 0);
        },
        
        dragStartFolder(event, folder) {
            folder.dragging = true;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('folder', JSON.stringify(folder));
            event.dataTransfer.setData('type', 'folder');
        },
        
        async handleDrop(event, targetPath) {
            const type = event.dataTransfer.getData('type');
            
            if (type === 'file') {
                const item = JSON.parse(event.dataTransfer.getData('mediaItem'));
                if (!confirm(`Di chuyển "${item.name}" vào thư mục này?`)) return;
                
                try {
                    const response = await fetch(`${this.baseUrl}/media/move`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            from: item.path,
                            to: targetPath,
                            type: 'file'
                        })
                    });
                    
                    if (response.ok) {
                        this.loadMedia();
                    }
                } catch (error) {
                    console.error('Move error:', error);
                }
            } else if (type === 'folder') {
                const folder = JSON.parse(event.dataTransfer.getData('folder'));
                if (folder.path === targetPath) return; // Can't drop on itself
                
                if (!confirm(`Di chuyển thư mục "${folder.name}" vào đây?`)) return;
                
                try {
                    const response = await fetch(`${this.baseUrl}/media/move`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            from: folder.path,
                            to: targetPath,
                            type: 'folder'
                        })
                    });
                    
                    if (response.ok) {
                        this.loadMedia();
                    }
                } catch (error) {
                    console.error('Move folder error:', error);
                }
            }
        },
        
        confirmSelection(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (this.selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một ảnh');
                return;
            }
            const urls = this.selectedItems.map(item => item.url);
            const files = this.selectedItems.map(item => ({
                id: item.id,
                name: item.name,
                url: item.url,
                path: item.path
            }));
            const result = {
                field: this.targetField || 'ckeditor',
                target: this.targetField || 'ckeditor',
                url: urls[0] || '',
                value: urls[0] || '',
                file: files[0] || null,
                urls: this.multiple ? urls : urls[0],
                files: files,
                folders: []
            };
            window.dispatchEvent(new CustomEvent('media-selected', {
                detail: result
            }));
            window.dispatchEvent(new CustomEvent('media-picker-selected', {
                detail: result
            }));
            this.closeModal();
        }
    }
}
</script>

<style>
[x-cloak] { display: none; }
</style>

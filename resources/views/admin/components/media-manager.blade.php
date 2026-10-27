@php
    $managerBaseUrl = url('admin');
@endphp
<!-- Media Manager Modal -->
<div x-data="mediaManager()" x-cloak @keydown.window.delete="handleGlobalDelete($event)">
    <!-- Trigger Button -->
    <button type="button" @click.prevent="openModal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        {{ $slot ?? 'Chọn từ thư viện' }}
    </button>

    <!-- Modal -->
    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="isOpen" @click="closeModal()" class="fixed inset-0 bg-black bg-opacity-50"></div>

            <div x-show="isOpen" class="relative bg-white rounded-lg shadow-xl max-w-7xl w-full h-[95vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">Quản lý Media</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Toolbar -->
                <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
                    <input type="file" x-ref="fileInput" @change="uploadFiles($event)" multiple accept="image/*" class="hidden">
                    <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload
                    </button>
                    <button type="button" @click="showCreateFolder = true" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Tạo thư mục
                    </button>
                    <div class="flex-1"></div>
                    <input type="text" x-model="searchQuery" @input="filterMedia()" placeholder="Tìm kiếm..." class="px-4 py-2 border rounded-lg w-64">
                </div>

                <!-- Breadcrumb -->
                <div class="px-4 py-2 bg-gray-50 border-b flex items-center gap-2 text-sm">
                    <button @click="navigateToFolder('')" class="text-blue-600 hover:underline">Root</button>
                    <template x-for="(part, index) in currentPath.split('/').filter(p => p)" :key="index">
                        <div class="flex items-center gap-2">
                            <span>/</span>
                            <button @click="navigateToFolder(currentPath.split('/').slice(0, index + 2).join('/'))" 
                                    class="text-blue-600 hover:underline" x-text="part"></button>
                        </div>
                    </template>
                </div>

                <!-- Create Folder Modal -->
                <div x-show="showCreateFolder" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                    <div @click.stop class="bg-white rounded-lg p-6 w-96">
                        <h4 class="font-semibold mb-4">Tạo thư mục mới</h4>
                        <input type="text" x-model="newFolderName" @keyup.enter="createFolder()" 
                               placeholder="Tên thư mục" class="w-full px-4 py-2 border rounded-lg mb-4">
                        <div class="flex gap-3 justify-end">
                            <button @click="showCreateFolder = false; newFolderName = ''" 
                                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                            <button @click="createFolder()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tạo</button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div x-show="loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-gray-600">Đang tải...</p>
                    </div>

                    <div x-show="!loading && folders.length === 0 && filteredMedia.length === 0" class="text-center py-12 text-gray-500">
                        Thư mục trống
                    </div>

                    <div x-show="!loading && (folders.length > 0 || filteredMedia.length > 0)" class="space-y-4">
                        <!-- Folders -->
                        <div x-show="folders.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <template x-for="folder in folders" :key="folder.name">
                                <div @dblclick="navigateToFolder(folder.path)"
                                     draggable="true"
                                     @dragstart="dragStartFolder($event, folder)"
                                     @dragover.prevent="folder.dragOver = true"
                                     @dragleave="folder.dragOver = false"
                                     @drop.prevent="handleDrop($event, folder.path); folder.dragOver = false"
                                     :class="{'border-blue-500 bg-blue-50 scale-105': folder.dragOver, 'opacity-50': folder.dragging}"
                                     class="relative border-2 rounded-lg p-4 cursor-move hover:border-blue-500 transition-all duration-200 group">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                        <span class="mt-2 text-sm text-center truncate w-full" x-text="folder.name"></span>
                                    </div>
                                    <button @click.stop="deleteFolder(folder.path)" 
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
                                     draggable="true"
                                     @dragstart="dragStart($event, item)"
                                     @dragend="item.dragging = false"
                                     :class="{'ring-4 ring-blue-500': isSelected(item.id), 'opacity-50 scale-95': item.dragging}"
                                     class="relative aspect-square border-2 rounded-lg overflow-hidden cursor-move hover:border-blue-500 hover:scale-105 transition-all duration-200 group">
                                    <img :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center">
                                        <svg x-show="isSelected(item.id)" class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <button @click.stop="deleteMedia(item.id)" 
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
                <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span x-text="selectedItems.length"></span> ảnh đã chọn
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</button>
                        <button type="button" @click="confirmSelection()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Chọn ảnh</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mediaManager() {
    return {
        isOpen: false,
        loading: false,
        currentPath: '',
        folders: [],
        mediaItems: [],
        filteredMedia: [],
        selectedItems: [],
        searchQuery: '',
        showCreateFolder: false,
        newFolderName: '',
        baseUrl: '{{ $managerBaseUrl }}',
        
        handleGlobalDelete(event) {
            if (!this.isOpen) return;
            if (['INPUT', 'TEXTAREA'].includes(event.target.tagName)) return;
            if (this.selectedItem) {
                this.deleteMedia(this.selectedItem.id);
            }
        },

        get previewUrl() {
            if (!this.selectedItem) return null;
            return this.selectedItem.url;
        },
        
        openModal() {
            this.isOpen = true;
            this.loadMedia();
        },
        
        closeModal() {
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
                    this.loadMedia();
                } else {
                    alert('Tạo thư mục thất bại');
                }
            } catch (error) {
                console.error('Create folder error:', error);
                alert('Lỗi: ' + error.message);
            }
        },
        
        async deleteFolder(path) {
            if (!confirm('Xóa thư mục này và tất cả nội dung bên trong?')) return;
            
            try {
                const response = await fetch(`${this.baseUrl}/media/folder`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ path })
                });
                if (response.ok) {
                    this.loadMedia();
                }
            } catch (error) {
                console.error('Delete folder error:', error);
            }
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
        
        async uploadFiles(event) {
            const files = Array.from(event.target.files);
            const formData = new FormData();
            formData.append('path', this.currentPath);
            files.forEach(file => formData.append('files[]', file));
            
            this.loading = true;
            try {
                const response = await fetch(`${this.baseUrl}/media/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                if (response.ok) {
                    this.loadMedia();
                }
            } catch (error) {
                console.error('Upload error:', error);
            }
            this.loading = false;
        },
        
        async deleteMedia(id) {
            if (!confirm('Xóa ảnh này?')) return;
            
            try {
                const response = await fetch(`${this.baseUrl}/media/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (response.ok) {
                    this.loadMedia();
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
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
        
        confirmSelection() {
            if (this.selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một ảnh');
                return;
            }
            const result = {
                folders: [],
                files: this.selectedItems.map(item => ({
                    id: item.id,
                    name: item.name,
                    url: item.url,
                    path: item.path
                }))
            };
            window.dispatchEvent(new CustomEvent('media-selected', {
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

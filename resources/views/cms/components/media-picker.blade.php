@props(['name', 'value' => '', 'label' => 'Chọn Ảnh / File'])

@php
    $inputId = 'media_picker_' . Str::random(8);
    $mediaListUrl = route('admin.media.list');
@endphp

<div x-data="mediaPicker('{{ $inputId }}', '{{ $mediaListUrl }}', '{{ $value }}')" x-cloak @keydown.window.delete="handleGlobalDelete($event)" class="media-picker-wrapper">
    <!-- Hidden file inputs for upload -->
    <input type="file" x-ref="fileInput" @change="uploadFile" multiple accept="image/*,.ico" class="hidden">
    <input type="file" x-ref="folderInput" @change="uploadFolder" webkitdirectory directory multiple class="hidden">

    <div class="flex items-start gap-3">
        <!-- Preview Area -->
        <div class="relative w-20 h-20 shrink-0 border border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center shadow-sm">
            <template x-if="previewUrl">
                <img :src="previewUrl" class="w-full h-full object-cover">
            </template>
            <template x-if="!previewUrl">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </template>
            <!-- Remove Button -->
            <button type="button" x-show="previewUrl" @click.prevent="clearSelection()" class="absolute top-1 right-1 bg-white/90 rounded-full p-0.5 shadow hover:bg-red-50 text-red-500 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 min-w-0 space-y-2">
            <input type="text" 
                   name="{{ $name }}" 
                   id="{{ $inputId }}" 
                   x-model="selectedValue" 
                   placeholder="Nh&#7853;p URL ho&#7863;c ch&#7885;n t&#7915; th&#432; vi&#7879;n" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            <button type="button" @click.prevent="openModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-rose-500 transition-colors whitespace-nowrap shadow-sm">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                {{ $label }}
            </button>
        </div>
    </div>

    <!-- Media Picker Modal -->
    <template x-teleport="body">
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-[1px]">
        <div class="relative w-full max-w-4xl max-h-full p-4" @click.away="closeModal()">
            <div class="relative bg-white rounded-lg shadow-2xl h-[80vh] flex flex-col">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Quản lý Media</h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 flex-1 overflow-y-auto bg-gray-50 relative"
                     @dragover.prevent.stop="isDraggingOver = true"
                     @dragleave.prevent.stop="if($event.target === $el) isDraggingOver = false"
                     @drop.prevent.stop="handleFileDrop($event)"
                     :class="{'bg-blue-50 border-2 border-dashed border-blue-400': isDraggingOver}">
                     
                    <!-- Drag overlay indicator -->
                    <div x-show="isDraggingOver" class="absolute inset-0 bg-blue-50 bg-opacity-90 z-20 flex flex-col items-center justify-center pointer-events-none rounded-lg" style="display: none;">
                        <svg class="w-16 h-16 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-xl font-semibold text-blue-600">Thả ảnh vào đây để tải lên</p>
                    </div>

                    <!-- Loading State -->
                    <div x-show="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>

                    <!-- Breadcrumb / Toolbar -->
                    <div class="flex flex-wrap items-center gap-2 mb-4 bg-white p-2.5 rounded-lg shadow-sm border border-gray-100">
                        <button type="button" @click="loadMedia('')" class="p-2 hover:bg-gray-100 rounded">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </button>
                        <span class="text-gray-400">/</span>
                        <span class="text-sm text-gray-600" x-text="currentPath || 'Root'"></span>
                        
                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-1.5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload
                            </button>
                            <button type="button" @click="$refs.folderInput.click()" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 flex items-center gap-1.5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m-3-3h6"></path>
                                </svg>
                                Upload Folder
                            </button>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                        <!-- Folders -->
                        <template x-for="folder in folders" :key="folder.path">
                            <div @click="loadMedia(folder.path)" class="bg-white p-3 rounded-lg shadow-sm border border-transparent hover:border-blue-500 cursor-pointer text-center group transition-all relative">
                                <svg class="w-12 h-12 mx-auto text-yellow-400 group-hover:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                </svg>
                                <p class="text-xs mt-2 truncate text-gray-700" x-text="folder.name"></p>
                                <button @click.stop="deleteFolder(folder.path)" 
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <!-- Files -->
                        <template x-for="file in files" :key="file.path">
                            <div @click="selectFile($event, file)" 
                                 :class="{'border-blue-500 ring-2 ring-blue-200': isSelected(file.path), 'border-gray-200 hover:border-blue-400': !isSelected(file.path)}"
                                 class="bg-white p-2 rounded-lg shadow-sm border cursor-pointer group transition-all relative">
                                
                                <div class="aspect-square bg-gray-100 rounded overflow-hidden flex items-center justify-center mb-2">
                                    <!-- Image preview -->
                                    <template x-if="isImage(file.url)">
                                        <img :src="file.url" class="w-full h-full object-cover">
                                    </template>
                                    <!-- Icon for non-images -->
                                    <template x-if="!isImage(file.url)">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </template>
                                </div>
                                <p class="text-xs truncate text-gray-600" x-text="file.name" :title="file.name"></p>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-text="file.size"></p>

                                <!-- Checkmark for selected -->
                                <div x-show="isSelected(file.path)" class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full p-1 shadow">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <div x-show="folders.length === 0 && files.length === 0 && !isLoading" class="col-span-full py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">Thư mục trống</p>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-between p-4 border-t bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            <span x-text="selectedFiles.length"></span> ảnh đã chọn
                        </div>
                        <button x-show="selectedFiles.length > 0" type="button" @click.prevent.stop="deleteSelectedMedia()" class="px-3 py-1 text-sm text-red-600 bg-red-100 rounded hover:bg-red-200 transition">
                            Xóa các ảnh đã chọn
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="closeModal()" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-4 focus:ring-gray-100">
                            Hủy
                        </button>
                        <button type="button" @click="confirmSelection()" :disabled="!selectedFile" class="ms-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50 disabled:cursor-not-allowed">
                            Chọn file
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Upload Progress Modal -->
        <div x-show="uploading" style="display: none;" class="fixed inset-0 z-[100000] bg-black bg-opacity-50 flex items-center justify-center p-4">
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
             class="fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-[100001] flex items-center gap-3"
             :class="notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             style="display: none;">
            <svg x-show="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg x-show="notification.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span x-text="notification.message"></span>
        </div>

        <!-- Confirm Delete Modal -->
        <div x-show="showConfirmDelete" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;" @click.stop>
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
    </template>
</div>

@push('scripts')
@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mediaPicker', (inputId, mediaListUrl, initialValue) => ({
            isOpen: false,
            loading: false,
            isLoading: false,
            currentPath: '',
            folders: [],
            files: [],
            selectedValue: initialValue,
            selectedFiles: [],
            lastSelectedPath: null,
            isDraggingOver: false,
            tinymceCallback: null,
            notification: { show: false, message: '', type: 'success' },
            showConfirmDelete: false,
            deleteType: 'file',
            deleteTarget: null,
            deleteMessage: '',

            showNotification(message, type = 'success') {
                this.notification = { show: true, message, type };
                setTimeout(() => this.notification.show = false, 3000);
            },

            get selectedFile() {
                return this.selectedFiles.length > 0 ? this.selectedFiles[this.selectedFiles.length - 1] : null;
            },

            get previewUrl() {
                if (!this.selectedValue) return null;
                if (!this.isImage(this.selectedValue)) return null;
                if (this.selectedValue.startsWith('http://') || this.selectedValue.startsWith('https://')) {
                    return this.selectedValue;
                }
                if (this.selectedValue.startsWith('/')) {
                    return this.selectedValue;
                }
                if (this.selectedValue.startsWith('storage/')) {
                    return '/' + this.selectedValue;
                }
                return '/storage/' + this.selectedValue;
            },

            init() {
                // Listen for TinyMCE media picker requests
                window.addEventListener('open-media-picker-tinymce', (e) => {
                    this.tinymceCallback = e.detail.callback;
                    this.openModal();
                });
            },

            isImage(url) {
                if (!url) return false;
                const ext = url.split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'].includes(ext);
            },

            openModal() {
                this.isOpen = true;
                this.loadMedia(this.currentPath);
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.isOpen = false;
                this.selectedFiles = [];
                this.lastSelectedPath = null;
                this.tinymceCallback = null;
                document.body.style.overflow = 'auto';
            },

            async loadMedia(path = '') {
                this.isLoading = true;
                this.currentPath = path;
                this.selectedFiles = [];
                this.lastSelectedPath = null;
                
                try {
                    const url = new URL(mediaListUrl);
                    url.searchParams.append('path', path);
                    
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        // Assume MediaController returns { folders: [], files: [] } or similar
                        this.folders = data.folders || [];
                        this.files = data.files || [];
                    } else {
                        console.error('Failed to load media');
                    }
                } catch (error) {
                    console.error('Error loading media:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            selectFile(event, file) {
                // Shift + Click for range selection
                if (event.shiftKey && this.lastSelectedPath) {
                    const startIndex = this.files.findIndex(i => i.path === this.lastSelectedPath);
                    const endIndex = this.files.findIndex(i => i.path === file.path);
                    if (startIndex > -1 && endIndex > -1) {
                        const start = Math.min(startIndex, endIndex);
                        const end = Math.max(startIndex, endIndex);
                        for (let i = start; i <= end; i++) {
                            const rangeItem = this.files[i];
                            if (!this.isSelected(rangeItem.path)) {
                                this.selectedFiles.push(rangeItem);
                            }
                        }
                    }
                    return;
                }
                
                // Ctrl + Click or Meta + Click to toggle multiple
                if (event.ctrlKey || event.metaKey) {
                    const index = this.selectedFiles.findIndex(i => i.path === file.path);
                    if (index > -1) {
                        this.selectedFiles.splice(index, 1);
                    } else {
                        this.selectedFiles.push(file);
                    }
                    this.lastSelectedPath = file.path;
                    return;
                }

                // Normal click: select only this one
                this.selectedFiles = [file];
                this.lastSelectedPath = file.path;
            },

            isSelected(path) {
                return this.selectedFiles.some(f => f.path === path);
            },
            
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
                    // Filter allowed files
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
                        await this.performUpload(safeFiles);
                    } else {
                        this.showNotification('Không có file hợp lệ nào để tải lên. Chỉ hỗ trợ hình ảnh và tài liệu.', 'error');
                    }
                }
            },

            confirmSelection() {
                if (this.selectedFile) {
                    const url = this.selectedFile.url;
                    
                    if (this.tinymceCallback) {
                        // Return to TinyMCE
                        this.tinymceCallback(url, { title: this.selectedFile.name });
                    } else {
                        // Normal input selection. We store the relative path or full url based on logic.
                        // Let's store full URL or relative path if it's in storage
                        this.selectedValue = this.selectedFile.path; 
                        
                        // Wait, for TinyMCE we return full URL, for db we might just want the path or url.
                        // Let's just use the URL so it's simple to display.
                        this.selectedValue = url;
                    }
                    
                    // Trigger change event on the hidden input so other scripts know
                    setTimeout(() => {
                        const inputEl = document.getElementById(inputId);
                        if (inputEl) {
                            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                            inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        window.dispatchEvent(new CustomEvent('media-picker-selected', { detail: { inputId: inputId, value: this.selectedValue } }));
                    }, 50);
                }
                this.closeModal();
            },

            clearSelection() {
                this.selectedValue = '';
                setTimeout(() => {
                    const inputEl = document.getElementById(inputId);
                    if (inputEl) {
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                        inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    window.dispatchEvent(new CustomEvent('media-picker-cleared', { detail: { inputId: inputId } }));
                }, 50);
            },

            uploading: false,
            uploadTotal: 0,
            uploadCurrent: 0,
            uploadItems: [],

            async uploadFile(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;
                await this.performUpload(files);
                e.target.value = ''; // Reset input
            },

            async uploadFolder(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;
                await this.performUpload(files);
                e.target.value = ''; // Reset input
            },

            async performUpload(files) {
                const allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'mp4', 'mp3'];
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
                
                const uploadUrl = mediaListUrl.replace('/list', '/upload');
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
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
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
                        console.error('Lỗi tải file:', error);
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
                
                await this.loadMedia(this.currentPath);

                // Ưu tiên hiển thị vị trí đầu và tự động chọn file vừa tải lên
                if (newlyUploaded.length > 0) {
                    const uploadedPaths = new Set(newlyUploaded.map(u => u.path || u.id));
                    const uploadedNames = new Set(newlyUploaded.map(u => u.name));
                    const matched = this.files.filter(f => uploadedPaths.has(f.path) || uploadedNames.has(f.name) || uploadedPaths.has(f.id));
                    const others = this.files.filter(f => !uploadedPaths.has(f.path) && !uploadedNames.has(f.name) && !uploadedPaths.has(f.id));
                    if (matched.length > 0) {
                        this.files = [...matched, ...others];
                        this.selectedFiles = [matched[0]];
                        this.selectedFile = matched[0];
                    }
                }

                setTimeout(() => {
                    this.uploading = false;
                }, 600);
            },
            
            handleGlobalDelete(event) {
                if (!this.showModal) return;
                if (['INPUT', 'TEXTAREA'].includes(event.target.tagName)) return;
                this.deleteSelectedMedia();
            },

            async deleteFolder(path) {
                this.deleteType = 'folder';
                this.deleteTarget = path;
                this.deleteMessage = 'Bạn có chắc chắn muốn xóa thư mục này và tất cả nội dung bên trong? Hành động này không thể hoàn tác.';
                this.showConfirmDelete = true;
            },

            async deleteSelectedMedia() {
                if (this.selectedFiles.length === 0) return;
                this.deleteType = 'multiple';
                this.deleteTarget = null;
                this.deleteMessage = `Bạn đang chuẩn bị xóa ${this.selectedFiles.length} hình ảnh đã chọn. Hành động này không thể hoàn tác!`;
                this.showConfirmDelete = true;
            },

            async executeDelete() {
                this.loading = true;
                const baseUrl = mediaListUrl.replace('/list', '');
                
                try {
                    if (this.deleteType === 'folder') {
                        const response = await fetch(`${baseUrl}/folder`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ path: this.deleteTarget })
                        });
                        if (response.ok) {
                            this.showNotification('Xóa thư mục thành công!');
                            this.loadMedia(this.currentPath);
                        } else {
                            this.showNotification('Lỗi khi xóa thư mục', 'error');
                        }
                    } else if (this.deleteType === 'multiple') {
                        const deletePromises = this.selectedFiles.map(file => 
                            fetch(`${baseUrl}/${file.id}`, {
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
                            this.showNotification(`Xóa thành công ${this.selectedFiles.length} ảnh!`);
                        }
                        
                        this.selectedFiles = [];
                        this.lastSelectedPath = null;
                        this.loadMedia(this.currentPath);
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    this.showNotification('Đã xảy ra lỗi khi xóa', 'error');
                }
                this.loading = false;
                this.showConfirmDelete = false;
            }
        }));
    });
</script>
@endonce
@endpush

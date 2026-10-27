@once
@php
    $uploadUrl = url('/admin/media/upload');
    $listUrl = url('/admin/media/list');
    $createFolderUrl = url('/admin/media/folder');
    $deleteFolderUrl = url('/admin/media/folder');
    $moveUrl = url('/admin/media/move');
    $bulkDeleteUrl = url('/admin/media/bulk-delete');
@endphp
{{--
    Media Picker Modal — dùng chung toàn admin
    Cách dùng:
      @include('components.admin.media-picker')
    Mở modal:
      openMediaPicker(targetId, callback, multiple)
--}}
<div id="media-picker-modal" 
     x-data="mediaPickerData()" 
     x-show="show" 
     x-cloak 
     style="display: none;"
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6"
     @keydown.window.escape="show = false">
    
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" 
         x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         @click="show = false"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white rounded-[40px] shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden border border-white"
         x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-sm font-black text-slate-900 tracking-tighter uppercase">Thư viện phương tiện</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Chọn ảnh hoặc tệp tin để chèn vào nội dung</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- View Modes --}}
                <div class="hidden lg:flex bg-white border border-slate-100 p-1.5 rounded-2xl shadow-sm mr-1">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-blue-600'" class="w-8 h-8 rounded-xl flex items-center justify-center transition-all" title="Lưới lớn"><i class="fa-solid fa-th-large text-xs"></i></button>
                    <button @click="viewMode = 'compact'" :class="viewMode === 'compact' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-blue-600'" class="w-8 h-8 rounded-xl flex items-center justify-center transition-all mx-1" title="Lưới nhỏ"><i class="fa-solid fa-th text-xs"></i></button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-blue-600'" class="w-8 h-8 rounded-xl flex items-center justify-center transition-all" title="Danh sách"><i class="fa-solid fa-list text-xs"></i></button>
                </div>

                {{-- Nút Chế độ Chọn Nhiều --}}
                <button type="button" @click="toggleMultipleMode()" 
                        :class="isMultiple ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 ring-2 ring-blue-400/30' : 'bg-white text-slate-600 hover:text-blue-600 border border-slate-200'"
                        class="px-3.5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-wider flex items-center gap-2 transition-all shadow-sm">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>Chọn nhiều</span>
                    <span x-show="selectedItems.length > 0" 
                          class="px-1.5 py-0.5 rounded-full text-[9px] font-black"
                          :class="isMultiple ? 'bg-white text-blue-600' : 'bg-blue-600 text-white'"
                          x-text="selectedItems.length"></span>
                </button>

                {{-- Folder Creation --}}
                <button @click="openNewFolder()" class="w-10 h-10 rounded-2xl bg-white text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm flex items-center justify-center border border-slate-100" title="Tạo thư mục mới">
                    <i class="fa-solid fa-folder-plus text-xs"></i>
                </button>

                {{-- Search --}}
                <div class="relative hidden md:block">
                    <input type="text" x-model="search" @input.debounce.300ms="fetchMedia()" placeholder="Tìm kiếm tệp..." 
                           class="w-48 pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                </div>

                {{-- Upload btn --}}
                <button @click="$refs.mediaFileInput.click()" 
                        class="px-6 py-2.5 rounded-2xl bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-900/10 flex items-center gap-2">
                    <i x-show="uploading" class="fa-solid fa-spinner fa-spin"></i>
                    <i x-show="!uploading" class="fa-solid fa-cloud-arrow-up"></i>
                    <span x-text="uploading ? 'Đang tải...' : 'Tải lên'"></span>
                </button>
                <input type="file" x-ref="mediaFileInput" @change="uploadFile($event)" class="hidden" accept="image/*" multiple>

                <button @click="show = false" class="w-10 h-10 rounded-2xl bg-white text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm flex items-center justify-center border border-slate-100" title="Đóng">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
            {{-- Main Content: Grid --}}
            <div class="flex-1 flex flex-col bg-white overflow-hidden relative">
                {{-- Loader --}}
                <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin text-[0]">.</div>
                </div>

                <div class="flex-1 overflow-y-auto p-8 scroll-smooth custom-scroll">
                    {{-- Navigation Bar for Picker --}}
                    <div class="flex items-center gap-3 mb-4">
                        <template x-if="currentFolderId">
                            <button @click="goUp()" class="px-5 py-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-[10px] font-black uppercase text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center gap-2">
                                <i class="fa-solid fa-arrow-left"></i> Quay lại
                            </button>
                        </template>
                        <template x-for="r in roots" :key="r.id">
                            <button @click="setFolder(r.id)" 
                                    :class="currentFolderId === r.id ? 'bg-blue-600 text-white shadow-lg' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                                    class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                                    x-text="r.name"></button>
                        </template>
                    </div>

                    {{-- Bulk Actions Bar (Hiện khi chọn nhiều hoặc có tệp được chọn) --}}
                    <div x-show="isMultiple || selectedItems.length > 0" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mb-6 px-5 py-3.5 bg-gradient-to-r from-blue-50/90 to-indigo-50/90 border border-blue-100 rounded-[24px] flex flex-wrap items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-slate-800 tracking-tight flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-blue-600"></i>
                                Đã chọn: <span class="font-black text-blue-600 text-sm" x-text="selectedItems.length"></span> tệp
                            </span>
                            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                            <button type="button" @click="selectAll()" 
                                    class="px-3.5 py-1.5 rounded-xl bg-white border border-blue-200 text-blue-700 text-[10px] font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-check-double text-xs"></i> Chọn tất cả (<span x-text="items.length"></span>)
                            </button>
                            <button type="button" @click="clearSelection()" x-show="selectedItems.length > 0"
                                    class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-wider hover:bg-slate-100 transition-all shadow-sm">
                                Bỏ chọn
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="bulkDelete()" 
                                    :disabled="selectedItems.length === 0 || deleting"
                                    :class="selectedItems.length > 0 ? 'bg-rose-500 hover:bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider flex items-center gap-2 transition-all">
                                <i x-show="deleting" class="fa-solid fa-spinner fa-spin"></i>
                                <i x-show="!deleting" class="fa-solid fa-trash-can"></i>
                                <span>Xóa nhiều (<span x-text="selectedItems.length"></span>)</span>
                            </button>
                            <button type="button" @click="confirmSelection()" x-show="selectedItems.length > 0"
                                    class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 shadow-md shadow-blue-500/20 transition-all">
                                <i class="fa-solid fa-check"></i>
                                <span>Chèn <span x-text="selectedItems.length"></span> ảnh đã chọn</span>
                            </button>
                        </div>
                    </div>

                    {{-- Grid View --}}
                    <div x-show="viewMode === 'grid' || viewMode === 'compact'" 
                         :class="viewMode === 'compact' ? 'grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4' : 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6'"
                         class="grid">
                        
                        {{-- Folder Cards --}}
                        <template x-for="f in subFolders" :key="'f-'+f.id">
                            <div @dblclick="setFolder(f.id)" 
                                 class="group relative aspect-square bg-slate-50/50 rounded-[32px] border-2 border-transparent hover:border-blue-300 cursor-pointer overflow-hidden transition-all flex flex-col items-center justify-center shadow-sm">
                                
                                <template x-if="editingId === 'folder-'+f.id">
                                    <div class="px-4 w-full">
                                        <input type="text" :id="'rename-folder-'+f.id" :value="f.name" 
                                               @keyup.enter="saveRename(f.id, true)" @click.stop
                                               class="w-full text-[10px] font-bold text-center border-b border-blue-500 outline-none bg-transparent py-1">
                                    </div>
                                </template>
                                <template x-if="editingId !== 'folder-'+f.id">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-folder-open text-4xl mb-3" :style="`color: ${f.color || '#3b82f6'}`"></i>
                                        <span class="text-[10px] font-black uppercase text-slate-500 text-center px-4 tracking-tighter" x-text="f.name"></span>
                                    </div>
                                </template>

                                {{-- Action overlay --}}
                                <div class="absolute inset-x-0 bottom-0 py-2 bg-white/90 backdrop-blur-sm border-t border-slate-100 flex justify-center gap-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <button @click.stop="editingId = 'folder-'+f.id" class="text-slate-400 hover:text-blue-500 transition-colors" title="Đổi tên"><i class="fa-solid fa-pencil text-[10px]"></i></button>
                                    <button @click.stop="deleteFolder(f.id)" class="text-slate-400 hover:text-rose-500 transition-colors" title="Xóa thư mục"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                </div>
                            </div>
                        </template>

                        {{-- File Cards --}}
                        <template x-for="item in items" :key="item.id">
                            <div @click="handleItemClick(item)" 
                                 class="group relative aspect-square bg-slate-50 rounded-[32px] border-2 cursor-pointer overflow-hidden transition-all shadow-sm hover:shadow-xl hover:-translate-y-1"
                                 :class="isItemSelected(item) ? 'border-blue-500 ring-4 ring-blue-50 shadow-blue-500/10' : 'border-transparent hover:border-blue-400'">
                                
                                {{-- Preview --}}
                                <template x-if="isImage(item)">
                                    <img :src="item.url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </template>
                                <template x-if="!isImage(item)">
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100">
                                        <i class="fa-solid fa-file-invoice text-3xl text-slate-200 mb-2"></i>
                                        <span class="text-[9px] font-black text-slate-400 uppercase px-3 text-center" x-text="item.mime.split('/')[1]"></span>
                                    </div>
                                </template>

                                {{-- Name during rename --}}
                                <div x-show="editingId === 'file-'+item.id" class="absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center p-4 z-20" @click.stop>
                                    <input type="text" :id="'rename-file-'+item.id" :value="item.name" 
                                           @keyup.enter="saveRename(item.id, false)"
                                           class="w-full text-center text-[10px] font-black uppercase text-slate-900 border-b border-blue-500 outline-none bg-transparent">
                                </div>

                                {{-- Overlay info --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                    <p class="text-[9px] text-white font-black truncate uppercase tracking-tighter leading-none mb-1" x-text="item.name"></p>
                                    
                                    {{-- Mini Actions --}}
                                    <div class="flex gap-3 mt-2 mb-1">
                                        <button @click.stop="editingId = 'file-'+item.id" class="text-white hover:text-blue-400 transition-colors" title="Đổi tên"><i class="fa-solid fa-pencil text-[10px]"></i></button>
                                        <button @click.stop="deleteItem(item.id)" class="text-white hover:text-rose-400 transition-colors" title="Xóa tệp"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                        <button @click.stop="window.open(item.url, '_blank')" class="text-white hover:text-emerald-400 transition-colors ml-auto" title="Xem ảnh"><i class="fa-solid fa-eye text-[10px]"></i></button>
                                    </div>
                                </div>

                                {{-- Selection Checkbox / Badge --}}
                                <div @click.stop="toggleItem(item)" 
                                     class="absolute top-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-xs transition-all z-10 shadow-sm cursor-pointer"
                                     :class="isItemSelected(item) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30 ring-2 ring-white' : 'bg-white/90 border border-slate-300 text-transparent hover:border-blue-500 group-hover:flex ' + (isMultiple ? 'flex' : 'hidden')">
                                    <i class="fa-solid fa-check" :class="isItemSelected(item) ? 'text-white text-[11px]' : 'text-slate-300 text-[10px]'"></i>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- List View --}}
                    <div x-show="viewMode === 'list'" class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-widest">
                                <tr>
                                    <th class="px-4 py-4 w-10 text-center">
                                        <input type="checkbox" :checked="isAllSelected()" @change="toggleSelectAll($event.target.checked)" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-4">Tên tệp/thư mục</th>
                                    <th class="px-6 py-4">Dung lượng</th>
                                    <th class="px-6 py-4">Định dạng</th>
                                    <th class="px-6 py-4 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="f in subFolders" :key="'fl-'+f.id">
                                    <tr @dblclick="setFolder(f.id)" class="hover:bg-slate-50 border-b border-slate-100 transition-colors group">
                                        <td class="px-4 py-3 text-center">
                                            <i class="fa-solid fa-folder text-amber-400 text-sm"></i>
                                        </td>
                                        <td class="px-6 py-3 font-bold text-slate-700 flex items-center gap-3">
                                            <span x-text="f.name"></span>
                                        </td>
                                        <td class="px-6 py-3 text-slate-400 uppercase">--</td>
                                        <td class="px-6 py-3 text-slate-400 uppercase tracking-widest text-[9px]">Folder</td>
                                        <td class="px-6 py-3 text-right">
                                            <button @click="setFolder(f.id)" class="text-[9px] font-black uppercase text-blue-600 px-3 py-1.5 rounded-lg bg-blue-50">Mở</button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="item in items" :key="'il-'+item.id">
                                    <tr @click="handleItemClick(item)" 
                                        :class="isItemSelected(item) ? 'bg-blue-50/50' : ''"
                                        class="hover:bg-slate-50 border-b border-slate-100 transition-colors group cursor-pointer">
                                        <td class="px-4 py-4 text-center" @click.stop>
                                            <input type="checkbox" :checked="isItemSelected(item)" @change="toggleItem(item)" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4 flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-50 shrink-0">
                                                <template x-if="isImage(item)">
                                                    <img :src="item.url" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!isImage(item)">
                                                    <div class="w-full h-full flex items-center justify-center"><i class="fa-solid fa-file text-slate-300"></i></div>
                                                </template>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-black text-slate-800 uppercase tracking-tight truncate max-w-[200px]" x-text="item.name"></span>
                                                <span class="text-[9px] text-slate-400" x-text="item.url.split('/').pop()"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 font-bold" x-text="formatSize(item.size)"></td>
                                        <td class="px-6 py-4">
                                            <span class="text-[9px] font-black px-2 py-1 rounded bg-slate-100 text-slate-400 uppercase tracking-widest" x-text="item.mime.split('/')[1]"></span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click.stop="editingId = 'file-'+item.id" class="w-8 h-8 rounded-lg bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-blue-500" title="Đổi tên"><i class="fa-solid fa-pencil text-[10px]"></i></button>
                                                <button @click.stop="deleteItem(item.id)" class="w-8 h-8 rounded-lg bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-rose-500" title="Xóa"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                                <button @click.stop="selectItem(item); confirmSelection()" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest ml-1">Chọn</button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!loading && items.length === 0 && subFolders.length === 0" class="flex flex-col items-center justify-center h-full py-28 text-slate-200">
                        <i class="fa-solid fa-cloud-bolt-moon text-7xl mb-6 opacity-20"></i>
                        <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-300">Không tìm thấy mục nào</p>
                    </div>
                </div>

                {{-- Footer: Actions --}}
                <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                    <div class="text-[10px] text-slate-400 flex items-center gap-3">
                        <template x-if="selectedItems.length > 0">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-700 uppercase tracking-widest italic">
                                    Đã chọn: <span class="text-blue-600 font-extrabold" x-text="selectedItems.length"></span> tệp
                                </span>
                                <button type="button" @click="clearSelection()" class="text-slate-400 hover:text-rose-500 underline lowercase font-normal">(bỏ chọn)</button>
                            </div>
                        </template>
                        <template x-if="selectedItems.length === 0">
                            <span class="font-bold uppercase tracking-widest">Chưa chọn tệp nào</span>
                        </template>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="bulkDelete()" 
                                x-show="selectedItems.length > 0"
                                :disabled="deleting"
                                class="px-5 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5 shadow-sm">
                            <i x-show="deleting" class="fa-solid fa-spinner fa-spin"></i>
                            <i x-show="!deleting" class="fa-solid fa-trash-can"></i>
                            <span>Xóa nhiều (<span x-text="selectedItems.length"></span>)</span>
                        </button>
                        <button @click="show = false" class="px-6 py-2.5 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-slate-200 rounded-xl transition-colors italic">Đóng</button>
                        <button @click="confirmSelection()" 
                                :disabled="selectedItems.length === 0 && !selectedItem"
                                :class="(selectedItems.length > 0 || selectedItem) ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="px-10 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[.2em] transition-all italic">
                            <span x-text="selectedItems.length > 1 ? ('Sử dụng ' + selectedItems.length + ' tệp đã chọn') : 'Sử dụng tệp này'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function mediaPickerData() {
        return {
            show: false,
            loading: false,
            items: [],
            subFolders: [],
            roots: [],
            currentFolderId: null,
            search: '',
            selectedItem: null,
            selectedItems: [],
            isMultiple: false,
            deleting: false,
            targetId: null,
            callback: null,
            uploading: false,
            viewMode: 'grid',
            editingId: null,

            init() {
                window.openMediaPicker = (id, cb, multiple = false) => {
                    this.targetId = id;
                    this.callback = cb;
                    this.isMultiple = !!multiple;
                    this.selectedItem = null;
                    this.selectedItems = [];
                    this.show = true;
                    this.fetchMedia();
                };
            },

            toggleMultipleMode() {
                this.isMultiple = !this.isMultiple;
                if (this.isMultiple && this.selectedItem && !this.isItemSelected(this.selectedItem)) {
                    this.selectedItems.push(this.selectedItem);
                }
            },

            isItemSelected(item) {
                if (!item) return false;
                return this.selectedItems.some(i => (i.id && i.id === item.id) || (i.path && i.path === item.path) || (i.url && i.url === item.url));
            },

            toggleItem(item) {
                if (!item) return;
                const idx = this.selectedItems.findIndex(i => (i.id && i.id === item.id) || (i.path && i.path === item.path) || (i.url && i.url === item.url));
                if (idx >= 0) {
                    this.selectedItems.splice(idx, 1);
                } else {
                    this.selectedItems.push(item);
                }
                this.selectedItem = this.selectedItems.length > 0 ? this.selectedItems[this.selectedItems.length - 1] : null;
            },

            handleItemClick(item) {
                if (this.isMultiple) {
                    this.toggleItem(item);
                } else {
                    this.selectItem(item);
                }
            },

            selectItem(item) {
                this.selectedItem = item;
                this.selectedItems = item ? [item] : [];
                this.editingId = null;
            },

            selectAll() {
                this.selectedItems = [...this.items];
                this.selectedItem = this.selectedItems.length > 0 ? this.selectedItems[0] : null;
            },

            clearSelection() {
                this.selectedItems = [];
                this.selectedItem = null;
            },

            isAllSelected() {
                return this.items.length > 0 && this.selectedItems.length === this.items.length;
            },

            toggleSelectAll(checked) {
                if (checked) {
                    this.selectAll();
                } else {
                    this.clearSelection();
                }
            },

            async uploadFile(e) {
                const files = e.target.files;
                if (!files || !files.length) return;

                const formData = new FormData();
                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
                formData.append('path', this.currentFolderId || '');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                if (csrf) {
                    formData.append('_token', csrf);
                }

                this.uploading = true;
                try {
                    const response = await fetch('{{ $uploadUrl }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    const text = await response.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (parseErr) {
                        throw new Error('Máy chủ phản hồi không hợp lệ (HTTP ' + response.status + ')');
                    }

                    if (!response.ok || (data && data.success === false)) {
                        throw new Error(data.message || ('Lỗi máy chủ: HTTP ' + response.status));
                    }
                    
                    e.target.value = ''; // clear input
                    await this.fetchMedia();
                    
                    // Auto select the newly uploaded files and prioritize first position
                    const uploadedList = data.uploaded || data.items || [];
                    if (uploadedList && uploadedList.length > 0) {
                        const newItems = this.items.filter(i => uploadedList.some(u => u.id === i.id || u.url === i.url || u.path === i.path));
                        const otherItems = this.items.filter(i => !uploadedList.some(u => u.id === i.id || u.url === i.url || u.path === i.path));
                        if (newItems.length > 0) {
                            this.items = [...newItems, ...otherItems];
                            if (this.isMultiple) {
                                newItems.forEach(item => {
                                    if (!this.isItemSelected(item)) this.selectedItems.push(item);
                                });
                                this.selectedItem = newItems[0];
                            } else {
                                this.selectItem(newItems[0]);
                            }
                        }
                    }
                } catch (err) {
                    alert('Lỗi tải lên: ' + err.message);
                } finally {
                    this.uploading = false;
                }
            },

            async fetchMedia() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({ 
                        path: this.currentFolderId || '',
                        search: this.search || ''
                    });
                    const response = await fetch(`{{ $listUrl }}?${params.toString()}`, {
                        headers: { 
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    let rawFiles = data.files || data.items || [];
                    if (this.search) {
                        const q = this.search.toLowerCase();
                        rawFiles = rawFiles.filter(f => (f.name || '').toLowerCase().includes(q));
                    }
                    this.items = rawFiles.map(f => ({
                        ...f,
                        mime: f.mime || (['jpg','jpeg','png','gif','webp','svg'].includes((f.name || '').split('.').pop().toLowerCase()) ? 'image/' + (f.name || '').split('.').pop().toLowerCase() : 'application/octet-stream')
                    }));
                    this.subFolders = (data.folders || []).map(f => ({
                        id: f.path || f.name,
                        name: f.name,
                        path: f.path
                    }));
                    this.roots = [];

                    // Refresh selected items list to purge any deleted items
                    this.selectedItems = this.selectedItems.filter(sel => this.items.some(it => it.id === sel.id || it.path === sel.path));
                    if (this.selectedItem && !this.items.some(it => it.id === this.selectedItem.id || it.path === this.selectedItem.path)) {
                        this.selectedItem = this.selectedItems.length > 0 ? this.selectedItems[0] : null;
                    }
                } catch (e) {
                    console.error('Picker error:', e);
                } finally {
                    this.loading = false;
                }
            },

            setFolder(id) {
                this.currentFolderId = id;
                this.fetchMedia();
            },

            goUp() {
                if (!this.currentFolderId) return;
                const parts = this.currentFolderId.replace(/\\/g, '/').split('/').filter(p => p);
                parts.pop();
                this.currentFolderId = parts.join('/');
                this.fetchMedia();
            },

            async openNewFolder() {
                const name = prompt('Nhập tên thư mục mới:');
                if (!name) return;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                try {
                    const response = await fetch("{{ $createFolderUrl }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            name: name,
                            path: this.currentFolderId || ''
                        })
                    });
                    if (response.ok) this.fetchMedia();
                } catch(e) {}
            },

            async saveRename(id, isFolder) {
                const inputId = isFolder ? `rename-folder-${id}` : `rename-file-${id}`;
                const input = document.getElementById(inputId);
                if (!input) return (this.editingId = null);
                const newName = input.value.trim();
                if (!newName) return (this.editingId = null);

                const currentBase = this.currentFolderId ? this.currentFolderId.replace(/\\/g, '/').replace(/\/$/, '') + '/' : '';
                const fromPath = id;
                const toPath = currentBase + newName;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                try {
                    const res = await fetch("{{ $moveUrl }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            from: fromPath,
                            to: toPath,
                            type: isFolder ? 'folder' : 'file'
                        })
                    });
                    if (res.ok) this.fetchMedia();
                } catch(e) {}
                this.editingId = null;
            },

            async deleteFolder(id) {
                if (!confirm('Xóa thư mục này?')) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                try {
                    const res = await fetch("{{ $deleteFolderUrl }}", {
                        method: 'DELETE',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ path: id })
                    });
                    if (res.ok) this.fetchMedia();
                } catch(e) {}
            },

            async deleteItem(id) {
                if (!confirm('Xóa tệp này?')) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                const deleteUrl = "{{ $listUrl }}".replace(/\/list$/, '');
                try {
                    const res = await fetch(deleteUrl + '/' + encodeURIComponent(id), {
                        method: 'DELETE',
                        headers: { 
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    if (res.ok) {
                        this.selectedItems = this.selectedItems.filter(i => i.id !== id && i.path !== id);
                        if (this.selectedItem?.id === id) this.selectedItem = null;
                        this.fetchMedia();
                    }
                } catch(e) {}
            },

            async bulkDelete() {
                const count = this.selectedItems.length;
                if (!count) {
                    alert('Vui lòng chọn ít nhất một tệp để xóa.');
                    return;
                }
                if (!confirm(`Bạn có chắc chắn muốn xóa ${count} tệp đã chọn? Hành động này không thể hoàn tác.`)) {
                    return;
                }

                this.deleting = true;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                const ids = this.selectedItems.map(i => i.id || i.path);

                try {
                    const res = await fetch('{{ $bulkDeleteUrl }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ ids: ids })
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.clearSelection();
                        await this.fetchMedia();
                    } else {
                        alert(data.message || 'Lỗi khi xóa tệp đã chọn.');
                    }
                } catch (err) {
                    alert('Lỗi kết nối máy chủ: ' + err.message);
                } finally {
                    this.deleting = false;
                }
            },

            confirmSelection() {
                if (this.selectedItems.length === 0 && this.selectedItem) {
                    this.selectedItems = [this.selectedItem];
                }
                if (this.selectedItems.length === 0) return;

                const urls = this.selectedItems.map(i => i.url);
                const firstUrl = urls[0];

                if (this.targetId) {
                    const input = document.getElementById(this.targetId);
                    if (input) {
                        input.value = this.isMultiple || urls.length > 1 ? urls.join(',') : firstUrl;
                        input.dispatchEvent(new Event('input'));
                        input.dispatchEvent(new Event('change'));

                        // Cập nhật preview nếu có (tự động dự đoán element)
                        const previewId = this.targetId + '_preview';
                        if (window.updateImgPreview) {
                            window.updateImgPreview(previewId, firstUrl);
                        }
                        const previewImg = document.getElementById(previewId) || document.getElementById(this.targetId + '-preview');
                        if (previewImg && previewImg.tagName === 'IMG') {
                            previewImg.src = firstUrl;
                        }
                    }
                }

                if (this.callback && typeof this.callback === 'function') {
                    if (this.isMultiple || urls.length > 1) {
                        this.callback(urls, this.selectedItems);
                    } else {
                        this.callback(firstUrl, this.selectedItems[0]);
                    }
                }

                this.show = false;
            },

            isImage(item) {
                return (item.mime || '').startsWith('image/');
            },

            formatSize(bytes) {
                if (!bytes) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }
        };
    }
</script>

<style>
    [x-cloak] { display: none; }
</style>
@endonce

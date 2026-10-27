{{-- Professional File Manager Modal Component --}}
{{-- Usage: @include('cms.components.file-manager', ['field' => 'featured_image', 'label' => 'Ảnh sản phẩm']) --}}

@props(['single' => true, 'label' => 'Files'])

<div x-data="fileManager({
    initial: @json(isset($product) ? $product->gallery()->map(fn($m)=>['id'=>$m->id,'url'=>$m->getUrl('thumb') ?? $m->getUrl()])->toArray() : []),
    single: {{ $single ? 'true' : 'false' }}
})" class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>

    <div class="grid grid-cols-3 gap-2">
        <template x-for="(file,index) in files" :key="file.id ?? index">
            <div class="relative bg-white rounded border p-1">
                <img :src="file.url" class="w-full h-24 object-cover rounded" alt="thumb">
                <button type="button" x-show="!file.removing" @click="markRemove(index)"
                    class="absolute top-1 right-1 bg-white rounded-full p-1 text-red-600 shadow">
                    ×
                </button>
                <div x-show="file.removing" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white">Xóa</div>
                <input type="hidden" :name="'existing_media_ids[]'" :value="file.id" x-show="!file.removing">
            </div>
        </template>
    </div>

    <div class="pt-3">
        <input type="file" x-ref="input" :name="single ? '{{ $field }}' : 'images[]'" :multiple="!single"
               class="hidden" @change="handleFiles($event)">
        <button type="button" @click="$refs.input.click()"
                class="px-4 py-2 border rounded text-sm bg-gray-50 hover:bg-gray-100"
                x-text="'Tải lên ' + (single ? 'ảnh đại diện' : 'hình ảnh')">
        </button>
    </div>

    <!-- Alpine component script -->
    <script>
        function fileManager({ initial = [], single = false } = {}) {
            return {
                files: initial.map(f => ({ ...f, removing: false })),
                single,
                handleFiles(e) {
                    const inputFiles = Array.from(e.target.files);
                    inputFiles.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.files.push({ id: null, url: ev.target.result, file, removing: false, is_new: true });
                        };
                        reader.readAsDataURL(file);
                    });
                },
                markRemove(index) {
                    // toggles removal flag
                    this.files[index].removing = !this.files[index].removing;
                    // make sure removed existing media will be submitted for deletion
                    if (this.files[index].id && this.files[index].removing) {
                        const toRemove = document.querySelector('input[name="remove_media_ids[]"]') || null;
                        // append hidden fields for removed list
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'remove_media_ids[]';
                        input.value = this.files[index].id;
                        document.currentScript?.parentNode?.appendChild(input);
                    }
                }
            }
        }
    </script>
</div>

<style>
[x-cloak] {
    display: none;
}
</style>

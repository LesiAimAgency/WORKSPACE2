@extends('cms.layouts.app')

@section('title', 'Thêm thuộc tính mới')
@section('page-title', 'Thêm Thuộc Tính Sản Phẩm')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12" x-data="attributeCreateForm()">
    <!-- Header Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.attributes.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách thuộc tính
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm">
            <p class="font-bold mb-1">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attributes.store') }}" class="bg-white rounded-xl shadow-xs border border-gray-200 p-6 space-y-6">
        @csrf

        <!-- Name & Slug -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-800 mb-1">Tên thuộc tính <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" x-model="name" @input="updateSlug()" value="{{ old('name') }}" 
                       placeholder="VD: Màu sắc, Kích thước, Cấp điện áp..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-medium"
                       required>
                <p class="text-xs text-gray-400 mt-1">Tên hiển thị khi khách hàng chọn mua hoặc lọc sản phẩm.</p>
            </div>

            <div>
                <label for="slug" class="block text-sm font-semibold text-gray-800 mb-1">Đường dẫn định danh (Slug)</label>
                <input type="text" id="slug" name="slug" x-model="slug" value="{{ old('slug') }}" 
                       placeholder="VD: mau-sac, kich-thuoc, cap-dien-ap"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono text-gray-700 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Type & Order -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="type" class="block text-sm font-semibold text-gray-800 mb-1">Kiểu hiển thị</label>
                <select id="type" name="type" x-model="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white font-medium">
                    <option value="select">Danh sách chọn (Select Dropdown)</option>
                    <option value="button">Nút bấm chọn (Button / Label)</option>
                    <option value="color">Mã màu (Color Picker / Swatch)</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Giao diện mà khách hàng tương tác trên trang chi tiết sản phẩm.</p>
            </div>

            <div>
                <label for="order" class="block text-sm font-semibold text-gray-800 mb-1">Thứ tự ưu tiên</label>
                <input type="number" id="order" name="order" value="{{ old('order', 0) }}" min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-800 mb-1">Mô tả tóm tắt (Tùy chọn)</label>
            <textarea id="description" name="description" rows="2" placeholder="Ghi chú về thuộc tính này..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <!-- Attribute Values Management -->
        <div class="border-t border-gray-200 pt-5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-900 text-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Các giá trị của thuộc tính (Values)
                    </h4>
                    <p class="text-xs text-gray-500">Khai báo sẵn danh sách giá trị để chọn nhanh khi tạo sản phẩm biến thể.</p>
                </div>
                <button type="button" @click="addValue()" 
                        class="px-3 py-1.5 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition flex items-center gap-1">
                    + Thêm một giá trị
                </button>
            </div>

            <!-- Quick Import Comma Separated -->
            <div class="p-3 bg-gray-50 rounded-lg border border-dashed border-gray-300 space-y-2">
                <label class="block text-xs font-medium text-gray-700">Nhập nhanh nhiều giá trị (phân tách bởi dấu phẩy):</label>
                <div class="flex items-center gap-2">
                    <input type="text" x-model="bulkInput" @keydown.enter.prevent="importBulkValues()" 
                           placeholder="VD: Đỏ, Xanh, Vàng, Trắng hoặc 24kV, 35kV, 110kV" 
                           class="flex-1 px-3 py-1.5 text-xs border border-gray-300 rounded bg-white">
                    <button type="button" @click="importBulkValues()" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded hover:bg-black transition">
                        Nạp giá trị
                    </button>
                </div>
            </div>

            <!-- Values List -->
            <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                <template x-for="(val, idx) in values" :key="idx">
                    <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-lg border border-gray-200 shadow-2xs">
                        <span class="w-6 h-6 rounded-full bg-white border text-gray-500 text-xs font-bold flex items-center justify-center" x-text="idx + 1"></span>
                        
                        <div class="flex-1">
                            <input type="text" :name="`values[${idx}][name]`" x-model="val.name" placeholder="Tên giá trị (VD: Đỏ, 24kV)"
                                   class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white font-medium" required>
                        </div>

                        <!-- Color code picker if type is color -->
                        <div x-show="type === 'color'" class="flex items-center gap-1.5">
                            <input type="color" :name="`values[${idx}][color_code]`" x-model="val.color_code" 
                                   class="w-8 h-8 rounded border border-gray-300 cursor-pointer p-0.5 bg-white">
                            <input type="text" x-model="val.color_code" placeholder="#000000"
                                   class="w-20 px-2 py-1 text-xs border border-gray-300 rounded font-mono bg-white">
                        </div>

                        <button type="button" @click="removeValue(idx)" class="text-red-500 hover:text-red-700 p-1" title="Xóa giá trị">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </template>

                <div x-show="values.length === 0" class="py-6 text-center text-xs text-gray-400 bg-white border border-dashed rounded-lg">
                    Chưa có giá trị nào. Bấm "+ Thêm một giá trị" hoặc nhập nhanh ở trên.
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="border-t border-gray-200 pt-5 flex items-center justify-end gap-3">
            <a href="{{ route('admin.attributes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm transition">
                Lưu thuộc tính
            </button>
        </div>
    </form>
</div>

<script>
function attributeCreateForm() {
    return {
        name: '{{ old('name', '') }}',
        slug: '{{ old('slug', '') }}',
        type: '{{ old('type', 'select') }}',
        bulkInput: '',
        values: @json(old('values', [['name' => '', 'color_code' => '#3B82F6']])),

        updateSlug() {
            if (!this.slug || this.slug === '') {
                this.slug = this.slugify(this.name);
            }
        },

        slugify(text) {
            return text.toString().toLowerCase().trim()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'd')
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        },

        addValue() {
            this.values.push({ name: '', color_code: '#3B82F6' });
        },

        removeValue(index) {
            this.values.splice(index, 1);
        },

        importBulkValues() {
            if (!this.bulkInput.trim()) return;
            const items = this.bulkInput.split(',').map(s => s.trim()).filter(Boolean);
            items.forEach(item => {
                if (!this.values.some(v => v.name.toLowerCase() === item.toLowerCase())) {
                    this.values.push({ name: item, color_code: '#3B82F6' });
                }
            });
            this.bulkInput = '';
        }
    };
}
</script>
@endsection

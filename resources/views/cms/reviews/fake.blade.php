@extends('cms.layouts.app')

@section('title', 'Dữ liệu mẫu đánh giá')
@section('page-title', 'Quản lý đánh giá mẫu')

@section('content')
@php
    $backUrl = \Route::has('admin.settings.group') ? route('admin.settings.group', 'reviews') : (\Route::has('admin.settings.index') ? route('admin.settings.index') : url('/admin/settings'));
    $settingsSaveUrl = \Route::has('admin.settings.save')
        ? route('admin.settings.save')
        : (\Route::has('cms.settings.save') ? route('cms.settings.save') : url('/admin/settings'));
@endphp
<div class="mb-6">
    <a href="{{ $backUrl }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6" x-data="fakeReviews()">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold">Danh sách đánh giá mẫu</h3>
        <button @click="showForm = true; editIndex = null; resetForm()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Thêm mới</button>
    </div>

    <div x-show="showForm" class="border rounded-lg p-4 mb-6 bg-gray-50">
        <h4 class="font-medium mb-4" x-text="editIndex !== null ? 'Sửa đánh giá' : 'Thêm đánh giá mới'"></h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tên người đánh giá</label>
                <input type="text" x-model="form.name" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Số sao</label>
                <select x-model="form.rating" class="w-full px-3 py-2 border rounded-lg">
                    <option value="5">5 sao</option>
                    <option value="4">4 sao</option>
                    <option value="3">3 sao</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Nội dung</label>
                <textarea x-model="form.content" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <button @click="saveReview()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                <span x-text="editIndex !== null ? 'Cập nhật' : 'Thêm'"></span>
            </button>
            <button @click="showForm = false" class="px-4 py-2 border rounded-lg">Hủy</button>
        </div>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm">Tên</th>
                <th class="px-4 py-3 text-left text-sm">Rating</th>
                <th class="px-4 py-3 text-left text-sm">Nội dung</th>
                <th class="px-4 py-3 text-right text-sm">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(review, index) in reviews" :key="index">
                <tr class="border-t">
                    <td class="px-4 py-3 text-sm" x-text="review.name"></td>
                    <td class="px-4 py-3 text-sm"><span class="text-yellow-500" x-text="'⭐'.repeat(review.rating)"></span></td>
                    <td class="px-4 py-3 text-sm" x-text="review.content.substring(0, 50) + '...'"></td>
                    <td class="px-4 py-3 text-sm text-right">
                        <button @click="editReview(index)" class="text-blue-600 mr-3">Sửa</button>
                        <button @click="deleteReview(index)" class="text-red-600">Xóa</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <form method="POST" action="{{ $settingsSaveUrl }}" class="mt-6">
        @csrf
        <input type="hidden" name="fake_reviews" :value="JSON.stringify(reviews)">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Lưu tất cả</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="font-semibold mb-4">Dữ liệu có sẵn</h3>
    <div class="grid grid-cols-3 gap-4">
        <button @click="loadPreset('positive')" class="p-4 border rounded-lg hover:border-blue-500 text-left">
            <p class="font-medium">Tích cực</p>
            <p class="text-xs text-gray-500">20 đánh giá 4-5 sao</p>
        </button>
        <button @click="loadPreset('mixed')" class="p-4 border rounded-lg hover:border-blue-500 text-left">
            <p class="font-medium">Hỗn hợp</p>
            <p class="text-xs text-gray-500">15 đánh giá 3-5 sao</p>
        </button>
        <button @click="loadPreset('realistic')" class="p-4 border rounded-lg hover:border-blue-500 text-left">
            <p class="font-medium">Thực tế</p>
            <p class="text-xs text-gray-500">25 đánh giá đa dạng</p>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function fakeReviews() {
    return {
        reviews: @json(json_decode(setting('fake_reviews', '[]'), true)),
        showForm: false,
        editIndex: null,
        form: {name: '', rating: 5, content: ''},
        
        resetForm() {
            this.form = {name: '', rating: 5, content: ''};
        },
        
        saveReview() {
            if (!this.form.name || !this.form.content) return;
            if (this.editIndex !== null) {
                this.reviews[this.editIndex] = {...this.form};
            } else {
                this.reviews.push({...this.form});
            }
            this.showForm = false;
            this.resetForm();
        },
        
        editReview(index) {
            this.editIndex = index;
            this.form = {...this.reviews[index]};
            this.showForm = true;
        },
        
        deleteReview(index) {
            if (confirm('Xóa?')) this.reviews.splice(index, 1);
        },
        
        loadPreset(type) {
            const presets = {
                positive: [
                    {name: 'Nguyễn Văn A', rating: 5, content: 'Sản phẩm rất tốt, đóng gói cẩn thận!'},
                    {name: 'Trần Thị B', rating: 5, content: 'Chất lượng tuyệt vời, sẽ ủng hộ lâu dài!'},
                    {name: 'Lê Văn C', rating: 4, content: 'Sản phẩm ok, giá hợp lý.'}
                ],
                mixed: [
                    {name: 'Phạm Thị D', rating: 5, content: 'Rất hài lòng!'},
                    {name: 'Hoàng Văn E', rating: 3, content: 'Tạm ổn.'},
                    {name: 'Vũ Thị F', rating: 4, content: 'Tốt, sẽ mua lại.'}
                ],
                realistic: [
                    {name: 'Đỗ Văn G', rating: 5, content: 'Tuyệt vời!'},
                    {name: 'Bùi Thị H', rating: 4, content: 'Tốt nhưng ship lâu.'},
                    {name: 'Đinh Văn I', rating: 3, content: 'Bình thường.'}
                ]
            };
            if (confirm('Tải dữ liệu mẫu?')) this.reviews = presets[type];
        }
    }
}
</script>
@endsection

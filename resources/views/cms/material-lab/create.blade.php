@extends('cms.layouts.app')

@section('title', 'Thêm mới vật liệu - Material Lab')
@section('page-title', 'Thêm mới vật liệu')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Thêm mẫu vật liệu mới</h2>
            <p class="text-xs text-gray-500 mt-0.5">Nhập thông tin tiêu chuẩn kỹ thuật &amp; thông số ứng dụng cho wizard Material Lab.</p>
        </div>
        <a href="{{ route('admin.material-lab.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg space-y-1">
            <div class="font-bold">Có lỗi xảy ra, vui lòng kiểm tra lại:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.material-lab.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- General Info -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                1. Thông tin chung
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mã định danh (Code) <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Ví dụ: lvt, terrazzo, acoustic_panel..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Dùng để định danh kỹ thuật trong wizard gợi ý (viết thường, không dấu, nối _ hoặc -).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tên vật liệu <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Sàn nhựa vinyl hèm khóa (LVT)" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Thương hiệu / Nhà cung cấp <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" value="{{ old('brand') }}" required placeholder="Ví dụ: LG Hausys / An Cường / Vĩnh Tường" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phân loại danh mục <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category') }}" required list="cat_list" placeholder="Chọn hoặc nhập mới (Sàn, Tường, Trần...)" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <datalist id="cat_list">
                        <option value="Sàn">
                        <option value="Tường">
                        <option value="Trần">
                        <option value="Đồ rời">
                        <option value="Kỹ thuật">
                    </datalist>
                </div>
            </div>
        </div>

        <!-- Technical & Commercial specs -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                2. Thông số kỹ thuật &amp; Chi phí
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Xuất xứ <span class="text-rose-500">*</span></label>
                    <select name="origin" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        <option value="Vietnam" {{ old('origin') === 'Vietnam' ? 'selected' : '' }}>Sản xuất tại Việt Nam</option>
                        <option value="Import" {{ old('origin') === 'Import' ? 'selected' : '' }}>Nhập khẩu chính hãng</option>
                        <option value="Both" {{ old('origin') === 'Both' ? 'selected' : '' }}>Liên doanh / Cả hai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nhãn xuất xứ hiển thị</label>
                    <input type="text" name="origin_label" value="{{ old('origin_label', 'Việt Nam') }}" placeholder="Ví dụ: Việt Nam / Hàn Quốc / Ý" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Thời gian cung ứng <span class="text-rose-500">*</span></label>
                    <input type="text" name="availability" value="{{ old('availability', 'Có sẵn kho') }}" required placeholder="Ví dụ: Có sẵn kho / Đặt 2-3 tuần" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Độ bền bảo hành (năm) <span class="text-rose-500">*</span></label>
                    <input type="number" name="durability_years" value="{{ old('durability_years', 10) }}" min="1" max="50" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mức ngân sách <span class="text-rose-500">*</span></label>
                    <select name="budget_level" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        <option value="1" {{ old('budget_level') == '1' ? 'selected' : '' }}>1: Tiết kiệm</option>
                        <option value="2" {{ old('budget_level', 2) == '2' ? 'selected' : '' }}>2: Cân bằng / Phổ thông</option>
                        <option value="3" {{ old('budget_level') == '3' ? 'selected' : '' }}>3: Cao cấp</option>
                        <option value="4" {{ old('budget_level') == '4' ? 'selected' : '' }}>4: Siêu cao cấp / Luxury</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Đơn giá tham khảo (Cost label)</label>
                    <input type="text" name="cost_label" value="{{ old('cost_label') }}" placeholder="Ví dụ: 350.000 - 450.000 đ/m²" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bề mặt hoàn thiện (Finish)</label>
                    <input type="text" name="finish" value="{{ old('finish') }}" placeholder="Ví dụ: Vân gỗ sồi mờ, chải xước hairline..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Thông số kỹ thuật tóm tắt (Tech specs)</label>
                    <input type="text" name="tech_specs" value="{{ old('tech_specs') }}" placeholder="Ví dụ: Dày 4mm, wear layer 0.3mm chống trượt R9" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ứng dụng tiêu biểu (Application)</label>
                    <input type="text" name="application" value="{{ old('application') }}" placeholder="Ví dụ: Khu vực văn phòng làm việc mở, sảnh đón, pantry khô..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
        </div>

        <!-- Suitable Locations & Description -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                3. Bề mặt tương thích &amp; Mô tả chi tiết
            </h3>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Vị trí bề mặt tương thích (Wizard Filter)</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                    @foreach($surfaces as $surf)
                        @php $key = is_array($surf) ? ($surf['en'] ?? '') : $surf; @endphp
                        @php $label = is_array($surf) ? ($surf['vi'] ?? $key) : $surf; @endphp
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                            <input type="checkbox" name="suitable_locations[]" value="{{ $key }}" {{ in_array($key, old('suitable_locations', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả đặc tính kỹ thuật (Technical Overview)</label>
                <textarea name="tech_description" rows="3" placeholder="Chi tiết chứng chỉ xanh, khả năng chống cháy, cách âm, tải trọng..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">{{ old('tech_description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Trải nghiệm không gian &amp; Kiến nghị Workspace (Experience &amp; Guidance)</label>
                <textarea name="experience_description" rows="3" placeholder="Đánh giá cảm quan thị giác, xúc giác, bảo trì vệ sinh thực tế tại công trình..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">{{ old('experience_description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Hình ảnh đại diện (URL hoặc đường dẫn)</label>
                    <input type="text" name="image_url" value="{{ old('image_url') }}" placeholder="https://... hoặc /storage/..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Đường dẫn hình ảnh thực tế chất lượng cao.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Thứ tự ưu tiên sắp xếp (Sort Order)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-xs font-semibold text-gray-800">Hiển thị trong danh mục gợi ý Material Lab (Active)</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.material-lab.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu vật liệu mới
            </button>
        </div>
    </form>
</div>
@endsection

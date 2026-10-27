@extends('cms.layouts.app')

@section('title', 'Sửa đánh giá')
@section('page-title', 'Sửa đánh giá')

@section('content')
@php $projectCode = request()->route('projectCode'); @endphp

<div class="max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('project.admin.reviews.index', $projectCode) }}" class="text-sm text-gray-600 hover:text-gray-900">
            ← Quay lại danh sách
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Chỉnh sửa đánh giá</h2>

        <form method="POST" action="{{ route('project.admin.reviews.update', [$projectCode, $review->id]) }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Reviewer Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên người đánh giá <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="reviewer_name" value="{{ old('reviewer_name', $review->reviewer_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('reviewer_name') border-red-500 @enderror">
                    @error('reviewer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chức danh / Nghề nghiệp</label>
                    <input type="text" name="reviewer_title" value="{{ old('reviewer_title', $review->reviewer_title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện (URL)</label>
                @if($review->reviewer_avatar)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ $review->reviewer_avatar }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                        <span class="text-xs text-gray-500">Ảnh hiện tại</span>
                    </div>
                @endif
                <input type="text" name="reviewer_avatar" value="{{ old('reviewer_avatar', $review->reviewer_avatar) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nội dung đánh giá <span class="text-red-500">*</span>
                </label>
                <textarea name="content" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror">{{ old('content', $review->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Rating --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số sao <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    @for($i = 5; $i >= 1; $i--)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                               {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                        <div class="flex items-center gap-1 px-3 py-1.5 border rounded-lg hover:bg-yellow-50 transition has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-400">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ $i }}</span>
                        </div>
                    </label>
                    @endfor
                </div>
            </div>

            {{-- Image --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh kèm đánh giá (URL, tùy chọn)</label>
                <input type="text" name="image" value="{{ old('image', $review->image) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Status + Sort --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="approved" {{ old('status', $review->status) === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="pending"  {{ old('status', $review->status) === 'pending'  ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="rejected" {{ old('status', $review->status) === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $review->sort_order) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lưu thay đổi
                </button>
                <a href="{{ route('project.admin.reviews.index', $projectCode) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <form method="POST"
                      action="{{ route('project.admin.reviews.destroy', [$projectCode, $review->id]) }}"
                      class="ml-auto"
                      onsubmit="return confirm('Xóa đánh giá này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition">
                        Xóa đánh giá
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection

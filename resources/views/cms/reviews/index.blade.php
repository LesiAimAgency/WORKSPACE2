@extends('cms.layouts.app')

@section('title', 'Quản lý đánh giá')
@section('page-title', 'Đánh giá khách hàng')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Đánh giá khách hàng</h1>
                <p class="text-gray-600 text-sm mt-1">Quản lý đánh giá / testimonials hiển thị trên website</p>
            </div>
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.reviews.create', $currentProject->code) : '#' }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm đánh giá
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mt-4 flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, nội dung..."
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 flex-1 min-w-48">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Lọc</button>
            @if(request()->anyFilled(['search','status']))
                <a href="{{ url()->current() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Xóa lọc</a>
            @endif
        </form>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người đánh giá</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ tự</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($review->reviewer_avatar)
                                    <img src="{{ $review->reviewer_avatar }}" alt="{{ $review->reviewer_name }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                        {{ mb_substr($review->reviewer_name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $review->reviewer_name }}</div>
                                    @if($review->reviewer_title)
                                        <div class="text-xs text-gray-500">{{ $review->reviewer_title }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700 line-clamp-2 max-w-xs">{{ $review->content }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-500 ml-1">({{ $review->rating }}/5)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'approved' => 'bg-green-100 text-green-800',
                                    'pending'  => 'bg-yellow-100 text-yellow-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'approved' => 'Đã duyệt',
                                    'pending'  => 'Chờ duyệt',
                                    'rejected' => 'Từ chối',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$review->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$review->status] ?? $review->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $review->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Quick status toggle --}}
                                @if($review->status !== 'approved')
                                    <form method="POST"
                                          action="{{ route('project.admin.reviews.update-status', [request()->route('projectCode'), $review->id]) }}"
                                          class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-xs px-2 py-1 border border-green-200 rounded">Duyệt</button>
                                    </form>
                                @endif

                                <a href="{{ route('project.admin.reviews.edit', [request()->route('projectCode'), $review->id]) }}"
                                   class="text-blue-600 hover:text-blue-900">Sửa</a>

                                <form method="POST"
                                      action="{{ route('project.admin.reviews.destroy', [request()->route('projectCode'), $review->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Xóa đánh giá này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <p class="text-lg font-medium">Chưa có đánh giá nào</p>
                            <a href="{{ route('project.admin.reviews.create', request()->route('projectCode')) }}"
                               class="mt-2 inline-block text-blue-600 hover:underline">Thêm đánh giá đầu tiên</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

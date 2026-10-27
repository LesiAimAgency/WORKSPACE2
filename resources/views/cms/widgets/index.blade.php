@extends('cms.layouts.app')

@section('title', 'Widgets Management')
@section('page-title', 'Widgets')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Widget Manager</h2>
                <p class="text-sm text-gray-500 mt-1">Thêm, cấu hình và xem trước các block nội dung cho từng khu vực website.</p>
            </div>
            <a href="{{ route('admin.widgets.index') }}" class="px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419]">
                Add New Widget
            </a>
        </div>
    </div>

    <div class="p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($widgets as $widget)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $widget->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ $widget->type }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $widget->area }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $widget->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($widget->is_active)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.widgets.edit', $widget->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <form action="{{ route('admin.widgets.destroy', $widget->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No widgets found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

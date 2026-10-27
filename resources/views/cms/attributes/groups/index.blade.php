{{-- MODIFIED: 2025-12-19 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý nhóm thuộc tính')
@section('page-title', 'Nhóm thuộc tính')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Tìm kiếm nhóm thuộc tính..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Tìm kiếm
        </button>
    </form>
    
    <div class="flex gap-2">
        <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Quản lý thuộc tính
        </a>
        <a href="{{ route('project.admin.attributes.groups.create', request()->route('projectCode')) }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Thêm nhóm
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc tính</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($groups as $group)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $group->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $group->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ $group->attributes()->count() }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $group->is_active ? 'Kích hoạt' : 'Vô hiệu' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('project.admin.attributes.groups.edit', [request()->route('projectCode'), $group]) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        <form method="POST" action="{{ route('project.admin.attributes.groups.destroy', [request()->route('projectCode'), $group]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Không có nhóm thuộc tính nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $groups->links() }}
</div>
@endsection

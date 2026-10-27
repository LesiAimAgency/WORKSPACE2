@extends('cms.layouts.app')

@section('title', 'Cài đặt - ' . $currentProject->name)
@section('page-title', 'Cài đặt dự án')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="font-medium text-blue-900">Dự án: {{ $currentProject->name }} ({{ $currentProject->code }})</p>
    </div>

    <form method="POST" action="{{ \Route::has('project.admin.settings.save') ? route('project.admin.settings.save', $currentProject->code) : route('admin.settings.save') }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        
        <div class="space-y-6">
            @foreach($permissions as $permission)
            <div class="border-b pb-4">
                <h3 class="font-semibold text-lg mb-3 capitalize">{{ $permission->module }}</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="permissions[{{ $permission->module }}][can_view]" 
                               value="1" {{ $permission->can_view ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Xem</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="permissions[{{ $permission->module }}][can_create]" 
                               value="1" {{ $permission->can_create ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Tạo mới</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="permissions[{{ $permission->module }}][can_edit]" 
                               value="1" {{ $permission->can_edit ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Chỉnh sửa</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="permissions[{{ $permission->module }}][can_delete]" 
                               value="1" {{ $permission->can_delete ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Xóa</span>
                    </label>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
            <a href="{{ \Route::has('project.admin.dashboard') ? route('project.admin.dashboard', $currentProject->code) : route('admin.dashboard') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu phân quyền</button>
        </div>
    </form>
</div>
@endsection

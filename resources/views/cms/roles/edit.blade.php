@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Cập nhật Vai trò')
@section('page-title', 'Cập nhật Vai trò')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết Vai trò: {{ $role->display_name }}</h2>
        <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.roles.index') : route('project.admin.roles.index', request()->route('projectCode')) }}" class="text-gray-500 hover:text-gray-700">
            &larr; Quay lại danh sách
        </a>
    </div>

    <form action="{{ request()->routeIs('superadmin.*') ? route('superadmin.roles.update', $role) : route('project.admin.roles.update', ['projectCode' => request()->route('projectCode'), 'role' => $role]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị <span class="text-red-500">*</span></label>
                <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror">
                @error('display_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã Vai trò (name) <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cấp độ (Level 1-10) <span class="text-red-500">*</span></label>
                <input type="number" name="level" value="{{ old('level', $role->level) }}" min="1" max="10" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('level') border-red-500 @enderror">
                @error('level') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $role->description) }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $role->is_default) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Đặt làm vai trò mặc định (cho người dùng mới)</span>
                </label>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-md font-medium text-gray-900 mb-4 border-b pb-2">Phân quyền (Permissions)</h3>
            
            @if(isset($permissions) && count($permissions) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $rolePermissions = old('permissions', $role->permissions->pluck('id')->toArray());
                    @endphp
                    @foreach($permissions as $group => $perms)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3 capitalize">{{ $group ?: 'Khác' }}</h4>
                            <div class="space-y-2">
                                @foreach($perms as $permission)
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700 block">{{ $permission->display_name ?? $permission->name }}</span>
                                            @if($permission->description)
                                                <span class="text-xs text-gray-500">{{ $permission->description }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Không có quyền (permissions) nào trong hệ thống.</p>
            @endif
            @error('permissions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.roles.index') : route('project.admin.roles.index', request()->route('projectCode')) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection

@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Xem Vai trò')
@section('page-title', 'Xem Vai trò')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết Vai trò: {{ $role->display_name }}</h2>
        <div class="flex gap-2">
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.roles.edit', $role) : route('project.admin.roles.edit', ['projectCode' => request()->route('projectCode'), 'role' => $role]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
                Sửa vai trò
            </a>
            <a href="{{ request()->routeIs('superadmin.*') ? route('superadmin.roles.index') : route('project.admin.roles.index', request()->route('projectCode')) }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 border rounded-lg">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-b pb-6">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Tên hiển thị</h3>
            <p class="mt-1 text-base text-gray-900">{{ $role->display_name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Mã (name)</h3>
            <p class="mt-1 text-base text-gray-900">{{ $role->name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Mô tả</h3>
            <p class="mt-1 text-base text-gray-900">{{ $role->description ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Level</h3>
            <p class="mt-1 text-base text-gray-900">{{ $role->level }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Mặc định</h3>
            <p class="mt-1 text-base">
                @if($role->is_default)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Có</span>
                @else
                    <span class="text-gray-400">Không</span>
                @endif
            </p>
        </div>
    </div>
    
    <div>
        <h3 class="text-md font-medium text-gray-900 mb-4">Các Quyền (Permissions) đã cấp</h3>
        
        @if($role->permissions->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($role->permissions as $permission)
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $permission->display_name ?? $permission->name }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 italic">Vai trò này chưa được cấp quyền nào.</p>
        @endif
    </div>
</div>
@endsection

@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Danh sách người dùng')

@section('content')
<div class="space-y-4">
    <!-- Header Tabs Phân loại tài khoản -->
    

    <!-- Table Container -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" class="flex flex-wrap gap-2 flex-1">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm email, tên, username..." 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 min-w-[220px]">
                
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả vai trò</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 font-medium">
                    Lọc
                </button>
                @if(request()->hasAny(['search', 'role', 'type']))
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm flex items-center">
                        Đặt lại
                    </a>
                @endif
            </form>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2 font-medium whitespace-nowrap shadow-xs">
                    + Thêm người dùng
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tên / Email</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Phòng ban / Đơn vị</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vai trò</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        @php
                            $isSuper = $user->isSuperAdmin();
                            $isMulti = ! $isSuper && $user->isMultiTenancy();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $isSuper ? 'bg-amber-50/20' : ($isMulti ? 'bg-purple-50/20' : '') }}">
                          
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="w-8 h-8 rounded-full object-cover bg-gray-100">
                                    @else
                                        <div class="w-8 h-8 rounded-full {{ $isSuper ? 'bg-amber-100 text-amber-700' : ($isMulti ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-600') }} flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($isSuper)
                                                <span class="text-[10px] bg-amber-600 text-white font-bold px-1.5 py-0.2 rounded">SUPER</span>
                                            @elseif($isMulti)
                                                <span class="text-[10px] bg-purple-600 text-white font-bold px-1.5 py-0.2 rounded">MT</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $user->email }}</div>
                                        @if($user->username)
                                            <div class="text-[11px] text-gray-400 font-mono">User: {{ $user->username }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($isSuper)
                                    <span class="text-amber-800 font-semibold text-xs">{{ $user->department ?: 'Ban Quản trị SuperAdmin' }}</span>
                                @elseif($isMulti)
                                    @php
                                        $userProjectIds = is_array($user->project_ids) ? $user->project_ids : json_decode($user->project_ids ?? '[]', true) ?? [];
                                    @endphp
                                    @if(!empty($userProjectIds) && isset($projects))
                                        <div class="space-y-1">
                                             @foreach($userProjectIds as $pid)
                                                @if(isset($projects[$pid]))
                                                    <div class="font-medium text-purple-700 text-xs flex items-center gap-1">
                                                        <span>{{ $projects[$pid]->name }}</span>
                                                        <span class="text-gray-400 font-mono">({{ $projects[$pid]->code }})</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($user->tenant)
                                        <div class="font-medium text-purple-700 text-xs flex items-center gap-1">
                                            <span class="text-gray-500">Tenant:</span>
                                            <span>{{ $user->tenant->name }}</span>
                                            <span class="text-gray-400 font-mono">({{ $user->tenant->code }})</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Chưa gắn dự án</span>
                                    @endif
                                @else
                                    <span class="text-gray-700 font-medium">{{ $user->department ?: '-' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if($isSuper)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Super Admin
                                        </span>
                                    @elseif($isMulti)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            Multi-Tenancy Control Center
                                        </span>
                                    @endif
                                    @foreach($user->roles as $role)
                                        @if(!in_array($role->name, ['multi_tenancy', 'super_admin', 'superadmin']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role->display_name ?? $role->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if($user->roles->isEmpty() && !$isMulti && !$isSuper)
                                        <span class="text-xs text-gray-400 italic">{{ $user->role ?? 'N/A' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Khóa
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $editUrl = route('admin.users.edit', $user);
                                        $deleteUrl = route('admin.users.destroy', $user);
                                    @endphp
                                    <a href="{{ $editUrl }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium text-xs" title="Sửa">
                                        Sửa
                                    </a>
                                    
                                    <form action="{{ $deleteUrl }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium text-xs" title="Xóa">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                Không tìm thấy người dùng nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

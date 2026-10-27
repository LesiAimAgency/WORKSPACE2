@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Chi tiết người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết người dùng: {{ $user->name }}</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                Sửa người dùng
            </a>
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Thông tin Tài khoản -->
        <div class="col-span-1 lg:col-span-3 pb-2 border-b border-gray-100">
            <h3 class="text-base font-medium text-gray-800">1. Thông tin Tài khoản</h3>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Email</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->email }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Username</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->username ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Trạng thái</h4>
            <p class="mt-1 text-base">
                @if($user->status)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Khóa</span>
                @endif
            </p>
        </div>
        <div class="col-span-1 lg:col-span-3">
            <h4 class="text-sm font-medium text-gray-500">Vai trò</h4>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($user->roles as $role)
                    <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $role->display_name ?? $role->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Thông tin Công việc -->
        <div class="col-span-1 lg:col-span-3 pb-2 border-b border-gray-100 mt-4">
            <h3 class="text-base font-medium text-gray-800">2. Thông tin Công việc</h3>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Mã nhân viên (ID)</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->employee_code ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Ngày vào làm</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('d/m/Y') : 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Phòng ban</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->department ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Loại hợp đồng</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->contract_type ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Quản lý trực tiếp</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->manager ? $user->manager->name : 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Mức lương cơ bản</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->base_salary ? number_format($user->base_salary, 0, ',', '.') . ' VNĐ' : 'N/A' }}</p>
        </div>

        <!-- Thông tin Cá nhân -->
        <div class="col-span-1 lg:col-span-3 pb-2 border-b border-gray-100 mt-4">
            <h3 class="text-base font-medium text-gray-800">3. Thông tin Cá nhân</h3>
        </div>
        <div class="col-span-1 lg:col-span-3 flex items-center gap-4 mb-2">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
            @else
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h4 class="text-sm font-medium text-gray-500">Tên hiển thị</h4>
                <p class="mt-1 text-lg font-medium text-gray-900">{{ $user->name }}</p>
            </div>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Ngày sinh</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Số điện thoại</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Địa chỉ</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->address ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Căn cước công dân</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->identity_card ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Ngày cấp</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->identity_date ? \Carbon\Carbon::parse($user->identity_date)->format('d/m/Y') : 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Nơi cấp</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->identity_place ?? 'N/A' }}</p>
        </div>

        <!-- Thông tin Ngân hàng -->
        <div class="col-span-1 lg:col-span-3 pb-2 border-b border-gray-100 mt-4">
            <h3 class="text-base font-medium text-gray-800">4. Thông tin Ngân hàng</h3>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Số tài khoản</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->bank_account ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Ngân hàng</h4>
            <p class="mt-1 text-base text-gray-900">{{ $user->bank_name ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection

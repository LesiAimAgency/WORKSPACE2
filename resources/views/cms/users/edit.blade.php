@extends(request()->routeIs('superadmin.*') ? 'superadmin.layouts.app' : 'cms.layouts.app')

@section('title', 'Cập nhật người dùng')
@section('page-title', 'Cập nhật người dùng')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">Thông tin người dùng: {{ $user->name }}</h2>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200">
                {{ session('success') }}
            </div>
        @endif


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Cột trái -->
            <div class="space-y-6">
                <!-- Thông tin tài khoản -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">1. Thông tin Tài khoản</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('username') border-red-500 @enderror">
                            @error('username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò (Roles)</label>
                            <div class="grid grid-cols-2 gap-2">
                                @php
                                    $userRoles = old('roles', $user->roles->pluck('id')->toArray());
                                @endphp
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="roles[]" value="{{ $role->id }}" 
                                               {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                                        <span class="text-sm text-gray-700">{{ $role->display_name ?? $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if(isset($projects) && $projects->isNotEmpty())
                        <div class="md:col-span-2 mt-2 pt-2 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Gán Dự án / Website quản lý <span class="text-xs text-purple-600 font-normal">(Dành cho Multi-Tenancy Control Center)</span>
                            </label>
                            @php
                                $assignedProjectIds = old('project_ids', is_array($user->project_ids) ? $user->project_ids : json_decode($user->project_ids ?? '[]', true) ?? []);
                            @endphp
                            <select name="project_ids[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Không gán / Chọn dự án --</option>
                                @foreach($projects ?? [] as $proj)
                                    <option value="{{ $proj->id }}" {{ in_array($proj->id, $assignedProjectIds) ? 'selected' : '' }}>
                                        {{ $proj->name }} ({{ $proj->code }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Tài khoản Multi-Tenancy sẽ được liên kết và chỉ quản lý website/dự án này.</p>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="status" value="1" {{ old('status', $user->status) == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Kích hoạt tài khoản</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Thông tin Công việc -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">2. Thông tin Công việc</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên (ID)</label>
                            <input type="text" name="employee_code" value="{{ old('employee_code', $user->employee_code) }}" placeholder="VD: NV001"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('employee_code') border-red-500 @enderror">
                            @error('employee_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày vào làm</label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                            <select name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Chọn phòng ban --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $user->department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại hợp đồng</label>
                            <select name="contract_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Chọn loại --</option>
                                <option value="Thử việc" {{ old('contract_type', $user->contract_type) == 'Thử việc' ? 'selected' : '' }}>Thử việc</option>
                                <option value="Chính thức" {{ old('contract_type', $user->contract_type) == 'Chính thức' ? 'selected' : '' }}>Chính thức</option>
                                <option value="Part-time" {{ old('contract_type', $user->contract_type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Freelance" {{ old('contract_type', $user->contract_type) == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quản lý trực tiếp (Manager)</label>
                            <select name="manager_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Chọn quản lý --</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}" {{ old('manager_id', $user->manager_id) == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }} ({{ $mgr->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mức lương cơ bản (VNĐ)</label>
                            <input type="number" name="base_salary" value="{{ old('base_salary', (int) $user->base_salary) }}" min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải -->
            <div class="space-y-6">
                <!-- Thông tin Cá nhân -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">3. Thông tin Cá nhân</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị (Họ và tên) <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                            <input type="date" name="dob" value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <select name="province_code" id="province_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                </select>
                                <select name="district_code" id="district_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                                <select name="ward_code" id="ward_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                            <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $user->street_address) }}" placeholder="Số nhà, tên đường..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <input type="hidden" name="full_address" id="full_address" value="{{ old('full_address', $user->address) }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Căn cước công dân (CCCD/CMND)</label>
                            <input type="text" name="identity_card" value="{{ old('identity_card', $user->identity_card) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày cấp</label>
                            <input type="date" name="identity_date" value="{{ old('identity_date', $user->identity_date ? \Carbon\Carbon::parse($user->identity_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nơi cấp</label>
                            <input type="text" name="identity_place" value="{{ old('identity_place', $user->identity_place) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
                            <h4 class="font-medium text-gray-700 mb-3 text-sm">Thông tin Ngân hàng</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                            <input type="text" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngân hàng</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="VD: Vietcombank CN Tân Bình"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200 flex items-center gap-4">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                            @endif
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện (Avatar)</label>
                                <input type="file" name="avatar" accept="image/*"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                @error('avatar') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2 bg-[#001B4E] text-white rounded-lg hover:bg-[#002D80] font-medium">
                Cập nhật
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province_code');
    const districtSelect = document.getElementById('district_code');
    const wardSelect = document.getElementById('ward_code');
    const streetInput = document.getElementById('street_address');
    const fullAddressInput = document.getElementById('full_address');

    const oldProvince = '{{ old('province_code', $user->province_code) }}';
    const oldDistrict = '{{ old('district_code', $user->district_code) }}';
    const oldWard = '{{ old('ward_code', $user->ward_code) }}';

    // Fetch Provinces
    fetch('https://provinces.open-api.vn/api/p/')
        .then(response => response.json())
        .then(data => {
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                if (province.code == oldProvince) option.selected = true;
                provinceSelect.appendChild(option);
            });
            if (oldProvince) fetchDistricts(oldProvince, oldDistrict);
        });

    provinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
        
        if (provinceCode) {
            fetchDistricts(provinceCode);
        } else {
            districtSelect.disabled = true;
        }
        updateFullAddress();
    });

    districtSelect.addEventListener('change', function() {
        const districtCode = this.value;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        
        if (districtCode) {
            fetchWards(districtCode);
        } else {
            wardSelect.disabled = true;
        }
        updateFullAddress();
    });

    wardSelect.addEventListener('change', updateFullAddress);
    streetInput.addEventListener('input', updateFullAddress);

    function fetchDistricts(provinceCode, selectedDistrict = null) {
        districtSelect.disabled = true;
        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.code;
                    option.textContent = district.name;
                    if (district.code == selectedDistrict) option.selected = true;
                    districtSelect.appendChild(option);
                });
                districtSelect.disabled = false;
                if (selectedDistrict) fetchWards(selectedDistrict, oldWard);
            });
    }

    function fetchWards(districtCode, selectedWard = null) {
        wardSelect.disabled = true;
        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                data.wards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.code;
                    option.textContent = ward.name;
                    if (ward.code == selectedWard) option.selected = true;
                    wardSelect.appendChild(option);
                });
                wardSelect.disabled = false;
            });
    }

    function updateFullAddress() {
        const parts = [];
        if (streetInput.value) parts.push(streetInput.value);
        if (wardSelect.value) parts.push(wardSelect.options[wardSelect.selectedIndex].text);
        if (districtSelect.value) parts.push(districtSelect.options[districtSelect.selectedIndex].text);
        if (provinceSelect.value) parts.push(provinceSelect.options[provinceSelect.selectedIndex].text);
        
        fullAddressInput.value = parts.join(', ');
    }
});
</script>
@endsection

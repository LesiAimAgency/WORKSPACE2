@extends('cms.layouts.app')

@section('title', 'Cấu hình vận chuyển')
@section('page-title', 'Vận chuyển')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = route('admin.settings.save');
@endphp

<div class="bg-white rounded-lg shadow-sm p-6" x-data="shippingConfig()">
    <form action="{{ $settingsSaveUrl }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @php
                $shippingSetting = setting('shipping', []);
                $shipping = is_array($shippingSetting) ? $shippingSetting : json_decode($shippingSetting, true) ?? [];
                $providers = $shipping['providers'] ?? [];
            @endphp

            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold mb-2">Đơn vị vận chuyển</h3>
                <p class="text-sm text-gray-500">Bật/tắt các đơn vị vận chuyển có sẵn</p>
            </div>

            <!-- GHN -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="https://ghn.vn/favicon.ico" alt="GHN" class="w-10 h-10 rounded">
                        <div>
                            <h4 class="font-semibold">Giao Hàng Nhanh (GHN)</h4>
                            <p class="text-xs text-gray-500">Giao hàng toàn quốc</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][ghn][enabled]" value="1" 
                               {{ ($providers['ghn']['enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['ghn']['enabled'] ?? false) ? 'true' : 'false' }}" class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t">
                    <div>
                        <label class="block text-xs font-medium mb-1">Token</label>
                        <input type="text" name="shipping[providers][ghn][token]" value="{{ $providers['ghn']['token'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Shop ID</label>
                        <input type="text" name="shipping[providers][ghn][shop_id]" value="{{ $providers['ghn']['shop_id'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- GHTK -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="https://giaohangtietkiem.vn/favicon.ico" alt="GHTK" class="w-10 h-10 rounded">
                        <div>
                            <h4 class="font-semibold">Giao Hàng Tiết Kiệm (GHTK)</h4>
                            <p class="text-xs text-gray-500">Tiết kiệm chi phí vận chuyển</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][ghtk][enabled]" value="1" 
                               {{ ($providers['ghtk']['enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['ghtk']['enabled'] ?? false) ? 'true' : 'false' }}" class="grid grid-cols-1 gap-3 mt-3 pt-3 border-t">
                    <div>
                        <label class="block text-xs font-medium mb-1">API Token</label>
                        <input type="text" name="shipping[providers][ghtk][token]" value="{{ $providers['ghtk']['token'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- VNPost -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="https://vnpost.vn/favicon.ico" alt="VNPost" class="w-10 h-10 rounded">
                        <div>
                            <h4 class="font-semibold">Bưu điện Việt Nam (VNPost)</h4>
                            <p class="text-xs text-gray-500">Dịch vụ bưu chính quốc gia</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][vnpost][enabled]" value="1" 
                               {{ ($providers['vnpost']['enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['vnpost']['enabled'] ?? false) ? 'true' : 'false' }}" class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t">
                    <div>
                        <label class="block text-xs font-medium mb-1">Username</label>
                        <input type="text" name="shipping[providers][vnpost][username]" value="{{ $providers['vnpost']['username'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Password</label>
                        <input type="password" name="shipping[providers][vnpost][password]" value="{{ $providers['vnpost']['password'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Viettel Post -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="https://viettelpost.vn/favicon.ico" alt="Viettel Post" class="w-10 h-10 rounded">
                        <div>
                            <h4 class="font-semibold">Viettel Post</h4>
                            <p class="text-xs text-gray-500">Chuyển phát nhanh Viettel</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][viettelpost][enabled]" value="1" 
                               {{ ($providers['viettelpost']['enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['viettelpost']['enabled'] ?? false) ? 'true' : 'false' }}" class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t">
                    <div>
                        <label class="block text-xs font-medium mb-1">Username</label>
                        <input type="text" name="shipping[providers][viettelpost][username]" value="{{ $providers['viettelpost']['username'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Password</label>
                        <input type="password" name="shipping[providers][viettelpost][password]" value="{{ $providers['viettelpost']['password'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- J&T Express -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="https://jtexpress.vn/favicon.ico" alt="J&T" class="w-10 h-10 rounded">
                        <div>
                            <h4 class="font-semibold">J&T Express</h4>
                            <p class="text-xs text-gray-500">Chuyển phát nhanh quốc tế</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][jnt][enabled]" value="1" 
                               {{ ($providers['jnt']['enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['jnt']['enabled'] ?? false) ? 'true' : 'false' }}" class="grid grid-cols-1 gap-3 mt-3 pt-3 border-t">
                    <div>
                        <label class="block text-xs font-medium mb-1">API Key</label>
                        <input type="text" name="shipping[providers][jnt][api_key]" value="{{ $providers['jnt']['api_key'] ?? '' }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- COD (Tự giao) -->
            <div class="border rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Tự giao hàng</h4>
                            <p class="text-xs text-gray-500">Giao hàng bằng đội ngũ riêng</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="shipping[providers][self][enabled]" value="1" 
                               {{ ($providers['self']['enabled'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div x-show="{{ ($providers['self']['enabled'] ?? true) ? 'true' : 'false' }}" class="grid grid-cols-1 gap-3 mt-3 pt-3 border-t border-gray-300">
                    <div>
                        <label class="block text-xs font-medium mb-1">Phí ship cố định (₫)</label>
                        <input type="number" name="shipping[providers][self][fee]" value="{{ $providers['self']['fee'] ?? 30000 }}" class="w-full px-3 py-2 text-sm border rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function shippingConfig() {
    return {
        init() {
            // Handle toggle visibility
            document.querySelectorAll('input[type="checkbox"][name*="enabled"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const container = this.closest('.border');
                    const detailsDiv = container.querySelector('[x-show]');
                    if (detailsDiv) {
                        detailsDiv.style.display = this.checked ? 'grid' : 'none';
                    }
                });
            });
        }
    }
}
</script>
@endsection

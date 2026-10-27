@extends('cms.settings.template', ['title' => 'Phương thức thanh toán'])

@section('form-content')
<div class="space-y-6">
    <div class="bg-gray-50 p-4 rounded-lg">
        <label class="flex items-center">
            <input type="checkbox" name="payment_cod_enabled" value="1" {{ setting('payment_cod_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
            <span class="ml-2 font-medium">Thanh toán khi nhận hàng (COD)</span>
        </label>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
        <label class="flex items-center">
            <input type="checkbox" name="payment_bank_enabled" value="1" {{ setting('payment_bank_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
            <span class="ml-2 font-medium">Chuyển khoản ngân hàng</span>
        </label>
        <div class="ml-6 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên ngân hàng</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', setting('bank_name')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                <input type="text" name="bank_account" value="{{ old('bank_account', setting('bank_account')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chủ tài khoản</label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', setting('bank_account_name')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
        <label class="flex items-center">
            <input type="checkbox" name="payment_momo_enabled" value="1" {{ setting('payment_momo_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
            <span class="ml-2 font-medium">Ví MoMo</span>
        </label>
        <div class="ml-6 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Partner Code</label>
                <input type="text" name="momo_partner_code" value="{{ old('momo_partner_code', setting('momo_partner_code')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Access Key</label>
                <input type="text" name="momo_access_key" value="{{ old('momo_access_key', setting('momo_access_key')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                <input type="password" name="momo_secret_key" value="{{ old('momo_secret_key', setting('momo_secret_key')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
        <label class="flex items-center">
            <input type="checkbox" name="payment_vnpay_enabled" value="1" {{ setting('payment_vnpay_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
            <span class="ml-2 font-medium">VNPay</span>
        </label>
        <div class="ml-6 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">TMN Code</label>
                <input type="text" name="vnpay_tmn_code" value="{{ old('vnpay_tmn_code', setting('vnpay_tmn_code')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hash Secret</label>
                <input type="password" name="vnpay_hash_secret" value="{{ old('vnpay_hash_secret', setting('vnpay_hash_secret')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
</div>
@endsection

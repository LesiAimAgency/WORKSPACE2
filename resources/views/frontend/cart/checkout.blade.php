@extends('frontend.layouts.master')

@php
  $projectCode = request()->route('projectCode');
@endphp

@section('content')
<div class="bg-gradient-to-r from-green-600 to-teal-600 py-8">
  <div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-white"> Thanh toán</h1>
    <nav class="text-white/80 text-sm mt-2">
      <a href="/{{ $projectCode }}" class="hover:text-white">Trang chủ</a> / 
      <a href="/{{ $projectCode }}/cart" class="hover:text-white">Giỏ hàng</a> / 
      <span>Thanh toán</span>
    </nav>
  </div>
</div>

<div class="container mx-auto px-4 py-8">
  @if(session('error'))
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
  @endif
  
  @if(empty($cart))
  <div class="bg-white rounded-lg shadow-sm p-12 text-center">
    <p class="text-gray-500 mb-4">Giỏ hàng trống, vui lòng thêm sản phẩm trước khi thanh toán</p>
    <a href="{{ locale_route('shop.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700">
      Mua sắm ngay
    </a>
  </div>
  @else
  <form action="/{{ $projectCode }}/checkout/process" method="POST" id="checkoutForm">
    @csrf
    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Checkout Form -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">1</span>
            Thông tin khách hàng
          </h2>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Họ tên <span class="text-red-500">*</span></label>
              <input type="text" name="name" required value="{{ old('name') }}"
                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Nguyễn Văn A">
              @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Số điện thoại <span class="text-red-500">*</span></label>
              <input type="tel" name="phone" required value="{{ old('phone') }}"
                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0901234567">
              @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
              <input type="email" name="email" required value="{{ old('email') }}"
                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="email@example.com">
              @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">2</span>
            Địa chỉ giao hàng
          </h2>
          <div class="space-y-4">
            <div class="grid md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Tỉnh/Thành phố</label>
                <select name="city" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                  <option value="">Chọn tỉnh/thành</option>
                  <option value="Hà Nội">Hà Nội</option>
                  <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                  <option value="Đà Nẵng">Đà Nẵng</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Quận/Huyện</label>
                <select name="district" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                  <option value="">Chọn quận/huyện</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Phường/Xã</label>
                <select name="ward" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                  <option value="">Chọn phường/xã</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
              <input type="text" name="address" required value="{{ old('address') }}"
                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Số nhà, tên đường, tòa nhà...">
              @error('address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Ghi chú đơn hàng</label>
              <textarea name="note" rows="2" 
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn">{{ old('note') }}</textarea>
            </div>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">3</span>
            Phương thức thanh toán
          </h2>
          <div class="space-y-3">
            <label class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-300 transition payment-option">
              <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-blue-600">
              <div class="flex-1">
                <div class="font-semibold flex items-center gap-2">
                   Thanh toán khi nhận hàng (COD)
                </div>
                <div class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận hàng</div>
              </div>
            </label>
            <label class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-300 transition payment-option">
              <input type="radio" name="payment_method" value="bank_transfer" class="w-5 h-5 text-blue-600">
              <div class="flex-1">
                <div class="font-semibold flex items-center gap-2">
                   Chuyển khoản ngân hàng
                </div>
                <div class="text-sm text-gray-600">Chuyển khoản trực tiếp vào tài khoản ngân hàng</div>
              </div>
            </label>
            <label class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-300 transition payment-option">
              <input type="radio" name="payment_method" value="vnpay" class="w-5 h-5 text-blue-600">
              <div class="flex-1">
                <div class="font-semibold flex items-center gap-2">
                  <img src="https://vnpay.vn/assets/images/logo-icon/logo-primary.svg" alt="VNPay" class="h-6">
                  VNPay
                </div>
                <div class="text-sm text-gray-600">Thanh toán qua cổng VNPay (ATM, Visa, MasterCard...)</div>
              </div>
            </label>
            <label class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-300 transition payment-option">
              <input type="radio" name="payment_method" value="momo" class="w-5 h-5 text-blue-600">
              <div class="flex-1">
                <div class="font-semibold flex items-center gap-2">
                  <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="Momo" class="h-6">
                  Ví MoMo
                </div>
                <div class="text-sm text-gray-600">Thanh toán qua ví điện tử MoMo</div>
              </div>
            </label>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
          <h2 class="text-xl font-bold mb-4"> Đơn hàng của bạn</h2>
          
          <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
            @foreach($cart as $item)
            <div class="flex gap-3 pb-3 border-b">
              <img src="{{ $item['image'] ?? '/assets/img/placeholder-images-image_large.webp' }}" 
                 alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
              <div class="flex-1">
                <h4 class="font-medium text-sm line-clamp-2">{{ $item['name'] }}</h4>
                <p class="text-gray-500 text-sm">x{{ $item['quantity'] }}</p>
                <p class="text-blue-600 font-bold text-sm">{{ number_format($item['price'] * $item['quantity']) }}đ</p>
              </div>
            </div>
            @endforeach
          </div>
          
          <div class="space-y-2 text-sm border-t pt-4">
            <div class="flex justify-between">
              <span class="text-gray-600">Tạm tính:</span>
              <span>{{ number_format($total) }}đ</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Phí vận chuyển:</span>
              <span class="text-green-600">Miễn phí</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Giảm giá:</span>
              <span>0đ</span>
            </div>
          </div>
          
          <div class="border-t pt-4 mt-4">
            <div class="flex justify-between text-lg font-bold">
              <span>Tổng cộng:</span>
              <span class="text-blue-600">{{ number_format($total) }}đ</span>
            </div>
          </div>
          
          <button type="submit" 
              class="mt-6 w-full bg-green-600 text-white py-4 rounded-lg hover:bg-green-700 font-bold text-lg transition flex items-center justify-center gap-2">
             Đặt hàng
          </button>
          
          <p class="text-center text-sm text-gray-500 mt-4">
             Thông tin của bạn được bảo mật
          </p>
          
          <div class="mt-4 text-xs text-gray-500">
            Bằng việc đặt hàng, bạn đồng ý với 
            <a href="#" class="text-blue-600 hover:underline">Điều khoản dịch vụ</a> và 
            <a href="#" class="text-blue-600 hover:underline">Chính sách bảo mật</a> của chúng tôi.
          </div>
        </div>
      </div>
    </div>
  </form>
  @endif
</div>

<style>
.payment-option:has(input:checked) {
  border-color: #3b82f6;
  background-color: #eff6ff;
}
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection

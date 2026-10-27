@extends('frontend.layouts.master')

@php
  $projectCode = request()->route('projectCode');
  $orderId = session('orderId');
@endphp

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
      <!-- Success Icon -->
      <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      
      <h1 class="text-3xl font-bold text-green-600 mb-4"> Đặt hàng thành công!</h1>
      
      @if($orderId)
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-gray-600">Mã đơn hàng của bạn:</p>
        <p class="text-2xl font-bold text-blue-600">{{ $orderId }}</p>
      </div>
      @endif
      
      <div class="text-gray-600 mb-8 space-y-2">
        <p>Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi.</p>
        <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
      </div>
      
      <!-- Order Info -->
      <div class="bg-blue-50 rounded-lg p-4 mb-8 text-left">
        <h3 class="font-bold text-blue-800 mb-2"> Thông tin đơn hàng</h3>
        <ul class="text-sm text-blue-700 space-y-1">
          <li> Email xác nhận đã được gửi đến địa chỉ email của bạn</li>
          <li> Đơn hàng sẽ được xử lý trong vòng 24h</li>
          <li> Thời gian giao hàng dự kiến: 2-5 ngày làm việc</li>
        </ul>
      </div>
      
      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/{{ $projectCode }}" 
          class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition">
           Về trang chủ
        </a>
        <a href="{{ locale_route('shop.index') }}" 
          class="inline-flex items-center justify-center gap-2 border border-blue-600 text-blue-600 px-8 py-3 rounded-lg hover:bg-blue-50 transition">
          ️ Tiếp tục mua sắm
        </a>
      </div>
      
      <!-- Contact -->
      <div class="mt-8 pt-6 border-t text-sm text-gray-500">
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ:</p>
        <p class="font-medium text-gray-700">
           Hotline: {{ setting_string('hotline', '1900 1234') }} | 
          ️ Email: {{ setting_string('email', 'support@example.com') }}
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

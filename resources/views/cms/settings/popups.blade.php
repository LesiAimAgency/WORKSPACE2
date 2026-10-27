@extends('cms.layouts.app')

@section('title', 'Cấu hình Popup')
@section('page-title', 'Popup Quảng cáo')

@section('content')
@include('cms.settings.partials.back-link')

@php
  $settingsSaveUrl = route('admin.settings.save');
  
  // Lấy popup settings
  $popupSetting = setting('popup', []);
  $popup = is_array($popupSetting) ? $popupSetting : (json_decode($popupSetting, true) ?: []);
  
  // Default values
  $enabled = $popup['enabled'] ?? false;
  $delay = $popup['delay'] ?? 3;
  $frequency = $popup['frequency'] ?? 'once';
  $position = $popup['position'] ?? 'center';
  $title = $popup['title'] ?? '';
  $subtitle = $popup['subtitle'] ?? '';
  $bgColor = $popup['bg_color'] ?? '#ffffff';
  $textColor = $popup['text_color'] ?? '#1f2937';
  $buttonText = $popup['button_text'] ?? 'Gửi';
  $buttonColor = $popup['button_color'] ?? '#2563eb';
  $image = $popup['image'] ?? '';
  $showOnMobile = $popup['show_mobile'] ?? true;
  $showOnDesktop = $popup['show_desktop'] ?? true;
  $customContent = $popup['content'] ?? '';
  $formId = $popup['form_id'] ?? '';
  
  // Lấy forms
  $formsSetting = setting('forms', []);
  $formsArray = is_array($formsSetting) ? $formsSetting : (json_decode($formsSetting, true) ?: []);
@endphp

<div class="space-y-6" x-data="popupManager()">
  <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Cấu hình chính -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4 pb-3 border-b flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Cấu hình chính
      </h3>
      
      <div class="space-y-4">
        <label class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg cursor-pointer border-2 border-transparent hover:border-blue-200 transition-all">
          <input type="checkbox" name="popup[enabled]" value="1" {{ $enabled ? 'checked' : '' }} 
              class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500" x-model="config.enabled">
          <div>
            <span class="font-semibold text-blue-900">Bật Popup</span>
            <p class="text-sm text-blue-700">Hiển thị popup trên trang frontend</p>
          </div>
        </label>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Độ trễ hiển thị (giây)</label>
            <input type="number" name="popup[delay]" value="{{ $delay }}" min="0" max="60"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                x-model="config.delay">
            <p class="text-xs text-gray-500 mt-1">Thời gian chờ trước khi hiển thị popup</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tần suất hiển thị</label>
            <select name="popup[frequency]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="config.frequency">
              <option value="always" {{ $frequency == 'always' ? 'selected' : '' }}>Mỗi lần truy cập</option>
              <option value="once" {{ $frequency == 'once' ? 'selected' : '' }}>Chỉ 1 lần</option>
              <option value="daily" {{ $frequency == 'daily' ? 'selected' : '' }}>Mỗi ngày 1 lần</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí hiển thị</label>
            <select name="popup[position]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="config.position">
              <option value="center" {{ $position == 'center' ? 'selected' : '' }}>Giữa màn hình</option>
              <option value="bottom-left" {{ $position == 'bottom-left' ? 'selected' : '' }}>Góc dưới trái</option>
              <option value="bottom-right" {{ $position == 'bottom-right' ? 'selected' : '' }}>Góc dưới phải</option>
              <option value="top-left" {{ $position == 'top-left' ? 'selected' : '' }}>Góc trên trái</option>
              <option value="top-right" {{ $position == 'top-right' ? 'selected' : '' }}>Góc trên phải</option>
            </select>
          </div>
        </div>

        <div class="flex flex-wrap gap-6 pt-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="popup[show_desktop]" value="1" {{ $showOnDesktop ? 'checked' : '' }} 
                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" x-model="config.show_desktop">
            <span class="text-sm text-gray-700">Hiển thị trên Desktop</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="popup[show_mobile]" value="1" {{ $showOnMobile ? 'checked' : '' }} 
                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" x-model="config.show_mobile">
            <span class="text-sm text-gray-700">Hiển thị trên Mobile</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Nội dung Popup -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4 pb-3 border-b flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Nội dung Popup
      </h3>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề</label>
            <input type="text" name="popup[title]" value="{{ $title }}" 
                placeholder="VD: Đăng ký nhận ưu đãi!"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                x-model="config.title">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả ngắn</label>
            <textarea name="popup[subtitle]" rows="2" placeholder="VD: Nhận ngay voucher giảm 10%..."
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                 x-model="config.subtitle">{{ $subtitle }}</textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Form</label>
            <select name="popup[form_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="config.form_id">
              <option value="">-- Không dùng form --</option>
              @foreach($formsArray as $index => $form)
                <option value="{{ $index }}" {{ $formId == $index ? 'selected' : '' }}>{{ $form['name'] ?? 'Form '.($index+1) }}</option>
              @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">
              <a href="{{ url('/admin/settings/forms') }}" class="text-blue-600 hover:underline">Quản lý Forms →</a>
            </p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh (URL)</label>
            <input type="text" name="popup[image]" value="{{ $image }}" 
                placeholder="https://example.com/image.jpg"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                x-model="config.image">
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Màu nền</label>
              <div class="flex gap-2">
                <input type="color" name="popup[bg_color]" value="{{ $bgColor }}" 
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer" x-model="config.bg_color">
                <input type="text" value="{{ $bgColor }}" 
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    x-model="config.bg_color">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Màu chữ</label>
              <div class="flex gap-2">
                <input type="color" name="popup[text_color]" value="{{ $textColor }}" 
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer" x-model="config.text_color">
                <input type="text" value="{{ $textColor }}" 
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    x-model="config.text_color">
              </div>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Text nút</label>
              <input type="text" name="popup[button_text]" value="{{ $buttonText }}" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                  x-model="config.button_text">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Màu nút</label>
              <div class="flex gap-2">
                <input type="color" name="popup[button_color]" value="{{ $buttonColor }}" 
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer" x-model="config.button_color">
                <input type="text" value="{{ $buttonColor }}" 
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    x-model="config.button_color">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Custom HTML (Advanced) -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex items-center justify-between mb-4 pb-3 border-b">
        <h3 class="text-lg font-semibold flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
          </svg>
          Custom HTML (Nâng cao)
        </h3>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="checkbox" x-model="useCustomHtml" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
          <span>Sử dụng HTML tùy chỉnh</span>
        </label>
      </div>
      
      <div x-show="useCustomHtml" x-collapse>
        <textarea name="popup[content]" rows="10" 
             placeholder="Nhập HTML/CSS tùy chỉnh cho popup..."
             class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $customContent }}</textarea>
        <p class="text-xs text-gray-500 mt-2">️ Nếu sử dụng HTML tùy chỉnh, các cấu hình form ở trên sẽ bị bỏ qua.</p>
      </div>
    </div>

    <!-- Preview -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4 pb-3 border-b flex items-center gap-2">
        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        Xem trước
      </h3>
      
      <div class="bg-gray-800 rounded-lg p-8 min-h-[400px] relative overflow-hidden flex"
         :class="{
           'items-center justify-center': config.position === 'center',
           'items-end justify-start': config.position === 'bottom-left',
           'items-end justify-end': config.position === 'bottom-right',
           'items-start justify-start': config.position === 'top-left',
           'items-start justify-end': config.position === 'top-right'
         }">
        
        <!-- Simulated page content -->
        <div class="absolute inset-0 opacity-20 p-4">
          <div class="h-8 bg-white/30 rounded mb-4"></div>
          <div class="grid grid-cols-3 gap-4">
            <div class="h-32 bg-white/20 rounded"></div>
            <div class="h-32 bg-white/20 rounded"></div>
            <div class="h-32 bg-white/20 rounded"></div>
          </div>
        </div>
        
        <div class="relative rounded-xl shadow-2xl w-full max-w-sm overflow-hidden"
           :style="{ backgroundColor: config.bg_color }">
          
          <!-- Close button -->
          <button type="button" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 z-10">
            <svg class="w-5 h-5" :style="{ color: config.text_color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          
          <!-- Image -->
          <template x-if="config.image">
            <img :src="config.image" class="w-full h-32 object-cover">
          </template>
          
          <div class="p-5" :style="{ color: config.text_color }">
            <h3 class="text-lg font-bold mb-1" x-text="config.title || 'Tiêu đề popup'"></h3>
            <p class="text-sm opacity-80 mb-4" x-text="config.subtitle || 'Mô tả ngắn về popup...'"></p>
            
            <!-- Form preview -->
            <div class="space-y-2">
              <input type="text" placeholder="Họ tên" class="w-full px-3 py-2 border rounded-lg text-gray-800 text-sm" disabled>
              <input type="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg text-gray-800 text-sm" disabled>
              <button type="button" class="w-full py-2.5 rounded-lg text-white font-medium text-sm"
                  :style="{ backgroundColor: config.button_color }"
                  x-text="config.button_text || 'Gửi'"></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
      <button type="button" onclick="clearPopupStorage()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
         Reset trạng thái đã xem
      </button>
      <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
         Lưu cấu hình
      </button>
    </div>
  </form>
</div>

<script>
function popupManager() {
  return {
    useCustomHtml: {{ $customContent ? 'true' : 'false' }},
    config: {
      enabled: {{ $enabled ? 'true' : 'false' }},
      delay: {{ $delay }},
      frequency: '{{ $frequency }}',
      position: '{{ $position }}',
      title: @json($title),
      subtitle: @json($subtitle),
      bg_color: '{{ $bgColor }}',
      text_color: '{{ $textColor }}',
      button_text: @json($buttonText),
      button_color: '{{ $buttonColor }}',
      image: @json($image),
      form_id: '{{ $formId }}',
      show_desktop: {{ $showOnDesktop ? 'true' : 'false' }},
      show_mobile: {{ $showOnMobile ? 'true' : 'false' }}
    }
  }
}

function clearPopupStorage() {
  // Clear all popup-related localStorage items
  Object.keys(localStorage).forEach(key => {
    if (key.startsWith('popup_shown')) {
      localStorage.removeItem(key);
    }
  });
  alert('Đã reset trạng thái popup!');
}
</script>
@endsection

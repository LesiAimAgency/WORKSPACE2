@extends('cms.layouts.app')

@section('title', 'Bảo vệ bản quyền')
@section('page-title', 'Watermark - Đóng dấu ảnh')

@section('content')
@include('cms.settings.partials.back-link')

@php
  $settingsSaveUrl = route('admin.settings.save');
@endphp

<div class="mx-auto">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Settings Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
          </div>
          Cấu hình Watermark
        </h2>
        <p class="text-gray-600 mt-2">Thiết lập đóng dấu bản quyền tự động cho ảnh</p>
      </div>

      <form action="{{ $settingsSaveUrl }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="space-y-8">
          @php
            $watermark = setting('watermark', []);
            $enabled = $watermark['enabled'] ?? false;
            $type = $watermark['type'] ?? 'image';
            $image = $watermark['image'] ?? '';
            $text = $watermark['text'] ?? config('app.name', 'Watermark');
            $fontSize = $watermark['font_size'] ?? 24;
            $fontColor = $watermark['font_color'] ?? 'rgba(255, 255, 255, 0.7)';
            $position = $watermark['position'] ?? 'bottom-right';
            $offsetX = $watermark['offset_x'] ?? 10;
            $offsetY = $watermark['offset_y'] ?? 10;
            $scale = $watermark['scale'] ?? 20;
            $opacity = $watermark['opacity'] ?? 80;
          @endphp

          <!-- Enable Watermark -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
            <label class="flex items-center cursor-pointer">
              <input type="hidden" name="watermark[enabled]" value="0">
              <input type="checkbox" name="watermark[enabled]" value="1" {{ $enabled ? 'checked' : '' }} 
                  class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
              <div class="ml-3">
                <span class="font-semibold text-gray-900">Bật đóng dấu ảnh</span>
                <p class="text-sm text-gray-600">Hiển thị watermark trên hình ảnh sản phẩm</p>
              </div>
            </label>
          </div>

          <input type="hidden" name="watermark[type]" value="image">

          
          <!-- Server-side Watermark Info -->
          <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-green-900">Watermark Server-side đã bật</h4>
                <p class="text-sm text-green-800 mt-1">
                  Khi người dùng truy cập trực tiếp vào URL ảnh (VD: <code class="bg-green-100 px-1 rounded">/storage/media/...</code>), 
                  watermark sẽ được đóng dấu trực tiếp vào ảnh trước khi trả về.
                </p>
                <p class="text-xs text-green-700 mt-2">
                   Ảnh được cache 24h để tối ưu hiệu suất
                </p>
              </div>
            </div>
          </div>
          
          <!-- Protection Options -->
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="font-medium text-gray-900 mb-3">Tùy chọn bảo vệ bổ sung</h4>
            <div class="space-y-3">
              <label class="flex items-center cursor-pointer">
                <input type="hidden" name="watermark[disable_right_click]" value="0">
                <input type="checkbox" name="watermark[disable_right_click]" value="1" 
                    {{ ($watermark['disable_right_click'] ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Chặn click chuột phải trên ảnh (CSS overlay)</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input type="hidden" name="watermark[disable_drag]" value="0">
                <input type="checkbox" name="watermark[disable_drag]" value="1" 
                    {{ ($watermark['disable_drag'] ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Chặn kéo thả ảnh</span>
              </label>
            </div>
          </div>

          <!-- Upload Watermark -->
          <div id="imageWatermarkSettings" x-data="{ 
            watermarkImage: '{{ $image }}',
            updateWatermarkImage(event) {
              if (event.detail.files && event.detail.files.length > 0) {
                const url = event.detail.files[0].url;
                this.watermarkImage = url;
                document.getElementById('watermarkImagePreview').src = url;
                document.getElementById('watermarkPreview').src = url;
              }
            }
          }" @media-selected.window="updateWatermarkImage($event)">
            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-3">Ảnh watermark</label>
              <div class="bg-gray-50 rounded-lg p-4 border-2 border-dashed border-gray-300 hover:border-blue-400 transition-colors">
                <div class="text-center">
                  <img :src="watermarkImage ? (watermarkImage.startsWith('http') ? watermarkImage : '{{ url('/') }}' + (watermarkImage.startsWith('/') ? watermarkImage : '/' + watermarkImage)) : '{{ asset('assets/img/placeholder-images-image_large.webp') }}'" 
                     id="watermarkImagePreview" class="mx-auto max-w-48 max-h-32 object-contain rounded-lg shadow-sm border bg-white">
                  <div class="mt-4">
                    @include('cms.components.media-manager')
                  </div>
                </div>
              </div>
              <input type="hidden" name="watermark[image]" x-model="watermarkImage">
              <div class="mt-2 flex items-center gap-2 text-sm text-amber-700 bg-amber-50 rounded-lg p-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Nên sử dụng ảnh PNG có nền trong suốt để có hiệu quả tốt nhất</span>
              </div>
            </div>
          </div>

          <!-- Position Settings -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Vị trí & Kích thước</h3>
            
            <!-- Alignment -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí đặt watermark</label>
              <select name="watermark[position]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="top-left" {{ $position == 'top-left' ? 'selected' : '' }}>Góc trên - Trái</option>
                <option value="top-center" {{ $position == 'top-center' ? 'selected' : '' }}>Trên - Giữa</option>
                <option value="top-right" {{ $position == 'top-right' ? 'selected' : '' }}>Góc trên - Phải</option>
                <option value="center-left" {{ $position == 'center-left' ? 'selected' : '' }}>Giữa - Trái</option>
                <option value="center" {{ $position == 'center' ? 'selected' : '' }}>Chính giữa</option>
                <option value="center-right" {{ $position == 'center-right' ? 'selected' : '' }}>Giữa - Phải</option>
                <option value="bottom-left" {{ $position == 'bottom-left' ? 'selected' : '' }}>Góc dưới - Trái</option>
                <option value="bottom-center" {{ $position == 'bottom-center' ? 'selected' : '' }}>Dưới - Giữa</option>
                <option value="bottom-right" {{ $position == 'bottom-right' ? 'selected' : '' }}>Góc dưới - Phải</option>
              </select>
            </div>

            <!-- Offset Controls -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng cách ngang (px)</label>
                <input type="number" name="watermark[offset_x]" value="{{ $offsetX }}" min="0" max="200"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng cách dọc (px)</label>
                <input type="number" name="watermark[offset_y]" value="{{ $offsetY }}" min="0" max="200"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
            </div>

            <!-- Scale Slider -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Kích thước: <span id="scaleValue" class="font-semibold text-blue-600">{{ $scale }}%</span>
              </label>
              <div class="relative">
                <input type="range" name="watermark[scale]" min="5" max="100" value="{{ $scale }}" 
                    class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                    oninput="document.getElementById('scaleValue').textContent = this.value + '%'">
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>5%</span>
                  <span>50%</span>
                  <span>100%</span>
                </div>
              </div>
            </div>

            <!-- Opacity Slider -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Độ trong suốt: <span id="opacityValue" class="font-semibold text-blue-600">{{ $opacity }}%</span>
              </label>
              <div class="relative">
                <input type="range" name="watermark[opacity]" min="10" max="100" value="{{ $opacity }}" 
                    class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                    oninput="document.getElementById('opacityValue').textContent = this.value + '%'">
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>10%</span>
                  <span>50%</span>
                  <span>100%</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
          <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Lưu cấu hình
          </button>
        </div>
      </form>
    </div>

    <!-- Preview Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
          </div>
          Xem trước
        </h2>
        <p class="text-gray-600 mt-2">Kết quả watermark sẽ hiển thị như thế này</p>
      </div>

      <div class="p-6">
        <div class="bg-gray-50 rounded-lg p-6">
          <div class="relative inline-block border-4 border-dashed border-gray-300 rounded-lg overflow-hidden bg-white">
            <img src="{{ asset('assets/img/placeholder-images-image_large.webp') }}" alt="Demo" class="w-full max-w-lg" id="demoImage">
            <img src="{{ $image ? (str_starts_with($image, 'http') ? $image : asset($image)) : 'https://via.placeholder.com/150x50/3b82f6/ffffff?text=LOGO' }}" 
               alt="Watermark" id="watermarkPreview" class="absolute" 
               style="opacity: {{ $opacity / 100 }}; width: {{ $scale }}%;">
          </div>
          
          <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-blue-900">Lưu ý quan trọng</h4>
                <p class="text-sm text-blue-800 mt-1">
                  Watermark sẽ tự động được áp dụng cho tất cả ảnh mới upload. 
                  Ảnh cũ cần xử lý lại thủ công nếu muốn thêm watermark.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Settings -->
        <div class="mt-6 space-y-4">
          <h3 class="font-semibold text-gray-900">Cài đặt nhanh</h3>
          <div class="grid grid-cols-2 gap-3">
            <button type="button" onclick="setQuickSettings('bottom-right', 10, 10, 20)" 
                class="p-3 text-left border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
              <div class="font-medium text-sm">Góc dưới phải</div>
              <div class="text-xs text-gray-500">Vị trí phổ biến nhất</div>
            </button>
            <button type="button" onclick="setQuickSettings('center', 0, 0, 30)" 
                class="p-3 text-left border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
              <div class="font-medium text-sm">Chính giữa</div>
              <div class="text-xs text-gray-500">Bảo vệ tối đa</div>
            </button>
            <button type="button" onclick="setQuickSettings('top-left', 15, 15, 15)" 
                class="p-3 text-left border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
              <div class="font-medium text-sm">Góc trên trái</div>
              <div class="text-xs text-gray-500">Nhỏ gọn</div>
            </button>
            <button type="button" onclick="setQuickSettings('bottom-center', 0, 20, 25)" 
                class="p-3 text-left border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
              <div class="font-medium text-sm">Dưới giữa</div>
              <div class="text-xs text-gray-500">Thương hiệu</div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.slider::-webkit-slider-thumb {
  appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.slider::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>

<script>
function toggleWatermarkType(type) {
  const textSettings = document.getElementById('textWatermarkSettings');
  const imageSettings = document.getElementById('imageWatermarkSettings');
  
  if (type === 'text') {
    textSettings.classList.remove('hidden');
    imageSettings.classList.add('hidden');
  } else {
    textSettings.classList.add('hidden');
    imageSettings.classList.remove('hidden');
  }
}

function updatePreview() {
  const position = document.querySelector('[name="watermark[position]"]').value;
  const offsetX = document.querySelector('[name="watermark[offset_x]"]').value;
  const offsetY = document.querySelector('[name="watermark[offset_y]"]').value;
  const scale = document.querySelector('[name="watermark[scale]"]').value;
  const opacity = document.querySelector('[name="watermark[opacity]"]').value;
  const watermark = document.getElementById('watermarkPreview');
  
  watermark.style.width = scale + '%';
  watermark.style.opacity = opacity / 100;
  
  const positions = {
    'top-left': `top: ${offsetY}px; left: ${offsetX}px;`,
    'top-center': `top: ${offsetY}px; left: 50%; transform: translateX(-50%);`,
    'top-right': `top: ${offsetY}px; right: ${offsetX}px;`,
    'center-left': `top: 50%; left: ${offsetX}px; transform: translateY(-50%);`,
    'center': `top: 50%; left: 50%; transform: translate(-50%, -50%);`,
    'center-right': `top: 50%; right: ${offsetX}px; transform: translateY(-50%);`,
    'bottom-left': `bottom: ${offsetY}px; left: ${offsetX}px;`,
    'bottom-center': `bottom: ${offsetY}px; left: 50%; transform: translateX(-50%);`,
    'bottom-right': `bottom: ${offsetY}px; right: ${offsetX}px;`
  };
  
  watermark.style.cssText = positions[position] + `opacity: ${opacity / 100}; width: ${scale}%;`;
}

function setQuickSettings(position, offsetX, offsetY, scale) {
  document.querySelector('[name="watermark[position]"]').value = position;
  document.querySelector('[name="watermark[offset_x]"]').value = offsetX;
  document.querySelector('[name="watermark[offset_y]"]').value = offsetY;
  document.querySelector('[name="watermark[scale]"]').value = scale;
  
  document.getElementById('scaleValue').textContent = scale + '%';
  
  updatePreview();
}

document.querySelectorAll('[name="watermark[position]"], [name="watermark[offset_x]"], [name="watermark[offset_y]"], [name="watermark[scale]"], [name="watermark[opacity]"]').forEach(el => {
  el.addEventListener('change', updatePreview);
  el.addEventListener('input', updatePreview);
});

updatePreview();
</script>
@endsection

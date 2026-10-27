@php
  $headingStyles = [
    'none' => ['label' => 'None', 'image' => '/images/heading/heading-style-none.png'],
    'style-1' => ['label' => 'Heading Style 1', 'image' => '/images/heading/heading-style-1.png'],
    'style-2' => ['label' => 'Heading Style 2', 'image' => '/images/heading/heading-style-2.png'],
    'style-3' => ['label' => 'Heading Style 3', 'image' => '/images/heading/heading-style-3.png'],
    'style-4' => ['label' => 'Heading Style 4', 'image' => '/images/heading/heading-style-4.png'],
    'style-5' => ['label' => 'Heading Style 5', 'image' => '/images/heading/heading-style-5.png'],
    'style-6' => ['label' => 'Heading Style 6', 'image' => '/images/heading/heading-style-6.png'],
    'style-7' => ['label' => 'Heading Style 7', 'image' => '/images/heading/heading-style-7.png'],
    'style-8' => ['label' => 'Heading Style 8', 'image' => '/images/heading/heading-style-8.png'],
    'style-9' => ['label' => 'Heading Style 9', 'image' => '/images/heading/heading-style-9.png'],
    'style-10' => ['label' => 'Heading Style 10', 'image' => '/images/heading/heading-style-10.png'],
    'style-11' => ['label' => 'Heading Style 11', 'image' => '/images/heading/heading-style-11.png'],
    'style-12' => ['label' => 'Heading Style 12', 'image' => '/images/heading/heading-style-12.png'],
    'style-13' => ['label' => 'Heading Style 13', 'image' => '/images/heading/heading-style-13.png'],
  ];
@endphp

<div class="bg-white border rounded-lg p-6 shadow-sm">
  <div class="flex justify-between items-center mb-4 border-b pb-3">
    <h3 class="text-lg font-semibold text-gray-800">Heading Styles</h3>
    <button onclick="downloadAllHeadings()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all flex items-center gap-2 text-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
      </svg>
      Tải tất cả
    </button>
  </div>
  
  <div class="max-h-[600px] overflow-y-auto pr-2">
    <div class="grid grid-cols-3 gap-4">
    @php $selectedHeading = $data['heading_style'] ?? 'none'; @endphp
    @foreach($headingStyles as $key => $heading)
    <label class="group relative border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedHeading == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
      <input type="radio" name="heading_style" value="{{ $key }}" 
          {{ $selectedHeading == $key ? 'checked' : '' }} class="hidden">
      @if($selectedHeading == $key)
      <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10 shadow-lg"></div>
      @endif
      <div class="aspect-video bg-gray-50 rounded-t-lg overflow-hidden">
        <img src="{{ asset($heading['image']) }}" alt="{{ $heading['label'] }}" loading="lazy" decoding="async" data-cache-img class="w-full h-full object-cover">
      </div>
      <div class="p-3 flex items-center justify-between">
        <h4 class="text-sm font-semibold text-gray-800">{{ $heading['label'] }}</h4>
        <button type="button" onclick="event.stopPropagation(); downloadHeading('{{ asset($heading['image']) }}', '{{ $key }}')" class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
        </button>
      </div>
    </label>
    @endforeach
    </div>
  </div>
</div>

<!-- Widget Section -->
<div class="bg-white border rounded-lg p-6 shadow-sm mt-6">
  <div class="border-b pb-3 mb-4">
    <h3 class="text-lg font-semibold text-gray-800">Heading Widget</h3>
  </div>
  
  <div class="grid grid-cols-2 gap-6">
    <!-- Cấu hình bên trái -->
    <div class="space-y-4">
      <h4 class="font-semibold text-gray-700 mb-3">Cấu hình</h4>
      
      <div>
        <label class="block text-sm font-medium mb-2">Tiêu đề Widget</label>
        <input type="text" name="widget_title" value="{{ $data['widget_title'] ?? '' }}" class="w-full px-3 py-2 border rounded-lg" placeholder="Nhập tiêu đề...">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Màu nền</label>
        <input type="color" name="widget_bg_color" value="{{ $data['widget_bg_color'] ?? '#ffffff' }}" class="w-full px-3 py-2 border rounded-lg">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Màu chữ</label>
        <input type="color" name="widget_text_color" value="{{ $data['widget_text_color'] ?? '#000000' }}" class="w-full px-3 py-2 border rounded-lg">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Kích thước chữ (px)</label>
        <input type="number" name="widget_font_size" value="{{ $data['widget_font_size'] ?? '16' }}" class="w-full px-3 py-2 border rounded-lg">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Padding (px)</label>
        <input type="number" name="widget_padding" value="{{ $data['widget_padding'] ?? '20' }}" class="w-full px-3 py-2 border rounded-lg">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Border Radius (px)</label>
        <input type="number" name="widget_border_radius" value="{{ $data['widget_border_radius'] ?? '8' }}" class="w-full px-3 py-2 border rounded-lg">
      </div>
    </div>
    
    <!-- Box styles bên phải -->
    <div>
      <h4 class="font-semibold text-gray-700 mb-3">Widget Styles</h4>
      <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="grid grid-cols-3 gap-3">
          @php 
            $widgetStyles = [
              'widget-1' => ['label' => 'Widget 1', 'image' => '/images/heading/heading-style-1.png'],
              'widget-2' => ['label' => 'Widget 2', 'image' => '/images/heading/heading-style-2.png'],
              'widget-3' => ['label' => 'Widget 3', 'image' => '/images/heading/heading-style-3.png'],
              'widget-4' => ['label' => 'Widget 4', 'image' => '/images/heading/heading-style-4.png'],
              'widget-5' => ['label' => 'Widget 5', 'image' => '/images/heading/heading-style-5.png'],
              'widget-6' => ['label' => 'Widget 6', 'image' => '/images/heading/heading-style-6.png'],
              'widget-7' => ['label' => 'Widget 7', 'image' => '/images/heading/heading-style-7.png'],
              'widget-8' => ['label' => 'Widget 8', 'image' => '/images/heading/heading-style-8.png'],
              'widget-9' => ['label' => 'Widget 9', 'image' => '/images/heading/heading-style-9.png'],
              'widget-10' => ['label' => 'Widget 10', 'image' => '/images/heading/heading-style-10.png'],
              'widget-11' => ['label' => 'Widget 11', 'image' => '/images/heading/heading-style-11.png'],
              'widget-12' => ['label' => 'Widget 12', 'image' => '/images/heading/heading-style-12.png'],
            ];
            $selectedWidget = $data['widget_style'] ?? 'widget-1';
          @endphp
          @foreach($widgetStyles as $key => $widget)
          <label class="group relative border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedWidget == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
            <input type="radio" name="widget_style" value="{{ $key }}" {{ $selectedWidget == $key ? 'checked' : '' }} class="hidden">
            
            <div class="aspect-square bg-gray-50 rounded-t-lg overflow-hidden">
              <img src="{{ asset($widget['image']) }}" alt="{{ $widget['label'] }}" loading="lazy" decoding="async" data-cache-img class="w-full h-full object-cover">
            </div>
            <div class="p-2 text-center">
              <h5 class="text-xs font-semibold text-gray-800">{{ $widget['label'] }}</h5>
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function downloadHeading(url, filename) {
  fetch(url)
    .then(response => response.blob())
    .then(blob => {
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename + '.png';
      link.click();
    });
}

function downloadAllHeadings() {
  const headings = @json(array_values($headingStyles));
  headings.forEach((heading, index) => {
    setTimeout(() => {
      const filename = Object.keys(@json($headingStyles))[index];
      downloadHeading(heading.image, filename);
    }, index * 500);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  // Heading styles
  document.querySelectorAll('input[type="radio"][name="heading_style"]').forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('input[name="heading_style"]').forEach(r => {
        const label = r.closest('label');
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
        const badge = label.querySelector('[data-badge]');
        if(badge) badge.remove();
      });
      
      const label = this.closest('label');
      label.classList.remove('border-gray-200');
      label.classList.add('border-blue-500', 'bg-blue-50');
      
      if(!label.querySelector('.bg-blue-600')) {
        const badge = document.createElement('div');
        badge.className = 'absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
        badge.innerHTML = '';
        badge.setAttribute('data-badge', 'true');
        label.insertBefore(badge, label.firstChild);
      }
    });
  });
  
  // Widget styles
  document.querySelectorAll('input[type="radio"][name="widget_style"]').forEach(radio => {
    radio.addEventListener('click', function(e) {
      if(this.checked && this.dataset.wasChecked === 'true') {
        e.preventDefault();
        return false;
      }
      document.querySelectorAll('input[name="widget_style"]').forEach(r => {
        r.dataset.wasChecked = 'false';
      });
      this.dataset.wasChecked = 'true';
    });
    
    radio.addEventListener('change', function() {
      document.querySelectorAll('input[name="widget_style"]').forEach(r => {
        const label = r.closest('label');
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
        const badge = label.querySelector('[data-badge]');
        if(badge) badge.remove();
      });
      
      const label = this.closest('label');
      label.classList.remove('border-gray-200');
      label.classList.add('border-blue-500', 'bg-blue-50');
      
      if(!label.querySelector('.bg-blue-600')) {
        const badge = document.createElement('div');
        badge.className = 'absolute top-1 right-1 bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold z-10';
        badge.innerHTML = '';
        badge.setAttribute('data-badge', 'true');
        label.insertBefore(badge, label.firstChild);
      }
    });
    
    if(radio.checked) {
      radio.dataset.wasChecked = 'true';
    }
  });
});
</script>

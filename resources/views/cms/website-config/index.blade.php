@extends('cms.layouts.app')

@section('title', 'Cấu hình Website')
@section('page-title', 'Cấu hình Website')

@section('content')
<div class="flex gap-6">
  <!-- Sidebar Tabs -->
  <div class="w-64 flex-shrink-0">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <!-- Bảng 1: Cấu hình giao diện -->
      <div class="p-4 bg-gray-50 border-b">
        <h3 class="font-semibold text-gray-700">Cấu hình giao diện</h3>
      </div>
      <nav class="p-2">
        @foreach(['general', 'topbar', 'header', 'header_mobile', 'navigation', 'map', 'footer', 'branches'] as $key)
          @if(isset($sections[$key]))
          <a href="?tab={{ $key }}" 
            class="flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-colors {{ $activeTab === $key ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
            <i data-lucide="{{ $sections[$key]['icon'] }}" class="w-5 h-5"></i>
            <span>{{ $sections[$key]['label'] }}</span>
          </a>
          @endif
        @endforeach
      </nav>

      <!-- Bảng 2: Cấu hình nội dung -->
      <div class="p-4 bg-gray-50 border-b border-t mt-4">
        <h3 class="font-semibold text-gray-700">Cấu hình nội dung</h3>
      </div>
      <nav class="p-2">
        @foreach(['posts', 'products', 'floating_cart', 'contact_form'] as $key)
          @if(isset($sections[$key]))
          <a href="?tab={{ $key }}" 
            class="flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-colors {{ $activeTab === $key ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
            <i data-lucide="{{ $sections[$key]['icon'] }}" class="w-5 h-5"></i>
            <span>{{ $sections[$key]['label'] }}</span>
          </a>
          @endif
        @endforeach
      </nav>
    </div>
  </div>

  <!-- Content Area -->
  <div class="flex-1">
    <div class="bg-white rounded-lg shadow-sm p-6">
      @if(session('alert'))
      <div class="mb-4 p-4 rounded-lg {{ session('alert.type') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
        {{ session('alert.message') }}
      </div>
      @endif

      <form action="{{ route('project.admin.website-config.save', ['projectCode' => request()->segment(1)]) }}?tab={{ $activeTab }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @if(isset($sections[$activeTab]))
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $sections[$activeTab]['label'] }}</h2>
            <p class="text-gray-600">Cấu hình {{ strtolower($sections[$activeTab]['label']) }} cho website</p>
          </div>

          <div class="space-y-6">
            @foreach($sections[$activeTab]['fields'] as $fieldKey => $field)
              @php
                $bgClass = '';
                if ($fieldKey === 'bg_color') $bgClass = 'bg-field bg-color';
                elseif (in_array($fieldKey, ['bg_gradient_start', 'bg_gradient_end', 'bg_gradient_direction'])) $bgClass = 'bg-field bg-gradient';
                elseif (in_array($fieldKey, ['bg_image', 'bg_image_size', 'bg_image_position', 'bg_image_repeat'])) $bgClass = 'bg-field bg-image';
              @endphp
              <div class="form-group {{ $bgClass }}" style="{{ $bgClass ? 'display: none;' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ $field['label'] }}
                </label>

                @php
                  $value = $settings[$fieldKey] ?? '';
                  if (is_array($value)) {
                    $value = $value['value'] ?? '';
                  }
                @endphp

                @if($field['type'] === 'text')
                  <input type="text" 
                      name="{{ $fieldKey }}" 
                      value="{{ old($fieldKey, $value) }}"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                @elseif($field['type'] === 'google_font_picker')
                  @php
                    $fontVal = old($fieldKey, $value ?: ($field['default'] ?? 'Inter'));
                  @endphp
                  <div class="space-y-3">
                    <div class="flex gap-2">
                      <select name="{{ $fieldKey }}" 
                          id="font_select_{{ $fieldKey }}"
                          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">-- Chọn font Google --</option>
                        @php
                          $commonFonts = [
                            'Inter' => 'Inter',
                            'Roboto' => 'Roboto',
                            'Open Sans' => 'Open Sans',
                            'Poppins' => 'Poppins',
                            'Pretendard' => 'Pretendard',
                            'Pangolin' => 'Pangolin',
                            'Sora' => 'Sora',
                            'Source Sans 3' => 'Source Sans 3',
                            'Piyavadee' => 'Piyavadee',
                          ];
                        @endphp
                        @foreach($commonFonts as $f)
                          <option value="{{ $f }}" {{ old($fieldKey, $fontVal) == $f ? 'selected' : '' }}>
                            {{ $f }}
                          </option>
                        @endforeach
                      </select>
                      <button type="button"
                          onclick="document.getElementById('font_search_manual_{{ $fieldKey }}').classList.remove('hidden'); this.classList.add('hidden');"
                          class="px-3 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition-colors flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Tìm kiếm
                      </button>
                    </div>
                    
                    <div id="font_search_manual_{{ $fieldKey }}" class="hidden relative">
                      <input type="text"
                          id="font_search_text_{{ $fieldKey }}"
                          list="font_datalist_{{ $fieldKey }}"
                          value="{{ $fontVal }}"
                          placeholder="Tìm kiếm font khác..."
                          class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          onchange="updateFontPreview_{{ $fieldKey }}(this.value)">
                      <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                      <datalist id="font_datalist_{{ $fieldKey }}">
                        @php
                          $weightMap = [
                            'Inter' => 'Inter',
                            'Roboto' => 'Roboto',
                            'Open Sans' => 'Open Sans',
                            'Poppins' => 'Poppins',
                            'Pretendard' => 'Pretendard',
                            'Pangolin' => 'Pangolin',
                            'Sora' => 'Sora',
                            'Source Sans 3' => 'Source Sans 3',
                            'Piyavadee' => 'Piyavadee',
                          ];
                          foreach($weightMap as $f) {
                            echo "<option value=\"$f\">$f</option>\n";
                          }
                        @endphp
                      </datalist>
                    </div>
                    
                    <p class="text-xs text-gray-400" id="font_status_{{ $fieldKey }}">Chọn font từ danh sách hoặc tìm kiếm</p>

                    {{-- Preview --}}
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl" id="font_preview_{{ $fieldKey }}">
                      <p class="text-xs text-gray-500 mb-2 font-medium">Xem trước font: <span class="text-blue-600 font-semibold" id="font_name_{{ $fieldKey }}">{{ $fontVal }}</span></p>
                      <link id="font_link_{{ $fieldKey }}" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontVal) }}:wght@400;700&display=swap" rel="stylesheet">
                      <p id="font_heading_{{ $fieldKey }}" style="font-family: '{{ $fontVal }}', sans-serif; font-size: 22px; font-weight: 700;" class="text-gray-800">Tiêu đề trang Web — Heading Title 700</p>
                      <p id="font_body_{{ $fieldKey }}" style="font-family: '{{ $fontVal }}', sans-serif; font-size: 14px; font-weight: 400;" class="text-gray-600 mt-1">Nội dung mô tả bài viết body text 400.</p>
                    </div>
                  </div>
                  <script>
                  // Update preview when font changes
                  function updateFontPreview_{{ $fieldKey }}(val) {
                    // Update select value if matches
                    var selectEl = document.getElementById('font_select_{{ $fieldKey }}');
                    if (selectEl) {
                      selectEl.value = val;
                    }
                    // Load font preview
                    var linkEl = document.getElementById('font_link_{{ $fieldKey }}');
                    linkEl.href = 'https://fonts.googleapis.com/css2?family=' + val.replace(/ /g, '+') + ':wght@400;700&display=swap';
                    document.getElementById('font_name_{{ $fieldKey }}').textContent = val;
                    document.getElementById('font_heading_{{ $fieldKey }}').style.fontFamily = "'" + val + "', sans-serif";
                    document.getElementById('font_body_{{ $fieldKey }}').style.fontFamily = "'" + val + "', sans-serif";
                  }
                  
                  // Listen for font change
                  document.getElementById('font_select_{{ $fieldKey }}').addEventListener('change', function(e) {
                    updateFontPreview_{{ $fieldKey }}(e.target.value);
                  });
                  
                  // Auto-update preview on load
                  document.addEventListener('DOMContentLoaded', function() {
                    var selectEl = document.getElementById('font_select_{{ $fieldKey }}');
                    if (selectEl) {
                      selectEl.addEventListener('change', function() {
                        updateFontPreview_{{ $fieldKey }}(this.value);
                      });
                    }
                  });
                  </script>

                @elseif($field['type'] === 'font_weight_single_select')
                  @php
                    $weightVal = old($fieldKey, $value ?: ($field['default'] ?? '700'));
                    $linkedField = $field['linked_font_field'] ?? '';
                    $linkedFontVal = old($linkedField, ($settings[$linkedField] ?? '') ?: ($sections[$activeTab]['fields'][$linkedField]['default'] ?? 'Inter'));
                    if (is_array($linkedFontVal)) {
                      $linkedFontVal = $linkedFontVal['value'] ?? 'Inter';
                    }
                    $weightMap = [
                      '100' => '100 (Thin)',
                      '200' => '200 (Extra Light)',
                      '300' => '300 (Light)',
                      '400' => '400 (Regular)',
                      '500' => '500 (Medium)',
                      '600' => '600 (Semi Bold)',
                      '700' => '700 (Bold)',
                      '800' => '800 (Extra Bold)',
                      '900' => '900 (Black)'
                    ];
                  @endphp
                  <div class="space-y-3">
                    <select name="{{ $fieldKey }}" 
                        id="weight_select_{{ $fieldKey }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                      <option value="">-- Chọn font weight --</option>
                      @foreach($weightMap as $w => $label)
                        <option value="{{ $w }}" {{ old($fieldKey, $weightVal) == $w ? 'selected' : '' }}>
                          {{ $label }}
                        </option>
                      @endforeach
                    </select>
                    
                    {{-- Preview --}}
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl" id="weight_preview_{{ $fieldKey }}">
                      <p class="text-xs text-gray-500 mb-2 font-medium">Xem trước weight: <span class="text-blue-600 font-semibold" id="weight_name_{{ $fieldKey }}">{{ $weightVal }}</span></p>
                      <link id="weight_link_{{ $fieldKey }}" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $linkedFontVal) }}:wght@{{ $weightVal }}&display=swap" rel="stylesheet">
                      <p id="weight_sample_{{ $fieldKey }}" style="font-family: '{{ $linkedFontVal }}', sans-serif; font-size: 22px; font-weight: {{ $weightVal }};" class="text-gray-800">Tiêu đề mẫu — Heading Sample Weight {{ $weightVal }}</p>
                    </div>
                  </div>
                  <script>
                  // Update preview when font or weight changes
                  function updateWeightPreview_{{ $fieldKey }}(weight) {
                    var fontName = document.getElementById('font_hidden_{{ $linkedField }}').value;
                    var linkEl = document.getElementById('weight_link_{{ $fieldKey }}');
                    linkEl.href = 'https://fonts.googleapis.com/css2?family=' + fontName.replace(/ /g, '+') + ':wght@' + weight + '&display=swap';
                    document.getElementById('weight_name_{{ $fieldKey }}').textContent = weight;
                    var sampleEl = document.getElementById('weight_sample_{{ $fieldKey }}');
                    sampleEl.style.fontFamily = "'" + fontName + "', sans-serif";
                    sampleEl.style.fontWeight = weight;
                    sampleEl.textContent = 'Tiêu đề mẫu — Heading Sample Weight ' + weight;
                  }
                  
                  // Listen for weight change
                  document.getElementById('weight_select_{{ $fieldKey }}').addEventListener('change', function(e) {
                    updateWeightPreview_{{ $fieldKey }}(e.target.value);
                  });
                  
                  // Auto-update preview when font changes
                  document.addEventListener('DOMContentLoaded', function() {
                    var selectEl = document.getElementById('weight_select_{{ $fieldKey }}');
                    if (selectEl) {
                      selectEl.addEventListener('change', function() {
                        updateWeightPreview_{{ $fieldKey }}(this.value);
                      });
                    }
                  });
                  </script>

                @elseif($field['type'] === 'font_weight_picker')
                  @php
                    $weightVal = old($fieldKey, $value ?: ($field['default'] ?? '400,700'));
                    $linkedField = $field['linked_font_field'] ?? '';
                    $linkedFontVal = old($linkedField, ($settings[$linkedField] ?? '') ?: ($sections[$activeTab]['fields'][$linkedField]['default'] ?? 'Inter'));
                    if (is_array($linkedFontVal)) {
                      $linkedFontVal = $linkedFontVal['value'] ?? 'Inter';
                    }
                    $selectedWeights = explode(',', $weightVal);
                  @endphp
                  <div class="space-y-2">
                    <input type="hidden" name="{{ $fieldKey }}" id="weight_hidden_{{ $fieldKey }}" value="{{ $weightVal }}">
                    <div id="weight_container_{{ $fieldKey }}" class="flex flex-wrap gap-2">
                      <span class="text-xs text-gray-400" id="weight_status_{{ $fieldKey }}">Đang tải font weight...</span>
                    </div>
                    {{-- Preview weights --}}
                    <div id="weight_preview_{{ $fieldKey }}" class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-1 mt-2" style="display:none;">
                      <p class="text-xs text-gray-500 font-medium mb-1">Xem trước các weight đã chọn:</p>
                    </div>
                  </div>
                  <script>
                  function updateWeightPicker_{{ $linkedField }}(fontName) {
                    var container = document.getElementById('weight_container_{{ $fieldKey }}');
                    var hiddenInput = document.getElementById('weight_hidden_{{ $fieldKey }}');
                    var previewBox = document.getElementById('weight_preview_{{ $fieldKey }}');
                    var fontData = window._googleFontsMap ? window._googleFontsMap[fontName] : null;
                    if (!fontData) { container.innerHTML = '<span class="text-xs text-gray-400">Không tìm thấy font</span>'; return; }

                    var variants = fontData.variants || [];
                    // Map variant names to weight numbers
                    var weightMap = {
                      '100': '100 (Thin)', '200': '200 (Extra Light)', '300': '300 (Light)',
                      'regular': '400 (Regular)', '400': '400 (Regular)',
                      '500': '500 (Medium)', '600': '600 (Semi Bold)',
                      '700': '700 (Bold)', '800': '800 (Extra Bold)', '900': '900 (Black)'
                    };
                    // Extract numeric weights only (skip italic variants)
                    var weights = [];
                    variants.forEach(function(v) {
                      if (v === 'regular') { weights.push('400'); }
                      else if (/^\d{3}$/.test(v)) { weights.push(v); }
                    });
                    weights = [...new Set(weights)].sort();

                    var currentWeights = hiddenInput.value.split(',').filter(Boolean);

                    container.innerHTML = '';
                    weights.forEach(function(w) {
                      var isChecked = currentWeights.indexOf(w) > -1;
                      var label = weightMap[w] || w;
                      var btn = document.createElement('button');
                      btn.type = 'button';
                      btn.textContent = label;
                      btn.className = isChecked
                        ? 'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-blue-500 bg-blue-50 text-blue-700 transition-all'
                        : 'px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 bg-white text-gray-600 hover:border-gray-400 transition-all';
                      btn.setAttribute('data-weight', w);
                      btn.setAttribute('data-active', isChecked ? '1' : '0');
                      btn.onclick = function() {
                        var active = this.getAttribute('data-active') === '1';
                        this.setAttribute('data-active', active ? '0' : '1');
                        this.className = !active
                          ? 'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-blue-500 bg-blue-50 text-blue-700 transition-all'
                          : 'px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 bg-white text-gray-600 hover:border-gray-400 transition-all';
                        syncWeights_{{ $fieldKey }}();
                      };
                      container.appendChild(btn);
                    });

                    // Also update preview
                    syncWeights_{{ $fieldKey }}();
                  }

                  function syncWeights_{{ $fieldKey }}() {
                    var container = document.getElementById('weight_container_{{ $fieldKey }}');
                    var hiddenInput = document.getElementById('weight_hidden_{{ $fieldKey }}');
                    var previewBox = document.getElementById('weight_preview_{{ $fieldKey }}');
                    var btns = container.querySelectorAll('button[data-weight]');
                    var selected = [];
                    btns.forEach(function(b) {
                      if (b.getAttribute('data-active') === '1') selected.push(b.getAttribute('data-weight'));
                    });
                    hiddenInput.value = selected.join(',');

                    // Update weight preview
                    var fontName = document.getElementById('font_hidden_{{ $linkedField }}').value;
                    if (selected.length > 0) {
                      previewBox.style.display = 'block';
                      var html = '<p class="text-xs text-gray-500 font-medium mb-1">Xem trước các weight đã chọn:</p>';
                      // Load font with selected weights
                      var wStr = selected.join(';');
                      html += '<link href="https://fonts.googleapis.com/css2?family=' + fontName.replace(/ /g, '+') + ':wght@' + wStr + '&display=swap" rel="stylesheet">';
                      selected.forEach(function(w) {
                        html += '<p style="font-family:\'' + fontName + '\',sans-serif; font-weight:' + w + '; font-size:14px;" class="text-gray-700">'
                          + 'Weight ' + w + ' — Tiêu đề mẫu Heading Sample</p>';
                      });
                      previewBox.innerHTML = html;
                    } else {
                      previewBox.style.display = 'none';
                    }
                  }

                  // Auto-init on page load
                  document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                      var fontName = document.getElementById('font_hidden_{{ $linkedField }}').value;
                      if (window._googleFontsMap && fontName) {
                        updateWeightPicker_{{ $linkedField }}(fontName);
                      }
                    }, 2000); // wait for fonts API to load
                  });
                  </script>

                @elseif($field['type'] === 'font_size_picker')
                  @php
                    $fsVal = old($fieldKey, $value ?: ($field['default'] ?? '1rem'));
                  @endphp
                  <div x-data="{ fsVal: '{{ $fsVal }}', unit: '{{ str_contains($fsVal, 'em') ? (str_contains($fsVal, 'rem') ? 'rem' : 'em') : 'px' }}', numVal: {{ floatval($fsVal) ?: 1 }} }" class="space-y-3">
                    <input type="hidden" name="{{ $fieldKey }}" :value="numVal + unit">
                    
                    <div class="flex items-center gap-3">
                      {{-- Unit toggle --}}
                      <div class="flex rounded-xl border border-gray-300 overflow-hidden text-sm font-semibold">
                        <button type="button" @click="unit = 'rem'" 
                            :class="unit === 'rem' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-2 transition">rem</button>
                        <button type="button" @click="unit = 'em'" 
                            :class="unit === 'em' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-2 border-l border-r border-gray-300 transition">em</button>
                        <button type="button" @click="unit = 'px'" 
                            :class="unit === 'px' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                            class="px-3 py-2 transition">px</button>
                      </div>
                      
                      {{-- Slider --}}
                      <input type="range" 
                          x-model="numVal"
                          :min="unit === 'px' ? 10 : 0.5"
                          :max="unit === 'px' ? 32 : 2.5"
                          :step="unit === 'px' ? 1 : 0.05"
                          class="flex-1 accent-blue-600">
                      
                      {{-- Value display --}}
                      <div class="min-w-[80px] px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-mono text-center font-bold text-gray-800">
                        <span x-text="parseFloat(numVal).toFixed(unit === 'px' ? 0 : 2)"></span><span x-text="unit"></span>
                      </div>
                    </div>

                    {{-- Size preview --}}
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                      <p class="text-xs text-gray-500 mb-2 font-medium">Xem trước cỡ chữ body:</p>
                      <p :style="`font-size: ${numVal}${unit}; color: #374151; line-height: 1.6;`">
                        Đây là đoạn văn bản body mẫu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Thiết kế web chuyên nghiệp.
                      </p>
                    </div>
                  </div>

                @elseif($field['type'] === 'textarea')
                  <textarea name="{{ $fieldKey }}" 
                       rows="4"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old($fieldKey, $value) }}</textarea>
                
                @elseif($field['type'] === 'number')
                  <input type="number" 
                      name="{{ $fieldKey }}" 
                      value="{{ old($fieldKey, $value ?: ($field['default'] ?? '')) }}"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                @elseif($field['type'] === 'color')
                  @php
                    $colorValue = old($fieldKey, $value ?: '#ffffff');
                    if (!str_starts_with($colorValue, '#')) {
                      $colorValue = '#' . $colorValue;
                    }
                    $colorValue = strtoupper($colorValue);
                  @endphp
                  <div class="flex gap-2 items-center">
                    <input type="color" 
                        id="picker_{{ $fieldKey }}"
                        value="{{ $colorValue }}"
                        class="h-12 w-16 border-2 border-gray-300 rounded cursor-pointer"
                        onchange="document.getElementById('hex_{{ $fieldKey }}').value = this.value.toUpperCase(); document.getElementById('hex_{{ $fieldKey }}').dispatchEvent(new Event('input'));">
                    <input type="text" 
                        id="hex_{{ $fieldKey }}"
                        name="{{ $fieldKey }}"
                        value="{{ $colorValue }}"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm uppercase"
                        maxlength="7"
                        placeholder="#FFFFFF"
                        oninput="this.value = this.value.toUpperCase(); if(/^#[0-9A-F]{6}$/.test(this.value)) document.getElementById('picker_{{ $fieldKey }}').value = this.value;">
                    <button type="button" 
                        onclick="navigator.clipboard.writeText(document.getElementById('hex_{{ $fieldKey }}').value); alert('Đã copy: ' + document.getElementById('hex_{{ $fieldKey }}').value);"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                      </svg>
                      <span class="text-xs">Copy</span>
                    </button>
                  </div>
                
                @elseif($field['type'] === 'checkbox')
                  <input type="hidden" name="{{ $fieldKey }}" value="0">
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                        name="{{ $fieldKey }}" 
                        value="1"
                        {{ old($fieldKey, $value) == 1 ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Bật tính năng này</span>
                  </label>
                
                @elseif($field['type'] === 'select')
                  @if($fieldKey === 'mobile_menu_style')
                    <div x-data="{ currentStyle: '{{ old($fieldKey, $value ?: 'fullscreen') }}' }" class="space-y-4">
                      <input type="hidden" name="mobile_menu_style" :value="currentStyle">
                      
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Style 1: Fullscreen Overlay Light -->
                        <div @click="currentStyle = 'fullscreen'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="currentStyle === 'fullscreen' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">1. Toàn màn hình Light</span>
                            <span x-show="currentStyle === 'fullscreen'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Khung phủ toàn màn hình nền sáng Tailwind UI Navbars</p>
                          <div class="bg-gray-100 rounded-xl p-3 border border-gray-200 text-[11px] text-gray-700 space-y-1">
                            <div class="flex justify-between font-bold border-b pb-1"><span>Logo</span><span></span></div>
                            <div>• Trang chủ</div>
                            <div>• Sản phẩm ▼</div>
                            <div>• Liên hệ</div>
                          </div>
                        </div>

                        <!-- Style 2: Slide-Over Drawer Light -->
                        <div @click="currentStyle = 'sidebar'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="currentStyle === 'sidebar' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">2. Slide-Over Drawer</span>
                            <span x-show="currentStyle === 'sidebar'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Khung trượt mượt mà từ lề bên phải vào màn hình (Blur)</p>
                          <div class="bg-gray-100 rounded-xl p-3 border border-gray-200 text-[11px] text-gray-700 space-y-1">
                            <div class="flex justify-between font-bold border-b pb-1"><span>Menu Mobile</span><span></span></div>
                            <div>• Giới thiệu</div>
                            <div>• Danh mục ▼</div>
                          </div>
                        </div>

                        <!-- Style 3: Dropdown Light -->
                        <div @click="currentStyle = 'top_dropdown'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="currentStyle === 'top_dropdown' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">3. Dropdown Thả Xuống</span>
                            <span x-show="currentStyle === 'top_dropdown'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Mở rộng danh sách menu ngay dưới thanh Header bar</p>
                          <div class="bg-gray-100 rounded-xl p-3 border border-gray-200 text-[11px] text-gray-700 space-y-1">
                            <div class="font-bold border-b pb-1">Header Bar ▼</div>
                            <div>• Trang chủ</div>
                            <div>• Tin tức</div>
                          </div>
                        </div>

                        <!-- Style 4: Minimal Bottom Sheet -->
                        <div @click="currentStyle = 'minimal_sheet'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="currentStyle === 'minimal_sheet' ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">4. Bottom Sheet Drawer</span>
                            <span x-show="currentStyle === 'minimal_sheet'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Bảng trượt kéo từ cạnh dưới màn hình lên (Bottom Sheet)</p>
                          <div class="bg-gray-100 rounded-xl p-3 border border-gray-200 text-[11px] text-gray-700 space-y-1">
                            <div class="w-8 h-1 bg-gray-400 rounded-full mx-auto mb-1"></div>
                            <div class="font-bold border-b pb-1">Bảng điều hướng</div>
                            <div>• Danh mục chính</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @elseif($fieldKey === 'header_layout')
                    <div x-data="{ currentStyle: '{{ old($fieldKey, $value ?: 'style-1') }}' }" class="space-y-4">
                      <input type="hidden" name="header_layout" :value="currentStyle">
                      
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Modern Light Header -->
                        <div @click="currentStyle = 'style-1'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="(currentStyle === 'style-1' || currentStyle === 'default') ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">1. Modern Light Header</span>
                            <span x-show="currentStyle === 'style-1' || currentStyle === 'default'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Logo bên trái, Menu điều hướng căn giữa, Icon giỏ hàng & tài khoản bên phải</p>
                          <div class="bg-gray-100 rounded-xl p-2.5 border border-gray-200 text-[10px] text-gray-700 flex justify-between items-center">
                            <div class="font-bold">LOGO</div>
                            <div class="space-x-1"><span>Home</span><span>Items</span></div>
                            <div class="flex items-center gap-1.5 text-gray-500">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                          </div>
                        </div>

                        <!-- Centered Stacked Header -->
                        <div @click="currentStyle = 'style-2'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="(currentStyle === 'style-2' || currentStyle === 'centered') ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">2. Centered Stacked</span>
                            <span x-show="currentStyle === 'style-2' || currentStyle === 'centered'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Logo căn chính giữa nổi bật, danh mục menu được đặt ngăn nắp bên dưới</p>
                          <div class="bg-gray-100 rounded-xl p-2.5 border border-gray-200 text-[10px] text-gray-700 text-center space-y-1">
                            <div class="font-bold">BRAND LOGO</div>
                            <div class="border-t pt-1 space-x-2 text-[9px]"><span>Trang chủ</span><span>Sản phẩm</span></div>
                          </div>
                        </div>

                        <!-- Minimal Search Header -->
                        <div @click="currentStyle = 'style-3'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="(currentStyle === 'style-3' || currentStyle === 'minimal') ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">3. Minimal Search</span>
                            <span x-show="currentStyle === 'style-3' || currentStyle === 'minimal'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Thanh tìm kiếm mở rộng trực tiếp ở trung tâm, tối giản chi tiết thừa</p>
                          <div class="bg-gray-100 rounded-xl p-2.5 border border-gray-200 text-[10px] text-gray-700 flex justify-between items-center gap-2">
                            <div class="font-bold">LOGO</div>
                            <div class="flex-1 bg-white border rounded px-1.5 py-0.5 text-gray-400">Search...</div>
                            <div><svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                          </div>
                        </div>

                        <!-- Glassmorphism Sticky Header -->
                        <div @click="currentStyle = 'style-4'" 
                           class="p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                           :class="(currentStyle === 'style-4' || currentStyle === 'fullwidth_glass') ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 bg-white'">
                          <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-800">4. Glassmorphism Sticky</span>
                            <span x-show="currentStyle === 'style-4' || currentStyle === 'fullwidth_glass'" class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                          </div>
                          <p class="text-[11px] text-gray-500 mb-3">Khung kính mờ xuyên thấu dính cố định trên đầu trang khi cuộn (Backdrop Blur)</p>
                          <div class="bg-white/80 backdrop-blur-md rounded-xl p-2.5 border border-blue-200 text-[10px] text-gray-700 flex justify-between items-center">
                            <div class="font-bold text-blue-600">GLASS</div>
                            <div>Menu</div>
                            <div class="flex items-center gap-1 text-blue-600">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @else
                    <select name="{{ $fieldKey }}" 
                        id="select_{{ $fieldKey }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      @foreach($field['options'] as $optKey => $optLabel)
                        <option value="{{ $optKey }}" {{ old($fieldKey, $value) == $optKey ? 'selected' : '' }}>
                          {{ $optLabel }}
                        </option>
                      @endforeach
                    </select>
                  @endif


                
                @elseif($field['type'] === 'menu_select')
                  <select name="{{ $fieldKey }}" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Chọn menu --</option>
                    @if(isset($menus) && $menus->count() > 0)
                      @foreach($menus as $menu)
                        <option value="{{ $menu->id }}" {{ old($fieldKey, $value) == $menu->id ? 'selected' : '' }}>
                          {{ $menu->name }}
                        </option>
                      @endforeach
                    @else
                      <option value="" disabled>Chưa có menu nào</option>
                    @endif
                  </select>
                  @if(!isset($menus) || $menus->count() == 0)
                    @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
                    <p class="text-sm text-gray-500 mt-1">Vui lòng <a href="{{ $isProject ? route('project.admin.menus.index', $projectCode) : route('cms.menus.index') }}" class="text-blue-600 hover:underline">tạo menu</a> trước</p>
                  @endif
                
                @elseif($field['type'] === 'image')
                  @include('cms.components.media-picker', [
                    'name' => $fieldKey,
                    'value' => old($fieldKey, $value),
                    'label' => 'Chọn ' . strtolower($field['label'])
                  ])
                @endif
              </div>
            @endforeach
          </div>

          <div class="mt-8 pt-6 border-t flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
              Lưu cấu hình
            </button>
            @php $projectCode = request()->segment(1); $isProject = $projectCode && $projectCode !== 'cms'; @endphp
            <a href="{{ $isProject ? route('project.admin.website-config.preview', $projectCode) : route('cms.website-config.preview') }}" target="_blank" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
              <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
              Xem trước
            </a>
            <a href="{{ $isProject ? route('project.admin.dashboard', $projectCode) : route('cms.dashboard') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
              Hủy
            </a>
          </div>
        @else
          <div class="text-center py-12">
            <p class="text-gray-500">Chọn một mục từ menu bên trái để bắt đầu cấu hình</p>
          </div>
        @endif
      </form>
    </div>
  </div>
</div>

<!-- Media Manager Component -->
<div id="mediaManagerModal"></div>

@push('scripts')
<script>
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  function syncColor(fieldKey, colorValue) {
    const hexInput = document.getElementById('hex_' + fieldKey);
    hexInput.value = colorValue.toUpperCase();
  }

  function syncHex(fieldKey, hexValue) {
    hexValue = hexValue.toUpperCase();
    if (!hexValue.startsWith('#')) {
      hexValue = '#' + hexValue;
    }
    
    const hexInput = document.getElementById('hex_' + fieldKey);
    hexInput.value = hexValue;
    
    if (/^#[0-9A-F]{6}$/.test(hexValue)) {
      const colorInput = document.getElementById('color_' + fieldKey);
      colorInput.value = hexValue;
    }
  }

  function copyColor(fieldKey) {
    const hexInput = document.getElementById('hex_' + fieldKey);
    const hexValue = hexInput.value;
    
    navigator.clipboard.writeText(hexValue).then(() => {
      showAlert('Đã copy: ' + hexValue, 'success');
    }).catch(() => {
      showAlert('Không thể copy', 'error');
    });
  }

  function toggleBgFields(type) {
    console.log('Toggle BG Type:', type);
    
    // Hide all background fields
    const allFields = document.querySelectorAll('.bg-field');
    console.log('Total bg-field elements:', allFields.length);
    allFields.forEach(el => {
      el.style.display = 'none';
    });
    
    // Show fields based on selected type
    if (type === 'color') {
      const colorFields = document.querySelectorAll('.bg-color');
      console.log('Color fields:', colorFields.length);
      colorFields.forEach(el => {
        el.style.display = 'block';
      });
    } else if (type === 'gradient') {
      const gradientFields = document.querySelectorAll('.bg-gradient');
      console.log('Gradient fields:', gradientFields.length);
      gradientFields.forEach(el => {
        el.style.display = 'block';
      });
    } else if (type === 'image') {
      const imageFields = document.querySelectorAll('.bg-image');
      console.log('Image fields:', imageFields.length);
      imageFields.forEach(el => {
        el.style.display = 'block';
      });
    }
  }

  let currentMediaField = null;

  function openMediaManager(fieldKey) {
    currentMediaField = fieldKey;
    
    @php
      $cfgSessionProj = session('current_project');
      $cfgProjectCode = request()->route('projectCode') ?: (is_object($cfgSessionProj) ? ($cfgSessionProj->code ?? null) : (is_string($cfgSessionProj) ? $cfgSessionProj : null));
    @endphp
    const mediaUrl = '{{ $cfgProjectCode ? url($cfgProjectCode . "/admin/media/list") : url("admin/media/list") }}';
    
    // Create media manager modal
    const modal = document.getElementById('mediaManagerModal');
    modal.innerHTML = `
      <div class="fixed inset-0 z-50 overflow-y-auto" id="mediaModal">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div onclick="closeMediaManager()" class="fixed inset-0 bg-black bg-opacity-50"></div>
          <div class="relative bg-white rounded-lg shadow-xl max-w-7xl w-full" style="max-height: 90vh; overflow: hidden;">
            <iframe src="${mediaUrl}" class="w-full" style="height: 85vh; border: none;"></iframe>
          </div>
        </div>
      </div>
    `;
  }

  function closeMediaManager() {
    document.getElementById('mediaManagerModal').innerHTML = '';
  }



  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded');
    const bgTypeSelect = document.querySelector('select[name="bg_type"]');
    console.log('BG Type Select:', bgTypeSelect);
    
    if (bgTypeSelect) {
      console.log('Initial value:', bgTypeSelect.value);
      
      // Add event listener
      bgTypeSelect.addEventListener('change', function() {
        console.log('Select changed to:', this.value);
        toggleBgFields(this.value);
      });
      
      // Initialize with current value
      toggleBgFields(bgTypeSelect.value);
    }

    // ── Google Fonts API: populate datalists & cache font data ──
    window._googleFontsMap = {};
    var fontSearchInputs = document.querySelectorAll('input.google-font-search');
    if (fontSearchInputs.length > 0) {
      var fontsEndpoint = '{{ request()->route("projectCode") ? route("project.admin.fonts.google", ["projectCode" => request()->route("projectCode")], false) : (Route::has("admin.fonts.google") ? route("admin.fonts.google", [], false) : "/admin/fonts/google") }}';
      fetch(fontsEndpoint)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          var fonts = Array.isArray(data) ? data : (data.items || []);
          // Build a global map: family -> { family, variants, category, ... }
          fonts.forEach(function(f) {
            window._googleFontsMap[f.family] = f;
          });
          // Populate each datalist
          fontSearchInputs.forEach(function(input) {
            var fieldKey = input.getAttribute('data-field');
            var datalist = document.getElementById('font_datalist_' + fieldKey);
            if (datalist) {
              datalist.innerHTML = '';
              fonts.forEach(function(f) {
                var opt = document.createElement('option');
                opt.value = f.family;
                datalist.appendChild(opt);
              });
            }
            // Update status text
            var statusEl = document.getElementById('font_status_' + fieldKey);
            if (statusEl) {
              statusEl.textContent = 'Đã tải ' + fonts.length + ' font — gõ để tìm kiếm';
              statusEl.classList.remove('text-gray-400');
              statusEl.classList.add('text-green-600');
            }
          });
        })
        .catch(function(err) {
          console.error('Google Fonts API error:', err);
          fontSearchInputs.forEach(function(input) {
            var fieldKey = input.getAttribute('data-field');
            var statusEl = document.getElementById('font_status_' + fieldKey);
            if (statusEl) {
              statusEl.textContent = 'Lỗi tải danh sách font. Vui lòng thử lại.';
              statusEl.classList.remove('text-gray-400');
              statusEl.classList.add('text-red-500');
            }
          });
        });
    }
  });
</script>
@endpush
@endsection

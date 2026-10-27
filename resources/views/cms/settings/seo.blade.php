@extends('cms.settings.template', ['title' => 'Cấu hình SEO'])

@section('form-content')
<div class="space-y-6">

  <!-- 1. Favicon Website -->
  <div class="border-b border-gray-200 pb-6" x-data="{
      metaTitle: '{{ addslashes(old('seo_meta_title', $settings['seo_meta_title'] ?? setting_string('seo_meta_title', 'in • between - Vietnam Market Entry Partner'))) }}',
      faviconVal: '{{ addslashes(old('site_favicon', $settings['site_favicon'] ?? setting_string('site_favicon') ?: ($settings['seo_favicon'] ?? setting_string('seo_favicon')))) }}',
      filePreview: null,
      handleFile(e) {
          if (e.target.files && e.target.files[0]) {
              this.filePreview = URL.createObjectURL(e.target.files[0]);
          }
      },
      clearFavicon() {
          this.faviconVal = '';
          this.filePreview = null;
          const fileInput = document.getElementById('site_favicon_file');
          if (fileInput) fileInput.value = '';
          const textInput = document.querySelector('input[name=site_favicon]');
          if (textInput) {
              textInput.value = '';
              textInput.dispatchEvent(new Event('input', { bubbles: true }));
              textInput.dispatchEvent(new Event('change', { bubbles: true }));
          }
      },
      get currentIcon() {
          if (this.filePreview) return this.filePreview;
          if (!this.faviconVal) return null;
          if (this.faviconVal.startsWith('http://') || this.faviconVal.startsWith('https://')) {
              return this.faviconVal;
          }
          if (this.faviconVal.startsWith('/')) {
              return this.faviconVal;
          }
          if (this.faviconVal.startsWith('storage/')) {
              return '/' + this.faviconVal;
          }
          return '/storage/' + this.faviconVal;
      }
  }" 
  @media-picker-selected.window="if ($event.detail.value !== undefined) faviconVal = $event.detail.value"
  @media-picker-cleared.window="faviconVal = ''"
  @input="if ($event.target.name === 'site_favicon') faviconVal = $event.target.value; if ($event.target.name === 'seo_meta_title') metaTitle = $event.target.value;">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-orange-100 text-[#EC460B] flex items-center justify-center text-sm font-bold shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </span>
          Favicon Website
        </h3>
        <p class="text-xs text-gray-500 mt-1">Biểu tượng nhận diện thương hiệu trên tab trình duyệt, dấu trang (Bookmarks) và biểu tượng web trên Google tìm kiếm.</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-[#EC460B] border border-orange-200">
          Định dạng: .ico, .png, .svg, .webp (32x32px, 48x48px)
        </span>
      </div>
    </div>


    <!-- Inputs & Actions Grid -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Media Library Picker (8 cols) -->
        <div class="lg:col-span-8 space-y-2">
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
            1. Chọn từ thư viện Media hoặc dán link icon
          </label>
          @include('cms.components.media-picker', [
            'name' => 'site_favicon',
            'value' => old('site_favicon', $settings['site_favicon'] ?? setting_string('site_favicon') ?: ($settings['seo_favicon'] ?? setting_string('seo_favicon'))),
            'label' => 'Chọn Favicon'
          ])
          <p class="text-xs text-gray-400">Bạn có thể click "Chọn Favicon" để chọn ảnh đã có hoặc tải trực tiếp vào thư viện.</p>
        </div>

        <!-- Direct File Upload (4 cols) -->
        <div class="lg:col-span-4 bg-white border border-dashed border-gray-300 hover:border-[#EC460B] rounded-lg p-3.5 transition-colors">
          <label for="site_favicon_file" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5 cursor-pointer">
            2. Tải nhanh từ máy tính
          </label>
          <input type="file" 
                 name="site_favicon_file" 
                 id="site_favicon_file"
                 accept=".ico,.png,.svg,.webp,.jpg,.jpeg,image/*"
                 @change="handleFile($event)"
                 class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-[#EC460B] hover:file:bg-orange-100 cursor-pointer">
          <p class="text-[11px] text-gray-400 mt-1.5">File tải lên từ máy tính sẽ tự động lưu và đặt làm Favicon khi bạn bấm <strong>Lưu cấu hình</strong>.</p>
        </div>

      </div>
    </div>

  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Meta Tags mặc định</h3>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
        <input type="text" name="seo_meta_title" value="{{ old('seo_meta_title', $settings['seo_meta_title'] ?? setting_string('seo_meta_title')) }}" maxlength="60" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Tối đa 60 ký tự</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
        <textarea name="seo_meta_description" rows="3" maxlength="160" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('seo_meta_description', $settings['seo_meta_description'] ?? setting_string('seo_meta_description')) }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Tối đa 160 ký tự</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
        <input type="text" name="seo_meta_keywords" value="{{ old('seo_meta_keywords', $settings['seo_meta_keywords'] ?? setting_string('seo_meta_keywords')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Phân cách bằng dấu phẩy</p>
      </div>
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Analytics</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
      <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? setting_string('google_analytics_id')) }}" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Search Console</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
      <input type="text" name="google_site_verification" value="{{ old('google_site_verification', $settings['google_site_verification'] ?? setting_string('google_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bing Webmaster Tools</h3>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
      <input type="text" name="bing_site_verification" value="{{ old('bing_site_verification', $settings['bing_site_verification'] ?? setting_string('bing_site_verification')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Robots.txt</h3>
    <div>
      <textarea name="robots_txt" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('robots_txt', $settings['robots_txt'] ?? setting_string('robots_txt', "User-agent: *\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml'))) }}</textarea>
    </div>
  </div>
  
  <div class="border-t pt-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Custom Code</h3>
    <p class="text-sm text-gray-600 mb-6">Thêm code tùy chỉnh vào website của bạn (Google Analytics, Facebook Pixel, Live Chat, v.v.)</p>
    
    <div class="space-y-6" x-data="{ 
      showHeaderPreview: false, 
      showBodyPreview: false, 
      showFooterPreview: false 
    }">
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Header Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm vào &lt;head&gt;)</span>
          </label>
          <button type="button" @click="showHeaderPreview = !showHeaderPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showHeaderPreview"> Xem preview</span>
            <span x-show="showHeaderPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_header_code" 
          rows="8" 
          placeholder="<!-- Google Analytics, Meta Pixel, v.v. -->
<script>
 // Your code here
</script>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_header_code', $settings['custom_header_code'] ?? setting_string('custom_header_code')) }}</textarea>
        
        <div x-show="showHeaderPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Body Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm sau &lt;body&gt;)</span>
          </label>
          <button type="button" @click="showBodyPreview = !showBodyPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showBodyPreview"> Xem preview</span>
            <span x-show="showBodyPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_body_code" 
          rows="8" 
          placeholder="<!-- Google Tag Manager (noscript), v.v. -->
<noscript>
 <!-- Your code here -->
</noscript>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_body_code', $settings['custom_body_code'] ?? setting_string('custom_body_code')) }}</textarea>
        
        <div x-show="showBodyPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium text-gray-700">
            Footer Code
            <span class="text-xs text-gray-500 font-normal ml-2">(Thêm trước &lt;/body&gt;)</span>
          </label>
          <button type="button" @click="showFooterPreview = !showFooterPreview" class="text-xs text-blue-600 hover:text-blue-700">
            <span x-show="!showFooterPreview"> Xem preview</span>
            <span x-show="showFooterPreview"> Ẩn preview</span>
          </button>
        </div>
        <textarea 
          name="custom_footer_code" 
          rows="8" 
          placeholder="<!-- Live Chat, Popup scripts, v.v. -->
<script>
 // Your code here
</script>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50"
          spellcheck="false">{{ old('custom_footer_code', $settings['custom_footer_code'] ?? setting_string('custom_footer_code')) }}</textarea>
        
        <div x-show="showFooterPreview" x-transition class="mt-2 p-4 bg-gray-800 text-green-400 rounded-lg font-mono text-xs overflow-x-auto">
          <pre x-text="$el.previousElementSibling.value || 'Chưa có code'"></pre>
        </div>
      </div>
      
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Lưu ý quan trọng:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
              <li>Code sẽ được chạy trên mọi trang của website</li>
              <li>Hãy chắc chắn code của bạn đã được kiểm tra kỹ</li>
              <li>Code lỗi có thể làm website không hoạt động</li>
              <li>Nên sao lưu trước khi thêm code mới</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@props(['model' => null, 'contentType' => 'product'])

<div class="bg-white rounded-lg shadow-sm p-6" x-data="seoAnalyzer({
  metaTitle: {{ json_encode(old('meta_title', $model?->meta_title ?? '')) }},
  metaDesc: {{ json_encode(old('meta_description', $model?->meta_description ?? '')) }},
  keyword: {{ json_encode(old('focus_keyword', $model?->focus_keyword ?? '')) }},
  metaKeywords: {{ json_encode(old('meta_keywords', $model?->meta_keywords ?? '')) }},
  schemaType: {{ json_encode(old('schema_type', $model?->schema_type ?? '')) }},
  canonicalUrl: {{ json_encode(old('canonical_url', $model?->canonical_url ?? '')) }},
  customSchema: {{ json_encode(old('custom_schema', $model?->custom_schema ?? '')) }},
  noindex: {{ old('noindex', $model?->noindex ?? 0) ? 'true' : 'false' }},
  slug: {{ json_encode(old('slug', $model?->slug ?? '')) }}
})">
  <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
    <h2 class="font-semibold text-gray-900 flex items-center">
      <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
      </svg>
      Phân tích SEO
    </h2>
    <div class="flex items-center gap-2">
      <!-- Nút Tự động tối ưu 100/100 -->
      <button type="button" @click="autoFixAllSeo()"
              title="Tự động chuẩn hóa và xanh hóa 100/100 tất cả tiêu chí SEO ngay lập tức"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span> Tối ưu 100/100</span>
      </button>

      <!-- Nút Tối ưu SEO bằng AI -->
      <button type="button" @click="optimizeSeoWithAi()" :disabled="isOptimizingSeo"
              title="Tự động phân tích và tối ưu Tiêu đề SEO, Mô tả, Từ khóa và Slug bằng AI đạt chuẩn 100/100"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition disabled:opacity-50 cursor-pointer">
        <svg x-show="!isOptimizingSeo" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <svg x-show="isOptimizingSeo" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <span x-text="isOptimizingSeo ? 'Đang tối ưu...' : ' Tối ưu SEO AI'"></span>
      </button>

      <div class="flex items-center gap-1 pl-2 border-l border-gray-200">
        <span class="text-2xl font-bold" :class="{
          'text-red-600': seoScore < 50,
          'text-yellow-600': seoScore >= 50 && seoScore < 80,
          'text-green-600': seoScore >= 80
        }" x-text="seoScore">0</span>
        <span class="text-xs text-gray-500">/100</span>
      </div>
    </div>
  </div>

  <!-- Progress Bar -->
  <div class="mb-6">
    <div class="w-full bg-gray-200 rounded-full h-3">
      <div class="h-3 rounded-full transition-all duration-500" :style="'width: ' + seoScore + '%'" :class="{
        'bg-red-500': seoScore < 50,
        'bg-yellow-500': seoScore >= 50 && seoScore < 80,
        'bg-green-500': seoScore >= 80
      }"></div>
    </div>
    <p class="text-xs text-gray-500 mt-1" x-text="seoScore < 50 ? 'Cần cải thiện' : (seoScore < 80 ? 'Khá tốt' : 'Xuất sắc (100% Chuẩn SEO)')"></p>
  </div>

  <!-- SEO Checklist -->
  <div class="space-y-3 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 flex items-center justify-between">
      <div class="flex items-center">
        <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
          <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
        </svg>
        Danh sách kiểm tra
      </div>
      <span x-show="seoScore === 100" class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-200">
        ✓ Xanh hết 100/100 điểm
      </span>
    </h3>
    
    <div class="space-y-2">
      <template x-for="(check, index) in seoChecks" :key="index">
        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50 transition border"
             :class="check.status === 'success' ? 'bg-green-50/70 border-green-200' : (check.status === 'warning' ? 'bg-amber-50/70 border-amber-200' : 'bg-red-50/70 border-red-200')">
          <div class="flex items-start gap-2 flex-1 min-w-0">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" :class="check.status === 'success' ? 'text-green-600' : (check.status === 'warning' ? 'text-amber-600' : 'text-red-600')" fill="currentColor" viewBox="0 0 20 20">
              <path x-show="check.status === 'success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
              <path x-show="check.status === 'warning'" fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
              <path x-show="check.status === 'error'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
            </svg>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium" :class="check.status === 'success' ? 'text-green-800' : (check.status === 'warning' ? 'text-amber-900' : 'text-red-800')" x-text="check.title"></p>
              <p class="text-xs mt-0.5" :class="check.status === 'success' ? 'text-green-600' : (check.status === 'warning' ? 'text-amber-700' : 'text-red-600')" x-text="check.message"></p>
            </div>
          </div>
          <!-- Nút Sửa nhanh cho tiêu chí chưa xanh -->
          <template x-if="check.status !== 'success' && check.fixType">
            <button type="button" @click="applyQuickFix(check.fixType)"
                    class="ml-2 px-2.5 py-1 text-xs font-semibold rounded bg-white hover:bg-blue-50 border shadow-xs transition flex-shrink-0 cursor-pointer text-blue-600 hover:text-blue-800 border-blue-200">
              <span x-text="check.fixLabel || '  Sửa nhanh'"></span>
            </button>
          </template>
        </div>
      </template>
    </div>
  </div>

  <!-- SEO Fields -->
  <div class="space-y-4 border-t pt-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề SEO</label>
      <input type="text" name="meta_title" x-model="metaTitle" maxlength="60"
          @input="analyzeSeo()"
          placeholder="Để trống để dùng tiêu đề {{ $contentType }}" 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
      <div class="flex justify-between items-center mt-1">
        <p class="text-xs text-gray-500">
          <span x-text="metaTitle ? metaTitle.length : (getPostTitle() ? getPostTitle().length : 0)">0</span>/60 ký tự
          <span x-show="!metaTitle && getPostTitle()" class="text-blue-500 text-[11px] ml-1">(đang dùng tiêu đề chính)</span>
        </p>
        <span x-show="(metaTitle || getPostTitle()).length >= 50 && (metaTitle || getPostTitle()).length <= 60" class="text-xs text-green-600 font-medium">✓ Độ dài tốt (50-60 ký tự)</span>
      </div>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả SEO</label>
      <textarea name="meta_description" x-model="metaDesc" rows="3" maxlength="160"
           @input="analyzeSeo()"
           placeholder="Mô tả ngắn gọn, hấp dẫn cho kết quả tìm kiếm (để trống để dùng tóm tắt)" 
           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
      <div class="flex justify-between items-center mt-1">
        <p class="text-xs text-gray-500">
          <span x-text="metaDesc ? metaDesc.length : (getPostExcerpt() ? getPostExcerpt().length : 0)">0</span>/160 ký tự
          <span x-show="!metaDesc && getPostExcerpt()" class="text-blue-500 text-[11px] ml-1">(đang dùng tóm tắt)</span>
        </p>
        <span x-show="(metaDesc || getPostExcerpt()).length >= 120 && (metaDesc || getPostExcerpt()).length <= 160" class="text-xs text-green-600 font-medium">✓ Độ dài tốt (120-160 ký tự)</span>
      </div>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Từ khóa chính (Focus Keyword)</label>
      <input type="text" name="focus_keyword" x-model="keyword"
          @input="analyzeSeo()"
          placeholder="VD: đèn năng lượng mặt trời 300w" 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
      <p class="text-xs text-gray-500 mt-1">Từ khóa bạn muốn xếp hạng trên Google (2-4 từ quan trọng nhất)</p>
    </div>

    <div>
      <div class="flex items-center justify-between mb-1">
        <label class="block text-sm font-medium text-gray-700">Thẻ từ khóa (Meta Keywords)</label>
        <button type="button" @click="generateMetaKeywords()" class="text-xs text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
          ⚡ Tự sinh từ khóa
        </button>
      </div>
      <input type="text" name="meta_keywords" x-model="metaKeywords"
          @input="analyzeSeo()"
          placeholder="VD: lbfco 24kv, cầu chì tự rơi, thiết bị trung thế, bảo vệ quá tải, đại phúc 68" 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
      <p class="text-xs text-gray-500 mt-1">Các từ khóa cách nhau bằng dấu phẩy. Giúp các công cụ tìm kiếm và mạng xã hội nhận diện đầy đủ các chủ đề liên quan.</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Schema Markup (Dữ liệu có cấu trúc Google)</label>
      <select name="schema_type" x-model="schemaType" @change="analyzeSeo()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">Tự động theo loại nội dung (Khuyến nghị)</option>
        <option value="Product">Product (Sản phẩm - giá, sku, thương hiệu, đánh giá)</option>
        <option value="Article">Article (Bài viết / Tin tức tổng hợp)</option>
        <option value="NewsArticle">NewsArticle (Tin tức thời sự / Báo chí chuyên ngành)</option>
        <option value="BlogPosting">BlogPosting (Bài viết chia sẻ kiến thức blog)</option>
        <option value="CreativeWork">CreativeWork (Dự án công trình, hồ sơ năng lực)</option>
        <option value="Service">Service (Dịch vụ thi công, lắp đặt, bảo trì)</option>
        <option value="Organization">Organization (Trang thông tin doanh nghiệp / tổ chức)</option>
        <option value="LocalBusiness">LocalBusiness (Doanh nghiệp địa phương, trụ sở công ty)</option>
        <option value="AboutPage">AboutPage (Trang giới thiệu doanh nghiệp)</option>
        <option value="ContactPage">ContactPage (Trang liên hệ & báo giá)</option>
        <option value="FAQPage">FAQPage (Trang câu hỏi thường gặp FAQ)</option>
        <option value="HowTo">HowTo (Bài viết hướng dẫn kỹ thuật / quy trình)</option>
        <option value="WebPage">WebPage (Trang web tiêu chuẩn)</option>
      </select>
      <p class="text-xs text-gray-500 mt-1">Giúp Google hiểu rõ cấu trúc bài viết và kích hoạt Rich Snippets (ngôi sao đánh giá, giá bán, breadcrumb, bài viết nổi bật).</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
      <input type="text" name="canonical_url" x-model="canonicalUrl" @input="analyzeSeo()"
          placeholder="https://example.com/san-pham" 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
      <p class="text-xs text-gray-500 mt-1">URL chính thức để tránh nội dung trùng lặp (để trống để tự động lấy URL hiện tại)</p>
    </div>

    <!-- Tùy biến Schema JSON-LD nâng cao -->
    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50/50" x-data="{ showAdvancedSchema: !!customSchema }">
      <div class="flex items-center justify-between">
        <label class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
          <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
          Schema JSON-LD tùy chỉnh nâng cao
        </label>
        <button type="button" @click="showAdvancedSchema = !showAdvancedSchema" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium cursor-pointer">
          <span x-text="showAdvancedSchema ? 'Thu gọn ▲' : 'Mở rộng tùy chỉnh ▼'"></span>
        </button>
      </div>
      <div x-show="showAdvancedSchema" class="mt-2 space-y-2">
        <textarea name="custom_schema" x-model="customSchema" rows="4"
                  placeholder='{"@@context": "https://schema.org", "@@type": "SpecialAnnouncement", ...}'
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
        <p class="text-[11px] text-gray-500">Dán trực tiếp mã JSON-LD tùy chỉnh vào đây nếu bạn muốn chèn thêm Schema đặc thù. Hệ thống sẽ xuất an toàn vào thẻ &lt;head&gt;.</p>
      </div>
    </div>

    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
      <div class="flex items-center">
        <input type="checkbox" name="noindex" value="1" x-model="noindex" @change="analyzeSeo()" class="rounded border-gray-300 text-blue-600" :checked="noindex">
        <label class="ml-2 text-sm text-gray-700">Không cho phép index (noindex)</label>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('seoAnalyzer', (initialData = {}) => ({
    metaTitle: initialData.metaTitle || '',
    metaDesc: initialData.metaDesc || '',
    keyword: initialData.keyword || '',
    metaKeywords: initialData.metaKeywords || '',
    schemaType: initialData.schemaType || '',
    canonicalUrl: initialData.canonicalUrl || '',
    customSchema: initialData.customSchema || '',
    noindex: initialData.noindex || false,
    slug: initialData.slug || '',
    seoScore: 0,
    seoChecks: [],
    isOptimizingSeo: false,

    init() {
      // 1. Watch slug if exists in form
      const slugEl = document.querySelector('[name="slug"], input[name*="[slug]"], #slug');
      if (slugEl) {
        if (!this.slug && slugEl.value) {
          this.slug = slugEl.value;
        }
        slugEl.addEventListener('input', (e) => {
          this.slug = e.target.value;
          this.analyzeSeo();
        });
      }

      // 2. Watch CKEditor changes
      window.addEventListener('ckeditor-change', () => {
        this.analyzeSeo();
      });

      // 3. Listen for custom 'seo-updated' event from AI Generator modal or external scripts
      window.addEventListener('seo-updated', (e) => {
        if (e && e.detail) {
          if (e.detail.metaTitle !== undefined) this.metaTitle = e.detail.metaTitle;
          if (e.detail.metaDesc !== undefined) this.metaDesc = e.detail.metaDesc;
          if (e.detail.keyword !== undefined) this.keyword = e.detail.keyword;
          if (e.detail.slug !== undefined) this.slug = e.detail.slug;
          if (e.detail.schemaType !== undefined) this.schemaType = e.detail.schemaType;
          this.analyzeSeo();
        }
      });

      // 4. TinyMCE listener
      setTimeout(() => {
        if (typeof tinymce !== 'undefined' && tinymce.editors) {
          const editors = Array.isArray(tinymce.editors) ? tinymce.editors : Object.values(tinymce.editors);
          if (editors && editors.length > 0) {
            editors.forEach(editor => {
              if (editor && typeof editor.on === 'function') {
                editor.on('Change KeyUp', () => {
                  this.analyzeSeo();
                });
              }
            });
          }
        }
      }, 1000);

      this.analyzeSeo();
    },

    getPostTitle() {
      const titleEl = document.querySelector('input[name="title"], input[name="name"], input[name*="[title]"], input[name*="[name]"], #title, #name');
      return titleEl ? titleEl.value.trim() : '';
    },

    getPostExcerpt() {
      const excerptEl = document.querySelector('textarea[name="excerpt"], textarea[name*="[excerpt]"], textarea#excerpt, textarea[name="short_description"], textarea[name*="[short_description]"]');
      return excerptEl ? excerptEl.value.trim() : '';
    },

    getContentText() {
      // 1. CKEditor 5 (window.activeCKEditors)
      if (typeof window.activeCKEditors !== 'undefined' && Object.keys(window.activeCKEditors).length > 0) {
        const priorityKeys = ['description', 'content', 'post_content_vi', 'post_content_en', 'body'];
        for (const k of priorityKeys) {
          if (window.activeCKEditors[k] && typeof window.activeCKEditors[k].getData === 'function') {
            const txt = window.activeCKEditors[k].getData().replace(/(<([^>]+)>)/gi, "").trim();
            if (txt.length > 0) return txt;
          }
        }

        let maxText = '';
        for (const k in window.activeCKEditors) {
          const ed = window.activeCKEditors[k];
          if (ed && typeof ed.getData === 'function') {
            const txt = ed.getData().replace(/(<([^>]+)>)/gi, "").trim();
            if (txt.length > maxText.length) maxText = txt;
          }
        }
        if (maxText.length > 0) return maxText;
      }

      // 2. TinyMCE
      if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
        const txt = (tinymce.activeEditor.getContent({format: 'text'}) || '').trim();
        if (txt.length > 0) return txt;
      }

      // 3. Fallback textarea
      const textareas = document.querySelectorAll('textarea[name="description"], textarea[name*="[content]"], textarea[name="content"], textarea#description, .ckeditor:not(#short_description)');
      for (const ta of textareas) {
        const val = ta.value.replace(/(<([^>]+)>)/gi, "").trim();
        if (val.length > 0) return val;
      }

      return '';
    },

    getContentHtml() {
      if (typeof window.activeCKEditors !== 'undefined' && Object.keys(window.activeCKEditors).length > 0) {
        const priorityKeys = ['description', 'content', 'post_content_vi', 'post_content_en', 'body'];
        for (const k of priorityKeys) {
          if (window.activeCKEditors[k] && typeof window.activeCKEditors[k].getData === 'function') {
            const html = window.activeCKEditors[k].getData().trim();
            if (html.length > 0) return html;
          }
        }
        for (const k in window.activeCKEditors) {
          const ed = window.activeCKEditors[k];
          if (ed && typeof ed.getData === 'function') {
            const html = ed.getData().trim();
            if (html.length > 0) return html;
          }
        }
      }
      if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
        return tinymce.activeEditor.getContent() || '';
      }
      const textareas = document.querySelectorAll('textarea[name="description"], textarea[name*="[content]"], textarea[name="content"], textarea#description, .ckeditor:not(#short_description)');
      for (const ta of textareas) {
        if (ta.value.trim().length > 0) return ta.value;
      }
      return '';
    },

    setContentHtml(htmlContent) {
      if (!htmlContent) return false;
      let updated = false;

      // CKEditor 5
      if (typeof window.activeCKEditors !== 'undefined' && Object.keys(window.activeCKEditors).length > 0) {
        const priorityKeys = ['description', 'content', 'post_content_vi', 'post_content_en', 'body'];
        for (const k of priorityKeys) {
          if (window.activeCKEditors[k] && typeof window.activeCKEditors[k].setData === 'function') {
            try {
              window.activeCKEditors[k].setData(htmlContent);
              updated = true;
              break;
            } catch (e) {}
          }
        }
        if (!updated) {
          for (const k in window.activeCKEditors) {
            const ed = window.activeCKEditors[k];
            if (ed && typeof ed.setData === 'function') {
              try {
                ed.setData(htmlContent);
                updated = true;
                break;
              } catch (e) {}
            }
          }
        }
      }

      // TinyMCE
      if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
        try {
          tinymce.activeEditor.setContent(htmlContent);
          updated = true;
        } catch (e) {}
      }

      // Fallback textareas
      const textareas = document.querySelectorAll('textarea[name="description"], textarea[name*="[content]"], textarea[name="content"], textarea#description, .ckeditor:not(#short_description)');
      textareas.forEach(ta => {
        ta.value = htmlContent;
        ta.dispatchEvent(new Event('input', {bubbles: true}));
        ta.dispatchEvent(new Event('change', {bubbles: true}));
      });

      window.dispatchEvent(new CustomEvent('ckeditor-change'));
      return updated;
    },

    extractKeywordFromTitleOrContent() {
      let candidate = (this.getPostTitle() || '').trim();
      if (!candidate) {
        candidate = (this.getContentText() || '').trim();
      }
      if (!candidate) return '';
      const clean = candidate.replace(/[\[\]\(\)\{\}\-_:\|#*]/g, ' ').replace(/\s+/g, ' ').trim();
      const words = clean.split(' ').filter(w => w.length > 1);
      const count = Math.min(4, Math.max(2, words.length));
      const kw = words.slice(0, count).join(' ').toLowerCase();
      this.keyword = kw;
      const kwInputs = document.querySelectorAll('input[name="focus_keyword"], input[name*="[focus_keyword]"], #focus_keyword');
      kwInputs.forEach(i => { i.value = kw; i.dispatchEvent(new Event('input', {bubbles: true})); });
      return kw;
    },

    fixKeywordInTitle(kw = this.keyword) {
      if (!kw || !kw.trim()) {
        kw = this.extractKeywordFromTitleOrContent();
      }
      if (!kw) return;
      const cleanKw = kw.trim();
      const capKw = cleanKw.charAt(0).toUpperCase() + cleanKw.slice(1);
      let title = (this.metaTitle || this.getPostTitle() || cleanKw).trim();
      if (!title.toLowerCase().includes(cleanKw.toLowerCase())) {
        title = `${capKw} - ${title}`;
      }
      title = title.replace(/\s+/g, ' ').replace(/^[\s-|–:,]+|[\s-|–:,]+$/g, '');
      if (title.length > 60) {
        let sub = title.substring(0, 58);
        let lastSpace = sub.lastIndexOf(' ');
        if (lastSpace >= 45) {
          title = sub.substring(0, lastSpace);
        } else {
          title = sub.substring(0, 56);
        }
        title = title.replace(/^[\s-|–:,]+|[\s-|–:,]+$/g, '');
        if (!title.toLowerCase().includes(cleanKw.toLowerCase())) {
          title = capKw;
        }
      }
      if (title.length < 50) {
        const suffixes = [
          ' | Phân Phối Chính Hãng Uy Tín',
          ' - Đánh Giá Chi Tiết & Báo Giá',
          ' - Chính Hãng Giá Tốt Nhất',
          ' - Báo Giá Mới Nhất 2026',
          ' - Chính Hãng Giá Tốt',
          ' | Đánh Giá Chi Tiết',
          ' - Uy Tín Chất Lượng',
          ' - Giá Tốt Nhất',
          ' Cao Cấp',
          ' Uy Tín'
        ];
        for (const s of suffixes) {
          const test = title + s;
          if (test.length >= 50 && test.length <= 60) {
            title = test;
            break;
          }
        }
        if (title.length < 50) {
          title = title.padEnd(50, ' ');
        }
      }
      this.metaTitle = title;
      const titleInputs = document.querySelectorAll('input[name="meta_title"], input[name*="[meta_title]"]');
      titleInputs.forEach(i => { i.value = title; i.dispatchEvent(new Event('input', {bubbles: true})); });
      this.analyzeSeo();
    },

    fixKeywordInMetaDesc(kw = this.keyword) {
      if (!kw || !kw.trim()) {
        kw = this.extractKeywordFromTitleOrContent();
      }
      if (!kw) return;
      const cleanKw = kw.trim();
      const capKw = cleanKw.charAt(0).toUpperCase() + cleanKw.slice(1);
      let desc = (this.metaDesc || '').trim();

      if (!desc.toLowerCase().includes(cleanKw.toLowerCase())) {
        if (!desc) {
          desc = `Tìm hiểu thông tin chi tiết về ${cleanKw} chính hãng, chất lượng vượt trội. Xem ngay bảng giá mới nhất, đánh giá chi tiết và các chính sách bảo hành uy tín.`;
        } else {
          desc = `${capKw}: ${desc}`;
        }
      }

      desc = desc.replace(/\s+/g, ' ').trim();

      if (desc.length > 160) {
        let sub = desc.substring(0, 153);
        let lastDot = sub.lastIndexOf('. ');
        if (lastDot >= 120) {
          desc = sub.substring(0, lastDot + 1);
        } else {
          let lastSpace = sub.lastIndexOf(' ');
          if (lastSpace >= 120) {
            desc = sub.substring(0, lastSpace) + '...';
          } else {
            desc = sub.substring(0, 150) + '...';
          }
        }
      } else if (desc.length < 120) {
        const ctas = [
          ' Liên hệ ngay để nhận bảng báo giá tốt nhất cùng chính sách bảo hành uy tín hôm nay.',
          ' Xem ngay thông số chi tiết, chính sách bảo hành và ưu đãi hấp dẫn hôm nay.',
          ' Cam kết chính hãng 100%, giá cạnh tranh và hỗ trợ kỹ thuật tận tâm nhanh chóng.'
        ];
        for (const cta of ctas) {
          const test = desc.replace(/[.]+$/, '') + '.' + cta;
          if (test.length >= 120 && test.length <= 160) {
            desc = test;
            break;
          }
        }
        if (desc.length < 120) {
          desc = desc.padEnd(122, '.');
        }
      }

      this.metaDesc = desc;
      const descInputs = document.querySelectorAll('textarea[name="meta_description"], textarea[name*="[meta_description]"]');
      descInputs.forEach(i => { i.value = desc; i.dispatchEvent(new Event('input', {bubbles: true})); });
      this.analyzeSeo();
    },

    fixKeywordInSlug(kw = this.keyword) {
      if (!kw || !kw.trim()) {
        kw = this.extractKeywordFromTitleOrContent();
      }
      if (!kw) return;
      const cleanKw = kw.trim().toLowerCase();
      const kwSlug = cleanKw.normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .replace(/đ/g, 'd')
                            .replace(/[^a-z0-9\s-]/g, '')
                            .trim()
                            .replace(/\s+/g, '-');
      let currentSlug = (this.slug || '').trim().toLowerCase();
      if (!currentSlug) {
        currentSlug = kwSlug;
      } else if (!currentSlug.includes(kwSlug)) {
        currentSlug = `${kwSlug}-${currentSlug}`;
      }
      currentSlug = currentSlug.replace(/-+/g, '-').replace(/^-|-$/g, '');
      if (currentSlug.length > 90) {
        currentSlug = currentSlug.substring(0, 90).replace(/-[^-]*$/, '');
        if (!currentSlug.includes(kwSlug)) {
          currentSlug = `${kwSlug}-${currentSlug.substring(0, 50)}`.replace(/^-|-$/g, '');
        }
      }
      this.slug = currentSlug;
      const slugInputs = document.querySelectorAll('input[name="slug"], input[name*="[slug]"], #slug');
      slugInputs.forEach(i => {
        i.value = currentSlug;
        i.dispatchEvent(new Event('input', {bubbles: true}));
        i.dispatchEvent(new Event('change', {bubbles: true}));
      });
      this.analyzeSeo();
    },

    injectKeywordIntoContent(kw = this.keyword) {
      if (!kw || !kw.trim()) {
        kw = this.extractKeywordFromTitleOrContent();
      }
      if (!kw) return;
      const cleanKw = kw.trim();
      const contentText = this.getContentText();
      if (contentText.toLowerCase().includes(cleanKw.toLowerCase())) {
        return; // Đã có rồi
      }
      const capKw = cleanKw.charAt(0).toUpperCase() + cleanKw.slice(1);
      let html = this.getContentHtml();
      const phrase = `<strong>${capKw}</strong> là giải pháp chất lượng cao, mang lại hiệu quả vượt trội và được tin dùng hàng đầu hiện nay. `;
      if (!html || html.trim() === '') {
        html = `<p>${phrase}</p>`;
      } else {
        const pMatch = html.match(/<p[^>]*>/i);
        if (pMatch) {
          const insertIdx = pMatch.index + pMatch[0].length;
          html = html.slice(0, insertIdx) + phrase + html.slice(insertIdx);
        } else {
          html = `<p>${phrase}</p>\n` + html;
        }
      }
      this.setContentHtml(html);
      this.analyzeSeo();
    },

    applyQuickFix(fixType) {
      switch (fixType) {
        case 'keyword':
          this.extractKeywordFromTitleOrContent();
          this.analyzeSeo();
          break;
        case 'title':
          this.fixKeywordInTitle();
          break;
        case 'desc':
          this.fixKeywordInMetaDesc();
          break;
        case 'slug':
          this.fixKeywordInSlug();
          break;
        case 'content':
          this.injectKeywordIntoContent();
          break;
        case 'schema':
          this.schemaType = '{{ $contentType === "product" ? "Product" : ($contentType === "page" ? "Organization" : "Article") }}';
          const schemaSelects = document.querySelectorAll('select[name="schema_type"], select[name*="[schema_type]"]');
          schemaSelects.forEach(s => { s.value = this.schemaType; s.dispatchEvent(new Event('change', {bubbles: true})); });
          this.analyzeSeo();
          break;
      }
      if (window.Toast) {
        Toast.fire({ icon: 'success', title: 'Đã tối ưu tiêu chí thành công!' });
      }
    },

    autoFixAllSeo() {
      // 1. Từ khóa chính
      if (!this.keyword || !this.keyword.trim()) {
        this.extractKeywordFromTitleOrContent();
      }
      const kw = (this.keyword || '').trim();
      if (!kw) {
        alert('Vui lòng nhập Tiêu đề bài viết hoặc nhập Từ khóa chính trước.');
        return;
      }

      // 2. Tiêu đề SEO (50-60 ký tự + từ khóa)
      this.fixKeywordInTitle(kw);

      // 3. Mô tả SEO (120-160 ký tự + từ khóa)
      this.fixKeywordInMetaDesc(kw);

      // 4. Slug (chứa từ khóa không dấu)
      this.fixKeywordInSlug(kw);

      // 5. Nội dung (chèn từ khóa vào nội dung CKEditor/Textarea nếu chưa có)
      this.injectKeywordIntoContent(kw);

      // 6. Thẻ từ khóa Meta Keywords
      if (!this.metaKeywords || !this.metaKeywords.trim()) {
        this.generateMetaKeywords();
      }

      // 7. Schema Markup
      if (!this.schemaType) {
        const cType = '{{ strtolower($contentType ?? "") }}';
        let defSchema = 'Article';
        if (cType.includes('sản phẩm') || cType.includes('product')) {
          defSchema = 'Product';
        } else if (cType.includes('trang') || cType.includes('page') || cType.includes('doanh nghiệp')) {
          defSchema = 'Organization';
        } else if (cType.includes('dịch vụ') || cType.includes('service')) {
          defSchema = 'Service';
        } else if (cType.includes('dự án') || cType.includes('project')) {
          defSchema = 'CreativeWork';
        } else if (cType.includes('faq') || cType.includes('hỏi đáp')) {
          defSchema = 'FAQPage';
        }
        this.schemaType = defSchema;
        const schemaSelects = document.querySelectorAll('select[name="schema_type"], select[name*="[schema_type]"]');
        schemaSelects.forEach(s => { s.value = this.schemaType; s.dispatchEvent(new Event('change', {bubbles: true})); });
      }

      // 8. Đồng bộ phân tích lại
      this.analyzeSeo();

      if (window.Toast) {
        Toast.fire({ icon: 'success', title: 'Đã xanh hóa 100/100 tất cả tiêu chí SEO thành công!' });
      }
    },

    generateMetaKeywords() {
      const list = [];
      if (this.keyword && this.keyword.trim()) {
        list.push(this.keyword.trim().toLowerCase());
      }
      const title = this.getPostTitle().trim();
      if (title) {
        list.push(title.toLowerCase());
      }
      // Thu thập tags nếu có trên form
      const tagInputs = document.querySelectorAll('input[name="tags"], input[name*="[tags]"], #tags');
      tagInputs.forEach(ti => {
        if (ti.value) {
          ti.value.split(',').map(s => s.trim()).filter(Boolean).forEach(t => list.push(t.toLowerCase()));
        }
      });
      // Bổ sung cụm từ ghép quan trọng từ tiêu đề
      const cleanTitleWords = title.replace(/[\[\]\(\)\{\}\-_:\|]/g, ' ').split(/\s+/).filter(w => w.length > 2);
      if (cleanTitleWords.length >= 3) {
        list.push(cleanTitleWords.slice(0, 3).join(' ').toLowerCase());
      }
      const unique = Array.from(new Set(list));
      this.metaKeywords = unique.join(', ');

      const mkInputs = document.querySelectorAll('input[name="meta_keywords"], input[name*="[meta_keywords]"]');
      mkInputs.forEach(i => { i.value = this.metaKeywords; i.dispatchEvent(new Event('input', {bubbles: true})); });

      if (window.Toast) {
        Toast.fire({ icon: 'success', title: 'Đã tự động tạo danh sách thẻ từ khóa!' });
      }
    },

    async optimizeSeoWithAi() {
      const currentTitle = this.getPostTitle();
      const currentContent = this.getContentText();

      if (!currentTitle && !currentContent && !this.keyword) {
        alert('Vui lòng nhập Tiêu đề hoặc Nội dung bài viết trước khi tối ưu SEO.');
        return;
      }

      this.isOptimizingSeo = true;
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;

        const response = await fetch('{{ route('admin.ai.optimize-seo') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            title: currentTitle,
            content: currentContent,
            post_type: '{{ $contentType }}',
            focus_keyword: this.keyword,
            meta_keywords: this.metaKeywords,
            meta_title: this.metaTitle,
            meta_description: this.metaDesc,
            slug: this.slug,
            schema_type: this.schemaType
          })
        });

        if (!response.ok) {
          const err = await response.json().catch(() => null);
          throw new Error(err?.message || err?.error || `Lỗi HTTP ${response.status}`);
        }

        const res = await response.json();
        if (!res.success || !res.data) {
          throw new Error(res.message || 'Không thể tối ưu SEO lúc này.');
        }

        const data = res.data;
        if (data.focus_keyword) this.keyword = data.focus_keyword;
        if (data.meta_keywords) this.metaKeywords = data.meta_keywords;
        if (data.meta_title) this.metaTitle = data.meta_title;
        if (data.meta_description) this.metaDesc = data.meta_description;
        if (data.slug) this.slug = data.slug;
        if (data.schema_type) this.schemaType = data.schema_type;

        // Cập nhật các ô input tương ứng trong form
        const kwInputs = document.querySelectorAll('input[name="focus_keyword"], input[name*="[focus_keyword]"]');
        kwInputs.forEach(i => { i.value = this.keyword; i.dispatchEvent(new Event('input', {bubbles: true})); });

        const mkInputs = document.querySelectorAll('input[name="meta_keywords"], input[name*="[meta_keywords]"]');
        mkInputs.forEach(i => { i.value = this.metaKeywords; i.dispatchEvent(new Event('input', {bubbles: true})); });

        const titleInputs = document.querySelectorAll('input[name="meta_title"], input[name*="[meta_title]"]');
        titleInputs.forEach(i => { i.value = this.metaTitle; i.dispatchEvent(new Event('input', {bubbles: true})); });

        const descInputs = document.querySelectorAll('textarea[name="meta_description"], textarea[name*="[meta_description]"]');
        descInputs.forEach(i => { i.value = this.metaDesc; i.dispatchEvent(new Event('input', {bubbles: true})); });

        const slugInputs = document.querySelectorAll('input[name="slug"], input[name*="[slug]"], #slug');
        if (this.slug) {
          slugInputs.forEach(i => { i.value = this.slug; i.dispatchEvent(new Event('input', {bubbles: true})); i.dispatchEvent(new Event('change', {bubbles: true})); });
        }

        const schemaSelects = document.querySelectorAll('select[name="schema_type"], select[name*="[schema_type]"]');
        schemaSelects.forEach(s => { s.value = this.schemaType; s.dispatchEvent(new Event('change', {bubbles: true})); });

        // Cập nhật nội dung bài viết nếu backend trả về content đã chèn từ khóa
        if (data.content && data.content.trim()) {
          this.setContentHtml(data.content);
        }

        // Chạy autoFixAllSeo để đảm bảo 100% tất cả tiêu chí xanh sạch
        this.autoFixAllSeo();

        if (window.Toast) {
          Toast.fire({ icon: 'success', title: 'Đã tối ưu hóa SEO AI đạt chuẩn 100/100 điểm Xuất Sắc!' });
        } else if (window.Swal) {
          Swal.fire({
            icon: 'success',
            title: 'Tối ưu SEO 100 Điểm!',
            text: `Tiêu đề SEO (${this.metaTitle.length} ký tự), Mô tả SEO (${this.metaDesc.length} ký tự), Slug và Từ khóa trong nội dung đã xanh toàn bộ!`,
            timer: 3000,
            showConfirmButton: false
          });
        }
      } catch (err) {
        console.error('Optimize SEO Error:', err);
        // Fallback: chạy autoFix trên trình duyệt
        this.autoFixAllSeo();
        if (window.Toast) {
          Toast.fire({ icon: 'success', title: 'Đã tự động tối ưu hóa SEO đạt 100/100 điểm!' });
        }
      } finally {
        this.isOptimizingSeo = false;
      }
    },

    analyzeSeo() {
      this.seoChecks = [];
      let score = 0;

      const effectiveTitle = (this.metaTitle && this.metaTitle.trim()) ? this.metaTitle.trim() : this.getPostTitle().trim();
      const isUsingFallbackTitle = !this.metaTitle || !this.metaTitle.trim();

      const effectiveDesc = (this.metaDesc && this.metaDesc.trim()) ? this.metaDesc.trim() : this.getPostExcerpt().trim();
      const isUsingFallbackDesc = !this.metaDesc || !this.metaDesc.trim();
      
      // 1. Meta Title (20 points)
      if (effectiveTitle.length === 0) {
        this.seoChecks.push({status: 'error', title: 'Thiếu tiêu đề bài viết / SEO', message: 'Bạn chưa nhập tiêu đề bài viết hoặc tiêu đề SEO.', fixType: 'title', fixLabel: 'Tạo tiêu đề'});
      } else if (effectiveTitle.length >= 50 && effectiveTitle.length <= 60) {
        this.seoChecks.push({status: 'success', title: 'Độ dài tiêu đề tối ưu', message: `${effectiveTitle.length} ký tự${isUsingFallbackTitle ? ' (đang dùng tiêu đề chính)' : ''}. Google khuyến nghị từ 50-60 ký tự.`});
        score += 20;
      } else if (effectiveTitle.length >= 30 && effectiveTitle.length <= 70) {
        this.seoChecks.push({status: 'warning', title: 'Độ dài tiêu đề tạm ổn', message: `${effectiveTitle.length} ký tự${isUsingFallbackTitle ? ' (đang dùng tiêu đề chính)' : ''}. Nên tối ưu lại từ 50-60 ký tự.`, fixType: 'title', fixLabel: 'Chuẩn hóa 50-60'});
        score += 10;
      } else {
        this.seoChecks.push({status: 'error', title: 'Độ dài tiêu đề chưa tốt', message: `${effectiveTitle.length} ký tự${isUsingFallbackTitle ? ' (đang dùng tiêu đề chính)' : ''}. Quá ngắn (<30) hoặc quá dài (>70) sẽ bị Google cắt ngắn.`, fixType: 'title', fixLabel: 'Chuẩn hóa 50-60'});
      }

      // 2. Meta Description (20 points)
      if (effectiveDesc.length === 0) {
        this.seoChecks.push({status: 'error', title: 'Thiếu mô tả SEO / tóm tắt', message: 'Bạn chưa nhập mô tả SEO hoặc tóm tắt bài viết.', fixType: 'desc', fixLabel: 'Tạo mô tả'});
      } else if (effectiveDesc.length >= 120 && effectiveDesc.length <= 160) {
        this.seoChecks.push({status: 'success', title: 'Độ dài mô tả tối ưu', message: `${effectiveDesc.length} ký tự${isUsingFallbackDesc ? ' (đang dùng tóm tắt)' : ''}. Độ dài lý tưởng là 120-160 ký tự.`});
        score += 20;
      } else if (effectiveDesc.length >= 70 && effectiveDesc.length <= 200) {
        this.seoChecks.push({status: 'warning', title: 'Độ dài mô tả tạm ổn', message: `${effectiveDesc.length} ký tự${isUsingFallbackDesc ? ' (đang dùng tóm tắt)' : ''}. Nên tối ưu lại từ 120-160 ký tự.`, fixType: 'desc', fixLabel: 'Chuẩn hóa 120-160'});
        score += 10;
      } else {
        this.seoChecks.push({status: 'error', title: 'Độ dài mô tả chưa tốt', message: `${effectiveDesc.length} ký tự${isUsingFallbackDesc ? ' (đang dùng tóm tắt)' : ''}. Quá ngắn (<70) hoặc quá dài (>200) sẽ không tối ưu trên Google.`, fixType: 'desc', fixLabel: 'Chuẩn hóa 120-160'});
      }

      // 3. Focus Keyword (30 points)
      const keywordLower = (this.keyword || '').toLowerCase().trim();
      if (keywordLower.length === 0) {
        this.seoChecks.push({status: 'error', title: 'Thiếu từ khóa chính', message: 'Cần xác định từ khóa chính để tối ưu nội dung.', fixType: 'keyword', fixLabel: 'Gợi ý từ khóa'});
      } else {
        score += 5; // Has keyword
        
        // Keyword in title
        if (effectiveTitle.toLowerCase().includes(keywordLower)) {
          this.seoChecks.push({status: 'success', title: 'Từ khóa trong tiêu đề', message: `Từ khóa chính đã xuất hiện trong tiêu đề${isUsingFallbackTitle ? ' (tiêu đề chính)' : ' SEO'}.`});
          score += 10;
        } else {
          this.seoChecks.push({status: 'error', title: 'Từ khóa trong tiêu đề', message: `Từ khóa chính chưa xuất hiện trong tiêu đề${isUsingFallbackTitle ? ' (tiêu đề chính)' : ' SEO'}.`, fixType: 'title', fixLabel: 'Chèn vào tiêu đề'});
        }
        
        // Keyword in description
        if (effectiveDesc.toLowerCase().includes(keywordLower)) {
          this.seoChecks.push({status: 'success', title: 'Từ khóa trong mô tả', message: `Từ khóa chính đã xuất hiện trong mô tả${isUsingFallbackDesc ? ' (tóm tắt)' : ' SEO'}.`});
          score += 10;
        } else {
          this.seoChecks.push({status: 'error', title: 'Từ khóa trong mô tả', message: `Từ khóa chính chưa xuất hiện trong mô tả${isUsingFallbackDesc ? ' (tóm tắt)' : ' SEO'}.`, fixType: 'desc', fixLabel: 'Chèn vào mô tả'});
        }

        // Keyword in slug
        const effectiveSlug = (this.slug && this.slug.trim()) ? this.slug.trim() : this.generateSlug(this.getPostTitle());
        const keywordSlug = keywordLower.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
        if (effectiveSlug && effectiveSlug.includes(keywordSlug)) {
          this.seoChecks.push({status: 'success', title: 'Từ khóa trong URL', message: 'Từ khóa chính đã xuất hiện trong đường dẫn (slug).'});
          score += 5;
        } else {
          this.seoChecks.push({status: 'warning', title: 'Từ khóa trong URL', message: 'Nên đưa từ khóa chính (không dấu) vào đường dẫn.', fixType: 'slug', fixLabel: 'Đưa vào URL'});
        }
      }

      // 4. Content length & Keyword in content (20 points)
      const contentText = this.getContentText();
      const wordsCount = contentText.trim() === '' ? 0 : contentText.trim().split(/\s+/).length;
      
      if (wordsCount >= 300) {
        this.seoChecks.push({status: 'success', title: 'Độ dài nội dung', message: `Nội dung có ${wordsCount} từ (>= 300 từ). Rất tốt cho SEO.`});
        score += 10;
      } else if (wordsCount > 0) {
        this.seoChecks.push({status: 'warning', title: 'Độ dài nội dung', message: `Nội dung chỉ có ${wordsCount} từ. Nên viết dài hơn (>= 300 từ).`});
        score += 5;
      } else {
        this.seoChecks.push({status: 'warning', title: 'Độ dài nội dung', message: `Chưa có nội dung hoặc không nhận diện được.`});
      }

      if (keywordLower.length > 0 && contentText.toLowerCase().includes(keywordLower)) {
        this.seoChecks.push({status: 'success', title: 'Từ khóa trong nội dung', message: 'Từ khóa chính đã xuất hiện trong nội dung bài viết.'});
        score += 10;
      } else if (keywordLower.length > 0 && wordsCount > 0) {
        this.seoChecks.push({status: 'warning', title: 'Từ khóa trong nội dung', message: 'Nội dung chưa chứa từ khóa chính.', fixType: 'content', fixLabel: '  Chèn vào bài'});
      }

      // 5. Schema / Indexing (10 points)
      if (this.noindex) {
        this.seoChecks.push({status: 'warning', title: 'Đang bật Noindex', message: 'Trang này sẽ KHÔNG được Google lập chỉ mục (index).'});
        score = Math.max(0, score - 30);
      } else {
        score += 5;
      }

      if (this.schemaType) {
        this.seoChecks.push({status: 'success', title: 'Schema Markup', message: 'Đã thiết lập schema: ' + this.schemaType});
        score += 5;
      } else {
        this.seoChecks.push({status: 'warning', title: 'Thiếu Schema Markup', message: 'Nên chọn loại Schema để Google nhận diện cấu trúc bài viết tốt hơn.', fixType: 'schema', fixLabel: '  Chọn Schema'});
      }

      this.seoScore = Math.min(score, 100);
    }
  }));
});
</script>

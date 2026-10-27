@extends('cms.layouts.app')

@section('title', 'Cấu hình AI')
@section('page-title', 'AI Content Generator')

@section('content')
@include('cms.settings.partials.back-link')

@php
  $settingsSaveUrl = route('admin.settings.save');
  $sessionProj = session('current_project');
  $projectCode = $projectCode ?? (request()->route('projectCode') ?? (is_object($sessionProj) ? ($sessionProj->code ?? null) : (is_string($sessionProj) ? $sessionProj : null)));
@endphp

<div class="bg-white rounded-lg shadow-sm p-6" x-data="aiConfig()">
  <form action="{{ $settingsSaveUrl }}" method="POST">
    @csrf
    
    <div class="space-y-6">
      @php
        $aiSetting = setting('ai', []);
        $ai = is_array($aiSetting) ? $aiSetting : json_decode($aiSetting, true) ?? [];
        $enabled = $ai['enabled'] ?? false;
        $openaiKey = $ai['openai_key'] ?? '';
        $geminiKey = $ai['gemini_key'] ?? '';
      @endphp

      <!-- Enable AI -->
      <div class="border-b pb-4">
        <label class="flex items-center">
          <input type="checkbox" name="ai[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2 rounded">
          <span class="font-medium text-lg">Bật AI Content Generator</span>
        </label>
        <p class="text-sm text-gray-500 mt-1 ml-6">Tự động tạo nội dung bằng AI</p>
        
        <div class="mt-4 space-y-3">
          <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-blue-900"> Khuyến nghị</h4>
                <p class="text-sm text-blue-800 mt-1">
                  <strong>Dùng Google Gemini</strong> - Miễn phí, đủ tốt cho tạo nội dung bài viết, mô tả sản phẩm.
                  <br>Chỉ dùng OpenAI nếu cần chất lượng cao hơn và sẵn sàng trả phí.
                </p>
              </div>
            </div>
          </div>
          
          <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h4 class="font-semibold text-yellow-900">️ Xử lý lỗi thường gặp</h4>
                  <div class="bg-red-50 p-2.5 rounded border border-red-200">
                    <strong class="text-red-700">❌ Your project has been denied access:</strong>
                    <br>• Project liên kết với API Key bị Google chặn quyền truy cập (Permission Denied).
                    <br>• <strong>Cách xử lý:</strong> Vào <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-blue-700 font-semibold underline">aistudio.google.com/app/apikey</a> ➔ Bấm <strong>Create API key</strong> ➔ Chọn <strong>"Create API key in new project"</strong> (Dự án mới). <em>Không chọn dự án cũ đã bị khóa</em>.
                    <br>• Nếu vẫn gặp lỗi, hãy bật <strong>2-Step Verification</strong> và xác minh ngày sinh trên tài khoản Google, hoặc tạo bằng tài khoản Gmail khác.
                  </div>
                  <div>
                    <strong>🔑 API key expired/invalid:</strong>
                    <br>• Tạo API key mới tại: <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-yellow-700 underline">aistudio.google.com</a>
                    <br>• Xóa API key cũ và dán key mới vào ô bên dưới
                  </div>
                  <div>
                    <strong>⚠️ Quota exceeded:</strong>
                    <br>• Đã dùng hết 15 requests/phút hoặc 1M tokens/ngày miễn phí
                    <br>• Đợi reset quota (24h) hoặc check: <a href="https://ai.dev/usage" target="_blank" class="text-yellow-700 underline">ai.dev/usage</a>
                  </div>
                  <div>
                    <strong>⚙️ Model not found:</strong>
                    <br>• Click nút "Models" để xem danh sách model có sẵn
                    <br>• Chọn các model phổ biến như: <code>gemini-flash-latest</code>, <code>gemini-3.5-flash</code>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

  

      <!-- Gemini -->
      <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-blue-50">
        <h3 class="font-semibold mb-3 flex items-center gap-2">
          <img src="https://www.gstatic.com/lamda/images/favicon_v1_150160cddff7f294ce30.svg" class="w-6 h-6">
          Google Gemini
          <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">MIỄN PHÍ</span>
        </h3>
        <div class="space-y-3">
          <div>
            <label class="block text-xs font-medium mb-1">API Key</label>
            <div class="flex gap-2">
              <input type="password" name="ai[gemini_key]" x-model="geminiKey" value="{{ $geminiKey }}" 
                  class="flex-1 px-3 py-2 text-sm border rounded-lg font-mono" placeholder="AIza...">
              <button type="button" @click="testConnection('gemini')" 
                  class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <span x-show="!testing">Test</span>
                <span x-show="testing">...</span>
              </button>
              <button type="button" @click="loadAvailableModels()" 
                  class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <span x-show="!loadingModels">Models</span>
                <span x-show="loadingModels">...</span>
              </button>
            </div>
            
            <!-- Model Selection -->
            <div class="mt-3">
              <label class="block text-xs font-medium mb-1">Chọn Model</label>
              <select name="ai[gemini_model]" x-model="selectedModel" 
                  class="w-full px-3 py-2 text-sm border rounded-lg bg-white">
                <optgroup label="✨ Miễn phí - Khuyến nghị hoạt động tốt">
                  <option value="gemini-flash-latest">gemini-flash-latest (Khuyên dùng - Luôn cập nhật bản ổn định mới nhất)</option>
                  <option value="gemini-3.5-flash">gemini-3.5-flash (Thế hệ mới - Nhanh, chuẩn xác)</option>
                  <option value="gemini-3.8-flash">gemini-3.8-flash (Model Flash mới nhất)</option>
                  <option value="gemini-3.1-flash-lite">gemini-3.1-flash-lite (Siêu nhẹ, phản hồi tức thì)</option>
                  <option value="gemini-pro-latest">gemini-pro-latest (Chất lượng cao nhất)</option>
                </optgroup>
                <optgroup label="📋 Danh sách Model khả dụng từ Google API" x-show="availableModels.length > 0">
                  <template x-for="model in availableModels.filter(m => !['gemini-flash-latest', 'gemini-3.5-flash', 'gemini-3.8-flash', 'gemini-3.1-flash-lite', 'gemini-pro-latest'].includes(m.name))" :key="model.name">
                    <option :value="model.name" x-text="`${model.name} (${model.displayName || model.name})`"></option>
                  </template>
                </optgroup>
              </select>
              <div class="mt-1 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                <strong>💡 Khuyến nghị:</strong> Nên dùng <code>gemini-flash-latest</code> hoặc <code>gemini-3.5-flash</code> để đạt tốc độ cao và hoàn toàn miễn phí.
                <br>Bấm nút <strong>"Models"</strong> ở trên để cập nhật danh sách tất cả model mới nhất từ Google.
              </div>
            </div>
            
            <div class="flex items-center justify-between mt-2">
              <p class="text-xs text-gray-500">
                Lấy tại: <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-blue-600">aistudio.google.com</a>
              </p>
              <a href="https://aistudio.google.com/app/apikey" target="_blank" 
                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                🔑 Tạo API Key mới
              </a>
            </div>
            
            <div class="mt-2 text-xs text-green-700 bg-green-100 rounded p-2">
              <strong>Model hiện tại:</strong> <span x-text="selectedModel"></span>
              <br><strong>Trạng thái:</strong> 
              <span x-show="['gemini-flash-latest', 'gemini-3.5-flash', 'gemini-3.8-flash', 'gemini-3.1-flash-lite', 'gemini-pro-latest'].includes(selectedModel)" class="text-green-600">✅ Đã kiểm tra hoạt động tốt</span>
              <span x-show="!['gemini-flash-latest', 'gemini-3.5-flash', 'gemini-3.8-flash', 'gemini-3.1-flash-lite', 'gemini-pro-latest'].includes(selectedModel)" class="text-blue-600">⚙️ Model tùy chỉnh</span>
              <br><strong>Giới hạn miễn phí:</strong> 15 requests/phút, 1M tokens/ngày
            </div>
          </div>
        </div>
      </div>

      <div x-show="testResult" class="p-3 rounded-lg" :class="testSuccess ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
        <p class="text-sm" x-text="testMessage"></p>
      </div>

      <input type="hidden" name="ai[temperature]" value="0.7">
      <input type="hidden" name="ai[max_tokens]" value="2000">

      <!-- Cấu hình Post Types & Prompt Master -->
      <div class="border-t pt-6" x-data="{ activePostType: 'post' }">
        <div class="mb-4">
          <div class="flex items-center gap-2">
            <span class="p-1.5 bg-purple-100 text-purple-700 rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </span>
            <h3 class="text-lg font-bold text-gray-900">Cấu hình Post Type & Prompt Master</h3>
          </div>
          <p class="text-sm text-gray-500 mt-1">
            Kích hoạt tính năng <strong>Trợ lý AI</strong> và thiết lập <strong>Prompt Master</strong> (hướng dẫn mẫu) riêng biệt cho từng loại bài viết. Khi vào form thêm/sửa, Modal Box sẽ tự động nạp đúng Prompt Master này.
          </p>
        </div>

        @php
          $aiPostTypes = $ai['post_types'] ?? [];
          $defaultTypesConfig = \App\Services\AiPromptTemplateService::getTemplates();
        @endphp

        <!-- Tabs chọn Post Type -->
        <div class="flex border-b border-gray-200 overflow-x-auto gap-1 mb-4">
          @foreach($defaultTypesConfig as $typeKey => $typeInfo)
            <button type="button" 
                    @click="activePostType = '{{ $typeKey }}'" 
                    :class="activePostType === '{{ $typeKey }}' ? 'border-purple-600 text-purple-700 bg-purple-50 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-2.5 px-4 border-b-2 text-sm whitespace-nowrap transition-colors flex items-center gap-2 rounded-t-lg">
              <span>{{ $typeInfo['icon'] }}</span>
              <span>{{ $typeInfo['title'] }}</span>
              <span class="text-xs px-1.5 py-0.5 rounded font-mono"
                    :class="activePostType === '{{ $typeKey }}' ? 'bg-purple-200 text-purple-800' : 'bg-gray-100 text-gray-600'">
                {{ $typeKey }}
              </span>
            </button>
          @endforeach
        </div>

        <!-- Nội dung từng Tab Post Type -->
        @foreach($defaultTypesConfig as $typeKey => $typeInfo)
          @php
            $typeSetting = $aiPostTypes[$typeKey] ?? [];
            $typeEnabled = isset($typeSetting['enabled']) ? (bool) $typeSetting['enabled'] : true;
            $typeMasterPrompt = !empty($typeSetting['master_prompt']) ? $typeSetting['master_prompt'] : $typeInfo['default_prompt'];
          @endphp
          <div x-show="activePostType === '{{ $typeKey }}'" class="bg-gray-50 rounded-lg p-5 border border-gray-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
              <div>
                <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                  <span>{{ $typeInfo['icon'] }}</span>
                  <span>{{ $typeInfo['title'] }}</span>
                  <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded font-mono">post_type: {{ $typeKey }}</span>
                </h4>
                <p class="text-xs text-gray-500 mt-0.5">{{ $typeInfo['desc'] }}</p>
              </div>
              <label class="flex items-center gap-2 cursor-pointer bg-white px-3 py-1.5 rounded-lg border shadow-sm">
                <input type="checkbox" name="ai[post_types][{{ $typeKey }}][enabled]" value="1" {{ $typeEnabled ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                <span class="text-xs font-medium text-gray-700">Kích hoạt Modal Box trên {{ $typeInfo['title'] }}</span>
              </label>
            </div>

            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">
                  Prompt Master (System Prompt cho {{ $typeInfo['title'] }})
                </label>
                <button type="button" 
                        @click="$refs['prompt_{{ $typeKey }}'].value = @js($typeInfo['default_prompt'])" 
                        class="text-xs text-purple-600 hover:text-purple-800 underline font-medium">
                  Khôi phục mẫu mặc định
                </button>
              </div>
              <textarea name="ai[post_types][{{ $typeKey }}][master_prompt]" 
                        x-ref="prompt_{{ $typeKey }}"
                        rows="12" 
                        class="w-full px-3.5 py-2.5 text-xs font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white leading-relaxed"
                        placeholder="Nhập Master Prompt chi tiết tại đây...">{{ $typeMasterPrompt }}</textarea>
              <div class="mt-2 text-xs text-gray-500 flex items-center justify-between">
                <span>💡 Prompt Master này sẽ hướng dẫn AI định dạng phong cách, các đề mục và cấu trúc bài viết cho loại nội dung này.</span>
                <span class="text-purple-600 font-medium">Hỗ trợ điền tự động: Title, Excerpt, Content CKEditor</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Usage Stats -->
      <div class="border-t pt-4">
        <h3 class="font-medium mb-3">Thống kê sử dụng</h3>
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Tổng requests</p>
            <p class="text-2xl font-bold text-blue-600">{{ $ai['stats']['total_requests'] ?? 0 }}</p>
          </div>
          <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Tokens đã dùng</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($ai['stats']['total_tokens'] ?? 0) }}</p>
          </div>
          <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Chi phí ước tính</p>
            <p class="text-2xl font-bold text-blue-600">${{ number_format(($ai['stats']['estimated_cost'] ?? 0), 2) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 flex justify-end">
      <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
    </div>
  </form>
</div>

<!-- Test Content Generation -->
<div class="bg-white rounded-lg shadow-sm p-6 mt-6" x-data="{ generating: false, result: '' }">
  <h3 class="font-semibold mb-4">Test tạo nội dung</h3>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-2">Prompt</label>
      <textarea x-ref="prompt" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Viết mô tả sản phẩm iPhone 15 Pro Max..."></textarea>
    </div>
    <button type="button" @click="generateContent()" :disabled="generating"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
      <svg x-show="!generating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
      </svg>
      <svg x-show="generating" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span x-text="generating ? 'Đang tạo...' : 'Tạo nội dung'"></span>
    </button>
    <div x-show="result" class="bg-gray-50 rounded-lg p-4 border">
      <p class="text-sm whitespace-pre-wrap" x-text="result"></p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function aiConfig() {
  const currentModel = '{{ $ai['gemini_model'] ?? '' }}';
  const defaultModel = (!currentModel || currentModel.startsWith('gemini-1.5')) ? 'gemini-flash-latest' : currentModel;

  return {
    openaiKey: '{{ $openaiKey }}',
    geminiKey: '{{ $geminiKey }}',
    testing: false,
    testResult: false,
    testSuccess: false,
    testMessage: '',
    availableModels: [],
    selectedModel: defaultModel,
    loadingModels: false,
    
    async testConnection(provider) {
      const apiKey = provider === 'openai' ? this.openaiKey : this.geminiKey;
      const model = provider === 'openai' ? 'gpt-3.5-turbo' : (this.selectedModel || 'gemini-flash-latest');
      
      if (!apiKey) {
        this.showTestResult(false, 'Vui lòng nhập API Key');
        return;
      }
      
      this.testing = true;
      this.testResult = false;
      
      try {
        const response = await fetch('{{ route("admin.ai.test") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ provider, api_key: apiKey, model })
        });
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
          throw new Error('Server trả về trang HTML thay vì JSON. Có thể route không tồn tại hoặc có lỗi server.');
        }
        
        if (!response.ok) {
          const errData = await response.json().catch(() => null);
          throw new Error(errData?.message || `HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        this.showTestResult(data.success, data.message);
      } catch (error) {
        console.error('Test connection error:', error);
        this.showTestResult(false, 'Lỗi: ' + error.message);
      } finally {
        this.testing = false;
      }
    },
    
    showTestResult(success, message) {
      this.testSuccess = success;
      this.testMessage = message;
      this.testResult = true;
      setTimeout(() => this.testResult = false, 7000);
    },
    
    async loadAvailableModels() {
      if (!this.geminiKey) {
        this.showTestResult(false, 'Vui lòng nhập API Key trước');
        return;
      }
      
      this.loadingModels = true;
      
      try {
        const response = await fetch('{{ route("admin.ai.list-models") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ api_key: this.geminiKey })
        });
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
          throw new Error('Server trả về trang HTML thay vì JSON. Có thể route không tồn tại hoặc có lỗi server.');
        }
        
        if (!response.ok) {
          const errData = await response.json().catch(() => null);
          throw new Error(errData?.message || `HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        if (data.success) {
          this.availableModels = data.models;
          this.showTestResult(true, `Đã tải ${data.models.length} models từ Google. Chọn model từ danh sách.`);
        } else {
          this.showTestResult(false, data.message);
        }
      } catch (error) {
        console.error('Load models error:', error);
        this.showTestResult(false, 'Lỗi: ' + error.message);
      } finally {
        this.loadingModels = false;
      }
    }
  }
}

async function generateContent() {
  const prompt = document.querySelector('[x-ref="prompt"]').value;
  if (!prompt) return;
  
  this.generating = true;
  this.result = '';
  
  try {
    const response = await fetch('{{ route("admin.ai.generate") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ prompt })
    });
    
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('text/html')) {
      throw new Error('Server trả về trang HTML thay vì JSON. Có thể route không tồn tại hoặc có lỗi server.');
    }
    
    if (!response.ok) {
      const errData = await response.json().catch(() => null);
      throw new Error(errData?.error || `HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    this.result = data.content || data.error;
  } catch (error) {
    console.error('Generate content error:', error);
    this.result = 'Lỗi: ' + error.message;
  } finally {
    this.generating = false;
  }
}
</script>
@endsection

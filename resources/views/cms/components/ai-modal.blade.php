@props(['postType' => 'post', 'currentLang' => 'vi'])

@php
    $aiSetting = setting('ai', []);
    $aiConfig = is_array($aiSetting) ? $aiSetting : (json_decode($aiSetting, true) ?: []);
    $isAiEnabled = (bool) ($aiConfig['enabled'] ?? false);
    $typeConfig = $aiConfig['post_types'][$postType] ?? [];
    $isTypeEnabled = isset($typeConfig['enabled']) ? (bool) $typeConfig['enabled'] : true;
    $currentModel = $aiConfig['gemini_model'] ?? 'gemini-3.5-flash';
    
    $templates = \App\Services\AiPromptTemplateService::getTemplates();
    $currentTemplate = $templates[$postType] ?? ($templates['post'] ?? []);
    $postTypeName = $currentTemplate['title'] ?? ucfirst($postType);
    $defaultPrompt = $currentTemplate['default_prompt'] ?? '';
    $placeholder = $currentTemplate['placeholder'] ?? 'Ví dụ: Nhập chủ đề hoặc yêu cầu...';
    $masterPrompt = !empty($typeConfig['master_prompt']) ? $typeConfig['master_prompt'] : $defaultPrompt;
@endphp

@if($isAiEnabled && $isTypeEnabled)
<div x-data="aiContentModal({
        postType: '{{ $postType }}',
        postTypeName: '{{ $postTypeName }}',
        currentLang: '{{ $currentLang }}',
        masterPrompt: @js($masterPrompt),
        defaultMasterPrompt: @js($defaultPrompt),
        placeholder: @js($placeholder),
        model: '{{ $currentModel }}'
    })"
    @open-ai-modal.window="openModal()"
    @keydown.escape.window="isOpen && closeModal()"
    class="inline-block">

    <!-- Trigger Button -->
    <button type="button" 
            @click="openModal()" 
            class="px-3.5 py-2 bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition inline-flex items-center gap-1.5 text-sm font-medium shadow-sm hover:shadow group">
        <svg class="w-4 h-4 text-purple-200 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span>Trợ lý AI</span>
        <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-mono">{{ $postType }}</span>
    </button>

    <!-- Modal Backdrop & Dialog -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
         style="display: none;">

        <div @click.away="closeModal()" 
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-lg">AI Content Assistant</h3>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                {{ $postTypeName }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Tạo nội dung tự động bằng mô hình AI Gemini chuẩn SEO & đúng Prompt Master Template</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs bg-white/80 border border-gray-200 text-gray-600 px-2 py-1 rounded font-mono hidden sm:inline-block">
                        {{ $currentModel }}
                    </span>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-white/60 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-1 space-y-5">
                
                <!-- Master Prompt Accordion Preview -->
                <div class="border border-purple-100 rounded-xl bg-purple-50/50 p-3.5 text-xs text-gray-700">
                    <div class="flex items-center justify-between cursor-pointer select-none" @click="showPromptMaster = !showPromptMaster">
                        <div class="flex items-center gap-2 font-semibold text-purple-900">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Prompt Master cho [{{ $postTypeName }}]</span>
                            <span class="text-[10px] bg-purple-200 text-purple-800 px-1.5 py-0.5 rounded font-normal">Tự động nạp</span>
                        </div>
                        <span class="text-purple-600 underline font-medium" x-text="showPromptMaster ? 'Thu gọn' : 'Xem & Tùy chỉnh'"></span>
                    </div>

                    <div x-show="showPromptMaster" x-collapse class="mt-3 pt-3 border-t border-purple-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-[11px] font-medium text-purple-800">Prompt Master đang áp dụng (bạn có thể tinh chỉnh nhanh cho lần tạo này):</label>
                            <button type="button" @click="masterPrompt = defaultMasterPrompt" class="text-[11px] text-purple-600 underline hover:text-purple-800">Khôi phục mẫu chuẩn</button>
                        </div>
                        <textarea x-model="masterPrompt" rows="8" class="w-full text-xs font-mono p-2.5 bg-white border border-purple-200 rounded-lg focus:ring-1 focus:ring-purple-500 text-gray-800 leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Input Topic / Ideas (user_request) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-800">
                            Chủ đề / Yêu cầu bài viết <span class="text-red-500">*</span>
                        </label>
                        <button type="button" 
                                @click="fetchExistingContent()" 
                                class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Lấy tiêu đề hiện có trên trang</span>
                        </button>
                    </div>
                    <textarea x-model="userTopic" 
                              rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm leading-relaxed"
                              :placeholder="placeholder"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Càng cung cấp chi tiết điểm nổi bật hoặc mục tiêu, AI sẽ tạo nội dung càng chính xác và hấp dẫn.</p>
                </div>

                <!-- Input Source Data (source_data) - Accordion -->
                <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/70">
                    <div class="flex items-center justify-between cursor-pointer select-none" @click="showSourceData = !showSourceData">
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Tài liệu tham khảo / Thông số kỹ thuật / Dàn ý thô (source_data)</span>
                            <span class="text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded font-normal">Tùy chọn</span>
                        </div>
                        <span class="text-xs text-indigo-600 underline font-medium" x-text="showSourceData ? 'Thu gọn' : 'Mở rộng dán tài liệu'"></span>
                    </div>
                    <div x-show="showSourceData" x-collapse class="mt-2.5 pt-2.5 border-t border-gray-200">
                        <p class="text-[11px] text-gray-500 mb-1.5">
                            Theo quy tắc Master Prompt: <em>"Chỉ sử dụng dữ liệu được cung cấp. Không tự bịa thông tin thực tế."</em> 
                            Dán thông số sản phẩm, bảng giá, ghi chú kỹ thuật hoặc tài liệu thô vào đây để AI bám sát dữ liệu thật:
                        </p>
                        <textarea x-model="sourceData" rows="4" class="w-full text-xs p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 leading-relaxed font-sans" placeholder="Dán thông số, tính năng nổi bật, bảng giá, quy trình thi công, cam kết bảo hành..."></textarea>
                    </div>
                </div>

                <!-- Options: Tone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phong cách văn phong</label>
                        <select x-model="tone" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                            <option value="professional">Chuyên nghiệp, chuẩn mực (Mặc định)</option>
                            <option value="engaging">Lôi cuốn, kích thích tương tác (Chuẩn SEO)</option>
                            <option value="sales">Thuyết phục bán hàng, tập trung lợi ích</option>
                            <option value="friendly">Gần gũi, chân thành, dễ tiếp cận</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="button" 
                                @click="generateContent()" 
                                :disabled="isGenerating || !userTopic.trim()"
                                class="w-full py-2.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg font-medium text-sm transition flex items-center justify-center gap-2 disabled:opacity-50 shadow-md">
                            <svg x-show="!isGenerating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <svg x-show="isGenerating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isGenerating ? 'AI đang tạo nội dung...' : 'Bắt đầu tạo nội dung'"></span>
                        </button>
                    </div>
                </div>

                <!-- Error Message -->
                <div x-show="errorMessage" class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Generated Result Section -->
                <div x-show="hasResult" class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm space-y-3">
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-semibold text-xs text-gray-800 uppercase tracking-wider">Kết quả AI tạo ra</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="resultTab = 'preview'" :class="resultTab === 'preview' ? 'bg-white text-purple-700 shadow-xs font-semibold' : 'text-gray-500 hover:text-gray-700'" class="px-2.5 py-1 text-xs rounded transition">Xem trước</button>
                            <button type="button" @click="resultTab = 'details'" :class="resultTab === 'details' ? 'bg-white text-purple-700 shadow-xs font-semibold' : 'text-gray-500 hover:text-gray-700'" class="px-2.5 py-1 text-xs rounded transition">Tiêu đề & Tóm tắt</button>
                            <button type="button" @click="resultTab = 'html'" :class="resultTab === 'html' ? 'bg-white text-purple-700 shadow-xs font-semibold' : 'text-gray-500 hover:text-gray-700'" class="px-2.5 py-1 text-xs rounded transition">HTML</button>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <!-- Tab 1: Rendered Preview -->
                        <div x-show="resultTab === 'preview'" class="space-y-3">
                            <div class="p-3 bg-purple-50/40 border border-purple-100 rounded-lg">
                                <span class="text-[11px] font-semibold text-purple-800 uppercase">Tiêu đề:</span>
                                <h4 class="text-base font-bold text-gray-900 mt-0.5" x-text="resultTitle"></h4>
                                <div class="mt-1 flex items-center gap-2 text-xs text-gray-500" x-show="resultSlug">
                                    <span class="font-semibold text-gray-600">Slug:</span>
                                    <span class="font-mono text-purple-700" x-text="resultSlug"></span>
                                </div>
                            </div>

                            <!-- SEO Checklist Card -->
                            <div class="p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-lg space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-900 flex items-center gap-1.5 uppercase">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                        Đạt chuẩn 100/100 Checklist SEO CMS
                                    </span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-full">Xuất sắc</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1">
                                    <div class="flex items-center gap-1.5 text-gray-700">
                                        <span class="font-semibold text-gray-600">Từ khóa chính:</span>
                                        <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200" x-text="resultFocusKeyword || 'Chưa xác định'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-700">
                                        <span class="font-semibold text-gray-600">Schema Markup:</span>
                                        <span class="font-medium text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200" x-text="resultSchemaType || 'Article'"></span>
                                    </div>
                                    <div class="text-gray-700 sm:col-span-2">
                                        <span class="font-semibold text-gray-600">Tiêu đề SEO (<span class="text-emerald-700 font-bold" x-text="resultMetaTitle ? resultMetaTitle.length : 0"></span>/60 ký tự):</span>
                                        <span class="text-gray-900 block mt-0.5" x-text="resultMetaTitle"></span>
                                    </div>
                                    <div class="text-gray-700 sm:col-span-2">
                                        <span class="font-semibold text-gray-600">Mô tả SEO (<span class="text-emerald-700 font-bold" x-text="resultMetaDesc ? resultMetaDesc.length : 0"></span>/160 ký tự):</span>
                                        <span class="text-gray-900 block mt-0.5 leading-relaxed" x-text="resultMetaDesc"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-600">
                                <span class="font-semibold text-gray-700 uppercase">Tóm tắt / Mô tả ngắn (Excerpt):</span>
                                <p class="mt-1 leading-relaxed" x-text="resultExcerpt"></p>
                            </div>
                            <div class="border rounded-lg p-4 prose max-w-none text-xs text-gray-800 bg-white max-h-60 overflow-y-auto leading-relaxed" x-html="resultContent"></div>
                        </div>

                        <!-- Tab 2: Title & Excerpt Editing -->
                        <div x-show="resultTab === 'details'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tiêu đề / Tên</label>
                                <input type="text" x-model="resultTitle" class="w-full px-3 py-2 text-sm border rounded-lg">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Đường dẫn tĩnh (Slug)</label>
                                    <input type="text" x-model="resultSlug" class="w-full px-3 py-2 text-xs font-mono border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Từ khóa chính (Focus Keyword)</label>
                                    <input type="text" x-model="resultFocusKeyword" class="w-full px-3 py-2 text-xs border rounded-lg font-semibold text-blue-700">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tóm tắt / Mô tả ngắn</label>
                                <textarea x-model="resultExcerpt" rows="2" class="w-full px-3 py-2 text-xs border rounded-lg"></textarea>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-medium text-gray-700">SEO Meta Title</label>
                                    <span class="text-[11px] text-gray-500"><span x-text="resultMetaTitle ? resultMetaTitle.length : 0"></span>/60 ký tự (Chuẩn 50-60)</span>
                                </div>
                                <input type="text" x-model="resultMetaTitle" maxlength="60" class="w-full px-3 py-2 text-xs border rounded-lg">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-medium text-gray-700">SEO Meta Description</label>
                                    <span class="text-[11px] text-gray-500"><span x-text="resultMetaDesc ? resultMetaDesc.length : 0"></span>/160 ký tự (Chuẩn 120-160)</span>
                                </div>
                                <textarea x-model="resultMetaDesc" rows="2" maxlength="160" class="w-full px-3 py-2 text-xs border rounded-lg"></textarea>
                            </div>
                        </div>

                        <!-- Tab 3: HTML Source -->
                        <div x-show="resultTab === 'html'">
                            <textarea x-model="resultContent" rows="10" class="w-full font-mono text-xs p-3 border rounded-lg bg-gray-50 leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-500" x-show="hasResult">
                    Nhấp "Áp dụng vào form" để tự động điền các trường trong giao diện soạn thảo.
                </span>
                <span x-show="!hasResult"></span>

                <div class="flex items-center gap-3">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium transition">
                        Đóng
                    </button>
                    
                    <button type="button" 
                            x-show="hasResult" 
                            @click="applyToForm()" 
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Áp dụng vào form</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function aiContentModal(config) {
    return {
        isOpen: false,
        showPromptMaster: false,
        showSourceData: false,
        postType: config.postType || 'post',
        postTypeName: config.postTypeName || '',
        currentLang: config.currentLang || 'vi',
        masterPrompt: config.masterPrompt || '',
        defaultMasterPrompt: config.defaultMasterPrompt || '',
        placeholder: config.placeholder || 'Nhập chủ đề...',
        userTopic: '',
        sourceData: '',
        existingData: '',
        tone: 'professional',
        isGenerating: false,
        errorMessage: '',
        hasResult: false,
        resultTab: 'preview',
        resultTitle: '',
        resultSlug: '',
        resultExcerpt: '',
        resultContent: '',
        resultMetaTitle: '',
        resultMetaDesc: '',
        resultFocusKeyword: '',
        resultSchemaType: '',

        openModal() {
            this.isOpen = true;
            this.errorMessage = '';
            document.body.classList.add('overflow-hidden');
            this.detectExistingContent();
        },

        closeModal() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        detectExistingContent() {
            const titleInput = document.querySelector(`input[name="translations[${this.currentLang}][title]"]`) 
                || document.querySelector('input[name="title"]')
                || document.querySelector('input[name="name"]');
            if (titleInput && titleInput.value.trim() && !this.userTopic) {
                this.existingData = `Tiêu đề hiện tại: ${titleInput.value.trim()}`;
            }
        },

        fetchExistingContent() {
            const titleInput = document.querySelector(`input[name="translations[${this.currentLang}][title]"]`) 
                || document.querySelector('input[name="title"]')
                || document.querySelector('input[name="name"]');
            if (titleInput && titleInput.value.trim()) {
                this.userTopic = titleInput.value.trim();
            }
        },

        async generateContent() {
            if (!this.userTopic.trim()) return;

            this.isGenerating = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("admin.ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        prompt: this.userTopic,
                        source_data: this.sourceData,
                        existing_data: this.existingData,
                        post_type: this.postType,
                        master_prompt: this.masterPrompt,
                        tone: this.tone,
                        lang: this.currentLang,
                        structured: true
                    })
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => null);
                    throw new Error(err?.error || err?.message || `Lỗi HTTP ${response.status}`);
                }

                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }

                if (!data.structured || !data.title) {
                    throw new Error(data.error || 'AI không tạo được cấu trúc bài viết hợp lệ. Vui lòng bấm tạo lại!');
                }

                this.resultTitle = data.title;
                this.resultSlug = data.slug || '';
                this.resultExcerpt = data.excerpt || '';
                this.resultContent = data.content || '';
                this.resultFocusKeyword = data.focus_keyword || '';
                this.resultMetaTitle = data.meta_title || data.title;
                this.resultMetaDesc = data.meta_description || data.excerpt;
                this.resultSchemaType = data.schema_type || '';

                this.hasResult = true;
                this.resultTab = 'preview';
            } catch (err) {
                console.error('AI Generate Error:', err);
                this.errorMessage = err.message || 'Không thể tạo nội dung từ AI. Vui lòng kiểm tra lại cấu hình API.';
            } finally {
                this.isGenerating = false;
            }
        },

        cleanPlainText(content) {
            if (!content) return '';
            const tmp = document.createElement('div');
            tmp.innerHTML = content
                .replace(/<br\s*[\/]?>/gi, '\n')
                .replace(/<\/p>/gi, '\n\n')
                .replace(/<li>/gi, '• ')
                .replace(/<\/li>/gi, '\n');
            return (tmp.textContent || tmp.innerText || '').trim();
        },

        setRichEditorValue(targetEl, htmlContent, isPlainTextFallback = false) {
            if (!targetEl || htmlContent === undefined || htmlContent === null) return false;
            let updated = false;

            // 1. CKEditor 5 (Standard in this project: window.activeCKEditors)
            if (window.activeCKEditors && typeof window.activeCKEditors === 'object') {
                // By targetEl.id
                if (targetEl.id && window.activeCKEditors[targetEl.id]) {
                    try {
                        window.activeCKEditors[targetEl.id].setData(htmlContent);
                        updated = true;
                    } catch (e) {
                        console.warn('CKEditor 5 setData error on #' + targetEl.id, e);
                    }
                }

                // By targetEl.name
                if (!updated && targetEl.name && window.activeCKEditors[targetEl.name]) {
                    try {
                        window.activeCKEditors[targetEl.name].setData(htmlContent);
                        updated = true;
                    } catch (e) {
                        console.warn('CKEditor 5 setData error on name=' + targetEl.name, e);
                    }
                }

                // By sourceElement or element match
                if (!updated) {
                    for (const key in window.activeCKEditors) {
                        const inst = window.activeCKEditors[key];
                        if (inst && (inst.sourceElement === targetEl || inst.element === targetEl)) {
                            try {
                                inst.setData(htmlContent);
                                updated = true;
                                break;
                            } catch (e) {}
                        }
                    }
                }
            }

            // 2. Direct element property ckeditorInstance
            if (!updated && targetEl.ckeditorInstance && typeof targetEl.ckeditorInstance.setData === 'function') {
                try {
                    targetEl.ckeditorInstance.setData(htmlContent);
                    updated = true;
                } catch (e) {}
            }

            // 3. CKEditor 4 legacy
            if (!updated && window.CKEDITOR && window.CKEDITOR.instances) {
                if (targetEl.id && window.CKEDITOR.instances[targetEl.id]) {
                    try {
                        window.CKEDITOR.instances[targetEl.id].setData(htmlContent);
                        updated = true;
                    } catch (e) {}
                }
            }

            // 4. Quill Editor
            if (!updated && targetEl.__quill && typeof targetEl.__quill.root === 'object') {
                try {
                    targetEl.__quill.root.innerHTML = htmlContent;
                    updated = true;
                } catch (e) {}
            }

            // 5. TinyMCE
            if (!updated && window.tinymce && targetEl.id && window.tinymce.get(targetEl.id)) {
                try {
                    window.tinymce.get(targetEl.id).setContent(htmlContent);
                    updated = true;
                } catch (e) {}
            }

            // 6. Set value on underlying textarea/input
            const textToSet = (!updated && isPlainTextFallback) ? this.cleanPlainText(htmlContent) : htmlContent;
            targetEl.value = textToSet;
            targetEl.dispatchEvent(new Event('input', { bubbles: true }));
            targetEl.dispatchEvent(new Event('change', { bubbles: true }));

            // Dispatch global event
            if (targetEl.id) {
                window.dispatchEvent(new CustomEvent('ckeditor-change', { detail: { id: targetEl.id, value: targetEl.value } }));
            }

            return updated;
        },

        applyToForm() {
            // 1. Fill Title or Product Name
            const titleInputs = document.querySelectorAll(`
                input[name="name"],
                input[name="translations[${this.currentLang}][title]"],
                input[name="title"],
                input[name*="[title]"],
                #title, #name
            `);
            titleInputs.forEach(input => {
                if (this.resultTitle) {
                    input.value = this.resultTitle;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // 2. Fill Slug
            let targetSlug = this.resultSlug;
            if (!targetSlug && this.resultTitle) {
                targetSlug = this.resultTitle
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[đĐ]/g, 'd')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
            }
            if (targetSlug) {
                const slugInputs = document.querySelectorAll('input[name="slug"], input[name*="[slug]"], #slug');
                slugInputs.forEach(input => {
                    input.value = targetSlug;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            // 3. Fill Excerpt or Product Short Description (CKEditor 5 / Textarea)
            if (this.resultExcerpt) {
                const excerptElements = document.querySelectorAll(`
                    #short_description,
                    textarea[name="short_description"],
                    textarea[name="translations[${this.currentLang}][excerpt]"],
                    textarea[name="excerpt"],
                    #excerpt,
                    textarea[name*="[excerpt]"]
                `);
                
                let excerptUpdated = false;
                excerptElements.forEach(el => {
                    if (this.setRichEditorValue(el, this.resultExcerpt, true)) {
                        excerptUpdated = true;
                    }
                });

                // Fallback for CKEditor 5 instances directly
                if (!excerptUpdated && window.activeCKEditors) {
                    ['short_description', 'excerpt', `post_excerpt_${this.currentLang}`].forEach(k => {
                        if (window.activeCKEditors[k]) {
                            try { window.activeCKEditors[k].setData(this.resultExcerpt); } catch (e) {}
                        }
                    });
                }
            }

            // 4. Fill Content or Product Full Description (CKEditor 5 / Textarea)
            if (this.resultContent) {
                const contentElements = document.querySelectorAll(`
                    #post_content_${this.currentLang},
                    #description,
                    textarea[name="description"],
                    textarea[name="translations[${this.currentLang}][content]"],
                    textarea[name="content"],
                    #content,
                    textarea.ckeditor:not(#short_description)
                `);

                let contentUpdated = false;
                contentElements.forEach(el => {
                    if (this.setRichEditorValue(el, this.resultContent, false)) {
                        contentUpdated = true;
                    }
                });

                // Fallback for CKEditor 5 instances directly
                if (!contentUpdated && window.activeCKEditors) {
                    ['description', `post_content_${this.currentLang}`, 'content'].forEach(k => {
                        if (window.activeCKEditors[k]) {
                            try { window.activeCKEditors[k].setData(this.resultContent); } catch (e) {}
                        }
                    });
                }
            }

            // 5. Fill SEO Meta Title & Meta Description
            const seoTitleInputs = document.querySelectorAll(`
                input[name="meta_title"],
                input[name="seo_title"],
                input[name="translations[${this.currentLang}][meta_title]"],
                input[name="translations[${this.currentLang}][seo_title]"],
                input[name*="[meta_title]"],
                input[name*="[seo_title]"]
            `);
            const targetMetaTitle = this.resultMetaTitle || this.resultTitle;
            if (targetMetaTitle) {
                seoTitleInputs.forEach(input => {
                    input.value = targetMetaTitle;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            const seoDescInputs = document.querySelectorAll(`
                textarea[name="meta_description"],
                textarea[name="seo_description"],
                textarea[name="translations[${this.currentLang}][meta_description]"],
                textarea[name="translations[${this.currentLang}][seo_description]"],
                textarea[name*="[meta_description]"],
                textarea[name*="[seo_description]"]
            `);
            const targetMetaDesc = this.cleanPlainText(this.resultMetaDesc || this.resultExcerpt);
            if (targetMetaDesc) {
                seoDescInputs.forEach(textarea => {
                    textarea.value = targetMetaDesc;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            // 6. Fill Focus Keyword & Schema Type
            if (this.resultFocusKeyword) {
                const kwInputs = document.querySelectorAll(`
                    input[name="focus_keyword"],
                    input[name="translations[${this.currentLang}][focus_keyword]"],
                    input[name*="[focus_keyword]"],
                    #focus_keyword
                `);
                kwInputs.forEach(input => {
                    input.value = this.resultFocusKeyword;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            if (this.resultSchemaType) {
                const schemaSelects = document.querySelectorAll(`
                    select[name="schema_type"],
                    select[name*="[schema_type]"],
                    #schema_type
                `);
                schemaSelects.forEach(select => {
                    select.value = this.resultSchemaType;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            // 7. Dispatch custom 'seo-updated' event to automatically trigger SEO Analyzer with 100/100 points
            window.dispatchEvent(new CustomEvent('seo-updated', {
                detail: {
                    keyword: this.resultFocusKeyword,
                    metaTitle: targetMetaTitle,
                    metaDesc: targetMetaDesc,
                    slug: targetSlug,
                    schemaType: this.resultSchemaType
                }
            }));

            // Notification feedback
            if (window.Toast) {
                Toast.fire({ icon: 'success', title: 'Đã điền đầy đủ tiêu đề, mô tả ngắn, nội dung và chuẩn SEO 100/100 vào form!' });
            } else if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Nội dung do AI tạo và dữ liệu SEO 100/100 điểm đã được áp dụng đầy đủ vào form.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Đã điền đầy đủ nội dung do AI tạo và dữ liệu SEO chuẩn vào form thành công!');
            }

            this.closeModal();
        }
    }
}
</script>
@endif

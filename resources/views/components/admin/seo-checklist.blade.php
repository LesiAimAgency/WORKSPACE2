{{-- SEO Checklist --}}
@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $context    = $context ?? 'post';
    $model      = $model ?? null;
    $seoEnabled = (bool) Setting::get('seo_checklist_' . $context, '1');
    $seoId      = $id ?? 'seo_' . Str::random(6);

    $checkKeys  = $seoEnabled
        ? Setting::where('group', 'seo')
            ->where('key', 'like', 'seo_check_%')
            ->where('value', '1')
            ->orderBy('sort_order')
            ->pluck('key')
            ->toArray()
        : [];

    $robotsMeta = $model?->robots_meta ?? [];
@endphp

@if($seoEnabled)
<div class="card" id="{{ $seoId }}_wrap" data-keys='@json($checkKeys)'>
    
    <div class="card-header">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-gray-400"></i> SEO Checklist
        </h3>
    </div>

    <div class="card-body p-0">

        {{-- Tabs --}}
        <div class="flex border-b border-gray-100">
            <button type="button" class="seo-tab active"
                onclick="SEOCheck.switchTab('{{ $seoId }}','general')">
                <i class="fa-solid fa-gear"></i> Cấu hình chung
            </button>

            <button type="button" class="seo-tab"
                onclick="SEOCheck.switchTab('{{ $seoId }}','advanced')">
                <i class="fa-solid fa-sliders"></i> Nâng cao
            </button>

            <button type="button" class="seo-tab"
                onclick="SEOCheck.switchTab('{{ $seoId }}','schema')">
                <i class="fa-solid fa-code"></i> Schema
            </button>
        </div>

        {{-- TAB GENERAL --}}
        <div class="seo-tab-panel p-4 space-y-4" id="{{ $seoId }}_tab_general">

            <div>
                <label class="form-label">Keyword chính</label>

                <div class="flex gap-2 items-center">
                    <input type="text"
                        id="{{ $seoId }}_keyword"
                        name="seo_focus_keyword"
                        value="{{ old('seo_focus_keyword', $model?->seo_focus_keyword) }}"
                        maxlength="100"
                        class="form-input flex-1"
                        placeholder="Chèn từ khóa bạn muốn xếp hạng."
                        oninput="SEOCheck.run('{{ $seoId }}')">

                    <span id="{{ $seoId }}_kw_count"
                        class="text-xs text-white bg-blue-500 rounded px-2 py-1">
                        0/100
                    </span>
                </div>
            </div>

            @if(empty($checkKeys))
                <p class="text-xs text-gray-400 py-2">
                    Chưa có tiêu chí nào được bật.
                </p>
            @else
                <div id="{{ $seoId }}_checklist" class="space-y-1.5"></div>
            @endif

        </div>

        {{-- TAB ADVANCED --}}
        <div class="seo-tab-panel p-4 space-y-5 hidden" id="{{ $seoId }}_tab_advanced">

            <div>
                <p class="text-sm font-semibold text-gray-700 mb-3">Robots Meta</p>

                <div class="space-y-2 pl-2">

                    <p class="text-xs font-medium text-gray-500 uppercase">Index meta</p>

                    <label class="flex items-center gap-2">
                        <input type="radio" name="robots_meta[index]" value="noindex"
                            {{ ($robotsMeta['index'] ?? 'index') === 'noindex' ? 'checked' : '' }}>
                        <span>No Index</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="radio" name="robots_meta[index]" value="index"
                            {{ ($robotsMeta['index'] ?? 'index') === 'index' ? 'checked' : '' }}>
                        <span>Index</span>
                    </label>

                    <p class="text-xs font-medium text-gray-500 uppercase mt-3">Meta flags</p>

                    @foreach(['nofollow', 'noarchive', 'noimageindex', 'nosnippet'] as $flag)
                        <label class="flex items-center gap-2">
                            <input type="checkbox"
                                name="robots_meta[{{ $flag }}]"
                                value="1"
                                {{ ($robotsMeta[$flag] ?? false) ? 'checked' : '' }}>
                            <span>{{ ucwords(str_replace('no', 'No ', $flag)) }}</span>
                        </label>
                    @endforeach

                </div>
            </div>

        </div>

        {{-- TAB SCHEMA --}}
        <div class="seo-tab-panel p-4 space-y-4 hidden" id="{{ $seoId }}_tab_schema">
            @php
                $schemaData = is_array($model?->schema_json) ? $model->schema_json : [];
                $schemaMode = $schemaData['mode'] ?? 'auto';
            @endphp

            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Cấu hình Schema</span>
                <select name="schema_json[mode]" class="text-[10px] border-none bg-gray-100 rounded px-2 py-1 font-bold outline-none"
                    onchange="document.getElementById('{{ $seoId }}_schema_auto').classList.toggle('hidden', this.value !== 'auto');
                              document.getElementById('{{ $seoId }}_schema_manual').classList.toggle('hidden', this.value !== 'manual');">
                    <option value="auto" {{ $schemaMode === 'auto' ? 'selected' : '' }}>Tự động (Khuyên dùng)</option>
                    <option value="manual" {{ $schemaMode === 'manual' ? 'selected' : '' }}>Thủ công (JSON-LD)</option>
                </select>
            </div>

            <div id="{{ $seoId }}_schema_auto" class="{{ $schemaMode !== 'auto' ? 'hidden' : '' }} space-y-4">
                @if($context === 'product')
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 space-y-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Thông tin sản phẩm</p>
                        <div>
                            <label class="text-[10px] font-bold text-gray-600 block mb-1">Thương hiệu (Brand)</label>
                            <input type="text" name="schema_json[brand]" value="{{ $schemaData['brand'] ?? '' }}" 
                                class="form-input !py-1.5 !text-xs" placeholder="e.g. VietTinMart">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-gray-600 block mb-1">MPN / SKU</label>
                                <input type="text" name="schema_json[mpn]" value="{{ $schemaData['mpn'] ?? '' }}" 
                                    class="form-input !py-1.5 !text-xs" placeholder="Mã định danh">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-600 block mb-1">Tình trạng</label>
                                <select name="schema_json[condition]" class="form-select !py-1.5 !text-xs">
                                    <option value="NewCondition" {{ ($schemaData['condition'] ?? '') == 'NewCondition' ? 'selected' : '' }}>Mới (New)</option>
                                    <option value="UsedCondition" {{ ($schemaData['condition'] ?? '') == 'UsedCondition' ? 'selected' : '' }}>Đã qua sử dụng</option>
                                    <option value="RefurbishedCondition" {{ ($schemaData['condition'] ?? '') == 'RefurbishedCondition' ? 'selected' : '' }}>Tân trang</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-600 block mb-1">Trạng thái hàng</label>
                            <select name="schema_json[availability]" class="form-select !py-1.5 !text-xs">
                                <option value="InStock" {{ ($schemaData['availability'] ?? '') == 'InStock' ? 'selected' : '' }}>Còn hàng (In Stock)</option>
                                <option value="OutOfStock" {{ ($schemaData['availability'] ?? '') == 'OutOfStock' ? 'selected' : '' }}>Hết hàng</option>
                                <option value="PreOrder" {{ ($schemaData['availability'] ?? '') == 'PreOrder' ? 'selected' : '' }}>Đặt trước</option>
                            </select>
                        </div>
                    </div>
                @elseif($context === 'post')
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 space-y-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Thông tin bài viết</p>
                        <div>
                            <label class="text-[10px] font-bold text-gray-600 block mb-1">Loại bài viết</label>
                            <select name="schema_json[type]" class="form-select !py-1.5 !text-xs">
                                <option value="Article" {{ ($schemaData['type'] ?? '') == 'Article' ? 'selected' : '' }}>Article (Mặc định)</option>
                                <option value="BlogPosting" {{ ($schemaData['type'] ?? '') == 'BlogPosting' ? 'selected' : '' }}>Blog Posting</option>
                                <option value="NewsArticle" {{ ($schemaData['type'] ?? '') == 'NewsArticle' ? 'selected' : '' }}>News Article</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-600 block mb-1">Tác giả (Author)</label>
                            <input type="text" name="schema_json[author]" value="{{ $schemaData['author'] ?? '' }}" 
                                class="form-input !py-1.5 !text-xs" placeholder="Tên người viết">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-600 block mb-1">Nhà xuất bản (Publisher)</label>
                            <input type="text" name="schema_json[publisher]" value="{{ $schemaData['publisher'] ?? '' }}" 
                                class="form-input !py-1.5 !text-xs" placeholder="e.g. VietTinMart News">
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-xs text-gray-400 border-2 border-dashed rounded-xl">
                        Hệ thống sẽ tự động tạo Schema WebPage cho loại nội dung này.
                    </div>
                @endif
            </div>

            <div id="{{ $seoId }}_schema_manual" class="{{ $schemaMode !== 'manual' ? 'hidden' : '' }}">
                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1.5">Mã JSON-LD tùy chỉnh</label>
                <textarea name="schema_json[raw]" rows="10"
                    class="w-full font-mono text-[10px] bg-gray-900 text-green-400 border rounded-lg p-3 resize-y outline-none"
                    placeholder='{ "@@context": "https://schema.org", "@@type": "Product", ... }'
                >{{ $schemaData['raw'] ?? '' }}</textarea>
                <p class="text-[10px] text-orange-500 mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Chế độ thủ công sẽ ghi đè toàn bộ Schema tự động.</p>
            </div>
        </div>

    </div>
</div>

{{-- INIT JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof SEOCheck !== 'undefined') {
        SEOCheck.init(@json($seoId));
    }
});
</script>
@endif
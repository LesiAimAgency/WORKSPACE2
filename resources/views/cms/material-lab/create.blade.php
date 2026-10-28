@extends('cms.layouts.app')

@section('title', 'Thêm mới vật liệu - Material Lab')
@section('page-title', 'Thêm mới vật liệu')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.material-lab.index') }}" class="hover:text-rose-600 transition-colors">Material Lab</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Thêm mới</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Thêm mẫu vật liệu mới</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.material-lab.config') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                Cấu hình các bước Wizard
            </a>
            <a href="{{ route('material-lab') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.material-lab.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc pl-5 space-y-0.5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.material-lab.store') }}" method="POST">
        @csrf

        <!-- Bố cục 2 cột tỷ lệ 4/8: Cột trái 4/12 sticky, Cột phải 8/12 cuộn tự nhiên -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ============================================== -->
            <!-- CỘT TRÁI: 4/12 (Sticky) - Nhóm 1: Thông tin chung -->
            <!-- ============================================== -->
            <div class="lg:col-span-4 lg:sticky lg:top-6 self-start space-y-5">
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        1. Thông tin chung
                    </h3>

                    <!-- Component Chọn Hình ảnh đại diện -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh đại diện</label>
                        @include('cms.components.media-picker', [
                            'name' => 'image_url', 
                            'value' => old('image_url', ''),
                            'label' => 'Chọn ảnh'
                        ])
                        <p class="text-[10px] text-gray-400 mt-1.5">Ảnh hiển thị thực tế tại thư viện và tài liệu tư vấn.</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tên vật liệu <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Sàn nhựa vinyl hèm khóa (LVT)..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <!-- Active Toggle -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 bg-gray-50 hover:bg-gray-100/80 rounded-lg border border-gray-200 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="text-xs font-semibold text-gray-800 block">Kích hoạt hiển thị</span>
                                <span class="text-[11px] text-gray-500">Cho phép gợi ý trong thư viện Material Lab</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Guide Tip -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 text-[11px] text-gray-500 space-y-1.5">
                    <div class="font-semibold text-gray-700 mb-1">Mẫu vật liệu mới</div>
                    <p>• Mã định danh sẽ được hệ thống tự động sinh từ tên vật liệu.</p>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CỘT PHẢI: 8/12 (Form cuộn tự nhiên)            -->
            <!-- ============================================== -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- Nhóm 2: Phân loại & Vị trí áp dụng Step-by-Step (Bước 1, Bước 2, Bước 3) -->
                @php
                    $defaultTypologyKeys = collect($typologies)->map(fn($t) => is_array($t) ? ($t['en'] ?? '') : $t)->filter()->values()->toArray();
                    $selectedTypologies = old('suitable_typologies', $defaultTypologyKeys);
                    if (!is_array($selectedTypologies)) {
                        $selectedTypologies = $defaultTypologyKeys;
                    }

                    $selectedLocs = old('suitable_locations', ['Floor']);
                    if (!is_array($selectedLocs)) {
                        $selectedLocs = [];
                    }

                    $selectedAreas = old('suitable_areas', []);
                    if (!is_array($selectedAreas)) {
                        $selectedAreas = [];
                    }
                @endphp
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-5"
                     x-data="{
                        selectedTypos: @js(array_values($selectedTypologies)),
                        selectedSurfs: @js(array_values($selectedLocs)),
                        selectedAreas: @js(array_values($selectedAreas)),
                        areaMatrix: @js($areaMatrix ?? []),
                        areaLabels: @js($areaLabels ?? []),
                        allTypos: @js($defaultTypologyKeys),
                        allSurfs: @js(collect($surfaces)->map(fn($s) => is_array($s) ? ($s['en'] ?? '') : $s)->filter()->values()->toArray()),
                        get availableAreas() {
                            const set = new Set();
                            const typos = this.selectedTypos.length ? this.selectedTypos : this.allTypos;
                            const surfs = this.selectedSurfs.length ? this.selectedSurfs : this.allSurfs;
                            typos.forEach(t => {
                                surfs.forEach(s => {
                                    if (this.areaMatrix[t] && Array.isArray(this.areaMatrix[t][s])) {
                                        this.areaMatrix[t][s].forEach(a => set.add(a));
                                    }
                                });
                            });
                            return Array.from(set).map(en => ({
                                en: en,
                                vi: this.areaLabels[en] || en
                            }));
                        },
                        toggleAllTypos() {
                            if (this.selectedTypos.length === this.allTypos.length) {
                                this.selectedTypos = [];
                            } else {
                                this.selectedTypos = [...this.allTypos];
                            }
                        },
                        toggleAllSurfs() {
                            if (this.selectedSurfs.length === this.allSurfs.length) {
                                this.selectedSurfs = [];
                            } else {
                                this.selectedSurfs = [...this.allSurfs];
                            }
                        },
                        toggleAllAreas() {
                            const availKeys = this.availableAreas.map(a => a.en);
                            const allSelected = availKeys.length > 0 && availKeys.every(k => this.selectedAreas.includes(k));
                            if (allSelected) {
                                this.selectedAreas = [];
                            } else {
                                this.selectedAreas = [...availKeys];
                            }
                        }
                     }">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            2. Phân loại &amp; Vị trí áp dụng theo từng bước (/material-lab)
                        </h3>
                        <a href="{{ route('admin.material-lab.config') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Sửa danh sách câu hỏi / lựa chọn Wizard &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Brand Select -->
                        @php
                            $currentBrand = old('brand', '');
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-gray-700">
                                    Thương hiệu / Nhà cung cấp <span class="text-rose-500">*</span>
                                </label>
                                <a href="{{ route('admin.material-lab.brands') }}" class="text-[11px] text-rose-600 hover:text-rose-700 hover:underline font-medium">
                                    Quản lý thương hiệu &rarr;
                                </a>
                            </div>
                            <select name="brand" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="">-- Chọn thương hiệu / nhà cung cấp --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b }}" {{ $currentBrand === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Select -->
                        @php
                            $currentCategory = old('category', '');
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-gray-700">
                                    Phân loại danh mục <span class="text-rose-500">*</span>
                                </label>
                                <a href="{{ route('admin.material-lab.categories') }}" class="text-[11px] text-indigo-600 hover:text-indigo-700 hover:underline font-medium">
                                    Quản lý danh mục &rarr;
                                </a>
                            </div>
                            <select name="category" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="">-- Chọn phân loại danh mục --</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c }}" {{ $currentCategory === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- BƯỚC 1: LOẠI HÌNH CÔNG TRÌNH TƯƠNG THÍCH -->
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] inline-flex items-center justify-center font-bold">1</span>
                                    Loại hình công trình tương thích (Bộ lọc Bước 1: "{{ $step1Title ?? 'Công trình của bạn là gì?' }}")
                                </label>
                                <p class="text-[11px] text-gray-500 mt-0.5 ml-6">{{ $step1Desc ?? 'Chọn loại hình để gợi ý vật liệu phù hợp.' }}</p>
                            </div>
                            <button type="button" @click="toggleAllTypos()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 transition-colors">
                                <span x-text="selectedTypos.length === allTypos.length ? 'Bỏ chọn tất cả' : 'Chọn tất cả'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                            @foreach($typologies as $typo)
                                @php
                                    $tKey = is_array($typo) ? ($typo['en'] ?? '') : $typo;
                                    $tLabel = is_array($typo) ? ($typo['vi'] ?? $tKey) : $typo;
                                    $tIcon = is_array($typo) ? ($typo['icon'] ?? '🏢') : '🏢';
                                    $tDesc = is_array($typo) ? ($typo['desc'] ?? '') : '';
                                @endphp
                                <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-all select-none bg-white"
                                       :class="selectedTypos.includes('{{ addslashes($tKey) }}') ? 'border-rose-500 ring-1 ring-rose-500/20 bg-rose-50/20' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="checkbox" name="suitable_typologies[]" value="{{ $tKey }}" x-model="selectedTypos" class="mt-0.5 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-xs font-semibold text-gray-800">{{ $tIcon }} {{ $tLabel }}</span>
                                            <span class="text-[10px] font-mono text-gray-400">{{ $tKey }}</span>
                                        </div>
                                        @if($tDesc)
                                            <p class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ $tDesc }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- BƯỚC 2: VỊ TRÍ BỀ MẶT TƯƠNG THÍCH -->
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] inline-flex items-center justify-center font-bold">2</span>
                                    Vị trí bề mặt tương thích (Bộ lọc Bước 2: "{{ $step2Title ?? 'Vị trí nào cần vật liệu?' }}")
                                </label>
                                <p class="text-[11px] text-gray-500 mt-0.5 ml-6">{{ $step2Desc ?? 'Chọn bề mặt để lọc vật liệu phù hợp.' }}</p>
                            </div>
                            <button type="button" @click="toggleAllSurfs()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 px-2.5 py-1 rounded bg-blue-50 hover:bg-blue-100 transition-colors">
                                <span x-text="selectedSurfs.length === allSurfs.length ? 'Bỏ chọn tất cả' : 'Chọn tất cả'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                            @foreach($surfaces as $surf)
                                @php $key = is_array($surf) ? ($surf['en'] ?? '') : $surf; @endphp
                                @php $label = is_array($surf) ? ($surf['vi'] ?? $key) : $surf; @endphp
                                <label class="flex flex-col justify-between p-2.5 rounded-lg border cursor-pointer text-xs select-none bg-white transition-all"
                                       :class="selectedSurfs.includes('{{ addslashes($key) }}') ? 'border-blue-600 ring-1 ring-blue-600/20 bg-blue-50/20' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="suitable_locations[]" value="{{ $key }}" x-model="selectedSurfs" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                                        <span class="font-semibold text-gray-800">{{ $label }}</span>
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-400 mt-1 pl-6">{{ $key }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- BƯỚC 3: KHU VỰC CHỨC NĂNG ÁP DỤNG -->
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] inline-flex items-center justify-center font-bold">3</span>
                                    Khu vực chức năng áp dụng (Bộ lọc Bước 3: "{{ $step3Title ?? 'Khu vực chức năng nào?' }}")
                                </label>
                                <p class="text-[11px] text-gray-500 mt-0.5 ml-6">
                                    Nếu <strong>không tích chọn ô nào</strong> (để trống), vật liệu sẽ tự động hiển thị cho <strong>tất cả khu vực</strong> thuộc Loại hình (Bước 1) &amp; Bề mặt (Bước 2) đã chọn. Nếu chỉ muốn giới hạn ở một số khu vực cụ thể, hãy tích chọn bên dưới:
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-gray-500" x-text="selectedAreas.length === 0 ? 'Đang áp dụng: Tất cả khu vực' : ('Đã chọn: ' + selectedAreas.length + ' khu vực')"></span>
                                <button type="button" @click="toggleAllAreas()" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                    Chọn / Bỏ chọn tất cả
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                            <div x-show="availableAreas.length === 0" class="text-xs text-gray-400 text-center py-2">
                                Vui lòng chọn ít nhất 1 Loại hình (Bước 1) và 1 Bề mặt (Bước 2) để hiển thị các khu vực chức năng tương ứng.
                            </div>
                            <div class="flex flex-wrap gap-2" x-show="availableAreas.length > 0">
                                <template x-for="area in availableAreas" :key="area.en">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all bg-white"
                                           :class="selectedAreas.includes(area.en) ? 'border-emerald-600 bg-emerald-50/40 text-emerald-900 font-semibold' : 'border-gray-200 text-gray-700 hover:border-gray-300'">
                                        <input type="checkbox" name="suitable_areas[]" :value="area.en" x-model="selectedAreas" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span x-text="area.vi"></span>
                                        <span class="text-[10px] text-gray-400 italic" x-text="'(' + area.en + ')'"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nhóm 3: Thuộc tính vật liệu & Chi phí (Bộ lọc Bước 4: Nhu cầu thực tế) -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        3. Nhu cầu thực tế &amp; Thuộc tính chi phí (Bộ lọc Bước 4: "{{ $step4Title ?? 'Nhu cầu thực tế?' }}")
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- Budget Level -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Mức ngân sách / Đơn giá (Bước 4) <span class="text-rose-500">*</span></label>
                            <select name="budget_level" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                @foreach($budgetLevels ?? [] as $bItem)
                                    @if(empty($bItem['isSkip']))
                                        <option value="{{ $bItem['level'] }}" {{ (int) old('budget_level', 2) === (int) $bItem['level'] ? 'selected' : '' }}>
                                            {{ $bItem['level'] }}: {{ $bItem['vi'] }} ({{ $bItem['vnd'] }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Durability Years -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Độ bền bảo hành (năm - Bước 4) <span class="text-rose-500">*</span></label>
                            <input type="number" name="durability_years" value="{{ old('durability_years', 10) }}" min="1" max="50" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>

                        <!-- Origin Select -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nguồn gốc / Xuất xứ (Bước 4) <span class="text-rose-500">*</span></label>
                            <select name="origin" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="Vietnam" {{ old('origin', 'Vietnam') === 'Vietnam' ? 'selected' : '' }}>Sản xuất tại Việt Nam (Hàng Việt)</option>
                                <option value="Import" {{ old('origin') === 'Import' ? 'selected' : '' }}>Nhập khẩu chính hãng</option>
                                <option value="Both" {{ old('origin') === 'Both' ? 'selected' : '' }}>Liên doanh / Cả hai</option>
                            </select>
                        </div>

                        <!-- Availability Select -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tồn kho / Thời gian cung ứng (Bước 4) <span class="text-rose-500">*</span></label>
                            <select name="availability" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="Stock" {{ old('availability', 'Stock') === 'Stock' ? 'selected' : '' }}>Có sẵn kho (Stock)</option>
                                <option value="2-3 weeks" {{ old('availability') === '2-3 weeks' ? 'selected' : '' }}>Đặt 2–3 tuần (2-3 weeks)</option>
                                <option value="6-8 weeks" {{ old('availability') === '6-8 weeks' ? 'selected' : '' }}>Chấp nhận 6–8 tuần (6-8 weeks)</option>
                            </select>
                        </div>

                        <!-- Finish Select with Inline Add Option -->
                        @php
                            $currentFinish = old('finish', '');
                            $isCustomFinish = !empty($currentFinish) && !in_array($currentFinish, $finishes);
                        @endphp
                        <div x-data="{ 
                            isNew: {{ $isCustomFinish ? 'true' : 'false' }}, 
                            finishVal: '{{ addslashes($currentFinish) }}' 
                        }">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-gray-700">
                                    Bề mặt hoàn thiện (Finish)
                                </label>
                                <button type="button" @click="isNew = !isNew; if(isNew) { finishVal = ''; } else { finishVal = ''; }" class="text-[11px] text-rose-600 hover:text-rose-700 font-medium flex items-center gap-1 transition-colors">
                                    <span x-show="!isNew">+ Nhập mới</span>
                                    <span x-show="isNew">← Chọn có sẵn</span>
                                </button>
                            </div>

                            <!-- Select existing -->
                            <div x-show="!isNew">
                                <select :disabled="isNew" name="finish" x-model="finishVal" @change="if($event.target.value === '__NEW__') { isNew = true; finishVal = ''; }" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                    <option value="">-- Chọn bề mặt hoàn thiện --</option>
                                    @foreach($finishes as $f)
                                        <option value="{{ $f }}" {{ $currentFinish === $f ? 'selected' : '' }}>{{ $f }}</option>
                                    @endforeach
                                    <option value="__NEW__" class="font-bold text-rose-600">+ Thêm bề mặt mới...</option>
                                </select>
                            </div>

                             <!-- Input new -->
                             <div x-show="isNew">
                                 <input type="text" :disabled="!isNew" name="finish" x-model="finishVal" placeholder="Nhập tên bề mặt hoàn thiện mới..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Nhóm 4: Nội dung tư vấn chuyên sâu (Bước 5: Kết quả gợi ý) -->
                 <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-5 relative z-[1]">
                     <div>
                         <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                             <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                             4. Nội dung tư vấn chuyên sâu của từng sản phẩm (Hiển thị tại Bước 5 - Kết quả)
                         </h3>
                         <p class="text-[11px] text-gray-500 mt-2">
                             Mỗi sản phẩm vật liệu có bộ <strong>Căn cứ áp dụng, Yêu cầu cháy, Chỉ số</strong> (Tab Tiêu chuẩn kỹ thuật) và <strong>Thực tế thi công, Lỗi thường gặp, Mẹo</strong> (Tab Kinh nghiệm Workspace) đồng bộ trên cả thẻ sản phẩm và bảng chi tiết bên trái ở Bước 5.
                         </p>
                     </div>

                     <!-- 4.1. TAB 1: TIÊU CHUẨN KỸ THUẬT -->
                     <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                         <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                             <span class="text-xs font-bold text-gray-900 uppercase flex items-center gap-1.5">
                                 <span class="px-2 py-0.5 bg-gray-900 text-white rounded text-[10px]">Tab 1</span>
                                 Tiêu chuẩn kỹ thuật (AI / Standards)
                             </span>
                             <span class="text-[10px] text-gray-500">Hiển thị khi chọn tab "Tiêu chuẩn kỹ thuật"</span>
                         </div>

                         <div>
                             <label class="block text-xs font-semibold text-gray-800 mb-1">
                                 Căn cứ áp dụng &amp; Đặc tính kỹ thuật
                             </label>
                             <textarea name="ai_standards" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" placeholder="Mỗi dòng 1 tiêu chuẩn áp dụng cho sản phẩm này:&#10;• QCVN 16:2023/BXD – Cường độ nén 40MPa...&#10;• QCVN 06:2022/BXD – Chống trượt R11...">{{ old('ai_standards') }}</textarea>
                             <p class="text-[10px] text-gray-400 mt-1">Hiển thị đồng thời tại mục "Căn cứ áp dụng" (cột trái) và phần mô tả kỹ thuật trên thẻ sản phẩm (cột phải).</p>
                         </div>

                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Yêu cầu cháy (Ô "Yêu cầu cháy")
                                 </label>
                                 <input type="text" name="fire_requirement" value="{{ old('fire_requirement') }}" placeholder="Ví dụ: Class A1 – Không cháy / Bfl-s1 cho thoát nạn" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>

                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Chỉ số / Thông số kỹ thuật tóm tắt
                                 </label>
                                 <input type="text" name="metrics" value="{{ old('metrics') }}" placeholder="Ví dụ: R11 • Nén 40MPa • Hấp thụ nước <3% • VOC 0g/L" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>
                         </div>
                     </div>

                     <!-- 4.2. TAB 2: KINH NGHIỆM WORKSPACE -->
                     <div class="p-4 bg-rose-50/40 rounded-xl border border-rose-200/70 space-y-4">
                         <div class="flex items-center justify-between border-b border-rose-200/70 pb-2">
                             <span class="text-xs font-bold text-rose-900 uppercase flex items-center gap-1.5">
                                 <span class="px-2 py-0.5 bg-rose-600 text-white rounded text-[10px]">Tab 2</span>
                                 Kinh nghiệm Workspace (Thực tế thi công)
                             </span>
                             <span class="text-[10px] text-rose-600">Hiển thị khi chọn tab "Kinh nghiệm Workspace"</span>
                         </div>

                         <div>
                             <label class="block text-xs font-semibold text-gray-800 mb-1">
                                 Thực tế thi công &amp; Trải nghiệm Workspace
                             </label>
                             <textarea name="ws_experience" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" placeholder="Mỗi dòng 1 kinh nghiệm thực tế riêng của sản phẩm này:&#10;• Đã lát 12 sảnh chính với Terrazzo 60x60...&#10;• Sau 5-7 năm có thể mài phủ bóng lại như mới...">{{ old('ws_experience') }}</textarea>
                             <p class="text-[10px] text-gray-400 mt-1">Hiển thị đồng thời tại mục "Thực tế thi công" (cột trái) và phần nhận xét thực tế trên thẻ sản phẩm (cột phải).</p>
                         </div>

                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Lỗi thường gặp (Ô "Lỗi thường gặp")
                                 </label>
                                 <input type="text" name="common_errors" value="{{ old('common_errors') }}" placeholder="Ví dụ: Nền ẩm >75% phồng mép • Dùng keo giá rẻ bong mép" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>

                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Mẹo thi công (Ô "Mẹo")
                                 </label>
                                 <input type="text" name="tips" value="{{ old('tips') }}" placeholder="Ví dụ: Đo ẩm <75% • Cán phẳng 2mm • Keo Wakol D3310" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>
                         </div>
                     </div>
                 </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.material-lab.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Thêm mới vật liệu
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

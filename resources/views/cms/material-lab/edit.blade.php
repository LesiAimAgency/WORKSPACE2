@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa vật liệu - Material Lab')
@section('page-title', 'Chỉnh sửa vật liệu')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.material-lab.index') }}" class="hover:text-rose-600 transition-colors">Material Lab</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Chỉnh sửa</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span>{{ $material->name }}</span>
                <span class="text-xs px-2 py-0.5 rounded font-mono font-semibold {{ $material->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $material->code }}
                </span>
            </h2>
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

    <form action="{{ route('admin.material-lab.update', $material) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Mã định danh tự sinh, lưu ẩn -->
        <input type="hidden" name="code" value="{{ old('code', $material->code) }}">

        <!-- Bố cục 2 cột tỷ lệ 4/8: Cột trái 4/12 sticky, Cột phải 8/12 cuộn tự nhiên -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ============================================== -->
            <!-- CỘT TRÁI: 4/12 (Sticky) - Nhóm 1: Thông tin chung -->
            <!-- ============================================== -->
            <div class="lg:col-span-4 lg:sticky lg:top-6 self-start space-y-5">
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            1. Thông tin chung
                        </h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-gray-100 text-gray-600 rounded">
                            {{ $material->code }}
                        </span>
                    </div>

                    <!-- Component Sửa / Chọn Hình ảnh đại diện -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh đại diện</label>
                        @include('cms.components.media-picker', [
                            'name' => 'image_url', 
                            'value' => old('image_url', $material->image_url ?? ''),
                            'label' => 'Chọn ảnh'
                        ])
                        <p class="text-[10px] text-gray-400 mt-1.5">Ảnh hiển thị thực tế tại thư viện và tài liệu tư vấn.</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tên vật liệu <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $material->name) }}" required placeholder="Ví dụ: Sàn nhựa vinyl hèm khóa (LVT)..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <!-- Brand Select -->
                    @php
                        $currentBrand = old('brand', $material->brand ?? '');
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-gray-700">
                                Thương hiệu / Nhà cung cấp <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('admin.material-lab.brands') }}" class="text-[11px] text-rose-600 hover:text-rose-700 hover:underline font-medium">
                                Quản lý &rarr;
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
                        $currentCategory = old('category', $material->category ?? '');
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-gray-700">
                                Phân loại danh mục <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('admin.material-lab.categories') }}" class="text-[11px] text-indigo-600 hover:text-indigo-700 hover:underline font-medium">
                                Quản lý &rarr;
                            </a>
                        </div>
                        <select name="category" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                            <option value="">-- Chọn phân loại danh mục --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c }}" {{ $currentCategory === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Active Toggle -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 bg-gray-50 hover:bg-gray-100/80 rounded-lg border border-gray-200 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="text-xs font-semibold text-gray-800 block">Kích hoạt hiển thị</span>
                                <span class="text-[11px] text-gray-500">Cho phép gợi ý trong thư viện Material Lab</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 text-[11px] text-gray-500 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span>Mã định danh hệ thống:</span>
                        <span class="font-mono font-semibold text-gray-700">{{ $material->code }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Cập nhật gần nhất:</span>
                        <span class="font-medium text-gray-700">{{ $material->updated_at ? $material->updated_at->format('d/m/Y H:i') : 'Mới' }}</span>
                    </div>
                </div>
                 <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="if(confirm('Bạn có chắc chắn muốn xóa vật liệu này?')) document.getElementById('delete-material-form').submit();" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Xóa vật liệu
                    </button>

                    <div class="flex items-center gap-3">
                       
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Cập nhật thay đổi
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CỘT PHẢI: 8/12 (Form cuộn tự nhiên)            -->
            <!-- ============================================== -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- Nhóm 2: Phân loại & Vị trí áp dụng Step-by-Step (Bước 1, Bước 2, Bước 3) -->
                @php
                    $defaultTypologyKeys = collect($typologies)->map(fn($t) => is_array($t) ? ($t['en'] ?? '') : $t)->filter()->values()->toArray();
                    $selectedTypologies = old('suitable_typologies', $material->suitable_typologies);
                    if (!is_array($selectedTypologies) || empty($selectedTypologies)) {
                        $selectedTypologies = $defaultTypologyKeys;
                    }

                    $selectedLocs = old('suitable_locations', $material->suitable_locations ?? []);
                    if (!is_array($selectedLocs)) {
                        $selectedLocs = [];
                    }

                    $selectedAreas = old('suitable_areas', $material->suitable_areas ?? []);
                    if (!is_array($selectedAreas)) {
                        $selectedAreas = [];
                    }
                    $selectedAreas = array_values(array_unique(array_map(fn($a) => \App\Http\Controllers\Admin\MaterialController::canonicalizeArea((string)$a), $selectedAreas)));
                @endphp
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm space-y-6"
                     x-data="{
                        compat: @js($materialCompatibility ?? ($material->resolveCompatibilityMatrix())),
                        activeTypoTab: @js($defaultTypologyKeys[0] ?? 'Office'),
                        activeSurfaceTab: 'Floor',
                        newAreaInput: '',
                        
                        allTypos: @js($typologies),
                        allSurfs: @js($surfaces),
                        areaMatrix: @js($areaMatrix ?? []),
                        areaLabels: @js($areaLabels ?? []),

                        // --- Helper: Typology (Bước 1) ---
                        isTypoEnabled(tKey) {
                            return Boolean(this.compat[tKey] && this.compat[tKey].enabled);
                        },
                        toggleTypo(tKey) {
                            if (!this.compat[tKey]) {
                                this.compat[tKey] = { enabled: false, surfaces: {} };
                            }
                            this.compat[tKey].enabled = !this.compat[tKey].enabled;
                            this.activeTypoTab = tKey;
                        },
                        toggleAllTypos() {
                            const allEnabled = this.allTypos.every(t => this.isTypoEnabled(t.en || t));
                            this.allTypos.forEach(t => {
                                const k = t.en || t;
                                if (!this.compat[k]) this.compat[k] = { enabled: false, surfaces: {} };
                                this.compat[k].enabled = !allEnabled;
                            });
                        },

                        // --- Helper: Surface (Bước 2) ---
                        isSurfaceEnabled(sKey, tKey = null) {
                            const typoKey = tKey || this.activeTypoTab;
                            const typo = this.compat[typoKey];
                            return Boolean(typo && typo.surfaces && typo.surfaces[sKey] && typo.surfaces[sKey].enabled);
                        },
                        toggleSurface(sKey, tKey = null) {
                            const typoKey = tKey || this.activeTypoTab;
                            if (!this.compat[typoKey]) {
                                this.compat[typoKey] = { enabled: true, surfaces: {} };
                            }
                            if (!this.compat[typoKey].surfaces[sKey]) {
                                this.compat[typoKey].surfaces[sKey] = { enabled: false, areas: [] };
                            }
                            this.compat[typoKey].surfaces[sKey].enabled = !this.compat[typoKey].surfaces[sKey].enabled;
                            this.activeTypoTab = typoKey;
                            this.activeSurfaceTab = sKey;
                        },
                        selectSurfaceTab(sKey, tKey = null) {
                            if (tKey) {
                                this.activeTypoTab = tKey;
                            }
                            this.activeSurfaceTab = sKey;
                            if (!this.isSurfaceEnabled(sKey, this.activeTypoTab)) {
                                if (!this.compat[this.activeTypoTab]) {
                                    this.compat[this.activeTypoTab] = { enabled: true, surfaces: {} };
                                }
                                if (!this.compat[this.activeTypoTab].surfaces[sKey]) {
                                    this.compat[this.activeTypoTab].surfaces[sKey] = { enabled: true, areas: [] };
                                } else {
                                    this.compat[this.activeTypoTab].surfaces[sKey].enabled = true;
                                }
                            }
                        },
                        toggleAllSurfacesForCurrentTypo(tKey = null) {
                            const typoKey = tKey || this.activeTypoTab;
                            const allEnabled = this.allSurfs.every(s => this.isSurfaceEnabled(s.en || s, typoKey));
                            this.allSurfs.forEach(s => {
                                const k = s.en || s;
                                if (!this.compat[typoKey]) {
                                    this.compat[typoKey] = { enabled: true, surfaces: {} };
                                }
                                if (!this.compat[typoKey].surfaces[k]) {
                                    this.compat[typoKey].surfaces[k] = { enabled: false, areas: [] };
                                }
                                this.compat[typoKey].surfaces[k].enabled = !allEnabled;
                            });
                        },

                        // --- Helper: Area (Bước 3: Phân theo cặp project_type + surface) ---
                        getCurrentPairAreas() {
                            const list = (this.areaMatrix[this.activeTypoTab] && this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab]) || [];
                            return list.map(aKey => ({
                                en: aKey,
                                vi: this.areaLabels[aKey] || aKey
                            }));
                        },
                        getSelectedAreasForCurrentPair() {
                            const surf = this.compat[this.activeTypoTab]?.surfaces?.[this.activeSurfaceTab];
                            return Array.isArray(surf?.areas) ? surf.areas : [];
                        },
                        isAreaSelected(aKey) {
                            return this.getSelectedAreasForCurrentPair().includes(aKey);
                        },
                        toggleArea(aKey) {
                            if (!this.compat[this.activeTypoTab]) {
                                this.compat[this.activeTypoTab] = { enabled: true, surfaces: {} };
                            }
                            if (!this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab]) {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab] = { enabled: true, areas: [] };
                            }
                            this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].enabled = true;

                            let areas = this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas || [];
                            if (areas.includes(aKey)) {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas = areas.filter(k => k !== aKey);
                            } else {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas.push(aKey);
                            }
                        },
                        selectAllAreasForCurrentPair() {
                            const list = (this.areaMatrix[this.activeTypoTab] && this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab]) || [];
                            if (!this.compat[this.activeTypoTab]) {
                                this.compat[this.activeTypoTab] = { enabled: true, surfaces: {} };
                            }
                            if (!this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab]) {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab] = { enabled: true, areas: [] };
                            }
                            this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].enabled = true;
                            this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas = [...list];
                        },
                        applyAllAreasForCurrentPair() {
                            if (!this.compat[this.activeTypoTab]) {
                                this.compat[this.activeTypoTab] = { enabled: true, surfaces: {} };
                            }
                            if (!this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab]) {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab] = { enabled: true, areas: [] };
                            }
                            this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].enabled = true;
                            this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas = [];
                        },
                        deselectAllAreasForCurrentPair() {
                            if (this.compat[this.activeTypoTab]?.surfaces?.[this.activeSurfaceTab]) {
                                this.compat[this.activeTypoTab].surfaces[this.activeSurfaceTab].areas = [];
                            }
                        },
                        addCustomArea() {
                            const val = (this.newAreaInput || '').trim();
                            if (!val) return;
                            const key = val;
                            this.areaLabels[key] = val;

                            if (!this.areaMatrix[this.activeTypoTab]) this.areaMatrix[this.activeTypoTab] = {};
                            if (!this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab]) this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab] = [];
                            if (!this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab].includes(key)) {
                                this.areaMatrix[this.activeTypoTab][this.activeSurfaceTab].push(key);
                            }

                            this.toggleArea(key);
                            this.newAreaInput = '';
                        },

                        // --- Thống kê ---
                        countEnabledSurfaces(tKey) {
                            const typo = this.compat[tKey];
                            if (!typo || !typo.surfaces) return 0;
                            return Object.values(typo.surfaces).filter(s => s && s.enabled).length;
                        },
                        countSelectedAreas(tKey, sKey) {
                            const surf = this.compat[tKey]?.surfaces?.[sKey];
                            if (!surf || !surf.enabled) return 0;
                            return Array.isArray(surf.areas) ? surf.areas.length : 0;
                        },

                        // --- Serialize Helpers for Backward-Compatibility Hidden Inputs ---
                        serializeTypologies() {
                            return Object.keys(this.compat).filter(t => this.compat[t] && this.compat[t].enabled);
                        },
                        serializeSurfaces() {
                            const surfs = new Set();
                            Object.keys(this.compat).forEach(t => {
                                if (this.compat[t] && this.compat[t].enabled && this.compat[t].surfaces) {
                                    Object.keys(this.compat[t].surfaces).forEach(s => {
                                        if (this.compat[t].surfaces[s] && this.compat[t].surfaces[s].enabled) {
                                            surfs.add(s);
                                        }
                                    });
                                }
                            });
                            return Array.from(surfs);
                        },
                        serializeAreas() {
                            const areas = new Set();
                            Object.keys(this.compat).forEach(t => {
                                if (this.compat[t] && this.compat[t].enabled && this.compat[t].surfaces) {
                                    Object.keys(this.compat[t].surfaces).forEach(s => {
                                        if (this.compat[t].surfaces[s] && this.compat[t].surfaces[s].enabled) {
                                            const list = this.compat[t].surfaces[s].areas || [];
                                            list.forEach(a => areas.add(a));
                                        }
                                    });
                                }
                            });
                            return Array.from(areas);
                        }
                     }">

                    <!-- 3-Tier Compatibility Matrix JSON Submission -->
                    <input type="hidden" name="compatibility" :value="JSON.stringify(compat)">

                    <!-- Backward Compatible Hidden Inputs -->
                    <template x-for="typo in serializeTypologies()" :key="'h-typo-' + typo">
                        <input type="hidden" name="suitable_typologies[]" :value="typo">
                    </template>
                    <template x-for="surf in serializeSurfaces()" :key="'h-surf-' + surf">
                        <input type="hidden" name="suitable_locations[]" :value="surf">
                    </template>
                    <template x-for="area in serializeAreas()" :key="'h-area-' + area">
                        <input type="hidden" name="suitable_areas[]" :value="area">
                    </template>

                    <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                        <div>
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                2. Ma trận tương thích vật liệu theo 3 tầng chuẩn (/material-lab)
                            </h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Quy tắc chuẩn: <strong>Loại công trình → Vị trí bề mặt → Khu vực chức năng</strong> (Bước 3 phụ thuộc vào cặp Bước 1 + Bước 2).</p>
                        </div>
                        <a href="{{ route('admin.material-lab.config') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Cấu hình danh mục câu hỏi Wizard &rarr;
                        </a>
                    </div>

                    <!-- ============================================== -->
                    <!-- BƯỚC 1: LOẠI HÌNH CÔNG TRÌNH TƯƠNG THÍCH (ACCORDION) -->
                    <!-- ============================================== -->
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] inline-flex items-center justify-center font-bold">1</span>
                                    Loại hình công trình tương thích (Bộ lọc Bước 1: "{{ $step1Title ?? 'Công trình của bạn là gì?' }}")
                                </label>
                                <p class="text-[11px] text-gray-500 mt-0.5 ml-6">{{ $step1Desc ?? 'Chọn loại hình để gợi ý vật liệu phù hợp. Bấm vào từng loại công trình để mở rộng cấu hình Bề mặt và Khu vực chức năng:' }}</p>
                            </div>
                            <button type="button" @click="toggleAllTypos()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 transition-colors">
                                <span x-text="allTypos.every(t => isTypoEnabled(t.en || t)) ? 'Tắt tất cả 6 công trình' : 'Bật tất cả 6 công trình'"></span>
                            </button>
                        </div>

                        <!-- Danh sách Accordion 6 loại công trình -->
                        <div class="space-y-3">
                            @foreach($typologies as $typo)
                                @php
                                    $tKey = is_array($typo) ? ($typo['en'] ?? '') : $typo;
                                    $tLabel = is_array($typo) ? ($typo['vi'] ?? $tKey) : $typo;
                                    $tDesc = is_array($typo) ? ($typo['desc'] ?? '') : '';
                                @endphp
                                <div class="border rounded-xl transition-all overflow-hidden bg-white"
                                     :class="activeTypoTab === '{{ addslashes($tKey) }}' ? 'border-rose-400 ring-2 ring-rose-500/10 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    
                                    <!-- Accordion Header -->
                                    <div class="flex items-center justify-between p-3.5 cursor-pointer select-none transition-colors"
                                         :class="activeTypoTab === '{{ addslashes($tKey) }}' ? 'bg-rose-50/20' : 'hover:bg-gray-50/60'"
                                         @click="activeTypoTab = (activeTypoTab === '{{ addslashes($tKey) }}' ? '' : '{{ addslashes($tKey) }}')">
                                        <div class="flex items-start gap-3">
                                            <input type="checkbox" 
                                                   :checked="isTypoEnabled('{{ addslashes($tKey) }}')"
                                                   @click.stop="toggleTypo('{{ addslashes($tKey) }}')"
                                                   class="mt-0.5 rounded border-gray-300 text-rose-600 focus:ring-rose-500 pointer-events-auto cursor-pointer">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-xs text-gray-900">{{ $tLabel }}</span>
                                                    <span class="text-[10px] font-mono text-gray-400">({{ $tKey }})</span>
                                                    <template x-if="isTypoEnabled('{{ addslashes($tKey) }}')">
                                                        <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.2 rounded">Bật</span>
                                                    </template>
                                                    <template x-if="!isTypoEnabled('{{ addslashes($tKey) }}')">
                                                        <span class="text-[9px] font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.2 rounded">Tắt</span>
                                                    </template>
                                                </div>
                                                @if($tDesc)
                                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $tDesc }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <span class="text-[11px] font-mono text-gray-500 hidden sm:inline" x-text="countEnabledSurfaces('{{ addslashes($tKey) }}') + '/5 bề mặt bật'"></span>
                                            <span class="text-[11px] font-semibold"
                                                  :class="activeTypoTab === '{{ addslashes($tKey) }}' ? 'text-rose-600' : 'text-gray-400'"
                                                  x-text="activeTypoTab === '{{ addslashes($tKey) }}' ? 'Đang mở cấu hình' : 'Mở cấu hình &rarr;'"></span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                                 :class="activeTypoTab === '{{ addslashes($tKey) }}' ? 'rotate-180 text-rose-600' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Accordion Content: BƯỚC 2 & BƯỚC 3 -->
                                    <div x-show="activeTypoTab === '{{ addslashes($tKey) }}'" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="border-t border-gray-100 p-4 bg-gray-50/40 space-y-5">
                                        @include('cms.material-lab.partials.accordion-step-content', [
                                            'tKey' => $tKey,
                                            'tLabel' => $tLabel,
                                            'surfaces' => $surfaces,
                                            'step3Title' => $step3Title ?? 'Khu vực chức năng nào?'
                                        ])
                                    </div>
                                </div>
                            @endforeach
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
                                        <option value="{{ $bItem['level'] }}" {{ (int) old('budget_level', $material->budget_level) === (int) $bItem['level'] ? 'selected' : '' }}>
                                            {{ $bItem['level'] }}: {{ $bItem['vi'] }} ({{ $bItem['vnd'] }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Durability Years -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Độ bền bảo hành (năm - Bước 4) <span class="text-rose-500">*</span></label>
                            <input type="number" name="durability_years" value="{{ old('durability_years', $material->durability_years) }}" min="1" max="50" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        </div>

                        <!-- Origin Select -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nguồn gốc / Xuất xứ (Bước 4) <span class="text-rose-500">*</span></label>
                            <select name="origin" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="Vietnam" {{ old('origin', $material->origin) === 'Vietnam' ? 'selected' : '' }}>Sản xuất tại Việt Nam (Hàng Việt)</option>
                                <option value="Import" {{ old('origin', $material->origin) === 'Import' ? 'selected' : '' }}>Nhập khẩu chính hãng</option>
                                <option value="Both" {{ old('origin', $material->origin) === 'Both' ? 'selected' : '' }}>Liên doanh / Cả hai</option>
                            </select>
                        </div>

                        <!-- Availability Select -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tồn kho / Thời gian cung ứng (Bước 4) <span class="text-rose-500">*</span></label>
                            <select name="availability" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="Stock" {{ old('availability', $material->availability) === 'Stock' ? 'selected' : '' }}>Có sẵn kho (Stock)</option>
                                <option value="2-3 weeks" {{ old('availability', $material->availability) === '2-3 weeks' ? 'selected' : '' }}>Đặt 2–3 tuần (2-3 weeks)</option>
                                <option value="6-8 weeks" {{ old('availability', $material->availability) === '6-8 weeks' ? 'selected' : '' }}>Chấp nhận 6–8 tuần (6-8 weeks)</option>
                            </select>
                        </div>

                        <!-- Finish Select with Inline Add Option -->
                        @php
                            $currentFinish = old('finish', $material->finish ?? '');
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
                             <textarea name="ai_standards" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" placeholder="Mỗi dòng 1 tiêu chuẩn áp dụng cho sản phẩm này:&#10;• QCVN 16:2023/BXD – Cường độ nén 40MPa...&#10;• QCVN 06:2022/BXD – Chống trượt R11...">{{ old('ai_standards', $material->ai_standards ?: strip_tags((string) $material->tech_description)) }}</textarea>
                             <p class="text-[10px] text-gray-400 mt-1">Hiển thị đồng thời tại mục "Căn cứ áp dụng" (cột trái) và phần mô tả kỹ thuật trên thẻ sản phẩm (cột phải).</p>
                         </div>

                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Yêu cầu cháy (Ô "Yêu cầu cháy")
                                 </label>
                                 <input type="text" name="fire_requirement" value="{{ old('fire_requirement', $material->fire_requirement) }}" placeholder="Ví dụ: Class A1 – Không cháy / Bfl-s1 cho thoát nạn" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>

                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Chỉ số / Thông số kỹ thuật tóm tắt
                                 </label>
                                 <input type="text" name="metrics" value="{{ old('metrics', $material->metrics ?: $material->tech_specs) }}" placeholder="Ví dụ: R11 • Nén 40MPa • Hấp thụ nước <3% • VOC 0g/L" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
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
                             <textarea name="ws_experience" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500" placeholder="Mỗi dòng 1 kinh nghiệm thực tế riêng của sản phẩm này:&#10;• Đã lát 12 sảnh chính với Terrazzo 60x60...&#10;• Sau 5-7 năm có thể mài phủ bóng lại như mới...">{{ old('ws_experience', $material->ws_experience ?: strip_tags((string) ($material->experience_description ?: $material->application))) }}</textarea>
                             <p class="text-[10px] text-gray-400 mt-1">Hiển thị đồng thời tại mục "Thực tế thi công" (cột trái) và phần nhận xét thực tế trên thẻ sản phẩm (cột phải).</p>
                         </div>

                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Lỗi thường gặp (Ô "Lỗi thường gặp")
                                 </label>
                                 <input type="text" name="common_errors" value="{{ old('common_errors', $material->common_errors) }}" placeholder="Ví dụ: Nền ẩm >75% phồng mép • Dùng keo giá rẻ bong mép" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>

                             <div>
                                 <label class="block text-xs font-semibold text-gray-800 mb-1">
                                     Mẹo thi công (Ô "Mẹo")
                                 </label>
                                 <input type="text" name="tips" value="{{ old('tips', $material->tips) }}" placeholder="Ví dụ: Đo ẩm <75% • Cán phẳng 2mm • Keo Wakol D3310" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                             </div>
                         </div>
                     </div>
                 </div>

               
            </div>

        </div>
    </form>

    <form id="delete-material-form" action="{{ route('admin.material-lab.destroy', $material) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

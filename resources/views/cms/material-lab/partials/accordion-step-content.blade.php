<!-- ========================================================================= -->
<!-- BƯỚC 2: VỊ TRÍ BỀ MẶT TƯƠNG THÍCH CỦA {{ $tLabel }}                       -->
<!-- ========================================================================= -->
<div class="space-y-2.5">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] inline-flex items-center justify-center font-bold">2</span>
                Vị trí bề mặt tương thích của: <span class="text-rose-600 font-bold ml-1">{{ $tLabel }}</span>
            </label>
            <p class="text-[11px] text-gray-500 mt-0.5 ml-6">Cố định 5 bề mặt — Bật/tắt các bề mặt áp dụng cho {{ $tLabel }}. Bấm vào thẻ để chọn bề mặt đang cấu hình khu vực ở Bước 3:</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="toggleAllSurfacesForCurrentTypo('{{ addslashes($tKey) }}')" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 px-2.5 py-1 rounded bg-blue-50 hover:bg-blue-100 transition-colors">
                <span x-text="allSurfs.every(s => isSurfaceEnabled(s.en || s, '{{ addslashes($tKey) }}')) ? 'Tắt 5 bề mặt' : 'Bật tất cả 5 bề mặt'"></span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 bg-gray-50/80 p-3.5 rounded-xl border border-gray-200">
        @foreach($surfaces as $surf)
            @php 
                $sKey = is_array($surf) ? ($surf['en'] ?? '') : $surf; 
                $sLabel = is_array($surf) ? ($surf['vi'] ?? $sKey) : $surf; 
            @endphp
            <div class="relative flex flex-col justify-between p-3 rounded-xl border transition-all select-none cursor-pointer text-xs"
                 :class="{
                     'bg-white border-blue-500 shadow-sm ring-2 ring-blue-500/25': isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}') && activeSurfaceTab === '{{ addslashes($sKey) }}',
                     'bg-white border-gray-200 hover:border-blue-300 hover:shadow-2xs': isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}') && activeSurfaceTab !== '{{ addslashes($sKey) }}',
                     'bg-gray-100/80 border-gray-300 ring-2 ring-gray-400/20': !isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}') && activeSurfaceTab === '{{ addslashes($sKey) }}',
                     'bg-gray-100/50 border-dashed border-gray-300 opacity-60 hover:opacity-90': !isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}') && activeSurfaceTab !== '{{ addslashes($sKey) }}'
                 }"
                 @click="selectSurfaceTab('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')">
                
                <!-- Hàng trên: Checkbox + Tên bề mặt -->
                <div class="flex items-start gap-2">
                    <input type="checkbox" 
                           :checked="isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')"
                           @click.stop="toggleSurface('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')"
                           class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 pointer-events-auto cursor-pointer">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-gray-900 truncate" :class="!isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}') && 'text-gray-500'">{{ $sLabel }}</div>
                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $sKey }}</div>
                    </div>
                </div>

                <!-- Hàng giữa: Trạng thái Áp dụng (Badge số KV hoặc Tắt) -->
                <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                    <template x-if="isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span x-text="countSelectedAreas('{{ addslashes($tKey) }}', '{{ addslashes($sKey) }}') === 0 ? 'Tất cả KV' : (countSelectedAreas('{{ addslashes($tKey) }}', '{{ addslashes($sKey) }}') + ' KV')"></span>
                        </span>
                    </template>
                    <template x-if="!isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')">
                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-500 bg-gray-200/60 px-1.5 py-0.5 rounded">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Chưa bật
                        </span>
                    </template>

                    <template x-if="!isSurfaceEnabled('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')">
                        <button type="button" 
                                @click.stop="toggleSurface('{{ addslashes($sKey) }}', '{{ addslashes($tKey) }}')" 
                                class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                            + Bật
                        </button>
                    </template>
                </div>

                <!-- Hàng dưới: Chỉ báo Tab đang mở ở Bước 3 -->
                <div class="mt-2 text-center">
                    <template x-if="activeSurfaceTab === '{{ addslashes($sKey) }}'">
                        <div class="py-0.5 px-1.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold flex items-center justify-center gap-1 border border-blue-200/60">
                            <span>▼ Đang cấu hình</span>
                        </div>
                    </template>
                    <template x-if="activeSurfaceTab !== '{{ addslashes($sKey) }}'">
                        <div class="text-[10px] text-gray-400 hover:text-gray-600 py-0.5">
                            Xem khu vực &rarr;
                        </div>
                    </template>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ========================================================================= -->
<!-- BƯỚC 3: KHU VỰC CHỨC NĂNG ÁP DỤNG (THEO CẶP LOẠI CÔNG TRÌNH + VỊ TRÍ BỀ MẶT) -->
<!-- ========================================================================= -->
<div class="pt-4 border-t border-gray-200/80 space-y-3.5">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <label class="block text-xs font-bold text-gray-800 flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] inline-flex items-center justify-center font-bold">3</span>
                Khu vực chức năng áp dụng (Bộ lọc Bước 3: "{{ $step3Title ?? 'Khu vực chức năng nào?' }}")
            </label>
            <p class="text-[11px] text-gray-500 mt-0.5 ml-6">
                Đang cấu hình cặp: 
                <span class="font-semibold text-rose-600">{{ $tLabel }}</span>
                &rarr;
                <span class="font-semibold text-blue-600" x-text="(allSurfs.find(s => (s.en || s) === activeSurfaceTab)?.vi) || activeSurfaceTab"></span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="applyAllAreasForCurrentPair()" 
                    class="text-[11px] font-semibold text-emerald-800 hover:text-emerald-900 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 transition-colors border border-emerald-200">
                Áp dụng tất cả khu vực
            </button>
            <button type="button" 
                    @click="selectAllAreasForCurrentPair()" 
                    class="text-[11px] font-semibold text-indigo-700 hover:text-indigo-800 px-2.5 py-1 rounded bg-indigo-50 hover:bg-indigo-100 transition-colors">
                Chọn tất cả thẻ
            </button>
            <button type="button" 
                    @click="deselectAllAreasForCurrentPair()" 
                    class="text-[11px] font-semibold text-gray-600 hover:text-gray-800 px-2.5 py-1 rounded bg-gray-100 hover:bg-gray-200 transition-colors">
                Xóa lựa chọn
            </button>
        </div>
    </div>

    <!-- Khung chi tiết Khu vực của Cặp đang Active -->
    <div class="p-4 bg-white rounded-xl border border-gray-200 space-y-3.5 shadow-2xs">
        
        <!-- Trạng thái bề mặt đang chọn -->
        <div class="flex items-center justify-between pb-2 border-b border-gray-100 text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" :class="isSurfaceEnabled(activeSurfaceTab, '{{ addslashes($tKey) }}') ? 'bg-emerald-500' : 'bg-gray-300'"></span>
                <span class="font-bold text-gray-800" x-text="'Bề mặt: ' + ((allSurfs.find(s => (s.en || s) === activeSurfaceTab)?.vi) || activeSurfaceTab)"></span>
                <template x-if="isSurfaceEnabled(activeSurfaceTab, '{{ addslashes($tKey) }}')">
                    <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-medium">✓ Đã bật ở Bước 2</span>
                </template>
                <template x-if="!isSurfaceEnabled(activeSurfaceTab, '{{ addslashes($tKey) }}')">
                    <span class="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full font-medium">Tự động kích hoạt khi chọn khu vực</span>
                </template>
            </div>
            <template x-if="!isSurfaceEnabled(activeSurfaceTab, '{{ addslashes($tKey) }}')">
                <button type="button" @click="toggleSurface(activeSurfaceTab, '{{ addslashes($tKey) }}')" class="text-xs font-semibold text-emerald-600 hover:underline">
                    + Bật bề mặt này
                </button>
            </template>
        </div>

        <!-- Danh sách Multi-select Tags khu vực của Cặp này -->
        <div>
            <div class="text-[11px] text-gray-500 mb-2">
                <template x-if="getSelectedAreasForCurrentPair().length === 0">
                    <span>Đang áp dụng: <strong>Tất cả khu vực</strong> của bề mặt này. Bấm vào từng thẻ bên dưới nếu chỉ muốn giới hạn ở một số khu vực cụ thể:</span>
                </template>
                <template x-if="getSelectedAreasForCurrentPair().length > 0">
                    <span>Đã giới hạn <strong class="text-emerald-700" x-text="getSelectedAreasForCurrentPair().length"></strong> khu vực cụ thể cho cặp này:</span>
                </template>
            </div>

            <div class="flex flex-wrap gap-2 pt-1">
                <template x-for="area in getCurrentPairAreas()" :key="activeTypoTab + '-' + activeSurfaceTab + '-' + area.en">
                    <button type="button"
                            @click="toggleArea(area.en)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all"
                            :class="isAreaSelected(area.en) ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-semibold ring-1 ring-emerald-600/30' : 'border-gray-200 bg-white text-gray-700 hover:border-emerald-300 hover:bg-emerald-50/20'">
                        <span x-text="isAreaSelected(area.en) ? '✓' : '+'" class="text-[10px]" :class="isAreaSelected(area.en) ? 'text-emerald-600 font-bold' : 'text-gray-400'"></span>
                        <span x-text="area.vi"></span>
                        <span class="text-[10px] text-gray-400 font-mono" x-text="'(' + area.en + ')'"></span>
                    </button>
                </template>
            </div>

            <!-- Ô thêm nhanh khu vực chức năng mới cho Cặp này -->
            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2">
                <div class="text-[11px] font-bold text-gray-700 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm khu vực khác cho cặp này:
                </div>
                <div class="flex-1 min-w-[200px] flex items-center gap-1.5">
                    <input type="text" 
                           x-model="newAreaInput" 
                           @keydown.enter.prevent="addCustomArea()" 
                           placeholder="Ví dụ: Phòng livestream, Phone booth, Góc yoga..." 
                           class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <button type="button" 
                            @click="addCustomArea()" 
                            class="shrink-0 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        + Thêm vào cặp này
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


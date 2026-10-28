@extends('cms.layouts.app')

@section('title', 'Cấu hình Material Lab')
@section('page-title', 'Cấu hình Material Lab')

@section('content')
<div class="max-w-6xl mx-auto space-y-6"
     x-data="materialLabConfigEditor()">
    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.material-lab.index') }}" class="hover:text-rose-600 transition-colors">Material Lab</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Cấu hình các bước Wizard</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Cấu hình Thư viện vật liệu (Step-by-Step)</h2>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý toàn bộ tiêu đề, câu hỏi và dữ liệu từng bước (Bước 1 &rarr; Bước 5) ngoài trang <a href="{{ route('material-lab') }}" target="_blank" class="text-rose-600 font-medium hover:underline">/material-lab &nearr;</a></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('material-lab') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.material-lab.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Danh sách vật liệu
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.material-lab.save-config') }}" method="POST" @submit="prepareSubmit()" class="space-y-6">
        @csrf

        <!-- Hidden JSON fields synced via Alpine before submit -->
        <input type="hidden" name="typologies_json" :value="JSON.stringify(typologies)">
        <input type="hidden" name="surfaces_json" :value="JSON.stringify(surfaces)">
        <input type="hidden" name="area_labels_json" :value="JSON.stringify(areaLabelsMap)">
        <input type="hidden" name="area_matrix_json" :value="JSON.stringify(areaMatrix)">
        <input type="hidden" name="lifecycles_json" :value="JSON.stringify(lifeCycles)">
        <input type="hidden" name="budgets_json" :value="JSON.stringify(budgetLevels)">

        <!-- ======================================================= -->
        <!-- PHẦN 0: TIÊU ĐỀ HEADER FRONTEND (/material-lab)          -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-900"></span>
                    Header Trang Frontend (/material-lab)
                </h3>
                <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                    Lưu cấu hình
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề chính (Page Title)</label>
                    <input type="text" name="material_lab_title" value="{{ old('material_lab_title', $title) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phụ đề phiên bản (Subtitle / Version)</label>
                    <input type="text" name="material_lab_subtitle" value="{{ old('material_lab_subtitle', $subtitle) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BƯỚC 1: LOẠI HÌNH CÔNG TRÌNH (TYPOLOGIES)                -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] flex items-center justify-center font-bold">1</span>
                    Bước 1: Loại hình công trình ("Công trình của bạn là gì?")
                </h3>
                <button type="button" @click="addTypology()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition-colors">
                    + Thêm loại hình công trình
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề câu hỏi Bước 1</label>
                    <input type="text" name="material_lab_step1_title" value="{{ old('material_lab_step1_title', $step1Title) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả hướng dẫn Bước 1</label>
                    <input type="text" name="material_lab_step1_desc" value="{{ old('material_lab_step1_desc', $step1Desc) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <div class="text-xs font-semibold text-gray-700 mb-2">Danh sách các Loại hình công trình hiển thị tại Bước 1:</div>
                <div class="space-y-2.5">
                    <template x-for="(item, idx) in typologies" :key="idx">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Icon</label>
                                <input type="text" x-model="item.icon" class="w-full px-2 py-1.5 text-center text-sm border border-gray-300 rounded-lg bg-white" placeholder="🏢">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Tên tiếng Việt (Hiển thị chính)</label>
                                <input type="text" x-model="item.vi" class="w-full px-2.5 py-1.5 text-xs font-semibold border border-gray-300 rounded-lg bg-white" placeholder="Văn phòng làm việc">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Mã tiếng Anh (Key)</label>
                                <input type="text" x-model="item.en" class="w-full px-2.5 py-1.5 text-xs font-mono border border-gray-300 rounded-lg bg-white" placeholder="Office">
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Mô tả ngắn</label>
                                <input type="text" x-model="item.desc" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white" placeholder="Không gian hiệu suất cao...">
                            </div>
                            <div class="md:col-span-1 flex justify-end pt-3">
                                <button type="button" @click="removeTypology(idx)" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Xóa loại hình">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BƯỚC 2: VỊ TRÍ / BỀ MẶT CẦN VẬT LIỆU (SURFACES)          -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] flex items-center justify-center font-bold">2</span>
                    Bước 2: Vị trí / Bề mặt ("Vị trí nào cần vật liệu?")
                </h3>
                <button type="button" @click="addSurface()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-colors">
                    + Thêm bề mặt mới
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề câu hỏi Bước 2</label>
                    <input type="text" name="material_lab_step2_title" value="{{ old('material_lab_step2_title', $step2Title) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả hướng dẫn Bước 2</label>
                    <input type="text" name="material_lab_step2_desc" value="{{ old('material_lab_step2_desc', $step2Desc) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <div class="text-xs font-semibold text-gray-700 mb-2">Danh sách các Bề mặt hiển thị tại Bước 2:</div>
                <div class="space-y-2.5">
                    <template x-for="(surf, idx) in surfaces" :key="idx">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="md:col-span-5">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Tên bề mặt (Tiếng Việt)</label>
                                <input type="text" x-model="surf.vi" @input="surf.viShort = surf.vi" class="w-full px-2.5 py-1.5 text-xs font-semibold border border-gray-300 rounded-lg bg-white" placeholder="Bề mặt sàn">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Mã hệ thống (Key tiếng Anh)</label>
                                <input type="text" x-model="surf.en" class="w-full px-2.5 py-1.5 text-xs font-mono border border-gray-300 rounded-lg bg-white" placeholder="Floor">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] text-gray-500 mb-0.5">Nhãn phụ tiếng Anh (enLabel)</label>
                                <input type="text" x-model="surf.enLabel" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white" placeholder="Floor">
                            </div>
                            <div class="md:col-span-1 flex justify-end pt-3">
                                <button type="button" @click="removeSurface(idx)" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Xóa bề mặt">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BƯỚC 3: KHU VỰC CHỨC NĂNG & MA TRẬN ÁP DỤNG             -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-bold">3</span>
                    Bước 3: Khu vực chức năng ("Khu vực chức năng nào?")
                </h3>
                <button type="button" @click="addAreaLabel()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors">
                    + Thêm khu vực chức năng mới
                </button>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề câu hỏi Bước 3</label>
                <input type="text" name="material_lab_step3_title" value="{{ old('material_lab_step3_title', $step3Title) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
            </div>

            <!-- Ma trận Khu vực theo Loại hình x Bề mặt -->
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Cấu hình Khu vực hiển thị theo từng Loại hình (Bước 1) &times; Bề mặt (Bước 2)</div>
                        <p class="text-[11px] text-gray-500">Chọn một Loại hình bên dưới để bật/tắt các khu vực chức năng tương ứng với từng Bề mặt:</p>
                    </div>
                </div>

                <!-- Tabs chọn Loại hình -->
                <div class="flex flex-wrap gap-1.5 border-b border-gray-200 pb-2">
                    <template x-for="typo in typologies" :key="typo.en">
                        <button type="button"
                                @click="activeMatrixTypo = typo.en; ensureMatrixTypo(typo.en)"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                :class="activeMatrixTypo === typo.en ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <span x-text="(typo.icon || '🏢') + ' ' + typo.vi"></span>
                        </button>
                    </template>
                </div>

                <!-- Bảng chọn Khu vực cho từng Bề mặt của Loại hình đang chọn -->
                <div class="space-y-3">
                    <template x-for="surf in surfaces" :key="surf.en">
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800" x-text="surf.vi + ' (' + surf.en + ')'"></span>
                                <span class="text-[11px] text-gray-500" x-text="getMatrixList(activeMatrixTypo, surf.en).length + ' khu vực được bật'"></span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="areaItem in areaLabelList" :key="areaItem.en">
                                    <button type="button"
                                            @click="toggleMatrixArea(activeMatrixTypo, surf.en, areaItem.en)"
                                            class="px-2.5 py-1 rounded-lg border text-[11px] transition-all"
                                            :class="getMatrixList(activeMatrixTypo, surf.en).includes(areaItem.en)
                                                ? 'bg-emerald-600 text-white border-emerald-600 font-semibold shadow-sm'
                                                : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                                        <span x-text="areaItem.vi"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Từ điển tên Khu vực (Area Labels) -->
            <div x-data="{ openDict: false }" class="border-t border-gray-100 pt-3">
                <button type="button" @click="openDict = !openDict" class="flex items-center justify-between w-full text-left py-1 text-xs font-bold text-gray-700 hover:text-rose-600">
                    <span>Từ điển tên các Khu vực chức năng (<span x-text="areaLabelList.length"></span> khu vực) — Nhấn để mở rộng / chỉnh sửa tên Tiếng Việt &amp; Tiếng Anh</span>
                    <span x-text="openDict ? '▲ Thu gọn' : '▼ Mở rộng'"></span>
                </button>
                <div x-show="openDict" class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 max-h-96 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <template x-for="(aItem, aIdx) in areaLabelList" :key="aIdx">
                        <div class="flex items-center gap-1.5 bg-white p-2 rounded-lg border border-gray-200">
                            <input type="text" x-model="aItem.vi" class="w-1/2 px-2 py-1 text-xs font-semibold border border-gray-300 rounded" placeholder="Tên tiếng Việt">
                            <input type="text" x-model="aItem.en" class="w-1/2 px-2 py-1 text-[11px] font-mono border border-gray-300 rounded" placeholder="Key tiếng Anh">
                            <button type="button" @click="removeAreaLabel(aIdx)" class="text-gray-400 hover:text-rose-600 px-1" title="Xóa">✕</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BƯỚC 4: NHU CẦU THỰC TẾ (ĐỘ BỀN & NGÂN SÁCH)            -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] flex items-center justify-center font-bold">4</span>
                    Bước 4: Nhu cầu thực tế ("Nhu cầu thực tế?")
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề câu hỏi Bước 4</label>
                    <input type="text" name="material_lab_step4_title" value="{{ old('material_lab_step4_title', $step4Title) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả hướng dẫn Bước 4</label>
                    <input type="text" name="material_lab_step4_desc" value="{{ old('material_lab_step4_desc', $step4Desc) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Các mốc Độ bền (LifeCycles) -->
                <div class="space-y-2.5">
                    <div class="text-xs font-bold text-gray-800">1. Các mốc Độ bền (Life Cycles)</div>
                    <template x-for="(lc, idx) in lifeCycles" :key="idx">
                        <div class="grid grid-cols-12 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200 items-center">
                            <div class="col-span-3">
                                <label class="block text-[10px] text-gray-500">Nhãn (VI)</label>
                                <input type="text" x-model="lc.vi" class="w-full px-2 py-1 text-xs font-semibold border border-gray-300 rounded bg-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] text-gray-500">Số năm</label>
                                <input type="number" x-model.number="lc.years" class="w-full px-2 py-1 text-xs border border-gray-300 rounded bg-white">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] text-gray-500">Nhãn (EN)</label>
                                <input type="text" x-model="lc.en" class="w-full px-2 py-1 text-xs border border-gray-300 rounded bg-white">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-[10px] text-gray-500">Ghi chú</label>
                                <input type="text" x-model="lc.note" class="w-full px-2 py-1 text-xs border border-gray-300 rounded bg-white">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Các mức Ngân sách (Budget Levels) -->
                <div class="space-y-2.5">
                    <div class="text-xs font-bold text-gray-800">2. Các mức Ngân sách (Budget Levels)</div>
                    <template x-for="(bg, idx) in budgetLevels" :key="idx">
                        <div class="grid grid-cols-12 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200 items-center">
                            <div class="col-span-2">
                                <label class="block text-[10px] text-gray-500">Cấp</label>
                                <input type="number" x-model.number="bg.level" readonly class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center font-mono">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] text-gray-500">Tên (VI)</label>
                                <input type="text" x-model="bg.vi" class="w-full px-2 py-1 text-xs font-semibold border border-gray-300 rounded bg-white">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-[10px] text-gray-500">Khoảng giá</label>
                                <input type="text" x-model="bg.vnd" class="w-full px-2 py-1 text-xs border border-gray-300 rounded bg-white">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] text-gray-500">Nhãn (EN)</label>
                                <input type="text" x-model="bg.en" class="w-full px-2 py-1 text-xs border border-gray-300 rounded bg-white">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BƯỚC 5: KHUNG TƯ VẤN CỘT TRÁI (TIÊU CHUẨN & KINH NGHIỆM) -->
        <!-- ======================================================= -->
        <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] flex items-center justify-center font-bold">5</span>
                    Bước 5: Nội dung Khung tổng hợp bên trái trang Kết quả (Tiêu chuẩn &amp; Kinh nghiệm Workspace)
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tab 1: Tiêu chuẩn kỹ thuật -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                    <div class="text-xs font-bold text-gray-900 uppercase">Tab 1: Tiêu chuẩn kỹ thuật</div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Căn cứ áp dụng (Mỗi dòng 1 ý)</label>
                        <textarea name="step5_insight[ai_standards]" rows="5" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white">{{ $step5Insight['ai_standards'] ?? '' }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Yêu cầu cháy (Sàn)</label>
                            <input type="text" name="step5_insight[ai_fire_floor]" value="{{ $step5Insight['ai_fire_floor'] ?? '' }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Yêu cầu cháy (Trần)</label>
                            <input type="text" name="step5_insight[ai_fire_ceiling]" value="{{ $step5Insight['ai_fire_ceiling'] ?? '' }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Yêu cầu cháy (Khác)</label>
                            <input type="text" name="step5_insight[ai_fire_other]" value="{{ $step5Insight['ai_fire_other'] ?? '' }}" class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chỉ số tiêu biểu</label>
                        <input type="text" name="step5_insight[ai_metrics]" value="{{ $step5Insight['ai_metrics'] ?? '' }}" class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                    </div>
                </div>

                <!-- Tab 2: Kinh nghiệm Workspace -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                    <div class="text-xs font-bold text-rose-700 uppercase">Tab 2: Kinh nghiệm Workspace</div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Thực tế thi công (Mỗi dòng 1 ý)</label>
                        <textarea name="step5_insight[ws_experience]" rows="5" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white">{{ $step5Insight['ws_experience'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Lỗi thường gặp</label>
                        <input type="text" name="step5_insight[ws_common_errors]" value="{{ $step5Insight['ws_common_errors'] ?? '' }}" class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mẹo thi công</label>
                        <input type="text" name="step5_insight[ws_tips]" value="{{ $step5Insight['ws_tips'] ?? '' }}" class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Lưu toàn bộ cấu hình Material Lab
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function materialLabConfigEditor() {
    const initialTypologies = @json($typologies ?? []);
    const initialSurfaces = @json($surfaces ?? []);
    const initialAreaMatrix = @json($areaMatrix ?? []);
    const initialAreaLabels = @json($areaLabels ?? []);
    const initialLifeCycles = @json($lifeCycles ?? []);
    const initialBudgetLevels = @json($budgetLevels ?? []);

    const areaList = Object.entries(initialAreaLabels).map(([en, vi]) => ({ en, vi }));

    return {
        typologies: initialTypologies,
        surfaces: initialSurfaces,
        areaMatrix: initialAreaMatrix,
        areaLabelList: areaList,
        lifeCycles: initialLifeCycles,
        budgetLevels: initialBudgetLevels,
        activeMatrixTypo: initialTypologies[0] ? initialTypologies[0].en : 'Office',

        get areaLabelsMap() {
            const map = {};
            this.areaLabelList.forEach(item => {
                const key = (item.en || '').trim();
                const val = (item.vi || '').trim();
                if (key) {
                    map[key] = val || key;
                }
            });
            return map;
        },

        addTypology() {
            this.typologies.push({
                icon: '🏢',
                vi: 'Loại hình mới',
                en: 'NewTypology_' + (this.typologies.length + 1),
                desc: 'Mô tả loại hình không gian...'
            });
        },

        removeTypology(idx) {
            if (this.typologies.length <= 1) {
                alert('Cần giữ lại ít nhất 1 loại hình công trình.');
                return;
            }
            this.typologies.splice(idx, 1);
            if (!this.typologies.some(t => t.en === this.activeMatrixTypo)) {
                this.activeMatrixTypo = this.typologies[0].en;
            }
        },

        addSurface() {
            this.surfaces.push({
                vi: 'Bề mặt mới',
                viShort: 'Bề mặt mới',
                en: 'NewSurface_' + (this.surfaces.length + 1),
                enLabel: 'Surface'
            });
        },

        removeSurface(idx) {
            if (this.surfaces.length <= 1) {
                alert('Cần giữ lại ít nhất 1 bề mặt.');
                return;
            }
            this.surfaces.splice(idx, 1);
        },

        addAreaLabel() {
            const newKey = 'Area_' + (this.areaLabelList.length + 1);
            const viName = prompt('Nhập tên Khu vực chức năng (Tiếng Việt):', 'Khu vực mới');
            if (!viName) return;
            const enName = prompt('Nhập mã Khu vực chức năng (Tiếng Anh):', newKey) || newKey;
            this.areaLabelList.push({ en: enName.trim(), vi: viName.trim() });
        },

        removeAreaLabel(idx) {
            this.areaLabelList.splice(idx, 1);
        },

        ensureMatrixTypo(typoKey) {
            if (!this.areaMatrix[typoKey] || Array.isArray(this.areaMatrix[typoKey])) {
                this.areaMatrix[typoKey] = {};
            }
        },

        getMatrixList(typoKey, surfKey) {
            if (!typoKey || !surfKey) return [];
            if (!this.areaMatrix[typoKey]) return [];
            if (!Array.isArray(this.areaMatrix[typoKey][surfKey])) return [];
            return this.areaMatrix[typoKey][surfKey];
        },

        toggleMatrixArea(typoKey, surfKey, areaKey) {
            if (!typoKey || !surfKey || !areaKey) return;
            this.ensureMatrixTypo(typoKey);
            if (!Array.isArray(this.areaMatrix[typoKey][surfKey])) {
                this.areaMatrix[typoKey][surfKey] = [];
            }
            const arr = this.areaMatrix[typoKey][surfKey];
            const idx = arr.indexOf(areaKey);
            if (idx > -1) {
                arr.splice(idx, 1);
            } else {
                arr.push(areaKey);
            }
        },

        prepareSubmit() {
            // Ensure viShort and enLabel are synced
            this.surfaces.forEach(s => {
                if (!s.viShort) s.viShort = s.vi;
                if (!s.enLabel) s.enLabel = s.en;
            });
        }
    };
}
</script>
@endsection

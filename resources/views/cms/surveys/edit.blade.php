@extends('cms.layouts.app')

@section('title', 'Quản lý câu hỏi khảo sát - ' . $survey->title)
@section('page-title', 'Quản lý Biểu mẫu & Câu hỏi')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, activeSection: 'A', editModal: false, editData: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900">{{ $survey->title }}</h2>
                @if($survey->is_active)
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Đang hoạt động</span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">Tạm đóng</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-1">Quản lý câu hỏi khảo sát, điều chỉnh nội dung đánh giá và thứ tự hiển thị.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('customer-survey.show') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Xem Frontend
            </a>
            <a href="{{ route('admin.surveys.responses.index', $survey->id) }}" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Xem phản hồi ({{ $survey->responses()->count() }})
            </a>
            <a href="{{ route('admin.surveys.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs rounded-r-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg space-y-1">
            <div class="font-bold">Có lỗi xảy ra:</div>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Edit General Survey Info -->
    <div class="bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            1. Thông tin chung về Biểu mẫu khảo sát
        </h3>

        <form action="{{ route('admin.surveys.update', $survey->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tiêu đề biểu mẫu <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $survey->title) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phụ đề (Subtitle)</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $survey->subtitle) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Lời ngỏ mở đầu (Intro note)</label>
                <textarea name="intro_note" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('intro_note', $survey->intro_note) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Thông điệp cảm ơn sau gửi (Thank you note)</label>
                    <input type="text" name="thank_you_note" value="{{ old('thank_you_note', $survey->thank_you_note) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Chân trang biểu mẫu (Footer text)</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $survey->footer_text) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $survey->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-semibold text-gray-800">Mở cổng tiếp nhận khảo sát từ khách hàng</span>
                </label>

                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Cập nhật thông tin biểu mẫu
                </button>
            </div>
        </form>
    </div>

    <!-- Questions Management by Sections -->
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b pb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    2. Danh sách câu hỏi khảo sát
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Phân loại theo Phần A (Chất lượng thi công), Phần B (Dịch vụ &amp; Quản lý), Điểm giới thiệu (NPS) và Góp ý mở.</p>
            </div>
            <button type="button" @click="openAddModal = true" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm câu hỏi mới
            </button>
        </div>

        <!-- Section Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b pb-2">
            <button type="button" @click="activeSection = 'A'" :class="activeSection === 'A' ? 'bg-rose-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3.5 py-1.5 text-xs rounded-lg transition-colors">
                Phần A: Thi công ({{ $questionsA->count() }})
            </button>
            <button type="button" @click="activeSection = 'B'" :class="activeSection === 'B' ? 'bg-rose-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3.5 py-1.5 text-xs rounded-lg transition-colors">
                Phần B: Dịch vụ &amp; Quản lý ({{ $questionsB->count() }})
            </button>
            <button type="button" @click="activeSection = 'NPS'" :class="activeSection === 'NPS' ? 'bg-rose-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3.5 py-1.5 text-xs rounded-lg transition-colors">
                Chỉ số NPS ({{ $questionsNps->count() }})
            </button>
            <button type="button" @click="activeSection = 'OPEN'" :class="activeSection === 'OPEN' ? 'bg-rose-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3.5 py-1.5 text-xs rounded-lg transition-colors">
                Câu hỏi mở ({{ $questionsOpen->count() }})
            </button>
        </div>

        <!-- Tables of Questions by Section -->
        <!-- SECTION A -->
        <div x-show="activeSection === 'A'" class="overflow-x-auto">
            @include('cms.surveys.partials.question-table', ['questions' => $questionsA, 'sectionName' => 'Phần A: Thi công'])
        </div>

        <!-- SECTION B -->
        <div x-show="activeSection === 'B'" class="overflow-x-auto">
            @include('cms.surveys.partials.question-table', ['questions' => $questionsB, 'sectionName' => 'Phần B: Dịch vụ'])
        </div>

        <!-- SECTION NPS -->
        <div x-show="activeSection === 'NPS'" class="overflow-x-auto">
            @include('cms.surveys.partials.question-table', ['questions' => $questionsNps, 'sectionName' => 'Chỉ số NPS'])
        </div>

        <!-- SECTION OPEN -->
        <div x-show="activeSection === 'OPEN'" class="overflow-x-auto">
            @include('cms.surveys.partials.question-table', ['questions' => $questionsOpen, 'sectionName' => 'Câu hỏi mở'])
        </div>
    </div>

    <!-- Modal Add Question -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="openAddModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-gray-900">Thêm câu hỏi mới vào khảo sát</h3>
                <button type="button" @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.surveys.questions.store', $survey->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phần (Section) <span class="text-rose-500">*</span></label>
                        <select name="section" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="A">Phần A: Thi công</option>
                            <option value="B">Phần B: Dịch vụ</option>
                            <option value="NPS">Chỉ số NPS</option>
                            <option value="OPEN">Câu hỏi mở</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mã câu hỏi (Code) <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="Ví dụ: A7, B5..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
                    <textarea name="question_text" rows="3" required placeholder="Nhập câu hỏi hiển thị cho khách hàng..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Loại câu hỏi <span class="text-rose-500">*</span></label>
                        <select name="question_type" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="rating_5">Thang điểm 1 - 5 sao</option>
                            <option value="rating_10_nps">Thang điểm 0 - 10 (NPS)</option>
                            <option value="textarea">Văn bản dài (Textarea)</option>
                            <option value="text">Văn bản ngắn (Text)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" value="10" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" checked class="rounded border-gray-300 text-rose-600">
                        <span class="text-xs font-semibold text-gray-800">Bắt buộc trả lời (*)</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">Hủy</button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm">Lưu câu hỏi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Question -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-gray-900">Chỉnh sửa câu hỏi</h3>
                <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('admin/surveys/questions') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phần (Section) <span class="text-rose-500">*</span></label>
                        <select name="section" x-model="editData.section" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="A">Phần A: Thi công</option>
                            <option value="B">Phần B: Dịch vụ</option>
                            <option value="NPS">Chỉ số NPS</option>
                            <option value="OPEN">Câu hỏi mở</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mã câu hỏi (Code) <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" x-model="editData.code" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
                    <textarea name="question_text" x-model="editData.question_text" rows="3" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Loại câu hỏi <span class="text-rose-500">*</span></label>
                        <select name="question_type" x-model="editData.question_type" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="rating_5">Thang điểm 1 - 5 sao</option>
                            <option value="rating_10_nps">Thang điểm 0 - 10 (NPS)</option>
                            <option value="textarea">Văn bản dài (Textarea)</option>
                            <option value="text">Văn bản ngắn (Text)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" x-model="editData.sort_order" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" :checked="editData.is_required" class="rounded border-gray-300 text-rose-600">
                        <span class="text-xs font-semibold text-gray-800">Bắt buộc trả lời (*)</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">Hủy</button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm">Lưu cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

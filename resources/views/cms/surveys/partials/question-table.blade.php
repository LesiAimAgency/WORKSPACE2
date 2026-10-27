<table class="w-full text-left text-xs">
    <thead class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-200">
        <tr>
            <th class="py-2.5 px-3 w-16">Thứ tự</th>
            <th class="py-2.5 px-3 w-20">Mã</th>
            <th class="py-2.5 px-3">Nội dung câu hỏi</th>
            <th class="py-2.5 px-3 w-36">Kiểu câu hỏi</th>
            <th class="py-2.5 px-3 w-24">Bắt buộc</th>
            <th class="py-2.5 px-3 w-28 text-right">Thao tác</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 text-gray-700">
        @forelse($questions as $q)
            <tr class="hover:bg-gray-50/60 transition-colors">
                <td class="py-2.5 px-3 font-mono font-bold text-gray-500">
                    #{{ $q->sort_order }}
                </td>
                <td class="py-2.5 px-3">
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold font-mono rounded">
                        {{ $q->code }}
                    </span>
                </td>
                <td class="py-2.5 px-3">
                    <div class="font-medium text-gray-900">{{ $q->question_text }}</div>
                </td>
                <td class="py-2.5 px-3">
                    @if($q->question_type === 'rating_5')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                            Thang điểm 1-5
                        </span>
                    @elseif($q->question_type === 'rating_10_nps')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                            Thang điểm 0-10 (NPS)
                        </span>
                    @elseif($q->question_type === 'textarea')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">
                            Văn bản dài (Ý kiến)
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                            {{ $q->question_type }}
                        </span>
                    @endif
                </td>
                <td class="py-2.5 px-3">
                    @if($q->is_required)
                        <span class="text-rose-600 font-bold">Có (*)</span>
                    @else
                        <span class="text-gray-400">Tùy chọn</span>
                    @endif
                </td>
                <td class="py-2.5 px-3 text-right">
                    <div class="inline-flex items-center gap-1.5">
                        <button type="button" @click="editData = { id: {{ $q->id }}, section: '{{ $q->section }}', code: '{{ addslashes($q->code) }}', question_text: '{{ addslashes($q->question_text) }}', question_type: '{{ $q->question_type }}', sort_order: {{ $q->sort_order }}, is_required: {{ $q->is_required ? 'true' : 'false' }} }; editModal = true" class="p-1 hover:bg-gray-100 text-blue-600 rounded" title="Sửa câu hỏi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('admin.surveys.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 hover:bg-red-50 text-red-600 rounded" title="Xóa câu hỏi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-6 text-center text-gray-400">
                    Chưa có câu hỏi nào trong {{ $sectionName }}.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

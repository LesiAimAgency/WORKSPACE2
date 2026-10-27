@extends('employee.layouts.app')

@section('title', 'Hợp đồng của tôi')
@section('page-title', 'Hợp đồng của tôi')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Mã Hợp đồng</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Website/Dự án</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Ngày bắt đầu</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Ngày kết thúc</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Trạng thái</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($contracts as $contract)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <span class="font-mono font-bold text-blue-600">{{ $contract->full_code }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $contract->website->slug }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->start_date->format('d/m/Y') }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->end_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $contract->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $contract->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có hợp đồng nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($contracts->isNotEmpty())
    <div class="p-6 bg-gray-50 border-t">
        <h3 class="text-sm font-bold text-gray-700 mb-2">Ghi chú:</h3>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>• Hợp đồng này được gán cho bạn bởi Super Admin</li>
            <li>• Liên hệ quản lý trực tiếp nếu có thắc mắc về hợp đồng</li>
        </ul>
    </div>
    @endif
</div>
@endsection

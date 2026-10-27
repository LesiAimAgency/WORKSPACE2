@extends('employee.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Thông tin Nhân viên</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-gray-600">Mã NV:</span> <span class="font-bold text-blue-600">{{ $employee->code }}</span></div>
            <div><span class="text-gray-600">Bộ phận:</span> <span class="font-semibold">{{ strtoupper($employee->department) }}</span></div>
            <div><span class="text-gray-600">Vị trí:</span> {{ $employee->position ?? '-' }}</div>
            <div><span class="text-gray-600">Quản lý:</span> {{ $employee->manager?->name ?? '-' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Todo</p>
                    <p class="text-3xl font-bold text-gray-600">{{ $taskStats['todo'] }}</p>
                </div>
                <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $taskStats['in_progress'] }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Review</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $taskStats['review'] }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Done</p>
                    <p class="text-3xl font-bold text-green-600">{{ $taskStats['done'] }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Task Gần Đây</h3>
            <div class="space-y-3">
                @forelse($myTasks as $task)
                <div class="border-l-4 {{ $task->status == 'done' ? 'border-green-500' : ($task->status == 'in_progress' ? 'border-blue-500' : 'border-gray-300') }} pl-4 py-2">
                    <p class="font-medium text-gray-900">{{ $task->title }}</p>
                    <p class="text-sm text-gray-600">{{ $task->project->name }}</p>
                    <span class="text-xs px-2 py-1 rounded-full {{ $task->status == 'done' ? 'bg-green-100 text-green-800' : ($task->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Chưa có task nào</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Hợp Đồng của Tôi</h3>
            <div class="space-y-3">
                @forelse($myContracts as $contract)
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <p class="font-mono font-bold text-blue-600">{{ $contract->full_code }}</p>
                    <p class="text-sm text-gray-600">{{ $contract->website->slug }}</p>
                    <p class="text-xs text-gray-500">{{ $contract->start_date->format('d/m/Y') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Chưa có hợp đồng nào</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@extends('employee.layouts.app')

@section('title', 'Task của tôi')
@section('page-title', 'Task của tôi')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Task</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Dự án</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Ưu tiên</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Deadline</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Trạng thái</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($tasks as $task)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $task->title }}</p>
                    @if($task->description)
                    <p class="text-sm text-gray-600">{{ Str::limit($task->description, 50) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $task->project->name }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $task->priority == 'high' ? 'bg-red-100 text-red-800' : ($task->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-sm rounded-full {{ $task->status == 'done' ? 'bg-green-100 text-green-800' : ($task->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : ($task->status == 'review' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <form method="POST" action="{{ route('employee.tasks.update-status', $task) }}">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>Todo</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có task nào được gán</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

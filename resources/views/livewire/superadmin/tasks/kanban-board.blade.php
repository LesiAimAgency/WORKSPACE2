<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use function Livewire\Volt\{state, computed, rules, mount};

state(['editingTaskId' => null, 'title' => '', 'description' => '', 'dev_id' => '', 'deadline' => '', 'status' => '', 'result_notes' => '', 'project_id' => '']);

$tasks = computed(function () {
    $user = auth()->user();
    $query = Task::with(['project', 'dev']);
    
    if (($user->hasPermission('update-tasks-progress') || $user->role === 'dev' || $user->hasRole('dev')) && !$user->isSuperAdmin() && !$user->hasPermission('manage-tasks')) {
        $query->where('dev_id', $user->id);
    } elseif (($user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks')) && !$user->isSuperAdmin()) {
        $query->whereHas('project', function($q) use ($user) {
            $q->where('admin_id', $user->id);
        });
    }

    return $query->latest()->get();
});

$projects = computed(function () {
    $user = auth()->user();
    if (($user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks')) && !$user->isSuperAdmin()) {
        return Project::where('admin_id', $user->id)->get();
    }
    return Project::all();
});

$devs = computed(function () {
    return User::whereHas('roles', function($q) {
        $q->where('name', 'dev');
    })->orWhere('role', 'dev')->get();
});

$updateTaskStatus = function ($taskId, $newStatus) {
    $user = auth()->user();
    $task = Task::findOrFail($taskId);
    
    $canManage = $user->isSuperAdmin() || $user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks');
    $canUpdateProgress = ($user->hasPermission('update-tasks-progress') || $user->role === 'dev' || $user->hasRole('dev')) && !$user->isSuperAdmin() && !$user->hasPermission('manage-tasks');

    if (!$canManage && !$canUpdateProgress) {
        return;
    }

    if ($canUpdateProgress && !in_array($newStatus, ['todo', 'in_progress', 'review'])) {
        return;
    }

    $task->update(['status' => $newStatus]);
};

$editTask = function ($taskId) {
    $user = auth()->user();
    $canManage = $user->isSuperAdmin() || $user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks');
    $canUpdateProgress = ($user->hasPermission('update-tasks-progress') || $user->role === 'dev' || $user->hasRole('dev')) && !$user->isSuperAdmin() && !$user->hasPermission('manage-tasks');

    if (!$canManage && !$canUpdateProgress) {
        return;
    }

    $task = Task::findOrFail($taskId);
    $this->editingTaskId = $task->id;
    $this->title = $task->title;
    $this->description = $task->description;
    $this->dev_id = $task->dev_id;
    $this->project_id = $task->project_id;
    $this->deadline = $task->deadline ? $task->deadline->format('Y-m-d') : '';
    $this->status = $task->status;
    $this->result_notes = $task->result_notes;
    
    $this->dispatch('open-modal');
};

$saveTask = function () {
    $user = auth()->user();
    $task = Task::findOrFail($this->editingTaskId);

    $canManage = $user->isSuperAdmin() || $user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks');
    $canUpdateProgress = ($user->hasPermission('update-tasks-progress') || $user->role === 'dev' || $user->hasRole('dev')) && !$user->isSuperAdmin() && !$user->hasPermission('manage-tasks');

    if ($canManage) {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dev_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'deadline' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,review,rework,done',
            'result_notes' => 'nullable|string',
        ]);
        $task->update($validated);
    } elseif ($canUpdateProgress) {
        $validated = $this->validate([
            'status' => 'required|in:todo,in_progress,review',
            'result_notes' => 'nullable|string',
        ]);
        $task->update($validated);
    }
    
    $this->dispatch('close-modal');
    $this->reset(['editingTaskId', 'title', 'description', 'dev_id', 'project_id', 'deadline', 'status', 'result_notes']);
};

?>

<div x-data="{ dragging: null, showModal: false }" @open-modal.window="showModal = true" @close-modal.window="showModal = false">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Kanban Style Columns -->
        @php
            $statuses = [
                'todo' => ['label' => 'Cần làm', 'color' => 'bg-gray-50 border-gray-200', 'badge' => 'bg-gray-200 text-gray-800'],
                'in_progress' => ['label' => 'Đang làm', 'color' => 'bg-blue-50 border-blue-100', 'badge' => 'bg-blue-200 text-blue-800'],
                'review' => ['label' => 'Chờ duyệt', 'color' => 'bg-yellow-50 border-yellow-100', 'badge' => 'bg-yellow-200 text-yellow-800'],
                'done' => ['label' => 'Hoàn thành', 'color' => 'bg-green-50 border-green-100', 'badge' => 'bg-green-200 text-green-800'],
            ];
        @endphp

        @foreach($statuses as $key => $statusInfo)
        <div class="rounded-xl border {{ $statusInfo['color'] }} p-4 flex flex-col h-full min-h-[500px]"
             wire:key="column-{{ $key }}"
             @dragover.prevent="$event.dataTransfer.dropEffect='move'"
             @drop="if(dragging) { $wire.updateTaskStatus(dragging, '{{ $key }}'); dragging = null; }"
        >
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200/50">
                <h3 class="font-bold text-gray-700">{{ $statusInfo['label'] }}</h3>
                @php
                    $count = $this->tasks->filter(function($t) use ($key) { return $t->status == $key || ($key == 'todo' && $t->status == 'rework'); })->count();
                @endphp
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $statusInfo['badge'] }}">{{ $count }}</span>
            </div>
            
            <div class="flex-1 space-y-4">
                @foreach($this->tasks as $task)
                    @if($task->status == $key || ($key == 'todo' && $task->status == 'rework'))
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-move relative group"
                         wire:key="task-{{ $task->id }}"
                         draggable="true"
                         @dragstart="dragging = {{ $task->id }}; $event.dataTransfer.effectAllowed = 'move';"
                    >
                        @if($task->status == 'rework')
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-[10px] font-bold uppercase rounded mb-2">Làm lại</span>
                        @endif
                        
                        <h4 class="font-semibold text-gray-900 leading-tight mb-2 pr-6">{{ $task->title }}</h4>
                        
                        <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-3">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            {{ $task->project->name ?? 'N/A' }}
                        </div>
                        
                        <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-50">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-2 uppercase" title="{{ $task->dev->name ?? 'N/A' }}">
                                    {{ substr($task->dev->name ?? 'N', 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-gray-500 truncate max-w-[80px]">{{ $task->dev->name ?? 'Chưa gán' }}</span>
                            </div>
                            
                            <button type="button" draggable="false" onmousedown="event.stopPropagation()" wire:click.prevent="editTask({{ $task->id }})" class="p-2 -mr-2 text-xs font-medium text-[#001B4E] hover:text-[#002D80] hover:bg-gray-100 rounded flex items-center cursor-pointer relative z-10 transition-colors">
                                Chi tiết
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Edit Task Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2" id="modal-title">
                                Sửa Task
                            </h3>
                            <div class="mt-2 space-y-4 text-left">
                                @php
                                    $user = auth()->user();
                                    $canManage = $user->isSuperAdmin() || $user->hasPermission('manage-tasks') || $user->hasPermission('review-tasks');
                                @endphp
                                @if($canManage)
                                    <div>
                                        <x-form.label value="Tiêu đề Task" required="true" />
                                        <input type="text" wire:model="title" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm" required />
                                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <x-form.label value="Mô tả công việc" />
                                        <textarea wire:model="description" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm" rows="3"></textarea>
                                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-form.label value="Dự án" required="true" />
                                            <select wire:model="project_id" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm">
                                                <option value="">Chọn Dự Án</option>
                                                @foreach($this->projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <x-form.label value="Giao cho (Dev)" required="true" />
                                            <select wire:model="dev_id" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm">
                                                <option value="">Chọn Dev</option>
                                                @foreach($this->devs as $dev)
                                                    <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('dev_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <x-form.label value="Thời hạn (Deadline)" />
                                        <input type="date" wire:model="deadline" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm" />
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <h4 class="font-bold text-lg text-gray-900 mb-2">{{ $title }}</h4>
                                        <div class="text-sm text-gray-700 mb-4 whitespace-pre-line">{{ $description ?: 'Không có mô tả chi tiết.' }}</div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Dự án:</span>
                                                <span class="font-medium ml-1">
                                                    {{ $this->projects->firstWhere('id', $project_id)?->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Deadline:</span>
                                                <span class="font-medium ml-1 text-red-600">{{ $deadline ? \Carbon\Carbon::parse($deadline)->format('d/m/Y') : 'Không có' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <x-form.label value="Trạng thái" />
                                    <select wire:model="status" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm">
                                        @if(!$canManage)
                                            <option value="todo">Cần làm</option>
                                            <option value="in_progress">Đang làm</option>
                                            <option value="review">Chờ duyệt</option>
                                        @else
                                            <option value="todo">Cần làm</option>
                                            <option value="in_progress">Đang làm</option>
                                            <option value="review">Chờ duyệt</option>
                                            <option value="rework">Làm lại</option>
                                            <option value="done">Hoàn thành</option>
                                        @endif
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <x-form.label value="Kết quả / Notes" />
                                    <textarea wire:model="result_notes" class="w-full border-gray-300 focus:border-[#001B4E] focus:ring focus:ring-[#001B4E] focus:ring-opacity-50 rounded-md shadow-sm" rows="4" placeholder="Ghi chú hoặc link kết quả..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" wire:click="saveTask" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-[#001B4E] text-base font-medium text-white hover:bg-[#002D80] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#001B4E] sm:ml-3 sm:w-auto sm:text-sm">
                        Lưu Thay Đổi
                    </button>
                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#001B4E] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

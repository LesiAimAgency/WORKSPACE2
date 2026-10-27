<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#001B4E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
            </svg>
            Kanban Board & Tiến độ công việc
        </h3>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 overflow-x-auto pb-4">
        @foreach($columns as $status => $column)
        <div class="flex-1 min-w-[300px] bg-gray-50 rounded-xl border border-gray-200 flex flex-col max-h-[800px]"
             ondragover="event.preventDefault(); this.classList.add('bg-{{ $column['color'] }}-50');"
             ondragleave="this.classList.remove('bg-{{ $column['color'] }}-50');"
             ondrop="event.preventDefault(); this.classList.remove('bg-{{ $column['color'] }}-50'); const taskId = event.dataTransfer.getData('text/plain'); @this.call('updateTaskStatus', taskId, '{{ $status }}');">
            
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-xl sticky top-0 z-10 shadow-sm">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-{{ $column['color'] }}-500"></span>
                    {{ $column['title'] }}
                </h4>
                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">{{ count($column['tasks']) }}</span>
            </div>

            <div class="p-4 flex-1 overflow-y-auto space-y-3">
                @forelse($column['tasks'] as $task)
                <div draggable="true"
                     ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}'); event.target.classList.add('opacity-50');"
                     ondragend="event.target.classList.remove('opacity-50');"
                     class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 hover:shadow-md hover:border-{{ $column['color'] }}-300 transition-all cursor-move group relative">
                     
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded">#{{ $task->id }}</span>
                        @if(config('features.gold_enabled') && $task->gold)
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" fill="#FBBF24"/><text x="10" y="13" font-size="8" font-weight="bold" fill="#78350F" text-anchor="middle">G</text></svg>
                            {{ $task->gold }}
                        </span>
                        @endif
                    </div>
                    
                    <h5 class="font-medium text-gray-900 text-sm mb-3 leading-snug">{{ $task->title }}</h5>
                    
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-2">
                            @if($task->assignedUser)
                            <div class="w-6 h-6 rounded-full bg-[#001B4E] text-white flex items-center justify-center text-xs font-bold" title="{{ $task->assignedUser->name }}">
                                {{ substr($task->assignedUser->name, 0, 1) }}
                            </div>
                            <span class="text-xs text-gray-500">{{ explode(' ', $task->assignedUser->name)[0] }}</span>
                            @else
                            <span class="text-xs text-gray-400 italic">Chưa phân</span>
                            @endif
                        </div>
                        
                        @if($task->deadline)
                        @php
                            $isOverdue = $task->deadline->isPast() && $task->status !== 'completed';
                        @endphp
                        <span class="text-[11px] font-medium {{ $isOverdue ? 'text-red-600 bg-red-50' : 'text-gray-500' }} px-1.5 py-0.5 rounded flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $task->deadline->format('d/m') }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="h-24 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                    Kéo thả task vào đây
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

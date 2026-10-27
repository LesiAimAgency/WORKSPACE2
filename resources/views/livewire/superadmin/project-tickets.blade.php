<div class="mt-8 space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h2 class="text-xl font-bold text-[#001B4E]">Trao đổi & Xử lý (Tickets)</h2>
    </div>

    <!-- Create New Ticket -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-md font-semibold text-gray-800 mb-3">Tạo Ticket mới</h3>
        <form wire:submit.prevent="createTicket" class="space-y-3">
            <div>
                <input type="text" wire:model="newTicketTitle" placeholder="Tiêu đề (VD: Lỗi giao diện, Link test...)" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                @error('newTicketTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <textarea wire:model="newTicketDescription" rows="2" placeholder="Nội dung chi tiết..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm"></textarea>
                @error('newTicketDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Tạo Ticket
                </button>
            </div>
        </form>
    </div>

    <!-- Ticket List -->
    <div class="space-y-6">
        @forelse($tickets as $ticket)
            @php
                $isOverdue = $ticket->status !== 'closed' && $ticket->last_reply_at && $ticket->last_reply_at->diffInDays(now()) >= 2;
                $borderColor = $isOverdue ? 'border-red-400' : 'border-gray-200';
                $bgColor = $isOverdue ? 'bg-red-50' : 'bg-white';
            @endphp
            <div class="{{ $bgColor }} {{ $borderColor }} border rounded-lg shadow-sm overflow-hidden" wire:key="ticket-{{ $ticket->id }}">
                <!-- Ticket Header -->
                <div class="px-4 py-3 border-b flex justify-between items-start bg-gray-50/50">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $ticket->title }}</h4>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                            <span class="font-medium text-gray-700">{{ $ticket->creator->name }} ({{ strtoupper($ticket->creator->role) }})</span>
                            <span>•</span>
                            <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            
                            @if($isOverdue)
                                <span>•</span>
                                <span class="text-red-600 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Quá hạn phản hồi (>2 ngày)
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full 
                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : 
                               ($ticket->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                            @if($ticket->status === 'open') Mở
                            @elseif($ticket->status === 'processing') Đang xử lý
                            @elseif($ticket->status === 'replying') Đang trả lời
                            @elseif($ticket->status === 'closed') Đã xử lý xong
                            @endif
                        </span>
                        
                        @if($ticket->status !== 'closed' && (auth()->user()->role === 'dev' || auth()->user()->hasRole('dev') || auth()->user()->isSuperAdmin()))
                            <button wire:click="closeTicket({{ $ticket->id }})" class="text-xs text-red-600 hover:text-red-800 font-medium border border-red-200 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                Đóng Ticket
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ticket Content -->
                @if($ticket->description)
                    <div class="px-4 py-3 text-sm text-gray-700 whitespace-pre-line">
                        {{ $ticket->description }}
                    </div>
                @endif

                <!-- Replies -->
                @if($ticket->replies->count() > 0)
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 space-y-4">
                        @foreach($ticket->replies as $reply)
                            <div class="flex flex-col">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="font-bold text-sm text-gray-800">{{ $reply->user->name }}</span>
                                    <span class="text-xs font-medium px-1.5 py-0.5 rounded bg-gray-200 text-gray-700">{{ strtoupper($reply->user->role) }}</span>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-sm text-gray-700 whitespace-pre-line pl-2 border-l-2 {{ $reply->user->role === 'dev' ? 'border-blue-300' : 'border-orange-300' }}">
                                    {{ $reply->content }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Reply Form -->
                @if($ticket->status !== 'closed')
                    <div class="px-4 py-3 border-t border-gray-100 bg-white">
                        <form wire:submit.prevent="replyToTicket({{ $ticket->id }})" class="flex gap-2 items-start">
                            <div class="flex-1">
                                <textarea wire:model="replyContents.{{ $ticket->id }}" rows="1" placeholder="Nhập phản hồi..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm py-2"></textarea>
                                @error('replyContents.'.$ticket->id) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                                Phản hồi
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 italic bg-gray-50 rounded-lg border border-dashed border-gray-300">
                Chưa có ticket trao đổi nào.
            </div>
        @endforelse
    </div>
</div>

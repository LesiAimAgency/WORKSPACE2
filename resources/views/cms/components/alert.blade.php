{{-- Alert Component - Reusable across the system --}}
@if(session('alert'))
<div class="mb-6 p-4 rounded-lg border-l-4 {{ session('alert.type') === 'success' ? 'bg-green-50 border-green-500 text-green-800' : (session('alert.type') === 'error' ? 'bg-red-50 border-red-500 text-red-800' : (session('alert.type') === 'warning' ? 'bg-yellow-50 border-yellow-500 text-yellow-800' : 'bg-blue-50 border-blue-500 text-blue-800')) }}" 
     x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            @if(session('alert.type') === 'success')
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif(session('alert.type') === 'error')
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif(session('alert.type') === 'warning')
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            @else
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
            <div>
                <p class="font-medium">{{ session('alert.message') }}</p>
                @if(session('alert.details'))
                    <p class="text-sm mt-1 opacity-90">{{ session('alert.details') }}</p>
                @endif
            </div>
        </div>
        <button @click="show = false" class="flex-shrink-0 ml-4 text-current opacity-70 hover:opacity-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
// Auto-hide success alerts after 5 seconds
@if(session('alert.type') === 'success')
setTimeout(() => {
    const alertElement = document.querySelector('[x-data*="show: true"]');
    if (alertElement && alertElement.__x) {
        alertElement.__x.$data.show = false;
    }
}, 5000);
@endif
</script>
@endif

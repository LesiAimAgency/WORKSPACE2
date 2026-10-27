@php
    $config = setting('fake_notifications_config', [
        'enabled' => false,
        'show_desktop' => true,
        'show_mobile' => true,
        'position' => 'bottom-left'
    ]);
    
    $notifications = setting('fake_notifications', []);
    
    // Nếu không có notifications hoặc không enabled thì không hiển thị
    if (!($config['enabled'] ?? false) || empty($notifications)) {
        return;
    }
    
    // Position classes
    $positionClasses = match($config['position'] ?? 'bottom-left') {
        'bottom-left' => 'bottom-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'top-left' => 'top-20 left-4',
        'top-right' => 'top-20 right-4',
        default => 'bottom-4 left-4'
    };
    
    // Responsive classes
    $responsiveClasses = '';
    if (!($config['show_mobile'] ?? true)) {
        $responsiveClasses .= ' hidden md:block';
    }
    if (!($config['show_desktop'] ?? true)) {
        $responsiveClasses .= ' md:hidden';
    }
@endphp

<div id="fake-notification-container" 
     class="fixed {{ $positionClasses }} {{ $responsiveClasses }} z-50 max-w-sm"
     x-data="fakeNotificationSystem()"
     x-init="startNotifications()">
    
    <template x-if="currentNotification">
        <div x-show="showNotification"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="bg-white rounded-lg shadow-xl border border-gray-100 p-4 flex items-start gap-3">
            
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900" x-text="currentNotification.customer"></p>
                <p class="text-xs text-gray-500 mt-0.5" x-text="currentNotification.address"></p>
                <p class="text-sm text-gray-700 mt-1" x-text="currentNotification.content"></p>
                <p class="text-xs text-gray-400 mt-1" x-text="timeAgo"></p>
            </div>
            
            <!-- Close button -->
            <button @click="hideNotification()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function fakeNotificationSystem() {
    const notifications = @json($notifications);
    const config = @json($config);
    
    return {
        notifications: notifications,
        config: config,
        currentNotification: null,
        showNotification: false,
        currentIndex: 0,
        timeAgo: '',
        intervalId: null,
        
        startNotifications() {
            if (this.notifications.length === 0) return;
            
            // Shuffle notifications for randomness
            this.shuffleArray(this.notifications);
            
            // Initial delay from config (default 3 seconds)
            const initialDelay = (this.config.initial_delay || 3) * 1000;
            setTimeout(() => {
                this.showNextNotification();
            }, initialDelay);
        },
        
        showNextNotification() {
            if (this.notifications.length === 0) return;
            
            // Get current notification
            this.currentNotification = this.notifications[this.currentIndex];
            this.timeAgo = this.getRandomTimeAgo();
            this.showNotification = true;
            
            // Display time from config or notification setting (default 5 seconds)
            const displayTime = (this.config.display_time || this.currentNotification.time || 5) * 1000;
            setTimeout(() => {
                this.hideNotification();
            }, displayTime);
            
            // Move to next notification
            this.currentIndex = (this.currentIndex + 1) % this.notifications.length;
            
            // Calculate next delay
            let nextDelay = (this.config.interval_time || 10) * 1000;
            
            // Add randomness if enabled (±50%)
            if (this.config.random_interval !== false) {
                const variance = nextDelay * 0.5;
                nextDelay = nextDelay - variance + (Math.random() * variance * 2);
            }
            
            // Schedule next notification
            setTimeout(() => {
                this.showNextNotification();
            }, nextDelay);
        },
        
        hideNotification() {
            this.showNotification = false;
        },
        
        getRandomTimeAgo() {
            const times = [
                'Vừa xong',
                '1 phút trước',
                '2 phút trước',
                '3 phút trước',
                '5 phút trước',
                '10 phút trước',
                '15 phút trước',
                '30 phút trước',
                '1 giờ trước'
            ];
            return times[Math.floor(Math.random() * times.length)];
        },
        
        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }
    }
}
</script>

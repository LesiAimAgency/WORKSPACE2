@php
    // Lấy popup settings
    $popupSetting = setting('popup', []);
    $popup = is_array($popupSetting) ? $popupSetting : (json_decode($popupSetting, true) ?: []);
    
    // Kiểm tra popup có được bật không
    $enabled = !empty($popup['enabled']);
    
    // Nếu không enabled thì không hiển thị
    if (!$enabled) {
        return;
    }
    
    // Lấy các config
    $delay = (int)($popup['delay'] ?? 3);
    $frequency = $popup['frequency'] ?? 'once';
    $position = $popup['position'] ?? 'center';
    $title = $popup['title'] ?? '';
    $subtitle = $popup['subtitle'] ?? '';
    $bgColor = $popup['bg_color'] ?? '#ffffff';
    $textColor = $popup['text_color'] ?? '#1f2937';
    $buttonText = $popup['button_text'] ?? 'Gửi';
    $buttonColor = $popup['button_color'] ?? '#2563eb';
    $image = $popup['image'] ?? '';
    $showOnMobile = $popup['show_mobile'] ?? true;
    $showOnDesktop = $popup['show_desktop'] ?? true;
    $customContent = $popup['content'] ?? '';
    $formId = $popup['form_id'] ?? null;
    
    // Lấy form nếu có
    $form = null;
    if ($formId !== null && $formId !== '') {
        $formsSetting = setting('forms', []);
        $formsArray = is_array($formsSetting) ? $formsSetting : (json_decode($formsSetting, true) ?: []);
        $form = $formsArray[$formId] ?? null;
    }
    
    // Position classes
    $positionClasses = match($position) {
        'center' => 'items-center justify-center',
        'bottom-left' => 'items-end justify-start pb-4 pl-4',
        'bottom-right' => 'items-end justify-end pb-4 pr-4',
        'top-left' => 'items-start justify-start pt-20 pl-4',
        'top-right' => 'items-start justify-end pt-20 pr-4',
        default => 'items-center justify-center'
    };
    
    // Responsive classes
    $responsiveClasses = '';
    if (!$showOnMobile) {
        $responsiveClasses .= ' hidden md:flex';
    }
    if (!$showOnDesktop) {
        $responsiveClasses .= ' md:hidden';
    }
    
    // Unique key cho localStorage
    $popupKey = 'popup_shown_' . md5(json_encode($popup));
@endphp

<style>
[x-cloak] { display: none; }
</style>

<!-- Popup Container -->
<div id="popup-container" 
     class="fixed inset-0 bg-black/50 z-[9999] flex {{ $positionClasses }} {{ $responsiveClasses }}"
     x-data="popupController()"
     x-show="isVisible"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="close()"
     @click.self="close()">
    
    <!-- Popup Box -->
    <div class="relative w-full max-w-md rounded-2xl shadow-2xl overflow-hidden m-4"
         style="background-color: {{ $bgColor }};"
         x-show="isVisible"
         x-transition:enter="transition ease-out duration-300 delay-100"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         @click.stop>
        
        <!-- Close Button -->
        <button @click="close()" 
                class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 transition-colors">
            <svg class="w-5 h-5" style="color: {{ $textColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        @if($customContent)
            {{-- Custom HTML Content --}}
            <div class="p-6">
                {!! $customContent !!}
            </div>
        @else
            {{-- Default Content --}}
            @if($image)
                <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-40 object-cover">
            @endif
            
            <div class="p-6" style="color: {{ $textColor }};">
                @if($title)
                    <h3 class="text-xl font-bold mb-2">{{ $title }}</h3>
                @endif
                
                @if($subtitle)
                    <p class="text-sm opacity-80 mb-4">{{ $subtitle }}</p>
                @endif
                
                @if($form && !empty($form['fields']))
                    <form @submit.prevent="submitForm($event)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="form_id" value="{{ $formId }}">
                        <input type="hidden" name="form_name" value="{{ $form['name'] ?? 'Popup Form' }}">
                        <input type="hidden" name="project_id" value="{{ current_project()?->id }}">
                        
                        @foreach($form['fields'] ?? [] as $index => $field)
                            @php
                                $fieldType = $field['type'] ?? 'text';
                                $fieldLabel = $field['label'] ?? '';
                                $fieldPlaceholder = $field['placeholder'] ?? $fieldLabel;
                                $fieldRequired = $field['required'] ?? false;
                            @endphp
                            
                            @if($fieldType === 'textarea')
                                <textarea name="fields[{{ $fieldLabel }}]" 
                                          placeholder="{{ $fieldPlaceholder }}"
                                          {{ $fieldRequired ? 'required' : '' }}
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800"></textarea>
                            @elseif($fieldType === 'select')
                                <select name="fields[{{ $fieldLabel }}]" 
                                        {{ $fieldRequired ? 'required' : '' }}
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                                    <option value="">{{ $fieldPlaceholder ?: '-- Chọn --' }}</option>
                                    @foreach(explode("\n", $field['options'] ?? '') as $option)
                                        @if(trim($option))
                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @elseif($fieldType === 'checkbox')
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="fields[{{ $fieldLabel }}]" value="1"
                                           {{ $fieldRequired ? 'required' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                    <span class="text-sm">{{ $fieldPlaceholder }}</span>
                                </label>
                            @else
                                <input type="{{ $fieldType }}" 
                                       name="fields[{{ $fieldLabel }}]"
                                       placeholder="{{ $fieldPlaceholder }}"
                                       {{ $fieldRequired ? 'required' : '' }}
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                            @endif
                        @endforeach
                        
                        <button type="submit" 
                                class="w-full py-3 rounded-lg text-white font-medium transition-opacity hover:opacity-90 disabled:opacity-50"
                                style="background-color: {{ $buttonColor }};"
                                :disabled="isSubmitting">
                            <span x-show="!isSubmitting">{{ $buttonText }}</span>
                            <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang gửi...
                            </span>
                        </button>
                    </form>
                @else
                    {{-- No form - just show button --}}
                    <button @click="close()" 
                            class="w-full py-3 rounded-lg text-white font-medium transition-opacity hover:opacity-90"
                            style="background-color: {{ $buttonColor }};">
                        {{ $buttonText }}
                    </button>
                @endif
                
                {{-- Success Message --}}
                <div x-show="isSubmitted" x-cloak class="text-center py-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold mb-1">Cảm ơn bạn!</h4>
                    <p class="text-sm opacity-80">Chúng tôi đã nhận được thông tin.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('popupController', () => ({
        isVisible: false,
        isSubmitting: false,
        isSubmitted: false,
        popupKey: '{{ $popupKey }}',
        frequency: '{{ $frequency }}',
        delay: {{ $delay * 1000 }},
        
        init() {
            console.log('Popup init - frequency:', this.frequency, 'delay:', this.delay);
            
            if (!this.shouldShow()) {
                console.log('Popup should not show');
                return;
            }
            
            console.log('Popup will show after', this.delay, 'ms');
            setTimeout(() => this.show(), this.delay);
        },
        
        shouldShow() {
            if (this.frequency === 'always') return true;
            if (this.frequency === 'once') return !localStorage.getItem(this.popupKey);
            if (this.frequency === 'daily') {
                const lastShown = localStorage.getItem(this.popupKey + '_date');
                return lastShown !== new Date().toDateString();
            }
            return true;
        },
        
        markAsShown() {}
            if (this.frequency === 'once') localStorage.setItem(this.popupKey, '1');
            if (this.frequency === 'daily') localStorage.setItem(this.popupKey + '_date', new Date().toDateString());
        },
        
        show() {
            console.log('Popup showing now');
            this.isVisible = true;
            document.body.style.overflow = 'hidden';
            this.markAsShown();
        },
        
        close() {
            this.isVisible = false;
            document.body.style.overflow = '';
        },
        
        async submitForm(event) {
            this.isSubmitting = true;
            try {
                const formData = new FormData(event.target);
                const response = await fetch('/api/form-submit', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    this.isSubmitted = true;
                    event.target.style.display = 'none';
                    setTimeout(() => this.close(), 2000);
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            } catch (error) {
                console.error('Form submit error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }));
});
</script>

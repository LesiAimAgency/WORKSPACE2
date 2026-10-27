@props(['widget'])

<div 
    x-data="{ 
        loaded: false, 
        html: '',
        init() {
            @if($widget->is_lazy_loaded)
                // Lazy load via AlpineJS
                fetch('/api/widgets/render/{{ $widget->widget_code }}')
                    .then(response => response.text())
                    .then(data => {
                        this.html = data;
                        this.loaded = true;
                    });
            @else
                this.loaded = true;
            @endif
        }
    }"
    class="widget-wrapper"
>
    <!-- Skeleton loader for heavy widgets -->
    <template x-if="!loaded">
        <div class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-4 py-1">
                <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                <div class="space-y-2">
                    <div class="h-4 bg-gray-300 rounded"></div>
                    <div class="h-4 bg-gray-300 rounded w-5/6"></div>
                </div>
            </div>
        </div>
    </template>

    <!-- Render Content -->
    <template x-if="loaded">
        <div x-html="html">
            @if(!$widget->is_lazy_loaded)
                <!-- Nếu không lazy load, render thẳng HTML (có thể render view partial ở đây) -->
                @includeIf('widgets.partials.' . $widget->widget_code)
            @endif
        </div>
    </template>
</div>

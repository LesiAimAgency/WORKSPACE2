@props(['title', 'subtitle' => '', 'uniqueId' => '', 'isSlider' => false, 'titleClass' => 'title'])

<div class="title-area-between">
    <div class="title-left">
        <h2 class="{{ $titleClass }} mb--0">{{ $title }}</h2>
        @if($subtitle)
            <p class="disc-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if($isSlider && $uniqueId)
    <div class="next-prev-swiper-wrapper">
        <div class="swiper-button-prev slider-{{ $uniqueId }}-prev"><i class="fa-regular fa-chevron-left"></i></div>
        <div class="swiper-button-next slider-{{ $uniqueId }}-next"><i class="fa-regular fa-chevron-right"></i></div>
    </div>
    @endif

    {{ $slot }}
</div>

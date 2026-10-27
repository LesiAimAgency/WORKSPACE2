<div class="kalles-home-section type_slideshow type_carousel">
    <div class="slideshow" data-flickity='{ "fade":0,"cellAlign": "center","imagesLoaded": 0,"lazyLoad": 0,"freeScroll": 0,"wrapAround": true,"autoPlay" : 0,"pauseAutoPlayOnHover" : true, "rightToLeft": false, "prevNextButtons": false,"pageDots": true, "contain" : 1,"adaptiveHeight" : 1,"dragThreshold" : 5,"percentPosition": 1 }'>
        
        @if(isset($data['slides']) && is_array($data['slides']) && count($data['slides']) > 0)
            @foreach($data['slides'] as $index => $slide)
                @php
                    // Determine text alignment based on config or index
                    $alignClass = 'col-lg-7';
                    $textAlignClass = '';
                    $fadeClass = 'fade-right';
                    
                    if ($index % 3 == 1) {
                        $alignClass = 'col-lg-6';
                        $textAlignClass = 'text-end';
                    } elseif ($index % 3 == 2) {
                        $alignClass = 'col-lg-6';
                        $fadeClass = 'fade-up';
                    }
                @endphp
                
                <div class="slideshow__slide w-100" style="background-image: url('{{ $slide['image'] ?? '' }}');">
                    <div class="container">
                        <div class="row {{ $index % 3 == 1 ? 'justify-content-end' : '' }}">
                            <div class="{{ $alignClass }}">
                                <div class="{{ $textAlignClass }}" data-aos="{{ $fadeClass }}" data-aos-delay="300">
                                    @if(!empty($slide['subtitle']))
                                        <h4 class="fs-18 fw-medium">{{ $slide['subtitle'] }}</h4>
                                    @endif
                                    
                                    @if(!empty($slide['title']))
                                        <h1 class="display-4 fw-semibold mb-3">{{ $slide['title'] }}</h1>
                                    @endif
                                    
                                    @if(!empty($slide['button_text']))
                                        <a class="btn btn-dark rounded-0 min-w-150" href="{{ $slide['button_link'] ?? '#' }}">
                                            {{ $slide['button_text'] }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- placeholder slide if no data -->
            <div class="slideshow__slide w-100" style="background-image: url('/theme/images/slide/slider-01.jpg');">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <div data-aos="fade-right" data-aos-delay="300">
                                <h4 class="fs-18 fw-medium">SUMMER 2024</h4>
                                <h1 class="display-4 fw-semibold mb-3">Configure in CMS</h1>
                                <a class="btn btn-dark rounded-0 min-w-150" href="#">Explore Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
    </div>
</div>

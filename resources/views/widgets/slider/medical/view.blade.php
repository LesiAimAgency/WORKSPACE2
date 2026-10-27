<section class="kalles-home-section type_slideshow type_carousel kalles-medical overflow-hidden" {!! $widget->buildWrapperStyleAttribute() !!}>
    <div class="slideshow"
        data-flickity='{ "fade":0,"cellAlign": "center","imagesLoaded": 0,"lazyLoad": 0,"freeScroll": 0,"wrapAround": true,"autoPlay" : 0,"pauseAutoPlayOnHover" : true, "rightToLeft": false, "prevNextButtons": {{ ($settings["arrows_visible"] ?? "yes") === "yes" ? "true" : "false" }},"pageDots": true, "contain" : 1,"adaptiveHeight" : 1,"dragThreshold" : 5,"percentPosition": 1 }'>
        
        @foreach($data['slides'] as $slide)
        <div class="slideshow__slide">
            <img src="{{ $slide['image'] ?? '' }}" alt=""
                class="position-absolute w-100 h-100 object-fit-cover">
            <div class="container position-relative">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="content">
                            @if(!empty($slide['subtitle']))
                                {!! $slide['subtitle'] !!}
                            @endif
                            
                            @if(!empty($slide['title']))
                                <h1 class="fs-45 fw-semibold mb-3">{{ $slide['title'] }}</h1>
                            @endif
                            
                            @if(!empty($slide['button_text']) && !empty($slide['button_link']))
                            <a href="{{ $slide['button_link'] }}">
                                <div class="btn btn-primary text-white rounded-pill min-w-150 min-h-45 d-inline-flex align-items-center justify-content-center fw-semibold">
                                    {{ $slide['button_text'] }}
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

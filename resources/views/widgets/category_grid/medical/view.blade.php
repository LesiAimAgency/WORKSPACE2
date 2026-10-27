<section class="py-30 shop-categories" {!! $widget->buildWrapperStyleAttribute() !!}>
    <div class="container">
        @if(!empty($data['section_title']))
        <div class="row mt-3">
            <div class="col-lg-6">
                <h3 class="fs-26">{{ $data['section_title'] }}</h3>
            </div><!--end col-->
        </div><!--end row-->
        @endif
        
        @php
            // Use custom categories if provided in settings, otherwise use the resolved ones
            $categoriesToRender = !empty($settings['custom_categories']) ? $settings['custom_categories'] : $data['categories'];
        @endphp

        <div class="row mt-3 pb-5 blog-arrow g-2"
            data-flickity='{"imagesLoaded": 0, "adaptiveHeight": 0, "contain": 1, "groupCells": "100%", "dragThreshold": 5, "cellAlign": "left", "wrapAround": true, "swiper-pagination": false, "prevNextButtons": false, "percentPosition": 1, "pageDots": true, "autoPlay": 0, "pauseAutoPlayOnHover": true }'
            dir="ltr">
            
            @foreach($categoriesToRender as $category)
            <div class="col-lg-2 col-md-4 col-sm-6 px-2 text-center">
                <div class="img-zoom">
                    <a href="{{ $category['link'] ?? '#' }}"
                        class="overflow-hidden d-inline-block">
                        <img src="{{ $category['image'] ?? '' }}" alt=""
                            class="img-fluid">
                    </a>
                    <div class="p-10 text-center">
                        <h5 class="fw-medium mb-2"><a href="{{ $category['link'] ?? '#' }}"
                                class="text-reset">{{ $category['name'] }}</a></h5>
                        @if(!empty($category['subtitle']))
                        <p class="mb-0">{{ $category['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
            </div><!--end col-->
            @endforeach
            
        </div><!--end row-->
    </div><!--end container-->
</section>

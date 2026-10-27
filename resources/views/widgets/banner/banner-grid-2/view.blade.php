    <div class="banner-section position-relative py-3">
        <div class="container">
            @php
                $rawBanners = $data['banners'] ?? null;
                $rawSlides = $data['slides'] ?? null;
                $items = !empty($rawBanners) && is_array($rawBanners) ? $rawBanners : (!empty($rawSlides) && is_array($rawSlides) ? $rawSlides : []);
            @endphp
            <div class="row g-4">
                @if(count($items) > 0)
                    @foreach($items as $item)
                        <div class="col-lg-6">
                            <a href="{{ $item['link'] ?? '#' }}" class="position-relative hover-zoom d-block rounded overflow-hidden">
                                <img src="{{ $item['image'] ?? '/theme/images/home-01/bn-05.jpg' }}" alt="{{ $item['title'] ?? 'Banner' }}" class="img-fluid hover-zoom-img w-100" style="object-fit: cover; max-height: 400px;">
                                @if(!empty($item['title']) || !empty($item['subtitle']) || !empty($item['button_text']))
                                <div class="position-absolute start-0 end-0 top-0 bottom-0 d-flex align-items-center justify-content-center bg-dark bg-opacity-25">
                                    <div class="text-center text-white p-3">
                                        @if(!empty($item['title']))
                                        <h4 class="fs-24 fw-bold text-uppercase mb-2">{{ $item['title'] }}</h4>
                                        @endif
                                        @if(!empty($item['subtitle']))
                                        <h6 class="mb-0 text-white-50">{{ $item['subtitle'] }}</h6>
                                        @endif
                                        @if(!empty($item['button_text']))
                                        <span class="btn btn-light btn-sm mt-3 px-4 rounded-0">{{ $item['button_text'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-lg-6">
                        <a href="#!" class="position-relative hover-zoom d-block">
                            <img src="/theme/images/home-01/bn-05.jpg" alt="" class="img-fluid hover-zoom-img">
                            <div class="position-absolute start-0 end-0 top-0 bottom-0 d-flex align-items-center justify-content-center">
                                <div class="text-center text-white">
                                    <h4 class="fs-24">LOOKBOOK 2022</h4>
                                    <h6 class="mb-0">MAKE LOVE THIS LOOK</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <a href="#!" class="position-relative hover-zoom d-block">
                            <img src="/theme/images/home-01/bn-06.jpg" alt="" class="img-fluid hover-zoom-img">
                            <div class="position-absolute start-0 end-0 top-0 bottom-0 d-flex align-items-center justify-content-center">
                                <div class="text-center text-white">
                                    <h6 class="text-capitalize mb-2">Summer Sale</h6>
                                    <h1 class="mb-0" style="font-size: 50px;">UP TO 70%</h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

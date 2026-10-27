<section class="py-30" {!! $widget->buildWrapperStyleAttribute() !!}>
    <div class="container">
        <div class="my-5 pb-md-5">
            <div class="kalles-medical-deal-section">
                @if(!empty($data['section_title']))
                <h4 class="product-cd-header text-center fs-25 d-inline-flex bg-body align-items-center mb-0">
                    {{ $data['section_title'] }}
                </h4>
                @endif
                
                <div class="swiper medialSwiper">
                    <div class="swiper-wrapper">
                        @foreach($data['products'] as $product)
                        <div class="swiper-slide">
                            <div>
                                <h6 class="fs-16 fw-medium mb-1">
                                    <a class="main_link_primary" href="{{ $product['link'] }}">{{ $product['title'] }}</a>
                                </h6>
                                <p class="mb-3 pb-1 fs-15 text-muted">
                                    @if(!empty($product['sale_price']))
                                        <del>{{ $product['price'] }}</del>
                                        <span class="text-danger">{{ $product['sale_price'] }}</span>
                                    @else
                                        <span>{{ $product['price'] }}</span>
                                    @endif
                                </p>
                                <div x-data="{ imageUrl: '{{ $product['image'] }}', isHovered: false }" class="topbar-product-card desgin_1"
                                    x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                    <div class="position-relative overflow-hidden">
                                        <img :src="imageUrl" alt="" class="img-fluid">
                                        <div class="bg-overlay"></div>
                                        <a href="#" class="text-white wishlistadd position-absolute"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>
                                        <div class="product-button d-none d-lg-flex flex-column gap-2">
                                            <a href="#exampleModal" data-bs-toggle="modal"
                                                class="btn rounded-pill fs-14 text-white"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                            <button type="button" class="btn rounded-pill fs-14"
                                                data-bs-toggle="modal" data-bs-target="#cardModal"
                                                class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                <i class="iccl iccl-cart"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@pushOnce('widget-css')
<link rel="stylesheet" href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}">
@endPushOnce
@pushOnce('widget-js')
<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
<script>
    // Initialize swiper for medical products
    if(typeof Swiper !== 'undefined') {
        new Swiper(".medialSwiper", {
            slidesPerView: {{ $settings['columns_desktop'] ?? 4 }},
            spaceBetween: 30,
            breakpoints: {
                320: { slidesPerView: {{ $settings['columns_mobile'] ?? 1 }} },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: {{ $settings['columns_desktop'] ?? 4 }} },
            }
        });
    }
</script>
@endPushOnce

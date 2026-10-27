@php
    $section_title = $data['section_title'] ?? 'Best Seller';
    $section_subtitle = $data['section_subtitle'] ?? 'Top sale in this week';
    $products = $data['products'] ?? [];
@endphp
<!-- BEST SELLER -->
<section>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center">
                    <div class="mb-2">
                        <h3 class="section-title position-relative flex text-uppercase">
                            <span>{{ $section_title }}</span>
                        </h3>
                    </div>
                    @if(!empty($section_subtitle))
                    <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">{{ $section_subtitle }}</span>
                    @endif
                </div>
            </div><!--end col-->
        </div><!--end row-->
        <div class="row g-lg-4 g-3 mt-4">
            @if(!empty($products) && is_array($products))
                @foreach($products as $item)
                    <div class="col-md-3 col-6">
                        <div class="topbar-product-card pb-3">
                            <div class="position-relative overflow-hidden">
                                <img src="{{ $item['image'] ?? '/theme/images/products/pr-29.jpg' }}" alt="{{ $item['title'] ?? '' }}" class="img-fluid object-fit-cover w-100" style="height: 350px; object-fit: cover;">
                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
        
                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal"><span>Quick Shop</span>
                                        <i class="iccl iccl-cart"></i></button>
                                </div>
                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal">
                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                </div>
                            </div>
                            <a href="{{ $item['link'] ?? '#' }}" class="mt-3 d-block text-dark text-decoration-none">
                                <h6 class="mb-1 text-truncate">{{ $item['title'] ?? 'Product' }}</h6>
                                <p class="mb-0 fs-14 text-muted">
                                    @if(!empty($item['sale_price']))
                                        <del class="me-1">{{ $item['price'] }}</del>
                                        <span class="text-danger font-semibold">{{ $item['sale_price'] }}</span>
                                    @else
                                        <span class="font-semibold">{{ $item['price'] ?? '' }}</span>
                                    @endif
                                </p>
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5 text-muted">Không có sản phẩm nào</div>
            @endif
        </div><!--end row-->
    </div>
</section>

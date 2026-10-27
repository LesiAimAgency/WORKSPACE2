    <section class="py-4">
        <div class="container">
            @php
                $section_title = $data['section_title'] ?? 'Sản phẩm nổi bật';
                $section_subtitle = $data['section_subtitle'] ?? '';
                $products = $data['products'] ?? [];
            @endphp
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex">
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
                                <div class="position-relative overflow-hidden rounded">
                                    <img src="{{ $item['image'] ?? '/theme/images/products/pr-01.jpg' }}" alt="{{ $item['title'] ?? '' }}" class="img-fluid w-100 object-fit-cover" style="height: 250px; object-fit: cover;">
                                </div>
                                <a href="{{ $item['link'] ?? '#' }}" class="mt-3 d-block">
                                    <h6 class="mb-1 text-truncate">{{ $item['title'] ?? 'Sản phẩm' }}</h6>
                                    <p class="mb-0 fs-14 text-muted">
                                        @if(!empty($item['sale_price']))
                                            <del class="me-1">{{ $item['price'] }}</del>
                                            <span class="text-danger font-semibold">{{ $item['sale_price'] }}</span>
                                        @else
                                            <span class="font-semibold">{{ $item['price'] ?? '450.000đ' }}</span>
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
        </div><!--end container-->
    </section>


<div class="kalles-section-type-shipping">
    <div class="container">
        @php
            $section_title = $data['section_title'] ?? '';
            $features = $data['features'] ?? [];
        @endphp
        
        @if(!empty($section_title))
            <div class="text-center mb-4">
                <h3 class="section-title position-relative flex text-uppercase">
                    <span>{{ $section_title }}</span>
                </h3>
            </div>
        @endif
        
        <div class="row g-4 justify-content-center">
            @if(!empty($features) && is_array($features))
                @foreach($features as $item)
                    <div class="col-xl-3 col-md-6">
                        <div class="d-flex gap-3">
                            @if(!empty($item['icon']))
                                @if(str_contains($item['icon'], 'pe-7s') || str_contains($item['icon'], 'fa-') || str_contains($item['icon'], 'iccl'))
                                    <i class="{{ $item['icon'] }} fs-36 text-muted flex-shrink-0"></i>
                                @else
                                    <img src="{{ $item['icon'] }}" class="flex-shrink-0" style="width: 36px; height: 36px; object-fit: contain;">
                                @endif
                            @else
                                <i class="pegk pe-7s-car fs-36 text-muted flex-shrink-0"></i>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="text-uppercase">{{ $item['title'] ?? '' }}</h6>
                                <p class="text-muted mb-0">{{ $item['description'] ?? '' }}</p>
                                @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}" class="stretched-link"></a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-xl-3 col-md-6">
                    <div class="d-flex gap-3">
                        <i class="pegk pe-7s-car fs-36 text-muted flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase">Free Shipping</h6>
                            <p class="text-muted mb-0">Free shipping on all US order or order above $100</p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <div class="d-flex gap-3">
                        <i class="pegk pe-7s-help2 fs-36 text-muted flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase">Support 24/7</h6>
                            <p class="text-muted mb-0">Contact us 24 hours a day, 7 days a week</p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <div class="d-flex gap-3">
                        <i class="pegk pe-7s-refresh fs-36 text-muted flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase">30 Days Return</h6>
                            <p class="text-muted mb-0">Simply return it within 30 days for an exchange.</p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <div class="d-flex gap-3">
                        <i class="pegk pe-7s-door-lock fs-36 text-muted flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase">100% Payment Secure</h6>
                            <p class="text-muted mb-0">We ensure secure payment with PEV</p>
                        </div>
                    </div>
                </div><!--end col-->
            @endif
        </div>
    </div>
</div>

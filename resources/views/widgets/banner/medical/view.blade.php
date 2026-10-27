<section class="py-30" {!! $widget->buildWrapperStyleAttribute() !!}>
    <div class="container">
        <div class="row g-lg-4 g-2">
            @php
                $columns = $settings['grid_layout'] ?? '3_col';
                $colClass = 'col-xl-4 col-md-6'; // default 3 cols
                if ($columns === '1_col') $colClass = 'col-12';
                elseif ($columns === '2_col') $colClass = 'col-md-6';
            @endphp
            
            @foreach($data['banners'] as $banner)
            <div class="{{ $colClass }}">
                <div class="kalles-medical-banner-01 position-relative img-zoom">
                    <img src="{{ $banner['image'] ?? '' }}" alt="" class="w-100 img-fluid">
                    <div class="content position-absolute">
                        @if(!empty($banner['subtitle']))
                            <p class="text-uppercase">{{ $banner['subtitle'] }}</p>
                        @endif
                        @if(!empty($banner['title']))
                            <h3>{{ $banner['title'] }}</h3>
                        @endif
                        @if(!empty($banner['button_text']) && !empty($banner['link']))
                            <a href="{{ $banner['link'] }}">
                                <div class="d-inline-block text-white btn btn-primary rounded-pill">{{ $banner['button_text'] }}</div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

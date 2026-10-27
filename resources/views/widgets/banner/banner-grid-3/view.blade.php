    <section class="cat-section py-3">
        <div class="container">
            <div class="row g-lg-4 g-2">
                @php
                    $items = !empty($banners) && is_array($banners) ? $banners : (!empty($slides) && is_array($slides) ? $slides : []);
                @endphp
                @if(count($items) > 0)
                    @foreach($items as $item)
                        <div class="col-md-4 col-6">
                            <a href="{{ $item['link'] ?? '#' }}" class="d-block position-relative cat_grid_item overflow-hidden h-300 rounded">
                                <div class="h-100 w-100 cat-grid-img" style="background-image: url('{{ $item['image'] ?? '/theme/images/home-01/bn-01.jpg' }}'); background-size: cover; background-position: center;"></div>
                                @if(!empty($item['title']))
                                <div class="cat-grid-button text-body">
                                    <div class="cat_grid_item__title">{{ $item['title'] }}</div>
                                </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-6">
                        <a href="#!" class="d-block position-relative cat_grid_item overflow-hidden h-624">
                            <div class="h-100 w-100 cat-grid-img" style="background-image: url('/theme/images/home-01/bn-01.jpg');"></div>
                            <div class="cat-grid-button text-body">
                                <div class="cat_grid_item__title">Women</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="#!" class="d-block position-relative cat_grid_item overflow-hidden h-300 mb-2 mb-lg-4">
                            <div class="h-100 w-100 cat-grid-img" style="background-image: url('/theme/images/home-01/bn-02.jpg');"></div>
                            <div class="cat-grid-button text-body">
                                <div class="cat_grid_item__title">Accessories</div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

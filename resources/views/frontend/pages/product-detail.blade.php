@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-product-detail">
    <section class="product-detail l-container">
        <div class="product-detail__grid">
            <div class="product-detail__images">
                <div class="product-detail__main-image">
                    <img src="{{ $product->featured_image }}" alt="{{ $product->name }}">
                </div>
                @if($product->gallery)
                <div class="product-detail__gallery">
                    @foreach($product->gallery as $image)
                    <img src="{{ $image }}" alt="{{ $product->name }}">
                    @endforeach
                </div>
                @endif
            </div>

            <div class="product-detail__info">
                <h1 class="product-detail__title">{{ $product->name }}</h1>
                <div class="product-detail__price">{{ number_format($product->price) }}đ</div>
                
                @if($product->short_description)
                <div class="product-detail__short-desc">{{ $product->short_description }}</div>
                @endif

                <div class="product-detail__actions">
                    <button class="c-btn01">Liên Hệ Đặt Hàng</button>
                </div>

                <div class="product-detail__features">
                    <div class="feature-item">
                        <img src="/assets/img/icon/ic-shield.svg" alt="">
                        <span>Chất lượng đảm bảo</span>
                    </div>
                    <div class="feature-item">
                        <img src="/assets/img/icon/ic-deli.svg" alt="">
                        <span>Giao hàng toàn quốc</span>
                    </div>
                    <div class="feature-item">
                        <img src="/assets/img/icon/ic-medal.svg" alt="">
                        <span>Bảo hành chính hãng</span>
                    </div>
                </div>
            </div>
        </div>

        @if($product->description)
        <div class="product-detail__description">
            <h2>Mô Tả Sản Phẩm</h2>
            {!! $product->description !!}
        </div>
        @endif
    </section>

    @if($relatedProducts->count())
    <section class="related-products l-container">
        <h2 class="c-ttl03">Sản Phẩm Liên Quan</h2>
        <div class="products-grid">
            @foreach($relatedProducts as $related)
            <article class="product-card">
                <a href="{{ locale_route('shop.show', $related->slug) }}">
                    <img src="{{ $related->featured_image }}" alt="{{ $related->name }}">
                    <h3>{{ $related->name }}</h3>
                    <p>{{ number_format($related->price) }}đ</p>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif
</main>
@endsection

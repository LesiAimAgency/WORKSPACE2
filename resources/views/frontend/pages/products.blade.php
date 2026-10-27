@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-products">
    <section class="c-mainvisual">
        <div class="c-mainvisual__bg">
            <img src="/assets/img/banner-hero-products.png" alt="Sản Phẩm">
        </div>
        <div class="l-container">
            <h1 class="c-mainvisual__title">Sản Phẩm</h1>
        </div>
    </section>

    <section class="products-list l-container">
        <div class="products-list__sidebar">
            <h3>Danh Mục</h3>
            <ul class="category-list">
                @foreach($categories as $category)
                <li><a href="{{ locale_route('shop.category', $category->slug) }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="products-list__main">
            <div class="products-grid">
                @foreach($products as $product)
                <article class="product-card">
                    <a href="{{ locale_route('shop.show', $product->slug) }}">
                        <div class="product-card__image">
                            <img src="{{ $product->featured_image }}" alt="{{ $product->name }}">
                        </div>
                        <div class="product-card__content">
                            <h3 class="product-card__title">{{ $product->name }}</h3>
                            <p class="product-card__price">{{ number_format($product->price) }}đ</p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            {{ $products->links() }}
        </div>
    </section>
</main>
@endsection

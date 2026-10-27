<div id="kalles-section-header_top" class="{{ 'class-name' }}">
    <div class="h__top d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-12 col-md-12">
                    <div class="d-flex align-items-center gap-3">
                        <p class="mb-0"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i> +01 23456789</p>
                        <p class="mb-0"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com</p>
                    </div>
                </div>
                <div class="col-lg-4 col-12 col-md-12">
                    <div class="header-text text-center fs-12">
                        Summer sale discount off <span class="cr">50%</span>! <a href="{{ url('shop_pages/shop')}}" class="text-reset">Shop Now</a>
                    </div>
                </div>
                <div class="col-lg-4 col-12 col-md-12">
                    <div class="dropdown text-end position-relative">
                        <a href="#!" class="fs-12 text-reset currency-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" alt="" height="12" class="me-1"> USD <i class="facl facl-angle-down ms-1"></i>
                        </a>
                        <ul class="dropdown-menu p-3 dropdown-currency">
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/aud.svg')}}" alt="" height="12" class="me-1">
                                    AUD</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/cad.svg')}}" alt="" height="12" class="me-1">
                                    CAD</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/dkk.svg')}}" alt="" height="12" class="me-1">
                                    DKK</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/eur.svg')}}" alt="" height="12" class="me-1">
                                    EUR</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/gbp.svg')}}" alt="" height="12" class="me-1">
                                    GBP</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/hkd.svg')}}" alt="" height="12" class="me-1">
                                    HKD</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/jpy.svg')}}" alt="" height="12" class="me-1">
                                    JPY</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/nzd.svg')}}" alt="" height="12" class="me-1">
                                    NZD</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/sgd.svg')}}" alt="" height="12" class="me-1">
                                    SGD</a></li>
                            <li><a href="#!"><img src="{{ URL::asset('/build/images/svg/usd.svg')}}" alt="" height="12" class="me-1">
                                    USD</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-custom py-0 d-flex align-items-center">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{ url('index')}}"><img src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="" width="95"></a>
            <div class="collapse navbar-collapse mobile-menu-navbar" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown dropdown-mega-xxl">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            Demo
                        </a>
                        <div class="dropdown-menu">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="#!" class="dropdown-menu-title">Home Pages</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('index')}}">Home Default
                                                    <span class="badge bg-danger text-white">Hot</span>
                                                </a>
                                            </li>
                                            <li><a href="{{ url('home_pages/home-classic')}}">Home Classic <span class="badge bg-danger text-white">Hot</span></a></li>
                                            <li><a href="{{ url('home_pages/home-video-banner') }}">Home Video Banner</a></li>
                                            <li><a href="{{ url('home_pages/home-categories-links')}}">Home Categories Links</a></li>
                                            <li><a href="{{ url('home_pages/home-static-image')}}">Home Static Image</a></li>
                                            <li><a href="{{ url('home_pages/home-metro')}}">Home Metro</a></li>
                                            <li><a href="{{ url('home_pages/home-lookbook')}}">Home Lookbook</a></li>
                                            <li><a href="{{ url('home_pages/home-parallax')}}">Home Parallax</a></li>
                                            <li><a href="{{ url('home_pages/home-instagram-shop')}}">Home Instgram Shop</a></li>
                                            <li><a href="{{ url('home_pages/home-medical')}}">Home Medical</a></li>
                                            <li><a href="{{ url('home_pages/home-flower')}}">Home Flower</a></li>
                                            <li><a href="{{ url('home_pages/home-furniture')}}">Home Furniture</a></li>
                                            <li><a href="{{ url('home_pages/home-bag')}}">Home Bag</a></li>
                                            <li><a href="{{ url('home_pages/home-lingeries')}}">Home Lingeries</a></li>
                                            <li><a href="{{ url('home_pages/home-cosmetics')}}">Home Cosmetics</a></li>
                                            <li><a href="{{ url('home_pages/home-glasses')}}">Home Glasses</a></li>
                                            <li><a href="{{ url('home_pages/home-shoes')}}"> Home Shoes <span class="badge bg-danger text-white">Hot</span></a></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('home_pages/home-default') }}" class="dropdown-menu-title">Home Pages</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion9')}}">Home Fashion 9</a>
                                            </li>
                                            <li><a href="{{ url('home_pages/home-lookbook-collection')}}">Home Lookbook Collection</a>
                                            </li>
                                            <li><a href="{{ url('home_pages/home-fashion-simple')}}">Home Fashion Simple</a></li>
                                            <li><a href="{{ url('home_pages/home-fashion10')}}">Home Fashion 10</a></li>
                                            <li><a href="{{ url('home_pages/home-decor')}}">Home Decor</a></li>
                                            <li><a href="{{ url('home_pages/home-decor2')}}">Home Decor 2</a></li>
                                            <li><a href="{{ url('home_pages/home-fashion-vertical')}}">Home Fashion Vertical</a></li>
                                            <li><a href="{{ url('home_pages/home-electric')}}">Home Electric</a></li>
                                            <li><a href="{{ url('home_pages/home-electric-vertical')}}">Home Electric Vertical</a></li>
                                            <li><a href="{{ url('home_pages/home-digital')}}">Home Digital</a></li>
                                            <li><a href="{{ url('home_pages/home-one-product-store')}}">One Product Store</a></li>
                                            <li><a href="{{ url('home_pages/home-handmade')}}">Home Handmade</a></li>
                                            <li><a href="{{ url('home_pages/home-fashion-trend')}}">Home Fashion Trend</a></li>
                                            <li><a href="{{ url('home_pages/home-kids')}}">Home Kids</a></li>
                                            <li><a href="{{ url('home_pages/home-sport')}}">Home Sport</a></li>
                                            <li><a href="{{ url('home_pages/home-jewelry')}}">Home Jewelry</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('home_pages/home-default') }}" class="dropdown-menu-title">Header Layouts</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('home_pages.home-header-01') }}">Header Layout 1</a>
                                            </li>
                                            <li><a href="{{ url('home_pages.home-header-02') }}">Header Layout 2</a></li>
                                            <li><a href="{{ url('index')}}">Header Layout 3</a></li>
                                            <li><a href="{{ url('home_pages/home-header-04')}}">Header Layout 4</a></li>
                                            <li><a href="{{ url('home_pages/home-electric')}}">Header Layout 5</a></li>
                                            <li><a href="{{ ('home_pages.home-header-06')}}">Header Layout 6</a></li>
                                            <li><a href="{{ url('home_pages/home-fashion-vertical')}}">Header Layout 7</a></li>
                                            <li><a href="{{ url('home_pages/home-electric-vertical')}}">Header Layout 8</a></li>
                                            <li><a href="{{ url('home_pages/home-decor')}}">Header Transparent</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="#!" class="dropdown-menu-title">FEATURES</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter options</a>
                                            </li>
                                            <li><a href="{{ url('index')}}">Catalog mode</a></li>
                                            <li><a href="{{ url('shop_pages/shop')}}">Cookies law info</a></li>
                                            <li><a href="{{ url('home_pages/home-age-verified')}}">Age verification</a></li>
                                            <li><a href="{{ url('index')}}">Mega menu</a></li>
                                            <li><a href="{{ url('home_pages/home-parallax')}}">Footer sticky</a></li>
                                            <li><a href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a></li>
                                            <li><a href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden sidebar</a></li>
                                            <li><a href="{{ url('shop_pages/checkout')}}">Checkout</a></li>
                                            <li><a href="{{ url('product/product-detail-frequently-bought-together')}}">Frequently
                                                    Bought Together</a></li>
                                            <li><a href="">Variant Images
                                                    Grouped</a></li>
                                            <li><a href="{{ url('home_pages/home-rtl')}}">Demo RTL</a></li>
                                            <li><a href="{{ url('shop_pages/shop-grid-list-switcher')}}">Grid/List switcher</a></li>
                                            <li><a href="">Compare</a></li>
                                            <li><a href="{{ url('{{ url('product.product-detail-pickup-availability') }}')}}">Pickup
                                                    Availability</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-xxl">
                        <a class="nav-link position-relative" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                            Shop <span class="badge bg-info">New</span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="row g-0">
                                <div class="col-lg-5">
                                    <div class="row g-0">
                                        <div class="col-lg-6">
                                            <div class="dropdown-sub-column-item">
                                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">SHOP
                                                    PAGES</a>
                                                <ul class="sub-column-menu">
                                                    <li>
                                                        <a href="{{ url('shop_pages/shop')}}">Grid Layout</a>
                                                    </li>
                                                    <li><a href="{{ url('shop_pages/shop-packery-layout')}}">Packery Layout</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-masonry-layout')}}">Masonry Layout</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-full-width-layout')}}">Full Width Layout</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-1600px-layout')}}">1600px Layout</a></li>
                                                    <li><a href="{{url( 'shop_pages.shop-left-sidebar')}}">Left Sidebar</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden sidebar</a></li>
                                                    <li><a href="{{ url('shop_pages/shopping-cart')}}">Shopping cart</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="dropdown-sub-column-item">
                                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">FEATURES</a>
                                                <ul class="sub-column-menu">
                                                    <li>
                                                        <a href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter options</a>
                                                    </li>
                                                    <li><a href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter area</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-Infinite-scrolling') }}">Infinite scrolling</a>
                                                    </li>
                                                    <li><a href="{{ url('index')}}">Catalog mode</a></li>
                                                    <li><a href="{{ url('shop_pages/shop')}}">Cookies law info</a></li>
                                                    <li><a href="{{ url('home_pages/home-age-verified') }}">Age verification</a></li>
                                                    <li><a href="{{ url('index')}}">Mega menu</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a></li>
                                                    <li><a href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden Search</a></li>
                                                    <li><a href="{{ url('shop_pages/checkout')}}">Checkout</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="row g-0">
                                        <div class="col-lg-6">
                                            <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                <img src="{{ URL::asset('/build/images/megamenu/bn-01.jpg')}}" alt="" class="img-fluid">
                                                <a href="#!" class="btn bg-white px-5 fw-medium mb-4 position-absolute bottom-0 start-50 translate-middle-x">
                                                    Women
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                <img src="{{ URL::asset('/build/images/megamenu/bn-02.jpg')}}" alt="" class="img-fluid">
                                                <a href="#!" class="btn bg-white px-5 fw-medium mb-4 position-absolute bottom-0 start-50 translate-middle-x">
                                                    Men
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-xxl">
                        <a class="nav-link" href="{{ url('product/product-detail-layout-01')}}" data-bs-toggle="dropdown" aria-expanded="false">
                            Product
                        </a>
                        <div class="dropdown-menu">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT
                                            LAYOUT</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('product/product-detail-layout-01')}}">Product Detail Layout 1</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-layout-02')}}">Product Detail Layout 2</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-03')}}">Product Detail Layout 3</a></li>
                                            <li><a href="{{ url('product/product-detail-thumb-bottom')}}">Product thumb at bottom</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-thumb-right')}}">Product thumb on right</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-without-thumbnail')}}">Product without
                                                    thumbnail</a></li>
                                            <li><a href="">Left Sidebar</a></li>
                                            <li><a href="{{ url('product/product-detail-right-sidebar')}}">Right sidebar</a></li>
                                            <li><a href="{{ url('product/product-detail-sidebar-full-height')}}">Sidebar Full
                                                    Height</a></li>
                                            <li><a href="{{ url('product/product-detail-tab-accordion')}}">Product Tab Accordions</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-full-width-atc')}}">Product Full Width ATC</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-full-width')}}">Product full width layout</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-advance-product-type')}}">Advance Product
                                                    Type</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT
                                            DETAIL</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('product/product-detail-external-affiliate')}}">External/Affiliate
                                                    Product</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-simple-product')}}">Simple product</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">Variable product</a></li>
                                            <li><a href="{{ url('product/product-detail-grouped-product')}}">Grouped Product</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-02')}}">Inner Zoom #1</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">External Zoom</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-03')}}">Inner Zoom #2</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">PhotoSwipe Popup</a></li>
                                            <li><a href="{{ url('product/product-detail-description-with-product')}}">Description with
                                                    product</a></li>
                                            <li><a href="{{ url('product/product-detail-description-with-instagram-shop')}}">Description
                                                    with instagram shop</a></li>
                                            <li><a href="{{ url('product/product-detail-product-video')}}">Product video</a></li>
                                            <li><a href="{{ url('product/product-detail-3d-ar-models')}}">Product 3D, AR models</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT
                                            SWATCH</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('product/product-detail-layout-01')}}">Product Color Swatch</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-swatch-color')}}">Product Gallery Swatch</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-swatch-color')}}">Product Images Swatch</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-swatch-color')}}">Swatch Color</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">Swatch Color Circle</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-radio')}}">Swatch Radio</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-radio-color')}}">Swatch Radio Color</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-swatch-rectangle')}}">Swatch Rectangle</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-rectangle-color')}}">Swatch Rectangle
                                                    Color</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-simple')}}">Swatch Simple</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-simple-color')}}">Swatch Simple
                                                    Color</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT
                                            FEATURES</a>
                                        <ul class="sub-column-menu">
                                            <li>
                                                <a href="{{ url('product/product-detail-frequently-bought-together')}}">Frequently
                                                    Bought Together</a>
                                            </li>
                                            <li><a href="{{ ('product.product-detail-pre-orders')}}">Product pre-orders</a></li>
                                            <li><a href="{{ url('product/product-detail-tab-accordion')}}">Product Upsell Features</a>
                                            </li>
                                            <li><a href="{{ url('product/product-detail-description-with-lookbook')}}">Description with
                                                    Lookbook</a></li>
                                            <li><a href="{{ ('product.product-detail-back-in-stock-notification')}}">Back in stock
                                                    notification</a></li>
                                            <li><a href="">Variant Images
                                                    Grouped</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">Size Guide HTML</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">Delivery & Return</a></li>
                                            <li><a href="{{ url('product/product-detail-layout-01')}}">Ask a Question</a></li>
                                            <li><a href="{{ url('product/product-detail-product-sticky')}}">Product sticky</a></li>
                                            <li><a href="{{ url('product/product-detail-360-viewer')}}">360° product viewer</a></li>
                                            <li><a href="{{ url('product/product-detail-swatch-radio')}}">Dynamic checkout buttons</a>
                                            </li>
                                            <li><a href="ho{{ url('product.product-detail-layout-01')}}">Sticky add to cart</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-3xl">
                        <a class="nav-link position-relative" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                            Sale <span class="badge bg-warning">Sale</span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dropdown-sub-column-item">
                                        <a href="{{ url('shop_pages/shop-full-width-layout')}}" class="dropdown-menu-title">Accessories</a>
                                        <a href="{{ url('shop_pages/shop-1600px-layout')}}" class="dropdown-menu-title">Footwear</a>
                                        <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">Women</a>
                                        <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="dropdown-menu-title">T-Shirt</a>
                                        <a href="{{ url('shop_pages/shop-right-sidebar')}}" class="dropdown-menu-title">Shoes</a>
                                        <a href="{{ url('shop_pages/shop-masonry-layout')}}" class="dropdown-menu-title">Denim</a>
                                        <a href="{{ url('shop_pages/shop-1600px-layout')}}" class="dropdown-menu-title">Dress</a>
                                        <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">Men</a>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <!-- Swiper -->
                                    <div class="swiper mySwiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <span class="new-label bg-success text-white rounded-circle">
                                                            New </span>
                                                        <img src="{{ URL::asset('/build/images/megamenu/pr-01.jpg')}}" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                        <p class="product-size mb-0 text-center text-white fw-medium">
                                                            XS, S, M, L, XL</p>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="#!" class="product-title">Analogue
                                                                Resin Strap</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">$30.00</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <img src="{{ URL::asset('/build/images/megamenu/pr-03.jpg')}}" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                        <p class="product-size mb-0 text-center text-white fw-medium">
                                                            XS, S, M, L, XL</p>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="{{ url('product/product-detail-layout-01')}}" class="product-title">Ridley High Waist</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">$36.00</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div x-data="{ imageUrl: '/build/images/megamenu/pr-05.jpg' }" class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <img :src="imageUrl" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                        <p class="product-size mb-0 text-center text-white fw-medium">S,
                                                            M, L</p>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="#!" class="product-title">Blush
                                                                Beanie</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">$15.00</p>
                                                        <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/megamenu/pr-05.jpg'" x-on:click.prevent="imageUrl = '/build/images/megamenu/pr-05.jpg'" class="d-inline-block bg-body-tertiary rounded-circle"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-31.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-31.jpg'" class="d-inline-block bg_color_pink rounded-circle"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-32.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-32.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div x-data="{ imageUrl: '/build/images/megamenu/pr-07.jpg' }" class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <span class="new-label bg-danger text-white rounded-circle">
                                                            -25% </span>
                                                        <img :src="imageUrl" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                        <p class="product-size mb-0 text-center text-white fw-medium">
                                                            XS, S, M</p>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="#!" class="product-title">Cluse La
                                                                Boheme Rose Gold</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">
                                                            <del>$60.00</del>
                                                            <span class="text-danger">$45.00</span>
                                                        </p>
                                                        <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/megamenu/pr-07.jpg'" x-on:click.prevent="imageUrl = '/build/images/megamenu/pr-05.jpg'" class="d-inline-block bg_color_green rounded-circle"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-06.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-31.jpg'" class="d-inline-block bg-body-secondary rounded-circle"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-08.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-32.jpg'" class="d-inline-block bg_color_blue rounded-circle"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div x-data="{ imageUrl: '/build/images/megamenu/pr-09.jpg' }" class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <img :src="imageUrl" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                        <p class="product-size mb-0 text-center text-white fw-medium">S,
                                                            M</p>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="#!" class="product-title">Mercury
                                                                Tee</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">
                                                            <span>$68.00</span>
                                                        </p>
                                                        <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/megamenu/pr-09.jpg'" x-on:click.prevent="imageUrl = '/build/images/megamenu/pr-09.jpg'" class="d-inline-block rounded-circle" style="background: url('/build/images/megamenu/pr-09.jpg');background-size: cover;"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-14.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-14.jpg'" class="d-inline-block rounded-circle" style="background: url('/build/images/products/pr-14.jpg');background-size: cover;"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div x-data="{ imageUrl: '/build/images/megamenu/pr-11.jpg' }" class="topbar-product-card pb-3">
                                                    <div class="position-relative">
                                                        <span class="new-label bg-danger text-white rounded-circle">
                                                            -34% </span>
                                                        <img :src="imageUrl" alt="" class="img-fluid">
                                                        <a href="#" class="wishlistadd position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o"></i></a>

                                                        <div class="product-button d-flex flex-column gap-2">
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    View</span> <i class="iccl iccl-eye"></i></a>
                                                            <a href="#!" class="btn rounded-pill fs-14"><span>Quick
                                                                    Shop</span> <i class="iccl iccl-cart"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="mb-1"><a href="#!" class="product-title">La Bohème
                                                                Rose Gold</a></h6>
                                                        <p class="mb-0 fs-14 text-muted">
                                                            <del>$60.00</del>
                                                            <span class="text-danger">$40.00</span>
                                                        </p>
                                                        <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/megamenu/pr-11.jpg'" x-on:click.prevent="imageUrl = '/build/images/megamenu/pr-11.jpg'" class="d-inline-block bg_color_pink rounded-circle"></a>
                                                            <a href="#!" x-on:mouseover="imageUrl = '/build/images/products/pr-35.jpg'" x-on:click.prevent="imageUrl = '/build/images/products/pr-35.jpg'" class="d-inline-block bg-dark rounded-circle"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                        </div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-lg">
                        <a class="nav-link" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Portfolio
                        </a>
                        <ul class="dropdown-menu dropdown-sub-column">
                            <li><a href="{{ url('portfolio/portfolio')}}">Portfolio 2 Columns</a></li>
                            <li><a href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a></li>
                            <li><a href="{{ url('portfolio/portfolio-4-columns')}}">Portfolio 4 Columns</a></li>
                            <li><a href="{{ url('portfolio/single-portfolio-with-shop')}}">Single Portfolio With Shop</a></li>
                            <li><a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With Lookbook</a></li>
                            <li><a href="{{ url('portfolio/single-portfolio-with-instagram-shop')}}">Portfolio With Instagram Shop</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-lg">
                        <a class="nav-link" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lookbook
                        </a>
                        <ul class="dropdown-menu dropdown-sub-column">
                            <li><a href="{{ url('home_pages/home-lookbook')}}">Lookbook Slider</a></li>
                            <li><a href="{{ url('home_pages/home-lookbook-collection')}}">Lookbook Section</a></li>
                            <li><a href="{{ url('index')}}">Lookbook instagram</a></li>
                            <li><a href="{{ url('product/product-detail-description-with-lookbook')}}">Lookbook in product</a></li>
                            <li><a href="{{ url('blog/blog-post-with-lookbook')}}">Lookbook in blog post</a></li>
                            <li><a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Lookbook in portfolio post</a></li>
                            <li><a href="{{ url('blog/lookbook-in-page')}}">Lookbook in page</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-lg">
                        <a class="nav-link" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Blog
                        </a>
                        <ul class="dropdown-menu dropdown-sub-column">
                            <li><a href="#!">Grid Layout</a></li>
                            <li><a href="{{ url('blog/blog-masonry')}}">Masonry Layout</a></li>
                            <li><a href="{{ url('blog/blog-left-sidebar')}}">Left Sidebar</a></li>
                            <li><a href="{{ url('blog/blog-right-sidebar')}}">Right Sidebar</a></li>
                            <li><a href="{{ url('blog/blog-post-with-product-listing')}}">Single Post with Product Listing</a></li>
                            <li><a href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Instagram Shop</a></li>
                            <li><a href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Categories</a></li>
                            <li><a href="{{ url('blog/blog-post-with-lookbook')}}">Single Post with lookbook</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3">
                <a data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-search fw-semibold"></i></a>
                <a data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="iccl iccl-user fw-semibold"></i></a>
                <a href="#!"><i class="iccl iccl-heart fw-semibold"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart fw-semibold"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
            </div>
        </div>
    </nav>
</div>

<div class="backdrop-shadow d-none"></div>

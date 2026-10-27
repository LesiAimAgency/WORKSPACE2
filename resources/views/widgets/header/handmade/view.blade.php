<div id="kalles-section-header_top" class="navbar-handmade">
    <div class="h__top d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center py-1 py-md-0">
                <div class="col-md-5 col-xl-4">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                        <p class="mb-0"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i> +01 23456789</p>
                        <p class="mb-0"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com</p>
                    </div>
                </div>
                <div class="col-md-5 col-xl-4 py-1 py-md-0">
                    <div class="header-text text-center fs-12">
                        Summer sale discount off <span class="cr">50%</span>! <a href="{{ url('shop_pages/shop')}}" class="text-reset">Shop Now</a>
                    </div>
                </div>
                <div class="col-md-2 col-xl-4">
                    <div class="dropdown text-center text-md-end position-relative">
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
            <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <a class="navbar-brand fs-14 d-none d-lg-block" href="#">Welcome to our store!</a>
            <a class="navbar-brand d-lg-none nav-link" href="#"><img src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="" width="95"></a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-none d-lg-block mx-auto">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 lingeries-header">
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link fw-medium" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                Demo
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="#!" class="dropdown-menu-title">Home Pages</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('index')}}">Home Default
                                                        <span class="badge-tag badge bg-danger">Hot</span>
                                                    </a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-classic')}}">Home Classic <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="">Home Video Banner</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-categories-links')}}">Home Categories
                                                        Links</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-static-image')}}">Home Static Image</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-metro')}}">Home Metro</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook')}}">Home Lookbook</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-parallax')}}">Home Parallax</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-instagram-shop')}}">Home Instgram Shop</a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-medical')}}">Home Medical <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>

                                                <li><a class="text-muted" href="{{ url('home_pages/home-flower')}}">Home Flower</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-furniture')}}">Home Furniture<span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-bag')}}">Home Bag</a></li>

                                                <li><a class="text-muted" href="{{ url('home_pages/home-lingeries')}}">Home Lingeries</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-cosmetics')}}">Home Cosmetics <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-glasses')}}">Home Glasses <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-shoes')}}"> Home Shoes <span class="badge-tag badge bg-danger">Hot</span></a></li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('home_pages/home-default') }}" class="dropdown-menu-title">Home
                                                Pages</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="{{ url('home_pages/home-fashion9')}}">Home Fashion 9</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook-collection')}}">Home Lookbook
                                                        Collection</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-fashion-simple')}}">Home Fashion Simple</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-fashion10')}}">Home Fashion 10</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-decor')}}">Home Decor</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-decor2')}}">Home Decor 2</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-fashion-vertical')}}">Home Fashion
                                                        Vertical</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-electric')}}">Home Electric</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-electric-vertical')}}">Home Electric
                                                        Vertical</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-digital')}}">Home Digital</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-one-product-store')}}">One Product Store <span class="badge-tag badge bg-danger">Hot</span></a>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-handmade')}}">Home Handmade</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-fashion-trend')}}">Home Fashion Trend</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-kids')}}">Home Kids</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-sport')}}">Home Sport <span class="badge-tag badge bg-teal">new</span></a>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-jewelry')}}">Home Jewelry <span class="badge-tag badge bg-teal">new</span></a>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('home_pages/home-default') }}" class="dropdown-menu-title">Header
                                                Layouts</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="{{ url('home_pages.home-header-01') }}">Header Layout 1</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages.home-header-02') }}">Header Layout 2</a></li>
                                                <li><a class="text-muted" href="{{ url('index')}}">Header Layout 3</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-header-04')}}">Header Layout 4</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-electric')}}">Header Layout 5</a></li>
                                                <li><a class="text-muted" href="{{ ('home_pages.home-header-06')}}">Header Layout 6</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-fashion-vertical')}}">Header Layout 7</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-electric-vertical')}}">Header Layout 8</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-decor')}}">Header Transparent</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="#!" class="dropdown-menu-title">FEATURES</a>
                                            <ul class="sub-column-menu">
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter Options <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted" href="{{ url('index')}}">Catalog mode</a></li>
                                                <li><a class="text-muted" href="{{ url('shop_pages/shop')}}">Cookies law info</a></li>
                                                 <li><a class="text-muted" href="{{ url('home_pages/home-age-verified')}}">Age verification</a></li>
                                                <li><a class="text-muted" href="{{ url('index')}}">Mega menu</a></li>
                                                <li><a class="text-muted" href="{{ url('home_pages/home-parallax')}}">Footer sticky</a></li>
                                                <li><a class="text-muted" href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a></li>
                                                <li><a class="text-muted" href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden sidebar</a></li>
                                                <li><a class="text-muted" href="{{ url('shop_pages/checkout')}}">Checkout</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-frequently-bought-together')}}">Frequently
                                                        Bought Together</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-variant-images-grouped')}}">Variant
                                                        Images Grouped</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-rtl')}}">Demo RTL <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('shop_pages/shop-grid-list-switcher')}}">Grid/List
                                                        switcher <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-shoes')}}">Compare  <span class="badge-tag badge bg-teal">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-pickup-availability')}}">Pickup
                                                        Availability <span class="position-absolute badge rounded-pill fw-normal text-white" style="right:-40px; top: 2px; background-color: #01bad4;">SellingFeature</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link position-relative" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                                Shop
                            </a>
                            <div class="dropdown-menu p-3">
                                <div class="row g-0">
                                    <div class="col-lg-5">
                                        <div class="row g-0">
                                            <div class="col-lg-6">
                                                <div class="dropdown-sub-column-item">
                                                    <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">SHOP PAGES</a>
                                                    <ul class="sub-column-menu">
                                                        <li>
                                                            <a class="text-muted" href="{{ url('shop_pages/shop')}}">Grid Layout</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-packery-layout')}}">Packery
                                                                Layout</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-masonry-layout')}}">Masonry
                                                                Layout</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-full-width-layout')}}">Full Width
                                                                Layout</a></li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-1600px-layout')}}">1600px Layout</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{url( 'shop_pages.shop-left-sidebar')}}">Left Sidebar</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden
                                                                sidebar</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shopping-cart')}}">Shopping cart</a></li>

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="dropdown-sub-column-item">

                                                    <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">FEATURES</a>
                                                    <ul class="sub-column-menu">
                                                        <li><a class="text-muted position-relative d-inline-flex" href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter
                                                                options <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-load-more')}}">Load more button</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-Infinite-scrolling') }}">Infinite
                                                                scrolling</a></li>
                                                        <li><a class="text-muted" href="{{ url('index')}}">Catalog mode</a></li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop')}}">Cookies law info</a></li>
                                                        <li><a class="text-muted" href="{{ url('home_pages/home-age-verified')}}">Age
                                                                verification</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('index')}}">Mega menu</a></li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden Search</a>
                                                        </li>
                                                        <li><a class="text-muted" href="{{ url('shop_pages/checkout')}}">Checkout</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="row p-4">
                                            <div class="col-lg-4 p-0">
                                                <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                    <img src="{{ URL::asset('/build/images/home-hand-made/pr-01.jpg')}}" alt="" class="img-fluid">
                                                    <a class="mt-3" href="{{ url('product/product-detail-layout-01')}}">Pendant Key Ornaments</a>
                                                    <p class="text-muted"> $35.00</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 p-0">
                                                <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                    <img src="{{ URL::asset('/build/images/home-hand-made/pr-02.jpg')}}" alt="" class="img-fluid">
                                                    <a class="mt-3" href="{{ url('product/product-detail-layout-01')}}">Abstract Folded Pots</a>
                                                    <p class="text-muted"> $52.00</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 p-0">
                                                <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                    <img src="{{ URL::asset('/build/images/home-hand-made/pr-04.jpg')}}" alt="" class="img-fluid">
                                                    <a class="mt-3" href="{{ url('product/product-detail-layout-01')}}">Adhesive Tape Dispenser</a>
                                                    <p class="text-muted"> $15.00</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl">
                            <a class="nav-link fw-medium" href="{{ url('product/product-detail-layout-01')}}" data-bs-toggle="dropdown" aria-expanded="false">
                                Product
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT LAYOUT</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Product Detail
                                                        Layout
                                                        1</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-02')}}">Product Detail
                                                        Layout
                                                        2</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-03')}}">Product Detail
                                                        Layout
                                                        3</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-thumb-bottom')}}">Product thumb at
                                                        bottom</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-thumb-right')}}">Product thumb on
                                                        right</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-without-thumbnail')}}">Product
                                                        without
                                                        thumbnail</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-left-sidebar')}}">Left Sidebar</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-right-sidebar')}}">Right
                                                        sidebar</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-sidebar-full-height')}}">Sidebar
                                                        Full
                                                        Height</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-tab-accordion')}}">Product Tab
                                                        Accordions</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-full-width-atc')}}">Product Full
                                                        Width
                                                        ATC</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-full-width')}}">Product full width
                                                        layout</a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-advance-product-type')}}">
                                                        Advance Product Type <span class="badge-tag badge bg-danger">Hot</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT DETAIL</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="{{ url('product/product-detail-external-affiliate')}}">External/Affiliate
                                                        Product</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-simple-product')}}">Simple
                                                        product</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Variable product</a>
                                                </li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-grouped-product')}}">Grouped
                                                        Product <span class="badge-tag badge bg-danger">Hot</span></a></li>

                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-02')}}">Inner Zoom #1</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">External Zoom</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-03')}}">Inner Zoom #2</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">PhotoSwipe Popup</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-description-with-product')}}">Description
                                                        with product</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-description-with-instagram-shop')}}">Description
                                                        with instagram shop</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-product-video')}}">Product
                                                        video</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-3d-ar-models')}}">Product 3D, AR
                                                        models</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT SWATCH</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Product Color
                                                        Swatch</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-color')}}">Product Gallery
                                                        Swatch</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-color')}}">Product Images
                                                        Swatch</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-color')}}">Swatch Color</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Swatch Color
                                                        Circle</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-radio')}}">Swatch Radio</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-radio-color')}}">Swatch
                                                        Radio
                                                        Color</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-rectangle')}}">Swatch
                                                        Rectangle</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-rectangle-color')}}">Swatch
                                                        Rectangle Color</a></li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-simple')}}">Swatch
                                                        Simple</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-swatch-simple-color')}}">Swatch
                                                        Simple
                                                        Color</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="dropdown-sub-column-item">
                                            <a href="{{ url('product/product-detail-layout-01')}}" class="dropdown-menu-title">PRODUCT FEATURES</a>
                                            <ul class="sub-column-menu">
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-frequently-bought-together')}}" style="white-space: nowrap;">Frequently
                                                        Bought Together <span class="position-absolute badge bg-danger rounded-pill fw-normal text-white" style="right:-40px; top: 2px; ">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="{{ ('product.product-detail-pre-orders')}}">Product
                                                        pre-orders</a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-tab-accordion')}}">Product Upsell<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-description-with-lookbook')}}">Description
                                                        with Lookbook<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="{{ ('product.product-detail-back-in-stock-notification')}}">Back
                                                        in
                                                        stock notification</a></li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ ('product.product-detail-variant-images-grouped')}}">Variant
                                                        Images Grouped<span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Size Guide HTML</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Delivery &
                                                        Return</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-layout-01')}}">Ask a Question</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-product-sticky')}}">Product
                                                        sticky</a>
                                                </li>
                                                <li><a class="text-muted" href="{{ url('product/product-detail-360-viewer')}}">360° product
                                                        viewer</a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-swatch-radio')}}" style="white-space: nowrap;">Dynamic checkout
                                                        buttons <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                                <li>
                                                    <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-layout-01')}}">Sticky add to
                                                        cart <span class="badge-tag badge bg-danger">Hot</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="navbar-brand nav-link" href="{{ url('index')}}"><img src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="" width="95"></a>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-3xl">
                            <a class="nav-link fs-16 position-relative fw-medium" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                                Sale
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dropdown-sub-column-item">
                                            <a href="#!" class="dropdown-menu-title">Strapless</a>
                                            <a href="#!" class="dropdown-menu-title">Micro Bikini</a>
                                            <a href="#!" class="dropdown-menu-title">Women</a>
                                            <a href="#!" class="dropdown-menu-title">High Waisted</a>
                                            <a href="#!" class="dropdown-menu-title">Trikini</a>
                                            <a href="#!" class="dropdown-menu-title">Flounce</a>
                                            <a href="#!" class="dropdown-menu-title">Sling Bikini</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <!-- Swiper -->
                                        <div class="swiper mySwiper">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide">
                                                    <div>
                                                        <div x-data="{ imageUrl: '/build/images/home-hand-made/pr-01.jpg', isHovered: false }" class="topbar-product-card overlay-hover" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                                            <div class="position-relative overflow-hidden main">
                                                                <img :src="isHovered ? '/build/images/home-hand-made/pr-01.jpg' : imageUrl" alt="" class="img-fluid">
                                                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                                        <i class="iccl iccl-cart"></i></button>
                                                                </div>
                                                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                                                </div>

                                                            </div>
                                                            <div class="mt-3">
                                                                <h6 class="mb-1 fs-16 fw-medium font-avenir"><a href="{{ url('product/product-detail-layout-01')}}" class="text-reset">Pendant Key Ornaments</a></h6>
                                                                <p class="mb-0 fs-15 text-muted">
                                                                    <span>$35.00</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div class="col">
                                                        <div x-data="{ imageUrl: '/build/images/home-hand-made/pr-02.jpg', isHovered: false }" class="topbar-product-card overlay-hover" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                                            <div class="position-relative overflow-hidden main">
                                                                <img :src="isHovered ? '/build/images/home-hand-made/pr-03.jpg' : imageUrl" alt="" class="img-fluid">
                                                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>

                                                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                                        <i class="iccl iccl-cart"></i></button>
                                                                </div>
                                                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <h6 class="mb-1 fs-16 fw-medium font-avenir"><a href="{{ url('product/product-detail-layout-01')}}" class="text-reset">Abstract Folded Pots</a></h6>
                                                                <p class="mb-0 fs-15 text-muted">
                                                                    <span>$52.00</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div class="col">
                                                        <div x-data="{ imageUrl: '/build/images/home-hand-made/pr-06.jpg', isHovered: false }" class="topbar-product-card overlay-hover" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                                            <div class="position-relative overflow-hidden main">
                                                                <span class="new-label bg-danger text-white rounded-circle"> -20% </span>
                                                                <img :src="isHovered ? '/build/images/home-hand-made/pr-06.jpg' : imageUrl" alt="" class="img-fluid">
                                                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>

                                                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                                        <i class="iccl iccl-cart"></i></button>
                                                                </div>
                                                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <h6 class="mb-1 fs-16 fw-medium font-avenir"><a href="{{ url('product/product-detail-layout-01')}}" class="text-reset">Antique Sewing Scissors</a></h6>
                                                                <p class="mb-0 fs-15 text-muted">
                                                                    <del>$15.00</del>
                                                                    <span class="text-danger">$12.00</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div class="col">
                                                        <div x-data="{ imageUrl: '/build/images/home-hand-made/pr-08.jpg', isHovered: false }" class="topbar-product-card overlay-hover" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                                            <div class="position-relative overflow-hidden main">
                                                                <img :src="isHovered ? '/build/images/home-hand-made/pr-05.jpg' : imageUrl" alt="" class="img-fluid">
                                                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>

                                                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                                        <i class="iccl iccl-cart"></i></button>
                                                                </div>
                                                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <h6 class="mb-1 fs-16 fw-medium font-avenir"><a href="{{ url('product/product-detail-layout-01')}}" class="text-reset">Bouncer Measuring Cup</a></h6>
                                                                <p class="mb-0 fs-15 text-muted">
                                                                    <span>$350.00</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end slide-->
                                                <div class="swiper-slide">
                                                    <div class="col">
                                                        <div x-data="{ imageUrl: '/build/images/home-hand-made/pr-02.jpg', isHovered: false }" class="topbar-product-card overlay-hover" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                                                            <div class="position-relative overflow-hidden main">
                                                                <img :src="isHovered ? '/build/images/home-hand-made/pr-03.jpg' : imageUrl" alt="" class="img-fluid">
                                                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>

                                                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                                                        <i class="iccl iccl-cart"></i></button>
                                                                </div>
                                                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <h6 class="mb-1 fs-16 fw-medium font-avenir"><a href="{{ url('product/product-detail-layout-01')}}" class="text-reset">Abstract Folded Pots</a></h6>
                                                                <p class="mb-0 fs-15 text-muted">
                                                                    <span>$52.00</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end slide-->
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link fw-medium" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Portfolio
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="{{ url('portfolio/portfolio')}}">Portfolio 2 Columns</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/portfolio-4-columns')}}">Portfolio 4 Columns</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-shop')}}">Single Portfolio With Shop</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With Lookbook</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-instagram-shop')}}">Portfolio With Instagram
                                        Shop</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link fw-medium" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Lookbook
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook')}}">Lookbook Slider</a></li>
                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook-collection')}}">Lookbook Section</a></li>
                                <li><a class="text-muted" href="{{ url('index')}}">Lookbook instagram</a></li>
                                <li><a class="text-muted" href="{{ url('product/product-detail-description-with-lookbook')}}">Lookbook in product</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-lookbook')}}">Lookbook in blog post</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Lookbook in portfolio post</a></li>
                                <li><a class="text-muted" href="{{ url('blog/lookbook-in-page')}}">Lookbook in page</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link fw-medium" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Blog
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="#!">Grid Layout</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-masonry')}}">Masonry Layout</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-left-sidebar')}}">Left Sidebar</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-right-sidebar')}}">Right Sidebar</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-product-listing')}}">Single Post with Product Listing</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Instagram Shop</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Categories</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-lookbook')}}">Single Post with lookbook</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="navbar-nav header-offcanvas d-lg-none" tabindex="-1">
                    <!-- close icon -->
                    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
                        <i class="las la-times"></i>
                    </a>
                    <div class="offcanvas-body p-0">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item " role="presentation">
                                <button class="nav-link active text-uppercase" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Menu</button>
                            </li>
                            <li class="nav-item col-6 p-0" role="presentation">
                                <button class="nav-link text-uppercase" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">categories</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    <div class="accordion-item rounded-0">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-05" aria-expanded="false" aria-controls="panelsStayOpen-collapse-05">
                                                Demo
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapse-05" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-05">
                                            <div class="accordion" id="accordi-05xample">
                                                <div class="accordion-item rounded-0">
                                                    <h2 class="accordion-header" id="heading-05">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-05" aria-expanded="true" aria-controls="collapse-05">
                                                            Home Page
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-05" class="accordion-collapse collapse" aria-labelledby="heading-05" data-bs-parent="#accordi-05xample">
                                                        <!-- Updated data-bs-parent -->
                                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                                            <li>
                                                                <a href="{{ url('home_pages/home-default') }}">Home Default</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-classic')}}">Home Classic</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-video-banner') }}">Home Video Banner</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-categories-links')}}">Home Categories
                                                                    Links</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-static-image')}}">Home Static Image</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-metro')}}">Home Metro</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-lookbook')}}">Home Lookbook</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-parallax')}}">Home Parallax</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-instagram-shop')}}">Home Instagram
                                                                    Shop</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-medical')}}">Home Medical
                                                                    <span class="lbc_nav lb_menu_hot ml__5">Hot</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-fashion9')}}">Home Fashion 9</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-lookbook-collection')}}">Home Lookbook
                                                                    Collection</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-fashion-simple')}}">Home Fashion
                                                                    Simple</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-fashion10')}}">Home Fashion 10</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-decor')}}">Home Decor</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-decor2')}}">Home Decor 2</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-fashion-vertical')}}">Home Fashion
                                                                    Vertical</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-electric')}}">Home Electric</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-electric-vertical')}}">Home Electric
                                                                    Vertical</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="accordion-item border-0 rounded-0">
                                                    <h2 class="accordion-header" id="heading-06">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-06" aria-expanded="true" aria-controls="collapse-06">
                                                            Home Layouts
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-06" class="accordion-collapse collapse" aria-labelledby="heading-06" data-bs-parent="#accordi-05xample">
                                                        <!-- Updated data-bs-parent -->
                                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                                            <li>
                                                                <a href="{{ url('home_pages.home-header-01') }}">Header Layout 1</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages.home-header-02') }}">Header Layout 2</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('index')}}">Header Layout 3</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-header-04')}}">Header Layout 4</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-electric')}}">Header Layout 5</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ ('home_pages.home-header-06')}}">Header Layout 6</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-fashion-vertical')}}">Header Layout 7</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-electric-vertical')}}">Header Layout
                                                                    8</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('home_pages/home-decor')}}">Header Transparent</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                                Shop
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="{{ url('shop_pages/shop')}}">Grid Layout</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-packery-layout')}}">Packery Layout</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-masonry-layout')}}">Masonry Layout</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-full-width-layout')}}">Full Width Layout</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-1600px-layout')}}">1600px Layout</a>
                                                </li>
                                                <li>
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}">Left Sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop')}}">Filters area</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shop-filter-sidebar')}}">Filters sidebar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('shop_pages/shopping-cart')}}">Shopping cart</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                                Product
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="{{ url('product/product-detail-external-affiliate')}}">External/Affiliate
                                                        Product</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-simple-product')}}">Simple product</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-layout-01')}}">Variable product</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-grouped-product')}}">Group Product
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-layout-02')}}">Inner Zoom #1</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-layout-01')}}">External Zoom</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-layout-03')}}">Inner Zoom #2</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-layout-01')}}">PhotoSwipe Popup</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-description-with-product')}}">Description
                                                        with product</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-description-with-instagram-shop')}}">Description
                                                        with
                                                        instagram shop</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-product-video')}}">Product video
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-3d-ar-models')}}">Product 3D, AR models
                                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <a href="#!" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Sale</button>
                                    </a>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                                Portfolio
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="{{ url('portfolio/portfolio')}}">Portfolio 2 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/portfolio-4-columns')}}">Portfolio 4 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/single-portfolio-with-shop')}}">Single Portfolio With
                                                        Shop</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With
                                                        Lookbook</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With
                                                        Lookbook</a>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                                Lookbook
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="{{ url('home_pages/home-lookbook')}}">Lookbook Slider</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('home_pages/home-lookbook-collection')}}">Lookbook Section</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('home_pages/home-default') }}">Lookbook instagram</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('product/product-detail-description-with-lookbook')}}">Lookbook in
                                                        product</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Lookbook in portfolio
                                                        post</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('blog/lookbook-in-page')}}">Lookbook in page</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <a href="#!" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Blog</button>
                                    </a>
                                    <a href="{{ url('blog/wishlist')}}" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-heart fw-medium me-1"></i> Wishlist</button>
                                    </a>
                                    <a href="#!" class="pill-item col-6 p-0" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-search fw-medium me-1"></i> Search</button>
                                    </a>
                                    <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-user fw-medium me-1"></i> Login /
                                            Register</button>
                                    </a>

                                    <div class="pill-item col-6 p-0 w-100" role="presentation">
                                        <button class="nav-link border-0" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Need Help?</button>
                                        <ul class="list-unstyled need-help mb-0">
                                            <li>
                                                <a href="#!" class="d-flex align-items-center">
                                                    <i class="pegk pe-7s-call me-1"></i>
                                                    <p class="mb-0">+01 23456789</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="mailto:claue@domain.com" class="d-flex mt-2 align-items-center">
                                                    <i class="pegk pe-7s-mail fwb me-2"></i>
                                                    <p class="mb-0">claue@domain.com</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingSeven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
                                                USD
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingSeven">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">AUD
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">CAD
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">DKK
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">DKK
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">EUR
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">GBP
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#!">
                                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">USD
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                <ul class="accordion-nav-list accordion-navs-list list-unstyled mb-0">
                                    <li>
                                        <a href="#"><i class="las la-female me-2"></i>Women’s Clothing</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-male me-2"></i>Men’s Clothing</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-clock me-2"></i>Watches</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-glasses me-2"></i>Accessories</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-camera-retro me-2"></i>Electric</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-shoe-prints me-2"></i>Shoes</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-gem me-2"></i>Jewellery</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-tshirt me-2"></i>T-Shirt</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-child me-2"></i>Toys, Kids &amp; Baby</a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-chair me-2"></i>Decor</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3 lingeries-header">
                <a href="#!" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="iccl iccl-search"></i>
                </a>
                <a class="d-md-block d-none" data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="iccl iccl-user"></i></a>
                <a class="d-md-block d-none" href="{{ url('blog/wishlist')}}"><i class="iccl iccl-heart"></i><span class="tcount bg-danger text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-danger text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
            </div>
        </div>
    </nav>
</div>

<!-- custome header -->
<div class="header-offcanvas offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <!-- close icon -->
    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
        <i class="las la-times"></i>
    </a>
    <div class="offcanvas-body p-0">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item " role="presentation">
                <button class="nav-link active text-uppercase" id="pills-menu-tab" data-bs-toggle="pill" data-bs-target="#pills-menu" type="button" role="tab" aria-controls="pills-menu" aria-selected="true">Menu</button>
            </li>
            <li class="nav-item col-6 p-0" role="presentation">
                <button class="nav-link text-uppercase" id="pills-categories-tab" data-bs-toggle="pill" data-bs-target="#pills-categories" type="button" role="tab" aria-controls="pills-categories" aria-selected="false">categories</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-menu" role="tabpanel" aria-labelledby="pills-menu-tab" tabindex="0">
                <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item rounded-0">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-05" aria-expanded="false" aria-controls="panelsStayOpen-collapse-05">
                                Demo
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapse-05" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-05">
                            <div class="accordion" id="accordi-05xample">
                                <div class="accordion-item rounded-0">
                                    <h2 class="accordion-header" id="heading-05">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-05" aria-expanded="true" aria-controls="collapse-05">
                                            Home Page
                                        </button>
                                    </h2>
                                    <div id="collapse-05" class="accordion-collapse collapse" aria-labelledby="heading-05" data-bs-parent="#accordi-05xample">
                                        <!-- Updated data-bs-parent -->
                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                            <li>
                                                <a href="{{ url('home_pages/home-default') }}">Home Default</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-classic')}}">Home Classic</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-video-banner') }}">Home Video Banner</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-categories-links')}}">Home Categories Links</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-static-image')}}">Home Static Image</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-metro')}}">Home Metro</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-lookbook')}}">Home Lookbook</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-parallax')}}">Home Parallax</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-instagram-shop')}}">Home Instagram Shop</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-medical')}}">Home Medical
                                                    <span class="lbc_nav lb_menu_hot ml__5">Hot</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion9')}}">Home Fashion 9</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-lookbook-collection')}}">Home Lookbook Collection</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion-simple')}}">Home Fashion Simple</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion10')}}">Home Fashion 10</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-decor')}}">Home Decor</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-decor2')}}">Home Decor 2</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion-vertical')}}">Home Fashion Vertical</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-electric')}}">Home Electric</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-electric-vertical')}}">Home Electric Vertical</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="accordion-item border-0 rounded-0">
                                    <h2 class="accordion-header" id="heading-06">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-06" aria-expanded="true" aria-controls="collapse-06">
                                            Home Layouts
                                        </button>
                                    </h2>
                                    <div id="collapse-06" class="accordion-collapse collapse" aria-labelledby="heading-06" data-bs-parent="#accordi-05xample">
                                        <!-- Updated data-bs-parent -->
                                        <ul class="accordion-nav-list list-unstyled mb-0">
                                            <li>
                                                <a href="{{ url('home_pages.home-header-01') }}">Header Layout 1</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages.home-header-02') }}">Header Layout 2</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('index')}}">Header Layout 3</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-header-04')}}">Header Layout 4</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-electric')}}">Header Layout 5</a>
                                            </li>
                                            <li>
                                                <a href="{{ ('home_pages.home-header-06')}}">Header Layout 6</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-fashion-vertical')}}">Header Layout 7</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-electric-vertical')}}">Header Layout 8</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('home_pages/home-decor')}}">Header Transparent</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                Shop
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="{{ url('shop_pages/shop')}}">Grid Layout</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-packery-layout')}}">Packery Layout</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-masonry-layout')}}">Masonry Layout</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-full-width-layout')}}">Full Width Layout</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-1600px-layout')}}">1600px Layout</a>
                                </li>
                                <li>
                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}">Left Sidebar</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-right-sidebar')}}">Right Sidebar</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-hidden-sidebar')}}">Hidden sidebar</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop')}}">Filters area</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shop-filter-sidebar')}}">Filters sidebar</a>
                                </li>
                                <li>
                                    <a href="{{ url('shop_pages/shopping-cart')}}">Shopping cart</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                Product
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="{{ url('product/product-detail-external-affiliate')}}">External/Affiliate Product</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-simple-product')}}">Simple product</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-layout-01')}}">Variable product</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-grouped-product')}}">Group Product
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-layout-02')}}">Inner Zoom #1</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-layout-01')}}">External Zoom</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-layout-03')}}">Inner Zoom #2</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-layout-01')}}">PhotoSwipe Popup</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-description-with-product')}}">Description with product</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-description-with-instagram-shop')}}">Description with
                                        instagram shop</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-product-video')}}">Product video
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-3d-ar-models')}}">Product 3D, AR models
                                        <span class="badge bg-teal fw-normal rounded-pill">hot</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="#!" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Sale</button>
                    </a>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                Portfolio
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="{{ url('portfolio/portfolio')}}">Portfolio 2 Columns</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/portfolio-4-columns')}}">Portfolio 4 Columns</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/single-portfolio-with-shop')}}">Single Portfolio With Shop</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With Lookbook</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With Lookbook</a>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                Lookbook
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}">Lookbook Slider</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook-collection')}}">Lookbook Section</a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-default') }}">Lookbook instagram</a>
                                </li>
                                <li>
                                    <a href="{{ url('product/product-detail-description-with-lookbook')}}">Lookbook in product</a>
                                </li>
                                <li>
                                    <a href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Lookbook in portfolio post</a>
                                </li>
                                <li>
                                    <a href="{{ url('blog/lookbook-in-page')}}">Lookbook in page</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="#!" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Blog</button>
                    </a>
                    <a href="{{ url('blog/wishlist')}}" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-heart fw-medium me-1"></i> Wishlist</button>
                    </a>
                    <a href="#!" class="pill-item col-6 p-0" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-search fw-medium me-1"></i> Search</button>
                    </a>
                    <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-user fw-medium me-1"></i> Login /
                            Register</button>
                    </a>

                    <div class="pill-item col-6 p-0 w-100" role="presentation">
                        <button class="nav-link border-0" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Need Help?</button>
                        <ul class="list-unstyled need-help mb-0">
                            <li>
                                <a href="#!" class="d-flex align-items-center">
                                    <i class="pegk pe-7s-call me-1"></i>
                                    <p class="mb-0">+01 23456789</p>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:claue@domain.com" class="d-flex mt-2 align-items-center">
                                    <i class="pegk pe-7s-mail fwb me-2"></i>
                                    <p class="mb-0">claue@domain.com</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
                                USD
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingSeven">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">AUD
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">CAD
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">DKK
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">DKK
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">EUR
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">GBP
                                    </a>
                                </li>
                                <li>
                                    <a href="#!">
                                        <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" class="map-img" alt="">USD
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-categories" role="tabpanel" aria-labelledby="pills-categories-tab" tabindex="0">
                <ul class="accordion-nav-list accordion-navs-list list-unstyled mb-0">
                    <li>
                        <a href="#"><i class="las la-female me-2"></i>Women’s Clothing</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-male me-2"></i>Men’s Clothing</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-clock me-2"></i>Watches</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-glasses me-2"></i>Accessories</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-camera-retro me-2"></i>Electric</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-shoe-prints me-2"></i>Shoes</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-gem me-2"></i>Jewellery</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-tshirt me-2"></i>T-Shirt</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-child me-2"></i>Toys, Kids &amp; Baby</a>
                    </li>
                    <li>
                        <a href="#"><i class="las la-chair me-2"></i>Decor</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--search Modal -->
<div class="modal fade modal-overl " id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen header-search-model">
        <div class="modal-content">
            <div class="modal-body p-0 mb-4">
                <form class="row g-2 mx-2 mx-md-5 my-4 pb-3 pt-4" action="#!">
                    <div class="col-md-3">
                        <select class="form-select rounded-pill w-100">
                            <option value="*">All Categories</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Bag">Bag</option>
                            <option value="Camera">Camera</option>
                            <option value="Decor">Decor</option>
                            <option value="Earphones">Earphones</option>
                            <option value="Electric">Electric</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Headphone">Headphone</option>
                            <option value="Men">Men</option>
                            <option value="Shoes">Shoes</option>
                            <option value="Speaker">Speaker</option>
                            <option value="Watch">Watch</option>
                            <option value="Women">Women</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <div class="search-box position-relative">
                            <input type="text" class="form-control rounded-pill" id="exampleFormControlsearch" placeholder="Search for products">
                            <button type="submit" class="btn"><i class="iccl iccl-search"></i></button>
                        </div>
                    </div>
                </form>
                <p class="need-sec fs-16 fw-medium p-2 text-center">Need some inspiration?</p>
                <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 row-cols-xxl-auto mx-auto mt-4 justify-content-center px-lg-5">
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-01.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium"><a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">Analogue Resin
                                    Strap</a></h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$30.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-02.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">Ridley High
                                    Waist</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$36.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-03.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Blush Beanie </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$15.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-04.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Cluse La Boheme Rose <br /> Gold </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$60.00</del>
                                <span class="text-danger">$45.00</span>
                                <span class="badge bg-danger fw-normal text-white rounded-0">-25%</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-05.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Mercury Tee </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$68.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-06.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    La Bohème Rose Gold </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$60.00</del>
                                <span class="text-danger">$40.00</span>
                                <span class="badge bg-danger fw-normal text-white rounded-0">-34%</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-07.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Cream women pants </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$35.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-08.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Black mountain hat </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$50.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-09.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a class="product-title db" href="{{ url('product/product-detail-layout-01')}}">Short Sleeved
                                    Hoodie</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$45.00</del>
                                <span class="text-danger">$30.00</span>
                                <span class="text-danger">$34.00</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-10.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Black mountain hat </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$35.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-11.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Men pants </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$49.00–$56.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-12.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a class="product-title db" href="{{ url('product/product-detail-layout-01')}}">Skin
                                    Sweatpans</a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$75.00</del>
                                <span class="text-danger">$45.00</span>
                                <span class="text-danger">$40.00</span>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-13.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Simple Skin T-shirt </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$56.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-14.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Sunny Life </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$68.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-15.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Leather White Trainers </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$20.00</del>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <img src="{{ URL::asset('/build/images/home-fashion-9/pr-s-16.jpg')}}" alt="">
                        <div class="mt-4 text-center">
                            <h6 class="mb-1 text-capitalize fs-14 fw-medium">
                                <a href="{{ url('product/product-detail-layout-01')}}" class="main_link_teal">
                                    Stripe Long Sleeve Top </a>
                            </h6>
                            <p class="mb-0 fs-14 text-muted">
                                <del>$15.00</del>
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn search-model-close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!--account offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="accountOffcanvas" aria-labelledby="accountOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="accountOffcanvasLabel">LOGIN</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="#!" class="mb-4">
                <div class="mb-3">
                    <label for="emailInputOffcanvas" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="emailInputOffcanvas">
                </div>
                <div class="mb-3 pb-1">
                    <label for="passwordInputOffcanvas" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="passwordInputOffcanvas">
                </div>
                <div>
                    <button type="submit" class="btn btn-info w-100 rounded-pill">SIGN IN</button>
                </div>
            </form>
            <p class="text-muted">New customer? <a href="#!" class="product-title">Create your account</a></p>
            <p class="text-muted">Lost password? <a href="#!" class="product-title">Recover password</a></p>
        </div>
    </div>
</div>

<!--Shopping Cart offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="shoppingCartOffcanvas" aria-labelledby="shoppingCartnvasLabel">
    <div class="offcanvas-header p-20">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="shoppingCartnvasLabel">Shopping Cart</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="p-20 border-bottom border-top shadow-2xl">
        <h6 class="mb-0 fw-medium fs-13 lh-base">Almost there, add <span class="text-danger">$9.00</span> more to get
            <span class="text-danger">FREE SHIPPING!</span>
        </h6>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-20">
            <div class="row">
                <div class="col-lg-5">
                    <img src="{{ URL::asset('/build/images/mini-cart/mini-cart-01.jpg')}}" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">La Bohème Rose Gold</a></h6>
                    <p class="text-muted fs-12">Pink</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <del>$60.00</del>
                        <span class="text-danger">$40.00</span>
                    </p>
                    <div x-data="{ quantity: 2 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-20 border-top">
            <div class="row">
                <div class="col-lg-5">
                    <img src="{{ URL::asset('/build/images/mini-cart/mini-cart-02.jpg')}}" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">Blush Beanie</a></h6>
                    <p class="text-muted fs-12">Grey / S</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <span>$15.00</span>
                    </p>
                    <div x-data="{ quantity: 13 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-20 border-top">
            <div class="row">
                <div class="col-lg-5">
                    <img src="{{ URL::asset('/build/images/mini-cart/mini-cart-03.jpg')}}" alt="" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <h6 class="mb-1"><a href="#!" class="product-title">Ridley High Waist</a></h6>
                    <p class="text-muted fs-12">S</p>

                    <p class="fs-14 text-muted d-flex align-items-center gap-2">
                        <span>$36.00</span>
                    </p>
                    <div x-data="{ quantity: 7 }" class="quantity fs-14 position-relative">
                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                            <i class="facl facl-minus"></i>
                        </button>
                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                            <i class="facl facl-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <a href="#!" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-20 py-3">
            <ul class="mini_cart_tool list-unstyled d-flex gap-3 justify-content-center mb-0">
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="lar la-clipboard"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-gift"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-truck-moving"></i></a>
                </li>
                <li>
                    <a href="#!" class="square-sm rounded-circle fs-25 d-inline-flex text-reset align-items-center justify-content-center"><i class="las la-tag"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="offcanvas-footer p-20 border-top">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mb-0 flex-grow-1 fs-18">Subtotal:</h5>
            <a href="#!" class="cart_tot_price fs-18 texrt-reset">$91.00</a>
        </div>
        <p class="text-muted fs-13 mb-2">Taxes, shipping and discounts codes calculated at checkout</p>
        <div class="form-check form-check-info">
            <input class="form-check-input" type="checkbox" value="" id="cartAgreeInput">
            <label class="form-check-label fs-13 text-muted" for="cartAgreeInput">
                I agree with the terms and conditions.
            </label>
        </div>
        <div class="mt-3 vstack gap-3">
            <button type="button" class="btn btn-light w-100 rounded-pill">View cart</button>
            <button type="button" class="btn btn-info w-100 rounded-pill">Check out</button>
        </div>
        <div class="mt-3">
            <img src="{{ URL::asset('/build/images/trust_img2.png')}}" alt="" class="img-fluid">
        </div>
    </div>
</div>
<div class="backdrop-shadow d-none"></div>

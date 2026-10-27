<div id="kalles-section-header_top" class="medical-navbar">
    <div class="h__top d-flex align-items-center border-bottom">
        <div class="container">
            <div class="row align-items-center align-items-md-start">
                <div class="col-xl-4 col-lg-5 col-12 col-md-12">
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                        <p class="mb-0"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i> +01 23456789</p>
                        <p class="mb-0"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 col-12 col-md-12 d-none d-lg-block">
                    <div class="header-text text-center fs-12">
                        <span class="badge d-inline-block fs-12 fw-normal">COVID-19 UPDATE</span> We are open with
                        limited hours and staff.
                    </div>
                </div>
                <div class="col-xl-4 col-lg-2    col-12 col-md-12 d-none d-lg-block">
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
            <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <a class="navbar-brand" href="{{ url('index')}}"><img src="{{ URL::asset('/build/images/home-medical/kalles-medical-170.png')}}" alt="" width="130"></a>
            <div class="collapse navbar-collapse mobile-menu-navbar" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
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
                                            <li><a class="text-muted position-relative d-inline-flex" href="{{ url('shop_pages/shop-filter-sidebar')}}">Filter Options <span class="badge-tag badge bg-danger">Hot</span></a></li>
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
                                                    Availability <span class="position-absolute badge bg-teal rounded-pill fw-normal text-white" style="left:  103%; top: 10px;">Selling feature</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-xxl">
                        <a class="nav-link position-relative" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                            Shop <span class="badge bg-teal fw-normal">New</span>
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
                                                        <a class="text-muted" href="#!">Blood Pressure</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Facemask</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Hospital Equipment</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Independent Living</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Medical Accessories</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Pharmacy</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="dropdown-sub-column-item">

                                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="dropdown-menu-title">FEATURES</a>
                                                <ul class="sub-column-menu">
                                                    <li>
                                                        <a class="text-muted" href="#!">Blood Pressure</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Facemask</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Hospital Equipment</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Independent Living</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Medical Accessories</a>
                                                    </li>
                                                    <li>
                                                        <a class="text-muted" href="#!">Pharmacy</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 ">
                                    <div class="row p-4">
                                        <div class="col-lg-6 cat-section p-0">
                                            <a href="{{ url('home_pages/home-medical')}}" class="d-block position-relative cat_grid_item overflow-hidden " style="height: 350px;">
                                                <div class="h-100 w-100 cat-grid-img" style="background-image: url('{{ asset('/build/images/home-medical/megamenu1-promotion-01.png')}}"></div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 cat-section ps-4 p-0">
                                            <a href="{{ url('home_pages/home-medical')}}" class="d-block position-relative cat_grid_item overflow-hidden " style="height: 350px;">
                                                <div class="h-100 w-100 cat-grid-img" style="background-image: url('{{ asset('/build/images/home-medical/megamenu1-promotion-02.png')}}"></div>
                                            </a>
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
                            <div class="row me-4">
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
                                            <li><a class="text-muted" href="{{ url('product/product-detail-advance-product-type')}}">Advance
                                                    Product Type</a></li>
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
                                                <a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-description-with-lookbook')}}" style="white-space: nowrap;">Description
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
                    <li class="nav-item dropdown dropdown-mega-3xl">
                        <a class="nav-link position-relative text-danger" href="{{ url('shop_pages/shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
                            Sale <span class="badge bg-warning">Sale</span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dropdown-sub-column-item">
                                        <a href="#!" class="dropdown-menu-title">Blood Pressure</a>
                                        <a href="#!" class="dropdown-menu-title">Face mask</a>
                                        <a href="#!" class="dropdown-menu-title">Hospital Equipment</a>
                                        <a href="#!" class="dropdown-menu-title">Independent Living</a>
                                        <a href="#!" class="dropdown-menu-title">Medical Accessories</a>
                                        <a href="#1" class="dropdown-menu-title">Pharmacy</a>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <!-- Swiper -->
                                    <div class="swiper mySwiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="img-zoom">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="overflow-hidden d-inline-block">
                                                        <img src="{{ URL::asset('/build/images/home-medical/cat-03.jpg')}}" alt="" class="img-fluid">
                                                    </a>
                                                    <div class="p-10 text-center">
                                                        <h5 class="fw-medium mb-2"><a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="text-reset">Medical
                                                                Accessories</a></h5>
                                                        <p class="mb-0">5 Products</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div class="img-zoom">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="overflow-hidden d-inline-block">
                                                        <img src="{{ URL::asset('/build/images/home-medical/cat-04.jpg')}}" alt="" class="img-fluid">
                                                    </a>
                                                    <div class="p-10 text-center">
                                                        <h5 class="fw-medium mb-2"><a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="text-reset">Personal</a></h5>
                                                        <p class="mb-0">8 Products</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div class="img-zoom">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="overflow-hidden d-inline-block">
                                                        <img src="{{ URL::asset('/build/images/home-medical/cat-05.jpg')}}" alt="" class="img-fluid">
                                                    </a>
                                                    <div class="p-10 text-center">
                                                        <h5 class="fw-medium mb-2"><a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="text-reset">Independent Living</a></h5>
                                                        <p class="mb-0">8 Products</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div class="img-zoom">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="overflow-hidden d-inline-block">
                                                        <img src="{{ URL::asset('/build/images/home-medical/cat-06.jpg')}}" alt="" class="img-fluid">
                                                    </a>
                                                    <div class="p-10 text-center">
                                                        <h5 class="fw-medium mb-2"><a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="text-reset">Pharmacy</a></h5>
                                                        <p class="mb-0">8 Products</p>
                                                    </div>
                                                </div>
                                            </div><!--end slide-->
                                            <div class="swiper-slide">
                                                <div class="img-zoom">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="overflow-hidden d-inline-block">
                                                        <img src="{{ URL::asset('/build/images/home-medical/cat-05.jpg')}}" alt="" class="img-fluid">
                                                    </a>
                                                    <div class="p-10 text-center">
                                                        <h5 class="fw-medium mb-2"><a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="text-reset">Independent Living</a></h5>
                                                        <p class="mb-0">8 Products</p>
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
                            <li><a class="text-muted" href="{{ url('portfolio/portfolio')}}">Portfolio 2 Columns</a></li>
                            <li><a class="text-muted" href="{{ url('portfolio/portfolio-3-columns')}}">Portfolio 3 Columns</a></li>
                            <li><a class="text-muted" href="{{ url('portfolio/portfolio-4-columns')}}">Portfolio 4 Columns</a></li>
                            <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-shop')}}">Single Portfolio With Shop</a></li>
                            <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Single Portfolio With Lookbook</a></li>
                            <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-instagram-shop')}}">Portfolio With Instagram Shop</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown dropdown-mega-lg">
                        <a class="nav-link" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <a class="nav-link" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Blog
                        </a>
                        <ul class="dropdown-menu dropdown-sub-column">
                            <li><a class="text-muted" href="#!">Grid Layout</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-masonry')}}">Masonry Layout</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-left-sidebar')}}">Left Sidebar</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-right-sidebar')}}">Right Sidebar</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-post-with-product-listing')}}">Single Post with Product Listing</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Instagram Shop</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Categories</a></li>
                            <li><a class="text-muted" href="{{ url('blog/blog-post-with-lookbook')}}">Single Post with lookbook</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3">
                <a class="d-block d-xl-none" data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-search"></i></a>
                <form class="search_header position-relative d-none d-xl-block" role="search">
                    <input class="form-control rounded-pill" autocomplete="off" type="text" name="q" placeholder="Search for products">
                    <button class="btn btn-link rounded-pill text-body" type="submit">
                        <i class="iccl iccl-search fw-bold"></i></button>
                </form>
                <a class="d-none d-md-block" data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="las la-user fs-28"></i></a>
                <a href="#!"><i class="lar la-heart fs-28"></i><span class="tcount bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                <a class="d-none d-md-block" data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="las la-shopping-cart fs-28"></i><span class="tcount bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
            </div>
        </div>
    </nav>
</div>

<div class="backdrop-shadow d-none"></div>

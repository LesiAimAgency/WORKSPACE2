<div id="kalles-section-header_top ">
    <div class="h__top text-white d-flex align-items-center bg-pink2">
        <div class="container">
            <div class="row align-items-center justify-content-center py-3 py-xl-0">
                <div class="col-md-5 col-lg-3 col-12">
                    <div class="d-flex justify-content-center justify-content-md-start">
                        <p class="mb-0"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i> +01 23456789</p>
                        <p class="mb-0 ms-2"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com
                        </p>
                    </div>
                </div>
                <div class="col-md-5 col-lg-6 col-12">
                    <div class="header-text text-center fs-12 py-1 py-lg-0">
                        Summer sale discount off <span class="fw-bold">50% </span> <a href="{{ url('shop_pages/shop')}}" class="text-reset">Shop Now</a>
                    </div>
                </div>
                <div class="col-md-2 col-lg-3 col-12">
                    <div class="dropdown text-md-end text-center position-relative">
                        <a href="#!" class="fs-12 text-reset currency-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ URL::asset('/build/images/svg/usd.svg')}}" alt="" height="12" class="me-1"> USD <i class="facl facl-angle-down ms-1"></i>
                        </a>
                        <ul class="dropdown-menu p-3 dropdown-currency">
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/aud.svg')}}" alt="" height="12" class="me-1">
                                    AUD</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/cad.svg')}}" alt="" height="12" class="me-1">
                                    CAD</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/dkk.svg')}}" alt="" height="12" class="me-1">
                                    DKK</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/eur.svg')}}" alt="" height="12" class="me-1">
                                    EUR</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/gbp.svg')}}" alt="" height="12" class="me-1">
                                    GBP</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/hkd.svg')}}" alt="" height="12" class="me-1">
                                    HKD</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/jpy.svg')}}" alt="" height="12" class="me-1">
                                    JPY</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/nzd.svg')}}" alt="" height="12" class="me-1">
                                    NZD</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/sgd.svg')}}" alt="" height="12" class="me-1">
                                    SGD</a></li>
                            <li><a class="main_link_primary" href="#!"><img src="{{ URL::asset('/build/images/svg/usd.svg')}}" alt="" height="12" class="me-1">
                                    USD</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-custom pt-4 pb-0 d-block align-items-center ">
        <div class="container">
            <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <a class="navbar-brand d-lg-none" href="#"><img src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="" width="95"></a>
            <div class="collapse navbar-collapse w-100" id="navbarSupportedContent">
                <div class="d-none d-lg-block w-100">
                    <div class="row">
                        <div class="col-3">
                            <a class="navbar-brand" href="{{ url('index')}}"><img src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="" width="95"></a>
                        </div>
                        <div class="col-6 mb-lg-3">
                            <form action="#" method="get" class="electrict-input" role="search">
                                <div class="footer-subscribe position-relative electrict-input ps-2">
                                    <!-- <select name="product_type" class="position-absolute top-0 left-0 categories text-muted">
                                        <option value="*">All Categories</option>
                                        <option value="Acessories">Acessories</option>
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
                                    </select> -->
                                    <input type="text" name="email" placeholder="I'm shopping for..." value="" class="ps-4 input-text input-search form-control w-100 rounded-pill" required="required">
                                    <button type="submit" class="btn btn-pink2 position-absolute search-btn rounded-pill top-0 end-0">
                                        <span>Search</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="mt-2 col-3">
                            <div class="topbar-toolbar d-flex justify-content-end align-items-center gap-3">
                                <a class="main_link_primary" data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-user"></i></a>
                                <a class="main_link_primary" class="d-md-block d-none" href="#!"><i class="iccl iccl-heart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                                <a class="main_link_primary" data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3 d-lg-none">
                <a class="main_link_primary" data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-search"></i></a>
                <a class="main_link_primary" data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
            </div>
        </div>
        <div class="bg-blue-dark text-white d-none d-lg-block">
            <div class="container">
                <!-- nav-list -->
                <div class="d-flex justify-content-between">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link text-uppercase text-white d-flex align-items-center fw-medium bg-pink2" style=" height: 50px;" href="{{ url('portfolio/portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="nav-link text-uppercase text-white align-items-center fw-medium">
                                    <i class="las la-bars mr__5 fs-18 me-1"></i> Shop by category
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column p-2">
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-mobile me-1"></i>Mobiles & tablets
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-camera-retro me-1"></i>Cameras
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-desktop me-1"></i>Computer & Laptop
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-print me-1"></i>Printers & Ink
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-tv me-1"></i>TV & Audio
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-microchip me-1"></i>Software
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-mouse me-1"></i>Accessories
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="px-0 py-2">
                                        <i class="las la-headphones-alt me-1"></i>Headphone
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home_pages/home-lookbook')}}" class="border-bottom px-0 py-2">
                                        <i class="las la-gamepad me-1"></i>Game & Console
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl nav-electric-item">
                            <a class="nav-link text-white" style="height: 50px ;" href="#" data-bs-toggle="dropdown" aria-expanded="false">
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

                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-glasses')}}">Home Glasses <span class="badge-tag badge bg-blue-dark">new</span>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-shoes')}}" <span class="badge-tag badge bg-danger">Hot</span></a></a></li>

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
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-sport')}}">Home Sport <span class="position-absolute badge rounded-pill fw-normal text-white" style="right:-40px; top: 2px; background-color: #32355d;">new</span></a>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-jewelry')}}">Home Jewelry <span class="position-absolute badge rounded-pill fw-normal text-white" style="right:-40px; top: 2px; background-color: #32355d;">new</span></a>
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
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('home_pages/home-shoes')}}">Compare  <span class="badge-tag badge bg-blue-dark">new</span></a></li>
                                                <li><a class="text-muted position-relative d-inline-flex" href="{{ url('product/product-detail-pickup-availability')}}">Pickup
                                                        Availability <span class="position-absolute badge bg-blue-dark rounded-pill fw-normal text-white" style="left:  103%; top: 10px;">Selling feature</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl nav-electric-item">
                            <a class="nav-link position-relative text-white" style="height: 50px ;" href="
                                {{ url('shop_pages.shop-filter-sidebar')}}" data-bs-toggle="dropdown" aria-expanded="false">
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
                                    <div class="col-lg-7 p-4">
                                        <div class="row g-0">
                                            <div class="col-lg-6 p-0">
                                                <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                    <img src="{{ URL::asset('/build/images/home-kids/megamenu-cat-01.jpg')}}" alt="" class="img-fluid">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="btn bg-white px-5 fw-medium mb-4 position-absolute bottom-0 start-50 translate-middle-x" style="white-space: nowrap;">
                                                        Girls Fashion
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 cat-section p-0">
                                                <div class="dropdown-sub-column-item position-relative cat_grid_item overflow-hidden h-100">
                                                    <img src="{{ URL::asset('/build/images/home-kids/megamenu-cat-02.jpg')}}" alt="" class="img-fluid">
                                                    <a href="{{url( 'shop_pages.shop-left-sidebar')}}" class="btn bg-white px-5 fw-medium mb-4 position-absolute bottom-0 start-50 translate-middle-x">
                                                        Accessories
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-xxl nav-electric-item">
                            <a class="nav-link text-white" style="height: 50px ;" href=" {{ url('product.product-detail-layout-01')}}" data-bs-toggle="dropdown" aria-expanded="false">
                                Product
                            </a>
                            <div class="dropdown-menu">
                                <div class="row pe-3">
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
                        <li class="nav-item dropdown dropdown-mega-lg nav-electric-item">
                            <a class="nav-link text-white" style="height: 50px ;" href=" {{ url('portfolio.portfolio-3-columns')}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Lookbook
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook')}}">Lookbook Slider</a></li>
                                <li><a class="text-muted" href="{{ url('home_pages/home-lookbook-collection')}}">Lookbook Section</a></li>
                                <li><a class="text-muted" href="{{ url('index')}}">Lookbook instagram</a></li>
                                <li><a class="text-muted" href="{{ url('product/product-detail-description-with-lookbook')}}">Lookbook in
                                        product</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-lookbook')}}">Lookbook in blog post</a></li>
                                <li><a class="text-muted" href="{{ url('portfolio/single-portfolio-with-lookbook')}}">Lookbook in portfolio post</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/lookbook-in-page')}}">Lookbook in page</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg nav-electric-item">
                            <a class="nav-link text-white" style="height: 50px ;" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Blog
                            </a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="#!">Grid Layout</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-masonry')}}">Masonry Layout</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-left-sidebar')}}">Left Sidebar</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-right-sidebar')}}">Right Sidebar</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-product-listing')}}">Single Post with Product
                                        Listing</a></li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Instagram
                                        Shop</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-instagram-shop')}}">Single Post with Categories</a>
                                </li>
                                <li><a class="text-muted" href="{{ url('blog/blog-post-with-lookbook')}}">Single Post with lookbook</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex align-items-xl-center justify-content-center justify-content-md-start gap-3">
                        <a href="mailto:Kalles@domain.com" class="mb-0 text-white fs-12"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> contact</a>
                        <a href="mailto:Kalles@domain.com" class="mb-0 text-white fs-12"><i class="las la-clock "></i>
                            08:00 - 17:00</a>
                        <a href="tel:+01 23456789" class="mb-0 text-white fs-12"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i>
                            +01 23456789</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>


<div class="backdrop-shadow d-none"></div>

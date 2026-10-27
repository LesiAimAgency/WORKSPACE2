<div id="kalles-section-header_top">
    <div class="h__top d-flex align-items-center">
        <div class="container-fluid">
            <div class="row py-2">
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex justify-content-center justify-content-md-start">
                        <p class="mb-0"><i class="pegk pe-7s-call fs-14 me-1 align-middle"></i> +01 23456789</p>
                        <p class="mb-0 ms-2"><i class="pe-7s-mail pegk fs-14 me-1 align-middle"></i> Kalles@domain.com
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 header-text text-center fs-12 py-1 py-md-0">
                    Summer sale discount off <span class="cr">50%</span>! <a href="{{ url('shop_pages/shop')}}" class="text-reset">Shop
                        Now</a>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="navbar-custom d-flex align-items-center justify-content-between p-3">
            <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <div class="d-none d-lg-block nt-social">
                <a href="#" class="facebook text-black fs-14 mx-1">
                    <i class="facl facl-facebook"></i>
                </a>
                <a href="#" class="twitter text-black fs-14 mx-1">
                    <i class="facl facl-twitter"></i>
                </a>
                <a href="#" class="instagram text-black fs-14 mx-1">
                    <i class="facl facl-instagram"></i>
                </a>
                <a href="#" class="linkedin text-black fs-14 mx-1">
                    <i class="facl facl-linkedin"></i>
                </a>
                <a href="#" class="pinterest text-black fs-14 mx-1">
                    <i class="facl facl-pinterest"></i>
                </a>
            </div>
            <!-- logo -->
            <a href="#!">
                <img class="w__95 logo_normal dn db_lg" src="{{ URL::asset('/build/images/svg/kalles.svg')}}" alt="Kalles Template">
            </a>
            <div class="topbar-toolbar d-flex align-items-center gap-3">
                <a href="#!" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="iccl iccl-search"></i>
                </a>
                <a class="d-none d-lg-block" data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="iccl iccl-user"></i></a>
                <a class="d-none d-lg-block" href="{{ url('blog/wishlist')}}"><i class="iccl iccl-heart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">3</span></a>
                <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">5</span></a>
                <a class="d-none d-lg-block rotate-180" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                        <rect width="30" height="1.5"></rect>
                        <rect y="7" width="20" height="1.5"></rect>
                        <rect y="14" width="30" height="1.5"></rect>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="backdrop-shadow d-none"></div>
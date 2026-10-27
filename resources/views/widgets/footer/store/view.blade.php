<footer class="footer footer-dark bg-green-dark text-white home-one-product">
    <div class="container">
        <div class="row accordion" id="footer-accordion">
            <div class="col-md-4 col-lg-3 mb-2 footer-accordion-item accordion-item">
                <button class="accordion-button footer-accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <h5 class="text-white">Get in touch</h5>
                </button>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <a href="#!">
                        <img src="{{ URL::asset('/build/images/home-glasses/footer-logo.png')}}" alt="" height="29">
                    </a>
                    <div class="mt-4 pt-2">
                        <p class="d-flex align-items-start text-white gap-2">
                            <i class="pegk pe-7s-map-marker fs-24"></i>
                            <span>184 Main Rd E, St Albans <br> <span class="pl__30">VIC 3021, Australia</span></span>
                        </p>
                        <p class="d-flex align-items-start text-white gap-2">
                            <i class="pegk pe-7s-mail fs-24"></i>
                            <a href="mailto:contact@company.com" class="text-reset">contact@company.com</a>
                        </p>
                        <p class="d-flex align-items-start text-white gap-2">
                            <i class="pegk pe-7s-call fs-24"></i>
                            <span>+001 2233 456 </span>
                        </p>
                        <div class="footer-social d-flex align-items-center gap-4 mt-4">
                            <a href="https://www.facebook.com" class="d-inline-block fs-16 text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Follow on Facebook">
                                <i class="facl facl-facebook"></i>
                            </a>
                            <a href="https://twitter.com" class="d-inline-block fs-16 text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Follow on Twitter">
                                <i class="facl facl-twitter"></i>
                            </a>
                            <a href="https://www.instagram.com" class="d-inline-block fs-16 text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Follow on Instagram">
                                <i class="facl facl-instagram"></i>
                            </a>
                            <a href="https://www.linkedin.com" class="d-inline-block fs-16 text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Follow on LinkedIn">
                                <i class="facl facl-linkedin"></i>
                            </a>
                            <a href="https://www.pinterest.com" class="d-inline-block fs-16 text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Follow on Pinterest">
                                <i class="facl facl-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-lg-2 mb-2 accordion-item footer-accordion-item">
                <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <h5 class="text-white">Categories</h5>
                </button>
                <h5 class="fw-medium d-none d-md-block text-white">Categories</h5>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="mt-lg-4 pt-2">
                        <ul class="menu list-unstyled">
                            <li class="menu-item">
                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="text-white">Celebrations</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="text-white">Events</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('shop_pages/shop-1600px-layout')}}" class="text-white">Home Decor</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('shop_pages/shop-1600px-layout')}}" class="text-white">Seasonal Bouquet</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('shop_pages/shop-1600px-layout')}}" class="text-white">Wedding Decor</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-md-3 col-lg-2 mb-2 accordion-item footer-accordion-item">
                <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <h5 class="text-white">Infomation</h5>
                </button>
                <h5 class="fw-medium d-none d-md-block text-white">Infomation</h5>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="mt-md-4 pt-md-2">
                        <ul class="menu list-unstyled">
                            <li class="menu-item">
                                <a href="#!" class="text-white">About Us</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Contact Us</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Terms &amp; Conditions</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Returns &amp; Exchanges</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Shipping &amp; Delivery</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Privacy Policy</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-md-3 col-lg-2 mb-2 accordion-item footer-accordion-item">
                <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <h5 class="text-white">Useful links</h5>
                </button>
                <h5 class="fw-medium d-none d-md-block text-white">Useful links</h5>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="mt-md-4 pt-md-2">
                        <ul class="menu list-unstyled">
                            <li class="menu-item">
                                <a href="#!" class="text-white">Contact us</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Privacy Policy</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Shipping & Delivery</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Terms & Conditions</a>
                            </li>
                            <li class="menu-item">
                                <a href="#!" class="text-white">Returns & Exchanges</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-md-10 col-lg-3 mb-2 accordion-item footer-accordion-item">
                <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <h5 class="text-white">Newsletter Signup</h5>
                </button>
                <h5 class="fw-medium d-none d-md-block text-white">Newsletter Signup</h5>
                <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="mt-lg-4 pt-2">
                        <p class="text-white">Subscribe to our newsletter and get 10% off your first purchase</p>
                        <form id="contact_form" class="d-block">
                            <div class="footer-subscribe position-relative">
                                <input type="email" name="email" placeholder="Your email address" value="" class="input-text text-white form-control w-100 rounded-pill text-white bg-transparent" required="required">
                                <button type="submit" class="btn bg-black fw-semibold text-white position-absolute rounded-pill">
                                    <span>Subscribe</span>
                                </button>
                            </div>
                        </form>
                        <!-- <div class="mt-3">
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-01.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-02.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-03.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-04.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-05.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-06.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-07.svg')}}" alt="" class="img-fluid"></a>
                            <a href="#!"><img src="{{ URL::asset('/build/images/home-glasses/payment-08.svg')}}" alt="" class="img-fluid"></a>
                        </div> -->
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
</footer>

<div class="footer-alt mb-5 mb-lg-0 py-4">
    <div class=" container">
        <div class="row">
            <div class="col-sm-6 text-muted text-center">
                Copyright ©
                <script>document.write(new Date().getFullYear())</script> <a href="#!" class="text-muted">Kalles</a> all
                rights reserved. Powered by srbthemes
            </div>
            <div class="col-sm-6 mt-2 mt-lg-0">
                <ul id="footer-menu" class="list-unstyled d-flex align-items-center mb-0 justify-content-lg-end">
                    <li class="menu-item ">
                        <a href="{{ url('shop_pages/shop-filter-sidebar')}}" class="text-muted mx-2">Shop</a>
                    </li>
                    <li class="menu-item ">
                        <a href="#!" class="text-muted mx-2">About Us</a>
                    </li>
                    <li class="menu-item ">
                        <a href="#!" class="text-muted mx-2">Contact</a>
                    </li>
                    <li class="menu-item ">
                        <a href="#!" class="text-muted mx-2">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
{{-- @include('partials.responsive-footer') --}}
<a href="#" x-on:click.prevent="
      window.scrollTo({
         top: 0,
         behavior: 'smooth'
      });
   " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
    <i class="pr pegk pe-7s-angle-up"></i>
</a>

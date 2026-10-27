<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>

<head>
    <meta charset="utf-8" />
    <title>@yield('title', ' Kalles - Clean, Versatile, Responsive Bootstrap 5 Theme')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="" name="description" />
    <meta content="srbthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('/build/images/k_favicon_32x.png') }}">
    @yield('css')
    @include('frontend.partials.head-css')
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/js/main.js'])
</head>

<body class="{{ 'class-name' }}" x-data="{ showMenuScroll: false }">


    <!--head banner-->
    <div x-data="{ isOpen: true }">
        <div class="t_header fs-13 d-flex align-items-center" x-bind:class="{ 'd-none': !isOpen }">
            <div class="container-fluid">
                <div class="d-flex gap-2">
                    <div class="col text-center text-white">
                        Today deal sale off <strong>70% </strong>. End in
                        <strong class="js_kl__countdown"></strong>. <a href="#!" class="text-white">Hurry Up <i
                                class="las la-arrow-right"></i></a>
                    </div>
                    <div class="col-auto mt-2 mt-md-0">
                        <a href="#" class="h_banner_close text-white"
                            x-on:click.prevent="isOpen = false">close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end head banner-->
    {!! \App\Widgets\WidgetRegistry::render('HeaderWidget', [], setting('theme_header_variant', 'default')) !!}
    <div>
        @yield('content')
        {!! \App\Widgets\WidgetRegistry::render('FooterWidget', [], setting('theme_footer_variant', 'default')) !!}
        {{-- @include('frontend.partials.popup') --}}
    </div>
    {{-- @include('frontend.partials.card-model') --}}
    @include('frontend.partials.vendor-scripts')
    <script src="{{ URL::asset('build/libs/flickity/flickity.pkgd.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/main.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @yield('js')
</body>

</html>

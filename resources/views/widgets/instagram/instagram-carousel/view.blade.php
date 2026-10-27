    <section>
        <div class="container-fluid">
            @php
                $title = $data['title'] ?? '@ Follow us on Instagram';
                $username = $data['username'] ?? '';
                $photos = $data['photos'] ?? [];
            @endphp
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center mb-4">
                        <div>
                            <h3 class="section-title position-relative flex text-uppercase">
                                <span>{{ $title }}</span>
                            </h3>
                            @if(!empty($username))
                                <a href="https://instagram.com/{{ ltrim($username, '@') }}" target="_blank" class="text-muted">{{ $username }}</a>
                            @endif
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
            <div class="row" data-flickity='{"imagesLoaded": 0,"adaptiveHeight": 1, "contain": 1, "groupCells": "100%", "dragThreshold" : 5, "cellAlign": "left","wrapAround": false,"prevNextButtons": true,"percentPosition": 1,"pageDots": false, "autoPlay" : 0, "pauseAutoPlayOnHover" : true, "rightToLeft": false }' dir="ltr">
                @if(!empty($photos) && is_array($photos))
                    @foreach($photos as $index => $item)
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="insta-card position-relative">
                            <a href="{{ $item['link'] ?? '#' }}" target="_blank">
                                <img src="{{ $item['image'] ?? '/theme/images/instagram/ins1_14.jpg' }}" alt="{{ $item['caption'] ?? '' }}" class="img-fluid w-100" style="object-fit: cover; aspect-ratio: 1/1;">
                                <div class="card-spin position-01 position-absolute fs-14 bg-dark text-white rounded-circle fw-semibold d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="fab fa-instagram"></i></div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Placeholder when no photos are added -->
                    @for($i = 1; $i <= 6; $i++)
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="insta-card position-relative">
                            <img src="/theme/images/instagram/ins1_{{ $i }}.jpg" alt="" class="img-fluid w-100" style="object-fit: cover; aspect-ratio: 1/1;">
                        </div>
                    </div>
                    @endfor
                @endif
            </div>
        </div>
    </section>

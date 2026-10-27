    <section class="kalles-section_type_featured_blog latest-blogs py-4">
        <div class="container">
            @php
                $section_title = $data['section_title'] ?? 'Tin tức & Bài viết';
                $section_subtitle = $data['section_subtitle'] ?? '';
                $posts = $data['posts'] ?? [];
            @endphp
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center mb-4 pb-2">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex text-uppercase">
                                <span style="white-space: nowrap;">{{ $section_title }}</span>
                            </h3>
                        </div>
                        @if(!empty($section_subtitle))
                        <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">{{ $section_subtitle }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row g-4 kalles-blog-grid" dir="ltr">
                @if(!empty($posts) && is_array($posts))
                    @foreach($posts as $item)
                        <div class="col-md-4 px-2">
                            <a href="{{ $item['link'] ?? '#' }}" class="blog-card d-block blog-wrap">
                                <div class="blog_grid overflow-hidden rounded">
                                    <div class="blog_grid_img w-100 position-relative" style="background: url('{{ $item['image'] ?? '/theme/images/blog/blog-01.jpg' }}') center no-repeat; background-size: cover; height: 220px;">
                                    </div>
                                </div>
                                <h6 class="fs-16 mt-3 main_link text-truncate"><span class="text-reset font-semibold">{{ $item['title'] ?? 'Bài viết' }}</span></h6>
                                <div class="d-flex gap-1 align-items-center text-muted fs-13">
                                    <span class="me-1">Tác giả: <span class="text-body">{{ $item['author'] ?? 'Admin' }}</span></span>
                                    @if(!empty($item['date']))
                                    - Ngày: <span class="text-body">{{ $item['date'] }}</span>
                                    @endif
                                </div>
                                <div class="post-content text-muted mt-2 fs-14" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">{{ $item['summary'] ?? '' }}</div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5 text-muted">Không có bài viết nào</div>
                @endif
            </div>
        </div>
    </section>

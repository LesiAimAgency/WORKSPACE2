@props(['post', 'options' => [], 'layout' => 'grid'])

@php
    $excerptLength = $options['post_excerpt_length'] ?? 150;
    $showDate = $options['show_date'] ?? true;
    $showAuthor = $options['show_author'] ?? true;
    $showCategory = $options['show_category'] ?? true;
    $showComment = $options['show_comment_count'] ?? true;
    
    $postImage = $post->image ?? $post->featured_image;
    if (!$postImage) {
        $postImage = 'https://ui-avatars.com/api/?name='.urlencode($post->title ?? 'Blog').'&background=random&size=800x600';
    }
@endphp

<a href="/{{ request()->route('projectCode') }}/blog/{{ $post->slug }}">
    <div class="blog_grid overflow-hidden position-relative">
        <div class="blog_grid_img w-100 position-relative"
            style="background: url('{{ $postImage }}') center no-repeat; background-size: cover; height: {{ $layout === 'classic' ? '600px' : '400px' }};">
        </div>
        @if($showCategory && $post->category)
        <span class="position-absolute top-0 start-0 m-3 bg-white text-dark px-3 py-1 fw-bold text-uppercase fs-12">
            {{ $post->category->name }}
        </span>
        @endif
    </div>
    <div class="my-4">
        <p class="text-muted fs-14">
            @if($showAuthor)By <span class="text-black fw-medium">{{ $post->author->name ?? 'Admin' }}</span>@endif
            @if($showAuthor && $showDate) on @endif
            @if($showDate)<span class="text-black fw-medium">{{ $post->created_at->format('M d, Y') }}</span>@endif
        </p>
        <h6 class="text-black fs-18 mb-2">{{ $post->title }}</h6>
        <p class="text-muted mt-2 fs-15">
            {{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->content), $excerptLength) }}
        </p>
    </div>
</a>

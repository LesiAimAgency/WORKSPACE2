@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-news-detail">
    <article class="news-detail l-container">
        <header class="news-detail__header">
            <h1 class="news-detail__title">{{ $post->title }}</h1>
            <div class="news-detail__meta">
                <time class="news-detail__date">{{ $post->published_at->format('d/m/Y') }}</time>
                <span class="news-detail__author">{{ $post->author->name ?? 'Admin' }}</span>
            </div>
        </header>

        @if($post->featured_image)
        <div class="news-detail__featured-image">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
        </div>
        @endif

        <div class="news-detail__content">
            {!! $post->content !!}
        </div>

        {{-- Tags temporarily disabled because tags table is missing
        @if($post->tags->count())
        <div class="news-detail__tags">
            @foreach($post->tags as $tag)
            <a href="{{ route('news.tag', $tag->slug) }}" class="c-tag">{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif
        --}}
    </article>

    @if($relatedPosts->count())
    <section class="related-posts l-container">
        <h2 class="c-ttl03">Bài Viết Liên Quan</h2>
        <div class="related-posts__grid">
            @foreach($relatedPosts as $related)
            <article class="news-card">
                <a href="{{ route('news.show', $related->slug) }}">
                    <img src="{{ $related->featured_image }}" alt="{{ $related->title }}">
                    <h3>{{ $related->title }}</h3>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif
</main>
@endsection

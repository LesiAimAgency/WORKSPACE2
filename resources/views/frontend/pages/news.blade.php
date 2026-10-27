@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-news">
    <section class="c-mainvisual">
        <div class="c-mainvisual__bg">
            <img src="/assets/img/banner-hero-news.png" alt="Tin Tức">
        </div>
        <div class="l-container">
            <h1 class="c-mainvisual__title">Tin Tức</h1>
        </div>
    </section>

    <section class="news-list l-container">
        <div class="news-list__grid">
            @foreach($posts as $post)
            <article class="news-card">
                <a href="{{ route('news.show', $post->slug) }}" class="news-card__link">
                    <div class="news-card__image">
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                    </div>
                    <div class="news-card__content">
                        <h3 class="news-card__title">{{ $post->title }}</h3>
                        <p class="news-card__excerpt">{{ Str::limit($post->excerpt, 150) }}</p>
                        <time class="news-card__date">{{ $post->published_at->format('d/m/Y') }}</time>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
        {{ $posts->links() }}
    </section>
</main>
@endsection

@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-about">
    @include('frontend.sections.about.hero')
    @include('frontend.sections.about.products')
    @include('frontend.sections.about.about-us')
    @include('frontend.sections.about.core-values')
    @include('frontend.sections.about.vision-mission')
    @include('frontend.sections.about.history')
    @include('frontend.sections.about.achievements')
    @include('frontend.sections.about.culture')
    @include('frontend.sections.about.partners')
</main>
@endsection

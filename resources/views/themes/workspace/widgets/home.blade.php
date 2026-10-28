@php
  $settings = $settings ?? [];
  $logo = !empty($settings['logo']) 
      ? (Str::startsWith($settings['logo'], ['http://', 'https://', '/']) ? $settings['logo'] : asset($settings['logo'])) 
      : asset('themes/workspace/assets/logo.png');
  $alt = $settings['alt'] ?? 'Workspace Interior design and build';
  $ariaLabel = $settings['aria_label'] ?? 'Workspace';
@endphp
{{-- Step 1: Cover Banner with Centered Logo (Hình 2) --}}
<section class="panel panel-home" id="home" data-ui="light" aria-label="{{ $ariaLabel }}">
  <div class="surface surface-home">
    <img class="brand-logo animate__animated animate__zoomIn animate__delay-1s" src="{{ $logo }}" alt="{{ $alt }}" />
  </div>
</section>

{{-- Step 2: About Workspace (Hình 1) --}}
<section class="panel panel-about" id="about-workspace" data-ui="light" aria-label="About Workspace">
  <div class="surface surface-home surface-about">
    <img class="brand-logo animate__animated animate__fadeIn" src="{{ $logo }}" alt="{{ $alt }}" />
    <div class="home-about-heading animate__animated animate__fadeInUp" aria-hidden="true">
      <span class="home-about-word home-about-black">ABOUT</span>
      <span class="home-about-word home-about-red">WORKSPACE</span>
    </div>
  </div>
</section>


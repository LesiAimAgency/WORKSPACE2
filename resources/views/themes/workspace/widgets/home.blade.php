@php
  $settings = $settings ?? [];
  $logo = !empty($settings['logo']) 
      ? (Str::startsWith($settings['logo'], ['http://', 'https://', '/']) ? $settings['logo'] : asset($settings['logo'])) 
      : asset('themes/workspace/assets/logo.png');
  $alt = $settings['alt'] ?? 'Workspace Interior design and build';
  $ariaLabel = $settings['aria_label'] ?? 'Workspace';
@endphp
<section class="panel panel-home" id="home" data-ui="light" aria-label="{{ $ariaLabel }}">
  <div class="surface surface-home">
    <img class="brand-logo animate__animated animate__zoomIn animate__delay-1s" src="{{ $logo }}" alt="{{ $alt }}" />
    <div class="home-about-heading" aria-hidden="true">
      <span class="home-about-word home-about-black">ABOUT</span>
      <span class="home-about-word home-about-red">WORKSPACE</span>
    </div>
  </div>
</section>

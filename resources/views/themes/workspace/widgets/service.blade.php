@php
  $settings = $settings ?? [];
  $heading = $settings['heading'] ?? 'OUR SERVICES';
  $description = $settings['description'] ?? 'Explore our service categories and completed workspace projects.';
  $navItems = $settings['nav_items'] ?? [
      ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => false],
      ['label' => 'Our Services', 'link' => '#service', 'is_current' => true],
      ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => false],
  ];
  $defaultServices = [
      [
          'title' => 'DESIGN',
          'desc' => 'Office, commercial, and residential interior architectural concepts and detailed technical drawings.',
          'image' => asset('themes/workspace/assets/build1.png'),
          'link' => '#about',
          'data_section' => 'about',
      ],
      [
          'title' => 'BUILD',
          'desc' => 'Turnkey fit-out, MEP engineering, high-end construction management, and quality control.',
          'image' => asset('themes/workspace/assets/build3.png'),
          'link' => '#build',
          'data_section' => 'build',
      ],
      [
          'title' => 'FURNITURE DESIGN',
          'desc' => 'Bespoke loose furniture and custom joinery manufacturing with exquisite craftsmanship.',
          'image' => asset('themes/workspace/assets/build2.png'),
          'link' => '#furniture-design',
          'data_section' => 'furniture-design',
      ],
      [
          'title' => 'MAINTAIN',
          'desc' => 'Comprehensive facility maintenance, preventative inspections, and swift warranty support.',
          'image' => asset('themes/workspace/assets/maintain-crop.png'),
          'link' => '#maintain',
          'data_section' => 'maintain',
      ],
  ];

  $services = !empty($settings['services']) && is_array($settings['services']) && count($settings['services']) > 0
      ? array_map(function ($s) {
          $img = $s['image'] ?? '';
          if (!empty($img) && !Str::startsWith($img, ['http://', 'https://', '/'])) {
              $img = asset($img);
          }
          return [
              'title' => $s['title'] ?? 'Service',
              'desc' => $s['desc'] ?? '',
              'image' => !empty($img) ? $img : asset('themes/workspace/assets/build1.png'),
              'link' => $s['link'] ?? '#',
              'data_section' => $s['data_section'] ?? '',
          ];
      }, $settings['services'])
      : $defaultServices;
@endphp

<section class="panel panel-service" id="service" data-ui="light" aria-label="Our services">
  <div class="surface service-surface">
    <div class="service-layout">

      <div class="service-copy animate__animated animate__fadeInDown">
        <h2>{!! nl2br(e($heading)) !!}</h2>
        <span class="service-accent" aria-hidden="true"></span>
        <p>{{ $description }}</p>
      </div>

      <div class="service-gallery-wrapper animate__animated animate__fadeInUp animate__delay-1s">
        <button class="gallery-arrow gallery-arrow-prev" type="button" aria-label="Previous service"></button>
        <div class="service-gallery" aria-label="Service gallery">
          @foreach($services as $index => $srv)
            <article class="service-card {{ $index === 0 ? 'service-card-active' : '' }}">
              <div class="service-media">
                <img src="{{ $srv['image'] }}" alt="{{ $srv['title'] }}" />
              </div>
              <h3>{{ $srv['title'] }}</h3>
              <p>{{ Str::limit($srv['desc'], 130) }}</p>
              <a href="{{ $srv['link'] }}" @if(!empty($srv['data_section'])) data-section="{{ $srv['data_section'] }}" @endif>View more</a>
            </article>
          @endforeach
        </div>
        <button class="gallery-arrow gallery-arrow-next" type="button" aria-label="Next service"></button>
      </div>
    </div>
  </div>
</section>

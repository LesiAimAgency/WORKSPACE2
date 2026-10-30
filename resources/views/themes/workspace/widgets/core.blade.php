@php
  if (empty($settings)) {
      $dbWidget = \App\Models\Widget::withoutGlobalScopes()->where('type', 'workspace_core')->orderByDesc('is_active')->orderByDesc('id')->first();
      $settings = $dbWidget && is_array($dbWidget->settings) ? $dbWidget->settings : [];
  }
  $subHeading = $settings['sub_heading'] ?? 'WORKSPACE';
  $heading = $settings['heading'] ?? 'CORE VALUES';
  $description = $settings['description'] ?? 'This combination of core values is flexibly applied by us at each stage of the company, in personnel training, dealing with partners & serving customers!';
  
  $rawImage = $settings['image'] ?? ($settings['svg'] ?? 'themes/workspace/assets/main1.svg');
  $isSvgCode = is_string($rawImage) && Str::startsWith(trim($rawImage), '<svg');
  $imageSrc = null;
  if (!$isSvgCode) {
      $imageSrc = !empty($rawImage)
          ? (Str::startsWith($rawImage, ['http://', 'https://', '/']) ? $rawImage : asset($rawImage))
          : asset('themes/workspace/assets/main1.svg');
  }

  $navItems = $settings['nav_items'] ?? [
      ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => true],
      ['label' => 'Our Services', 'link' => '#service', 'is_current' => false],
      ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => false],
  ];
@endphp
<section class="panel panel-core" id="core-values" data-ui="light" aria-label="Workspace core values">
  <div class="surface core-surface">
    <div class="core-layout">

      <div class="core-header-row core-copy animate__animated animate__fadeInDown">
        <div class="core-title-group">
          <p class="core-subheading">{{ $subHeading }}</p>
          <h2 class="core-heading">{!! nl2br(e($heading)) !!}</h2>
        </div>
        <div class="core-desc-group">
          <p class="core-description">{!! $description !!}</p>
        </div>
      </div>

      <div class="core-graphic animate__animated animate__fadeInUp">
        @if($isSvgCode)
          {!! $rawImage !!}
        @else
          <img class="core-svg" src="{{ $imageSrc }}" alt="{{ $heading }}" loading="lazy" decoding="async" />        @endif
      </div>
    </div>
  </div>
</section>

@php
  if (empty($settings)) {
      $dbWidget = \App\Models\Widget::withoutGlobalScopes()->where('type', 'workspace_home')->orderByDesc('is_active')->orderByDesc('id')->first();
      $settings = $dbWidget && is_array($dbWidget->settings) ? $dbWidget->settings : [];
  }
  $logo = !empty($settings['logo']) 
      ? (Str::startsWith($settings['logo'], ['http://', 'https://', '/']) ? $settings['logo'] : asset($settings['logo'])) 
      : asset('themes/workspace/assets/logo.png');
  $alt = $settings['alt'] ?? 'Workspace Interior design and build';
  $ariaLabel = $settings['aria_label'] ?? 'Workspace';
@endphp
{{-- Step 1: Cover Banner with Centered Logo (Hình 2) --}}
<section class="panel panel-home is-active" id="home" data-ui="light" aria-label="{{ $ariaLabel }}">
  <div class="surface surface-home">
    <img class="brand-logo animate__animated animate__zoomIn" src="{{ $logo }}" alt="{{ $alt }}" />
  </div>
</section>

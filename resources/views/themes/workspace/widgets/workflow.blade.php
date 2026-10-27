@php
  $settings = $settings ?? [];
  $sectionId = $settings['section_id'] ?? 'about';
  $sectionClass = $settings['section_class'] ?? 'workflow-design';
  $dataUi = $settings['data_ui'] ?? 'dark';
  $kicker = $settings['kicker'] ?? 'We';
  $script = $settings['script'] ?? 'Design';
  $suffix = $settings['suffix'] ?? 'Spaces';
  $desc = $settings['desc'] ?? 'Transforming empty spaces into high-performance, human-centric working environments that stimulate creativity and productivity.';
  $image = !empty($settings['image'])
      ? (Str::startsWith($settings['image'], ['http://', 'https://', '/']) ? $settings['image'] : asset($settings['image']))
      : asset('themes/workspace/assets/build1.png');
  $navItems = $settings['nav_items'] ?? [
      ['label' => 'What we do', 'is_current' => true, 'data_workflow_slide' => '0'],
      ['label' => 'Projects', 'is_current' => false, 'data_workflow_slide' => '1'],
      ['label' => 'About the expert', 'is_current' => false, 'data_workflow_slide' => '2'],
  ];
@endphp

<section class="panel panel-workflow {{ $sectionClass }}" id="{{ $sectionId }}" data-ui="{{ $dataUi }}" aria-label="{{ $script }} Workflow">
  <div class="surface workflow-surface">
    <div class="workflow-media" aria-hidden="true">
      <img class="workflow-image is-active" src="{{ $image }}" alt="" />
    </div>
    <span class="workflow-shade" aria-hidden="true"></span>

    <nav class="workflow-mini-nav" aria-label="Workflow menu">
      <span class="mini-rule" aria-hidden="true"></span>
      @foreach($navItems as $item)
        <button {!! !empty($item['is_current']) ? 'class="is-current"' : '' !!} type="button" @if(isset($item['data_workflow_slide'])) data-workflow-slide="{{ $item['data_workflow_slide'] }}" @endif>{{ $item['label'] ?? '' }}</button>
      @endforeach
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <div class="workflow-copy">
      @if(!empty($kicker) && $kicker !== 'We')
        <p class="workflow-kicker">{{ $kicker }}</p>
      @endif
      <h2>
        <span class="workflow-script">{{ $script }}</span>
        @if(!empty($suffix) && $suffix !== 'Spaces')
          <span class="workflow-suffix">{{ $suffix }}</span>
        @endif
      </h2>
      <span class="workflow-accent" aria-hidden="true"></span>
      <p class="workflow-desc">{{ $desc }}</p>
    </div>
  </div>
</section>

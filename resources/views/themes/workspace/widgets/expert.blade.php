@php
  if (empty($settings)) {
      $dbWidget = \App\Models\Widget::withoutGlobalScopes()->where('type', 'workspace_expert')->orderByDesc('is_active')->orderByDesc('id')->first();
      $settings = $dbWidget && is_array($dbWidget->settings) ? $dbWidget->settings : [];
  }
  $sectionId = $settings['section_id'] ?? 'design-expert';
  $sectionClass = $settings['section_class'] ?? 'expert-design';
  $dataUi = $settings['data_ui'] ?? 'light';
  $heading = $settings['heading'] ?? "ABOUT THE<br />EXPERT";
  $desc = $settings['desc'] ?? ($settings['description'] ?? "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.");
  $image = !empty($settings['image'])
      ? (Str::startsWith($settings['image'], ['http://', 'https://', '/']) ? $settings['image'] : asset($settings['image']))
      : asset('themes/workspace/assets/Man 1.png');

  $navItems = $settings['nav_items'] ?? [
      ['label' => 'What we do', 'is_current' => false],
      ['label' => 'Projects', 'is_current' => false],
      ['label' => 'About the expert', 'is_current' => true],
  ];

  $defaultSkills = [
      ['name' => 'Thiết kế văn phòng', 'pct' => 77],
      ['name' => 'Thiết kế F&B', 'pct' => 56],
      ['name' => 'Thiết kế nhà ở', 'pct' => 88],
  ];
  $skills = !empty($settings['skills']) && is_array($settings['skills']) ? $settings['skills'] : $defaultSkills;

  $expertName = $settings['expert_name'] ?? 'Kiến trúc sư Võ Đức Minh';
  $contactButtonText = $settings['contact_button_text'] ?? 'Liên hệ';
  $projectsButtonText = $settings['projects_button_text'] ?? 'Xem dự án';
  $contactLink = $settings['contact_link'] ?? '#connect';
  $projectsLink = $settings['projects_link'] ?? '#design-project';
@endphp

<section class="panel panel-expert {{ $sectionClass }}" id="{{ $sectionId }}" data-ui="{{ $dataUi }}" aria-label="About the Expert">
  <div class="surface expert-surface">
 

    <div class="expert-content-col animate__animated animate__fadeInLeft">
      <div class="expert-header-block">
        <h2 class="expert-title">{!! $heading !!}</h2>
        <span class="expert-accent-line" aria-hidden="true"></span>
        <p class="expert-desc">{{ $desc }}</p>
      </div>

      <div class="expert-skills-list">
        @foreach($skills as $skill)
          <div class="expert-skill-item">
            <span class="expert-skill-name">{{ $skill['name'] }}</span>
            <div class="expert-skill-bar">
              <span class="expert-skill-track"></span>
              <span class="expert-skill-fill" style="width: {{ $skill['pct'] ?? 75 }}%;"></span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="expert-actions">
        <a href="{{ $contactLink }}" class="expert-btn expert-btn-primary">{{ $contactButtonText }}</a>
        <a href="{{ $projectsLink }}" class="expert-btn expert-btn-secondary">{{ $projectsButtonText }}</a>
      </div>

      <div class="expert-footer-profile">
        <div class="expert-profile-info">
          <span class="expert-author-name">{{ $expertName }}</span>
          <div class="expert-author-progress">
            <span class="author-track"></span>
            <span class="author-fill" style="width: 25%;"></span>
          </div>
        </div>
        <div class="expert-nav-arrows">
          <button class="expert-arrow expert-prev" type="button" aria-label="Previous expert"></button>
          <button class="expert-arrow expert-next" type="button" aria-label="Next expert"></button>
        </div>
      </div>
    </div>

    <div class="expert-visual animate__animated animate__fadeInRight animate__delay-1s" aria-hidden="true">
      <img src="{{ $image }}" alt="{{ $expertName }}" />
    </div>
  </div>
</section>

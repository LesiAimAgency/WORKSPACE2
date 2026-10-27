<?php
  $settings = $settings ?? [];
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
?>

<section class="panel panel-expert <?php echo e($sectionClass); ?>" id="<?php echo e($sectionId); ?>" data-ui="<?php echo e($dataUi); ?>" aria-label="About the Expert">
  <div class="surface expert-surface">
    <nav class="workflow-mini-nav expert-mini-nav" aria-label="Workflow menu">
      <span class="mini-rule" aria-hidden="true"></span>
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button <?php echo !empty($item['is_current']) ? 'class="is-current"' : ''; ?> type="button"><?php echo e($item['label'] ?? ''); ?></button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <div class="expert-content-col animate__animated animate__fadeInLeft">
      <div class="expert-header-block">
        <h2 class="expert-title"><?php echo $heading; ?></h2>
        <span class="expert-accent-line" aria-hidden="true"></span>
        <p class="expert-desc"><?php echo e($desc); ?></p>
      </div>

      <div class="expert-skills-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="expert-skill-item">
            <span class="expert-skill-name"><?php echo e($skill['name']); ?></span>
            <div class="expert-skill-bar">
              <span class="expert-skill-track"></span>
              <span class="expert-skill-fill" style="width: <?php echo e($skill['pct'] ?? 75); ?>%;"></span>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>

      <div class="expert-actions">
        <a href="<?php echo e($contactLink); ?>" class="expert-btn expert-btn-primary"><?php echo e($contactButtonText); ?></a>
        <a href="<?php echo e($projectsLink); ?>" class="expert-btn expert-btn-secondary"><?php echo e($projectsButtonText); ?></a>
      </div>

      <div class="expert-footer-profile">
        <div class="expert-profile-info">
          <span class="expert-author-name"><?php echo e($expertName); ?></span>
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
      <img src="<?php echo e($image); ?>" alt="<?php echo e($expertName); ?>" />
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/expert.blade.php ENDPATH**/ ?>
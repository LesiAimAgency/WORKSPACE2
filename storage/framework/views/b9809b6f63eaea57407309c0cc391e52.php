<?php
  $settings = $settings ?? [];
  $subHeading = $settings['sub_heading'] ?? 'WORKSPACE';
  $heading = $settings['heading'] ?? 'CORE VALUES';
  $description = $settings['description'] ?? 'This combination of core values is flexibly applied by us at each stage of the company, in personnel training, dealing with partners & serving customers!';
  
  $useCustomImage = !empty($settings['image']) && !Str::contains($settings['image'], 'main1.svg');
  $customImage = $useCustomImage
      ? (Str::startsWith($settings['image'], ['http://', 'https://', '/']) ? $settings['image'] : asset($settings['image']))
      : null;

  $navItems = $settings['nav_items'] ?? [
      ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => true],
      ['label' => 'Our Services', 'link' => '#service', 'is_current' => false],
      ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => false],
  ];

  $coreValues = $settings['values'] ?? [
      [
          'id' => 'collaborate',
          'name' => 'Collaborate',
          'icon' => 'collaborate.svg',
          'left_pct' => 49.67,
          'top_pct' => 30.83,
          'label_pos' => 'top',
      ],
      [
          'id' => 'innovative',
          'name' => 'Innovative',
          'icon' => 'innovative.svg',
          'left_pct' => 24.65,
          'top_pct' => 45.83,
          'label_pos' => 'top',
      ],
      [
          'id' => 'safety',
          'name' => 'Safety',
          'icon' => 'safety.svg',
          'left_pct' => 38.49,
          'top_pct' => 49.62,
          'label_pos' => 'top',
      ],
      [
          'id' => 'passionate',
          'name' => 'Passionate',
          'icon' => 'passionate.svg',
          'left_pct' => 12.05,
          'top_pct' => 74.10,
          'label_pos' => 'top',
      ],
      [
          'id' => 'energized',
          'name' => 'Energized',
          'icon' => 'energized.svg',
          'left_pct' => 45.33,
          'top_pct' => 81.92,
          'label_pos' => 'bottom',
      ],
      [
          'id' => 'creative',
          'name' => 'Creative',
          'icon' => 'creative.svg',
          'left_pct' => 74.40,
          'top_pct' => 66.81,
          'label_pos' => 'bottom',
      ],
      [
          'id' => 'speedy',
          'name' => 'Speedy',
          'icon' => 'speedy.svg',
          'left_pct' => 83.88,
          'top_pct' => 83.72,
          'label_pos' => 'top',
      ],
      [
          'id' => 'integrity',
          'name' => 'Integrity',
          'icon' => 'integrity.svg',
          'left_pct' => 86.56,
          'top_pct' => 55.82,
          'label_pos' => 'bottom',
      ],
  ];
?>
<section class="panel panel-core" id="core-values" data-ui="light" aria-label="Workspace core values">
  <div class="surface core-surface">
    <div class="core-layout">

      <div class="core-header-row core-copy animate__animated animate__fadeInDown">
        <div class="core-title-group">
          <p class="core-subheading"><?php echo e($subHeading); ?></p>
          <h2 class="core-heading"><?php echo nl2br(e($heading)); ?></h2>
        </div>
        <div class="core-desc-group">
          <p class="core-description"><?php echo e($description); ?></p>
        </div>
      </div>

      <div class="core-graphic animate__animated animate__fadeInUp">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useCustomImage && !empty($customImage)): ?>
          <img class="core-svg" src="<?php echo e($customImage); ?>" alt="Workspace Core Values" />
        <?php else: ?>
          <div class="core-orbit-system" aria-label="Workspace Core Values Orbital Diagram">
            <!-- 3 Concentric Orbit Rings in Background -->
            <img class="core-orbit-tracks" src="<?php echo e(asset('themes/workspace/assets/core-orbit-tracks.svg')); ?>" alt="" aria-hidden="true" />

            <!-- Center Sphere: PEOPLE FOCUS -->
            <div class="core-center-sphere">
              <img class="core-center-sphere-img" src="<?php echo e(asset('themes/workspace/assets/core-center-sphere.svg')); ?>" alt="PEOPLE FOCUS" />
              <span class="sr-only">PEOPLE FOCUS</span>
            </div>

            <!-- 8 Interactive Satellites -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $coreValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="core-satellite satellite-<?php echo e($item['id']); ?> label-<?php echo e($item['label_pos']); ?>" style="--left-pct: <?php echo e($item['left_pct']); ?>%; --top-pct: <?php echo e($item['top_pct']); ?>%;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['label_pos'] === 'top'): ?>
                  <span class="satellite-label"><?php echo e($item['name']); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="satellite-badge" tabindex="0" role="button" aria-label="<?php echo e($item['name']); ?>">
                  <img class="satellite-icon" src="<?php echo e(asset('themes/workspace/assets/icons/' . $item['icon'])); ?>" alt="<?php echo e($item['name']); ?>" />
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['label_pos'] === 'bottom'): ?>
                  <span class="satellite-label"><?php echo e($item['name']); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/core.blade.php ENDPATH**/ ?>
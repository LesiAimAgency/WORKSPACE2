<?php
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
?>

<section class="panel panel-workflow <?php echo e($sectionClass); ?>" id="<?php echo e($sectionId); ?>" data-ui="<?php echo e($dataUi); ?>" aria-label="<?php echo e($script); ?> Workflow">
  <div class="surface workflow-surface">
    <div class="workflow-media" aria-hidden="true">
      <img class="workflow-image is-active" src="<?php echo e($image); ?>" alt="" />
    </div>
    <span class="workflow-shade" aria-hidden="true"></span>

    <nav class="workflow-mini-nav" aria-label="Workflow menu">
      <span class="mini-rule" aria-hidden="true"></span>
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button <?php echo !empty($item['is_current']) ? 'class="is-current"' : ''; ?> type="button" <?php if(isset($item['data_workflow_slide'])): ?> data-workflow-slide="<?php echo e($item['data_workflow_slide']); ?>" <?php endif; ?>><?php echo e($item['label'] ?? ''); ?></button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <div class="workflow-copy">
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($kicker) && $kicker !== 'We'): ?>
        <p class="workflow-kicker"><?php echo e($kicker); ?></p>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      <h2>
        <span class="workflow-script"><?php echo e($script); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($suffix) && $suffix !== 'Spaces'): ?>
          <span class="workflow-suffix"><?php echo e($suffix); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </h2>
      <span class="workflow-accent" aria-hidden="true"></span>
      <p class="workflow-desc"><?php echo e($desc); ?></p>
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/workflow.blade.php ENDPATH**/ ?>
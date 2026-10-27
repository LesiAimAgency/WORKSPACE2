<?php
  $settings = $settings ?? [];
  $logo = !empty($settings['logo']) 
      ? (Str::startsWith($settings['logo'], ['http://', 'https://', '/']) ? $settings['logo'] : asset($settings['logo'])) 
      : asset('themes/workspace/assets/logo.png');
  $alt = $settings['alt'] ?? 'Workspace Interior design and build';
  $ariaLabel = $settings['aria_label'] ?? 'Workspace';
?>
<section class="panel panel-home" id="home" data-ui="light" aria-label="<?php echo e($ariaLabel); ?>">
  <div class="surface surface-home">
    <img class="brand-logo animate__animated animate__zoomIn animate__delay-1s" src="<?php echo e($logo); ?>" alt="<?php echo e($alt); ?>" />
    <div class="home-about-heading" aria-hidden="true">
      <span class="home-about-word home-about-black">ABOUT</span>
      <span class="home-about-word home-about-red">WORKSPACE</span>
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/home.blade.php ENDPATH**/ ?>
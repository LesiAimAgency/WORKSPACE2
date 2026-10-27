<?php
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
?>

<section class="panel panel-service" id="service" data-ui="light" aria-label="Our services">
  <div class="surface service-surface">
    <div class="service-layout">

      <div class="service-copy animate__animated animate__fadeInDown">
        <h2><?php echo nl2br(e($heading)); ?></h2>
        <span class="service-accent" aria-hidden="true"></span>
        <p><?php echo e($description); ?></p>
      </div>

      <div class="service-gallery-wrapper animate__animated animate__fadeInUp animate__delay-1s">
        <button class="gallery-arrow gallery-arrow-prev" type="button" aria-label="Previous service"></button>
        <div class="service-gallery" aria-label="Service gallery">
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $srv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="service-card <?php echo e($index === 0 ? 'service-card-active' : ''); ?>">
              <div class="service-media">
                <img src="<?php echo e($srv['image']); ?>" alt="<?php echo e($srv['title']); ?>" />
              </div>
              <h3><?php echo e($srv['title']); ?></h3>
              <p><?php echo e(Str::limit($srv['desc'], 130)); ?></p>
              <a href="<?php echo e($srv['link']); ?>" <?php if(!empty($srv['data_section'])): ?> data-section="<?php echo e($srv['data_section']); ?>" <?php endif; ?>>View more</a>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <button class="gallery-arrow gallery-arrow-next" type="button" aria-label="Next service"></button>
      </div>
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/service.blade.php ENDPATH**/ ?>
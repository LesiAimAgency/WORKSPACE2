<?php
  $settings = $settings ?? [];
  $heading1 = $settings['heading1'] ?? 'WHY CHOOSING';
  $heading2 = $settings['heading2'] ?? 'WORKSPACE';

  $navItems = $settings['nav_items'] ?? [
      ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => false],
      ['label' => 'Our Services', 'link' => '#service', 'is_current' => false],
      ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => true],
  ];

  $defaultReasons = [
      [
          'prefix' => 'Because of the',
          'title' => 'QUALITY',
          'bullet_1' => 'Design and engineering team directly involved in production & construction.',
          'bullet_2' => 'Skilled workers are trained from the WorkSpace center.',
          'bullet_3' => 'Key personnel directly involved in the quality control process.',
      ],
      [
          'prefix' => 'Because of the',
          'title' => 'PROGRESS',
          'bullet_1' => 'Workers have trained to multitask from the centers of WS.',
          'bullet_2' => 'Cooperation in accomplishing goals with strategic partners is the core value.',
          'bullet_3' => 'Flexible culture is highly valued by the WorkSpace management team.',
      ],
      [
          'prefix' => 'Because of the',
          'title' => 'COST EFFECTIVE',
          'bullet_1' => 'The production and construction process minimizes intermediate costs.',
          'bullet_2' => 'Key material and performance are managed for stronger efficiency.',
          'bullet_3' => 'WorkSpace brings practical solutions before the production and construction stage.',
      ],
      [
          'prefix' => 'Because of the',
          'title' => 'PROBLEM-SOLVING',
          'bullet_1' => 'Answer to handling design objectives for customers within agreed budgets.',
          'bullet_2' => 'The system keeps technical and site control in one working flow.',
          'bullet_3' => 'Real solutions focus on fast delivery improvement.',
      ],
  ];

  $rawReasons = !empty($settings['reasons']) && is_array($settings['reasons']) && count($settings['reasons']) > 0
      ? $settings['reasons']
      : $defaultReasons;

  $reasons = collect($rawReasons)->filter(fn($r) => !empty($r['title']))->values();
  if ($reasons->isEmpty()) {
      $reasons = collect($defaultReasons);
  }
  $firstReason = $reasons->first();
  $whySlides = $reasons->map(fn($r) => [
      'title' => $r['title'] ?? '',
      'bullets' => collect([$r['bullet_1'] ?? null, $r['bullet_2'] ?? null, $r['bullet_3'] ?? null])
          ->filter()
          ->values()
          ->all(),
  ])->values();
  $firstBullets = $whySlides->first()['bullets'] ?? [];

  $partnersHeading = $settings['partners_heading'] ?? 'OUR CUSTOMERS - PARTNERS';
  $defaultPartners = [
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 1'],
      ['image' => asset('themes/workspace/assets/parnert2.png'), 'alt' => 'Strategic Partner 2'],
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 3'],
      ['image' => asset('themes/workspace/assets/parnert2.png'), 'alt' => 'Strategic Partner 4'],
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 5'],
  ];
  $partners = !empty($settings['partners']) && is_array($settings['partners']) && count($settings['partners']) > 0
      ? array_map(function ($p) {
          $img = $p['image'] ?? '';
          if (!empty($img) && !Str::startsWith($img, ['http://', 'https://', '/'])) {
              $img = asset($img);
          }
          return [
              'image' => !empty($img) ? $img : asset('themes/workspace/assets/parnert1.png'),
              'alt' => $p['alt'] ?? 'Partner',
          ];
      }, $settings['partners'])
      : $defaultPartners;
?>

<section class="panel panel-why" id="why-workspace" data-ui="light" aria-label="Why choosing Workspace">
  <div class="surface why-surface">
    <div class="why-layout">
      <header class="why-heading animate__animated animate__fadeInDown">
        <h2>
          <?php echo e($heading1); ?>

          <span><?php echo e($heading2); ?></span>
        </h2>
      </header>

      <div class="why-content">
        <div class="why-statement animate__animated animate__fadeInLeft animate__delay-1s">
          <p><?php echo e($firstReason['prefix'] ?? 'Because of the'); ?></p>
          <strong class="why-slide-title"><?php echo e($firstReason['title'] ?? 'QUALITY'); ?></strong>
        </div>

        <ul class="why-bullets animate__animated animate__fadeInRight animate__delay-1s">
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $firstBullets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bullet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($bullet); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>

        <div class="why-controls" aria-label="Why choose navigation">
          <button class="why-arrow why-prev" type="button" aria-label="Previous reason"></button>
          <button class="why-arrow why-next" type="button" aria-label="Next reason"></button>
        </div>

        <div class="why-progress" aria-hidden="true">
          <span></span>
        </div>
      </div>

      <!-- Partner Marquee at bottom of Why Choose Us (Figma Frame 12:1958) -->
      <div class="why-partners animate__animated animate__fadeInUp animate__delay-1s">
        <h3><?php echo e($partnersHeading); ?></h3>
        <div class="partner-slider" aria-label="Partner brands">
          <div class="partner-viewport">
            <div class="partner-track">
              <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="partner-card">
                  <img src="<?php echo e($partner['image']); ?>" alt="<?php echo e($partner['alt']); ?>" />
                </article>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="application/json" id="workspace-why-slides"><?php echo json_encode($whySlides, 15, 512) ?></script>
<?php echo $__env->make('themes.workspace.widgets.connect', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/why.blade.php ENDPATH**/ ?>
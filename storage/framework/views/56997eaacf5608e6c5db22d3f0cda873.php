<?php
  $settings = $settings ?? [];
  $sectionId = $settings['section_id'] ?? 'design-project';
  $sectionClass = $settings['section_class'] ?? 'project-design';
  $dataUi = $settings['data_ui'] ?? 'dark';
  $bgImage = !empty($settings['bg_image'])
      ? (Str::startsWith($settings['bg_image'], ['http://', 'https://', '/']) ? $settings['bg_image'] : asset($settings['bg_image']))
      : asset('themes/workspace/assets/build1.png');

  $navItems = $settings['nav_items'] ?? [
      ['label' => 'What we do', 'is_current' => false],
      ['label' => 'Projects', 'is_current' => true],
      ['label' => 'About the expert', 'is_current' => false],
  ];

  $defaultProjects = [
      [
          'label' => 'PROJECT 01',
          'name' => 'Tech Hub Headquarters',
          'area' => '1,800 m²',
          'location' => 'Keangnam Landmark, Hanoi',
          'typology' => 'Modern Corporate Office',
          'style' => 'Industrial Minimalism',
          'tech' => 'Acoustic baffle ceilings, IoT lighting control, modular workstations',
          'team' => 'Lead Architect Nguyen & Technical Specialists',
          'image' => asset('themes/workspace/assets/build1.png'),
          'gallery' => [
              asset('themes/workspace/assets/build1.png'),
              asset('themes/workspace/assets/build3.png'),
              asset('themes/workspace/assets/build2.png'),
          ],
      ],
      [
          'label' => 'PROJECT 02',
          'name' => 'Fintech Innovation Center',
          'area' => '2,400 m²',
          'location' => 'Lotte Center, Ba Dinh, Hanoi',
          'typology' => 'Creative Co-working & Studio',
          'style' => 'Biophilic Scandinavian',
          'tech' => 'Green wall air-filtering, soundproof phone booths, ergonomic sit-stand desks',
          'team' => 'Senior Designer Tran & Interior Team',
          'image' => asset('themes/workspace/assets/build3.png'),
          'gallery' => [
              asset('themes/workspace/assets/build3.png'),
              asset('themes/workspace/assets/build2.png'),
          ],
      ],
  ];

  $projects = !empty($settings['projects']) && is_array($settings['projects']) && count($settings['projects']) > 0
      ? array_map(function ($p, $idx) use ($bgImage) {
          $img = $p['image'] ?? '';
          if (!empty($img) && !Str::startsWith($img, ['http://', 'https://', '/'])) {
              $img = asset($img);
          }
          $rawGallery = $p['gallery'] ?? [];
          $gallery = is_array($rawGallery) ? $rawGallery : (is_string($rawGallery) ? json_decode($rawGallery, true) : []);
          $gallery = !empty($gallery) ? array_map(fn($g) => Str::startsWith($g, ['http://', 'https://', '/']) ? $g : asset($g), $gallery) : [$img ?: $bgImage];

          return [
              'label' => $p['label'] ?? ('PROJECT ' . str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT)),
              'name' => $p['name'] ?? '',
              'area' => $p['area'] ?? '',
              'location' => $p['location'] ?? '',
              'typology' => $p['typology'] ?? '',
              'style' => $p['style'] ?? '',
              'tech' => $p['tech'] ?? '',
              'team' => $p['team'] ?? '',
              'image' => !empty($img) ? $img : $bgImage,
              'gallery' => $gallery,
          ];
      }, $settings['projects'], array_keys($settings['projects']))
      : $defaultProjects;
?>

<section class="panel panel-project <?php echo e($sectionClass); ?>" id="<?php echo e($sectionId); ?>" data-ui="<?php echo e($dataUi); ?>" aria-label="Projects">
  <div class="surface project-surface">
    <img class="project-bg" src="<?php echo e($bgImage); ?>" alt="" aria-hidden="true" />
    <span class="project-shade" aria-hidden="true"></span>

    <nav class="workflow-mini-nav project-mini-nav" aria-label="Workflow menu">
      <span class="mini-rule" aria-hidden="true"></span>
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button <?php echo !empty($item['is_current']) ? 'class="is-current"' : ''; ?> type="button"><?php echo e($item['label'] ?? ''); ?></button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <div class="project-slides animate__animated animate__zoomIn animate__delay-1s" aria-live="polite">
      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $proj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $galleryJson = e(json_encode($proj['gallery']));
        ?>
        <dl class="project-info project-slide" data-image="<?php echo e($proj['image']); ?>" data-label="<?php echo e($proj['label']); ?>" data-gallery="<?php echo e($galleryJson); ?>" style="<?php echo e($index === 0 ? 'display: grid;' : 'display: none;'); ?>">
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['name'])): ?><div><dt>Project</dt><dd><?php echo e($proj['name']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['area'])): ?><div><dt>Total Area</dt><dd><?php echo e($proj['area']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['location'])): ?><div><dt>Location</dt><dd><?php echo e($proj['location']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['typology'])): ?><div><dt>Typology</dt><dd><?php echo e($proj['typology']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['style'])): ?><div><dt>Style</dt><dd><?php echo e($proj['style']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['tech'])): ?><div><dt>Tech Solution</dt><dd><?php echo e($proj['tech']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($proj['team'])): ?><div><dt>Team</dt><dd><?php echo e($proj['team']); ?></dd></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </dl>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <button class="project-pill animate__animated animate__fadeInUp animate__delay-1s" type="button">Project Images</button>
    <div class="project-count animate__animated animate__fadeInUp animate__delay-1s">
      <strong><?php echo e($projects[0]['label'] ?? 'PROJECT 01'); ?></strong>
      <span></span>
    </div>
    <div class="project-controls animate__animated animate__fadeInUp animate__delay-1s" aria-label="Project navigation">
      <button class="project-arrow project-prev" type="button" aria-label="Previous project"></button>
      <button class="project-arrow project-next" type="button" aria-label="Next project"></button>
    </div>
    <div class="project-lightbox" role="dialog" aria-modal="true" aria-label="Project image gallery" aria-hidden="true">
      <div class="project-lightbox-dialog">
        <button class="project-lightbox-close" type="button" aria-label="Close project images"></button>
        <div class="project-split-viewer">
          <div class="split-pane split-pane-before">
            <img class="project-lightbox-image project-img-before" src="" alt="Before" />
          </div>
          <div class="split-pane split-pane-after">
            <img class="project-lightbox-image project-img-after" src="" alt="After" />
          </div>
          <div class="split-divider" aria-hidden="true"></div>
        </div>
        <div class="project-lightbox-controls">
          <button class="project-lightbox-arrow project-lightbox-prev" type="button" aria-label="Previous image"></button>
          <button class="project-lightbox-arrow project-lightbox-next" type="button" aria-label="Next image"></button>
          <div class="project-lightbox-count">1 / 1</div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/project.blade.php ENDPATH**/ ?>
<?php
  $connectHeading1 = $settings['connect_heading1'] ?? ($settings['heading1'] ?? 'CONNECT');
  $connectHeading2 = $settings['connect_heading2'] ?? ($settings['heading2'] ?? 'WORKSPACE');
  $labelName = $settings['label_name'] ?? 'Your Name';
  $labelPhone = $settings['label_phone'] ?? 'Phone Number (*)';
  $labelEmail = $settings['label_email'] ?? 'Email';
  $labelNote = $settings['label_note'] ?? 'Note for us';
  $buttonText = $settings['button_text'] ?? 'CONNECT WITH US';
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

  if (count($partners) > 0 && count($partners) < 5) {
      $originalCount = count($partners);
      $needed = 5 - $originalCount;
      for ($i = 0; $i < $needed; $i++) {
          $partners[] = $partners[$i % $originalCount];
      }
  }

  $formAction = route('contact.submit');
?>

<section class="panel panel-connect" id="connect" data-ui="light" aria-label="Connect Workspace">
  <div class="surface connect-surface">
    <div class="connect-layout">
      <header class="connect-heading">
        <h2>
          <?php echo e($connectHeading1); ?>

          <span><?php echo e($connectHeading2); ?></span>
        </h2>
      </header>

      <form class="connect-form" aria-label="Contact form" action="<?php echo e($formAction); ?>" method="POST" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="form_name" value="workspace_connect">
        <input type="hidden" name="_form_start_time" value="<?php echo e(time()); ?>">
        <input class="hp-field" type="text" name="website" autocomplete="off" tabindex="-1" aria-hidden="true" style="display:none;">
        <input type="hidden" name="lead_service" value="">
        <input type="hidden" name="lead_project" value="">
        <input type="hidden" name="lead_step" value="">
        <input type="hidden" name="lead_section" value="">
        
        <label>
          <span><?php echo e($labelName); ?></span>
          <input type="text" name="name" required />
        </label>
        
        <label>
          <span><?php echo e($labelPhone); ?></span>
          <input type="tel" name="phone" required />
        </label>
        
        <label>
          <span><?php echo e($labelEmail); ?></span>
          <input type="email" name="email" required />
        </label>
        
        <label>
          <span><?php echo e($labelNote); ?></span>
          <textarea name="message" rows="3"></textarea>
        </label>
        
        <button type="submit"><?php echo e($buttonText); ?></button>
        <p class="connect-status" role="status" aria-live="polite"></p>
      </form>

      <div class="connect-partners">
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
<?php /**PATH C:\MAMP\htdocs\workspace2\resources\views/themes/workspace/widgets/connect.blade.php ENDPATH**/ ?>
@php
  if (empty($settings)) {
      $dbWidget = \App\Models\Widget::withoutGlobalScopes()->where('type', 'workspace_connect')->orderByDesc('is_active')->orderByDesc('id')->first();
      $settings = $dbWidget && is_array($dbWidget->settings) ? $dbWidget->settings : [];
  }
  $connectHeading1 = $settings['connect_heading1'] ?? ($settings['heading1'] ?? 'CONNECT');
  $connectHeading2 = $settings['connect_heading2'] ?? ($settings['heading2'] ?? 'WORKSPACE');
  $labelName = $settings['label_name'] ?? 'Your Name';
  $labelPhone = $settings['label_phone'] ?? 'Phone Number (*)';
  $labelEmail = $settings['label_email'] ?? 'Email';
  $labelNote = $settings['label_note'] ?? 'Note for us';
  $buttonText = $settings['button_text'] ?? 'CONNECT WITH US';
  $partnersHeading = setting('partners_heading') ?: 'OUR CUSTOMERS - PARTNERS';

  $defaultPartners = [
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 1', 'link' => ''],
      ['image' => asset('themes/workspace/assets/parnert2.png'), 'alt' => 'Strategic Partner 2', 'link' => ''],
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 3', 'link' => ''],
      ['image' => asset('themes/workspace/assets/parnert2.png'), 'alt' => 'Strategic Partner 4', 'link' => ''],
      ['image' => asset('themes/workspace/assets/parnert1.png'), 'alt' => 'Strategic Partner 5', 'link' => ''],
  ];

  $rawSettingPartners = setting('partners_items', setting('partners'));
  if (is_string($rawSettingPartners)) {
      $rawSettingPartners = json_decode($rawSettingPartners, true) ?: [];
  }

  $activePartners = is_array($rawSettingPartners)
      ? array_values(array_filter($rawSettingPartners, fn ($p) => is_array($p) && ($p['is_active'] ?? true) !== false))
      : [];

  $partners = count($activePartners) > 0
      ? array_map(function ($p) {
          $img = $p['image'] ?? '';
          if (!empty($img) && !Str::startsWith($img, ['http://', 'https://', '/'])) {
              $img = asset($img);
          }
          return [
              'image' => !empty($img) ? $img : asset('themes/workspace/assets/parnert1.png'),
              'alt' => $p['alt'] ?? 'Partner',
              'link' => $p['link'] ?? '',
          ];
      }, $activePartners)
      : $defaultPartners;

  if (count($partners) > 0 && count($partners) < 5) {
      $originalCount = count($partners);
      $needed = 5 - $originalCount;
      for ($i = 0; $i < $needed; $i++) {
          $partners[] = $partners[$i % $originalCount];
      }
  }

  $formAction = route('contact.submit');
@endphp

<section class="panel panel-connect" id="connect" data-ui="light" aria-label="Connect Workspace">
  <div class="surface connect-surface">
    <div class="connect-layout">
      <header class="connect-heading">
        <h2>
          {{ $connectHeading1 }}
          <span>{{ $connectHeading2 }}</span>
        </h2>
      </header>

      <form class="connect-form" aria-label="Contact form" action="{{ $formAction }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="form_name" value="workspace_connect">
        <input type="hidden" name="_form_start_time" value="{{ time() }}">
        <input class="hp-field" type="text" name="website" autocomplete="off" tabindex="-1" aria-hidden="true" style="display:none;">
        <input type="hidden" name="lead_service" value="">
        <input type="hidden" name="lead_project" value="">
        <input type="hidden" name="lead_step" value="">
        <input type="hidden" name="lead_section" value="">
        
        <label>
          <span>{{ $labelName }}</span>
          <input type="text" name="name" required />
        </label>
        
        <label>
          <span>{{ $labelPhone }}</span>
          <input type="tel" name="phone" required />
        </label>
        
        <label>
          <span>{{ $labelEmail }}</span>
          <input type="email" name="email" required />
        </label>
        
        <label>
          <span>{{ $labelNote }}</span>
          <textarea name="message" rows="3"></textarea>
        </label>
        
        <button type="submit">{{ $buttonText }}</button>
        <p class="connect-status" role="status" aria-live="polite"></p>
      </form>

      <div class="connect-partners">
        <h3>{{ $partnersHeading }}</h3>
        <div class="partner-slider" aria-label="Partner brands">
          <div class="partner-viewport">
            <div class="partner-track">
              @foreach($partners as $partner)
                <article class="partner-card">
                  @if(!empty($partner['link']))
                    <a href="{{ $partner['link'] }}" target="_blank" rel="noopener noreferrer">
                      <img src="{{ $partner['image'] }}" alt="{{ $partner['alt'] }}" />
                    </a>
                  @else
                    <img src="{{ $partner['image'] }}" alt="{{ $partner['alt'] }}" />
                  @endif
                </article>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

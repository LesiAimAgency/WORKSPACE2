@extends('cms.settings.template', ['title' => 'Cấu hình Modal Box Liên Hệ'])

@section('form-content')
<x-media-picker-modal />

@php
  // Helper to extract prefix, highlight, suffix from raw HTML or existing string
  $parseTitle = function($rawHtml, $defPrefix, $defHighlight, $defSuffix) {
      if (empty($rawHtml)) {
          return [$defPrefix, $defHighlight, $defSuffix];
      }
      if (preg_match('/^(.*?)(?:<span[^>]*>(.*?)<\/span>)(.*)$/is', $rawHtml, $m)) {
          return [trim($m[1]), trim($m[2]), trim($m[3])];
      }
      return [trim($rawHtml), '', ''];
  };

  // 1. Branding & Logo
  $savedLogo = array_key_exists('contact_modal_logo', $settings) ? $settings['contact_modal_logo'] : (setting('contact_modal_logo') ?: asset('themes/inbetween_v2/images/Logo.svg'));
  $savedLogoAlt = array_key_exists('contact_modal_logo_alt', $settings) ? $settings['contact_modal_logo_alt'] : (setting('contact_modal_logo_alt') ?? '');

  // Title Lines - Can be null / empty without forcing defaults!
  $savedL1Prefix = array_key_exists('contact_modal_title_l1_prefix', $settings) 
      ? (string)$settings['contact_modal_title_l1_prefix'] 
      : (string)(setting('contact_modal_title_l1_prefix') ?? '');
  $savedL1Highlight = array_key_exists('contact_modal_title_l1_highlight', $settings) 
      ? (string)$settings['contact_modal_title_l1_highlight'] 
      : (string)(setting('contact_modal_title_l1_highlight') ?? '');
  $savedL1Suffix = array_key_exists('contact_modal_title_l1_suffix', $settings) 
      ? (string)$settings['contact_modal_title_l1_suffix'] 
      : (string)(setting('contact_modal_title_l1_suffix') ?? '');

  $savedL2Prefix = array_key_exists('contact_modal_title_l2_prefix', $settings) 
      ? (string)$settings['contact_modal_title_l2_prefix'] 
      : (string)(setting('contact_modal_title_l2_prefix') ?? '');
  $savedL2Highlight = array_key_exists('contact_modal_title_l2_highlight', $settings) 
      ? (string)$settings['contact_modal_title_l2_highlight'] 
      : (string)(setting('contact_modal_title_l2_highlight') ?? '');
  $savedL2Suffix = array_key_exists('contact_modal_title_l2_suffix', $settings) 
      ? (string)$settings['contact_modal_title_l2_suffix'] 
      : (string)(setting('contact_modal_title_l2_suffix') ?? '');

  // Fallback to legacy title line only if granular prefix/highlight/suffix were NEVER configured in DB
  $hasL1Configured = array_key_exists('contact_modal_title_l1_prefix', $settings) || setting('contact_modal_title_l1_prefix') !== null;
  if (!$hasL1Configured && !empty($settings['contact_modal_title_line1'])) {
      [$savedL1Prefix, $savedL1Highlight, $savedL1Suffix] = $parseTitle($settings['contact_modal_title_line1'], '', '', '');
  }
  $hasL2Configured = array_key_exists('contact_modal_title_l2_prefix', $settings) || setting('contact_modal_title_l2_prefix') !== null;
  if (!$hasL2Configured && !empty($settings['contact_modal_title_line2'])) {
      [$savedL2Prefix, $savedL2Highlight, $savedL2Suffix] = $parseTitle($settings['contact_modal_title_line2'], '', '', '');
  }

  $savedDesc = array_key_exists('contact_modal_desc', $settings) ? (string)$settings['contact_modal_desc'] : (string)(setting('contact_modal_desc') ?? '');
  $savedCloseText = array_key_exists('contact_modal_close_text', $settings) ? (string)$settings['contact_modal_close_text'] : (string)(setting('contact_modal_close_text') ?? '');

  // 2. Slider Images
  $rawSliderImages = $settings['contact_modal_slider_images'] ?? setting('contact_modal_slider_images');
  if (is_string($rawSliderImages)) {
      $rawSliderImages = json_decode($rawSliderImages, true);
  }
  if (!is_array($rawSliderImages) || empty($rawSliderImages)) {
      $savedSliderImages = [
          ['image' => asset('themes/inbetween_v2/images/founder-airu-portrait.png'), 'alt' => 'in • between - Founder AiRu'],
          ['image' => asset('themes/inbetween_v2/images/client-asian-smes.png'), 'alt' => 'in • between - Asian SMEs'],
          ['image' => asset('themes/inbetween_v2/images/client-founders.png'), 'alt' => 'in • between - Founders & Entrepreneurs'],
          ['image' => asset('themes/inbetween_v2/images/client-regional-teams.png'), 'alt' => 'in • between - Regional Teams'],
      ];
  } else {
      $savedSliderImages = [];
      foreach ($rawSliderImages as $item) {
          $img = $item['image'] ?? '';
          if ($img && !Str::startsWith($img, ['http://', 'https://', '/'])) {
              $img = asset($img);
          }
          $savedSliderImages[] = [
              'image' => $img,
              'alt' => $item['alt'] ?? ''
          ];
      }
  }

  // 3. Form Fields
  $savedFullnameLabel = array_key_exists('contact_modal_fullname_label', $settings) ? (string)$settings['contact_modal_fullname_label'] : (setting('contact_modal_fullname_label') ?? 'FULL NAME');
  $savedFullnamePlaceholder = array_key_exists('contact_modal_fullname_placeholder', $settings) ? (string)$settings['contact_modal_fullname_placeholder'] : (setting('contact_modal_fullname_placeholder') ?? '');
  $savedFullnameError = array_key_exists('contact_modal_fullname_error', $settings) ? (string)$settings['contact_modal_fullname_error'] : (setting('contact_modal_fullname_error') ?? 'Please enter your full name.');
  $savedFullnameRequired = ($settings['contact_modal_fullname_required'] ?? setting('contact_modal_fullname_required', '1')) !== '0';

  $savedPhoneLabel = array_key_exists('contact_modal_phone_label', $settings) ? (string)$settings['contact_modal_phone_label'] : (setting('contact_modal_phone_label') ?? 'PHONE NUMBER *');
  $savedPhonePlaceholder = array_key_exists('contact_modal_phone_placeholder', $settings) ? (string)$settings['contact_modal_phone_placeholder'] : (setting('contact_modal_phone_placeholder') ?? '');
  $savedPhoneRequired = ($settings['contact_modal_phone_required'] ?? setting('contact_modal_phone_required', '1')) !== '0';

  $savedEmailLabel = array_key_exists('contact_modal_email_label', $settings) ? (string)$settings['contact_modal_email_label'] : (setting('contact_modal_email_label') ?? 'EMAIL');
  $savedEmailPlaceholder = array_key_exists('contact_modal_email_placeholder', $settings) ? (string)$settings['contact_modal_email_placeholder'] : (setting('contact_modal_email_placeholder') ?? '');
  $savedEmailRequired = ($settings['contact_modal_email_required'] ?? setting('contact_modal_email_required', '0')) === '1';

  $savedServiceLabel = array_key_exists('contact_modal_service_label', $settings) ? (string)$settings['contact_modal_service_label'] : (setting('contact_modal_service_label') ?? 'SERVICE NEEDED');
  $savedServiceRequired = ($settings['contact_modal_service_required'] ?? setting('contact_modal_service_required', '0')) === '1';

  // 4. Services Dropdown Options
  $rawServices = $settings['contact_modal_services'] ?? setting('contact_modal_services');
  if (is_string($rawServices)) {
      $rawServices = json_decode($rawServices, true);
  }
  if (!is_array($rawServices) || empty($rawServices)) {
      $savedServices = [
          ['value' => 'sales-bd', 'label' => 'Sales & BD'],
          ['value' => 'market-validation', 'label' => 'Market Validation'],
          ['value' => 'market-entry', 'label' => 'Market Entry Execution'],
          ['value' => 'local-support', 'label' => 'Local Business Support'],
      ];
  } else {
      $savedServices = $rawServices;
  }

  $savedMessageLabel = array_key_exists('contact_modal_message_label', $settings) ? (string)$settings['contact_modal_message_label'] : (setting('contact_modal_message_label') ?? 'MESSAGE / INQUIRY');
  $savedMessagePlaceholder = array_key_exists('contact_modal_message_placeholder', $settings) ? (string)$settings['contact_modal_message_placeholder'] : (setting('contact_modal_message_placeholder') ?? '');
  $savedMessageRequired = ($settings['contact_modal_message_required'] ?? setting('contact_modal_message_required', '0')) === '1';

  $savedPrivacyText = array_key_exists('contact_modal_privacy_text', $settings) ? (string)$settings['contact_modal_privacy_text'] : (setting('contact_modal_privacy_text') ?? 'I have read and agree to the Data & Privacy Policy of in • between');
  $savedPrivacyChecked = ($settings['contact_modal_privacy_checked'] ?? setting('contact_modal_privacy_checked', '1')) !== '0';
  $savedPrivacyRequired = ($settings['contact_modal_privacy_required'] ?? setting('contact_modal_privacy_required', '1')) !== '0';

  $savedNewsletterText = array_key_exists('contact_modal_newsletter_text', $settings) ? (string)$settings['contact_modal_newsletter_text'] : (setting('contact_modal_newsletter_text') ?? 'Send me regular business updates and market insights');
  $savedNewsletterChecked = ($settings['contact_modal_newsletter_checked'] ?? setting('contact_modal_newsletter_checked', '0')) === '1';

  $savedSubmitText = array_key_exists('contact_modal_submit_text', $settings) ? (string)$settings['contact_modal_submit_text'] : (setting('contact_modal_submit_text') ?? "LET'S CONNECT");
  $savedSuccessMsg = array_key_exists('contact_modal_success_msg', $settings) ? (string)$settings['contact_modal_success_msg'] : (setting('contact_modal_success_msg') ?? '✓ Thank you! Your inquiry has been sent successfully. We will get back to you shortly.');

  // 5. Contact Information (Mobile Drawer & Footer)
  // Retrieve Footer Widget settings to pour data from footer widget directly into modal box
  $footerWidget = null;
  $footerWidgetSettings = [];
  try {
      $currentProj = $project ?? (function_exists('current_project') ? current_project() : (request()->attributes->get('project') ?? session('current_project')));
      $projId = is_object($currentProj) ? $currentProj->id : (session('current_project_id') ?? 7);

      $footerWidget = \App\Models\Widget::withoutGlobalScopes()
          ->where('type', 'inbetween_v2_footer')
          ->where(function($q) use ($projId) {
              if ($projId) {
                  $q->where('project_id', $projId)->orWhereNull('project_id');
              }
          })
          ->orderByRaw("CASE WHEN project_id = ? THEN 0 ELSE 1 END", [$projId ?: 0])
          ->first();

      if ($footerWidget && is_array($footerWidget->settings)) {
          $footerWidgetSettings = $footerWidget->settings;
      }
  } catch (\Throwable $e) {}

  $footerPhone = !empty($footerWidgetSettings['contact_phone']) ? $footerWidgetSettings['contact_phone'] : null;
  $footerEmail = !empty($footerWidgetSettings['contact_email']) ? $footerWidgetSettings['contact_email'] : null;
  $footerAddress = !empty($footerWidgetSettings['contact_address']) ? $footerWidgetSettings['contact_address'] : null;
  $footerContactTitle = !empty($footerWidgetSettings['contact_for_work_title']) ? $footerWidgetSettings['contact_for_work_title'] : null;

  $settingEmail = $settings['contact_email'] ?? (setting_string('contact_email') ?: null);
  $settingPhone = $settings['contact_phone'] ?? (setting_string('contact_phone') ?: null);
  $settingAddress = $settings['contact_address'] ?? (setting_string('contact_address') ?: null);
  $settingContactTitle = $settings['contact_info_title'] ?? (setting_string('contact_info_title') ?: null);

  // Pour data from footer widget into modal box:
  $isSeederEmail = empty($settingEmail) || in_array($settingEmail, ['contact@example.com', 'contact@inbetween.vn']);
  $savedContactEmail = (!$isSeederEmail) 
      ? $settingEmail 
      : ($footerEmail ?: ($settingEmail ?: 'hello@inbetween.asia'));

  $isSeederPhone = empty($settingPhone) || in_array($settingPhone, ['0123456789', '+84 90 xxx xxxx']);
  $savedContactPhone = (!$isSeederPhone) 
      ? $settingPhone 
      : ($footerPhone ?: ($settingPhone ?: '+84 86 5651874'));

  $isSeederAddr = empty($settingAddress) || $settingAddress === 'Ho Chi Minh City, Vietnam';
  $savedContactAddress = (!$isSeederAddr) 
      ? $settingAddress 
      : ($footerAddress ?: ($settingAddress ?: 'Ho Chi Minh City, Vietnam'));

  $savedContactInfoTitle = $settingContactTitle ?: ($footerContactTitle ?: 'CONTACT INFORMATION');

  $footerWidgetData = [
      'phone' => $footerPhone ?: '',
      'email' => $footerEmail ?: '',
      'address' => $footerAddress ?: '',
      'title' => $footerContactTitle ?: 'CONTACT INFORMATION',
  ];

  // 6. Security, Anti-Spam & Google reCAPTCHA
  $savedRecaptchaEnabled = ($settings['recaptcha_enabled'] ?? setting('recaptcha_enabled', '0')) === '1';
  $savedRecaptchaSiteKey = $settings['recaptcha_site_key'] ?? setting_string('recaptcha_site_key') ?: (config('services.recaptcha.site_key') ?: '');
  $savedRecaptchaSecretKey = $settings['recaptcha_secret_key'] ?? setting_string('recaptcha_secret_key') ?: (config('services.recaptcha.secret_key') ?: '');
  $savedAntispamBlockLinks = ($settings['contact_antispam_block_links'] ?? setting('contact_antispam_block_links', '1')) !== '0';
@endphp

<div x-data="modalBoxManager()" 
     @media-picker-selected.window="handleMediaSelected($event.detail)"
     @media-selected.window="handleMediaSelected($event.detail)"
     class="space-y-6">


  <!-- Main 2-Column Grid: Left Editors (7 cols) & Right Live Preview (5 cols) -->
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

    <!-- LEFT COLUMN: Settings Tabs & Editors -->
    <div class="xl:col-span-7 space-y-5">

      <!-- Navigation Subtabs -->
      <div class="flex items-center gap-1.5 bg-gray-100 p-1.5 rounded-xl border border-gray-200 overflow-x-auto">
        <button type="button" 
                @click="activeSection = 'branding'"
                :class="activeSection === 'branding' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-signature text-[#EC460B]"></i>
          <span>Thương Hiệu &amp; Tiêu Đề</span>
        </button>

        <button type="button" 
                @click="activeSection = 'slider'"
                :class="activeSection === 'slider' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-images text-[#EC460B]"></i>
          <span>Slider Ảnh (<span x-text="sliderImages.length"></span>)</span>
        </button>

        <button type="button" 
                @click="activeSection = 'fields'"
                :class="activeSection === 'fields' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-list-check text-[#EC460B]"></i>
          <span>Trường Form &amp; Dịch Vụ</span>
        </button>

        <button type="button" 
                @click="activeSection = 'submit'"
                :class="activeSection === 'submit' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-shield-halved text-[#EC460B]"></i>
          <span>Điều Khoản &amp; Nút Gửi</span>
        </button>

        <button type="button" 
                @click="activeSection = 'security'"
                :class="activeSection === 'security' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-robot text-[#EC460B]"></i>
          <span>Bảo Mật &amp; Captcha</span>
        </button>

        <button type="button" 
                @click="activeSection = 'contact_info'"
                :class="activeSection === 'contact_info' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="px-3.5 py-2 rounded-lg text-xs transition flex items-center gap-2 whitespace-nowrap">
          <i class="fa-solid fa-address-book text-[#EC460B]"></i>
          <span>Thông Tin Liên Hệ</span>
        </button>
      </div>

      <!-- SECTION 1: Thương Hiệu & Tiêu Đề -->
      <div x-show="activeSection === 'branding'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 pb-3">
          <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">1</span>
            Nhận Diện Thương Hiệu (Cột Trái Modal)
          </h3>
          <p class="text-xs text-gray-500 mt-1">Tuỳ biến Logo, Tiêu đề thương hiệu và Lời mô tả mở đầu.</p>
        </div>

        <!-- Logo Setting -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 shadow-xs">
          <label class="block text-xs font-bold text-gray-700 uppercase">
            Logo Modal
          </label>
          <div class="flex items-center gap-4">
            <!-- Preview Box -->
            <div class="w-20 h-16 rounded-lg border border-gray-200 bg-neutral-900 flex items-center justify-center p-2 overflow-hidden shrink-0 shadow-inner">
              <template x-if="logo">
                <img :src="resolveUrl(logo)" class="max-h-full max-w-full object-contain" alt="Logo preview">
              </template>
              <template x-if="!logo">
                <span class="text-[10px] text-gray-400 text-center">Chưa có logo</span>
              </template>
            </div>

            <!-- Logo URL Input + Media Picker trigger -->
            <div class="flex-1 space-y-2">
              <div class="flex gap-2">
                <input type="text" 
                       name="contact_modal_logo" 
                       x-model="logo" 
                       placeholder="Nhập link logo (vd: themes/inbetween_v2/images/Logo.svg)" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
                <button type="button" 
                        @click="openMediaPickerFor('logo')"
                        class="px-3.5 py-2 bg-[#EC460B] hover:bg-[#d03d09] text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shrink-0 shadow-xs">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Thư Viện Media</span>
                </button>
              </div>

              <!-- Quick File Upload -->
              <div class="flex items-center gap-2">
                <input type="file" 
                       name="contact_modal_logo_file" 
                       accept=".svg,.png,.webp,.jpg,.jpeg,.ico" 
                       @change="handleLogoFileUpload($event)"
                       class="text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-orange-50 file:text-[#EC460B] hover:file:bg-orange-100 cursor-pointer">
              </div>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Văn bản Alt của Logo (SEO &amp; Accessibility)</label>
            <input type="text" name="contact_modal_logo_alt" x-model="logoAlt" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
          </div>
        </div>

        <!-- Headings Setting (No HTML Required) -->
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 space-y-4 shadow-xs">
          <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
              <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                Tiêu Đề Lớn (Heading 2 Dòng)
              </label>
              <p class="text-[11px] text-gray-500 mt-0.5">Nhập chữ thông thường. Hệ thống sẽ tự động tô màu cam thương hiệu cho ô nổi bật.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
              <i class="fa-solid fa-circle-check text-xs"></i> Tự động định dạng màu cam
            </span>
          </div>

          <!-- Dòng 1 -->
          <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-gray-700 flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-md bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-bold">1</span>
                Dòng Tiêu Đề 1
              </span>
              <span class="text-[11px] text-gray-400 font-sans">
                Xem trước: <strong class="text-neutral-900 font-semibold" x-text="computedTitleLine1Plain || '(trống)'"></strong>
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
              <div class="md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Văn bản trước:</label>
                <input type="text" 
                       name="contact_modal_title_l1_prefix" 
                       x-model="titleL1Prefix" 
                       placeholder="Ví dụ: LET'S (hoặc để trống)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>

              <div class="md:col-span-4">
                <label class="block text-[11px] font-bold text-[#EC460B] mb-1 flex items-center justify-between">
                  <span>Chữ nổi bật (Màu cam):</span>
                  <span class="w-2 h-2 rounded-full bg-[#EC460B]"></span>
                </label>
                <input type="text" 
                       name="contact_modal_title_l1_highlight" 
                       x-model="titleL1Highlight" 
                       placeholder="Ví dụ: CONNECT (hoặc để trống)" 
                       class="w-full px-3 py-2 border border-orange-300 bg-orange-50/40 text-[#EC460B] font-bold rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>

              <div class="md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Văn bản sau (nếu có):</label>
                <input type="text" 
                       name="contact_modal_title_l1_suffix" 
                       x-model="titleL1Suffix" 
                       placeholder="(trống nếu không có)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>
            </div>
          </div>

          <!-- Dòng 2 -->
          <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-gray-700 flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-md bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-bold">2</span>
                Dòng Tiêu Đề 2
              </span>
              <span class="text-[11px] text-gray-400 font-sans">
                Xem trước: <strong class="text-neutral-900 font-semibold" x-text="computedTitleLine2Plain || '(trống)'"></strong>
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
              <div class="md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Văn bản trước:</label>
                <input type="text" 
                       name="contact_modal_title_l2_prefix" 
                       x-model="titleL2Prefix" 
                       placeholder="Ví dụ: WITH IN (hoặc để trống)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>

              <div class="md:col-span-4">
                <label class="block text-[11px] font-bold text-[#EC460B] mb-1 flex items-center justify-between">
                  <span>Ký tự / Từ nổi bật (Màu cam):</span>
                  <span class="w-2 h-2 rounded-full bg-[#EC460B]"></span>
                </label>
                <input type="text" 
                       name="contact_modal_title_l2_highlight" 
                       x-model="titleL2Highlight" 
                       placeholder="Ví dụ: • (hoặc để trống)" 
                       class="w-full px-3 py-2 border border-orange-300 bg-orange-50/40 text-[#EC460B] font-bold rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>

              <div class="md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Văn bản sau:</label>
                <input type="text" 
                       name="contact_modal_title_l2_suffix" 
                       x-model="titleL2Suffix" 
                       placeholder="Ví dụ: BETWEEN (hoặc để trống)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#EC460B] focus:border-[#EC460B]">
              </div>
            </div>
          </div>

          <!-- Hidden inputs for backward compatibility -->
          <input type="hidden" name="contact_modal_title_line1" :value="compiledTitleLine1">
          <input type="hidden" name="contact_modal_title_line2" :value="compiledTitleLine2">
        </div>

        <!-- Description Paragraph -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-2 shadow-xs">
          <label class="block text-xs font-bold text-gray-700 uppercase">
            Đoạn Giới Thiệu / Mô Tả Ngắn
          </label>
          <textarea name="contact_modal_desc" 
                    x-model="description" 
                    rows="3" 
                    placeholder="We help Asian SMEs, founders and entrepreneurs..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs leading-relaxed focus:ring-2 focus:ring-[#EC460B]"></textarea>
          <p class="text-[11px] text-gray-400">Đoạn văn nằm ngay dưới tiêu đề lớn bên cột trái.</p>
        </div>

        <!-- Close Button Text -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-2 shadow-xs">
          <label class="block text-xs font-bold text-gray-700 uppercase">
            Chữ Nút Đóng Modal
          </label>
          <input type="text" 
                 name="contact_modal_close_text" 
                 x-model="closeText" 
                 placeholder="Close" 
                 class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
          <p class="text-[11px] text-gray-400">Nút góc trên cùng bên phải modal (mặc định: "Close").</p>
        </div>
      </div>

      <!-- SECTION 2: Slider Ảnh Góc Trái -->
      <div x-show="activeSection === 'slider'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 flex-wrap gap-2">
          <div>
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
              <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">2</span>
              Slider Ảnh Giới Thiệu (Image Slider)
            </h3>
            <p class="text-xs text-gray-500 mt-1">Khung ảnh tỷ lệ 297/165 chạy tự động ở góc dưới trái của modal.</p>
          </div>
          <button type="button" 
                  @click="addSlide()" 
                  class="px-3.5 py-1.5 bg-[#EC460B] text-white rounded-lg text-xs font-semibold hover:bg-[#d03d09] transition flex items-center gap-1.5 shadow-xs">
            <i class="fa-solid fa-plus"></i>
            <span>Thêm Ảnh Slider</span>
          </button>
        </div>

        <!-- Hidden input storing sliderImages JSON -->
        <input type="hidden" name="contact_modal_slider_images" :value="JSON.stringify(sliderImages)">

        <!-- Slider Repeater Items -->
        <div class="space-y-3">
          <template x-for="(slide, sIdx) in sliderImages" :key="sIdx">
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center gap-3 group transition-all hover:border-gray-300">
              <!-- Index Badge & Grip -->
              <div class="flex items-center gap-2 shrink-0">
                <span class="w-6 h-6 rounded-md bg-neutral-100 text-neutral-700 text-xs font-bold flex items-center justify-center" x-text="sIdx + 1"></span>
              </div>

              <!-- Thumbnail Preview -->
              <div class="w-20 aspect-[297/165] rounded-md overflow-hidden bg-neutral-100 border border-neutral-300 shrink-0 relative flex items-center justify-center shadow-xs">
                <template x-if="slide.image">
                  <img :src="resolveUrl(slide.image)" class="w-full h-full object-cover" :alt="slide.alt || 'Slide'">
                </template>
                <template x-if="!slide.image">
                  <i class="fa-solid fa-image text-gray-400 text-sm"></i>
                </template>
              </div>

              <!-- Inputs: URL & Alt -->
              <div class="flex-1 w-full space-y-2">
                <div class="flex gap-2">
                  <input type="text" 
                         x-model="slide.image" 
                         placeholder="Đường dẫn ảnh (URL hoặc /storage/...)" 
                         class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
                  <button type="button" 
                          @click="openMediaPickerFor('slide', sIdx)"
                          class="px-3 py-1.5 bg-neutral-900 hover:bg-[#EC460B] text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shrink-0 shadow-xs"
                          title="Tải lên hoặc chọn ảnh từ thư viện media">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                    <span>Tải lên / Chọn ảnh</span>
                  </button>
                </div>
                <input type="text" 
                       x-model="slide.alt" 
                       placeholder="Chú thích ảnh (Alt text)..." 
                       class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 focus:ring-2 focus:ring-[#EC460B]">
              </div>

              <!-- Action Buttons -->
              <div class="flex sm:flex-col items-center gap-1 shrink-0 self-end sm:self-center">
                <button type="button" 
                        @click="moveSlide(sIdx, sIdx - 1)" 
                        :disabled="sIdx === 0"
                        class="p-1.5 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition" 
                        title="Di chuyển lên">
                  <i class="fa-solid fa-chevron-up text-xs"></i>
                </button>
                <button type="button" 
                        @click="moveSlide(sIdx, sIdx + 1)" 
                        :disabled="sIdx === sliderImages.length - 1"
                        class="p-1.5 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition" 
                        title="Di chuyển xuống">
                  <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <button type="button" 
                        @click="removeSlide(sIdx)" 
                        class="p-1.5 text-red-400 hover:text-red-600 transition" 
                        title="Xoá ảnh này">
                  <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
              </div>
            </div>
          </template>

          <template x-if="sliderImages.length === 0">
            <div class="p-8 text-center bg-white rounded-xl border border-dashed border-gray-300">
              <i class="fa-solid fa-images text-gray-300 text-3xl mb-2"></i>
              <p class="text-xs text-gray-500 font-medium">Chưa có ảnh nào trong slider.</p>
              <button type="button" @click="resetDefaultSlides()" class="mt-3 text-xs text-[#EC460B] hover:underline font-bold">
                Khôi phục ảnh mẫu mặc định
              </button>
            </div>
          </template>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button type="button" @click="resetDefaultSlides()" class="text-xs text-gray-500 hover:text-gray-800 underline">
            Khôi phục lại 4 ảnh gốc ban đầu
          </button>
          <span class="text-xs text-gray-400">Thời gian tự chuyển: 3.5 giây</span>
        </div>
      </div>

      <!-- SECTION 3: Trường Form & Dịch Vụ -->
      <div x-show="activeSection === 'fields'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 pb-3">
          <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">3</span>
            Nhãn &amp; Trường Nhập Liệu (Cột Phải Form)
          </h3>
          <p class="text-xs text-gray-500 mt-1">Tuỳ chỉnh tiêu đề nhãn, placeholder và danh mục dịch vụ trong menu thả xuống.</p>
        </div>

        <!-- Row 1: Fullname & Phone -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Fullname -->
          <div class="bg-white p-3.5 rounded-xl border border-gray-200 space-y-2 shadow-xs">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">
              <label class="block text-xs font-bold text-gray-700 uppercase">Trường Họ &amp; Tên</label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="fullnameRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span class="font-medium text-[11px] text-gray-800">Bắt buộc nhập</span>
              </label>
            </div>
            <input type="hidden" name="contact_modal_fullname_required" :value="fullnameRequired ? '1' : '0'">
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Tiêu đề nhãn (Label):</span>
              <input type="text" name="contact_modal_fullname_label" x-model="fullnameLabel" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Gợi ý mờ (Placeholder):</span>
              <input type="text" name="contact_modal_fullname_placeholder" x-model="fullnamePlaceholder" placeholder="Để trống nếu không dùng" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Thông báo lỗi khi thiếu:</span>
              <input type="text" name="contact_modal_fullname_error" x-model="fullnameError" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs text-red-600 focus:ring-2 focus:ring-red-400">
            </div>
          </div>

          <!-- Phone -->
          <div class="bg-white p-3.5 rounded-xl border border-gray-200 space-y-2 shadow-xs">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">
              <label class="block text-xs font-bold text-gray-700 uppercase">Trường Số Điện Thoại</label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="phoneRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span class="font-medium text-[11px] text-gray-800">Bắt buộc nhập</span>
              </label>
            </div>
            <input type="hidden" name="contact_modal_phone_required" :value="phoneRequired ? '1' : '0'">
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Tiêu đề nhãn (Label):</span>
              <input type="text" name="contact_modal_phone_label" x-model="phoneLabel" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Gợi ý mờ (Placeholder):</span>
              <input type="text" name="contact_modal_phone_placeholder" x-model="phonePlaceholder" placeholder="Để trống nếu không dùng" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <p class="text-[11px] mt-2" :class="phoneRequired ? 'text-amber-600 font-medium' : 'text-gray-400'" x-text="phoneRequired ? 'Trường này là bắt buộc nhập trên frontend.' : 'Trường này là tuỳ chọn (không bắt buộc).'"></p>
          </div>
        </div>

        <!-- Row 2: Email & Service Label -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Email -->
          <div class="bg-white p-3.5 rounded-xl border border-gray-200 space-y-2 shadow-xs">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">
              <label class="block text-xs font-bold text-gray-700 uppercase">Trường Email</label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="emailRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span class="font-medium text-[11px] text-gray-800">Bắt buộc nhập</span>
              </label>
            </div>
            <input type="hidden" name="contact_modal_email_required" :value="emailRequired ? '1' : '0'">
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Tiêu đề nhãn (Label):</span>
              <input type="text" name="contact_modal_email_label" x-model="emailLabel" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Gợi ý mờ (Placeholder):</span>
              <input type="text" name="contact_modal_email_placeholder" x-model="emailPlaceholder" placeholder="Để trống nếu không dùng" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
          </div>

          <!-- Service Label -->
          <div class="bg-white p-3.5 rounded-xl border border-gray-200 space-y-2 shadow-xs">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">
              <label class="block text-xs font-bold text-gray-700 uppercase">Nhãn Mục Chọn Dịch Vụ</label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="serviceRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span class="font-medium text-[11px] text-gray-800">Bắt buộc chọn</span>
              </label>
            </div>
            <input type="hidden" name="contact_modal_service_required" :value="serviceRequired ? '1' : '0'">
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Tiêu đề nhãn (Label):</span>
              <input type="text" name="contact_modal_service_label" x-model="serviceLabel" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Danh sách các dịch vụ chọn bên dưới sẽ nằm trong dropdown này.</p>
          </div>
        </div>

        <!-- Services Dropdown Repeater -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 shadow-xs">
          <div class="flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 pb-2.5">
            <div>
              <label class="block text-xs font-bold text-gray-800 uppercase">
                Danh Sách Dịch Vụ Cần Tư Vấn (Dropdown Options)
              </label>
              <p class="text-[11px] text-gray-500">Khách hàng sẽ chọn 1 trong các dịch vụ này khi gửi yêu cầu.</p>
            </div>
            <button type="button" 
                    @click="addService()" 
                    class="px-3 py-1 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700 transition flex items-center gap-1.5 shadow-xs">
              <i class="fa-solid fa-plus"></i>
              <span>Thêm Dịch Vụ</span>
            </button>
          </div>

          <!-- Hidden JSON input -->
          <input type="hidden" name="contact_modal_services" :value="JSON.stringify(services)">

          <div class="space-y-2 pt-1">
            <template x-for="(service, svIdx) in services" :key="svIdx">
              <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-600 text-[10px] font-bold flex items-center justify-center shrink-0" x-text="svIdx + 1"></span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-1">
                  <div>
                    <input type="text" 
                           x-model="service.label" 
                           placeholder="Tên hiển thị (ví dụ: Sales & BD)" 
                           class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-xs font-medium focus:ring-2 focus:ring-[#EC460B]">
                  </div>
                  <div>
                    <input type="text" 
                           x-model="service.value" 
                           placeholder="Mã (value, ví dụ: sales-bd)" 
                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-md text-xs font-mono text-gray-500 focus:ring-2 focus:ring-[#EC460B]">
                  </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                  <button type="button" 
                          @click="moveService(svIdx, svIdx - 1)" 
                          :disabled="svIdx === 0"
                          class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-20 transition"
                          title="Lên trên">
                    <i class="fa-solid fa-chevron-up text-xs"></i>
                  </button>
                  <button type="button" 
                          @click="moveService(svIdx, svIdx + 1)" 
                          :disabled="svIdx === services.length - 1"
                          class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-20 transition"
                          title="Xuống dưới">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                  </button>
                  <button type="button" 
                          @click="removeService(svIdx)" 
                          class="p-1 text-red-400 hover:text-red-600 transition"
                          title="Xoá">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </button>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Message Field -->
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 space-y-2 shadow-xs">
          <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">
            <label class="block text-xs font-bold text-gray-700 uppercase">Trường Lời Nhắn / Nhu Cầu (Message / Inquiry)</label>
            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
              <input type="checkbox" x-model="messageRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
              <span class="font-medium text-[11px] text-gray-800">Bắt buộc nhập</span>
            </label>
          </div>
          <input type="hidden" name="contact_modal_message_required" :value="messageRequired ? '1' : '0'">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Tiêu đề nhãn (Label):</span>
              <input type="text" name="contact_modal_message_label" x-model="messageLabel" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <div>
              <span class="text-[11px] text-gray-500 block mb-0.5">Gợi ý mờ (Placeholder):</span>
              <input type="text" name="contact_modal_message_placeholder" x-model="messagePlaceholder" placeholder="Để trống nếu không dùng" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 4: Điều Khoản, Bản Tin & Nút Gửi -->
      <div x-show="activeSection === 'submit'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 pb-3">
          <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">4</span>
            Điều Khoản Bảo Mật, Bản Tin &amp; Nút Gửi
          </h3>
          <p class="text-xs text-gray-500 mt-1">Cấu hình các ô checkbox xác nhận, nút bấm gửi và thông báo hoàn thành.</p>
        </div>

        <!-- Privacy Checkbox -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 shadow-xs">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-gray-700 uppercase">
              1. Hộp Kiểm Đồng Ý Điều Khoản (Data &amp; Privacy Policy)
            </label>
            <div class="flex items-center gap-4">
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="privacyRequired" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span>Bắt buộc tick</span>
              </label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
                <input type="checkbox" x-model="privacyChecked" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                <span>Tick sẵn mặc định</span>
              </label>
            </div>
          </div>
          <input type="hidden" name="contact_modal_privacy_required" :value="privacyRequired ? '1' : '0'">
          <input type="hidden" name="contact_modal_privacy_checked" :value="privacyChecked ? '1' : '0'">

          <textarea name="contact_modal_privacy_text" 
                    x-model="privacyText" 
                    rows="2" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs leading-relaxed focus:ring-2 focus:ring-[#EC460B]"></textarea>
        </div>

        <!-- Newsletter Checkbox -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 shadow-xs">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-gray-700 uppercase">
              2. Hộp Kiểm Nhận Bản Tin (Newsletter Subscription)
            </label>
            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-gray-700">
              <input type="checkbox" x-model="newsletterChecked" class="rounded text-[#EC460B] focus:ring-[#EC460B]">
              <span>Tick sẵn mặc định</span>
            </label>
          </div>
          <input type="hidden" name="contact_modal_newsletter_checked" :value="newsletterChecked ? '1' : '0'">

          <input type="text" 
                 name="contact_modal_newsletter_text" 
                 x-model="newsletterText" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
        </div>

        <!-- Submit Button -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-2 shadow-xs">
          <label class="block text-xs font-bold text-gray-700 uppercase">
            Chữ Trên Nút Gửi Form (Submit Button Text)
          </label>
          <div class="flex items-center gap-3">
            <input type="text" 
                   name="contact_modal_submit_text" 
                   x-model="submitText" 
                   placeholder="LET'S CONNECT" 
                   class="w-full max-w-sm px-3 py-2 border border-gray-300 rounded-lg text-xs font-bold tracking-wider uppercase focus:ring-2 focus:ring-[#EC460B]">
            <div class="text-xs text-gray-500 flex items-center gap-1.5">
              <span>Biểu tượng kèm theo:</span>
              <span class="inline-flex items-center justify-center px-2 py-0.5 bg-gray-100 rounded text-gray-800 font-bold">&rarr; &rarr;</span>
            </div>
          </div>
        </div>

        <!-- Success Toast Message -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-2 shadow-xs">
          <label class="block text-xs font-bold text-gray-700 uppercase">
            Thông Báo Sau Khi Gửi Thành Công (Success Toast Alert)
          </label>
          <input type="text" 
                 name="contact_modal_success_msg" 
                 x-model="successMsg" 
                 placeholder="✓ Thank you! Your inquiry has been sent successfully..." 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs text-green-700 font-medium focus:ring-2 focus:ring-green-500">
        </div>
      </div>

      <!-- SECTION: Bảo Mật, Captcha & Chống Spam -->
      <div x-show="activeSection === 'security'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 pb-3">
          <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">
              <i class="fa-solid fa-shield-halved"></i>
            </span>
            Bảo Mật Form &amp; Chống Thư Rác (Anti-Spam / Captcha)
          </h3>
          <p class="text-xs text-gray-500 mt-1">Cấu hình Google reCAPTCHA và bộ lọc thông minh ngăn chặn bot spam giao dịch giả mạo, crypto và link lừa đảo.</p>
        </div>

        <!-- 1. Google reCAPTCHA v2 -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-4 shadow-xs">
          <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
            <div>
              <label class="block text-xs font-bold text-gray-800 uppercase">
                Xác Thực Google reCAPTCHA (v2 Checkbox)
              </label>
              <p class="text-[11px] text-gray-500 mt-0.5">Bắt buộc người dùng tích ô "Tôi không phải là người máy" trước khi gửi form.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" x-model="recaptchaEnabled" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#EC460B]"></div>
            </label>
            <input type="hidden" name="recaptcha_enabled" :value="recaptchaEnabled ? '1' : '0'">
          </div>

          <div class="space-y-3" x-show="recaptchaEnabled">
            <div>
              <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                Google reCAPTCHA Site Key (Khóa Công Khai):
              </label>
              <input type="text" 
                     name="recaptcha_site_key" 
                     x-model="recaptchaSiteKey" 
                     placeholder="Ví dụ: 6Ld... (Lấy tại google.com/recaptcha/admin)" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#EC460B]">
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                Google reCAPTCHA Secret Key (Khóa Bí Mật Server):
              </label>
              <input type="password" 
                     name="recaptcha_secret_key" 
                     x-model="recaptchaSecretKey" 
                     placeholder="Ví dụ: 6Ld..." 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#EC460B]">
            </div>

            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-[11px] text-amber-800 leading-relaxed">
              <i class="fa-solid fa-circle-info text-amber-600 mr-1"></i>
              Nếu bạn chưa có khóa, hãy truy cập <a href="https://www.google.com/recaptcha/admin" target="_blank" class="underline font-bold text-[#EC460B]">Google reCAPTCHA Admin</a>, tạo nhãn mới, chọn loại <strong>reCAPTCHA v2 ("I'm not a robot" Checkbox)</strong>, thêm tên miền website và dán 2 khóa vào đây.
            </div>
          </div>
        </div>

        <!-- 2. Bộ lọc Spam hệ thống -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 shadow-xs">
          <div class="border-b border-gray-100 pb-2">
            <h4 class="text-xs font-bold text-gray-800 uppercase flex items-center gap-1.5">
              <i class="fa-solid fa-shield text-[#EC460B]"></i>
              Bộ Lọc Spam Hệ Thống (Đã tắt - Chỉ dùng Google reCAPTCHA)
            </h4>
            <p class="text-[11px] text-gray-500 mt-0.5">Các lớp kiểm tra antispam ngầm (bẫy bot honeypot, giới hạn thời gian nhập, chặn link/từ khóa) đã được tắt để tránh chặn nhầm người dùng. Hệ thống hiện chỉ sử dụng đúng xác thực Google reCAPTCHA.</p>
          </div>

          <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
            <span>Chỉ duy nhất Google reCAPTCHA kiểm soát bảo mật và ngăn chặn bot tự động.</span>
          </div>
        </div>
      </div>

      <!-- SECTION 5: Thông Tin Liên Hệ (Contact Information) -->
      <div x-show="activeSection === 'contact_info'" class="space-y-4 bg-gray-50/70 p-5 rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 pb-3 flex items-start justify-between gap-4 flex-wrap">
          <div>
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
              <span class="w-6 h-6 rounded-md bg-orange-100 text-[#EC460B] flex items-center justify-center text-xs">5</span>
              Thông Tin Liên Hệ (Mobile Menu Drawer &amp; Footer)
            </h3>
            <p class="text-xs text-gray-500 mt-1">Cấu hình tiêu đề, email, số điện thoại và địa chỉ liên hệ hiển thị ở menu drawer và footer của website.</p>
          </div>

          <!-- Quick Action: Pour/Sync from Footer Widget -->
          <button type="button" 
                  @click="syncFromFooterWidget()"
                  class="inline-flex items-center gap-2 px-3.5 py-2 bg-gradient-to-r from-[#EC460B] to-[#d63b04] hover:from-[#d63b04] hover:to-[#bc3201] text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition transform active:scale-95 cursor-pointer">
            <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
            <span>Lấy dữ liệu từ Footer Widget</span>
          </button>
        </div>

        @if(!empty($footerPhone) || !empty($footerEmail) || !empty($footerAddress))
        <div class="bg-gradient-to-r from-orange-50 to-amber-50/60 border border-orange-200/80 rounded-xl p-3 text-xs text-gray-700 flex items-center justify-between gap-3">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Dữ liệu phát hiện từ <strong>Footer Section</strong>:</span>
            <span class="text-gray-600 text-[11px] font-mono">
              @if($footerPhone) P: <strong>{{ $footerPhone }}</strong> @endif
              @if($footerEmail) | E: <strong>{{ $footerEmail }}</strong> @endif
              @if($footerAddress) | A: <strong>{{ $footerAddress }}</strong> @endif
            </span>
          </div>
          <button type="button" 
                  @click="syncFromFooterWidget()" 
                  class="text-[#EC460B] hover:underline font-bold text-xs shrink-0 flex items-center gap-1 cursor-pointer">
            <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
            Đổ vào form ngay
          </button>
        </div>
        @endif

        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-4 shadow-xs">
          <!-- Title / Heading -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-gray-700 uppercase">Tiêu Đề Khối Liên Hệ (Section Label):</label>
              @if(!empty($footerContactTitle))
                <button type="button" @click="contactInfoTitle = @json($footerContactTitle)" class="text-[11px] text-[#EC460B] hover:underline cursor-pointer">
                  Lấy từ footer: "{{ $footerContactTitle }}"
                </button>
              @endif
            </div>
            <input type="text" name="contact_info_title" x-model="contactInfoTitle" placeholder="CONTACT INFORMATION" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            <p class="text-[11px] text-gray-400 mt-1">Mặc định: CONTACT INFORMATION</p>
          </div>

          <!-- Email -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-gray-700 uppercase">Địa Chỉ Email Liên Hệ:</label>
              @if(!empty($footerEmail))
                <button type="button" @click="contactEmail = @json($footerEmail)" class="text-[11px] text-[#EC460B] hover:underline cursor-pointer">
                  Lấy từ footer: "{{ $footerEmail }}"
                </button>
              @endif
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"><i class="fa-regular fa-envelope text-xs"></i></span>
              <input type="email" name="contact_email" x-model="contactEmail" placeholder="hello@inbetween.asia" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Mặc định: hello@inbetween.asia</p>
          </div>

          <!-- Phone -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-gray-700 uppercase">Số Điện Thoại / Hotline:</label>
              @if(!empty($footerPhone))
                <button type="button" @click="contactPhone = @json($footerPhone)" class="text-[11px] text-[#EC460B] hover:underline cursor-pointer">
                  Lấy từ footer: "{{ $footerPhone }}"
                </button>
              @endif
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"><i class="fa-solid fa-phone text-xs"></i></span>
              <input type="text" name="contact_phone" x-model="contactPhone" placeholder="+84 86 5651874" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Mặc định: +84 86 5651874</p>
          </div>

          <!-- Address -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-gray-700 uppercase">Địa Chỉ / Trụ Sở Văn Phòng:</label>
              @if(!empty($footerAddress))
                <button type="button" @click="contactAddress = @json($footerAddress)" class="text-[11px] text-[#EC460B] hover:underline cursor-pointer">
                  Lấy từ footer: "{{ $footerAddress }}"
                </button>
              @endif
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"><i class="fa-solid fa-location-dot text-xs"></i></span>
              <input type="text" name="contact_address" x-model="contactAddress" placeholder="Ho Chi Minh City, Vietnam" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#EC460B]">
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Mặc định: Ho Chi Minh City, Vietnam</p>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: Sticky Real-Time Live Preview -->
    <div class="xl:col-span-5 sticky top-6 space-y-3">
      <div class="flex items-center justify-between px-1">
        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
          Mô Phỏng Trực Tiếp (Live Preview)
        </span>
        <div class="flex items-center gap-1.5 bg-gray-200 p-0.5 rounded-lg text-[11px]">
          <button type="button" 
                  @click="previewDevice = 'desktop'"
                  :class="previewDevice === 'desktop' ? 'bg-white text-gray-900 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                  class="px-2 py-0.5 rounded transition">
            Desktop
          </button>
          <button type="button" 
                  @click="previewDevice = 'mobile'"
                  :class="previewDevice === 'mobile' ? 'bg-white text-gray-900 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                  class="px-2 py-0.5 rounded transition">
            Mobile
          </button>
        </div>
      </div>

      <!-- Live Mockup Box mimicking contact-modal-box -->
      <div class="bg-[#171b22] p-3 rounded-2xl border border-gray-800 shadow-xl overflow-hidden text-neutral-800">
        <!-- Window Mock Topbar -->
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 px-1 text-[11px] text-gray-400">
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-red-500/80 inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/80 inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-green-500/80 inline-block"></span>
            <span class="ml-2 text-gray-400 font-mono text-[10px]" x-text="previewDevice === 'desktop' ? '1280 × 800 (Desktop View)' : '390 × 844 (Mobile View)'"></span>
          </div>
          <span class="text-[10px] text-orange-400 font-semibold uppercase">Modal Interactive</span>
        </div>

        <!-- The Modal Container inside Preview -->
        <div class="bg-white rounded-xl p-4 sm:p-5 relative shadow-lg text-neutral-900 max-h-[82vh] overflow-y-auto"
             :class="previewDevice === 'mobile' ? 'max-w-[360px] mx-auto text-xs' : ''">

          <!-- Close button -->
          <div class="absolute top-3 right-3 flex items-center gap-1 text-[11px] text-neutral-500 font-medium cursor-pointer hover:text-neutral-900">
            <span class="w-3.5 h-[1.5px] bg-neutral-600 inline-block"></span>
            <span x-text="closeText || 'Close'"></span>
          </div>

          <!-- Modal Grid Layout -->
          <div :class="previewDevice === 'desktop' ? 'grid grid-cols-12 gap-5 items-stretch' : 'space-y-4'">

            <!-- Left Col (5 cols on desktop) -->
            <div :class="previewDevice === 'desktop' ? 'col-span-5 flex flex-col justify-between' : 'space-y-4'">
              <div>
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-3">
                  <img :src="resolveUrl(logo)" :alt="logoAlt" class="h-6 w-auto object-contain">
                </div>

                <!-- Headings -->
                <div class="space-y-1 mb-3">
                  <h3 class="text-base sm:text-lg font-bold text-[#131313] leading-tight tracking-normal uppercase font-sans" x-show="compiledTitleLine1 || compiledTitleLine2">
                    <span class="block" x-html="compiledTitleLine1" x-show="compiledTitleLine1"></span>
                    <span class="block" x-html="compiledTitleLine2" x-show="compiledTitleLine2"></span>
                  </h3>
                  <p class="text-[11px] sm:text-xs text-[#3E3939] font-light leading-relaxed font-sans" x-text="description" x-show="description"></p>
                </div>
              </div>

              <!-- Slider Preview Box -->
              <div class="mt-3 pt-2">
                <div class="relative w-[140px] sm:w-[170px] aspect-[297/165] rounded-lg overflow-hidden border border-neutral-300/80 shadow-xs bg-neutral-200">
                  <template x-if="sliderImages.length > 0">
                    <div class="w-full h-full relative">
                      <img :src="resolveUrl(sliderImages[previewSlideIdx]?.image || sliderImages[0]?.image)" 
                           class="w-full h-full object-cover transition-all duration-300" 
                           :alt="sliderImages[previewSlideIdx]?.alt || 'Slide'">
                      <!-- Slide Switcher Dots on Hover -->
                      <div class="absolute bottom-1 right-1 bg-black/60 backdrop-blur-xs px-1.5 py-0.5 rounded text-[9px] text-white font-mono flex items-center gap-1">
                        <button type="button" @click.stop="prevPreviewSlide()" class="hover:text-orange-300">&larr;</button>
                        <span x-text="(previewSlideIdx + 1) + '/' + sliderImages.length"></span>
                        <button type="button" @click.stop="nextPreviewSlide()" class="hover:text-orange-300">&rarr;</button>
                      </div>
                    </div>
                  </template>
                  <template x-if="sliderImages.length === 0">
                    <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">Không có ảnh</div>
                  </template>
                </div>
              </div>
            </div>

            <!-- Right Col (7 cols on desktop) -->
            <div :class="previewDevice === 'desktop' ? 'col-span-7 space-y-3' : 'space-y-3'">
              <!-- Row: Name & Phone -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                  <label class="block text-[10px] font-bold text-neutral-800 uppercase mb-1">
                    <span x-text="fullnameLabel || 'FULL NAME'"></span>
                    <span x-show="fullnameRequired" class="text-[#EC460B] ml-0.5 font-bold">*</span>
                  </label>
                  <input type="text" disabled :placeholder="fullnamePlaceholder" class="w-full px-2.5 py-1.5 border border-neutral-300 rounded text-xs bg-neutral-50/50">
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-neutral-800 uppercase mb-1">
                    <span x-text="phoneLabel || 'PHONE NUMBER'"></span>
                    <span x-show="phoneRequired" class="text-[#EC460B] ml-0.5 font-bold">*</span>
                  </label>
                  <input type="text" disabled :placeholder="phonePlaceholder" class="w-full px-2.5 py-1.5 border border-neutral-300 rounded text-xs bg-neutral-50/50">
                </div>
              </div>

              <!-- Row: Email & Service Dropdown -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                  <label class="block text-[10px] font-bold text-neutral-800 uppercase mb-1">
                    <span x-text="emailLabel || 'EMAIL'"></span>
                    <span x-show="emailRequired" class="text-[#EC460B] ml-0.5 font-bold">*</span>
                  </label>
                  <input type="text" disabled :placeholder="emailPlaceholder" class="w-full px-2.5 py-1.5 border border-neutral-300 rounded text-xs bg-neutral-50/50">
                </div>

                <!-- Interactive Custom Dropdown in Preview -->
                <div class="relative" @click.outside="previewDropdownOpen = false">
                  <label class="block text-[10px] font-bold text-neutral-800 uppercase mb-1">
                    <span x-text="serviceLabel || 'SERVICE NEEDED'"></span>
                    <span x-show="serviceRequired" class="text-[#EC460B] ml-0.5 font-bold">*</span>
                  </label>
                  <button type="button" 
                          @click="previewDropdownOpen = !previewDropdownOpen"
                          class="w-full px-2.5 py-1.5 border border-neutral-300 rounded text-xs bg-white text-left flex items-center justify-between hover:border-neutral-400 transition">
                    <span class="truncate font-medium text-neutral-800" x-text="previewSelectedServiceLabel()"></span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-neutral-500 transition-transform" :class="previewDropdownOpen ? 'rotate-180' : ''"></i>
                  </button>

                  <!-- Dropdown Menu -->
                  <div x-show="previewDropdownOpen" 
                       x-transition 
                       class="absolute left-0 right-0 top-full mt-1 bg-white border border-neutral-200 rounded-md shadow-lg z-30 max-h-40 overflow-y-auto py-1">
                    <template x-for="(s, idx) in services" :key="idx">
                      <div @click="previewSelectedService = s.value; previewDropdownOpen = false" 
                           class="px-2.5 py-1.5 text-xs cursor-pointer hover:bg-neutral-100 flex items-center justify-between"
                           :class="previewSelectedService === s.value ? 'bg-orange-50 text-[#EC460B] font-semibold' : 'text-neutral-700'">
                        <span x-text="s.label"></span>
                        <i x-show="previewSelectedService === s.value" class="fa-solid fa-check text-[10px]"></i>
                      </div>
                    </template>
                  </div>
                </div>
              </div>

              <!-- Message -->
              <div>
                <label class="block text-[10px] font-bold text-neutral-800 uppercase mb-1">
                  <span x-text="messageLabel || 'MESSAGE / INQUIRY'"></span>
                  <span x-show="messageRequired" class="text-[#EC460B] ml-0.5 font-bold">*</span>
                </label>
                <textarea disabled rows="2" :placeholder="messagePlaceholder" class="w-full px-2.5 py-1.5 border border-neutral-300 rounded text-xs bg-neutral-50/50 resize-none"></textarea>
              </div>

              <div class="w-full h-[1px] bg-neutral-200 my-1.5"></div>

              <!-- Checkboxes -->
              <div class="space-y-1.5 text-[11px] text-neutral-700">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                  <input type="checkbox" :checked="privacyChecked" class="mt-0.5 rounded text-[#EC460B] focus:ring-[#EC460B] shrink-0">
                  <span class="leading-tight" x-html="privacyText"></span>
                </label>
                <label class="flex items-start gap-2 cursor-pointer select-none">
                  <input type="checkbox" :checked="newsletterChecked" class="mt-0.5 rounded text-[#EC460B] focus:ring-[#EC460B] shrink-0">
                  <span class="leading-tight" x-text="newsletterText"></span>
                </label>
              </div>

              <!-- Google reCAPTCHA Preview -->
              <div x-show="recaptchaEnabled" class="pt-1.5 pb-0.5">
                <div class="border border-gray-300 rounded bg-gray-50/90 p-2 flex items-center justify-between text-xs max-w-[220px] shadow-xs">
                  <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" checked disabled class="rounded text-[#EC460B] focus:ring-[#EC460B]">
                    <span class="text-[10px] font-medium text-gray-700">Tôi không phải là robot</span>
                  </label>
                  <div class="flex flex-col items-center pl-1 border-l border-gray-200">
                    <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm1 14.5h-2v-2h2zm0-4h-2V7h2z"/>
                    </svg>
                    <span class="text-[7px] text-gray-400 font-mono">reCAPTCHA</span>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="pt-2">
                <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-5 py-2.5 bg-neutral-900 text-white rounded font-sans text-xs font-bold tracking-wider hover:bg-[#EC460B] transition-colors shadow-sm">
                  <span class="text-orange-400 font-mono font-bold tracking-tighter">&rarr; &rarr;</span>
                  <span x-text="submitText || 'LET\'S CONNECT'"></span>
                </button>
              </div>
            </div>

          </div>

        </div>
      </div>

      <!-- Preview Mobile Drawer Contact Info -->
      <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-4 text-white space-y-2 shadow-xs">
        <div class="flex items-center justify-between border-b border-neutral-800 pb-2">
          <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 flex items-center gap-1.5">
            <i class="fa-solid fa-address-book text-[#EC460B]"></i>
            Mô phỏng Mobile Drawer Contact
          </span>
          <span class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-800 text-orange-400 font-mono">Drawer Footer</span>
        </div>
        <div class="space-y-2 pt-1 font-sans">
          <div class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400" x-text="contactInfoTitle || 'CONTACT INFORMATION'"></div>
          <div class="space-y-1.5 text-xs text-neutral-200">
            <div class="flex items-center gap-2.5">
              <i class="fa-regular fa-envelope text-neutral-400 text-xs shrink-0 w-3.5"></i>
              <span class="truncate hover:text-[#EC460B] transition-colors" x-text="contactEmail || 'contact@inbetween.vn'"></span>
            </div>
            <div class="flex items-center gap-2.5">
              <i class="fa-solid fa-phone text-neutral-400 text-xs shrink-0 w-3.5"></i>
              <span class="truncate hover:text-[#EC460B] transition-colors" x-text="contactPhone || '+84 90 xxx xxxx'"></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-location-dot text-neutral-400 text-xs shrink-0 w-3.5 mt-0.5"></i>
              <span class="truncate" x-text="contactAddress || 'Ho Chi Minh City, Vietnam'"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Tips Card -->
      <div class="bg-orange-50/80 border border-orange-200/80 rounded-xl p-3 text-xs text-orange-950 space-y-1">
        <p class="font-bold flex items-center gap-1.5 text-[#EC460B]">
          <i class="fa-solid fa-lightbulb"></i>
          Gợi ý chỉnh sửa
        </p>
        <p class="text-[11px] text-orange-900/80 leading-relaxed">
          - Bấm <strong>Lưu cấu hình</strong> ở cuối trang để áp dụng mọi thay đổi vào website.<br>
          - Các ảnh slider, dịch vụ và thông tin liên hệ sẽ được đồng bộ ngay tức thì ngoài trang chủ.
        </p>
      </div>
    </div>

  </div>

</div>

<!-- Media Picker Integration Script -->
<script>
  function modalBoxManager() {
    return {
      activeSection: 'branding',
      previewDevice: 'desktop',

      // Data
      logo: @json($savedLogo),
      logoAlt: @json($savedLogoAlt),

      // Structured Title fields
      titleL1Prefix: @json($savedL1Prefix),
      titleL1Highlight: @json($savedL1Highlight),
      titleL1Suffix: @json($savedL1Suffix),
      titleL2Prefix: @json($savedL2Prefix),
      titleL2Highlight: @json($savedL2Highlight),
      titleL2Suffix: @json($savedL2Suffix),

      description: @json($savedDesc),
      closeText: @json($savedCloseText),

      sliderImages: @json($savedSliderImages),
      previewSlideIdx: 0,

      fullnameLabel: @json($savedFullnameLabel),
      fullnamePlaceholder: @json($savedFullnamePlaceholder),
      fullnameError: @json($savedFullnameError),
      fullnameRequired: @json($savedFullnameRequired),

      phoneLabel: @json($savedPhoneLabel),
      phonePlaceholder: @json($savedPhonePlaceholder),
      phoneRequired: @json($savedPhoneRequired),

      emailLabel: @json($savedEmailLabel),
      emailPlaceholder: @json($savedEmailPlaceholder),
      emailRequired: @json($savedEmailRequired),

      serviceLabel: @json($savedServiceLabel),
      services: @json($savedServices),
      serviceRequired: @json($savedServiceRequired),

      messageLabel: @json($savedMessageLabel),
      messagePlaceholder: @json($savedMessagePlaceholder),
      messageRequired: @json($savedMessageRequired),

      contactInfoTitle: @json($savedContactInfoTitle),
      contactEmail: @json($savedContactEmail),
      contactPhone: @json($savedContactPhone),
      contactAddress: @json($savedContactAddress),

      privacyText: @json($savedPrivacyText),
      privacyChecked: @json($savedPrivacyChecked),
      privacyRequired: @json($savedPrivacyRequired),

      newsletterText: @json($savedNewsletterText),
      newsletterChecked: @json($savedNewsletterChecked),

      submitText: @json($savedSubmitText),
      successMsg: @json($savedSuccessMsg),

      // Security & reCAPTCHA
      recaptchaEnabled: @json($savedRecaptchaEnabled),
      recaptchaSiteKey: @json($savedRecaptchaSiteKey),
      recaptchaSecretKey: @json($savedRecaptchaSecretKey),
      antispamBlockLinks: @json($savedAntispamBlockLinks),

      // Live Preview state
      previewDropdownOpen: false,
      previewSelectedService: 'sales-bd',
      targetMediaPickerField: null,
      targetMediaPickerSlideIdx: null,

      // Footer Widget Connected Data
      footerWidgetData: @json($footerWidgetData),

      syncFromFooterWidget() {
        if (!this.footerWidgetData) {
          alert('Không tìm thấy dữ liệu từ Footer Widget!');
          return;
        }
        let list = [];
        if (this.footerWidgetData.email) {
          this.contactEmail = this.footerWidgetData.email;
          list.push('• Email: ' + this.footerWidgetData.email);
        }
        if (this.footerWidgetData.phone) {
          this.contactPhone = this.footerWidgetData.phone;
          list.push('• SĐT: ' + this.footerWidgetData.phone);
        }
        if (this.footerWidgetData.address) {
          this.contactAddress = this.footerWidgetData.address;
          list.push('• Địa chỉ: ' + this.footerWidgetData.address);
        }
        if (this.footerWidgetData.title) {
          this.contactInfoTitle = this.footerWidgetData.title;
          list.push('• Tiêu đề: ' + this.footerWidgetData.title);
        }
        if (list.length > 0) {
          alert('✓ Đã đổ dữ liệu từ Footer Widget vào Modal Box thành công:\n\n' + list.join('\n'));
        } else {
          alert('Footer Widget chưa có dữ liệu liên hệ để đổ vào.');
        }
      },

      init() {
        if (this.services.length > 0) {
          this.previewSelectedService = this.services[0].value;
        }
      },

      get compiledTitleLine1() {
        const parts = [];
        if (this.titleL1Prefix && this.titleL1Prefix.trim()) parts.push(this.escapeHtml(this.titleL1Prefix.trim()));
        if (this.titleL1Highlight && this.titleL1Highlight.trim()) parts.push(`<span class="text-[#EC460B]">${this.escapeHtml(this.titleL1Highlight.trim())}</span>`);
        if (this.titleL1Suffix && this.titleL1Suffix.trim()) parts.push(this.escapeHtml(this.titleL1Suffix.trim()));
        return parts.join(' ');
      },

      get compiledTitleLine2() {
        const parts = [];
        if (this.titleL2Prefix && this.titleL2Prefix.trim()) parts.push(this.escapeHtml(this.titleL2Prefix.trim()));
        if (this.titleL2Highlight && this.titleL2Highlight.trim()) parts.push(`<span class="text-[#EC460B]">${this.escapeHtml(this.titleL2Highlight.trim())}</span>`);
        if (this.titleL2Suffix && this.titleL2Suffix.trim()) parts.push(this.escapeHtml(this.titleL2Suffix.trim()));
        return parts.join(' ');
      },

      get computedTitleLine1Plain() {
        const parts = [];
        if (this.titleL1Prefix && this.titleL1Prefix.trim()) parts.push(this.titleL1Prefix.trim());
        if (this.titleL1Highlight && this.titleL1Highlight.trim()) parts.push(this.titleL1Highlight.trim());
        if (this.titleL1Suffix && this.titleL1Suffix.trim()) parts.push(this.titleL1Suffix.trim());
        return parts.join(' ');
      },

      get computedTitleLine2Plain() {
        const parts = [];
        if (this.titleL2Prefix && this.titleL2Prefix.trim()) parts.push(this.titleL2Prefix.trim());
        if (this.titleL2Highlight && this.titleL2Highlight.trim()) parts.push(this.titleL2Highlight.trim());
        if (this.titleL2Suffix && this.titleL2Suffix.trim()) parts.push(this.titleL2Suffix.trim());
        return parts.join(' ');
      },

      escapeHtml(text) {
        if (!text) return '';
        const map = {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
      },

      resolveUrl(url) {
        if (!url) return '';
        if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('/') || url.startsWith('data:')) {
          return url;
        }
        return '/' + url;
      },

      togglePreviewMode() {
        this.previewDevice = this.previewDevice === 'desktop' ? 'mobile' : 'desktop';
      },

      previewSelectedServiceLabel() {
        const found = this.services.find(s => s.value === this.previewSelectedService);
        return found ? found.label : (this.services[0]?.label || 'Chọn dịch vụ');
      },

      nextPreviewSlide() {
        if (this.sliderImages.length === 0) return;
        this.previewSlideIdx = (this.previewSlideIdx + 1) % this.sliderImages.length;
      },

      prevPreviewSlide() {
        if (this.sliderImages.length === 0) return;
        this.previewSlideIdx = (this.previewSlideIdx - 1 + this.sliderImages.length) % this.sliderImages.length;
      },

      // Slider actions
      addSlide() {
        const newIdx = this.sliderImages.length;
        this.sliderImages.push({
          image: '',
          alt: ''
        });
        this.previewSlideIdx = newIdx;
        this.$nextTick(() => {
          this.openMediaPickerFor('slide', newIdx);
        });
      },

      removeSlide(index) {
        this.sliderImages.splice(index, 1);
        if (this.previewSlideIdx >= this.sliderImages.length) {
          this.previewSlideIdx = Math.max(0, this.sliderImages.length - 1);
        }
      },

      moveSlide(from, to) {
        if (to < 0 || to >= this.sliderImages.length) return;
        const item = this.sliderImages.splice(from, 1)[0];
        this.sliderImages.splice(to, 0, item);
      },

      resetDefaultSlides() {
        this.sliderImages = [
          { image: '/themes/inbetween_v2/images/founder-airu-portrait.png', alt: 'in • between - Founder AiRu' },
          { image: '/themes/inbetween_v2/images/client-asian-smes.png', alt: 'in • between - Asian SMEs' },
          { image: '/themes/inbetween_v2/images/client-founders.png', alt: 'in • between - Founders & Entrepreneurs' },
          { image: '/themes/inbetween_v2/images/client-regional-teams.png', alt: 'in • between - Regional Teams' }
        ];
        this.previewSlideIdx = 0;
      },

      // Services actions
      addService() {
        const newIdx = this.services.length + 1;
        this.services.push({
          value: 'service-' + newIdx,
          label: 'Dịch vụ ' + newIdx
        });
      },

      removeService(index) {
        this.services.splice(index, 1);
      },

      moveService(from, to) {
        if (to < 0 || to >= this.services.length) return;
        const item = this.services.splice(from, 1)[0];
        this.services.splice(to, 0, item);
      },

      // Media Picker & File Upload Integration (NO prompt)
      openMediaPickerFor(field, slideIdx = null) {
        this.targetMediaPickerField = field;
        this.targetMediaPickerSlideIdx = slideIdx;

        const targetFieldId = slideIdx !== null ? ('slide_' + slideIdx) : field;

        // 1. If global openMediaPicker helper exists
        if (typeof window.openMediaPicker === 'function') {
          window.openMediaPicker(null, (url) => {
            this.applyMediaSelected(field, slideIdx, url);
          }, false);
          return;
        }

        // 2. Dispatch Alpine event to open x-media-picker-modal
        window.dispatchEvent(new CustomEvent('open-media-picker', {
          detail: {
            field: targetFieldId,
            multiple: false,
            mediaType: 'image'
          }
        }));
      },

      applyMediaSelected(field, slideIdx, url) {
        if (!url) return;
        if (field === 'logo') {
          this.logo = url;
        } else if (field === 'slide' && slideIdx !== null) {
          if (this.sliderImages[slideIdx]) {
            this.sliderImages[slideIdx].image = url;
          }
        }
      },

      handleMediaSelected(detail) {
        if (!detail) return;
        const url = detail.urls || (detail.files && detail.files[0] ? (detail.files[0].url || detail.files[0].path) : '') || detail.value || '';
        if (!url) return;

        const field = detail.field || this.targetMediaPickerField;
        if (field === 'logo') {
          this.logo = url;
        } else if (typeof field === 'string' && field.startsWith('slide_')) {
          const idx = parseInt(field.replace('slide_', ''));
          if (this.sliderImages[idx]) {
            this.sliderImages[idx].image = url;
          }
        } else if (this.targetMediaPickerSlideIdx !== null && this.sliderImages[this.targetMediaPickerSlideIdx]) {
          this.sliderImages[this.targetMediaPickerSlideIdx].image = url;
        }
        this.targetMediaPickerField = null;
        this.targetMediaPickerSlideIdx = null;
      },

      handleLogoFileUpload(e) {
        if (e.target.files && e.target.files[0]) {
          this.logo = URL.createObjectURL(e.target.files[0]);
        }
      }
    };
  }
</script>
@endsection

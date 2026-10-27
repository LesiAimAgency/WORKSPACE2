<div class="contact-modal-overlay" id="contact-modal-overlay" role="dialog" aria-modal="true" aria-label="Bảng liên hệ trực tiếp Đại Phúc 68">
  <div class="contact-modal-box">
    <button class="contact-modal-close-btn" id="close-contact-modal-btn" type="button" aria-label="Đóng bảng liên hệ">
      <span class="close-line"></span><span>Close</span>
    </button>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
      <div class="lg:col-span-5 flex flex-col justify-between h-full min-h-[460px]">
        <div class="flex flex-col gap-5">
          <div class="flex items-center gap-3">
            <img class="h-10 sm:h-12 w-auto object-contain" src="{{ asset('themes/daiphuc68/assets/icons/logo-daiphuc68.svg') }}" alt="Logo Đại Phúc 68">
          </div>
          <div class="flex flex-col gap-2 pt-2">
            <h2 class="text-[26px] sm:text-[30px] lg:text-[34px] font-bold text-neutral-900 leading-[1.25] tracking-tight uppercase">
              KIẾN TẠO <span class="kiet-tac-text italic font-normal text-[28px] sm:text-[32px] lg:text-[36px] text-neutral-800 lowercase">kiệt tác</span><br>CÙNG ĐẠI PHÚC 68
            </h2>
            <p class="text-[13px] sm:text-[14px] text-neutral-600 font-normal leading-relaxed">Chúng tôi sẽ liên hệ cho bạn ngay khi nhận được thông tin.</p>
          </div>
        </div>
        <div class="pt-6 flex justify-start items-end">
          <div class="relative w-[190px] sm:w-[210px] md:w-[220px] aspect-[297/165] rounded-[8px] overflow-hidden border border-neutral-300/80 shadow-xs bg-neutral-200" id="modal-image-slider">
            <img class="modal-slide-img active absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out" src="{{ asset('storage/media/common/contact_overlay_map.png') }}" alt="Công trình Đại Phúc 68 - Bản đồ" loading="lazy">
            <img class="modal-slide-img absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 ease-in-out" src="{{ asset('storage/media/projects/project-gallery-1.jpg') }}" alt="Công trình Đại Phúc 68 - Dự án 1" loading="lazy">
            <img class="modal-slide-img absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 ease-in-out" src="{{ asset('storage/media/projects/project-gallery-2.jpg') }}" alt="Công trình Đại Phúc 68 - Dự án 2" loading="lazy">
            <img class="modal-slide-img absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 ease-in-out" src="{{ asset('storage/media/projects/project-gallery-3.jpg') }}" alt="Công trình Đại Phúc 68 - Dự án 3" loading="lazy">
          </div>
        </div>
      </div>
      <div class="lg:col-span-7 flex flex-col gap-5">
        <form class="flex flex-col gap-4" id="modal-contact-form" action="{{ route('contact.submit') }}" method="POST" novalidate>
          @csrf
          <input type="hidden" name="source" value="modal">
          <input type="hidden" name="page_url" id="modal-input-page-url" value="{{ url()->current() }}">
          <input type="hidden" name="product_title" id="modal-input-product-title" value="">
          <input type="hidden" name="product_sku" id="modal-input-product-sku" value="">
          <input type="hidden" name="product_variation" id="modal-input-product-variation" value="">
          <input type="hidden" name="product_price" id="modal-input-product-price" value="">
          <input type="hidden" name="product_quantity" id="modal-input-product-quantity" value="">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="block text-xs font-bold text-neutral-800 uppercase">HỌ VÀ TÊN *</label>
              <input class="contact-input" type="text" name="fullname" required placeholder="Họ và tên của bạn">
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="block text-xs font-bold text-neutral-800 uppercase">SỐ ĐIỆN THOẠI *</label>
              <input class="contact-input" type="tel" name="phone" required placeholder="Số điện thoại liên hệ">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="modal-row-email-service">
            <div id="modal-col-email" class="sm:col-span-1 flex flex-col gap-1.5">
              <label class="block text-xs font-bold text-neutral-800 uppercase">EMAIL</label>
              <input class="contact-input" type="email" name="email" placeholder="Địa chỉ email (tùy chọn)">
            </div>
            <div id="modal-col-service" class="sm:col-span-1 flex flex-col gap-1.5">
              <label class="block text-xs font-bold text-neutral-800 uppercase">DỊCH VỤ CẦN TƯ VẤN</label>
              <div class="contact-custom-dropdown relative">
                <input type="hidden" name="service" value="vat-tu-thiet-bi-dien" id="modal-service-input">
                <button class="dropdown-trigger" type="button" aria-haspopup="listbox" aria-expanded="false">
                  <span class="dropdown-selected">Vật tư thiết bị điện</span>
                  <span class="dropdown-arrow-wrapper"><span class="dropdown-separator"></span><span class="dropdown-chevron-icon"><i class="fa-solid fa-chevron-down text-xs"></i></span></span>
                </button>
                <ul class="dropdown-menu" role="listbox">
                  <li class="dropdown-item" role="option" data-value="bao-gia-san-pham"><span>Báo giá sản phẩm</span></li>
                  <li class="dropdown-item selected" role="option" data-value="vat-tu-thiet-bi-dien"><span>Vật tư thiết bị điện</span></li>
                  <li class="dropdown-item" role="option" data-value="cot-be-tong-ly-tam"><span>Cột bê tông ly tâm</span></li>
                  <li class="dropdown-item" role="option" data-value="thi-cong-xay-lap"><span>Thi công xây lắp điện</span></li>
                  <li class="dropdown-item" role="option" data-value="thiet-bi-nang-luong-mat-troi"><span>Năng lượng mặt trời</span></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="block text-xs font-bold text-neutral-800 uppercase">GHI CHÚ</label>
            <textarea class="contact-textarea" name="message" placeholder="Nội dung cần tư vấn hoặc khối lượng vật tư..." style="resize: none; height: 110px; min-height: 110px; max-height: 110px;"></textarea>
          </div>
          <div class="w-full h-[1px] bg-neutral-300/80"></div>
          <div class="flex flex-col gap-3 pt-1 text-[13px] text-neutral-700">
            <label class="flex items-center gap-3 cursor-pointer select-none">
              <input class="contact-checkbox" type="checkbox" name="privacy_consent" required checked>
              <span>Tôi đã đọc và hoàn toàn đồng ý với Chính sách dữ liệu của Đại Phúc 68</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer select-none">
              <input class="contact-checkbox" type="checkbox" name="newsletter">
              <span>Gửi email cập nhật báo giá và tin tức mới cho tôi</span>
            </label>
          </div>
          <div class="pt-2 flex items-center justify-between flex-wrap gap-4">
            <button class="contact-submit-btn" type="submit">
              <span class="submit-arrow-box"><span class="arrow-primary">&rarr;</span><span class="arrow-secondary">&rarr;</span></span>
              <span class="submit-btn-text">GỬI YÊU CẦU</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  window.openProductQuoteModal = function(data) {
    const modal = document.getElementById('contact-modal-overlay');
    if (!modal) return;

    data = data || {};

    // 1. Tự động lấy URL trang hiện tại đang đứng
    const currentUrl = window.location.href;
    const pageUrlInput = document.getElementById('modal-input-page-url');
    if (pageUrlInput) {
      pageUrlInput.value = currentUrl;
    }

    // 2. Giữ nguyên phần DỊCH VỤ CẦN TƯ VẤN (không ẩn)
    const serviceCol = document.getElementById('modal-col-service');
    const emailCol = document.getElementById('modal-col-email');
    if (serviceCol) serviceCol.classList.remove('hidden');
    if (emailCol) {
      emailCol.classList.remove('sm:col-span-2');
      emailCol.classList.add('sm:col-span-1');
    }

    // 3. Set hidden form inputs
    const inTitle = document.getElementById('modal-input-product-title');
    const inSku = document.getElementById('modal-input-product-sku');
    const inVar = document.getElementById('modal-input-product-variation');
    const inPrice = document.getElementById('modal-input-product-price');
    const inQty = document.getElementById('modal-input-product-quantity');
    const inService = document.getElementById('modal-service-input');
    const textarea = modal.querySelector('textarea[name="message"]');

    const targetService = data.service || 'bao-gia-san-pham';
    if (inTitle) inTitle.value = data.title || '';
    if (inSku) inSku.value = data.sku || '';
    if (inVar) inVar.value = data.variationText || '';
    if (inPrice) inPrice.value = data.price || '';
    if (inQty) inQty.value = data.quantity || 1;
    if (inService) inService.value = targetService;

    // Cập nhật giao diện dropdown dịch vụ trên modal
    const serviceDropdown = modal.querySelector('.contact-custom-dropdown');
    if (serviceDropdown) {
      const selectedSpan = serviceDropdown.querySelector('.dropdown-selected');
      const items = serviceDropdown.querySelectorAll('.dropdown-item');
      let targetText = 'Báo giá sản phẩm';

      items.forEach(function(item) {
        if (item.getAttribute('data-value') === targetService) {
          item.classList.add('selected');
          targetText = item.textContent.trim();
        } else {
          item.classList.remove('selected');
        }
      });
      if (selectedSpan) selectedSpan.textContent = targetText;
    }

    if (textarea) {
      let inquiry = `[Yêu cầu báo giá nhanh]\n`;
      inquiry += `Sản phẩm: ${data.title || ''}\n`;
      if (data.sku) inquiry += `- Mã sản phẩm (SKU): ${data.sku}\n`;
      if (data.variationText) inquiry += `- Biến thể: ${data.variationText}\n`;
      if (data.price) inquiry += `- Đơn giá: ${data.price}\n`;
      inquiry += `- Số lượng: ${data.quantity || 1}\n`;
      inquiry += `- Link trang: ${currentUrl}\n`;
      textarea.value = inquiry;
    }

    // 4. Update submit button text
    const submitText = modal.querySelector('.submit-btn-text');
    if (submitText) submitText.textContent = 'GỬI YÊU CẦU BÁO GIÁ';

    // 5. Open modal
    modal.classList.add('active');
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
  };

  window.openContactModal = function(customData = null) {
    if (customData && (customData.title || customData.productTitle)) {
      window.openProductQuoteModal(customData);
      return;
    }

    const modal = document.getElementById('contact-modal-overlay');
    if (!modal) return;

    window.resetContactModalToDefault();
    modal.classList.add('active');
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
  };

  window.closeContactModal = function() {
    const modal = document.getElementById('contact-modal-overlay');
    if (modal) {
      modal.classList.remove('active');
    }
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';

    if (typeof window.resetContactModalToDefault === 'function') {
      window.resetContactModalToDefault();
    }
  };

  window.resetContactModalToDefault = function() {
    const modal = document.getElementById('contact-modal-overlay');
    if (!modal) return;

    const pageUrlInput = document.getElementById('modal-input-page-url');
    if (pageUrlInput) pageUrlInput.value = window.location.href;

    const serviceCol = document.getElementById('modal-col-service');
    const emailCol = document.getElementById('modal-col-email');
    if (serviceCol) serviceCol.classList.remove('hidden');
    if (emailCol) {
      emailCol.classList.remove('sm:col-span-2');
      emailCol.classList.add('sm:col-span-1');
    }

    const inTitle = document.getElementById('modal-input-product-title');
    const inSku = document.getElementById('modal-input-product-sku');
    const inVar = document.getElementById('modal-input-product-variation');
    const inPrice = document.getElementById('modal-input-product-price');
    const inQty = document.getElementById('modal-input-product-quantity');
    const inService = document.getElementById('modal-service-input');
    const textarea = modal.querySelector('textarea[name="message"]');

    if (inTitle) inTitle.value = '';
    if (inSku) inSku.value = '';
    if (inVar) inVar.value = '';
    if (inPrice) inPrice.value = '';
    if (inQty) inQty.value = '';
    if (inService) inService.value = 'vat-tu-thiet-bi-dien';

    const serviceDropdown = modal.querySelector('.contact-custom-dropdown');
    if (serviceDropdown) {
      const selectedSpan = serviceDropdown.querySelector('.dropdown-selected');
      const items = serviceDropdown.querySelectorAll('.dropdown-item');
      items.forEach(function(item) {
        if (item.getAttribute('data-value') === 'vat-tu-thiet-bi-dien') {
          item.classList.add('selected');
        } else {
          item.classList.remove('selected');
        }
      });
      if (selectedSpan) selectedSpan.textContent = 'Vật tư thiết bị điện';
    }

    if (textarea && textarea.value.startsWith('[Yêu cầu báo giá')) {
      textarea.value = '';
    }

    const submitText = modal.querySelector('.submit-btn-text');
    if (submitText) submitText.textContent = 'GỬI YÊU CẦU';
  };

  document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('close-contact-modal-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.closeContactModal();
      });
    }

    // Close when clicking outside modal box
    const modalOverlay = document.getElementById('contact-modal-overlay');
    if (modalOverlay) {
      modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
          window.closeContactModal();
        }
      });
    }

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('contact-modal-overlay');
        if (modal && modal.classList.contains('active')) {
          window.closeContactModal();
        }
      }
    });

    // Custom Dropdown Handling inside Contact Modal
    const dropdown = document.querySelector('.contact-modal-overlay .contact-custom-dropdown');
    if (dropdown) {
      const trigger = dropdown.querySelector('.dropdown-trigger');
      const selectedSpan = dropdown.querySelector('.dropdown-selected');
      const hiddenInput = dropdown.querySelector('#modal-service-input');
      const items = dropdown.querySelectorAll('.dropdown-item');

      if (trigger) {
        trigger.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          dropdown.classList.toggle('active');
        });
      }

      items.forEach(function(item) {
        item.addEventListener('click', function(e) {
          e.stopPropagation();
          const val = item.getAttribute('data-value');
          const text = item.textContent.trim();
          if (hiddenInput) hiddenInput.value = val;
          if (selectedSpan) selectedSpan.textContent = text;
          items.forEach(i => i.classList.remove('selected'));
          item.classList.add('selected');
          dropdown.classList.remove('active');
        });
      });

      document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
          dropdown.classList.remove('active');
        }
      });
    }

    // Simple Auto-Rotation for Modal Left Slider
    const sliderContainer = document.getElementById('modal-image-slider');
    if (sliderContainer) {
      const slides = sliderContainer.querySelectorAll('.modal-slide-img');
      if (slides.length > 1) {
        let currentSlideIdx = 0;
        setInterval(function() {
          const modal = document.getElementById('contact-modal-overlay');
          if (!modal || !modal.classList.contains('active')) return;
          slides[currentSlideIdx].classList.remove('active');
          slides[currentSlideIdx].style.opacity = '0';
          currentSlideIdx = (currentSlideIdx + 1) % slides.length;
          slides[currentSlideIdx].classList.add('active');
          slides[currentSlideIdx].style.opacity = '1';
        }, 3500);
      }
    }

    // Global Click Delegation for Quote & Contact Triggers
    document.addEventListener('click', function(e) {
      // 1. Direct Quote Request from Product Card / Button
      const quoteTrigger = e.target.closest('[data-open-quote-modal], [data-product-quote]');
      if (quoteTrigger) {
        e.preventDefault();
        e.stopPropagation();
        const title = quoteTrigger.getAttribute('data-product-title') || '';
        const image = quoteTrigger.getAttribute('data-product-image') || '';
        const sku = quoteTrigger.getAttribute('data-product-sku') || '';
        const price = quoteTrigger.getAttribute('data-product-price') || '';
        const qty = quoteTrigger.getAttribute('data-product-quantity') || 1;
        window.openProductQuoteModal({
          title: title,
          image: image,
          sku: sku,
          price: price,
          quantity: qty,
          service: quoteTrigger.getAttribute('data-product-service') || 'bao-gia-san-pham'
        });
        return;
      }

      // 2. Contact Modal Trigger
      const contactTrigger = e.target.closest('[data-contact-modal-toggle], .nav-contact-link, [data-open-contact-modal]');
      if (contactTrigger) {
        e.preventDefault();
        e.stopPropagation();
        window.openContactModal();
        return;
      }
    });
  });
</script>

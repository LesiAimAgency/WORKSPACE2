@php
  $img1 = $image_1 ?? ($settings['image_1'] ?? ($settings['image_left'] ?? 'storage/media/banners/aboutus_villa_showcase.png'));
  $img2 = $image_2 ?? ($settings['image_2'] ?? ($settings['image_right'] ?? 'storage/media/projects/project-detail-hero.jpg'));
  $alt1 = $alt_1 ?? ($settings['alt_1'] ?? ($settings['alt_left'] ?? 'Dự án khu dân cư cao cấp'));
  $alt2 = $alt_2 ?? ($settings['alt_2'] ?? ($settings['alt_right'] ?? 'Thi công hoàn thiện biệt thự'));
@endphp

<section class="relative w-full overflow-hidden select-none box-border" id="about-gallery" aria-label="Hình Ảnh Công Trình Tiêu Biểu">
  <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-0">
    <div class="relative w-full aspect-[720/462] overflow-hidden group bg-neutral-900 reveal-item reveal-delay-1">
      <img class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
           src="{{ asset($img1) }}" 
           alt="{{ $alt1 }}" 
           loading="lazy"
           onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/aboutus_villa_showcase.png') }}';">
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
        <span class="text-white text-sm font-semibold drop-shadow">{{ $alt1 }}</span>
      </div>
    </div>
    <div class="relative w-full aspect-[720/462] overflow-hidden group bg-neutral-900 reveal-item reveal-delay-2">
      <img class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
           src="{{ asset($img2) }}" 
           alt="{{ $alt2 }}" 
           loading="lazy"
           onerror="this.onerror=null; this.src='{{ asset('storage/media/projects/project-detail-hero.jpg') }}';">
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
        <span class="text-white text-sm font-semibold drop-shadow">{{ $alt2 }}</span>
      </div>
    </div>
  </div>
</section>

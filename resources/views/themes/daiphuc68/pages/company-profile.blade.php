@extends('themes.daiphuc68.layouts.app')

@php
  $seoTitle = $title ?: 'Hồ Sơ Năng Lực - CÔNG TY TNHH ĐẠI PHÚC 68';
  $seoDesc = $description ?: 'Xem trực tuyến tài liệu hồ sơ năng lực, chứng chỉ xuất xưởng, năng lực thi công và cung cấp vật tư thiết bị điện chính hãng Đại Phúc 68.';

  // Xử lý mã nhúng Heyzine hoặc URL
  $embedContent = trim((string)($heyzineEmbed ?? ''));
  $heyzineUrl = null;
  $isRawIframe = false;

  if (!empty($embedContent)) {
    if (str_starts_with($embedContent, 'http')) {
      $heyzineUrl = $embedContent;
    } elseif (preg_match('/src=["\']([^"\']+)["\']/i', $embedContent, $matches)) {
      $heyzineUrl = $matches[1];
    } else {
      $isRawIframe = true;
    }
  }

  $pdfUrl = !empty($profilePdf) ? (str_starts_with($profilePdf, 'http') ? $profilePdf : asset(ltrim($profilePdf, '/'))) : asset('themes/daiphuc68/assets/ho-so-nang-luc.pdf');
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDesc)

@section('content')

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'TRANG CHỦ', 'url' => route('home')],
      ['label' => 'HỒ SƠ NĂNG LỰC', 'active' => true]
    ]
  ])

  {{-- Main Profile Content Section --}}
  <main class="w-full bg-[#F6F6F6] py-10 md:py-14 flex justify-center">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16 flex flex-col gap-8">

    

      {{-- Flipbook Viewer Container --}}
      <div id="heyzine-flipbook-container" class="bg-white rounded-2xl border border-neutral-200/90 shadow-sm overflow-hidden flex flex-col items-center justify-center min-h-[580px] sm:min-h-[720px] md:min-h-[1024px] relative">
        @if($heyzineUrl)
          {{-- Render Iframe Heyzine --}}
          <iframe 
            id="heyzine-iframe-viewer"
            src="{{ $heyzineUrl }}" 
            class="w-full h-[580px] sm:h-[720px] md:h-[820px] border-0" 
            allowfullscreen="allowfullscreen" 
            scrolling="no"
            loading="lazy"
            title="Hồ Sơ Năng Lực Đại Phúc 68 - Heyzine Flipbook" style="height: 70vh;">
          </iframe>
        @elseif($isRawIframe)
          {{-- Render Raw Iframe if provided --}}
          <div class="w-full h-[580px] sm:h-[720px] md:h-[820px] [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0">
            {!! $embedContent !!}
          </div>
        @else
          {{-- Fallback: PDF Preview or Placeholder when not configured --}}
          <div class="w-full h-full flex flex-col items-center justify-center p-8 text-center gap-5 my-auto">
            <div class="w-20 h-20 rounded-2xl bg-brand-navy/10 text-brand-navy flex items-center justify-center text-3xl">
              <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="max-w-md">
              <h3 class="text-xl font-bold text-neutral-900 mb-1">Tài Liệu Hồ Sơ Năng Lực Đại Phúc 68</h3>
              <p class="text-xs sm:text-sm text-neutral-500">
                Quý khách có thể xem và tải trực tiếp tài liệu PDF bên dưới hoặc liên hệ bộ phận kinh doanh để nhận hồ sơ năng lực bản in hoàn chỉnh.
              </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap justify-center pt-2">
              <a href="{{ $pdfUrl }}" 
                 target="_blank" 
                 class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-navy hover:bg-[#0c2370] text-white text-xs sm:text-sm font-bold uppercase tracking-wider transition-all shadow-md">
                <i class="fa-solid fa-file-pdf text-red-400"></i>
                <span>Mở File PDF Trực Tuyến</span>
              </a>
              <a href="{{ $pdfUrl }}" 
                 download="Ho-So-Nang-Luc-Dai-Phuc-68.pdf"
                 class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-neutral-50 text-neutral-800 border border-neutral-300 text-xs sm:text-sm font-semibold transition-all">
                <i class="fa-solid fa-download"></i>
                <span>Tải File Về Máy</span>
              </a>
            </div>
          </div>
        @endif
      </div>


    </div>
  </main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fsBtn = document.getElementById('btn-toggle-fullscreen-profile');
    const container = document.getElementById('heyzine-flipbook-container');
    const iframe = document.getElementById('heyzine-iframe-viewer');

    if (fsBtn && (iframe || container)) {
      fsBtn.addEventListener('click', function() {
        const target = iframe || container;
        if (!document.fullscreenElement) {
          if (target.requestFullscreen) {
            target.requestFullscreen();
          } else if (target.webkitRequestFullscreen) {
            target.webkitRequestFullscreen();
          } else if (target.msRequestFullscreen) {
            target.msRequestFullscreen();
          }
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          }
        }
      });
    }
  });
</script>
@endpush

@php
  $watermark = setting('watermark', []);
  $enabled = $watermark['enabled'] ?? false;
@endphp

@if($enabled)
<style>
/* Watermark container styles */
.watermark-container {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}

.watermark-container img {
  -webkit-user-drag: none;
  -khtml-user-drag: none;
  -moz-user-drag: none;
  -o-user-drag: none;
  user-drag: none;
}

/* Product images protection */
.product-image-protected {
  position: relative;
  user-select: none;
  -webkit-user-select: none;
}

.product-image-protected::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
  z-index: 5;
}

/* Disable image dragging globally for product images */
.product-gallery img,
.product-image img,
.product-thumbnail img {
  -webkit-user-drag: none;
  -khtml-user-drag: none;
  -moz-user-drag: none;
  -o-user-drag: none;
  user-drag: none;
  pointer-events: none;
}

/* Watermark overlay animation */
.watermark-overlay {
  animation: watermarkFadeIn 0.5s ease-in-out;
}

@keyframes watermarkFadeIn {
  from { opacity: 0; }
  to { opacity: var(--watermark-opacity, 0.8); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Disable right-click on product images
  document.querySelectorAll('.watermark-container, .product-image-protected, .product-gallery').forEach(function(el) {
    el.addEventListener('contextmenu', function(e) {
      e.preventDefault();
      return false;
    });
    
    el.addEventListener('dragstart', function(e) {
      e.preventDefault();
      return false;
    });
  });
  
  // Disable keyboard shortcuts for saving images
  document.addEventListener('keydown', function(e) {
    // Ctrl+S, Ctrl+Shift+S
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      // Only prevent if focused on image area
      if (e.target.closest('.watermark-container, .product-image-protected')) {
        e.preventDefault();
        return false;
      }
    }
  });
  
  // Console warning
  console.log('%c️ Hình ảnh được bảo vệ bản quyền', 'color: red; font-size: 20px; font-weight: bold;');
  console.log('%cViệc sao chép hình ảnh mà không có sự cho phép là vi phạm bản quyền.', 'color: orange; font-size: 14px;');
});
</script>
@endif

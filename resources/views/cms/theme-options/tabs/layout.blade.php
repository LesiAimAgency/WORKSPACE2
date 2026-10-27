@php
  $layouts = [
    'full-width' => [
      'label' => 'Full Width',
      'image' => '/images/layouts/layout-full.png',
      'description' => 'Nội dung full width không có sidebar'
    ],
    'full-width-banner' => [
      'label' => 'Full Width Banner',
      'image' => '/images/layouts/layout-full-banner.png',
      'description' => 'Full width với banner trên đầu'
    ],
    'sidebar-left' => [
      'label' => 'Sidebar Left',
      'image' => '/images/layouts/layout-sidebar-left.png',
      'description' => 'Sidebar bên trái, nội dung bên phải'
    ],
    'sidebar-left-1' => [
      'label' => 'Sidebar Left #1',
      'image' => '/images/layouts/layout-sidebar-left-banner-1.png',
      'description' => 'Sidebar trái với banner style 1'
    ],
    'sidebar-left-2' => [
      'label' => 'Sidebar Left #2',
      'image' => '/images/layouts/layout-sidebar-left-banner-2.png',
      'description' => 'Sidebar trái với banner style 2'
    ],
    'sidebar-right' => [
      'label' => 'Sidebar Right',
      'image' => '/images/layouts/layout-sidebar-right.png',
      'description' => 'Nội dung bên trái, sidebar bên phải'
    ],
    'sidebar-right-1' => [
      'label' => 'Sidebar Right #1',
      'image' => '/images/layouts/layout-sidebar-right-banner-1.png',
      'description' => 'Sidebar phải với banner style 1'
    ],
    'sidebar-right-2' => [
      'label' => 'Sidebar Right #2',
      'image' => '/images/layouts/layout-sidebar-right-banner-2.png',
      'description' => 'Sidebar phải với banner style 2'
    ]
  ];
@endphp

<div class="space-y-8">
  @include('cms.theme-options.tabs.layouts.page')

  @include('cms.theme-options.tabs.layouts.post')
  @include('cms.theme-options.tabs.layouts.post-category')

  @include('cms.theme-options.tabs.layouts.product')
  @include('cms.theme-options.tabs.layouts.product-category')
</div>

<script>
const layouts = @json($layouts);

document.addEventListener('DOMContentLoaded', function() {
  // Handle layout selection
  document.querySelectorAll('.layout-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      const name = this.name;
      const value = this.value;
      const layoutType = name.replace('_layout', '');
      
      // Update UI
      document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
        const label = r.closest('.layout-option');
        label.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
        label.classList.add('border-gray-200');
        const badge = label.querySelector('.absolute');
        if(badge && badge.textContent === '') badge.remove();
      });
      
      const label = this.closest('.layout-option');
      label.classList.remove('border-gray-200');
      label.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
      
      const badge = document.createElement('div');
      badge.className = 'absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
      badge.innerHTML = '';
      label.querySelector('.relative').appendChild(badge);
      

    });
  });
});
</script>

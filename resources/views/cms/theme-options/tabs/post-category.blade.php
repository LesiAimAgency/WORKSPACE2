@php
  $postLayouts = [
    'grid' => [
      'label' => 'Grid',
      'image' => '/images/layouts/post-grid.svg',
      'description' => 'Hiển thị bài viết dạng lưới đều'
    ],
    'classic' => [
      'label' => 'Classic',
      'image' => '/images/layouts/post-classic.svg',
      'description' => 'Hiển thị bài viết dạng danh sách cổ điển'
    ],
    'masonry' => [
      'label' => 'Masonry',
      'image' => '/images/layouts/post-masonry.svg',
      'description' => 'Hiển thị bài viết dạng gạch xếp chồng'
    ],
    'masonry-tiles' => [
      'label' => 'Masonry Tiles',
      'image' => '/images/layouts/post-masonry-tiles.svg',
      'description' => 'Hiển thị bài viết dạng ô vuông xếp chồng'
    ],
    'photo2' => [
      'label' => 'Photo 2',
      'image' => '/images/layouts/post-photo2.svg',
      'description' => 'Hiển thị bài viết tập trung vào hình ảnh'
    ]
  ];
@endphp

<div class="space-y-8">
  <!-- Post Category Layout -->
  <div>
    <h3 class="text-lg font-semibold mb-2">Post Category Layout</h3>
    <p class="text-sm text-gray-600 mb-4">Chọn kiểu hiển thị cho trang danh mục bài viết</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
      @php $selectedPostCategoryLayout = $data['post_category_style'] ?? 'grid'; @endphp
      @foreach($postLayouts as $key => $layout)
      <label class="layout-option block p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedPostCategoryLayout === $key ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200' }}">
        <input type="radio" name="post_category_style" value="{{ $key }}" 
            {{ $selectedPostCategoryLayout === $key ? 'checked' : '' }} class="hidden layout-radio">
        <div class="relative">
          @if($selectedPostCategoryLayout === $key)
          <div class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10"></div>
          @endif
          <img src="{{ asset($layout['image']) }}" alt="{{ $layout['label'] }}" class="w-full h-32 object-contain rounded mb-2 bg-white">
        </div>
        <span class="text-sm font-semibold text-center block mb-1">{{ $layout['label'] }}</span>
        <span class="text-xs text-gray-500 text-center block">{{ $layout['description'] }}</span>
      </label>
      @endforeach
    </div>
  </div>

  <!-- Posts Per Page -->
  <div>
    <h3 class="text-lg font-semibold mb-2">Posts Per Page</h3>
    <p class="text-sm text-gray-600 mb-4">Số bài viết hiển thị trên mỗi trang</p>
    <div class="max-w-xs">
      <input type="number" name="posts_per_page" value="{{ $data['posts_per_page'] ?? 12 }}" 
          min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <!-- Post Excerpt Length -->
  <div>
    <h3 class="text-lg font-semibold mb-2">Post Excerpt Length</h3>
    <p class="text-sm text-gray-600 mb-4">Số từ hiển thị trong đoạn trích bài viết</p>
    <div class="max-w-xs">
      <input type="number" name="post_excerpt_length" value="{{ $data['post_excerpt_length'] ?? 150 }}" 
          min="50" max="500" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <!-- Show Post Meta -->
  <div>
    <h3 class="text-lg font-semibold mb-2">Show Post Meta</h3>
    <p class="text-sm text-gray-600 mb-4">Hiển thị thông tin meta của bài viết</p>
    <div class="space-y-3">
      <label class="flex items-center">
        <input type="checkbox" name="show_post_date" value="1" 
            {{ ($data['show_post_date'] ?? true) ? 'checked' : '' }} 
            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span>Hiển thị ngày đăng</span>
      </label>
      <label class="flex items-center">
        <input type="checkbox" name="show_post_author" value="1" 
            {{ ($data['show_post_author'] ?? true) ? 'checked' : '' }} 
            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span>Hiển thị tác giả</span>
      </label>
      <label class="flex items-center">
        <input type="checkbox" name="show_post_category" value="1" 
            {{ ($data['show_post_category'] ?? true) ? 'checked' : '' }} 
            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span>Hiển thị danh mục</span>
      </label>
      <label class="flex items-center">
        <input type="checkbox" name="show_post_comments" value="1" 
            {{ ($data['show_post_comments'] ?? false) ? 'checked' : '' }} 
            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span>Hiển thị số bình luận</span>
      </label>
    </div>
  </div>

  <!-- Responsive Settings -->
  <div>
    <h3 class="text-lg font-semibold mb-2">Responsive Settings</h3>
    <p class="text-sm text-gray-600 mb-4">Hiển thị grid sản phẩm theo từng thiết bị</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Desktop -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
          </svg>
          Desktop
        </h4>
        <div class="grid grid-cols-5 gap-2">
          @for($i = 1; $i <= 5; $i++)
          <label class="flex flex-col items-center p-2 border rounded cursor-pointer hover:bg-blue-50 {{ ($data['desktop_columns'] ?? 4) == $i ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
            <input type="radio" name="desktop_columns" value="{{ $i }}" 
                {{ ($data['desktop_columns'] ?? 4) == $i ? 'checked' : '' }} class="hidden">
            <span class="text-lg font-bold">{{ $i }}</span>
          </label>
          @endfor
        </div>
      </div>

      <!-- Tablet -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4z" clip-rule="evenodd"></path>
          </svg>
          Tablet
        </h4>
        <div class="grid grid-cols-5 gap-2">
          @for($i = 1; $i <= 5; $i++)
          <label class="flex flex-col items-center p-2 border rounded cursor-pointer hover:bg-blue-50 {{ ($data['tablet_columns'] ?? 3) == $i ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
            <input type="radio" name="tablet_columns" value="{{ $i }}" 
                {{ ($data['tablet_columns'] ?? 3) == $i ? 'checked' : '' }} class="hidden">
            <span class="text-lg font-bold">{{ $i }}</span>
          </label>
          @endfor
        </div>
      </div>

      <!-- Mobile -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zM6 4a1 1 0 011-1h6a1 1 0 011 1v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4z" clip-rule="evenodd"></path>
          </svg>
          Mobile
        </h4>
        <div class="grid grid-cols-5 gap-2">
          @for($i = 1; $i <= 5; $i++)
          <label class="flex flex-col items-center p-2 border rounded cursor-pointer hover:bg-blue-50 {{ ($data['mobile_columns'] ?? 2) == $i ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
            <input type="radio" name="mobile_columns" value="{{ $i }}" 
                {{ ($data['mobile_columns'] ?? 2) == $i ? 'checked' : '' }} class="hidden">
            <span class="text-lg font-bold">{{ $i }}</span>
          </label>
          @endfor
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle layout selection
  document.querySelectorAll('.layout-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      const name = this.name;
      const value = this.value;
      
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

  // Handle responsive settings
  document.querySelectorAll('input[name="desktop_columns"], input[name="tablet_columns"], input[name="mobile_columns"]').forEach(radio => {
    radio.addEventListener('change', function() {
      const name = this.name;
      
      // Update UI for responsive settings
      document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
        const label = r.closest('label');
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-300');
      });
      
      const label = this.closest('label');
      label.classList.remove('border-gray-300');
      label.classList.add('border-blue-500', 'bg-blue-50');
    });
  });
});
</script>

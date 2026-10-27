@php
  $navigationStyles = [
    'style-1' => ['label' => 'Navigation Style 1', 'image' => '/images/navigation/navigation-style-1.png'],
    'style-2' => ['label' => 'Navigation Style 2', 'image' => '/images/navigation/navigation-style-2.png'],
    'style-3' => ['label' => 'Navigation Style 3', 'image' => '/images/navigation/navigation-style-3.png'],
    'style-4' => ['label' => 'Navigation Style 4', 'image' => '/images/navigation/navigation-style-4.png'],
    'style-5' => ['label' => 'Navigation Style 5', 'image' => '/images/navigation/navigation-style-5.png'],
  ];
@endphp

<div class="bg-white border rounded-lg p-6 shadow-sm">
  <div class="flex justify-between items-center mb-4 border-b pb-3">
    <h3 class="text-lg font-semibold text-gray-800">Navigation Styles</h3>
    <button onclick="downloadAllNavigations()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
      </svg>
      Tải tất cả về
    </button>
  </div>
  
  <div class="grid grid-cols-1 gap-4">
    @php $selectedNavigation = $data['navigation_style'] ?? ''; @endphp
    @foreach($navigationStyles as $key => $navigation)
    <label class="flex items-center gap-4 p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer relative {{ $selectedNavigation == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
      <input type="radio" name="navigation_style" value="{{ $key }}" 
          {{ $selectedNavigation == $key ? 'checked' : '' }} class="hidden">
      @if($selectedNavigation == $key)
      <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10"></div>
      @endif
      <div class="flex-shrink-0 w-3/4 h-20 bg-gray-50 rounded overflow-hidden">
        <img src="{{ asset($navigation['image']) }}" alt="{{ $navigation['label'] }}" loading="lazy" data-cache-img class="w-full h-auto object-cover object-top">
      </div>
      <div class="flex-1 flex items-center justify-between gap-2">
        <h4 class="text-xs font-semibold text-gray-800">{{ $navigation['label'] }}</h4>
        <button type="button" onclick="event.stopPropagation(); downloadNavigation('{{ asset($navigation['image']) }}', '{{ $key }}')" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-all flex items-center gap-1 whitespace-nowrap">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          Tải
        </button>
      </div>
    </label>
    @endforeach
  </div>
</div>

<script>
function downloadNavigation(url, filename) {
  fetch(url)
    .then(response => response.blob())
    .then(blob => {
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename + '.png';
      link.click();
    });
}

function downloadAllNavigations() {
  const navigations = @json(array_values($navigationStyles));
  navigations.forEach((navigation, index) => {
    setTimeout(() => {
      const filename = Object.keys(@json($navigationStyles))[index];
      downloadNavigation(navigation.image, filename);
    }, index * 500);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('input[type="radio"][name="navigation_style"]').forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('input[name="navigation_style"]').forEach(r => {
        const label = r.closest('label');
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
        const badge = label.querySelector('[data-badge]');
        if(badge) badge.remove();
      });
      
      const label = this.closest('label');
      label.classList.remove('border-gray-200');
      label.classList.add('border-blue-500', 'bg-blue-50');
      
      if(!label.querySelector('.bg-blue-600')) {
        const badge = document.createElement('div');
        badge.className = 'absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
        badge.innerHTML = '';
        badge.setAttribute('data-badge', 'true');
        label.insertBefore(badge, label.firstChild);
      }
    });
  });
});
</script>

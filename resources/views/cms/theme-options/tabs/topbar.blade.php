@php
  $topbarStyles = [
    'style-1' => ['label' => 'Top Bar Style 1', 'image' => '/images/top-bar/top-bar-style-1.png'],
    'style-2' => ['label' => 'Top Bar Style 2', 'image' => '/images/top-bar/top-bar-style-2.png'],
    'style-3' => ['label' => 'Top Bar Style 3', 'image' => '/images/top-bar/top-bar-style-3.png'],
    'style-4' => ['label' => 'Top Bar Style 4', 'image' => '/images/top-bar/top-bar-style-4.png'],
    'style-5' => ['label' => 'Top Bar Style 5', 'image' => '/images/top-bar/top-bar-style-5.png'],
    'style-6' => ['label' => 'Top Bar Style 6', 'image' => '/images/top-bar/top-bar-style-6.png'],
  ];
@endphp

<div class="bg-white border rounded-lg p-6 shadow-sm">
  <div class="mb-4 border-b pb-3">
    <h3 class="text-lg font-semibold text-gray-800">Top Bar Styles</h3>
    <p class="text-sm text-gray-600 mt-1">Chọn style topbar và nhấn Lưu để áp dụng</p>
  </div>
  
  <div class="grid grid-cols-1 gap-4">
    @php $selectedTopbar = $data['topbar_style'] ?? setting('topbar_style', 'style-1'); @endphp
    @foreach($topbarStyles as $key => $topbar)
    <label class="flex items-center gap-4 p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer relative {{ $selectedTopbar == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" data-style="{{ $key }}">
      <input type="radio" name="topbar_style" value="{{ $key }}" 
          {{ $selectedTopbar == $key ? 'checked' : '' }} class="hidden" id="topbar_{{ $key }}">
      @if($selectedTopbar == $key)
      <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10"></div>
      @endif
      <div class="flex-shrink-0 w-3/4 h-20 bg-gray-50 rounded overflow-hidden">
        <img src="{{ asset($topbar['image']) }}" alt="{{ $topbar['label'] }}" class="w-full h-auto object-cover object-top">
      </div>
      <div class="flex-1">
        <h4 class="text-sm font-semibold text-gray-800">{{ $topbar['label'] }}</h4>
      </div>
    </label>
    @endforeach
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle label clicks
  document.querySelectorAll('label[data-style]').forEach(label => {
    label.addEventListener('click', function(e) {
      const radio = this.querySelector('input[type="radio"]');
      if (radio && !radio.checked) {
        radio.checked = true;
        updateSelection(radio);
      }
    });
  });
  
  function updateSelection(selectedRadio) {
    // Remove selection from all
    document.querySelectorAll('input[name="topbar_style"]').forEach(r => {
      const label = r.closest('label');
      label.classList.remove('border-blue-500', 'bg-blue-50');
      label.classList.add('border-gray-200');
      const badge = label.querySelector('.absolute.top-2');
      if(badge) badge.remove();
    });
    
    // Add selection to clicked
    const label = selectedRadio.closest('label');
    label.classList.remove('border-gray-200');
    label.classList.add('border-blue-500', 'bg-blue-50');
    
    // Add checkmark
    const badge = document.createElement('div');
    badge.className = 'absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
    badge.innerHTML = '';
    label.insertBefore(badge, label.firstChild);
  }
});
</script>

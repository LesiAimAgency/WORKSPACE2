@extends('cms.layouts.app')

@section('title', 'Quản lý Font')
@section('page-title', 'Quản lý Font chữ')

@section('content')
<div class="mb-6">
  <div class="flex justify-between items-center mb-4">
    @php
      $backUrl = route('admin.settings.index');
      $fonts = $fonts ?? (setting('fonts') ? (is_array(setting('fonts')) ? setting('fonts') : json_decode(setting('fonts'), true) ?? []) : []);
    @endphp
    <a href="{{ $backUrl }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Thêm Font</button>
  </div>

  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Font Key</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Font Type</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Font Label</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Font Load</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mặc định</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($fonts as $font)
        <tr>
          <td class="px-6 py-4">{{ $font['key'] ?? '' }}</td>
          <td class="px-6 py-4">
            <span class="px-2 py-1 text-xs rounded {{ $font['type'] === 'google' ? 'bg-blue-100 text-blue-800' : 'bg-blue-100 text-blue-800' }}">
              {{ ucfirst($font['type']) }}
            </span>
          </td>
          <td class="px-6 py-4">{{ $font['label'] ?? '' }}</td>
          <td class="px-6 py-4">{{ $font['load'] ?? '' }}</td>
          <td class="px-6 py-4">
            <form action="{{ url('/admin/settings/fonts/toggle') }}" method="POST" style="display:inline">
              @csrf
              <input type="hidden" name="id" value="{{ $font['id'] ?? '' }}">
              <button type="submit" class="px-3 py-1 text-xs rounded {{ !empty($font['is_active']) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ !empty($font['is_active']) ? 'Bật' : 'Tắt' }}
              </button>
            </form>
          </td>
          <td class="px-6 py-4">
            <form action="{{ url('/admin/settings/fonts/default') }}" method="POST" style="display:inline">
              @csrf
              <input type="hidden" name="id" value="{{ $font['id'] ?? '' }}">
              <button type="submit" class="px-3 py-1 text-xs rounded {{ !empty($font['is_default']) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                {{ !empty($font['is_default']) ? '✓' : '-' }}
              </button>
            </form>
          </td>
          <td class="px-6 py-4 text-right">
            <form action="{{ url('/admin/settings/fonts/destroy') }}" method="POST" style="display:inline" onsubmit="return confirm('Xác nhận ?')">
              @csrf @method('DELETE')
              <input type="hidden" name="id" value="{{ $font['id'] ?? '' }}">
              <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Xóa</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-6 py-8 text-center text-gray-500">Chưa có font nào</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
      <h3 class="text-lg font-semibold mb-4">Thêm Font mới</h3>

      <form action="{{ url('/admin/settings/fonts/store') }}" method="POST" id="fontForm">
        @csrf
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Font Type</label>
            <select name="type" id="fontType" required class="w-full px-4 py-2 border rounded-lg" onchange="toggleFontKey()">
              <option value="google">Google Font</option>
              <option value="custom">Custom Font</option>
            </select>
          </div>

          <div id="googleFontKey">
            <label class="block text-sm font-medium mb-1">Font Key</label>
            <select name="key" id="fontKey" required class="w-full px-4 py-2 border rounded-lg" onchange="autoFill()">
              <option value="">-- Chọn font --</option>
            </select>
          </div>

          <div id="customFontKey" style="display:none">
            <label class="block text-sm font-medium mb-1">Font Key</label>
            <input type="text" name="key_custom" placeholder="vd: my-font" class="w-full px-4 py-2 border rounded-lg">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Font Label</label>
            <input type="text" name="label" id="fontLabel" required placeholder="vd: Roboto" class="w-full px-4 py-2 border rounded-lg">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Font Load</label>
            <input type="text" name="load" id="fontLoad" required placeholder="vd: Roboto:300,400,500,700" class="w-full px-4 py-2 border rounded-lg">
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Thêm Font</button>
        </div>
      </form>

      <script>
let fonts = [];

// Popular fonts
const popularFonts = [
  'Roboto', 'Arsenal', 'Montserrat', 'Times New Roman'
];

// Load Google Fonts
fetch('/admin/fonts/google')
  .then(r => r.json())
  .then(data => {
    fonts = data;
    const select = document.getElementById('fontKey');
    select.innerHTML = '<option value="">-- Chọn font --</option>';
    
    // Add popular fonts first
    const popular = document.createElement('optgroup');
    popular.label = 'Font phổ biến';
    popularFonts.forEach(name => {
      const option = document.createElement('option');
      option.value = name;
      option.textContent = name;
      popular.appendChild(option);
    });
    select.appendChild(popular);
    
    // Add all fonts
    const all = document.createElement('optgroup');
    all.label = 'Tất cả font';
    data.forEach(font => {
      const option = document.createElement('option');
      option.value = font.family;
      option.textContent = font.family;
      all.appendChild(option);
    });
    select.appendChild(all);
  });

function toggleFontKey() {
  const type = document.getElementById('fontType').value;
  const google = document.getElementById('googleFontKey');
  const custom = document.getElementById('customFontKey');
  
  if (type === 'google') {
    google.style.display = 'block';
    custom.style.display = 'none';
    document.getElementById('fontKey').required = true;
    document.querySelector('[name="key_custom"]').required = false;
    document.querySelector('[name="key_custom"]').removeAttribute('name');
    document.getElementById('fontKey').setAttribute('name', 'key');
  } else {
    google.style.display = 'none';
    custom.style.display = 'block';
    document.getElementById('fontKey').required = false;
    document.getElementById('fontKey').removeAttribute('name');
    document.querySelector('input[placeholder="vd: my-font"]').setAttribute('name', 'key');
    document.querySelector('input[placeholder="vd: my-font"]').required = true;
  }
}

function autoFill() {
  const key = document.getElementById('fontKey').value;
  const font = fonts.find(f => f.family === key);
  if (font) {
    document.getElementById('fontLabel').value = font.family;
    document.getElementById('fontLoad').value = font.family + ':300,400,500,600,700';
  }
}


      </script>
    </div>
  </div>
</div>
@endsection

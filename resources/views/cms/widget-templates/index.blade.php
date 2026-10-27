@extends('cms.layouts.app')

@section('title', 'Widget Templates')
@section('page-title', 'Quản lý Widget Templates')

@section('content')
<div class="mb-6 flex justify-between items-center">
  <div>
    <h1 class="text-2xl font-bold text-gray-900">Widget Templates</h1>
    <p class="text-gray-600 mt-1">Quản lý các loại widget và cấu hình fields</p>
  </div>
  <div class="flex items-center gap-3">
    <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" 
        class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
      Import
    </button>
    
    @if(!empty($dbTemplates))
    <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.export-all', $currentProject->code) : route('cms.widget-templates.export-all') }}" 
      class="px-3 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 text-sm">
      Export All
    </a>
    @endif
    
    <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.create', $currentProject->code) : route('cms.widget-templates.create') }}" 
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Tạo Widget
    </a>
  </div>
</div>

@if (session()->has('success'))
  <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
    {{ session('success') }}
  </div>
@endif

@if (session()->has('error'))
  <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
    {{ session('error') }}
  </div>
@endif

{{-- Custom Widget Templates --}}
@if(!empty($dbTemplates))
<div class="bg-white rounded-lg shadow-sm mb-6">
  <div class="p-4 border-b bg-gradient-to-r from-blue-50 to-blue-50 flex justify-between items-center">
    <div>
      <h2 class="text-lg font-bold text-gray-900">Custom Widget Templates</h2>
      <p class="text-gray-600 text-sm">Widget tự tạo</p>
    </div>
    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
      {{ array_sum(array_map('count', $dbTemplates)) }} widgets
    </span>
  </div>

  <div class="p-4">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
      @foreach($dbTemplates as $category => $templates)
        @foreach($templates as $template)
        <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md hover:border-blue-300 transition group relative">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="font-medium text-gray-900 text-sm truncate" title="{{ $template['name'] }}">{{ $template['name'] }}</h4>
              <p class="text-xs text-gray-500 truncate">{{ $template['type'] }}</p>
            </div>
          </div>
          
          <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
            <span class="px-1.5 py-0.5 bg-gray-100 rounded">{{ ucfirst($category) }}</span>
            <span>{{ count($template['config_schema']['fields'] ?? []) }} fields</span>
          </div>
          
          <div class="flex gap-1">
            <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.edit', [$currentProject->code, $template['id']]) : route('cms.widget-templates.edit', $template['id']) }}" 
              class="flex-1 text-center px-2 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
              Sửa
            </a>
            <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.export', [$currentProject->code, $template['id']]) : route('cms.widget-templates.export', $template['id']) }}" 
              class="px-2 py-1.5 border border-gray-300 text-gray-600 rounded text-xs hover:bg-gray-50" title="Export">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
            </a>
            <form action="{{ isset($currentProject) ? route('project.admin.widget-templates.destroy', [$currentProject->code, $template['id']]) : route('cms.widget-templates.destroy', $template['id']) }}" 
               method="POST" onsubmit="return confirm('Xóa widget này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="px-2 py-1.5 border border-red-300 text-red-600 rounded text-xs hover:bg-red-50" title="Xóa">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </form>
          </div>
        </div>
        @endforeach
      @endforeach
    </div>
  </div>
</div>
@else
<div class="bg-white rounded-lg shadow-sm mb-6 p-8 text-center">
  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
    </svg>
  </div>
  <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có Custom Widget</h3>
  <p class="text-gray-500 mb-4">Tạo widget template đầu tiên của bạn</p>
  <a href="{{ isset($currentProject) ? route('project.admin.widget-templates.create', $currentProject->code) : route('cms.widget-templates.create') }}" 
    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Tạo Widget Template
  </a>
</div>
@endif

{{-- Code-based Widgets --}}
@if(!empty($codeWidgets))
<div class="bg-white rounded-lg shadow-sm">
  <div class="p-4 border-b bg-gradient-to-r from-blue-50 to-cyan-50 flex justify-between items-center">
    <div>
      <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
        </svg>
        Code-based Widgets
      </h2>
      <p class="text-gray-600 text-sm">Widgets định nghĩa trong code (app/Widgets)</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ isset($currentProject) ? route('project.admin.code-widgets.export-all', $currentProject->code) : route('cms.code-widgets.export-all') }}" 
        class="px-3 py-1.5 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 text-sm">
        Export All
      </a>
      <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
        {{ array_sum(array_map('count', $codeWidgets)) }} widgets
      </span>
    </div>
  </div>

  <div class="p-4">
    @foreach($codeWidgets as $category => $categoryWidgets)
    <div class="mb-6 last:mb-0">
      <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
        {{ ucfirst($category) }} ({{ count($categoryWidgets) }})
      </h3>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
        @foreach($categoryWidgets as $widget)
        <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md hover:border-blue-300 transition group relative">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="font-medium text-gray-900 text-sm truncate" title="{{ $widget['metadata']['name'] ?? $widget['type'] }}">
                {{ $widget['metadata']['name'] ?? $widget['type'] }}
              </h4>
              <p class="text-xs text-gray-500 truncate">{{ $widget['type'] }}</p>
            </div>
          </div>
          
          <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
            <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded">{{ ucfirst($category) }}</span>
            <span>{{ count($widget['metadata']['fields'] ?? []) }} fields</span>
          </div>
          
          <div class="flex gap-1">
            <a href="{{ isset($currentProject) ? route('project.admin.code-widgets.edit', [$currentProject->code, $widget['type']]) : route('cms.code-widgets.edit', $widget['type']) }}" 
              class="flex-1 text-center px-2 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
              Sửa
            </a>
            <a href="{{ isset($currentProject) ? route('project.admin.code-widgets.export', [$currentProject->code, $widget['type']]) : route('cms.code-widgets.export', $widget['type']) }}" 
              class="px-2 py-1.5 border border-gray-300 text-gray-600 rounded text-xs hover:bg-gray-50" title="Export JSON">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
            </a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg w-full max-w-md">
    <div class="flex justify-between items-center p-4 border-b">
      <h3 class="text-lg font-semibold">Import Widget Templates</h3>
      <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700"></button>
    </div>
    <form action="{{ isset($currentProject) ? route('project.admin.widget-templates.import', $currentProject->code) : route('cms.widget-templates.import') }}" 
       method="POST" enctype="multipart/form-data" class="p-4">
      @csrf
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn file JSON</label>
        <input type="file" name="json_file" accept=".json" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        <p class="text-xs text-gray-500 mt-1">Tối đa 2MB</p>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" 
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
          Hủy
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
          Import
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

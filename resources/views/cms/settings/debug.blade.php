@extends('cms.layouts.app')

@section('title', 'Debug Settings')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-2xl font-bold mb-4">Debug Information</h2>
  
  <div class="space-y-4">
    <div>
      <h3 class="font-semibold text-lg mb-2">Current User:</h3>
      <pre class="bg-gray-100 p-4 rounded">{{ json_encode($authUser ?? auth()->user(), JSON_PRETTY_PRINT) }}</pre>
    </div>
    
    <div>
      <h3 class="font-semibold text-lg mb-2">System Menu Config:</h3>
      <pre class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ json_encode(config('system_menu'), JSON_PRETTY_PRINT) }}</pre>
    </div>
    
    <div>
      <h3 class="font-semibold text-lg mb-2">Available Routes:</h3>
      <ul class="list-disc pl-6">
        @foreach(config('system_menu') as $module)
          <li>
            <strong>{{ $module['title'] }}</strong>: 
            {{ $module['route'] }} 
            @if(Route::has($module['route']))
              <span class="text-green-600"> Exists</span>
            @else
              <span class="text-red-600"> Not Found</span>
            @endif
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endsection

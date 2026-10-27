@extends('cms.layouts.app')

@section('title', 'CSRF Debug')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">CSRF Debug Information</h1>
    
    <div class="space-y-4">
        <div>
            <strong>CSRF Token:</strong> {{ csrf_token() }}
        </div>
        
        <div>
            <strong>Session Token:</strong> {{ session()->token() }}
        </div>
        
        <div>
            <strong>Meta CSRF Token:</strong>
            <span id="meta-csrf"></span>
        </div>
        
        <div>
            <strong>Session ID:</strong> {{ session()->getId() }}
        </div>
    </div>
    
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Test Delete Form</h2>
        <form method="POST" action="{{ route('project.admin.debug.session', request()->route('projectCode')) }}" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Test Delete (Safe)</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
    document.getElementById('meta-csrf').textContent = metaCsrf ? metaCsrf.getAttribute('content') : 'Not found';
});
</script>
@endsection

{{-- Topbar Style 6: Contact trái, Menu + Social phải --}}
<div class="topbar hidden md:block" style="background-color: #db2777; color: #ffffff;">
    <div class="container mx-auto px-4 py-2 flex justify-between items-center text-sm">
        <div class="flex gap-4">
            @if(setting('contact_phone'))
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                    {{ setting('contact_phone') }}
                </span>
            @endif
            @if(setting('contact_email'))
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    {{ setting('contact_email') }}
                </span>
            @endif
        </div>
        
        <div class="flex items-center gap-4">
            @if($topbarMenu && $topbarMenu->items)
                <nav class="flex gap-4">
                    @foreach($topbarMenu->items as $item)
                        <a href="{{ $item->url }}" class="hover:opacity-80" style="color: {{ $topbarText }};">{{ $item->title }}</a>
                    @endforeach
                </nav>
            @endif
            
            <div class="flex gap-2 ml-4 pl-4 border-l" style="border-color: rgba(255,255,255,0.3);">
                <a href="#" class="hover:opacity-80" style="color: {{ $topbarText }};">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="hover:opacity-80" style="color: {{ $topbarText }};">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>

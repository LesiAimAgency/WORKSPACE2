@props(['name', 'value' => '', 'placeholder' => ''])

<div class="border border-gray-300 rounded-lg overflow-hidden focus-within:border-[#001B4E] focus-within:ring-1 focus-within:ring-[#001B4E] bg-white shadow-sm transition-all duration-200">
    <!-- Toolbar -->
    <div class="bg-gray-50 border-b border-gray-300 px-3 py-2 flex flex-wrap items-center gap-1 shadow-sm">
        <button type="button" onclick="document.execCommand('bold', false, null); updateTextarea('{{ $name }}'); this.classList.toggle('bg-gray-200');" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="In đậm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15.6 11.8c1-.7 1.6-1.8 1.6-3 0-2.3-1.9-4.1-4.2-4.1H6v14h7.5c2.5 0 4.5-2 4.5-4.5 0-1.5-.7-2.7-1.8-3.4zM8.5 7h4c1.1 0 2 .9 2 2s-.9 2-2 2h-4V7zm4.5 9h-4v-4h4c1.4 0 2.5 1.1 2.5 2.5s-1.1 2.5-2.5 2.5z"/></svg>
        </button>
        <button type="button" onclick="document.execCommand('italic', false, null); updateTextarea('{{ $name }}'); this.classList.toggle('bg-gray-200');" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="In nghiêng">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v3h2.2l-3.4 10H6v3h8v-3h-2.2l3.4-10H18V4z"/></svg>
        </button>
        <button type="button" onclick="document.execCommand('underline', false, null); updateTextarea('{{ $name }}'); this.classList.toggle('bg-gray-200');" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="Gạch chân">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17c3.3 0 6-2.7 6-6V3h-2.5v8c0 1.9-1.6 3.5-3.5 3.5S8.5 12.9 8.5 11V3H6v8c0 3.3 2.7 6 6 6zm-7 2v2h14v-2H5z"/></svg>
        </button>
        
        <div class="w-px h-5 bg-gray-300 mx-1"></div>
        
        <button type="button" onclick="document.execCommand('insertUnorderedList', false, null); updateTextarea('{{ $name }}')" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="Danh sách">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h2v2H4zm0 5h2v2H4zm0 5h2v2H4zm4-10h12v2H8zm0 5h12v2H8zm0 5h12v2H8z"/></svg>
        </button>
        <button type="button" onclick="document.execCommand('insertOrderedList', false, null); updateTextarea('{{ $name }}')" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="Danh sách số">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z"/></svg>
        </button>
        
        <div class="w-px h-5 bg-gray-300 mx-1"></div>
        
        <button type="button" onclick="document.execCommand('justifyLeft', false, null); updateTextarea('{{ $name }}')" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="Căn trái">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zm0-4h12v-2H3v2zm0-4h18v-2H3v2zm0-4h12V7H3v2zm0-6v2h18V3H3z"/></svg>
        </button>
        <button type="button" onclick="document.execCommand('justifyCenter', false, null); updateTextarea('{{ $name }}')" class="p-1.5 text-gray-700 hover:bg-gray-200 hover:text-black rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#001B4E]" title="Căn giữa">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 15v2h10v-2H7zm-4 6h18v-2H3v2zm0-8h18v-2H3v2zm4-6v2h10V7H7zM3 3v2h18V3H3z"/></svg>
        </button>
        
        <div class="w-px h-5 bg-gray-300 mx-1 flex-grow"></div>
        
        <button type="button" onclick="document.getElementById('editor-{{ $name }}').innerHTML = ''; updateTextarea('{{ $name }}')" class="p-1.5 text-red-500 hover:bg-red-100 hover:text-red-700 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500" title="Xóa nội dung">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
    </div>

    <!-- Editor Area -->
    <div class="relative">
        <div 
            id="editor-{{ $name }}"
            class="prose prose-sm max-w-none w-full min-h-[150px] max-h-[500px] overflow-y-auto p-4 outline-none text-gray-800 focus:bg-blue-50/10 transition-colors"
            contenteditable="true"
            onkeyup="updateTextarea('{{ $name }}')"
            onblur="updateTextarea('{{ $name }}')"
            onpaste="setTimeout(() => updateTextarea('{{ $name }}'), 10)"
            onfocus="document.getElementById('placeholder-{{ $name }}').style.display='none';"
            onfocusout="if(this.innerHTML.trim()===''){ document.getElementById('placeholder-{{ $name }}').style.display='block'; }"
        >{!! $value !!}</div>
        
        <div 
            id="placeholder-{{ $name }}" 
            class="absolute top-4 left-4 text-gray-400 pointer-events-none text-sm select-none"
            style="display: {{ empty(trim($value)) ? 'block' : 'none' }};"
        >
            {{ $placeholder }}
        </div>
    </div>

    <!-- Hidden Input -->
    <textarea name="{{ $name }}" id="textarea-{{ $name }}" class="hidden" style="display: none;">{!! $value !!}</textarea>
</div>
@error($name)
    <x-form.error :message="$message" />
@enderror

<script>
    if (typeof updateTextarea === 'undefined') {
        window.updateTextarea = function(name) {
            const editor = document.getElementById('editor-' + name);
            const textarea = document.getElementById('textarea-' + name);
            const placeholder = document.getElementById('placeholder-' + name);
            
            if (editor && textarea) {
                const content = editor.innerHTML;
                textarea.value = content;
                
                if (placeholder) {
                    if (content.trim() === '' || content === '<br>') {
                        placeholder.style.display = 'block';
                        if (content === '<br>') editor.innerHTML = ''; // Clean empty br
                    } else {
                        placeholder.style.display = 'none';
                    }
                }
            }
        }

        // Global form submit synchronization
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const editors = form.querySelectorAll('[id^="editor-"]');
            editors.forEach(editor => {
                const name = editor.id.replace('editor-', '');
                window.updateTextarea(name);
            });
        });
    }
</script>

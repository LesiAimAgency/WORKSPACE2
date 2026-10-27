@props(['name', 'value' => '', 'height' => 400])

<textarea name="{{ $name }}" id="{{ $name }}" class="tinymce-editor">{{ $value }}</textarea>

@once
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '.tinymce-editor',
        height: {{ $height }},
        menubar: 'file edit view insert format tools table help',
        language: 'vi',
        
        // Word-like plugins
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
            'pagebreak', 'nonbreaking', 'template', 'paste', 'textpattern',
            'autoresize', 'quickbars', 'codesample', 'hr'
        ],
        
        // Word-like toolbar (multiple rows like Word)
        toolbar1: 'undo redo | cut copy paste pastetext | print | spellchecker | searchreplace | selectall',
        toolbar2: 'bold italic underline strikethrough | subscript superscript | removeformat',
        toolbar3: 'styles | fontfamily fontsize | forecolor backcolor | highlight',
        toolbar4: 'alignleft aligncenter alignright alignjustify | outdent indent | ltr rtl',
        toolbar5: 'bullist numlist | checklist | table | link unlink anchor | image media | insertdatetime hr pagebreak | charmap emoticons | codesample',
        toolbar6: 'visualblocks visualchars | fullscreen preview | help',
        
        // Word-like styling
        skin: 'oxide',
        content_css: 'default',
        content_style: `
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                font-size: 11pt; 
                line-height: 1.15; 
                margin: 1in; 
                background: white;
                color: #000;
            }
            h1 { font-size: 18pt; font-weight: bold; margin: 12pt 0; }
            h2 { font-size: 16pt; font-weight: bold; margin: 10pt 0; }
            h3 { font-size: 14pt; font-weight: bold; margin: 8pt 0; }
            h4 { font-size: 12pt; font-weight: bold; margin: 6pt 0; }
            p { margin: 6pt 0; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #ccc; padding: 4pt 8pt; }
        `,
        
        // Word-like fonts
        font_family_formats: 'Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times,serif; Calibri=calibri,sans-serif; Segoe UI=segoe ui,sans-serif; Tahoma=tahoma,arial,helvetica,sans-serif; Verdana=verdana,geneva,sans-serif; Georgia=georgia,palatino,serif; Courier New=courier new,courier,monospace',
        
        // Word-like font sizes
        font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 36pt 48pt 72pt',
        
        // Word-like heading styles
        style_formats: [
            { title: 'Tiêu đề 1', format: 'h1' },
            { title: 'Tiêu đề 2', format: 'h2' },
            { title: 'Tiêu đề 3', format: 'h3' },
            { title: 'Tiêu đề 4', format: 'h4' },
            { title: 'Đoạn văn', format: 'p' },
            { title: 'Trích dẫn', format: 'blockquote' },
            { title: 'Code', format: 'code' }
        ],
        
        // Word-like features
        paste_data_images: true,
        paste_as_text: false,
        paste_word_valid_elements: 'b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ol,ul,li,a[href],span,color,font-size,font-color,font-family,mark,table,tr,td,th',
        paste_retain_style_properties: 'all',
        automatic_uploads: true,
        file_picker_types: 'image',
        
        // Word-like table features
        table_default_attributes: {
            border: '1'
        },
        table_default_styles: {
            'border-collapse': 'collapse'
        },
        
        // Word-like list features
        lists_indent_on_tab: true,
        
        // Word-like quick formatting
        quickbars_selection_toolbar: 'bold italic underline | formatselect | bullist numlist | blockquote quicklink',
        quickbars_insert_toolbar: 'quickimage quicktable | hr pagebreak',
        
        // Advanced features
        contextmenu: 'link image table configurepermanentpen',
        a11y_advanced_options: true,
        
        // Word-like status bar
        statusbar: true,
        elementpath: true,
        resize: 'both',
        
        // Spell check
        browser_spellcheck: true,
        
        // Auto-resize like Word
        autoresize_bottom_margin: 50,
        autoresize_overflow_padding: 50,
        
        // Image upload (giữ nguyên)
        images_upload_handler: function (blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('/admin/media/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.url) {
                    success(result.url);
                } else {
                    failure('Upload failed');
                }
            })
            .catch(() => failure('Upload failed'));
        },
        
        // Word-like setup
        setup: function (editor) {
            // Add custom Word-like shortcuts
            editor.addShortcut('ctrl+b', 'Bold', 'Bold');
            editor.addShortcut('ctrl+i', 'Italic', 'Italic');
            editor.addShortcut('ctrl+u', 'Underline', 'Underline');
            editor.addShortcut('ctrl+shift+l', 'Bullet List', 'InsertUnorderedList');
        }
    });
});
</script>
@endpush
@endonce

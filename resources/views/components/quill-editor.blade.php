@props(['name', 'value' => '', 'height' => '400px', 'maxHeight' => '600px', 'placeholder' => 'Nhập nội dung...'])

<div x-data="quillEditor('{{ $name }}', @js($value), '{{ $height }}', '{{ $maxHeight }}', '{{ $placeholder }}')" x-init="init()">
  <div :id="editorId" :style="'min-height: {{ $height }}; --max-editor-height: {{ $maxHeight }}'"></div>
  <input type="hidden" :name="name" x-model="content">
</div>

@once
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Google Fonts for Quill Editor -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&family=Lato:wght@400;700&family=Montserrat:wght@400;500;700&family=Nunito:wght@400;600;700&family=Poppins:wght@400;500;700&family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700&family=Source+Sans+Pro:wght@400;600;700&family=Raleway:wght@400;500;700&family=Ubuntu:wght@400;500;700&family=Oswald:wght@400;500;700&family=Dancing+Script:wght@400;700&family=Pacifico&family=Lobster&display=swap" rel="stylesheet">
<style>
/* Custom Font Families for Quill */
.ql-font-arial { font-family: Arial, sans-serif; }
.ql-font-times { font-family: 'Times New Roman', Times, serif; }
.ql-font-georgia { font-family: Georgia, serif; }
.ql-font-verdana { font-family: Verdana, sans-serif; }
.ql-font-tahoma { font-family: Tahoma, sans-serif; }
.ql-font-trebuchet { font-family: 'Trebuchet MS', sans-serif; }
.ql-font-courier { font-family: 'Courier New', Courier, monospace; }
.ql-font-roboto { font-family: 'Roboto', sans-serif; }
.ql-font-opensans { font-family: 'Open Sans', sans-serif; }
.ql-font-lato { font-family: 'Lato', sans-serif; }
.ql-font-montserrat { font-family: 'Montserrat', sans-serif; }
.ql-font-nunito { font-family: 'Nunito', sans-serif; }
.ql-font-poppins { font-family: 'Poppins', sans-serif; }
.ql-font-playfair { font-family: 'Playfair Display', serif; }
.ql-font-merriweather { font-family: 'Merriweather', serif; }
.ql-font-sourcesans { font-family: 'Source Sans Pro', sans-serif; }
.ql-font-raleway { font-family: 'Raleway', sans-serif; }
.ql-font-ubuntu { font-family: 'Ubuntu', sans-serif; }
.ql-font-oswald { font-family: 'Oswald', sans-serif; }
.ql-font-dancing { font-family: 'Dancing Script', cursive; }
.ql-font-pacifico { font-family: 'Pacifico', cursive; }
.ql-font-lobster { font-family: 'Lobster', cursive; }

/* Font picker dropdown styling */
.ql-snow .ql-picker.ql-font {
  width: 150px;
}

.ql-snow .ql-picker.ql-font .ql-picker-label::before,
.ql-snow .ql-picker.ql-font .ql-picker-item::before {
  content: 'Sans Serif';
}

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="arial"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="arial"]::before { content: 'Arial'; font-family: Arial; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="times"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="times"]::before { content: 'Times New Roman'; font-family: 'Times New Roman'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="georgia"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="georgia"]::before { content: 'Georgia'; font-family: Georgia; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="verdana"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="verdana"]::before { content: 'Verdana'; font-family: Verdana; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="tahoma"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="tahoma"]::before { content: 'Tahoma'; font-family: Tahoma; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="trebuchet"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="trebuchet"]::before { content: 'Trebuchet MS'; font-family: 'Trebuchet MS'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="courier"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="courier"]::before { content: 'Courier New'; font-family: 'Courier New'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="roboto"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="roboto"]::before { content: 'Roboto'; font-family: 'Roboto'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="opensans"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="opensans"]::before { content: 'Open Sans'; font-family: 'Open Sans'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="lato"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="lato"]::before { content: 'Lato'; font-family: 'Lato'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="montserrat"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="montserrat"]::before { content: 'Montserrat'; font-family: 'Montserrat'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="nunito"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="nunito"]::before { content: 'Nunito'; font-family: 'Nunito'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="poppins"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="poppins"]::before { content: 'Poppins'; font-family: 'Poppins'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="playfair"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="playfair"]::before { content: 'Playfair Display'; font-family: 'Playfair Display'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="merriweather"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="merriweather"]::before { content: 'Merriweather'; font-family: 'Merriweather'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="sourcesans"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="sourcesans"]::before { content: 'Source Sans Pro'; font-family: 'Source Sans Pro'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="raleway"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="raleway"]::before { content: 'Raleway'; font-family: 'Raleway'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="ubuntu"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="ubuntu"]::before { content: 'Ubuntu'; font-family: 'Ubuntu'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="oswald"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="oswald"]::before { content: 'Oswald'; font-family: 'Oswald'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="dancing"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="dancing"]::before { content: 'Dancing Script'; font-family: 'Dancing Script'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="pacifico"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="pacifico"]::before { content: 'Pacifico'; font-family: 'Pacifico'; }

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="lobster"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="lobster"]::before { content: 'Lobster'; font-family: 'Lobster'; }

/* Custom Font Sizes */
.ql-snow .ql-picker.ql-size { width: 80px; }
.ql-snow .ql-picker.ql-size .ql-picker-label::before,
.ql-snow .ql-picker.ql-size .ql-picker-item::before { content: '16px'; }

.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="8px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="8px"]::before { content: '8px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="9px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="9px"]::before { content: '9px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10px"]::before { content: '10px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="11px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="11px"]::before { content: '11px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12px"]::before { content: '12px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14px"]::before { content: '14px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18px"]::before { content: '18px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="20px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="20px"]::before { content: '20px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24px"]::before { content: '24px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="28px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="28px"]::before { content: '28px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="32px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="32px"]::before { content: '32px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36px"]::before { content: '36px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="48px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="48px"]::before { content: '48px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="60px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="60px"]::before { content: '60px'; }
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="72px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="72px"]::before { content: '72px'; }

/* Font size classes for editor content */
.ql-editor .ql-size-8px { font-size: 8px; }
.ql-editor .ql-size-9px { font-size: 9px; }
.ql-editor .ql-size-10px { font-size: 10px; }
.ql-editor .ql-size-11px { font-size: 11px; }
.ql-editor .ql-size-12px { font-size: 12px; }
.ql-editor .ql-size-14px { font-size: 14px; }
.ql-editor .ql-size-18px { font-size: 18px; }
.ql-editor .ql-size-20px { font-size: 20px; }
.ql-editor .ql-size-24px { font-size: 24px; }
.ql-editor .ql-size-28px { font-size: 28px; }
.ql-editor .ql-size-32px { font-size: 32px; }
.ql-editor .ql-size-36px { font-size: 36px; }
.ql-editor .ql-size-48px { font-size: 48px; }
.ql-editor .ql-size-60px { font-size: 60px; }
.ql-editor .ql-size-72px { font-size: 72px; }

/* Quill Editor Styling */
.ql-toolbar.ql-snow {
  border: 1px solid #d1d5db;
  border-radius: 8px 8px 0 0;
  background: #f9fafb;
  padding: 12px;
}

/* Hide duplicate toolbars */
.ql-container + .ql-toolbar {
  display: none;
}

/* Ensure only one toolbar per editor */
[id^="quill-editor-"] .ql-toolbar:not(:first-child) {
  display: none;
}

.ql-container.ql-snow {
  border: 1px solid #d1d5db;
  border-top: none;
  border-radius: 0 0 8px 8px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ql-editor {
  min-height: 300px;
  max-height: var(--max-editor-height, 600px);
  overflow-y: auto;
  font-size: 14px;
  line-height: 1.6;
  padding: 16px;
  color: #374151;
}

/* Custom scrollbar for editor */
.ql-editor::-webkit-scrollbar {
  width: 8px;
}

.ql-editor::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.ql-editor::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.ql-editor::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

.ql-editor.ql-blank::before {
  color: #9ca3af;
  font-style: normal;
}

.ql-editor h1 { font-size: 2em; font-weight: bold; margin: 0.67em 0; }
.ql-editor h2 { font-size: 1.5em; font-weight: bold; margin: 0.75em 0; }
.ql-editor h3 { font-size: 1.17em; font-weight: bold; margin: 0.83em 0; }
.ql-editor h4 { font-size: 1em; font-weight: bold; margin: 1.12em 0; }
.ql-editor h5 { font-size: 0.83em; font-weight: bold; margin: 1.5em 0; }
.ql-editor h6 { font-size: 0.75em; font-weight: bold; margin: 1.67em 0; }

.ql-editor p { margin: 1em 0; }
.ql-editor blockquote { 
  border-left: 4px solid #e5e7eb; 
  margin: 1em 0; 
  padding-left: 1em; 
  color: #6b7280;
}

.ql-editor ul, .ql-editor ol { margin: 1em 0; padding-left: 2em; }
.ql-editor li { margin: 0.5em 0; }

.ql-editor pre { 
  background: #f3f4f6; 
  border: 1px solid #e5e7eb; 
  border-radius: 4px; 
  padding: 1em; 
  margin: 1em 0;
  overflow-x: auto;
}

.ql-editor code { 
  background: #f3f4f6; 
  padding: 0.2em 0.4em; 
  border-radius: 3px; 
  font-family: 'Courier New', monospace;
}

.ql-editor img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1em 0;
}

.ql-editor iframe {
  max-width: 100%;
  border-radius: 8px;
  margin: 1em 0;
}

/* Toolbar button styling */
.ql-toolbar .ql-formats {
  margin-right: 15px;
}

.ql-toolbar button {
  padding: 5px;
  margin: 2px;
  border-radius: 4px;
}

.ql-toolbar button:hover {
  background: #e5e7eb;
}

.ql-toolbar button.ql-active {
  background: #3b82f6;
  color: white;
}

/* Custom media button styling */
.ql-toolbar .ql-media-image,
.ql-toolbar .ql-media-video {
  position: relative;
}

.ql-toolbar .ql-media-image::after,
.ql-toolbar .ql-media-video::after {
  content: '';
  position: absolute;
  bottom: -2px;
  right: -2px;
  font-size: 8px;
}

/* Focus styling */
.ql-container.ql-snow:focus-within {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// Register custom fonts with Quill
var Font = Quill.import('formats/font');
Font.whitelist = [
  false, // Default font
  'arial', 'times', 'georgia', 'verdana', 'tahoma', 'trebuchet', 'courier',
  'roboto', 'opensans', 'lato', 'montserrat', 'nunito', 'poppins',
  'playfair', 'merriweather', 'sourcesans', 'raleway', 'ubuntu', 'oswald',
  'dancing', 'pacifico', 'lobster'
];
Quill.register(Font, true);

// Register custom font sizes with Quill
var Size = Quill.import('formats/size');
Size.whitelist = ['8px', '9px', '10px', '11px', '12px', '14px', false, '18px', '20px', '24px', '28px', '32px', '36px', '48px', '60px', '72px'];
Quill.register(Size, true);

// Global variable to track which Quill editor is requesting media
window.activeQuillEditor = null;
window.activeQuillMediaType = null;

function quillEditor(name, initialValue, height, maxHeight, placeholder) {
  return {
    name: name,
    content: initialValue,
    editorId: 'quill-editor-' + name + '-' + Math.random().toString(36).substr(2, 9),
    quill: null,
    showMediaManager: false,
    
    init() {
      // Prevent multiple initialization
      if (this.quill) {
        return;
      }
      
      // Wait for DOM to be ready
      this.$nextTick(() => {
        this.initQuill(placeholder);
        this.setupMediaListener();
      });
    },
    
    setupMediaListener() {
      // Listen for media selection from Media Manager
      window.addEventListener('media-selected', (e) => {
        // Only process if this editor requested the media
        if (window.activeQuillEditor !== this.editorId) {
          return;
        }
        
        const items = e.detail.files || e.detail.items || [];
        if (items.length > 0 && this.quill) {
          const range = this.quill.getSelection(true);
          const index = range ? range.index : this.quill.getLength();
          
          items.forEach((item, i) => {
            const url = item.url;
            if (window.activeQuillMediaType === 'video') {
              // Insert as video/iframe
              this.quill.insertEmbed(index + i, 'video', url);
            } else {
              // Insert as image
              this.quill.insertEmbed(index + i, 'image', url);
            }
          });
          
          // Move cursor after inserted content
          this.quill.setSelection(index + items.length);
        }
        
        // Reset active editor
        window.activeQuillEditor = null;
        window.activeQuillMediaType = null;
      });
    },
    
    initQuill(placeholder) {
      // Check if element exists and not already initialized
      const element = document.getElementById(this.editorId);
      if (!element || this.quill) {
        return;
      }
      
      const self = this;
      
      const toolbarOptions = [
        // Text formatting
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'font': [false, 'arial', 'times', 'georgia', 'verdana', 'tahoma', 'trebuchet', 'courier', 'roboto', 'opensans', 'lato', 'montserrat', 'nunito', 'poppins', 'playfair', 'merriweather', 'sourcesans', 'raleway', 'ubuntu', 'oswald', 'dancing', 'pacifico', 'lobster'] }, { 'size': ['8px', '9px', '10px', '11px', '12px', '14px', false, '18px', '20px', '24px', '28px', '32px', '36px', '48px', '60px', '72px'] }],
        
        // Style formatting 
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        
        // Paragraph formatting
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'align': '' }, { 'align': 'center' }, { 'align': 'right' }, { 'align': 'justify' }],
        
        // Insert elements
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],
        
        // Utilities
        ['clean']
      ];

      this.quill = new Quill('#' + this.editorId, {
        theme: 'snow',
        placeholder: placeholder,
        modules: {
          toolbar: {
            container: toolbarOptions,
            handlers: {
              image: function() {
                self.openMediaManager('image');
              },
              video: function() {
                self.openMediaManager('video');
              }
            }
          },
          clipboard: {
            matchVisual: false
          }
        },
        formats: [
          'header', 'font', 'size',
          'bold', 'italic', 'underline', 'strike', 
          'color', 'background', 'script',
          'list', 'bullet', 'indent', 'align',
          'blockquote', 'code-block', 'code',
          'link', 'image', 'video'
        ]
      });
      
      // Set initial content
      if (this.content) {
        this.quill.root.innerHTML = this.content;
      }
      
      // Listen for content changes
      this.quill.on('text-change', () => {
        this.content = this.quill.root.innerHTML;
      });
    },
    
    openMediaManager(type) {
      // Set this editor as active
      window.activeQuillEditor = this.editorId;
      window.activeQuillMediaType = type;
      
      // Dispatch event to open media manager
      window.dispatchEvent(new CustomEvent('open-quill-media-manager', {
        detail: {
          editorId: this.editorId,
          type: type
        }
      }));
    },
    
    destroy() {
      if (this.quill) {
        this.quill = null;
      }
    }
  }
}
</script>
@endpush
@endonce

<!-- Quill Media Manager Modal (shared across all editors) -->
@once
<div x-data="quillMediaManagerGlobal()" x-cloak>
  <!-- Modal -->
  <div x-show="isOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div x-show="isOpen" @click="closeModal()" class="fixed inset-0 bg-black bg-opacity-50"></div>

      <div x-show="isOpen" class="relative bg-white rounded-lg shadow-xl max-w-7xl w-full h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <h3 class="text-lg font-semibold">
            <span x-text="mediaType === 'video' ? 'Chọn Video từ thư viện' : 'Chọn Ảnh từ thư viện'"></span>
          </h3>
          <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b bg-gray-50 flex items-center gap-3">
          <input type="file" x-ref="fileInput" @change="uploadFiles($event)" multiple :accept="mediaType === 'video' ? 'video/*' : 'image/*'" class="hidden">
          <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            Upload
          </button>
          <button type="button" @click="showCreateFolder = true" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
            </svg>
            Tạo thư mục
          </button>
          <div class="flex-1"></div>
          <input type="text" x-model="searchQuery" @input="filterMedia()" placeholder="Tìm kiếm..." class="px-4 py-2 border rounded-lg w-64">
        </div>

        <!-- Breadcrumb -->
        <div class="px-4 py-2 bg-gray-50 border-b flex items-center gap-2 text-sm">
          <button @click="navigateToFolder('')" class="text-blue-600 hover:underline">Root</button>
          <template x-for="(part, index) in currentPath.split('/').filter(p => p)" :key="index">
            <div class="flex items-center gap-2">
              <span>/</span>
              <button @click="navigateToFolder(currentPath.split('/').slice(0, index + 2).join('/'))" 
                  class="text-blue-600 hover:underline" x-text="part"></button>
            </div>
          </template>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="showCreateFolder" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
          <div @click.stop class="bg-white rounded-lg p-6 w-96">
            <h4 class="font-semibold mb-4">Tạo thư mục mới</h4>
            <input type="text" x-model="newFolderName" @keyup.enter="createFolder()" 
                placeholder="Tên thư mục" class="w-full px-4 py-2 border rounded-lg mb-4">
            <div class="flex gap-3 justify-end">
              <button @click="showCreateFolder = false; newFolderName = ''" 
                  class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
              <button @click="createFolder()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tạo</button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-4">
          <div x-show="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Đang tải...</p>
          </div>

          <div x-show="!loading && folders.length === 0 && filteredMedia.length === 0" class="text-center py-12 text-gray-500">
            Thư mục trống
          </div>

          <div x-show="!loading" class="space-y-4">
            <!-- Folders -->
            <div x-show="folders.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <template x-for="folder in folders" :key="folder.name">
                <div @dblclick="navigateToFolder(folder.path)"
                   class="relative border-2 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition group">
                  <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                    </svg>
                    <span class="mt-2 text-sm text-center truncate w-full" x-text="folder.name"></span>
                  </div>
                </div>
              </template>
            </div>

            <!-- Files -->
            <div x-show="filteredMedia.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <template x-for="item in filteredMedia" :key="item.id">
                <div @click="selectMedia(item)"
                   :class="{'ring-4 ring-blue-500': isSelected(item.id)}"
                   class="relative aspect-square border-2 rounded-lg overflow-hidden cursor-pointer hover:border-blue-500 transition group">
                  <template x-if="item.type === 'video' || item.name.match(/\.(mp4|webm|ogg|mov)$/i)">
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                  </template>
                  <template x-if="!(item.type === 'video' || item.name.match(/\.(mp4|webm|ogg|mov)$/i))">
                    <img :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                  </template>
                  <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center">
                    <svg x-show="isSelected(item.id)" class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate" x-text="item.name"></div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
          <div class="text-sm text-gray-600">
            <span x-text="selectedItems.length"></span> file đã chọn
          </div>
          <div class="flex gap-3">
            <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</button>
            <button type="button" @click="confirmSelection()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Chèn vào nội dung</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function quillMediaManagerGlobal() {
  return {
    isOpen: false,
    loading: false,
    currentPath: '',
    folders: [],
    mediaItems: [],
    filteredMedia: [],
    selectedItems: [],
    searchQuery: '',
    showCreateFolder: false,
    newFolderName: '',
    mediaType: 'image',
    baseUrl: '{{ url("admin") }}',
    
    init() {
      // Listen for open media manager event from Quill
      window.addEventListener('open-quill-media-manager', (e) => {
        this.mediaType = e.detail.type || 'image';
        this.openModal();
      });
    },
    
    openModal() {
      this.isOpen = true;
      this.selectedItems = [];
      this.loadMedia();
    },
    
    closeModal() {
      this.isOpen = false;
      this.selectedItems = [];
    },
    
    async loadMedia() {
      this.loading = true;
      try {
        const response = await fetch(`${this.baseUrl}/media/list?path=${encodeURIComponent(this.currentPath)}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const data = await response.json();
        this.folders = data.folders || [];
        this.mediaItems = data.files || [];
        this.filteredMedia = this.mediaItems;
        this.loading = false;
      } catch (error) {
        console.error('Error loading media:', error);
        this.loading = false;
      }
    },
    
    navigateToFolder(path) {
      this.currentPath = path;
      this.selectedItems = [];
      this.loadMedia();
    },
    
    async createFolder() {
      if (!this.newFolderName) return;
      
      try {
        const response = await fetch(`${this.baseUrl}/media/folder`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            path: this.currentPath,
            name: this.newFolderName
          })
        });
        
        if (response.ok) {
          this.showCreateFolder = false;
          this.newFolderName = '';
          this.loadMedia();
        }
      } catch (error) {
        console.error('Create folder error:', error);
      }
    },
    
    filterMedia() {
      if (!this.searchQuery) {
        this.filteredMedia = this.mediaItems;
        return;
      }
      this.filteredMedia = this.mediaItems.filter(item => 
        item.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    
    selectMedia(item) {
      const index = this.selectedItems.findIndex(i => i.id === item.id);
      if (index > -1) {
        this.selectedItems.splice(index, 1);
      } else {
        this.selectedItems.push(item);
      }
    },
    
    isSelected(id) {
      return this.selectedItems.some(item => item.id === id);
    },
    
    async uploadFiles(event) {
      const files = Array.from(event.target.files);
      const formData = new FormData();
      formData.append('path', this.currentPath);
      files.forEach(file => formData.append('files[]', file));
      
      this.loading = true;
      try {
        const response = await fetch(`${this.baseUrl}/media/upload`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: formData
        });
        if (response.ok) {
          this.loadMedia();
        }
      } catch (error) {
        console.error('Upload error:', error);
      }
      this.loading = false;
    },
    
    confirmSelection() {
      if (this.selectedItems.length === 0) {
        alert('Vui lòng chọn ít nhất một file');
        return;
      }
      
      // Dispatch media-selected event for Quill to catch
      window.dispatchEvent(new CustomEvent('media-selected', {
        detail: {
          files: this.selectedItems.map(item => ({
            id: item.id,
            name: item.name,
            url: item.url,
            path: item.path
          }))
        }
      }));
      
      this.closeModal();
    }
  }
}
</script>
@endpush
@endonce

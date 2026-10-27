@props(['name', 'value' => '', 'height' => '300px'])

<textarea name="{{ $name }}" id="editor-{{ $name }}">{{ $value }}</textarea>

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css">
<style>
    .ck-editor__editable_inline {
        min-height: {{ $height }};
    }
</style>
@endpush

@push('scripts')
<script type="importmap">
{
    "imports": {
        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/"
    }
}
</script>
<script type="module">
import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Font,
    Paragraph,
    Heading,
    Link,
    List,
    BlockQuote,
    Image,
    ImageToolbar,
    ImageUpload,
    ImageCaption,
    ImageStyle,
    ImageResize,
    Table,
    TableToolbar,
    MediaEmbed,
    Alignment,
    Indent,
    Underline,
    Strikethrough,
    Code,
    Subscript,
    Superscript,
    RemoveFormat,
    SourceEditing,
    GeneralHtmlSupport
} from 'ckeditor5';

ClassicEditor
    .create(document.querySelector('#editor-{{ $name }}'), {
        plugins: [
            Essentials, Bold, Italic, Font, Paragraph, Heading, Link, List, BlockQuote,
            Image, ImageToolbar, ImageUpload, ImageCaption, ImageStyle, ImageResize,
            Table, TableToolbar, MediaEmbed, Alignment, Indent,
            Underline, Strikethrough, Code, Subscript, Superscript, RemoveFormat,
            SourceEditing, GeneralHtmlSupport
        ],
        toolbar: {
            items: [
                'undo', 'redo', '|',
                'heading', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'link', 'uploadImage', 'insertTable', 'blockQuote', 'mediaEmbed', '|',
                'alignment', 'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                'code', 'subscript', 'superscript', 'removeFormat', '|',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: true
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
        },
        image: {
            toolbar: [
                'imageTextAlternative', 'toggleImageCaption', '|',
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side'
            ]
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        htmlSupport: {
            allow: [
                {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ],
            disallow: [
                {
                    name: /^(script|iframe|embed|object|noscript|applet|meta|link)$/i
                },
                {
                    name: /.*/,
                    attributes: {
                        key: /^on/i
                    }
                },
                {
                    name: /.*/,
                    attributes: {
                        key: /^(href|src|action|data)$/i,
                        value: /^javascript:/i
                    }
                }
            ]
        }
    })
    .catch(error => {
        console.error(error);
    });
</script>
@endpush
@endonce

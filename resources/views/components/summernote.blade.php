@props(['name', 'value' => '', 'height' => '400'])

<textarea name="{{ $name }}" id="summernote-{{ $name }}" class="summernote">{{ $value }}</textarea>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
.note-editor.note-frame{border:1px solid #d1d5db;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
.note-toolbar{background:#fff;border-bottom:1px solid #d1d5db;padding:8px 10px}
.note-editable{font-size:15px;line-height:1.8;padding:20px;min-height:300px;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
.note-btn{border-radius:4px;margin:1px;transition:all 0.15s;padding:6px 8px ;min-width:32px ;height:32px ;border:1px solid transparent }
.note-btn:hover{background:#f3f4f6;border-color:#d1d5db ;box-shadow:0 1px 2px rgba(0,0,0,0.05)}
.note-btn:active{background:#e5e7eb}
.note-btn-group{margin-right:4px;border:1px solid #e5e7eb;border-radius:4px;padding:2px}
.note-btn i{font-size:16px;color:#374151}
.note-dropdown-toggle:after{margin-left:4px}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-vi-VN.min.js"></script>
<script>
$(document).ready(function() {
    $('#summernote-{{ $name }}').summernote({
        height: {{ $height }},
        lang: 'vi-VN',
        placeholder: 'Nhập nội dung tại đây...',
        tooltip: true,
        tooltips: {
            style: 'Kiểu văn bản',
            bold: 'In đậm (Ctrl+B)',
            italic: 'In nghiêng (Ctrl+I)',
            underline: 'Gạch chân (Ctrl+U)',
            strikethrough: 'Gạch ngang',
            fontname: 'Phông chữ',
            fontsize: 'Cỡ chữ',
            color: 'Màu chữ',
            ul: 'Danh sách không thứ tự',
            ol: 'Danh sách có thứ tự',
            paragraph: 'Căn chỉnh đoạn văn',
            height: 'Độ cao dòng',
            table: 'Chèn bảng',
            link: 'Chèn liên kết',
            picture: 'Chèn hình ảnh',
            video: 'Chèn video',
            fullscreen: 'Toàn màn hình',
            codeview: 'Xem mã HTML',
            help: 'Trợ giúp'
        },
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        fontNames: [
            'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 
            'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana',
            'Segoe UI', 'Roboto', 'Open Sans'
        ],
        fontNamesIgnoreCheck: ['Segoe UI', 'Roboto', 'Open Sans'],
        fontSizes: ['10', '12', '14', '16', '18', '20', '24', '28', '32', '36', '48'],
        lineHeights: ['1.0', '1.2', '1.4', '1.5', '1.6', '1.8', '2.0', '3.0'],
        styleTags: [
            'p',
            { title: 'Tiêu đề 1', tag: 'h1', className: '', value: 'h1' },
            { title: 'Tiêu đề 2', tag: 'h2', className: '', value: 'h2' },
            { title: 'Tiêu đề 3', tag: 'h3', className: '', value: 'h3' },
            { title: 'Tiêu đề 4', tag: 'h4', className: '', value: 'h4' },
            { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
            { title: 'Code', tag: 'pre', className: '', value: 'pre' }
        ],
        popover: {
            image: [
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],
            link: [
                ['link', ['linkDialogShow', 'unlink']]
            ],
            table: [
                ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
            ]
        },
        callbacks: {
            onImageUpload: function(files) {
                uploadImage{{ $name }}(files[0]);
            },
            onPaste: function(e) {
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                document.execCommand('insertText', false, bufferText);
            }
        }
    });
    
    // Tooltip sẽ hiển thị khi hover
});

function uploadImage{{ $name }}(file) {
    let data = new FormData();
    data.append("file", file);
    data.append("_token", document.querySelector('meta[name="csrf-token"]').content);
    
    $.ajax({
        url: '/admin/media/upload',
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: "POST",
        success: function(response) {
            $('#summernote-{{ $name }}').summernote('insertImage', response.url);
        },
        error: function(data) {
            console.log(data);
            alert('Lỗi khi tải ảnh lên. Vui lòng thử lại!');
        }
    });
}
</script>
@endpush

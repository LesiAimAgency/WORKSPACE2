{{--
    VTM WYSIWYG Editor
    Usage: @include('admin.components.editor', ['name'=>'content', 'value'=>$val, 'height'=>400])
--}}
@php
    $edId = 'vtmed_' . Str::random(6);
    $edH  = $height ?? 380;
@endphp
<div class="vtmed-wrap" id="{{ $edId }}_wrap">

{{-- MENUBAR --}}
<div class="vtmed-menubar">
@php
$menus = [
  'file'   => ['label'=>'Tập tin','items'=>[
    ['cmd'=>'newDoc','label'=>'Mới','fa'=>'fa-file'],
    ['sep'=>1],
    ['cmd'=>'print','label'=>'In...','fa'=>'fa-print'],
  ]],
  'edit'   => ['label'=> __('common.edit'),'items'=>[
    ['cmd'=>'undo','label'=>'Hoàn tác','fa'=>'fa-rotate-left','kbd'=>'Ctrl+Z'],
    ['cmd'=>'redo','label'=>'Làm lại','fa'=>'fa-rotate-right','kbd'=>'Ctrl+Y'],
    ['sep'=>1],
    ['cmd'=>'cut','label'=>'Cắt','fa'=>'fa-scissors','kbd'=>'Ctrl+X'],
    ['cmd'=>'copy','label'=>'Sao chép','fa'=>'fa-copy','kbd'=>'Ctrl+C'],
    ['cmd'=>'paste','label'=>'Dán','fa'=>'fa-paste','kbd'=>'Ctrl+V'],
    ['sep'=>1],
    ['cmd'=>'selectAll','label'=>'Chọn tất cả','fa'=>'fa-object-group','kbd'=>'Ctrl+A'],
    ['sep'=>1],
    ['cmd'=>'find','label'=>'Tìm & Thay thế...','fa'=>'fa-magnifying-glass','kbd'=>'Ctrl+H'],
  ]],
  'insert' => ['label'=>'Chèn','items'=>[
    ['cmd'=>'insertImage','label'=>'Hình ảnh...','fa'=>'fa-image'],
    ['cmd'=>'insertLink','label'=>'Liên kết...','fa'=>'fa-link','kbd'=>'Ctrl+K'],
    ['cmd'=>'insertMedia','label'=>'Media...','fa'=>'fa-film'],
    ['sub'=>'tableGrid','label'=>'Bảng','fa'=>'fa-table','children'=>[]],
    ['sep'=>1],
    ['cmd'=>'insertSpecialChar','label'=>'Ký tự đặc biệt...','fa'=>'fa-omega'],
    ['cmd'=>'insertHorizontalRule','label'=>'Kẻ ngang','fa'=>'fa-minus'],
    ['sep'=>1],
    ['cmd'=>'insertPageBreak','label'=>'Ngắt trang','fa'=>'fa-file-circle-plus'],
    ['cmd'=>'insertNbsp','label'=>'Khoảng trắng không ngắt','fa'=>'fa-text-width'],
    ['sub'=>'datetime','label'=>'Ngày/Giờ','fa'=>'fa-clock','children'=>[
      ['cmd'=>'insertDate','label'=>'Chèn ngày hiện tại','fa'=>'fa-calendar'],
      ['cmd'=>'insertTime','label'=>'Chèn giờ hiện tại','fa'=>'fa-clock'],
      ['cmd'=>'insertDateTime','label'=>'Chèn ngày & giờ','fa'=>'fa-calendar-days'],
    ]],
  ]],
  'view'   => ['label'=> __('common.view'),'items'=>[
    ['cmd'=>'toggleSource','label'=>'Mã nguồn HTML','fa'=>'fa-code'],
    ['cmd'=>'toggleFullscreen','label'=>'Toàn màn hình','fa'=>'fa-expand'],
    ['sep'=>1],
    ['cmd'=>'wordCount','label'=>'Đếm từ','fa'=>'fa-calculator'],
  ]],
  'format' => ['label'=>'Định dạng','items'=>[
    ['cmd'=>'bold','label'=>'Đậm','fa'=>'fa-bold','kbd'=>'Ctrl+B'],
    ['cmd'=>'italic','label'=>'Nghiêng','fa'=>'fa-italic','kbd'=>'Ctrl+I'],
    ['cmd'=>'underline','label'=>'Gạch dưới','fa'=>'fa-underline','kbd'=>'Ctrl+U'],
    ['cmd'=>'strikeThrough','label'=>'Gạch ngang','fa'=>'fa-strikethrough'],
    ['cmd'=>'superscript','label'=>'Chỉ số trên','fa'=>'fa-superscript'],
    ['cmd'=>'subscript','label'=>'Chỉ số dưới','fa'=>'fa-subscript'],
    ['sep'=>1],
    ['cmd'=>'removeFormat','label'=> __('common.delete') . ' định dạng','fa'=>'fa-text-slash'],
  ]],
  'table'  => ['label'=>'Bảng','items'=>[
    ['cmd'=>'insertTable','label'=>'Chèn bảng...','fa'=>'fa-table'],
    ['sep'=>1],
    ['cmd'=>'tableAddRowBefore','label'=>'Thêm hàng trên','fa'=>'fa-table-rows'],
    ['cmd'=>'tableAddRowAfter','label'=>'Thêm hàng dưới','fa'=>'fa-table-rows'],
    ['cmd'=>'tableDelRow','label'=> __('common.delete') . ' hàng','fa'=>'fa-trash'],
    ['sep'=>1],
    ['cmd'=>'tableAddColBefore','label'=>'Thêm cột trái','fa'=>'fa-table-columns'],
    ['cmd'=>'tableAddColAfter','label'=>'Thêm cột phải','fa'=>'fa-table-columns'],
    ['cmd'=>'tableDelCol','label'=> __('common.delete') . ' cột','fa'=>'fa-trash'],
    ['sep'=>1],
    ['cmd'=>'tableDelTable','label'=> __('common.delete') . ' bảng','fa'=>'fa-trash'],
  ]],
  'tools'  => ['label'=>'Công cụ','items'=>[
    ['cmd'=>'find','label'=>'Tìm & Thay thế...','fa'=>'fa-magnifying-glass'],
    ['cmd'=>'wordCount','label'=>'Đếm từ','fa'=>'fa-calculator'],
    ['cmd'=>'toggleSource','label'=> __('common.view') . ' mã nguồn','fa'=>'fa-code'],
  ]],
];
@endphp
@foreach($menus as $mk => $menu)
<div class="vtmed-menu-item" data-menu="{{ $mk }}">
    <button type="button" class="vtmed-menu-btn">{{ $menu['label'] }}</button>
    <div class="vtmed-dropdown">
        @foreach($menu['items'] as $it)
            @if(isset($it['sep']))
                <div class="vtmed-dd-sep"></div>
            @elseif(isset($it['sub']) && $it['sub'] === 'tableGrid')
                <div class="vtmed-dd-item vtmed-dd-sub">
                    <span class="vtmed-dd-icon"><i class="fa-solid {{ $it['fa'] }}"></i></span>
                    <span class="vtmed-dd-label">{{ $it['label'] }}</span>
                    <span class="vtmed-dd-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                    <div class="vtmed-subdropdown vtmed-tg-drop">
                        <div class="vtmed-tg-grid" id="{{ $edId }}_tg"></div>
                        <div class="vtmed-tg-lbl" id="{{ $edId }}_tg_lbl">0 × 0</div>
                    </div>
                </div>
            @elseif(isset($it['sub']))
                <div class="vtmed-dd-item vtmed-dd-sub">
                    <span class="vtmed-dd-icon"><i class="fa-solid {{ $it['fa'] ?? 'fa-circle' }}"></i></span>
                    <span class="vtmed-dd-label">{{ $it['label'] }}</span>
                    <span class="vtmed-dd-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                    <div class="vtmed-subdropdown">
                        @foreach($it['children'] as $ch)
                            <div class="vtmed-dd-item" data-cmd="{{ $ch['cmd'] }}">
                                <span class="vtmed-dd-icon"><i class="fa-solid {{ $ch['fa'] ?? 'fa-circle' }}"></i></span>
                                <span class="vtmed-dd-label">{{ $ch['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="vtmed-dd-item" data-cmd="{{ $it['cmd'] }}">
                    <span class="vtmed-dd-icon"><i class="fa-solid {{ $it['fa'] ?? 'fa-circle' }}"></i></span>
                    <span class="vtmed-dd-label">{{ $it['label'] }}</span>
                    @if(isset($it['kbd']))<span class="vtmed-dd-kbd">{{ $it['kbd'] }}</span>@endif
                </div>
            @endif
        @endforeach
    </div>
</div>
@endforeach
</div>{{-- end menubar --}}

{{-- TOOLBAR ROW 1 --}}
<div class="vtmed-toolbar">
    <button type="button" class="vtmed-tb-btn" data-cmd="undo" title="Hoàn tác (Ctrl+Z)"><i class="fa-solid fa-rotate-left"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="redo" title="Làm lại (Ctrl+Y)"><i class="fa-solid fa-rotate-right"></i></button>
    <div class="vtmed-tb-sep"></div>
    <select class="vtmed-tb-select" data-action="formatBlock" title="Kiểu đoạn" style="width:120px">
        <option value="p">Đoạn văn</option>
        <option value="h1">Tiêu đề 1</option>
        <option value="h2">Tiêu đề 2</option>
        <option value="h3">Tiêu đề 3</option>
        <option value="h4">Tiêu đề 4</option>
        <option value="h5">Tiêu đề 5</option>
        <option value="h6">Tiêu đề 6</option>
        <option value="blockquote">Trích dẫn</option>
        <option value="pre">Code</option>
    </select>
    <select class="vtmed-tb-select" data-action="fontName" title="Font chữ" style="width:110px">
        <option value="Roboto,sans-serif">Roboto</option>
        <option value="Arial,sans-serif">Arial</option>
        <option value="Georgia,serif">Georgia</option>
        <option value="'Times New Roman',serif">Times New Roman</option>
        <option value="'Courier New',monospace">Courier New</option>
        <option value="Verdana,sans-serif">Verdana</option>
    </select>
    <select class="vtmed-tb-select" data-action="fontSize" title="Cỡ chữ" style="width:68px">
        <option value="1">8px</option>
        <option value="2">10px</option>
        <option value="3" selected>12px</option>
        <option value="4">14px</option>
        <option value="5">18px</option>
        <option value="6">24px</option>
        <option value="7">36px</option>
    </select>
    <div class="vtmed-tb-sep"></div>
    <button type="button" class="vtmed-tb-btn" data-cmd="bold" title="Đậm (Ctrl+B)"><i class="fa-solid fa-bold"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="italic" title="Nghiêng (Ctrl+I)"><i class="fa-solid fa-italic"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="underline" title="Gạch dưới (Ctrl+U)"><i class="fa-solid fa-underline"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="strikeThrough" title="Gạch ngang"><i class="fa-solid fa-strikethrough"></i></button>
    <div class="vtmed-tb-sep"></div>
    <div class="vtmed-tb-dropdown-wrap">
        <button type="button" class="vtmed-tb-btn vtmed-tb-dropdown-btn" title="Căn chỉnh">
            <i class="fa-solid fa-align-left"></i><i class="fa-solid fa-chevron-down" style="font-size:9px;margin-left:2px"></i>
        </button>
        <div class="vtmed-tb-dropdown">
            <button type="button" class="vtmed-tb-dd-item" data-cmd="justifyLeft"><i class="fa-solid fa-align-left"></i> Căn trái</button>
            <button type="button" class="vtmed-tb-dd-item" data-cmd="justifyCenter"><i class="fa-solid fa-align-center"></i> Căn giữa</button>
            <button type="button" class="vtmed-tb-dd-item" data-cmd="justifyRight"><i class="fa-solid fa-align-right"></i> Căn phải</button>
            <button type="button" class="vtmed-tb-dd-item" data-cmd="justifyFull"><i class="fa-solid fa-align-justify"></i> Căn đều</button>
        </div>
    </div>
    <div class="vtmed-tb-sep"></div>
    <button type="button" class="vtmed-tb-btn" data-cmd="insertUnorderedList" title="Danh sách gạch đầu dòng"><i class="fa-solid fa-list-ul"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="insertOrderedList" title="Danh sách số"><i class="fa-solid fa-list-ol"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="outdent" title="Giảm thụt lề"><i class="fa-solid fa-outdent"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="indent" title="Tăng thụt lề"><i class="fa-solid fa-indent"></i></button>
</div>

{{-- TOOLBAR ROW 2 --}}
<div class="vtmed-toolbar vtmed-toolbar2">
    <button type="button" class="vtmed-tb-btn" data-action="insertLink" title="Chèn liên kết (Ctrl+K)"><i class="fa-solid fa-link"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="unlink" title="'Xóa' liên kết"><i class="fa-solid fa-unlink"></i></button>
    <button type="button" class="vtmed-tb-btn" data-action="insertImage" title="Chèn hình ảnh"><i class="fa-solid fa-image"></i></button>
    <button type="button" class="vtmed-tb-btn" data-action="insertTable" title="Chèn bảng"><i class="fa-solid fa-table"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="insertHorizontalRule" title="Kẻ ngang"><i class="fa-solid fa-minus"></i></button>
    <div class="vtmed-tb-sep"></div>
    <div class="vtmed-tb-dropdown-wrap">
        <button type="button" class="vtmed-tb-btn vtmed-color-tb-btn vtmed-tb-dropdown-btn" title="Màu chữ">
            <i class="fa-solid fa-font"></i>
            <div class="vtmed-color-bar" id="{{ $edId }}_fgbar" style="background:#000"></div>
            <i class="fa-solid fa-chevron-down" style="font-size:9px"></i>
        </button>
        <div class="vtmed-tb-dropdown vtmed-color-picker-drop">
            <div class="vtmed-color-grid" data-cmd="foreColor"></div>
            <div style="display:flex;align-items:center;gap:6px;padding:6px 8px;border-top:1px solid #e2e8f0">
                <label style="font-size:11px;color:#64748b">Tùy chỉnh:</label>
                <input type="color" class="vtmed-custom-color" data-cmd="foreColor" data-bar="{{ $edId }}_fgbar" value="#000000" style="width:32px;height:24px;border:none;cursor:pointer">
            </div>
        </div>
    </div>
    <div class="vtmed-tb-dropdown-wrap">
        <button type="button" class="vtmed-tb-btn vtmed-color-tb-btn vtmed-tb-dropdown-btn" title="Màu nền chữ">
            <i class="fa-solid fa-highlighter"></i>
            <div class="vtmed-color-bar" id="{{ $edId }}_bgbar" style="background:#ffff00"></div>
            <i class="fa-solid fa-chevron-down" style="font-size:9px"></i>
        </button>
        <div class="vtmed-tb-dropdown vtmed-color-picker-drop">
            <div class="vtmed-color-grid" data-cmd="hiliteColor"></div>
            <div style="display:flex;align-items:center;gap:6px;padding:6px 8px;border-top:1px solid #e2e8f0">
                <label style="font-size:11px;color:#64748b">Tùy chỉnh:</label>
                <input type="color" class="vtmed-custom-color" data-cmd="hiliteColor" data-bar="{{ $edId }}_bgbar" value="#ffff00" style="width:32px;height:24px;border:none;cursor:pointer">
            </div>
        </div>
    </div>
    <div class="vtmed-tb-sep"></div>
    <button type="button" class="vtmed-tb-btn" data-cmd="superscript" title="Chỉ số trên"><i class="fa-solid fa-superscript"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="subscript" title="Chỉ số dưới"><i class="fa-solid fa-subscript"></i></button>
    <button type="button" class="vtmed-tb-btn" data-cmd="removeFormat" title=""Xóa" định dạng"><i class="fa-solid fa-eraser"></i></button>
    <div class="vtmed-tb-sep"></div>
    <button type="button" class="vtmed-tb-btn" data-action="toggleSource" title="Mã nguồn HTML"><i class="fa-solid fa-code"></i></button>
    <button type="button" class="vtmed-tb-btn" data-action="toggleFullscreen" title="Toàn màn hình"><i class="fa-solid fa-expand"></i></button>
</div>

{{-- EDITOR BODY --}}
<div class="vtmed-body">
    <div class="vtmed-content" id="{{ $edId }}" contenteditable="true"
         data-placeholder="Nhập nội dung..."
         style="min-height:{{ $edH }}px"></div>
    <textarea class="vtmed-source-view" id="{{ $edId }}_src" style="display:none;min-height:{{ $edH }}px"></textarea>
</div>

{{-- STATUS BAR --}}
<div class="vtmed-statusbar">
    <span id="{{ $edId }}_path" class="vtmed-path">p</span>
    <span style="margin-left:auto;display:flex;gap:16px">
        <span id="{{ $edId }}_wc">0 từ</span>
        <span id="{{ $edId }}_cc">0 ký tự</span>
    </span>
</div>

{{-- Hidden textarea --}}
<textarea name="{{ $name ?? 'content' }}" id="{{ $edId }}_val" style="display:none">{{ old($name ?? 'content', $value ?? '') }}</textarea>

</div>{{-- end vtmed-wrap --}}

{{-- MODALS --}}
<div class="vtmed-overlay" id="{{ $edId }}_overlay" style="display:none" onclick="VTMed.closeModal('{{ $edId }}')"></div>

<div class="vtmed-modal" id="{{ $edId }}_modal_link" style="display:none">
    <div class="vtmed-modal-hd"><span>Chèn / 'Sửa' liên kết</span><button type="button" onclick="VTMed.closeModal('{{ $edId }}')" class="vtmed-modal-x"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="vtmed-modal-bd">
        <label class="vtmed-modal-label">URL <span style="color:red">*</span></label>
        <input type="url" id="{{ $edId }}_lnk_url" class="vtmed-modal-inp" placeholder="https://">
        <label class="vtmed-modal-label" style="margin-top:10px">Văn bản hiển thị</label>
        <input type="text" id="{{ $edId }}_lnk_txt" class="vtmed-modal-inp" placeholder="Nhập văn bản...">
        <label class="vtmed-modal-label" style="margin-top:10px">Tiêu đề (title)</label>
        <input type="text" id="{{ $edId }}_lnk_title" class="vtmed-modal-inp" placeholder="Tooltip khi hover...">
        <label class="vtmed-modal-label" style="margin-top:10px">Mở trong</label>
        <select id="{{ $edId }}_lnk_target" class="vtmed-modal-inp">
            <option value="_self">Cùng tab</option>
            <option value="_blank">Tab mới</option>
        </select>
    </div>
    <div class="vtmed-modal-ft">
        <button type="button" class="vtmed-modal-cancel" onclick="VTMed.closeModal('{{ $edId }}')">Hủy</button>
        <button type="button" class="vtmed-modal-ok" onclick="VTMed.confirmLink('{{ $edId }}')">Chèn liên kết</button>
    </div>
</div>

<div class="vtmed-modal" id="{{ $edId }}_modal_image" style="display:none">
    <div class="vtmed-modal-hd"><span>Chèn hình ảnh</span><button type="button" onclick="VTMed.closeModal('{{ $edId }}')" class="vtmed-modal-x"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="vtmed-modal-bd">
        <label class="vtmed-modal-label">URL hình ảnh <span style="color:red">*</span></label>
        <div style="display:flex;gap:8px">
            <input type="url" id="{{ $edId }}_img_url" class="vtmed-modal-inp" placeholder="https://..." style="flex:1" oninput="VTMed.previewImg('{{ $edId }}')">
            <button type="button" class="vtmed-modal-media-btn" onclick="VTMed.pickMedia('{{ $edId }}')"><i class="fa-solid fa-images"></i> Thư viện</button>
        </div>
        <div id="{{ $edId }}_img_prev" style="margin-top:8px;min-height:60px;background:#f8fafc;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px dashed #e2e8f0">
            <span style="color:#94a3b8;font-size:12px">'Xem' trước</span>
        </div>
        <label class="vtmed-modal-label" style="margin-top:10px">Alt text</label>
        <input type="text" id="{{ $edId }}_img_alt" class="vtmed-modal-inp" placeholder="Mô tả hình ảnh...">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:10px">
            <div><label class="vtmed-modal-label">Rộng</label><input type="text" id="{{ $edId }}_img_w" class="vtmed-modal-inp" placeholder="auto"></div>
            <div><label class="vtmed-modal-label">Cao</label><input type="text" id="{{ $edId }}_img_h" class="vtmed-modal-inp" placeholder="auto"></div>
            <div><label class="vtmed-modal-label">Căn</label>
                <select id="{{ $edId }}_img_align" class="vtmed-modal-inp">
                    <option value="">"Mặc định"</option>
                    <option value="left">Trái</option>
                    <option value="center">Giữa</option>
                    <option value="right">Phải</option>
                </select>
            </div>
        </div>
    </div>
    <div class="vtmed-modal-ft">
        <button type="button" class="vtmed-modal-cancel" onclick="VTMed.closeModal('{{ $edId }}')">Hủy</button>
        <button type="button" class="vtmed-modal-ok" onclick="VTMed.confirmImage('{{ $edId }}')">Chèn hình</button>
    </div>
</div>

<div class="vtmed-modal" id="{{ $edId }}_modal_table" style="display:none">
    <div class="vtmed-modal-hd"><span>Chèn bảng</span><button type="button" onclick="VTMed.closeModal('{{ $edId }}')" class="vtmed-modal-x"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="vtmed-modal-bd">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div><label class="vtmed-modal-label">Số hàng</label><input type="number" id="{{ $edId }}_tbl_r" class="vtmed-modal-inp" value="3" min="1" max="30"></div>
            <div><label class="vtmed-modal-label">Số cột</label><input type="number" id="{{ $edId }}_tbl_c" class="vtmed-modal-inp" value="3" min="1" max="15"></div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin-top:12px;cursor:pointer"><input type="checkbox" id="{{ $edId }}_tbl_hd" checked> Có hàng tiêu đề</label>
        <label style="display:flex;align-items:center;gap:8px;margin-top:8px;cursor:pointer"><input type="checkbox" id="{{ $edId }}_tbl_border" checked> Có viền</label>
        <div style="margin-top:10px"><label class="vtmed-modal-label">Chiều rộng</label>
            <input type="text" id="{{ $edId }}_tbl_w" class="vtmed-modal-inp" value="100%" placeholder="100% hoặc 600px">
        </div>
    </div>
    <div class="vtmed-modal-ft">
        <button type="button" class="vtmed-modal-cancel" onclick="VTMed.closeModal('{{ $edId }}')">Hủy</button>
        <button type="button" class="vtmed-modal-ok" onclick="VTMed.confirmTable('{{ $edId }}')">Chèn bảng</button>
    </div>
</div>

<div class="vtmed-modal vtmed-modal-find" id="{{ $edId }}_modal_find" style="display:none">
    <div class="vtmed-modal-hd"><span>Tìm & Thay thế</span><button type="button" onclick="VTMed.closeModal('{{ $edId }}')" class="vtmed-modal-x"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="vtmed-modal-bd">
        <label class="vtmed-modal-label">Tìm</label>
        <input type="text" id="{{ $edId }}_find_q" class="vtmed-modal-inp" placeholder="Nhập từ cần tìm...">
        <label class="vtmed-modal-label" style="margin-top:10px">Thay thế bằng</label>
        <input type="text" id="{{ $edId }}_find_r" class="vtmed-modal-inp" placeholder="Nhập từ thay thế...">
        <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer"><input type="checkbox" id="{{ $edId }}_find_case"> Phân biệt hoa/thường</label>
    </div>
    <div class="vtmed-modal-ft">
        <button type="button" class="vtmed-modal-cancel" onclick="VTMed.closeModal('{{ $edId }}')">Đóng</button>
        <button type="button" class="vtmed-modal-sec" onclick="VTMed.findReplace('{{ $edId }}',false)">Thay thế</button>
        <button type="button" class="vtmed-modal-ok" onclick="VTMed.findReplace('{{ $edId }}',true)">Thay tất cả</button>
    </div>
</div>

@once
@push('styles')
<style>
.vtmed-wrap{border:1.5px solid #d1d5db;border-radius:10px;overflow:hidden;background:#fff;font-family:'Be Vietnam Pro',sans-serif;font-size:13px}
.vtmed-wrap:focus-within{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.vtmed-menubar{display:flex;align-items:center;background:#f8fafc;border-bottom:1px solid #e5e7eb;padding:2px 6px;gap:2px;flex-wrap:wrap}
.vtmed-menu-item{position:relative}
.vtmed-menu-btn{background:none;border:none;padding:4px 8px;font-size:13px;color:#374151;cursor:pointer;border-radius:5px;font-family:inherit}
.vtmed-menu-btn:hover{background:#e5e7eb}
.vtmed-dropdown{display:none;position:absolute;top:100%;left:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:210px;z-index:9999;padding:4px 0}
.vtmed-menu-item.open>.vtmed-dropdown{display:block}
.vtmed-dd-item{display:flex;align-items:center;gap:8px;padding:7px 14px;cursor:pointer;color:#374151;white-space:nowrap;position:relative}
.vtmed-dd-item:hover{background:#f1f5f9}
.vtmed-dd-icon{width:18px;text-align:center;font-size:13px;flex-shrink:0;color:#6b7280}
.vtmed-dd-label{flex:1;font-size:13px}
.vtmed-dd-kbd{font-size:11px;color:#94a3b8;margin-left:auto;padding-left:12px}
.vtmed-dd-arrow{margin-left:auto;color:#94a3b8;font-size:11px}
.vtmed-dd-sep{height:1px;background:#f1f5f9;margin:3px 0}
.vtmed-dd-sub{position:relative}
.vtmed-subdropdown{display:none;position:absolute;left:100%;top:-4px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:180px;z-index:10000;padding:4px 0}
.vtmed-dd-sub:hover>.vtmed-subdropdown{display:block}
.vtmed-tg-drop{min-width:auto ;padding:8px }
.vtmed-tg-grid{display:grid;grid-template-columns:repeat(10,18px);gap:2px}
.vtmed-tg-cell{width:18px;height:18px;border:1px solid #d1d5db;border-radius:2px;cursor:pointer;background:#fff}
.vtmed-tg-cell.hover{background:#bfdbfe;border-color:#3b82f6}
.vtmed-tg-lbl{text-align:center;font-size:12px;color:#64748b;margin-top:6px}
.vtmed-toolbar{display:flex;align-items:center;background:#f8fafc;border-bottom:1px solid #e5e7eb;padding:4px 8px;gap:2px;flex-wrap:wrap}
.vtmed-toolbar2{border-bottom:1px solid #e5e7eb}
.vtmed-tb-btn{background:none;border:none;padding:5px 7px;border-radius:5px;cursor:pointer;color:#374151;display:inline-flex;align-items:center;gap:3px;font-size:13px;font-family:inherit;line-height:1}
.vtmed-tb-btn:hover{background:#e5e7eb}
.vtmed-tb-btn.active{background:#dbeafe;color:#2563eb}
.vtmed-tb-sep{width:1px;height:20px;background:#d1d5db;margin:0 4px;flex-shrink:0}
.vtmed-tb-select{border:1px solid #d1d5db;border-radius:5px;padding:4px 6px;font-size:12.5px;background:#fff;color:#374151;cursor:pointer;font-family:inherit;outline:none}
.vtmed-tb-select:focus{border-color:#3b82f6}
.vtmed-tb-dropdown-wrap{position:relative}
.vtmed-tb-dropdown{display:none;position:absolute;top:calc(100% + 4px);left:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:150px;z-index:9999;padding:4px 0}
.vtmed-tb-dropdown-wrap.open>.vtmed-tb-dropdown{display:block}
.vtmed-tb-dd-item{display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;color:#374151;font-size:13px;background:none;border:none;width:100%;text-align:left;font-family:inherit}
.vtmed-tb-dd-item:hover{background:#f1f5f9}
.vtmed-color-tb-btn{flex-direction:column;gap:1px;padding:4px 6px}
.vtmed-color-bar{width:18px;height:3px;border-radius:2px}
.vtmed-color-picker-drop{min-width:210px ;padding:8px }
.vtmed-color-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:3px;padding:4px}
.vtmed-color-swatch{width:18px;height:18px;border-radius:3px;cursor:pointer;border:1px solid rgba(0,0,0,.1);transition:transform .1s}
.vtmed-color-swatch:hover{transform:scale(1.2);z-index:1;position:relative}
.vtmed-body{position:relative}
.vtmed-content{padding:16px 20px;outline:none;line-height:1.75;color:#1e293b;font-size:15px;overflow-y:auto}
.vtmed-content:empty:before{content:attr(data-placeholder);color:#94a3b8;pointer-events:none}
.vtmed-content h1{font-size:2em;font-weight:700;margin:.8em 0 .4em}
.vtmed-content h2{font-size:1.5em;font-weight:700;margin:.8em 0 .4em}
.vtmed-content h3{font-size:1.25em;font-weight:600;margin:.8em 0 .4em}
.vtmed-content h4{font-size:1.1em;font-weight:600;margin:.8em 0 .4em}
.vtmed-content blockquote{border-left:4px solid #3b82f6;margin:1em 0;padding:8px 16px;background:#eff6ff;color:#1d4ed8;border-radius:0 8px 8px 0}
.vtmed-content pre{background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;font-size:13px;overflow-x:auto;font-family:monospace}
.vtmed-content table{border-collapse:collapse;width:100%;margin:1em 0}
.vtmed-content td,.vtmed-content th{border:1px solid #d1d5db;padding:8px 12px}
.vtmed-content th{background:#f8fafc;font-weight:600}
.vtmed-content img{max-width:100%;border-radius:6px}
.vtmed-content a{color:#2563eb;text-decoration:underline}
.vtmed-content hr{border:none;border-top:2px solid #e5e7eb;margin:1.5em 0}
.vtmed-source-view{width:100%;box-sizing:border-box;padding:16px;font-family:'Courier New',monospace;font-size:13px;border:none;outline:none;resize:vertical;background:#1e293b;color:#e2e8f0;line-height:1.6}
.vtmed-statusbar{display:flex;align-items:center;padding:4px 12px;background:#f8fafc;border-top:1px solid #e5e7eb;font-size:11.5px;color:#94a3b8;gap:8px}
.vtmed-path{color:#64748b}
.vtmed-wrap.vtmed-fullscreen{position:fixed ;inset:0 ;z-index:99999 ;border-radius:0 ;display:flex;flex-direction:column}
.vtmed-wrap.vtmed-fullscreen .vtmed-body{flex:1;overflow:hidden}
.vtmed-wrap.vtmed-fullscreen .vtmed-content{height:100%;overflow-y:auto}
.vtmed-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99998}
.vtmed-modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:99999;width:460px;max-width:95vw}
.vtmed-modal-find{width:400px}
.vtmed-modal-hd{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;font-weight:600;font-size:14px;color:#0f172a}
.vtmed-modal-x{background:none;border:none;cursor:pointer;font-size:16px;color:#94a3b8;padding:4px 8px;border-radius:4px}
.vtmed-modal-x:hover{background:#f1f5f9;color:#374151}
.vtmed-modal-bd{padding:20px}
.vtmed-modal-label{display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px}
.vtmed-modal-inp{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13.5px;outline:none;font-family:inherit;box-sizing:border-box}
.vtmed-modal-inp:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.vtmed-modal-ft{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9}
.vtmed-modal-cancel,.vtmed-modal-sec{background:#f1f5f9;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:13px;font-family:inherit;color:#374151}
.vtmed-modal-cancel:hover,.vtmed-modal-sec:hover{background:#e2e8f0}
.vtmed-modal-ok{background:#2563eb;color:#fff;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit}
.vtmed-modal-ok:hover{background:#1d4ed8}
.vtmed-modal-media-btn{background:#f1f5f9;border:1px solid #e2e8f0;padding:8px 12px;border-radius:8px;cursor:pointer;font-size:13px;white-space:nowrap;font-family:inherit;display:flex;align-items:center;gap:6px}
.vtmed-modal-media-btn:hover{background:#e2e8f0}
</style>
@endpush
@endonce

@once
@push('scripts')
@verbatim
<script>
window.VTMed = (function(){
    var COLORS = [
        '#000000','#434343','#666666','#999999','#b7b7b7','#cccccc','#d9d9d9','#ffffff',
        '#ff0000','#ff4500','#ff9900','#ffff00','#00ff00','#00ffff','#4a86e8','#0000ff',
        '#9900ff','#ff00ff','#e6b8a2','#f4cccc','#fce5cd','#fff2cc','#d9ead3','#d0e0e3',
        '#c9daf8','#cfe2f3','#d9d2e9','#ead1dc','#cc4125','#e06666','#f6b26b','#ffd966',
        '#93c47d','#76a5af','#6fa8dc','#6d9eeb','#8e7cc3','#c27ba0','#a61c00','#cc0000',
        '#e69138','#f1c232','#6aa84f','#45818e','#3c78d8','#3d85c6','#674ea7','#a64d79'
    ];

    function g(id) { return document.getElementById(id); }

    function init(id) {
        var wrap = g(id + '_wrap');
        if (!wrap) return;
        var ed = g(id);
        var val = g(id + '_val');

        // Load initial value
        if (val && val.value.trim()) ed.innerHTML = val.value;

        // Sync on input
        ed.addEventListener('input', function() { syncVal(id); updateStats(id); updatePath(id); });
        ed.addEventListener('keyup', function() { updateFmtState(id); updatePath(id); });
        ed.addEventListener('mouseup', function() { updateFmtState(id); updatePath(id); });

        // Keyboard shortcuts
        ed.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'b') { e.preventDefault(); execCmd(id, 'bold'); }
                else if (e.key === 'i') { e.preventDefault(); execCmd(id, 'italic'); }
                else if (e.key === 'u') { e.preventDefault(); execCmd(id, 'underline'); }
                else if (e.key === 'k') { e.preventDefault(); openModal(id, 'link'); }
                else if (e.key === 'z') { e.preventDefault(); execCmd(id, 'undo'); }
                else if (e.key === 'y') { e.preventDefault(); execCmd(id, 'redo'); }
                else if (e.key === 'h') { e.preventDefault(); openModal(id, 'find'); }
            }
        });

        // Menubar clicks
        wrap.querySelectorAll('.vtmed-menu-item').forEach(function(item) {
            item.querySelector('.vtmed-menu-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                var isOpen = item.classList.contains('open');
                wrap.querySelectorAll('.vtmed-menu-item.open').forEach(function(x) { x.classList.remove('open'); });
                if (!isOpen) item.classList.add('open');
            });
            item.querySelectorAll('.vtmed-dd-item[data-cmd]').forEach(function(dd) {
                dd.addEventListener('click', function(e) {
                    e.stopPropagation();
                    wrap.querySelectorAll('.vtmed-menu-item.open').forEach(function(x) { x.classList.remove('open'); });
                    handleAction(id, dd.dataset.cmd);
                });
            });
        });

        // Toolbar buttons
        wrap.querySelectorAll('.vtmed-tb-btn[data-cmd]').forEach(function(btn) {
            btn.addEventListener('click', function() { execCmd(id, btn.dataset.cmd); });
        });
        wrap.querySelectorAll('.vtmed-tb-btn[data-action]').forEach(function(btn) {
            btn.addEventListener('click', function() { handleAction(id, btn.dataset.action); });
        });

        // Toolbar dropdown toggles (align, color)
        wrap.querySelectorAll('.vtmed-tb-dropdown-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var dw = btn.closest('.vtmed-tb-dropdown-wrap');
                var isOpen = dw.classList.contains('open');
                wrap.querySelectorAll('.vtmed-tb-dropdown-wrap.open').forEach(function(x) { x.classList.remove('open'); });
                if (!isOpen) dw.classList.add('open');
            });
        });
        wrap.querySelectorAll('.vtmed-tb-dd-item[data-cmd]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                wrap.querySelectorAll('.vtmed-tb-dropdown-wrap.open').forEach(function(x) { x.classList.remove('open'); });
                execCmd(id, btn.dataset.cmd);
            });
        });

        // Select dropdowns
        wrap.querySelectorAll('select[data-action]').forEach(function(sel) {
            sel.addEventListener('change', function() {
                var action = sel.dataset.action;
                var v = sel.value;
                ed.focus();
                if (action === 'formatBlock') document.execCommand('formatBlock', false, '<' + v + '>');
                else if (action === 'fontSize') document.execCommand('fontSize', false, v);
                else if (action === 'fontName') document.execCommand('fontName', false, v);
                syncVal(id);
            });
        });

        // Color swatches
        wrap.querySelectorAll('.vtmed-color-grid').forEach(function(grid) {
            var cmd = grid.dataset.cmd;
            COLORS.forEach(function(c) {
                var sw = document.createElement('div');
                sw.className = 'vtmed-color-swatch';
                sw.style.background = c;
                sw.title = c;
                sw.addEventListener('click', function(e) {
                    e.stopPropagation();
                    wrap.querySelectorAll('.vtmed-tb-dropdown-wrap.open').forEach(function(x) { x.classList.remove('open'); });
                    ed.focus();
                    document.execCommand(cmd, false, c);
                    var barId = cmd === 'foreColor' ? id + '_fgbar' : id + '_bgbar';
                    var bar = g(barId);
                    if (bar) bar.style.background = c;
                    syncVal(id);
                });
                grid.appendChild(sw);
            });
        });

        // Custom color inputs
        wrap.querySelectorAll('.vtmed-custom-color').forEach(function(inp) {
            inp.addEventListener('input', function() {
                var bar = g(inp.dataset.bar);
                if (bar) bar.style.background = inp.value;
                ed.focus();
                document.execCommand(inp.dataset.cmd, false, inp.value);
                syncVal(id);
            });
        });

        // Table grid picker
        var tg = g(id + '_tg');
        var tgLbl = g(id + '_tg_lbl');
        if (tg) {
            for (var r = 0; r < 8; r++) {
                for (var c = 0; c < 10; c++) {
                    (function(row, col) {
                        var cell = document.createElement('div');
                        cell.className = 'vtmed-tg-cell';
                        cell.addEventListener('mouseover', function() {
                            tg.querySelectorAll('.vtmed-tg-cell').forEach(function(x, i) {
                                var xr = Math.floor(i / 10), xc = i % 10;
                                x.classList.toggle('hover', xr < row && xc < col);
                            });
                            if (tgLbl) tgLbl.textContent = row + ' x ' + col;
                        });
                        cell.addEventListener('click', function() {
                            wrap.querySelectorAll('.vtmed-menu-item.open').forEach(function(x) { x.classList.remove('open'); });
                            insertTable(id, row, col, true, true, '100%');
                        });
                        tg.appendChild(cell);
                    })(r + 1, c + 1);
                }
            }
        }

        // Close all on outside click
        document.addEventListener('click', function() {
            wrap.querySelectorAll('.vtmed-menu-item.open').forEach(function(x) { x.classList.remove('open'); });
            wrap.querySelectorAll('.vtmed-tb-dropdown-wrap.open').forEach(function(x) { x.classList.remove('open'); });
        });

        updateStats(id);
    }

    function execCmd(id, cmd) {
        var ed = g(id);
        if (!ed) return;
        ed.focus();
        document.execCommand(cmd, false, null);
        syncVal(id);
        updateFmtState(id);
    }

    function handleAction(id, action) {
        var simpleCommands = {
            'bold':1,'italic':1,'underline':1,'strikeThrough':1,'superscript':1,'subscript':1,
            'removeFormat':1,'undo':1,'redo':1,'cut':1,'copy':1,'paste':1,'selectAll':1,
            'insertUnorderedList':1,'insertOrderedList':1,'indent':1,'outdent':1,'unlink':1,
            'justifyLeft':1,'justifyCenter':1,'justifyRight':1,'justifyFull':1,'insertHorizontalRule':1
        };
        if (simpleCommands[action]) { execCmd(id, action); return; }
        switch (action) {
            case 'insertLink':       openModal(id, 'link'); break;
            case 'insertImage':      openModal(id, 'image'); break;
            case 'insertTable':      openModal(id, 'table'); break;
            case 'toggleSource':     toggleSource(id); break;
            case 'toggleFullscreen': toggleFullscreen(id); break;
            case 'find':             openModal(id, 'find'); break;
            case 'wordCount':        showWordCount(id); break;
            case 'print':            window.print(); break;
            case 'newDoc':
                if (confirm('Xóa' + ' toàn bộ nội dung?')) { var ed=g(id); if(ed) ed.innerHTML=''; syncVal(id); }
                break;
            case 'insertMedia':      pickMedia(id); break;
            case 'insertSpecialChar': insertSpecialChar(id); break;
            case 'insertPageBreak':
                insertHtml(id, '<div style="page-break-after:always;border-top:2px dashed #94a3b8;margin:1em 0"></div>'); break;
            case 'insertNbsp':       insertHtml(id, '&nbsp;'); break;
            case 'insertDate':       insertHtml(id, new Date().toLocaleDateString('vi-VN')); break;
            case 'insertTime':       insertHtml(id, new Date().toLocaleTimeString('vi-VN')); break;
            case 'insertDateTime':   insertHtml(id, new Date().toLocaleString('vi-VN')); break;
            case 'tableAddRowBefore': tableOp(id, 'addRowBefore'); break;
            case 'tableAddRowAfter':  tableOp(id, 'addRowAfter'); break;
            case 'tableDelRow':       tableOp(id, 'delRow'); break;
            case 'tableAddColBefore': tableOp(id, 'addColBefore'); break;
            case 'tableAddColAfter':  tableOp(id, 'addColAfter'); break;
            case 'tableDelCol':       tableOp(id, 'delCol'); break;
            case 'tableDelTable':     tableOp(id, 'delTable'); break;
        }
    }

    function insertHtml(id, html) {
        var ed = g(id);
        if (!ed) return;
        ed.focus();
        document.execCommand('insertHTML', false, html);
        syncVal(id);
    }

    function syncVal(id) {
        var ed = g(id), val = g(id + '_val');
        if (ed && val) val.value = ed.innerHTML;
    }

    function updateStats(id) {
        var ed = g(id);
        if (!ed) return;
        var text = ed.innerText || '';
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        var wc = g(id + '_wc'), cc = g(id + '_cc');
        if (wc) wc.textContent = words + ' từ';
        if (cc) cc.textContent = text.length + ' ký tự';
    }

    function updatePath(id) {
        var sel = window.getSelection();
        if (!sel || !sel.anchorNode) return;
        var node = sel.anchorNode.nodeType === 3 ? sel.anchorNode.parentNode : sel.anchorNode;
        var path = [], cur = node, ed = g(id);
        while (cur && cur !== ed && cur.tagName) { path.unshift(cur.tagName.toLowerCase()); cur = cur.parentNode; }
        var el = g(id + '_path');
        if (el) el.textContent = path.join(' > ') || 'p';
    }

    function updateFmtState(id) {
        var wrap = g(id + '_wrap');
        if (!wrap) return;
        ['bold','italic','underline','strikeThrough','superscript','subscript'].forEach(function(cmd) {
            var btn = wrap.querySelector('[data-cmd="' + cmd + '"]');
            if (btn) btn.classList.toggle('active', document.queryCommandState(cmd));
        });
    }

    function showWordCount(id) {
        var ed = g(id);
        if (!ed) return;
        var text = ed.innerText || '';
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        alert('Số từ: ' + words + '\nSố ký tự: ' + text.length);
    }

    function toggleSource(id) {
        var ed = g(id), src = g(id + '_src'), val = g(id + '_val');
        if (!ed || !src) return;
        if (src.style.display === 'none') {
            src.value = ed.innerHTML;
            ed.style.display = 'none';
            src.style.display = 'block';
            src.oninput = function() { ed.innerHTML = src.value; if (val) val.value = src.value; };
        } else {
            ed.innerHTML = src.value;
            src.style.display = 'none';
            ed.style.display = 'block';
            syncVal(id);
        }
    }

    function toggleFullscreen(id) {
        var wrap = g(id + '_wrap');
        if (!wrap) return;
        wrap.classList.toggle('vtmed-fullscreen');
        var btn = wrap.querySelector('[data-action="toggleFullscreen"]');
        if (btn) {
            var icon = btn.querySelector('i');
            if (icon) icon.className = wrap.classList.contains('vtmed-fullscreen') ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
        }
    }

    function openModal(id, type) {
        var overlay = g(id + '_overlay'), modal = g(id + '_modal_' + type);
        if (overlay) overlay.style.display = 'block';
        if (modal) modal.style.display = 'block';
        if (type === 'link') {
            var sel = window.getSelection();
            var txtEl = g(id + '_lnk_txt');
            if (txtEl && sel && sel.toString()) txtEl.value = sel.toString();
            var node = sel && sel.anchorNode ? (sel.anchorNode.nodeType === 3 ? sel.anchorNode.parentNode : sel.anchorNode) : null;
            var a = node && node.closest ? node.closest('a') : null;
            if (a) {
                var urlEl = g(id + '_lnk_url');
                if (urlEl) urlEl.value = a.href;
                if (txtEl) txtEl.value = a.textContent;
                var titleEl = g(id + '_lnk_title'); if (titleEl) titleEl.value = a.title || '';
                var targetEl = g(id + '_lnk_target'); if (targetEl) targetEl.value = a.target || '_self';
            }
        }
    }

    function closeModal(id) {
        var overlay = g(id + '_overlay');
        if (overlay) overlay.style.display = 'none';
        ['link','image','table','find'].forEach(function(t) {
            var m = g(id + '_modal_' + t);
            if (m) m.style.display = 'none';
        });
    }

    function confirmLink(id) {
        var url = (g(id + '_lnk_url') || {}).value || '';
        if (!url) { alert('Vui lòng nhập URL'); return; }
        var txt = (g(id + '_lnk_txt') || {}).value || url;
        var title = (g(id + '_lnk_title') || {}).value || '';
        var target = (g(id + '_lnk_target') || {}).value || '_self';
        var ed = g(id); if (!ed) return;
        ed.focus();
        document.execCommand('insertHTML', false, '<a href="' + url + '"' + (title ? ' title="' + title + '"' : '') + ' target="' + target + '">' + txt + '</a>');
        syncVal(id); closeModal(id);
        ['lnk_url','lnk_txt','lnk_title'].forEach(function(f) { var el = g(id + '_' + f); if (el) el.value = ''; });
    }

    function confirmImage(id) {
        var url = (g(id + '_img_url') || {}).value || '';
        if (!url) { alert('Vui lòng nhập URL hình ảnh'); return; }
        var alt = (g(id + '_img_alt') || {}).value || '';
        var w = (g(id + '_img_w') || {}).value || '';
        var h = (g(id + '_img_h') || {}).value || '';
        var align = (g(id + '_img_align') || {}).value || '';
        var style = '';
        if (w) style += 'width:' + w + ';';
        if (h) style += 'height:' + h + ';';
        if (align === 'center') style += 'display:block;margin:0 auto;';
        else if (align === 'left') style += 'float:left;margin:0 12px 8px 0;';
        else if (align === 'right') style += 'float:right;margin:0 0 8px 12px;';
        var ed = g(id); if (!ed) return;
        ed.focus();
        document.execCommand('insertHTML', false, '<img src="' + url + '" alt="' + alt + '"' + (style ? ' style="' + style + '"' : '') + ' />');
        syncVal(id); closeModal(id);
        ['img_url','img_alt','img_w','img_h'].forEach(function(f) { var el = g(id + '_' + f); if (el) el.value = ''; });
        var prev = g(id + '_img_prev');
        if (prev) prev.innerHTML = '<span style="color:#94a3b8;font-size:12px">' + 'Xem' + ' trước</span>';
    }

    function confirmTable(id) {
        var rows = parseInt((g(id + '_tbl_r') || {}).value || 3);
        var cols = parseInt((g(id + '_tbl_c') || {}).value || 3);
        var hasHead = (g(id + '_tbl_hd') || {}).checked !== false;
        var hasBorder = (g(id + '_tbl_border') || {}).checked !== false;
        var width = (g(id + '_tbl_w') || {}).value || '100%';
        closeModal(id);
        insertTable(id, rows, cols, hasHead, hasBorder, width);
    }

    function insertTable(id, rows, cols, hasHead, hasBorder, width) {
        var border = hasBorder ? '1' : '0';
        var html = '<table border="' + border + '" style="border-collapse:collapse;width:' + width + '">';
        if (hasHead) {
            html += '<thead><tr>';
            for (var c = 0; c < cols; c++) html += '<th style="border:1px solid #d1d5db;padding:8px 12px;background:#f8fafc">Tiêu đề ' + (c + 1) + '</th>';
            html += '</tr></thead>';
        }
        html += '<tbody>';
        var dataRows = hasHead ? Math.max(rows - 1, 1) : rows;
        for (var r = 0; r < dataRows; r++) {
            html += '<tr>';
            for (var c2 = 0; c2 < cols; c2++) html += '<td style="border:1px solid #d1d5db;padding:8px 12px">&nbsp;</td>';
            html += '</tr>';
        }
        html += '</tbody></table><p><br></p>';
        var ed = g(id); if (!ed) return;
        ed.focus();
        document.execCommand('insertHTML', false, html);
        syncVal(id);
    }

    function findReplace(id, replaceAll) {
        var q = (g(id + '_find_q') || {}).value || '';
        var r = (g(id + '_find_r') || {}).value || '';
        var cs = (g(id + '_find_case') || {}).checked;
        if (!q) return;
        var ed = g(id); if (!ed) return;
        var flags = replaceAll ? (cs ? 'g' : 'gi') : (cs ? '' : 'i');
        var regex = new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), flags);
        ed.innerHTML = ed.innerHTML.replace(regex, r);
        syncVal(id);
        if (!replaceAll) closeModal(id);
    }

    function tableOp(id, op) {
        var sel = window.getSelection();
        if (!sel || !sel.anchorNode) return;
        var node = sel.anchorNode.nodeType === 3 ? sel.anchorNode.parentNode : sel.anchorNode;
        var td = node.closest ? node.closest('td,th') : null;
        if (!td) return;
        var tr = td.parentNode;
        var table = tr.closest('table');
        var colIdx = Array.from(tr.children).indexOf(td);
        if (op === 'addRowBefore' || op === 'addRowAfter') {
            var newTr = tr.cloneNode(true);
            newTr.querySelectorAll('td,th').forEach(function(c) { c.innerHTML = '&nbsp;'; });
            tr.parentNode.insertBefore(newTr, op === 'addRowBefore' ? tr : tr.nextSibling);
        } else if (op === 'delRow') {
            if (table.querySelectorAll('tr').length > 1) tr.remove();
        } else if (op === 'addColBefore' || op === 'addColAfter') {
            table.querySelectorAll('tr').forEach(function(row) {
                var ref = row.children[colIdx];
                var newCell = document.createElement(ref && ref.tagName === 'TH' ? 'th' : 'td');
                newCell.style.cssText = 'border:1px solid #d1d5db;padding:8px 12px';
                newCell.innerHTML = '&nbsp;';
                row.insertBefore(newCell, op === 'addColBefore' ? ref : (ref ? ref.nextSibling : null));
            });
        } else if (op === 'delCol') {
            table.querySelectorAll('tr').forEach(function(row) { if (row.children[colIdx]) row.children[colIdx].remove(); });
        } else if (op === 'delTable') {
            table.remove();
        }
        syncVal(id);
    }

    function pickMedia(id) {
        if (typeof openMediaPicker === 'function') {
            openMediaPicker(null, function(url) {
                var urlEl = g(id + '_img_url');
                if (urlEl) { 
                    urlEl.value = url; 
                    previewImg(id); 
                } else { 
                    insertHtml(id, '<img src="' + url + '" alt="" style="max-width:100%" />'); 
                }
            });
        }
    }

    function previewImg(id) {
        var url = (g(id + '_img_url') || {}).value || '';
        var prev = g(id + '_img_prev');
        if (!prev) return;
        prev.innerHTML = url
            ? '<img src="' + url + '" style="max-height:120px;border-radius:6px;object-fit:contain" onerror="this.style.display=\'none\'">'
            : '<span style="color:#94a3b8;font-size:12px">' + 'Xem' + ' trước</span>';
    }

    function insertSpecialChar(id) {
        var chars = ['©','®','™','€','£','¥','§','¶','†','‡','•','…','–','—',
                     '←','→','↑','↓','↔','⇒','⇐','⇔','≠','≤','≥','±','×','÷',
                     'α','β','γ','δ','π','Ω','∞','∑','√','∫','°','′','″'];
        var html = '<div style="display:grid;grid-template-columns:repeat(8,32px);gap:4px;padding:8px">';
        chars.forEach(function(c) {
            html += '<button type="button" onclick="VTMed.insertHtml(\'' + id + '\',\'' + c + '\');VTMed.closeModal(\'' + id + '\')" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:4px;cursor:pointer;font-size:16px;background:#fff" title="' + c + '">' + c + '</button>';
        });
        html += '</div>';
        var overlay = g(id + '_overlay');
        if (overlay) overlay.style.display = 'block';
        var modal = g(id + '_modal_find');
        if (modal) {
            modal.querySelector('.vtmed-modal-hd span').textContent = 'Ký tự đặc biệt';
            modal.querySelector('.vtmed-modal-bd').innerHTML = html;
            modal.querySelector('.vtmed-modal-ft').innerHTML = '<button type="button" class="vtmed-modal-cancel" onclick="VTMed.closeModal(\'' + id + '\')">Đóng</button>';
            modal.style.display = 'block';
        }
    }

    // Public API
    return {
        init: init,
        execCmd: execCmd,
        handleAction: handleAction,
        insertHtml: insertHtml,
        syncVal: syncVal,
        toggleSource: toggleSource,
        toggleFullscreen: toggleFullscreen,
        openModal: openModal,
        closeModal: closeModal,
        confirmLink: confirmLink,
        confirmImage: confirmImage,
        confirmTable: confirmTable,
        findReplace: findReplace,
        tableOp: tableOp,
        pickMedia: pickMedia,
        previewImg: previewImg,
        insertSpecialChar: insertSpecialChar,
        setContent: function(id, html) {
            var ed = g(id);
            if (ed) { ed.innerHTML = html || ''; syncVal(id); updateStats(id); }
        }
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.vtmed-content[id]').forEach(function(el) {
        VTMed.init(el.id);
    });
});
</script>
@endverbatim
@endpush
@endonce



@props(['label' => '', 'prefix' => 'address', 'address' => []])

@if($label)
    <h4 class="font-medium text-gray-700 mb-2">{{ $label }}</h4>
@endif
<div class="space-y-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">Tỉnh/Thành phố</label>
        <select name="{{ $prefix }}[city]" data-old="{{ old($prefix . '.city', $address['city'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg location-select" data-level="1" data-target="#{{ $prefix }}_state"></select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Quận/Huyện</label>
        <select id="{{ $prefix }}_state" name="{{ $prefix }}[state]" data-old="{{ old($prefix . '.state', $address['state'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg location-select" data-level="2" data-target="#{{ $prefix }}_ward"></select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Phường/Xã</label>
        <select id="{{ $prefix }}_ward" name="{{ $prefix }}[ward]" data-old="{{ old($prefix . '.ward', $address['ward'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg location-select" data-level="3" data-target=""></select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Số nhà, Tên đường</label>
        <input type="text" name="{{ $prefix }}[address]" value="{{ old($prefix . '.address', $address['address'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Mã bưu điện</label>
            <input type="text" name="{{ $prefix }}[postal_code]" value="{{ old($prefix . '.postal_code', $address['postal_code'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Quốc gia</label>
            <input type="text" name="{{ $prefix }}[country]" value="{{ old($prefix . '.country', $address['country'] ?? 'Việt Nam') }}" class="w-full px-3 py-2 border rounded-lg">
        </div>
    </div>
</div>

@pushOnce('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: 12px;
        color: #4b5563;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.location-select').select2({
        width: '100%',
        placeholder: "Vui lòng chọn...",
        language: {
            noResults: function() {
                return "Không tìm thấy kết quả";
            }
        }
    });

    let locationData = null;
    let locationPromise = null;

    function fetchLocationData() {
        if (locationData) return Promise.resolve(locationData);
        if (!locationPromise) {
            locationPromise = $.getJSON('/data/vietnam-provinces.json').then(data => {
                locationData = data;
                return data;
            });
        }
        return locationPromise;
    }

    function loadLocation(level, parentId, selectElement, selectedName, callback) {
        fetchLocationData().then(data => {
            let items = [];
            
            if (level === 1) {
                items = data;
            } else if (level === 2) {
                let province = data.find(p => p.code == parentId);
                if (province && province.districts) {
                    items = province.districts;
                }
            } else if (level === 3) {
                let district = null;
                for (let p of data) {
                    if (p.districts) {
                        district = p.districts.find(d => d.code == parentId);
                        if (district) break;
                    }
                }
                if (district && district.wards) {
                    items = district.wards;
                }
            }
            
            let html = '<option value="">Chọn...</option>';
            items.forEach(item => {
                let selected = (item.name === selectedName) ? 'selected' : '';
                html += `<option value="${item.name}" data-id="${item.code}" ${selected}>${item.name}</option>`;
            });
            
            selectElement.html(html);
            selectElement.trigger('change.select2'); // update select2 UI
            
            if (callback) callback();
        }).catch(err => {
            console.error('Error loading location data:', err);
        });
    }

    // Initialize all city selectors
    $('select[data-level="1"]').each(function() {
        let select = $(this);
        let oldName = select.data('old');
        loadLocation(1, 0, select, oldName, function() {
            if(oldName) select.trigger('change', [true]); // true = isInitial
        });
    });

    // Handle change for city and state
    $(document).on('change', '.location-select', function(e, isInitial) {
        let select = $(this);
        let level = parseInt(select.data('level'));
        
        if (level === 3) return; // Ward has no children to load
        
        let target = $(select.data('target'));
        if (!target.length) return;
        
        let id = select.find('option:selected').data('id');
        
        if(!id) {
            target.html('<option value="">Chọn...</option>').trigger('change.select2');
            if(level == 1) {
                let subTarget = $(target.data('target'));
                if (subTarget.length) subTarget.html('<option value="">Chọn...</option>').trigger('change.select2');
            }
            return;
        }

        let oldName = isInitial ? target.data('old') : '';
        loadLocation(level + 1, id, target, oldName, function() {
            if(oldName && level == 1) {
                target.trigger('change', [true]);
            }
        });
    });
});
</script>
@endpushOnce

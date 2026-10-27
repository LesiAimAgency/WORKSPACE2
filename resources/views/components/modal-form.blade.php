@props(['modal'])

<div id="modalForm-{{ $modal['id'] }}" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog bg_image" 
         @if(!empty($modal['config']['styles']['background_image']))
         style="background-image: url('{{ $modal['config']['styles']['background_image'] }}'); background-size: cover; background-position: center;"
         @endif>
        <div class="modal-content" 
             style="background-color: {{ $modal['config']['styles']['background_color'] ?? '#ffffff' }}; 
                    color: {{ $modal['config']['styles']['text_color'] ?? '#000000' }};">
            
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-light fa-x"></i>
                </button>
            </div>
            
            <div class="modal-body text-center">
                <div class="inner-content">
                    <div class="content">
                        @if(!empty($modal['config']['content']['pre_title']))
                            <span class="pre-title">{{ $modal['config']['content']['pre_title'] }}</span>
                        @endif
                        
                        @if(!empty($modal['config']['content']['main_title']))
                            <h1 class="title">{!! nl2br(e($modal['config']['content']['main_title'])) !!}</h1>
                        @endif
                        
                        @if(!empty($modal['config']['content']['description']))
                            <p class="disc">{!! nl2br(e($modal['config']['content']['description'])) !!}</p>
                        @endif

                        <!-- Form Fields -->
                        @if(!empty($modal['config']['fields']))
                            <form id="modalForm-{{ $modal['id'] }}-form" class="modal-form mt-4">
                                @csrf
                                <div class="row g-3">
                                    @foreach($modal['config']['fields'] as $field)
                                        <div class="col-{{ $field['width'] ?? '12' }}">
                                            @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'tel' || $field['type'] === 'number')
                                                <input 
                                                    type="{{ $field['type'] }}" 
                                                    name="{{ $field['name'] }}" 
                                                    class="form-control" 
                                                    placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                    @if($field['required'] ?? false) required @endif>
                                            @elseif($field['type'] === 'textarea')
                                                <textarea 
                                                    name="{{ $field['name'] }}" 
                                                    class="form-control" 
                                                    rows="{{ $field['rows'] ?? 3 }}"
                                                    placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                    @if($field['required'] ?? false) required @endif></textarea>
                                            @elseif($field['type'] === 'select')
                                                <select 
                                                    name="{{ $field['name'] }}" 
                                                    class="form-control"
                                                    @if($field['required'] ?? false) required @endif>
                                                    <option value="">{{ $field['placeholder'] ?? 'Chọn...' }}</option>
                                                    @foreach($field['options'] ?? [] as $option)
                                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($field['type'] === 'checkbox')
                                                <div class="form-check">
                                                    <input 
                                                        type="checkbox" 
                                                        name="{{ $field['name'] }}" 
                                                        value="1"
                                                        class="form-check-input" 
                                                        id="field-{{ $field['name'] }}"
                                                        @if($field['required'] ?? false) required @endif>
                                                    <label class="form-check-label" for="field-{{ $field['name'] }}">
                                                        {{ $field['label'] }}
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        Gửi thông tin
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- Action Buttons -->
                        <div class="rts-btn-banner-area mt-4">
                            @if(!empty($modal['config']['content']['button_text']))
                                <a href="{{ $modal['config']['content']['button_link'] ?? '#' }}" 
                                   class="rts-btn btn-primary radious-sm with-icon"
                                   style="background-color: {{ $modal['config']['styles']['button_color'] ?? '#007bff' }};">
                                    <div class="btn-text">{{ $modal['config']['content']['button_text'] }}</div>
                                    <div class="arrow-icon"><i class="fa-light fa-arrow-right"></i></div>
                                    <div class="arrow-icon"><i class="fa-light fa-arrow-right"></i></div>
                                </a>
                            @endif
                            
                            @if(!empty($modal['config']['content']['price_value']))
                                <div class="price-area">
                                    @if(!empty($modal['config']['content']['price_text']))
                                        <span>{{ $modal['config']['content']['price_text'] }}</span>
                                    @endif
                                    <h3 class="title animated fadeIn">{{ $modal['config']['content']['price_value'] }}</h3>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modalForm-{{ $modal['id'] }}-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('/modal-forms/{{ $modal['id'] }}/submit', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalForm-{{ $modal['id'] }}')).hide();
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        });
    }
});
</script>
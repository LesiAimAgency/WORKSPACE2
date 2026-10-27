@extends('cms.layouts.app')

@section('title', 'Quản lý Form')
@section('page-title', 'Form Builder')

@section('content')
@include('cms.settings.partials.back-link')

@php
  $settingsSaveUrl = route('admin.settings.save');
  
  $formsData = setting('forms', []);
  if (is_string($formsData)) {
    $formsData = json_decode($formsData, true) ?: [];
  }
  if (!is_array($formsData)) {
    $formsData = [];
  }
@endphp

<div class="bg-white rounded-lg shadow-sm p-6" x-data="formManager()">
  <div class="flex items-center justify-between mb-6">
    <h3 class="font-semibold text-lg">Danh sách Form</h3>
    <button type="button" @click="showEditor = true; editIndex = null; resetForm()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
      + Tạo Form
    </button>
  </div>

  <!-- Form Editor Modal -->
  <div x-show="showEditor" x-cloak
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-xl"
       @click.stop>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-lg" x-text="editIndex !== null ? 'Sửa Form' : 'Tạo Form Mới'"></h3>
        <button type="button" @click="showEditor = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tên Form <span class="text-red-500">*</span></label>
          <input type="text" x-model="currentForm.name" 
              placeholder="VD: Form liên hệ, Form đăng ký..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex items-center justify-between pt-2">
          <h4 class="font-medium text-gray-700">Các trường</h4>
          <button type="button" @click="showFieldSelector = !showFieldSelector" 
              class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors">
            + Thêm trường
          </button>
        </div>
        
        <!-- Field Selector -->
        <div x-show="showFieldSelector" x-collapse class="border border-gray-200 rounded-lg p-4 bg-gray-50">
          <p class="text-sm text-gray-600 mb-3">Chọn loại trường:</p>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <button type="button" @click="addField('text')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium"> Text</span>
              <p class="text-xs text-gray-500">Văn bản ngắn</p>
            </button>
            <button type="button" @click="addField('email')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium"> Email</span>
              <p class="text-xs text-gray-500">Địa chỉ email</p>
            </button>
            <button type="button" @click="addField('phone')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium"> Phone</span>
              <p class="text-xs text-gray-500">Số điện thoại</p>
            </button>
            <button type="button" @click="addField('textarea')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium"> Textarea</span>
              <p class="text-xs text-gray-500">Văn bản dài</p>
            </button>
            <button type="button" @click="addField('select')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium"> Select</span>
              <p class="text-xs text-gray-500">Danh sách chọn</p>
            </button>
            <button type="button" @click="addField('checkbox')" 
                class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-left">
              <span class="font-medium">️ Checkbox</span>
              <p class="text-xs text-gray-500">Hộp kiểm</p>
            </button>
          </div>
        </div>
        
        <!-- Fields List -->
        <div class="space-y-3">
          <template x-for="(field, index) in currentForm.fields" :key="index">
            <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-medium" x-text="index + 1"></span>
                  <span class="font-medium text-sm text-gray-700" x-text="field.label || 'Trường chưa đặt tên'"></span>
                  <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded" x-text="field.type"></span>
                </div>
                <button type="button" @click="removeField(index)" 
                    class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Xóa
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs text-gray-500 mb-1">Nhãn</label>
                  <input type="text" x-model="field.label" placeholder="VD: Họ tên" 
                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                  <label class="block text-xs text-gray-500 mb-1">Placeholder</label>
                  <input type="text" x-model="field.placeholder" placeholder="VD: Nhập họ tên..." 
                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end">
                  <label class="flex items-center text-sm cursor-pointer">
                    <input type="checkbox" x-model="field.required" class="w-4 h-4 text-blue-600 rounded mr-2">
                    <span>Bắt buộc</span>
                  </label>
                </div>
              </div>
              <template x-if="field.type === 'select'">
                <div class="mt-3">
                  <label class="block text-xs text-gray-500 mb-1">Các tùy chọn (mỗi dòng 1 tùy chọn)</label>
                  <textarea x-model="field.options" placeholder="Tùy chọn 1&#10;Tùy chọn 2&#10;Tùy chọn 3" rows="3" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
              </template>
            </div>
          </template>
          
          <div x-show="currentForm.fields.length === 0" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>Chưa có trường nào</p>
            <p class="text-sm">Nhấn "Thêm trường" để bắt đầu</p>
          </div>
        </div>
      </div>
      
      <div class="flex gap-3 mt-6 pt-4 border-t">
        <button type="button" @click="saveForm()" 
            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
          <span x-text="editIndex !== null ? ' Cập nhật' : ' Tạo Form'"></span>
        </button>
        <button type="button" @click="showEditor = false" 
            class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          Hủy
        </button>
      </div>
    </div>
  </div>

  <!-- Forms List -->
  <div x-show="forms.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <template x-for="(form, index) in forms" :key="index">
      <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:shadow-md transition-all bg-white">
        <div class="flex items-start justify-between mb-3">
          <h4 class="font-medium text-gray-800" x-text="form.name"></h4>
          <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" x-text="'#' + (index + 1)"></span>
        </div>
        <p class="text-sm text-gray-500 mb-4">
          <span x-text="form.fields ? form.fields.length : 0"></span> trường
        </p>
        <div class="flex gap-3 pt-3 border-t border-gray-100">
          <button type="button" @click="editForm(index)" 
              class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            ️ Sửa
          </button>
          <button type="button" @click="deleteForm(index)" 
              class="text-sm text-red-500 hover:text-red-700 font-medium">
            ️ Xóa
          </button>
        </div>
      </div>
    </template>
  </div>
  
  <div x-show="forms.length === 0" class="text-center py-12 text-gray-500">
    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <p class="text-lg font-medium mb-1">Chưa có form nào</p>
    <p class="text-sm">Nhấn "Tạo Form" để bắt đầu tạo form mới</p>
  </div>

  <!-- Save All Button -->
  <form id="saveFormsForm" method="POST" action="{{ $settingsSaveUrl }}" x-ref="saveForm">
    @csrf
    <input type="hidden" name="forms" x-ref="formsInput" :value="JSON.stringify(forms)">
    <div class="flex justify-end pt-4 border-t">
      <button type="submit" 
          @click="$refs.formsInput.value = JSON.stringify(forms)"
          class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
         Lưu tất cả Forms
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script>
function formManager() {
  return {
    forms: @json($formsData),
    showEditor: false,
    showFieldSelector: false,
    editIndex: null,
    currentForm: { name: '', fields: [] },
    
    resetForm() {
      this.currentForm = { name: '', fields: [] };
      this.showFieldSelector = false;
    },
    
    addField(type) {
      this.currentForm.fields.push({
        type: type,
        label: '',
        placeholder: '',
        required: false,
        options: type === 'select' ? 'Tùy chọn 1\nTùy chọn 2' : ''
      });
      this.showFieldSelector = false;
    },
    
    removeField(index) {
      this.currentForm.fields.splice(index, 1);
    },
    
    saveForm() {
      if (!this.currentForm.name.trim()) {
        alert('Vui lòng nhập tên form');
        return;
      }
      
      // Validate at least one field
      if (this.currentForm.fields.length === 0) {
        alert('Vui lòng thêm ít nhất 1 trường');
        return;
      }
      
      if (this.editIndex !== null) {
        this.forms[this.editIndex] = JSON.parse(JSON.stringify(this.currentForm));
      } else {
        this.forms.push(JSON.parse(JSON.stringify(this.currentForm)));
      }
      this.showEditor = false;
      this.resetForm();
    },
    
    editForm(index) {
      this.editIndex = index;
      this.currentForm = JSON.parse(JSON.stringify(this.forms[index]));
      this.showEditor = true;
    },
    
    deleteForm(index) {
      if (confirm('Bạn có chắc muốn xóa form này?')) {
        this.forms.splice(index, 1);
      }
    }
  };
}
</script>
@endpush

<style>
[x-cloak] { display: none; }
</style>
@endsection

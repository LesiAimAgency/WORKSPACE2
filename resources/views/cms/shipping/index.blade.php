@extends('cms.layouts.app')

@section('title', 'Hệ thống tính phí vận chuyển')
@section('page-title', 'Vận chuyển')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="shippingEngine()">
  
  <!-- Page header -->
  <div class="sm:flex sm:justify-between sm:items-center mb-8">
    <div class="mb-4 sm:mb-0">
      <h1 class="text-2xl md:text-3xl text-slate-800 font-bold tracking-tight">Hệ thống Cước Vận Chuyển </h1>
      <p class="text-slate-500 mt-1">Quản lý các đối tác giao hàng và quy tắc tính phí tự động.</p>
    </div>
    
    <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-3">
      <button @click="testModal = true" class="px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 text-indigo-600 text-sm font-medium rounded-lg shadow-sm transition-all inline-flex items-center gap-2">
        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
          <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
        </svg>
        <span>Thử Tính Phí (Calculator)</span>
      </button>
    </div>
  </div>

  <!-- Livewire Professional Dashboard -->
  <livewire:admin.shipping.professional-dashboard />

  <!-- Professional Calculator Modal (Receipt Style) -->
  <div x-show="testModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="testModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="testModal = false"></div>

      <div x-show="testModal" class="inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-transparent rounded-2xl shadow-2xl relative z-10" style="margin-top: 5vh;">
        <div class="grid grid-cols-1 lg:grid-cols-5 bg-white rounded-2xl overflow-hidden">
          
          <!-- Left: Input Form -->
          <div class="lg:col-span-3 p-8 bg-white">
            <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
              <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
              </span>
              Simulate Checkout
            </h3>
            
            <div class="space-y-5">
              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Giá trị đơn hàng (VND)</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-slate-400 font-medium">₫</span>
                    </div>
                    <input type="number" x-model="calcForm.order_value" class="form-input w-full pl-8 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                  </div>
                </div>
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tiền thu hộ COD (VND)</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-slate-400 font-medium">₫</span>
                    </div>
                    <input type="number" x-model="calcForm.cod_amount" class="form-input w-full pl-8 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                  </div>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Khoảng cách giao (km)</label>
                  <input type="number" step="0.1" x-model="calcForm.distance" class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                </div>
                <div class="space-y-1">
                  <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Trọng lượng (kg)</label>
                  <input type="number" step="0.1" x-model="calcForm.actual_weight" class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                </div>
              </div>

              <div class="pt-4 border-t border-slate-100">
                <h4 class="text-sm font-semibold text-slate-700 mb-3">Kích thước quy đổi thể tích (Tùy chọn)</h4>
                <div class="grid grid-cols-3 gap-4">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Dài (cm)</label>
                    <input type="number" x-model="calcForm.length" class="form-input w-full rounded-lg border-slate-200 text-sm">
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Rộng (cm)</label>
                    <input type="number" x-model="calcForm.width" class="form-input w-full rounded-lg border-slate-200 text-sm">
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Cao (cm)</label>
                    <input type="number" x-model="calcForm.height" class="form-input w-full rounded-lg border-slate-200 text-sm">
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-8 pt-6">
              <button @click="calculateFee" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 flex justify-center items-center">
                <span x-show="!loading">Tiến hành Tính Phí</span>
                <span x-show="loading" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  Đang phân tích Rule Engine...
                </span>
              </button>
            </div>
          </div>

          <!-- Right: Receipt Panel -->
          <div class="lg:col-span-2 bg-slate-50 p-8 border-l border-slate-200 relative">
            <!-- Receipt Zigzag top -->
            <div class="absolute top-0 left-0 right-0 h-3 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxMiI+PHBhdGggZD0iTTggMTJMMCAwaDE2TDggMTJ6IiBmaWxsPSIjZjhmYWZjIi8+PC9zdmc+')] opacity-0"></div>

            <h3 class="text-lg font-bold text-slate-800 uppercase tracking-widest text-center mb-6">Biên lai cước phí</h3>
            
            <div x-show="!calcResult && !error" class="text-center py-12 text-slate-400">
              <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
              <p class="text-sm font-medium">Nhập thông tin bên trái để xem kết quả tính cước</p>
            </div>

            <div x-show="error" class="bg-rose-50 text-rose-600 p-4 rounded-lg border border-rose-100 text-sm font-medium" x-text="error" style="display: none;"></div>

            <div x-show="calcResult" style="display: none;">
              <template x-if="calcResult && !calcResult.success">
                <div class="bg-rose-50 text-rose-600 p-4 rounded-lg border border-rose-100 text-sm font-medium" x-text="calcResult.error"></div>
              </template>
              
              <template x-if="calcResult && calcResult.success">
                <div class="space-y-5">
                  <div class="flex justify-between items-end border-b border-dashed border-slate-300 pb-3">
                    <div>
                      <p class="text-xs text-slate-500 font-medium">Hãng vận chuyển</p>
                      <p class="font-bold text-slate-800" x-text="calcResult.carrier"></p>
                    </div>
                    <div class="text-right">
                      <p class="text-xs text-slate-500 font-medium">Bảng giá</p>
                      <p class="font-semibold text-indigo-600 text-sm" x-text="calcResult.rate_version"></p>
                    </div>
                  </div>
                  
                  <div class="bg-white p-4 rounded-lg border border-slate-100 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Quy tắc được áp dụng</p>
                    <ul class="space-y-2">
                      <template x-for="rule in calcResult.matched_rules">
                        <li class="flex justify-between text-sm">
                          <span class="text-slate-600 flex-1 pr-2" x-text="rule.name"></span>
                          <span class="font-medium text-slate-800 whitespace-nowrap" x-text="formatMoney(rule.fee)"></span>
                        </li>
                      </template>
                    </ul>
                  </div>

                  <div class="space-y-2 pt-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-slate-500">Phí cơ bản</span>
                      <span class="font-semibold text-slate-800" x-text="formatMoney(calcResult.base_fee)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-slate-500">Phụ phí</span>
                      <span class="font-semibold text-amber-600" x-text="'+ ' + formatMoney(calcResult.surcharge)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-slate-500">Giảm giá</span>
                      <span class="font-semibold text-emerald-600" x-text="'- ' + formatMoney(calcResult.discount)"></span>
                    </div>
                  </div>

                  <div class="border-t-[2px] border-dashed border-slate-300 pt-4 mt-2">
                    <div class="flex justify-between items-center">
                      <span class="text-sm font-bold text-slate-800 uppercase">Tổng cước phí</span>
                      <span class="text-2xl font-black text-indigo-600" x-text="formatMoney(calcResult.final_fee)"></span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <button @click="testModal = false" class="absolute -top-12 right-0 text-white hover:text-slate-200 transition-colors bg-slate-900/50 p-2 rounded-full">
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('shippingEngine', () => ({
    testModal: false,
    loading: false,
    error: null,
    calcForm: {
      order_value: 2500000,
      distance: 8.5,
      actual_weight: 4,
      cod_amount: 0,
      length: '',
      width: '',
      height: ''
    },
    calcResult: null,
    
    async calculateFee() {
      this.loading = true;
      this.error = null;
      this.calcResult = null;
      
      try {
        const res = await fetch('{{ route('project.admin.shipping.calculate', $projectCode) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(this.calcForm)
        });
        
        const data = await res.json();
        this.calcResult = data;
      } catch (err) {
        console.error(err);
        this.error = 'Lỗi kết nối. Vui lòng thử lại sau.';
      } finally {
        this.loading = false;
      }
    },
    
    formatMoney(amount) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
  }))
})
</script>
@endpush
@endsection

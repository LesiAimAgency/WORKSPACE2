@extends('cms.layouts.app')

@section('title', 'Shipping Calculator')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="shippingCalculator()">
  <div class="sm:flex sm:justify-between sm:items-center mb-8">
    <div class="mb-4 sm:mb-0">
      <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Thử Tính Phí Vận Chuyển </h1>
      <p class="text-sm text-gray-500 mt-1">Công cụ giả lập tính phí vận chuyển theo cấu hình Rule Engine</p>
    </div>
    <div>
      <a href="{{ route('project.shipping.index', ['projectCode' => request()->route('projectCode')]) }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-700">
        &lt;- Trở về
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Input Form -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Nhập thông tin đơn hàng</h2>
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị đơn hàng (VND)</label>
          <input type="number" x-model="form.order_value" class="form-input w-full" placeholder="VD: 2500000">
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trọng lượng thực (kg)</label>
            <input type="number" step="0.1" x-model="form.weight" class="form-input w-full" placeholder="VD: 4">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">TL quy đổi (kg)</label>
            <input type="number" step="0.1" x-model="form.volumetric_weight" class="form-input w-full" placeholder="VD: 5">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Khoảng cách (km)</label>
          <input type="number" step="0.1" x-model="form.distance" class="form-input w-full" placeholder="VD: 8.5">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Khu vực (Zone ID)</label>
          <input type="number" x-model="form.zone_id" class="form-input w-full" placeholder="VD: 1">
        </div>

        <div>
          <label class="flex items-center">
            <input type="checkbox" x-model="form.is_cod" class="form-checkbox">
            <span class="text-sm ml-2">Thanh toán COD</span>
          </label>
        </div>

        <div class="pt-4">
          <button @click="calculate" class="btn bg-indigo-500 hover:bg-indigo-600 text-white w-full" :disabled="loading">
            <span x-show="!loading">TÍNH PHÍ</span>
            <span x-show="loading">Đang tính...</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Result Panel -->
    <div class="bg-gray-50 shadow-lg rounded-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Kết quả tính toán</h2>
      
      <div x-show="!result && !error" class="text-gray-500 text-center py-12">
        Nhập thông tin và bấm Tính Phí để xem kết quả chi tiết.
      </div>

      <div x-show="error" class="bg-red-50 text-red-600 p-4 rounded mb-4" x-text="error"></div>

      <div x-show="result" class="space-y-4 text-sm" style="display: none;">
        <div class="flex justify-between border-b pb-2">
          <span class="text-gray-600">Trạng thái:</span>
          <span class="font-medium" :class="result.success ? 'text-green-600' : 'text-red-600'" x-text="result.success ? 'Thành công' : 'Thất bại'"></span>
        </div>
        
        <template x-if="result.success">
          <div>
            <div class="flex justify-between pb-2">
              <span class="text-gray-600">Hãng vận chuyển:</span>
              <span class="font-medium" x-text="result.carrier"></span>
            </div>
            <div class="flex justify-between pb-2">
              <span class="text-gray-600">Bảng giá (Version):</span>
              <span class="font-medium text-indigo-600" x-text="result.rate_version"></span>
            </div>
            <div class="flex justify-between pb-2">
              <span class="text-gray-600">Trọng lượng tính phí:</span>
              <span class="font-medium" x-text="result.chargeable_weight + ' kg'"></span>
            </div>
            
            <div class="mt-4 mb-2 font-semibold text-gray-800">Rules được áp dụng:</div>
            <ul class="list-disc pl-5 mb-4 text-gray-600 space-y-1">
              <template x-for="rule in result.matched_rules" :key="rule.id">
                <li x-text="rule.name"></li>
              </template>
              <li x-show="result.matched_rules.length === 0">Không có rule nào.</li>
            </ul>

            <div class="border-t pt-4 space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Phí cơ bản:</span>
                <span class="font-medium" x-text="formatMoney(result.base_fee)"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Phụ phí (Cộng thêm):</span>
                <span class="font-medium text-orange-500" x-text="'+ ' + formatMoney(result.surcharge)"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Giảm giá:</span>
                <span class="font-medium text-green-500" x-text="'- ' + formatMoney(result.discount)"></span>
              </div>
            </div>

            <div class="border-t mt-4 pt-4 flex justify-between items-center">
              <span class="text-base font-bold text-gray-800">TỔNG PHÍ:</span>
              <span class="text-2xl font-bold text-indigo-600" x-text="formatMoney(result.final_fee)"></span>
            </div>
          </div>
        </template>
        <template x-if="!result.success">
          <div class="text-red-600 p-2" x-text="result.message"></div>
        </template>
      </div>
      
      <div class="mt-6" x-show="result">
        <h3 class="font-semibold text-xs text-gray-500 uppercase tracking-wider mb-2">RAW JSON</h3>
        <pre class="bg-gray-800 text-gray-200 p-4 rounded text-xs overflow-auto max-h-64" x-text="JSON.stringify(result, null, 2)"></pre>
      </div>
    </div>
  </div>
</div>

<script>
function shippingCalculator() {
  return {
    form: {
      order_value: 2500000,
      weight: 4,
      volumetric_weight: 4,
      distance: 8.5,
      zone_id: '',
      is_cod: false
    },
    loading: false,
    result: null,
    error: null,
    
    calculate() {
      this.loading = true;
      this.error = null;
      this.result = null;
      
      fetch("{{ route('project.shipping.calculate', ['projectCode' => request()->route('projectCode')]) }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(this.form)
      })
      .then(res => res.json())
      .then(data => {
        this.result = data;
        this.loading = false;
      })
      .catch(err => {
        this.error = 'Có lỗi xảy ra khi gọi API.';
        this.loading = false;
        console.error(err);
      });
    },
    
    formatMoney(amount) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
  }
}
</script>
@endsection

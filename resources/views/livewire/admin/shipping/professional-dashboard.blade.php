<div>
  @if (session()->has('message'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md flex items-center shadow-sm">
      <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      <span class="text-green-800 font-medium">{{ session('message') }}</span>
    </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <!-- Rule Engine -->
    <div class="p-6 md:p-8">
      <div class="mb-6 flex justify-between items-start">
        <div class="max-w-3xl">
          <h2 class="text-xl font-bold text-slate-800 mb-2">Quy tắc tính phí Tự giao hàng</h2>
          <p class="text-slate-500">Hệ thống Rule Engine (động) cho phép bạn thiết lập giá ship theo khoảng cách, theo giá trị đơn hoặc các phụ phí khác.</p>
        </div>
        <button wire:click="openRuleModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm shadow-indigo-200 transition-all inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span>Thêm quy tắc mới</span>
        </button>
      </div>

      @if($localCarrier && $localCarrier->activeRateVersion)
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-5 mb-6">
          <div class="flex items-center text-indigo-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">Bảng giá đang áp dụng: {{ $localCarrier->activeRateVersion->version_name }}</span>
          </div>

          <div class="space-y-3">
            @forelse($localCarrier->activeRateVersion->rules as $rule)
              <div class="bg-white border border-slate-200 rounded-lg p-4 flex justify-between items-center shadow-sm hover:shadow-md transition-shadow">
                <div>
                  <div class="flex items-center space-x-3 mb-1">
                    <h4 class="font-bold text-slate-800 text-base">{{ $rule->name }}</h4>
                    @if($rule->action_type === 'free')
                      <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider">Free Ship</span>
                    @elseif($rule->is_surcharge)
                      <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">Phụ phí</span>
                    @else
                      <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-wider">Phí cố định</span>
                    @endif
                  </div>
                  <div class="flex flex-wrap items-center text-sm gap-2 mt-2">
                    <span class="text-slate-500">NẾU:</span>
                    @forelse($rule->conditions as $cond)
                      <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs">
                        {{ $cond->condition_type }} <strong class="mx-1">{{ $cond->operator }}</strong> {{ $cond->value_1 }}
                      </span>
                    @empty
                      <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs">
                        Luôn luôn đúng (Mặc định)
                      </span>
                    @endforelse
                    
                    <span class="text-indigo-500 font-bold ml-2">
                       
                      @if($rule->action_type === 'free')
                        0đ
                      @elseif($rule->action_type === 'override')
                        = {{ number_format($rule->fee) }}đ
                      @elseif($rule->action_type === 'add')
                        + {{ number_format($rule->fee) }}đ
                      @endif
                    </span>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                  <button wire:click="deleteRule({{ $rule->id }})" onclick="return confirm('Bạn có chắc chắn muốn xóa quy tắc này?')" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
              </div>
            @empty
              <div class="text-center p-8 bg-white border border-dashed border-slate-300 rounded-lg">
                <p class="text-slate-500 mb-2">Chưa có quy tắc nào.</p>
              </div>
            @endforelse
          </div>
        </div>
      @else
        <div class="text-center p-12 bg-slate-50 border border-slate-200 rounded-xl">
          <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          <p class="text-slate-500">Chưa có Hãng "Tự giao hàng" hoặc Bảng giá được khởi tạo.</p>
        </div>
      @endif
    </div>
  </div>
  
  <!-- Rule Modal -->
  @if($showRuleModal)
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showRuleModal', false)"></div>

      <div class="inline-block w-full max-w-xl overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl relative z-10 p-6 md:p-8">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Thêm quy tắc mới</h3>
        
        <form wire:submit.prevent="saveRule" class="space-y-4">
          <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-700">Tên quy tắc</label>
            <input type="text" wire:model="ruleForm.name" class="form-input w-full rounded-lg border-slate-300 focus:border-indigo-500" placeholder="VD: Phí ship nội thành">
            @error('ruleForm.name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-sm font-semibold text-slate-700">Loại hành động</label>
              <select wire:model="ruleForm.action_type" class="form-select w-full rounded-lg border-slate-300 focus:border-indigo-500">
                <option value="override">Phí cố định (Ghi đè)</option>
                <option value="add">Cộng thêm phí (+)</option>
                <option value="free">Miễn phí ship (0đ)</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="text-sm font-semibold text-slate-700">Mức phí (VND)</label>
              <input type="number" wire:model="ruleForm.fee" class="form-input w-full rounded-lg border-slate-300 focus:border-indigo-500">
            </div>
          </div>
          
          <div class="space-y-1 border border-slate-200 p-4 rounded-lg bg-slate-50">
            <label class="text-sm font-semibold text-slate-700 block mb-2">Điều kiện (Tùy chọn)</label>
            <div class="grid grid-cols-3 gap-2">
              <select wire:model="ruleForm.condition_type" class="form-select text-sm rounded-lg border-slate-300 focus:border-indigo-500">
                <option value="distance">Khoảng cách (km)</option>
                <option value="order_value">Giá trị đơn hàng</option>
                <option value="weight">Trọng lượng (kg)</option>
              </select>
              <select wire:model="ruleForm.operator" class="form-select text-sm rounded-lg border-slate-300 focus:border-indigo-500">
                <option value="<="><= (Nhỏ hơn hoặc bằng)</option>
                <option value=">=">>= (Lớn hơn hoặc bằng)</option>
                <option value="=">= (Bằng)</option>
              </select>
              <input type="number" step="0.1" wire:model="ruleForm.value_1" class="form-input text-sm rounded-lg border-slate-300 focus:border-indigo-500" placeholder="Giá trị">
            </div>
          </div>
          
          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" wire:click="$set('showRuleModal', false)" class="px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-all">Hủy</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <span>Lưu quy tắc</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>

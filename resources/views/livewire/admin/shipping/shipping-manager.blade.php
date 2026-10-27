<div>
    <!-- Tabs -->
    <div class="mb-6 flex border-b border-gray-200">
        <button wire:click="setTab('methods')" class="py-2 px-4 font-medium {{ $activeTab === 'methods' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            Phương thức & Bảng giá
        </button>
        <button wire:click="setTab('zones')" class="py-2 px-4 font-medium {{ $activeTab === 'zones' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            Khu vực (Zones)
        </button>
        <button wire:click="setTab('settings')" class="py-2 px-4 font-medium {{ $activeTab === 'settings' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            Cài đặt chung
        </button>
    </div>

    @if($activeTab === 'methods')
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Các phương thức vận chuyển</h2>
                <button wire:click="createCarrier" class="btn bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-sm">
                    + Thêm Hãng / Phương thức
                </button>
            </div>

            <div class="space-y-6">
                @forelse($carriers as $carrier)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $carrier->name }}</h3>
                                <p class="text-sm text-gray-500">Mã: {{ $carrier->code }} | Loại: <span class="uppercase font-medium text-xs bg-gray-100 px-2 py-1 rounded">{{ $carrier->type }}</span></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $carrier->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $carrier->status ? 'Đang bật' : 'Đang tắt' }}
                                </span>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Active Version Display -->
                        @php
                            $activeVersion = $carrier->rateVersions->where('is_active', true)->first();
                        @endphp
                        
                        @if($activeVersion)
                            <div class="mt-4 border border-indigo-100 rounded bg-indigo-50/50 p-4">
                                <div class="flex justify-between items-center mb-3 border-b border-indigo-100 pb-2">
                                    <div class="font-medium text-indigo-800">
                                        <svg class="w-4 h-4 inline-block mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Đang áp dụng: {{ $activeVersion->version_name }}
                                    </div>
                                    <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Thêm Rule Mới</button>
                                </div>
                                
                                <div class="space-y-3">
                                    @forelse($activeVersion->rules as $rule)
                                        <div class="bg-white border border-gray-200 rounded p-3 flex justify-between items-center hover:border-indigo-300 transition-colors">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-800">{{ $rule->name }}</span>
                                                    @if($rule->is_surcharge)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 uppercase">Phụ phí</span>
                                                    @endif
                                                    @if($rule->action_type === 'free')
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">Free Ship</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @foreach($rule->conditions as $cond)
                                                        <span class="inline-block bg-gray-100 rounded px-1.5 py-0.5 mr-1 mb-1">
                                                            NẾU {{ $cond->condition_type }} {{ $cond->operator }} {{ $cond->value_1 }}
                                                        </span>
                                                    @endforeach
                                                    <span class="inline-block font-medium text-indigo-600 ml-1">
                                                        => 
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
                                            <div class="flex items-center space-x-3 text-gray-400">
                                                <button class="hover:text-blue-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                                <button class="hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-sm text-gray-500 bg-white border border-dashed border-gray-300 rounded">
                                            Chưa có rule nào. Hãy <a href="#" class="text-indigo-600 font-medium">thêm rule mới</a>.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-4 text-center bg-gray-50 rounded border border-gray-200">
                                <p class="text-sm text-gray-500">Chưa có bảng giá nào được kích hoạt.</p>
                                <button class="mt-2 text-indigo-600 font-medium text-sm hover:underline">Tạo phiên bản bảng giá mới</button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <h3 class="text-lg font-medium text-gray-900">Chưa có phương thức vận chuyển nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Bắt đầu bằng cách thêm một hãng vận chuyển hoặc phương thức tự giao hàng.</p>
                        <button wire:click="createCarrier" class="mt-4 btn bg-indigo-500 hover:bg-indigo-600 text-white">
                            + Thêm Phương thức
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'zones')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center text-gray-500 py-12">
            Module Quản lý Vùng (Zone Management) sẽ hiển thị ở đây.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center text-gray-500 py-12">
            Các cài đặt chung về đơn vị đo lường, Divisor, API key sẽ hiển thị ở đây.
        </div>
    @endif
</div>

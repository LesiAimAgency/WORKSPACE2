@extends('cms.layouts.app')

@section('content')
@include('cms.settings.partials.back-link')
<div class="container mx-auto px-4 py-6" x-data="fakeNotifications()">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Cấu hình hiển thị</h2>
        
        <div class="space-y-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="config.enabled" class="rounded">
                <span class="font-medium">Bật hiển thị thông báo ảo</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="config.show_desktop" class="rounded">
                <span>Hiển thị trên desktop</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="config.show_mobile" class="rounded">
                <span>Hiển thị trên mobile</span>
            </label>

            <div>
                <label class="block font-medium mb-2">Vị trí hiển thị</label>
                <select x-model="config.position" class="border rounded px-3 py-2">
                    <option value="bottom-left">Dưới trái</option>
                    <option value="bottom-right">Dưới phải</option>
                    <option value="top-left">Trên trái</option>
                    <option value="top-right">Trên phải</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                <div>
                    <label class="block font-medium mb-2">Thời gian hiển thị mỗi thông báo (giây)</label>
                    <input type="number" x-model="config.display_time" min="1" max="30" class="border rounded px-3 py-2 w-full" placeholder="5">
                    <p class="text-sm text-gray-500 mt-1">Mặc định: 5 giây</p>
                </div>
                <div>
                    <label class="block font-medium mb-2">Khoảng cách giữa các thông báo (giây)</label>
                    <input type="number" x-model="config.interval_time" min="5" max="60" class="border rounded px-3 py-2 w-full" placeholder="10">
                    <p class="text-sm text-gray-500 mt-1">Mặc định: 8-15 giây ngẫu nhiên</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-2">Delay ban đầu (giây)</label>
                    <input type="number" x-model="config.initial_delay" min="0" max="30" class="border rounded px-3 py-2 w-full" placeholder="3">
                    <p class="text-sm text-gray-500 mt-1">Thời gian chờ trước khi hiển thị thông báo đầu tiên</p>
                </div>
                <div>
                    <label class="flex items-center gap-2 mt-8">
                        <input type="checkbox" x-model="config.random_interval" class="rounded">
                        <span>Khoảng cách ngẫu nhiên</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">Nếu bật, thời gian sẽ dao động ±50%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Danh sách thông báo</h2>
            <button @click="addNotification()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm thông báo
            </button>
        </div>

        <div class="space-y-3">
            <template x-for="(notif, index) in notifications" :key="index">
                <div class="flex items-center gap-4 p-4 border rounded-lg hover:bg-gray-50">
                    <div class="flex-1 grid grid-cols-4 gap-4">
                        <input type="text" x-model="notif.customer" placeholder="Tên khách hàng" class="border rounded px-3 py-2">
                        <input type="text" x-model="notif.address" placeholder="Địa chỉ" class="border rounded px-3 py-2">
                        <input type="text" x-model="notif.content" placeholder="Nội dung" class="border rounded px-3 py-2">
                        <input type="number" x-model="notif.time" placeholder="Thời gian (giây)" class="border rounded px-3 py-2">
                    </div>
                    <button @click="deleteNotification(index)" class="p-2 text-red-600 hover:bg-red-50 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="mt-6 flex gap-3">
            <button @click="save()" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Lưu
            </button>
        </div>
    </div>

    @php
        $settingsSaveUrl = \Route::has('admin.settings.save')
            ? route('admin.settings.save')
            : (\Route::has('cms.settings.save') ? route('cms.settings.save') : url('/admin/settings'));
    @endphp
    <form action="{{ $settingsSaveUrl }}" method="POST" x-ref="saveForm">
        @csrf
        <input type="hidden" name="fake_notifications" :value="JSON.stringify(notifications)">
        <input type="hidden" name="fake_notifications_config" :value="JSON.stringify(config)">
    </form>
</div>

<script>
function fakeNotifications() {
    const defaultConfig = {
        enabled: true, 
        show_desktop: true, 
        show_mobile: true, 
        position: 'bottom-left',
        display_time: 5,
        interval_time: 10,
        initial_delay: 3,
        random_interval: true
    };
    const savedConfig = @json(setting('fake_notifications_config'));
    
    return {
        notifications: @json(setting('fake_notifications', [])),
        config: {...defaultConfig, ...savedConfig},

        addNotification() {
            this.notifications.push({
                customer: '',
                address: '',
                content: '',
                time: 5
            });
        },

        deleteNotification(index) {
            if (confirm('Xóa thông báo này?')) {
                this.notifications.splice(index, 1);
            }
        },

        save() {
            showAlert('Đang lưu...', 'info');
            this.$nextTick(() => {
                this.$refs.saveForm.submit();
            });
        }
    }
}
</script>
@endsection

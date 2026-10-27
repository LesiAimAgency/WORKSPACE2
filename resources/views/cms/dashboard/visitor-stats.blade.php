<!-- Visitor Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Today's Visitor Stats -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thống kê truy cập hôm nay</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Tổng lượt truy cập:</span>
                <span class="font-semibold text-blue-600">{{ number_format($visitor_stats['total_visits']) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">IP duy nhất:</span>
                <span class="font-semibold text-green-600">{{ number_format($visitor_stats['unique_ips']) }}</span>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="font-medium text-gray-900 mb-3">Trang được truy cập nhiều nhất</h4>
            <div class="space-y-2">
                @foreach($visitor_stats['top_pages'] as $page)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 truncate">{{ parse_url(is_array($page) ? $page['url'] : $page->url, PHP_URL_PATH) ?: '/' }}</span>
                    <span class="font-medium">{{ is_array($page) ? $page['visits'] : $page->visits }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top IPs -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">IP truy cập nhiều nhất (7 ngày)</h3>
        <div class="space-y-3">
            @foreach($top_ips as $ip)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <div>
                    <span class="font-mono text-sm">{{ $ip->ip_address }}</span>
                    @if($ip->visits > 100)
                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Cao</span>
                    @elseif($ip->visits > 50)
                        <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Trung bình</span>
                    @endif
                </div>
                <span class="font-semibold text-gray-900">{{ number_format($ip->visits) }} lượt</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

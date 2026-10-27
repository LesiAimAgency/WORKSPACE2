<div class="analytics-widget style-{{ $style }}">
    @if($showTitle)
        <h3 class="text-lg font-semibold mb-4 text-gray-900">{{ $title }}</h3>
    @endif
    
    <div class="grid grid-cols-{{ $columns }} gap-4">
        <div class="stat-item">
            <div class="stat-number text-green-600">{{ number_format($online) }}</div>
            <div class="stat-label">Đang online</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-blue-600">{{ number_format($today) }}</div>
            <div class="stat-label">Hôm nay</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-blue-600">{{ number_format($days3) }}</div>
            <div class="stat-label">3 ngày qua</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-indigo-600">{{ number_format($days7) }}</div>
            <div class="stat-label">7 ngày qua</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-pink-600">{{ number_format($month) }}</div>
            <div class="stat-label">Tháng này</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-red-600">{{ number_format($year) }}</div>
            <div class="stat-label">Năm nay</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-number text-gray-800">{{ number_format($total) }}</div>
            <div class="stat-label">Tổng truy cập</div>
        </div>
    </div>
</div>

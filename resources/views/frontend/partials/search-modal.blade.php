{{-- Search Modal Component --}}
<div id="search-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleSearchModal()"></div>
    <div class="absolute top-0 left-0 right-0 bg-white shadow-lg p-6">
        <div class="container mx-auto">
            <form action="/{{ $projectCode }}/search" method="GET" class="flex gap-4">
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." 
                       class="flex-1 px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-blue-500 text-lg" autofocus>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Tìm kiếm
                </button>
                <button type="button" onclick="toggleSearchModal()" class="px-4 py-3 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSearchModal() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            modal.querySelector('input[name="q"]')?.focus();
        }
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('search-modal');
        if (modal && !modal.classList.contains('hidden')) {
            toggleSearchModal();
        }
    }
});
</script>

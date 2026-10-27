<?php

use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\Review;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
  use WithFileUploads;

  public Post $product;

  public $rating = 5;
  public $name = '';
  public $email = '';
  public $content = '';
  public $image = null;
  
  public $filter = null; // null means all, 1-5 means specific rating
  public $successMessage = '';

  public function submit()
  {
    $this->validate([
      'rating' => 'required|integer|min:1|max:5',
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'content' => 'required|string',
      'image' => 'nullable|image|max:5120', // 5MB max
    ]);

    $imagePath = null;
    if ($this->image) {
      $imagePath = $this->image->store('reviews', 'public');
    }

    Review::create([
      'project_id' => $this->product->project_id ?? null,
      'tenant_id' => $this->product->tenant_id ?? session('current_tenant_id'),
      'post_id' => $this->product->id,
      'reviewer_name' => $this->name,
      'reviewer_email' => $this->email,
      'rating' => $this->rating,
      'content' => $this->content,
      'image' => $imagePath,
      'status' => 'pending', // Requires admin approval
      'sort_order' => Review::max('sort_order') + 1,
    ]);

    $this->reset(['rating', 'name', 'email', 'content', 'image']);
    $this->rating = 5;
    $this->successMessage = 'Đánh giá của bạn đã được gửi và đang chờ duyệt.';
  }

  public function setFilter($rating)
  {
    $this->filter = $rating;
  }

  public function with(): array
  {
    $allApprovedReviews = Review::where('post_id', $this->product->id)
                  ->approved()
                  ->get();

    $totalReviews = $allApprovedReviews->count();
    $averageRating = $totalReviews > 0 ? round($allApprovedReviews->avg('rating'), 1) : 0;
    
    $ratingCounts = [
      5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
    ];

    foreach ($allApprovedReviews as $review) {
      $ratingCounts[$review->rating]++;
    }

    $query = Review::where('post_id', $this->product->id)
            ->approved()
            ->orderBy('created_at', 'desc');

    if ($this->filter !== null) {
      $query->where('rating', $this->filter);
    }

    return [
      'reviews' => $query->get(),
      'totalReviews' => $totalReviews,
      'averageRating' => number_format($averageRating, 1),
      'ratingCounts' => $ratingCounts,
    ];
  }
}
?>

<div id="reviews" class="w-full">
  <!-- Top Block: Stats & Form -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Stats Left -->
    <div class="bg-gray-50 rounded-xl p-8 flex flex-col justify-center">
      <div class="flex items-center gap-4 mb-6">
        <div class="text-5xl font-bold text-gray-900">{{ $averageRating }}</div>
        <div>
          <div class="flex text-yellow-400 text-xl mb-1">
            @for($i = 1; $i <= 5; $i++)
              @if($i <= round((float)$averageRating))
                
              @else
                
              @endif
            @endfor
          </div>
          <div class="text-gray-500 text-sm">{{ $totalReviews }} đánh giá</div>
        </div>
      </div>

      <div class="space-y-2">
        @for($star = 5; $star >= 1; $star--)
          @php 
            $count = $ratingCounts[$star];
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
          @endphp
          <div class="flex items-center text-sm">
            <div class="flex text-yellow-400 w-16">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= $star) @else @endif
              @endfor
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mx-3">
              <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
            <div class="w-16 text-right text-gray-500 text-xs">
              <span class="font-bold text-gray-700">{{ round($percentage) }}%</span> | {{ $count }}
            </div>
          </div>
        @endfor
      </div>
    </div>

    <!-- Form Right -->
    <div class="bg-gray-50 rounded-xl p-8">
      @if($successMessage)
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-4 text-sm font-medium">
          {{ $successMessage }}
        </div>
      @else
        <form wire:submit="submit">
          <!-- Rating Input -->
          <div class="flex justify-center mb-6">
            <div class="flex text-3xl cursor-pointer text-gray-300">
              @for($i = 1; $i <= 5; $i++)
                <span wire:click="$set('rating', {{ $i }})" class="{{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }} transition-colors"></span>
              @endfor
            </div>
          </div>

          <!-- Inputs -->
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <input type="text" wire:model="name" placeholder="Họ tên của bạn" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required>
              @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
              <input type="email" wire:model="email" placeholder="Email của bạn" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required>
              @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="mb-4">
            <textarea wire:model="content" rows="3" placeholder="Hãy chia sẻ những điều bạn thích về sản phẩm này nhé" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required></textarea>
            @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="flex items-center justify-between">
            <div>
              <input type="file" wire:model="image" id="review-image" class="hidden" accept="image/*">
              <label for="review-image" class="cursor-pointer text-blue-600 font-medium text-sm flex items-center gap-1 hover:text-blue-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Gửi ảnh thực tế
              </label>
              @if($image)
                <span class="text-xs text-green-600 mt-1 block">Đã chọn ảnh</span>
              @endif
              @error('image') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-8 py-2 rounded-md font-bold text-sm shadow-sm transition-colors" wire:loading.attr="disabled">
              GỬI
            </button>
          </div>
        </form>
      @endif
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="flex items-center gap-2 flex-wrap border-b border-gray-100 pb-6 mb-6">
    <span class="text-gray-500 text-sm mr-2">Lọc theo:</span>
    <button wire:click="setFilter(null)" class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $filter === null ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Tất cả</button>
    @for($i = 1; $i <= 5; $i++)
      <button wire:click="setFilter({{ $i }})" class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $filter === $i ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">{{ $i }} </button>
    @endfor
  </div>

  <!-- Reviews List -->
  <div class="space-y-6">
    @forelse($reviews as $review)
      <div class="border-b border-gray-100 pb-6 last:border-0 flex gap-4">
        <!-- Avatar generator (First two letters) -->
        <div class="shrink-0 w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center text-white font-bold text-sm mt-1">
          {{ mb_strtoupper(mb_substr($review->reviewer_name, 0, 2)) }}
        </div>
        
        <div class="flex-1">
          <div class="flex flex-col sm:flex-row sm:items-start justify-between">
            <div>
              <span class="font-bold text-gray-900 block">{{ $review->reviewer_name }}</span>
              <!-- Assuming verified purchase badge for all since it's an example, or based on condition -->
              <div class="flex items-center text-green-500 text-xs font-medium mt-0.5">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Đã Mua Hàng Từ Shop
              </div>
            </div>
            <div class="flex text-yellow-400 text-sm mt-2 sm:mt-0">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= $review->rating) @else @endif
              @endfor
            </div>
          </div>

          <!-- Content box -->
          <div class="bg-gray-50 rounded-lg p-4 mt-3">
            <p class="text-gray-700 text-sm leading-relaxed">{{ $review->content }}</p>
            @if($review->image)
              <div class="mt-3">
                <img src="{{ Storage::url($review->image) }}" alt="Review image" class="h-24 w-auto object-cover rounded-md border border-gray-200">
              </div>
            @endif
          </div>
          
          <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
            <span>{{ $review->created_at->diffForHumans() }}</span>
            <div class="flex items-center gap-4">
              <button class="flex items-center gap-1 hover:text-blue-600 border border-gray-300 rounded px-2 py-1 bg-white">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                Like 0
              </button>
              <button class="hover:text-blue-600 transition">Gửi trả lời</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
        </svg>
        <p class="text-gray-500">Chưa có đánh giá nào{{ $filter ? ' với ' . $filter . ' sao' : '' }}. Hãy là người đầu tiên đánh giá!</p>
      </div>
    @endforelse
  </div>
</div>

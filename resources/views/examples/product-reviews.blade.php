{{-- Example: Product Reviews Page with Popup --}}
@extends('layouts.app')

@section('title', 'Đánh giá sản phẩm')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="max-w-4xl mx-auto">
    {{-- Product Info --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <div class="flex items-start space-x-6">
        <img src="https://via.placeholder.com/300x300" alt="Sản phẩm mẫu" class="w-32 h-32 object-cover rounded-lg">
        <div class="flex-1">
          <h1 class="text-2xl font-bold mb-2">iPhone 15 Pro Max</h1>
          <p class="text-gray-600 mb-4">Điện thoại thông minh cao cấp với camera chuyên nghiệp</p>
          <div class="flex items-center space-x-4 mb-4">
            <div class="flex items-center">
              @for($i = 1; $i <= 5; $i++)
              <span class="text-yellow-400"></span>
              @endfor
              <span class="ml-2 text-sm text-gray-600">(4.8/5 - 124 đánh giá)</span>
            </div>
          </div>
          <div class="flex space-x-3">
            <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              Viết đánh giá
            </button>
            <button class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
              Xem tất cả đánh giá
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Quick Review Buttons --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Đánh giá nhanh</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          <span class="text-2xl mr-2"></span>
          <span class="text-sm">Tuyệt vời</span>
        </button>
        <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          <span class="text-2xl mr-2"></span>
          <span class="text-sm">Hài lòng</span>
        </button>
        <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          <span class="text-2xl mr-2"></span>
          <span class="text-sm">Bình thường</span>
        </button>
        <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          <span class="text-2xl mr-2"></span>
          <span class="text-sm">Không hài lòng</span>
        </button>
      </div>
    </div>
      
      <form id="review-form" class="space-y-4">
        @csrf
        
        {{-- Rating --}}
        <div>
          <label class="block text-sm font-medium mb-2">Đánh giá của bạn</label>
          <div class="flex items-center space-x-1" id="rating-stars">
            @for($i = 1; $i <= 5; $i++)
            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="{{ $i }}">
              
            </button>
            @endfor
          </div>
          <input type="hidden" name="rating" id="rating-input" required>
        </div>
        
        {{-- Comment --}}
        <div>
          <label for="comment" class="block text-sm font-medium mb-2">Nhận xét</label>
          <textarea name="comment" id="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
        </div>
        
        {{-- Images Upload --}}
        <div id="images-section" style="display: none;">
          <label class="block text-sm font-medium mb-2">Hình ảnh</label>
          <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
          <p class="text-xs text-gray-500 mt-1" id="images-help">Tối đa 5 hình ảnh</p>
        </div>
        
        {{-- Submit Button --}}
        <div class="flex justify-end">
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Gửi đánh giá
          </button>
        </div>
      </form>
    </div>

    {{-- Reviews List --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Đánh giá từ khách hàng (124)</h2>
        <button onclick="openReviewPopup(1, 'iPhone 15 Pro Max', 'https://via.placeholder.com/300x300')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
          + Thêm đánh giá
        </button>
      </div>

      {{-- Rating Summary --}}
      <div class="border-b pb-6 mb-6">
        <div class="flex items-center space-x-8">
          <div class="text-center">
            <div class="text-4xl font-bold text-gray-900">4.8</div>
            <div class="flex items-center justify-center mt-1">
              @for($i = 1; $i <= 5; $i++)
              <span class="text-yellow-400"></span>
              @endfor
            </div>
            <div class="text-sm text-gray-500 mt-1">124 đánh giá</div>
          </div>
          <div class="flex-1">
            @for($rating = 5; $rating >= 1; $rating--)
            <div class="flex items-center mb-1">
              <span class="text-sm w-8">{{ $rating }}</span>
              <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $rating == 5 ? '70' : ($rating == 4 ? '20' : ($rating == 3 ? '7' : ($rating == 2 ? '2' : '1'))) }}%"></div>
              </div>
              <span class="text-sm text-gray-500 w-8">{{ $rating == 5 ? '87' : ($rating == 4 ? '25' : ($rating == 3 ? '9' : ($rating == 2 ? '2' : '1'))) }}</span>
            </div>
            @endfor
          </div>
        </div>
      </div>

    <div id="reviews-list" class="space-y-6"></div>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">Đánh giá từ khách hàng</h2>
        
        {{-- Sort Options --}}
        <select id="reviews-sort" class="px-3 py-2 border border-gray-300 rounded-md">
          <option value="newest">Mới nhất</option>
          <option value="highest">Rating cao nhất</option>
          <option value="lowest">Rating thấp nhất</option>
          <option value="helpful">Hữu ích nhất</option>
        </select>
      </div>
      
      {{-- Sample Review --}}
      <div class="review-item bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-start space-x-4">
          <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
          </div>
          
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <span class="font-medium">Nguyễn Văn A</span>
              <span class="verified-badge px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full" style="display: none;">
                 Đã mua hàng
              </span>
            </div>
            
            <div class="flex items-center space-x-1 mb-2">
              @for($i = 1; $i <= 5; $i++)
              <span class="text-yellow-400"></span>
              @endfor
              <span class="text-sm text-gray-500 ml-2">2 ngày trước</span>
            </div>
            
            <p class="text-gray-700 mb-3">Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh. Mình rất hài lòng!</p>
            
            {{-- Review Images --}}
            <div class="review-images flex space-x-2 mb-3" style="display: none;">
              <div class="w-20 h-20 bg-gray-200 rounded"></div>
              <div class="w-20 h-20 bg-gray-200 rounded"></div>
            </div>
            
            {{-- Helpful Actions --}}
            <div class="helpful-actions flex items-center space-x-4 text-sm" style="display: none;">
              <button class="text-gray-600 hover:text-blue-600"> Hữu ích (12)</button>
              <button class="text-gray-600 hover:text-gray-800">Trả lời</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    {{-- Pagination --}}
    <div id="reviews-pagination" class="mt-8 flex justify-center">
      <nav class="flex space-x-2">
        <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Trước</button>
        <button class="px-3 py-2 bg-blue-600 text-white rounded-md">1</button>
        <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">2</button>
        <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">3</button>
        <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Sau</button>
      </nav>
    </div>
    </div>
  </div>
</div>

{{-- Include Review Popup Component --}}
<x-review-popup 
  :product-id="1" 
  product-name="iPhone 15 Pro Max" 
  product-image="https://via.placeholder.com/300x300" 
/>

<script>
document.addEventListener('DOMContentLoaded', async function() {
  // Wait for reviews config to load
  await window.ReviewsConfig.load();
  
  // Initialize the page based on configuration
  await initializeReviewsPage();
});

async function initializeReviewsPage() {
  const config = await window.ReviewsConfig.getConfig();
  
  // Show/hide review form based on configuration
  const formContainer = document.getElementById('review-form-container');
  if (config.enabled && config.enable_product) {
    formContainer.style.display = 'block';
  }
  
  // Setup images section
  const imagesSection = document.getElementById('images-section');
  const imagesHelp = document.getElementById('images-help');
  if (config.allow_images) {
    imagesSection.style.display = 'block';
    imagesHelp.textContent = `Tối đa ${config.max_images} hình ảnh`;
  }
  
  // Show verified badges
  const verifiedBadges = document.querySelectorAll('.verified-badge');
  if (config.show_verified) {
    verifiedBadges.forEach(badge => badge.style.display = 'inline-block');
  }
  
  // Show review images
  const reviewImages = document.querySelectorAll('.review-images');
  if (config.allow_images) {
    reviewImages.forEach(images => images.style.display = 'flex');
  }
  
  // Show helpful actions
  const helpfulActions = document.querySelectorAll('.helpful-actions');
  if (config.allow_helpful) {
    helpfulActions.forEach(actions => actions.style.display = 'flex');
  }
  
  // Setup sorting
  await window.ReviewsHelper.setupSorting(document.getElementById('reviews-sort'));
  
  // Apply template styling
  const reviewItems = document.querySelectorAll('.review-item');
  reviewItems.forEach(item => window.ReviewsHelper.applyTemplate(item));
  
  // Setup rating stars
  setupRatingStars();
  
  // Setup form validation
  setupFormValidation();
}

function setupRatingStars() {
  const stars = document.querySelectorAll('.star-btn');
  const ratingInput = document.getElementById('rating-input');
  
  stars.forEach((star, index) => {
    star.addEventListener('click', function() {
      const rating = index + 1;
      ratingInput.value = rating;
      
      // Update star display
      stars.forEach((s, i) => {
        if (i < rating) {
          s.classList.remove('text-gray-300');
          s.classList.add('text-yellow-400');
        } else {
          s.classList.remove('text-yellow-400');
          s.classList.add('text-gray-300');
        }
      });
    });
  });
}

function setupFormValidation() {
  const form = document.getElementById('review-form');
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = {
      rating: parseInt(document.getElementById('rating-input').value),
      comment: document.getElementById('comment').value,
      images: Array.from(document.getElementById('images-input').files)
    };
    
    // Validate using ReviewsHelper
    const validation = await window.ReviewsHelper.validateReviewForm(formData);
    
    if (!validation.valid) {
      // Show errors
      showErrors(validation.errors);
      return;
    }
    
    // Submit form (implement your submission logic here)
    console.log('Form is valid, submitting...', formData);
    showSuccess('Đánh giá của bạn đã được gửi thành công!');
  });
}

function showErrors(errors) {
  // Remove existing error messages
  const existingErrors = document.querySelectorAll('.reviews-error');
  existingErrors.forEach(error => error.remove());
  
  // Show new errors
  const form = document.getElementById('review-form');
  errors.forEach(error => {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'reviews-error';
    errorDiv.textContent = error;
    form.insertBefore(errorDiv, form.firstChild);
  });
}

function showSuccess(message) {
  // Remove existing messages
  const existingMessages = document.querySelectorAll('.reviews-success, .reviews-error');
  existingMessages.forEach(msg => msg.remove());
  
  // Show success message
  const form = document.getElementById('review-form');
  const successDiv = document.createElement('div');
  successDiv.className = 'reviews-success';
  successDiv.textContent = message;
  form.insertBefore(successDiv, form.firstChild);
  
  // Reset form
  form.reset();
  document.getElementById('rating-input').value = '';
  document.querySelectorAll('.star-btn').forEach(star => {
    star.classList.remove('text-yellow-400');
    star.classList.add('text-gray-300');
  });
}
</script>
@endsection

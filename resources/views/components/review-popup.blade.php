{{-- Review Popup Component --}}
@props(['productId' => null, 'productName' => '', 'productImage' => ''])

<div id="review-popup" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between p-6 border-b">
      <h3 class="text-lg font-semibold">Đánh giá sản phẩm</h3>
      <button id="close-review-popup" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    {{-- Product Info --}}
    <div class="p-6 border-b">
      <div class="flex items-center space-x-3">
        @if($productImage)
        <img src="{{ $productImage }}" alt="{{ $productName }}" class="w-16 h-16 object-cover rounded-lg">
        @else
        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        @endif
        <div>
          <h4 class="font-medium text-gray-900">{{ $productName ?: 'Sản phẩm' }}</h4>
          <p class="text-sm text-gray-500">Chia sẻ trải nghiệm của bạn</p>
        </div>
      </div>
    </div>

    {{-- Review Form --}}
    <form id="popup-review-form" class="p-6 space-y-4">
      @csrf
      <input type="hidden" name="product_id" value="{{ $productId }}">
      
      {{-- Login Required Message --}}
      <div id="login-required-message" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-sm text-yellow-800">Bạn cần đăng nhập để đánh giá sản phẩm</span>
        </div>
        <div class="mt-3">
          <a href="/login" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
            Đăng nhập ngay
          </a>
        </div>
      </div>

      {{-- Purchase Required Message --}}
      <div id="purchase-required-message" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-sm text-blue-800">Chỉ khách hàng đã mua sản phẩm mới có thể đánh giá</span>
        </div>
      </div>

      {{-- Rating Stars --}}
      <div id="rating-section">
        <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá của bạn *</label>
        <div class="flex items-center space-x-1" id="popup-rating-stars">
          @for($i = 1; $i <= 5; $i++)
          <button type="button" class="popup-star-btn text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors" data-rating="{{ $i }}">
            
          </button>
          @endfor
        </div>
        <input type="hidden" name="rating" id="popup-rating-input" required>
        <p class="text-xs text-gray-500 mt-1">Nhấp vào sao để đánh giá</p>
      </div>

      {{-- Comment --}}
      <div>
        <label for="popup-comment" class="block text-sm font-medium text-gray-700 mb-2">Nhận xét</label>
        <textarea name="comment" id="popup-comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
      </div>

      {{-- Name (for guests) --}}
      <div id="guest-name-section" class="hidden">
        <label for="popup-reviewer-name" class="block text-sm font-medium text-gray-700 mb-2">Tên của bạn *</label>
        <input type="text" name="reviewer_name" id="popup-reviewer-name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nhập tên của bạn">
      </div>

      {{-- Email (for guests) --}}
      <div id="guest-email-section" class="hidden">
        <label for="popup-reviewer-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input type="email" name="reviewer_email" id="popup-reviewer-email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="email@example.com">
        <p class="text-xs text-gray-500 mt-1">Email sẽ không được hiển thị công khai</p>
      </div>

      {{-- Images Upload --}}
      <div id="popup-images-section" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors">
          <input type="file" name="images[]" id="popup-images-input" multiple accept="image/*" class="hidden">
          <label for="popup-images-input" class="cursor-pointer">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="mt-2">
              <span class="text-sm text-gray-600">Nhấp để chọn hình ảnh</span>
              <p class="text-xs text-gray-500" id="popup-images-help">Tối đa 5 hình ảnh</p>
            </div>
          </label>
        </div>
        <div id="popup-selected-images" class="mt-3 grid grid-cols-3 gap-2 hidden"></div>
      </div>

      {{-- Reward Points Info --}}
      <div id="reward-points-info" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-sm text-green-800">Bạn sẽ nhận được <span id="reward-points-amount">0</span> điểm thưởng khi đánh giá</span>
        </div>
      </div>

      {{-- Error Messages --}}
      <div id="popup-error-messages"></div>

      {{-- Submit Button --}}
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" id="cancel-review" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
          Hủy
        </button>
        <button type="submit" id="submit-review" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
          <span class="submit-text">Gửi đánh giá</span>
          <span class="loading-text hidden">Đang gửi...</span>
        </button>
      </div>
    </form>

    {{-- Success Message --}}
    <div id="popup-success-message" class="hidden p-6 text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Cảm ơn bạn!</h3>
      <p class="text-sm text-gray-500 mb-4" id="success-message-text">Đánh giá của bạn đã được gửi thành công.</p>
      <button id="close-success-popup" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Đóng
      </button>
    </div>
  </div>
</div>

{{-- Include Reviews Configuration --}}
<x-reviews-config />

{{-- Include Review Popup Helper --}}
<script src="{{ asset('js/review-popup-helper.js') }}"></script>

<script>
class ReviewPopup {
  constructor() {
    this.popup = document.getElementById('review-popup');
    this.form = document.getElementById('popup-review-form');
    this.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    this.currentUser = @json(auth()->user());
    this.config = null;
    
    this.init();
  }

  async init() {
    // Wait for reviews config to load
    await window.ReviewsConfig.load();
    this.config = await window.ReviewsConfig.getConfig();
    
    this.setupEventListeners();
    this.setupForm();
  }

  setupEventListeners() {
    // Close popup events
    document.getElementById('close-review-popup').addEventListener('click', () => this.close());
    document.getElementById('cancel-review').addEventListener('click', () => this.close());
    document.getElementById('close-success-popup').addEventListener('click', () => this.close());
    
    // Close on backdrop click
    this.popup.addEventListener('click', (e) => {
      if (e.target === this.popup) this.close();
    });

    // Rating stars
    this.setupRatingStars();
    
    // Form submission
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    
    // Image upload
    this.setupImageUpload();
  }

  async setupForm() {
    // Check if reviews are enabled
    if (!this.config.enabled) {
      this.showError('Chức năng đánh giá hiện tại không khả dụng');
      return;
    }

    // Check login requirement
    if (this.config.require_login && !this.isLoggedIn) {
      document.getElementById('login-required-message').classList.remove('hidden');
      document.getElementById('rating-section').style.display = 'none';
      return;
    }

    // Show guest fields if not logged in and login not required
    if (!this.isLoggedIn && !this.config.require_login) {
      document.getElementById('guest-name-section').classList.remove('hidden');
      document.getElementById('guest-email-section').classList.remove('hidden');
    }

    // Setup images section
    if (this.config.allow_images) {
      document.getElementById('popup-images-section').classList.remove('hidden');
      document.getElementById('popup-images-help').textContent = `Tối đa ${this.config.max_images} hình ảnh`;
    }

    // Show reward points info
    if (this.config.reward_points > 0) {
      document.getElementById('reward-points-info').classList.remove('hidden');
      document.getElementById('reward-points-amount').textContent = this.config.reward_points;
    }
  }

  setupRatingStars() {
    const stars = document.querySelectorAll('.popup-star-btn');
    const ratingInput = document.getElementById('popup-rating-input');
    
    stars.forEach((star, index) => {
      star.addEventListener('click', async () => {
        const rating = index + 1;
        
        // Check minimum rating
        if (!await window.ReviewsConfig.isValidRating(rating)) {
          const minRating = await window.ReviewsConfig.getMinRating();
          this.showError(`Đánh giá tối thiểu là ${minRating} sao`);
          return;
        }
        
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

  setupImageUpload() {
    const input = document.getElementById('popup-images-input');
    const selectedImagesContainer = document.getElementById('popup-selected-images');
    
    input.addEventListener('change', async (e) => {
      const files = Array.from(e.target.files);
      
      // Validate image count
      if (!await window.ReviewsConfig.isValidImageCount(files.length)) {
        const maxImages = await window.ReviewsConfig.getMaxImages();
        this.showError(`Tối đa ${maxImages} hình ảnh`);
        input.value = '';
        return;
      }
      
      // Display selected images
      selectedImagesContainer.innerHTML = '';
      if (files.length > 0) {
        selectedImagesContainer.classList.remove('hidden');
        
        files.forEach((file, index) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'relative';
            imageDiv.innerHTML = `
              <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg">
              <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600" onclick="this.parentElement.remove()">×</button>
            `;
            selectedImagesContainer.appendChild(imageDiv);
          };
          reader.readAsDataURL(file);
        });
      } else {
        selectedImagesContainer.classList.add('hidden');
      }
    });
  }

  async handleSubmit(e) {
    e.preventDefault();
    
    // Show loading state
    this.setLoading(true);
    
    try {
      // Collect form data
      const formData = new FormData(this.form);
      const reviewData = {
        rating: parseInt(formData.get('rating')),
        comment: formData.get('comment'),
        images: formData.getAll('images[]').filter(file => file.size > 0)
      };
      
      // Validate using ReviewsHelper
      const validation = await window.ReviewsHelper.validateReviewForm(reviewData);
      
      if (!validation.valid) {
        this.showErrors(validation.errors);
        return;
      }
      
      // Submit to server
      const response = await fetch('/api/reviews', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });
      
      const result = await response.json();
      
      if (result.success) {
        this.showSuccess(result.message || 'Đánh giá của bạn đã được gửi thành công!');
      } else {
        this.showError(result.message || 'Có lỗi xảy ra khi gửi đánh giá');
      }
      
    } catch (error) {
      console.error('Error submitting review:', error);
      this.showError('Có lỗi xảy ra khi gửi đánh giá');
    } finally {
      this.setLoading(false);
    }
  }

  open(productId = null, productName = '', productImage = '') {
    // Update product info if provided
    if (productId) {
      this.form.querySelector('input[name="product_id"]').value = productId;
    }
    
    // Reset form
    this.resetForm();
    
    // Show popup
    this.popup.classList.remove('hidden');
    this.popup.classList.add('flex');
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.popup.classList.add('hidden');
    this.popup.classList.remove('flex');
    document.body.style.overflow = '';
    this.resetForm();
  }

  resetForm() {
    this.form.reset();
    document.getElementById('popup-rating-input').value = '';
    document.getElementById('popup-error-messages').innerHTML = '';
    document.getElementById('popup-selected-images').classList.add('hidden');
    document.getElementById('popup-success-message').classList.add('hidden');
    this.form.style.display = 'block';
    
    // Reset stars
    document.querySelectorAll('.popup-star-btn').forEach(star => {
      star.classList.remove('text-yellow-400');
      star.classList.add('text-gray-300');
    });
  }

  showSuccess(message) {
    document.getElementById('success-message-text').textContent = message;
    document.getElementById('popup-success-message').classList.remove('hidden');
    this.form.style.display = 'none';
  }

  showError(message) {
    this.showErrors([message]);
  }

  showErrors(errors) {
    const container = document.getElementById('popup-error-messages');
    container.innerHTML = '';
    
    errors.forEach(error => {
      const errorDiv = document.createElement('div');
      errorDiv.className = 'reviews-error';
      errorDiv.textContent = error;
      container.appendChild(errorDiv);
    });
  }

  setLoading(loading) {
    const submitBtn = document.getElementById('submit-review');
    const submitText = submitBtn.querySelector('.submit-text');
    const loadingText = submitBtn.querySelector('.loading-text');
    
    if (loading) {
      submitBtn.disabled = true;
      submitText.classList.add('hidden');
      loadingText.classList.remove('hidden');
    } else {
      submitBtn.disabled = false;
      submitText.classList.remove('hidden');
      loadingText.classList.add('hidden');
    }
  }
}

// Initialize popup when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  window.reviewPopup = new ReviewPopup();
});

// Global function to open review popup
window.openReviewPopup = function(productId, productName = '', productImage = '') {
  if (window.reviewPopup) {
    window.reviewPopup.open(productId, productName, productImage);
  }
};
</script>

<style>
/* Additional popup-specific styles */
#review-popup {
  backdrop-filter: blur(4px);
}

.popup-star-btn {
  transition: all 0.2s ease;
  transform: scale(1);
}

.popup-star-btn:hover {
  transform: scale(1.1);
}

.popup-star-btn:active {
  transform: scale(0.95);
}

/* Image upload area */
#popup-images-section input[type="file"] + label:hover {
  background-color: #f9fafb;
}

/* Loading animation */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-text::before {
  content: '';
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid #ffffff;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
}
</style>

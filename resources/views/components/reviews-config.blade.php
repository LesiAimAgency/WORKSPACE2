{{-- Reviews Configuration Component --}}
<script src="{{ asset('js/reviews-config.js') }}"></script>

<script>
// Initialize reviews configuration when DOM is loaded
document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Pre-load configuration
        await window.ReviewsConfig.load();
        
        // Dispatch event when config is loaded
        window.dispatchEvent(new CustomEvent('reviewsConfigLoaded', {
            detail: await window.ReviewsConfig.getConfig()
        }));
        
        console.log('Reviews configuration loaded successfully');
    } catch (error) {
        console.error('Failed to load reviews configuration:', error);
    }
});

// Helper functions for common use cases
window.ReviewsHelper = {
    /**
     * Show/hide review form based on configuration
     */
    async toggleReviewForm(formElement) {
        const isEnabled = await window.ReviewsConfig.isEnabled();
        if (formElement) {
            formElement.style.display = isEnabled ? 'block' : 'none';
        }
    },

    /**
     * Validate review form before submission
     */
    async validateReviewForm(formData) {
        const config = await window.ReviewsConfig.getConfig();
        const errors = [];

        // Check if reviews are enabled
        if (!config.enabled) {
            errors.push('Đánh giá hiện tại không khả dụng');
            return { valid: false, errors };
        }

        // Check rating
        if (formData.rating && !await window.ReviewsConfig.isValidRating(formData.rating)) {
            const minRating = await window.ReviewsConfig.getMinRating();
            errors.push(`Đánh giá tối thiểu là ${minRating} sao`);
        }

        // Check images
        if (formData.images && !await window.ReviewsConfig.isValidImageCount(formData.images.length)) {
            const maxImages = await window.ReviewsConfig.getMaxImages();
            const allowsImages = await window.ReviewsConfig.allowsImages();
            
            if (!allowsImages) {
                errors.push('Không được phép tải lên hình ảnh');
            } else {
                errors.push(`Tối đa ${maxImages} hình ảnh`);
            }
        }

        return {
            valid: errors.length === 0,
            errors: errors
        };
    },

    /**
     * Apply template styling based on configuration
     */
    async applyTemplate(containerElement) {
        const template = await window.ReviewsConfig.getTemplate();
        const align = await window.ReviewsConfig.getAlign();
        
        if (containerElement) {
            containerElement.classList.add(`review-template-${template}`);
            containerElement.classList.add(`review-align-${align}`);
        }
    },

    /**
     * Setup review sorting
     */
    async setupSorting(sortElement) {
        const defaultSort = await window.ReviewsConfig.getDefaultSort();
        
        if (sortElement && sortElement.tagName === 'SELECT') {
            sortElement.value = defaultSort;
        }
    },

    /**
     * Setup pagination
     */
    async setupPagination() {
        const perPage = await window.ReviewsConfig.getPerPage();
        return perPage;
    }
};
</script>

{{-- CSS for different templates --}}
<style>
.review-template-template1 {
    /* Classic template styles */
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
}

.review-template-template2 {
    /* Modern template styles */
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.review-template-template3 {
    /* Minimal template styles */
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 0;
}

.review-align-left {
    text-align: left;
}

.review-align-center {
    text-align: center;
}

.review-align-right {
    text-align: right;
}

/* Loading state */
.reviews-loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Error state */
.reviews-error {
    color: #dc2626;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin: 0.5rem 0;
}

/* Success state */
.reviews-success {
    color: #059669;
    background-color: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin: 0.5rem 0;
}
</style>

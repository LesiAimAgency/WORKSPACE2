<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coverflow Project - Workspace</title>
    <!-- Google Fonts Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Workspace Stylesheet -->
    <link rel="stylesheet" href="{{ asset('themes/workspace/css/style.css') }}">
    <link rel="stylesheet" href="/themes/workspace/css/style.css">
</head>
<body>
    <header class="site-header" aria-label="Dieu huong chinh">
      <button class="menu-trigger" type="button" aria-label="Mo menu" aria-controls="menuOverlay" aria-expanded="false">
        <span class="menu-icon" aria-hidden="true">
          <span class="menu-line"></span>
          <span class="menu-line"></span>
        </span>
        <span>MENU</span>
      </button>
      <a href="#connect" class="contact-link" style="pointer-events: auto;">CONTACT</a>
    </header>

    <section id="coverflowProject" class="coverflowProject-section">
        <div class="container">
            <div class="coverflowProject-stage">
                <!-- Cột trái: Hình ảnh đại diện dự án -->
                <div class="coverflowProject-media">
                    <img src="{{ asset('themes/workspace/coverflow/frame1.png') }}" class="coverflowProject-image">
                </div>

                <!-- Cột phải: Thông số chi tiết dự án -->
                <div class="coverflowProject-details">
                    <div class="coverflowProject-header">
                        <div class="coverflowProject-heading">
                            <h2 class="coverflowProject-title">PROJECT 01</h2>
                        </div>
                        <span class="coverflowProject-separator"></span>
                    </div>

                    <!-- Bảng thông tin dự án (5 dòng) -->
                    <div class="coverflowProject-info">
                        <div class="coverflowProject-info-row">
                            <span class="coverflowProject-info-label">Total Area</span>
                            <span class="coverflowProject-info-value">125 m²</span>
                        </div>
                        <div class="coverflowProject-info-row">
                            <span class="coverflowProject-info-label">Location</span>
                            <span class="coverflowProject-info-value">District 2, Ho Chi Minh City</span>
                        </div>
                        <div class="coverflowProject-info-row">
                            <span class="coverflowProject-info-label">Typology</span>
                            <span class="coverflowProject-info-value">Office</span>
                        </div>
                        <div class="coverflowProject-info-row">
                            <span class="coverflowProject-info-label">Style</span>
                            <span class="coverflowProject-info-value">Modern</span>
                        </div>
                        <div class="coverflowProject-info-row">
                            <span class="coverflowProject-info-label">Tech solution</span>
                            <span class="coverflowProject-info-value">Integrated Smart-home system</span>
                        </div>
                    </div>

                    <!-- Nút View More -->
                    <div class="coverflowProject-action">
                        <a href="#" class="coverflowProject-btn">
                            <span class="coverflowProject-btn-text">Comparison Design and Result</span>
                        </a>
                    </div>

                    <!-- Điều khiển Slider (Progress Bar + 2 Nút Mũi Tên) -->
                    <div class="coverflowProject-controls">
                        <div class="coverflowProject-progress">
                            <div class="coverflowProject-progress-bar"></div>
                        </div>
                        <div class="coverflowProject-arrows">
                            <button type="button" class="coverflowProject-arrow prev" aria-label="Previous project">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="coverflowProject-arrow next" aria-label="Next project">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thanh điều hướng đáy (Navigation Tabs) -->
            <div class="coverflowProject-nav">
                <a href="{{ url('/coverflow-service') }}" class="coverflowProject-nav-tab">
                    <span class="coverflowProject-nav-text">INTRODUCTIONS</span>
                </a>
                <button type="button" class="coverflowProject-nav-tab is-active">
                    <span class="coverflowProject-nav-text">PROJECTS</span>
                </button>
                <a href="{{ url('/about-expert') }}" class="coverflowProject-nav-tab">
                    <span class="coverflowProject-nav-text">EXPERTS</span>
                </a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const prevBtn = document.querySelector('#coverflowProject .coverflowProject-arrow.prev');
            const nextBtn = document.querySelector('#coverflowProject .coverflowProject-arrow.next');
            const progressBar = document.querySelector('#coverflowProject .coverflowProject-progress-bar');
            
            let currentSlide = 0;
            const totalSlides = 3;
            
            function updateSlide(index) {
                currentSlide = (index + totalSlides) % totalSlides;
                if (progressBar) {
                    const widthPercent = Math.max(25, ((currentSlide + 1) / totalSlides) * 100);
                    progressBar.style.width = widthPercent + '%';
                }
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => updateSlide(currentSlide - 1));
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => updateSlide(currentSlide + 1));
            }
        });
    </script>
</body>
</html>

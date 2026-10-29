<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coverflow Service - Workspace</title>
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

    <section id="coverflowService" class="coverflowService-section">
        <div class="container">
            <div class="coverflowService-stage"> 
                <!-- Cột trái: Nội dung + Danh sách dịch vụ + Điều hướng -->
                <div class="coverflowService-content">
                    <div class="coverflowService-header">
                        <a href="#services" class="coverflowService-badge">
                            <span class="coverflowService-badge-text">Services</span>
                        </a>
                        <div class="coverflowService-heading">
                            <h2 class="coverflowService-title">Interior Advisory and Project Setup</h2>
                        </div>
                        <span class="coverflowService-separator"></span>
                    </div>

                    <div class="coverflowService-list">
                        <div class="coverflowService-item">
                            <h3 class="coverflowService-item-title">Interior Project Advisory</h3>
                            <p class="coverflowService-item-desc">Providing comprehensive strategic solutions aimed at orienting design concepts, optimizing budget, allocating implementation personnel, and establishing operational standards for interior projects.</p>
                        </div>
                        <div class="coverflowService-item"> 
                            <h3 class="coverflowService-item-title">Interior Project Advisory</h3>
                            <p class="coverflowService-item-desc">Strategic preparation phase reviewing design documentation cost estimation and construction schedule to ensure feasibility and risk control.</p>
                        </div>
                    </div>

                    <div class="coverflowService-nav">
                        <button type="button" class="coverflowService-nav-tab is-active" data-slide="0">
                            <span class="coverflowService-nav-text">INTRODUCTIONS</span>
                        </button>
                        <a href="{{ url('/coverflow-project') }}" class="coverflowService-nav-tab">
                            <span class="coverflowService-nav-text">PROJECTS</span>
                        </a>
                        <a href="{{ url('/about-expert') }}" class="coverflowService-nav-tab">
                            <span class="coverflowService-nav-text">EXPERTS</span>
                        </a>
                    </div>
                </div>

                <!-- Cột phải: Khung ảnh dịch vụ -->
                <div class="coverflowService-media">
                    <img src="{{ asset('themes/workspace/coverflow/frame1.png') }}" class="coverflowService-image">
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('#coverflowService .coverflowService-nav-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('is-active'));
                    this.classList.add('is-active');
                });
            });
        });
    </script>
</body>
</html>

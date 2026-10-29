<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About The Expert - Workspace</title>
    <!-- Google Fonts Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
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

    <section id="aboutExpert" class="aboutExpert-section">
        <div class="container">
            <div class="aboutExpert-stage">
                <!-- Cột trái: Tiêu đề + Vạch phân cách gradient + Bio chuyên gia + Navigation tabs -->
                <div class="aboutExpert-content">
                    <div class="aboutExpert-content-contain">
                        <div class="aboutExpert-header">
                            <h2 class="aboutExpert-title">ABOUT THE EXPERT</h2>
                            <span class="aboutExpert-separator"></span>
                        </div>

                        <div class="aboutExpert-desc">
                            <p class="aboutExpert-desc-text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                            
                        </div>
                    </div>

                    
                </div>

                <!-- Cột phải: Card chuyên gia nền 930C10 bo góc 12px -->
                <div class="aboutExpert-media">
                    <img src="{{ asset('themes/workspace/assets/Man 1.png') }}" class="aboutExpert-image">
                </div>
            </div>

           
        </div>
    </section>

    
</body>
</html>
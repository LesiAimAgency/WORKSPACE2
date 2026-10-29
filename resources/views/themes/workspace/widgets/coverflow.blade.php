<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coverflow</title>

    <link rel="stylesheet" href="./themes/workspace/css/style.css">
</head>
<body>
    

<section class="panel panel-coverflow" id="coverflow" data-ui="light">
  <div class="surface coverflow-surface">
    <div class="coverflow-layout">

      <!-- 1. Top Category Tabs (Chips Selector) -->
      <nav class="coverflow-chips">
        <button type="button" class="coverflow-chip is-active">
          Interior Advisory and Project Setup
        </button>
        <button type="button" class="coverflow-chip">
          Interior Design and FF&E Consultancy
        </button>
        <button type="button" class="coverflow-chip">
          Fit-Out and Finishing Works
        </button>
        <button type="button" class="coverflow-chip">
          Joinery Manufacturer and FF&E Installation
        </button>
        <button type="button" class="coverflow-chip">
          Material Library and Competency Management
        </button>
      </nav>

      <!-- 2. Main Visual Stage with Floating Red Badge -->
      <div class="coverflow-stage">
        <div class="coverflow-media">
          <img src="{{ asset('themes/workspace/coverflow/frame1.png') }}"/>
        </div>

        <!-- Floating Red Badge / Overlay Card -->
        <div class="coverflow-badge">
          <div class="badge-header">
            <span class="badge-num">01</span>
            <a href="#" class="view-more-btn">
              <span>View more</span>
            </a>
          </div>
          <h3 class="badge-title">
            Interior Advisory and Project Setup
          </h3>
        </div>
      </div>

      <!-- 3. Bottom Description & Navigation Arrows -->
      <div class="coverflow-footer">
        <p class="coverflow-desc">
          Providing comprehensive strategic solutions aimed at orienting design concepts, optimizing budget, allocating implementation personnel, and establishing operational standards for interior projects.
        </p>

        <div class="coverflow-controls">
          <button class="coverflow-arrow coverflow-prev" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button class="coverflow-arrow coverflow-next" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>

    </div>
  </div>
</section>

</body>
</html>
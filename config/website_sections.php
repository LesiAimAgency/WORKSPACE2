<?php

return [
    'general' => [
        'label' => 'Cấu hình chung',
        'icon' => 'settings',
        'fields' => [
            'site_name' => ['type' => 'text', 'label' => 'Tên website'],
            'site_logo' => ['type' => 'image', 'label' => 'Logo'],
            'header_height' => [
                'type' => 'number',
                'label' => 'Chiều cao Header (px)',
                'placeholder' => 'VD: 90',
                'default' => '90',
            ],
            'theme_color' => ['type' => 'color', 'label' => 'Màu chủ đề'],
            // === TYPOGRAPHY / GOOGLE FONTS ===
            'heading_font' => [
                'type' => 'google_font_picker',
                'label' => 'Font Tiêu đề (Heading h1-h6)',
                'default' => 'Roboto',
            ],
            'heading_font_weight' => [
                'type' => 'font_weight_single_select',
                'label' => 'Font Weight Tiêu đề',
                'default' => '700',
                'linked_font_field' => 'heading_font',
            ],
            'body_font' => [
                'type' => 'google_font_picker',
                'label' => 'Font Nội dung (Body / Paragraph)',
                'default' => 'Inter',
            ],
            'body_font_weight' => [
                'type' => 'font_weight_single_select',
                'label' => 'Font Weight Nội dung',
                'default' => '400',
                'linked_font_field' => 'body_font',
            ],
            'body_font_size' => [
                'type' => 'font_size_picker',
                'label' => 'Cỡ chữ Body (Base Font Size)',
                'default' => '1rem',
            ],
            // === BACKGROUND ===
            'bg_type' => ['type' => 'select', 'label' => 'Nền website', 'options' => ['color' => 'Màu', 'gradient' => 'Gradient', 'image' => 'Hình ảnh']],
            'bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'bg_gradient_start' => ['type' => 'color', 'label' => 'Gradient - Màu bắt đầu'],
            'bg_gradient_end' => ['type' => 'color', 'label' => 'Gradient - Màu kết thúc'],
            'bg_gradient_direction' => ['type' => 'select', 'label' => 'Gradient - Hướng', 'options' => ['to right' => 'Ngang', 'to bottom' => 'Dọc', 'to bottom right' => 'Chéo']],
            'bg_image' => ['type' => 'image', 'label' => 'Hình nền'],
            'bg_image_size' => ['type' => 'select', 'label' => 'Background Size', 'options' => ['cover' => 'Cover', 'contain' => 'Contain', 'auto' => 'Auto']],
            'bg_image_position' => ['type' => 'select', 'label' => 'Background Position', 'options' => ['center' => 'Center', 'top' => 'Top', 'bottom' => 'Bottom', 'left' => 'Left', 'right' => 'Right']],
            'bg_image_repeat' => ['type' => 'select', 'label' => 'Background Repeat', 'options' => ['no-repeat' => 'No Repeat', 'repeat' => 'Repeat', 'repeat-x' => 'Repeat X', 'repeat-y' => 'Repeat Y']],
        ],
    ],
    'topbar' => [
        'label' => 'Top Bar (Tailwind UI)',
        'icon' => 'align-justify',
        'fields' => [
            'topbar_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị Top Bar'],
            'topbar_style' => [
                'type' => 'select',
                'label' => 'Kiểu Giao diện Top Bar (Tailwind Blocks)',
                'options' => [
                    'style-1' => '1. Marquee Announcement (Thanh thông báo khuyến mãi chạy chữ)',
                    'style-2' => '2. Minimal Contact Row (Địa chỉ, Hotline & Giờ làm việc)',
                    'style-3' => '3. Promotional Pill (Hộp khuyến mãi bo tròn căn giữa)',
                ],
            ],
            'topbar_menu_id' => ['type' => 'menu_select', 'label' => 'Menu Topbar'],
            'topbar_text' => ['type' => 'text', 'label' => 'Nội dung thông báo'],
            'topbar_bg_color' => ['type' => 'color', 'label' => 'Màu nền Top Bar'],
            'topbar_text_color' => ['type' => 'color', 'label' => 'Màu chữ Top Bar'],
        ],
    ],
    'header' => [
        'label' => 'Header Desktop (Tailwind Blocks)',
        'icon' => 'layout',
        'fields' => [
            'header_layout' => [
                'type' => 'select',
                'label' => 'Layout Header Desktop (Tailwind UI Blocks)',
                'options' => [
                    'style-1' => '1. Modern Light Header (Logo Trái - Menu Giữa - Actions Phải)',
                    'style-2' => '2. Centered Stacked Header (Logo Căn Giữa - Menu bên dưới)',
                    'style-3' => '3. Minimal Search Header (Khung Tìm Kiếm Rộng - Tối Giản)',
                    'style-4' => '4. Glassmorphism Sticky Header (Thanh Kính Mờ - Sticky Header)',
                ],
            ],
            'header_height' => [
                'type' => 'number',
                'label' => 'Chiều cao Header Desktop (px)',
                'placeholder' => 'VD: 90',
                'default' => '90',
            ],
            'header_sticky' => ['type' => 'checkbox', 'label' => 'Header dính khi cuộn trang (Sticky)'],
            'header_bg_color' => ['type' => 'color', 'label' => 'Màu nền Header'],
            'header_text_color' => ['type' => 'color', 'label' => 'Màu chữ Header'],
            'show_search' => ['type' => 'checkbox', 'label' => 'Hiển thị icon Tìm kiếm'],
            'show_cart' => ['type' => 'checkbox', 'label' => 'Hiển thị icon Giỏ hàng & Badge số lượng'],
            'show_account' => ['type' => 'checkbox', 'label' => 'Hiển thị icon Tài khoản'],
            'show_hotline_badge' => ['type' => 'checkbox', 'label' => 'Hiển thị Huy hiệu Hotline cấp tốc'],
        ],
    ],
    'header_mobile' => [
        'label' => 'Header Mobile (Tailwind Blocks)',
        'icon' => 'smartphone',
        'fields' => [
            'mobile_menu_style' => [
                'type' => 'select',
                'label' => 'Kiểu Giao diện Mobile (Tailwind UI Blocks)',
                'options' => [
                    'fullscreen' => '1. Toàn màn hình Light (Tailwind Full-Screen Flyout Overlay)',
                    'sidebar' => '2. Sidebar Trượt Bên Phải (Tailwind Slide-Over Drawer)',
                    'top_dropdown' => '3. Dropdown Thả Xuống (Tailwind Accordion Dropdown)',
                    'minimal_sheet' => '4. Bottom Sheet Tối Giản (Tailwind Bottom Sheet Drawer)',
                ],
            ],
            'show_mobile_menu_text' => [
                'type' => 'checkbox',
                'label' => 'Hiển thị Chữ (Text) trên Nút Menu Mobile',
            ],
            'mobile_menu_button_text' => [
                'type' => 'text',
                'label' => 'Nội dung Chữ Nút Menu Mobile',
                'placeholder' => 'VD: MENU',
                'default' => 'MENU',
            ],
            'mobile_theme_preset' => [
                'type' => 'select',
                'label' => 'Tone Màu & Theme Tailwind Mobile',
                'options' => [
                    'light_clean' => 'Clean White (Trắng tinh tế - Modern Light)',
                    'slate_modern' => 'Slate Crisp (Xám nhạt & Blue Accent)',
                    'glassmorphism' => 'Glassmorphism (Kính mờ Backdrop Blur)',
                    'amber_warm' => 'Warm Neutral (Ấm áp & Thảo dược)',
                ],
            ],
            'mobile_sticky_header' => ['type' => 'checkbox', 'label' => 'Cố định Header khi cuộn trang (Sticky Header)'],
            'mobile_backdrop_blur' => ['type' => 'checkbox', 'label' => 'Bật hiệu ứng mờ nền (Backdrop Blur Glass)'],
            'mobile_show_search' => ['type' => 'checkbox', 'label' => 'Hiển thị ô tìm kiếm nhanh'],
            'mobile_show_cart' => ['type' => 'checkbox', 'label' => 'Hiển thị icon Giỏ hàng & Badge số lượng'],
            'mobile_show_hotline' => ['type' => 'checkbox', 'label' => 'Hiển thị Nút gọi Hotline cấp tốc'],
            'mobile_show_social' => ['type' => 'checkbox', 'label' => 'Hiển thị liên kết Mạng xã hội'],
            'mobile_bg_color' => ['type' => 'color', 'label' => 'Màu nền Menu Mobile'],
            'mobile_text_color' => ['type' => 'color', 'label' => 'Màu chữ Menu Mobile'],
        ],
    ],
    'navigation' => [
        'label' => 'Navigation (Menu chính)',
        'icon' => 'menu',
        'fields' => [
            'navigation_menu_id' => ['type' => 'menu_select', 'label' => 'Chọn Menu Navigation'],
            'nav_style' => [
                'type' => 'select',
                'label' => 'Kiểu dáng Menu Navigation (Tailwind Blocks)',
                'options' => [
                    'horizontal' => '1. Thanh Ngang Tiêu Chuẩn (Horizontal Navigation Links)',
                    'mega' => '2. Mega Menu Đa Cấp (Full-Width Dropdown Category Grid)',
                    'pills' => '3. Pill Badges Tabs (Thẻ Bo Tròn Hiện Đại)',
                    'underline_glow' => '4. Underline Indicator (Gạch Dưới Phát Sáng)',
                ],
            ],
            'nav_bg_color' => ['type' => 'color', 'label' => 'Màu nền Thanh Navigation'],
            'nav_text_color' => ['type' => 'color', 'label' => 'Màu chữ Menu'],
            'nav_hover_color' => ['type' => 'color', 'label' => 'Màu Hover Menu'],
        ],
    ],
    'map' => [
        'label' => 'Bản đồ / Google Map',
        'icon' => 'map-pin',
        'fields' => [
            'map_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị bản đồ'],
            'map_iframe' => ['type' => 'textarea', 'label' => 'Google Maps Iframe'],
        ],
    ],
    'footer' => [
        'label' => 'Footer (Chân trang)',
        'icon' => 'align-bottom',
        'fields' => [
            'footer_layout' => [
                'type' => 'select',
                'label' => 'Layout Footer (Tailwind UI Blocks)',
                'options' => [
                    '4-columns' => '1. 4 Cột Standard Tailwind UI (Giới thiệu, Menu, Sản phẩm, Đăng ký tin)',
                    '3-columns' => '2. 3 Cột Cân Bằng (Thông tin doanh nghiệp, Danh mục, Chi nhánh)',
                    'minimal' => '3. Minimal Footer (Dòng Bản Quyền Tối Giản & Social Icons)',
                ],
            ],
            'footer_bg_color' => ['type' => 'color', 'label' => 'Màu nền Footer'],
            'footer_text_color' => ['type' => 'color', 'label' => 'Màu chữ Footer'],
            'footer_copyright' => ['type' => 'textarea', 'label' => 'Nội dung Copyright'],
            'footer_about' => ['type' => 'textarea', 'label' => 'Đoạn Văn Giới thiệu'],
        ],
    ],

    'branches' => [
        'label' => 'Chi Nhánh',
        'icon' => 'map',
        'fields' => [
            'show_branches' => ['type' => 'checkbox', 'label' => 'Hiển thị chi nhánh'],
            'branches_title' => ['type' => 'text', 'label' => 'Tiêu đề'],
        ],
    ],
    'posts' => [
        'label' => 'Bài viết',
        'icon' => 'file-text',
        'fields' => [
            'posts_per_page' => ['type' => 'number', 'label' => 'Số bài/trang', 'default' => 12],
            'show_author' => ['type' => 'checkbox', 'label' => 'Hiển thị tác giả'],
            'show_date' => ['type' => 'checkbox', 'label' => 'Hiển thị ngày'],
            'show_excerpt' => ['type' => 'checkbox', 'label' => 'Hiển thị mô tả ngắn'],
        ],
    ],
    'products' => [
        'label' => 'Sản phẩm',
        'icon' => 'shopping-bag',
        'fields' => [
            'products_per_page' => ['type' => 'number', 'label' => 'Số sản phẩm/trang', 'default' => 16],
            'show_quick_view' => ['type' => 'checkbox', 'label' => 'Quick view'],
            'show_compare' => ['type' => 'checkbox', 'label' => 'So sánh sản phẩm'],
            'show_wishlist' => ['type' => 'checkbox', 'label' => 'Yêu thích'],
        ],
    ],
    'floating_cart' => [
        'label' => 'Nút giỏ hàng nổi',
        'icon' => 'shopping-cart',
        'fields' => [
            'floating_cart_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị'],
            'floating_cart_position' => ['type' => 'select', 'label' => 'Vị trí', 'options' => ['bottom-right' => 'Dưới phải', 'bottom-left' => 'Dưới trái', 'top-right' => 'Trên phải']],
            'floating_cart_color' => ['type' => 'color', 'label' => 'Màu nút'],
        ],
    ],
    'contact_form' => [
        'label' => 'Form tư vấn',
        'icon' => 'message-square',
        'fields' => [
            'form_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị form'],
            'form_title' => ['type' => 'text', 'label' => 'Tiêu đề'],
            'form_position' => ['type' => 'select', 'label' => 'Vị trí', 'options' => ['sidebar' => 'Sidebar', 'popup' => 'Popup', 'footer' => 'Footer']],
            'form_fields' => ['type' => 'textarea', 'label' => 'Các trường (JSON)'],
        ],
    ],
];

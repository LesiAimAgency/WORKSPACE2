<?php

return [
    'post' => [
        'name' => 'Bài viết',
        'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo', 'categories'],
        'fields' => [
            // Custom fields specific to post
        ],
    ],
    'page' => [
        'name' => 'Trang',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'supports' => ['title', 'content', 'featured_image', 'seo', 'template'],
        'fields' => [],
    ],
    'product' => [
        'name' => 'Sản phẩm',
        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo'],
        'fields' => [
            'price' => [
                'type' => 'number',
                'label' => 'Giá bán',
                'default' => 0,
            ],
            'sale_price' => [
                'type' => 'number',
                'label' => 'Giá khuyến mãi',
                'default' => null,
            ],
            'sku' => [
                'type' => 'text',
                'label' => 'Mã SKU',
                'default' => '',
            ],
            'stock' => [
                'type' => 'number',
                'label' => 'Số lượng tồn kho',
                'default' => 0,
            ],
        ],
    ],
    'project' => [
        'name' => 'Dự án',
        'singular_name' => 'Dự án',
        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery', 'categories'],
        'fields' => [
            'scale' => [
                'type' => 'text',
                'label' => 'QUY MÔ / DIỆN TÍCH (VD: 3.500 m² - Trạm TBA 630kVA)',
                'default' => '',
            ],
            'location' => [
                'type' => 'text',
                'label' => 'VỊ TRÍ (VD: Bến Nghé, Quận 1, TP. Hồ Chí Minh)',
                'default' => '',
            ],
            'category' => [
                'type' => 'text',
                'label' => 'LOẠI HÌNH DỰ ÁN (VD: Nhà Hàng, Khách Sạn)',
                'default' => '',
            ],
            'client' => [
                'type' => 'text',
                'label' => 'CHỦ ĐẦU TƯ / ĐỐI TÁC (VD: Tập đoàn The Long Hospitality)',
                'default' => '',
            ],
        ],
    ],
    'faq' => [
        'name' => 'Câu hỏi thường gặp (FAQ)',
        'singular_name' => 'Câu hỏi thường gặp',
        'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'supports' => ['title', 'content', 'excerpt'],
        'fields' => [
            'order' => [
                'type' => 'number',
                'label' => 'Thứ tự hiển thị (Số nhỏ xếp trước)',
                'default' => 0,
            ],
        ],
    ],
    'task' => [
        'name' => 'Công việc (Task)',
        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'supports' => ['title', 'content', 'featured_image'],
        'fields' => [
            'priority' => [
                'type' => 'select',
                'label' => 'Độ ưu tiên',
                'options' => [
                    'low' => 'Thấp',
                    'medium' => 'Trung bình',
                    'high' => 'Cao',
                    'urgent' => 'Khẩn cấp',
                ],
                'default' => 'medium',
            ],
            'deadline' => [
                'type' => 'date',
                'label' => 'Hạn chót (Deadline)',
            ],
            'assigned_to' => [
                'type' => 'user_select',
                'label' => 'Người phụ trách',
                'role' => 'employee',
            ],
        ],
    ],
    'contract' => [
        'name' => 'Hợp đồng',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'supports' => ['title', 'content', 'featured_image'],
        'fields' => [
            'client_name' => [
                'type' => 'text',
                'label' => 'Tên khách hàng/Đối tác',
            ],
            'value' => [
                'type' => 'number',
                'label' => 'Giá trị hợp đồng',
            ],
            'valid_from' => [
                'type' => 'date',
                'label' => 'Ngày bắt đầu',
            ],
            'valid_until' => [
                'type' => 'date',
                'label' => 'Ngày kết thúc',
            ],
        ],
    ],
    'ticket' => [
        'name' => 'Ticket Hỗ trợ',
        'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
        'supports' => ['title', 'content'],
        'fields' => [
            'customer_name' => [
                'type' => 'text',
                'label' => 'Tên khách hàng',
            ],
            'customer_email' => [
                'type' => 'email',
                'label' => 'Email liên hệ',
            ],
            'priority' => [
                'type' => 'select',
                'label' => 'Mức độ',
                'options' => [
                    'normal' => 'Bình thường',
                    'high' => 'Cao',
                    'urgent' => 'Khẩn cấp',
                ],
                'default' => 'normal',
            ],
            'assigned_to' => [
                'type' => 'user_select',
                'label' => 'Người xử lý',
                'role' => 'employee',
            ],
        ],
    ],
    'order' => [
        'name' => 'Đơn hàng',
        'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'supports' => ['title', 'content'],
        'fields' => [
            'total_amount' => [
                'type' => 'number',
                'label' => 'Tổng tiền',
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Trạng thái',
                'options' => [
                    'pending' => 'Chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                ],
                'default' => 'pending',
            ],
        ],
    ],
    'newsletter' => [
        'name' => 'Newsletter',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'supports' => ['title', 'content'],
        'fields' => [
            'email' => [
                'type' => 'email',
                'label' => 'Email đăng ký',
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Trạng thái',
                'options' => [
                    'active' => 'Hoạt động',
                    'unsubscribed' => 'Đã hủy đăng ký',
                ],
                'default' => 'active',
            ],
        ],
    ],
    'feedback' => [
        'name' => 'Feedback',
        'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586l-2.828-2.828A2 2 0 014 14.172V6a2 2 0 012-2h6a2 2 0 012 2v2',
        'supports' => ['title', 'content'],
        'fields' => [
            'rating' => [
                'type' => 'number',
                'label' => 'Đánh giá (1-5 sao)',
            ],
            'customer_name' => [
                'type' => 'text',
                'label' => 'Tên khách hàng',
            ],
        ],
    ],
    // BẤT ĐỘNG SẢN
    'property' => [
        'name' => 'Bất động sản',
        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'supports' => ['title', 'content', 'excerpt', 'featured_image', 'seo', 'gallery'],
        'fields' => [
            'price' => [
                'type' => 'number',
                'label' => 'Giá bán/thuê',
            ],
            'area' => [
                'type' => 'text',
                'label' => 'Diện tích',
            ],
            'location' => [
                'type' => 'text',
                'label' => 'Vị trí',
            ],
        ],
    ],
    'agent' => [
        'name' => 'Môi giới',
        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'supports' => ['title', 'content', 'featured_image'],
        'fields' => [
            'phone' => [
                'type' => 'text',
                'label' => 'Số điện thoại',
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
            ],
        ],
    ],
    'gallery_360' => [
        'name' => 'Thư viện ảnh 360',
        'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'supports' => ['title', 'content'],
        'fields' => [
            'iframe_url' => [
                'type' => 'text',
                'label' => 'Đường dẫn 360 (Iframe)',
            ],
        ],
    ],
    // KHÁCH SẠN
    'room' => [
        'name' => 'Phòng',
        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'supports' => ['title', 'content', 'featured_image', 'gallery'],
        'fields' => [
            'price_per_night' => [
                'type' => 'number',
                'label' => 'Giá mỗi đêm',
            ],
            'capacity' => [
                'type' => 'number',
                'label' => 'Sức chứa',
            ],
        ],
    ],
    'hotel_booking' => [
        'name' => 'Đặt phòng',
        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'supports' => ['title', 'content'],
        'fields' => [
            'check_in' => [
                'type' => 'date',
                'label' => 'Ngày nhận phòng',
            ],
            'check_out' => [
                'type' => 'date',
                'label' => 'Ngày trả phòng',
            ],
            'customer_name' => [
                'type' => 'text',
                'label' => 'Tên khách hàng',
            ],
        ],
    ],
    'amenity' => [
        'name' => 'Tiện nghi',
        'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
        'supports' => ['title', 'featured_image'],
        'fields' => [],
    ],
    // Y TẾ
    'doctor' => [
        'name' => 'Bác sĩ',
        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'supports' => ['title', 'content', 'featured_image'],
        'fields' => [
            'specialty' => [
                'type' => 'text',
                'label' => 'Chuyên khoa',
            ],
            'experience' => [
                'type' => 'text',
                'label' => 'Kinh nghiệm',
            ],
        ],
    ],
    'patient' => [
        'name' => 'Bệnh nhân',
        'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'supports' => ['title', 'content'],
        'fields' => [
            'dob' => [
                'type' => 'date',
                'label' => 'Ngày sinh',
            ],
            'phone' => [
                'type' => 'text',
                'label' => 'Số điện thoại',
            ],
        ],
    ],
    'prescription' => [
        'name' => 'Đơn thuốc',
        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'supports' => ['title', 'content'],
        'fields' => [
            'patient_id' => [
                'type' => 'text',
                'label' => 'Mã Bệnh nhân',
            ],
        ],
    ],
    'appointment' => [
        'name' => 'Lịch khám',
        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'supports' => ['title', 'content'],
        'fields' => [
            'appointment_date' => [
                'type' => 'date',
                'label' => 'Ngày hẹn',
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Trạng thái',
                'options' => [
                    'pending' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'completed' => 'Đã khám',
                    'cancelled' => 'Đã hủy',
                ],
                'default' => 'pending',
            ],
        ],
    ],
];

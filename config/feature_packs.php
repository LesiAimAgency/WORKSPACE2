<?php

return [
    'groups' => [
        'medical' => [
            'label' => 'Y tế & Phòng khám',
            'icon' => '',
            'color' => 'red',
            'description' => 'Hệ thống quản lý phòng khám, bệnh viện và y tế',
            'features' => [
                'booking' => [
                    'name' => 'Đặt lịch khám (Booking)',
                    'description' => 'Cho phép bệnh nhân đặt lịch hẹn khám online',
                ],
                'doctor' => [
                    'name' => 'Quản lý Bác sĩ (Doctor)',
                    'description' => 'Hồ sơ bác sĩ, lịch làm việc và chuyên khoa',
                ],
                'patient' => [
                    'name' => 'Quản lý Bệnh nhân (Patient)',
                    'description' => 'Hồ sơ bệnh nhân, lịch sử khám chữa bệnh',
                ],
                'prescription' => [
                    'name' => 'Quản lý Đơn thuốc (Prescription)',
                    'description' => 'Kê đơn thuốc và quản lý toa thuốc điện tử',
                ],
            ],
        ],
        'hotel' => [
            'label' => 'Khách sạn & Lưu trú',
            'icon' => '',
            'color' => 'blue',
            'description' => 'Hệ thống quản lý khách sạn và dịch vụ lưu trú',
            'features' => [
                'room' => [
                    'name' => 'Quản lý Phòng (Room)',
                    'description' => 'Danh mục phòng, giá cả và tình trạng phòng',
                ],
                'hotel_booking' => [
                    'name' => 'Đặt phòng (Hotel Booking)',
                    'description' => 'Hệ thống đặt phòng online và quản lý booking',
                ],
                'amenity' => [
                    'name' => 'Tiện nghi & Dịch vụ (Amenity)',
                    'description' => 'Quản lý tiện nghi, dịch vụ khách sạn',
                ],
            ],
        ],
        'real_estate' => [
            'label' => 'Bất động sản',
            'icon' => '',
            'color' => 'yellow',
            'description' => 'Hệ thống quản lý và đăng bán bất động sản',
            'features' => [
                'property' => [
                    'name' => 'Quản lý Bất động sản (Property)',
                    'description' => 'Danh sách dự án, căn hộ và thông tin BĐS',
                ],
                'property_category' => [
                    'name' => 'Danh mục Bất động sản',
                    'description' => 'Quản lý các danh mục phân loại bất động sản',
                ],
            ],
        ],
        'ecommerce' => [
            'label' => 'Thương mại điện tử',
            'icon' => '',
            'color' => 'green',
            'description' => 'Hệ thống mua bán, giỏ hàng và thanh toán online',
            'features' => [
                'commerce' => [
                    'name' => 'Giỏ hàng & Đặt hàng (Commerce)',
                    'description' => 'Giỏ hàng, thanh toán và quản lý đơn hàng',
                ],
                'product_listing' => [
                    'name' => 'Danh sách sản phẩm (Listing)',
                    'description' => 'Hiển thị và lọc danh sách sản phẩm nâng cao',
                ],
            ],
        ],
        'general' => [
            'label' => 'Tính năng chung',
            'icon' => '️',
            'color' => 'purple',
            'description' => 'Các tính năng phổ biến cho mọi loại website',
            'features' => [
                'blog' => [
                    'name' => 'Tin tức & Blog',
                    'description' => 'Đăng bài viết, tin tức và quản lý danh mục',
                ],
                'contact' => [
                    'name' => 'Form Liên hệ',
                    'description' => 'Form liên hệ với email tự động và quản lý inbox',
                ],
                'gallery' => [
                    'name' => 'Thư viện hình ảnh',
                    'description' => 'Quản lý album ảnh và thư viện media',
                ],
            ],
        ],
    ],
];

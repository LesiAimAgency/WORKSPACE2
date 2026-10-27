<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo liên hệ mới - {{ $siteName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #334155; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container (max-width: 600px) -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #131313 0%, #1e293b 100%); padding: 32px 36px 28px; border-bottom: 4px solid #EC460B; text-align: left;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 22px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; margin-bottom: 4px;">
                                            in <span style="color: #EC460B;">•</span> between
                                        </div>
                                        <div style="font-size: 13px; color: #94a3b8; font-weight: 400; text-transform: uppercase; letter-spacing: 1px;">
                                            {{ $siteName }} &bull; Hệ thống thông báo
                                        </div>
                                    </td>
                                    <td align="right" valign="top">
                                        @if(!empty($isTest))
                                            <span style="display: inline-block; background-color: #3b82f6; color: #ffffff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                TEST SMTP
                                            </span>
                                        @else
                                            <span style="display: inline-block; background-color: #EC460B; color: #ffffff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                MỚI NHẬN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="padding: 24px 36px 12px; background-color: #fff7ed; border-bottom: 1px solid #fed7aa;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td width="36" valign="top">
                                        <div style="width: 32px; height: 32px; background-color: #ea580c; border-radius: 50%; text-align: center; line-height: 32px; color: #ffffff; font-size: 16px;">
                                            &#9993;
                                        </div>
                                    </td>
                                    <td style="padding-left: 12px;">
                                        <h1 style="margin: 0; font-size: 18px; font-weight: 700; color: #9a3412;">
                                            @if(!empty($isTest))
                                                Kiểm tra cấu hình SMTP thành công!
                                            @else
                                                Có khách hàng vừa gửi thông tin liên hệ!
                                            @endif
                                        </h1>
                                        <p style="margin: 3px 0 0; font-size: 13px; color: #c2410c;">
                                            Dưới đây là chi tiết thông tin khách hàng để lại:
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Customer Highlight Card -->
                    <tr>
                        <td style="padding: 24px 36px 8px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">
                                            Khách hàng liên hệ
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">
                                            {{ $fullname }}
                                        </div>
                                        <div>
                                            @if(!empty($phone))
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" style="display: inline-block; background-color: #EC460B; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; margin-right: 8px; margin-bottom: 6px;">
                                                    &#128222; Gọi ngay: {{ $phone }}
                                                </a>
                                            @endif
                                            @if(!empty($customerEmail))
                                                <a href="mailto:{{ $customerEmail }}" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; margin-bottom: 6px;">
                                                    &#9993; Gửi Email
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Details Table -->
                    <tr>
                        <td style="padding: 16px 36px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; font-size: 14px;">
                                
                                <!-- Phone -->
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td width="35%" style="padding: 12px 8px; color: #64748b; font-weight: 500;">
                                        Số điện thoại
                                    </td>
                                    <td style="padding: 12px 8px; color: #0f172a; font-weight: 600;">
                                        {{ $phone ?: '(Không có)' }}
                                    </td>
                                </tr>

                                <!-- Email -->
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; color: #64748b; font-weight: 500;">
                                        Email
                                    </td>
                                    <td style="padding: 12px 8px; color: #0f172a; font-weight: 600;">
                                        {{ $customerEmail ?: '(Không có)' }}
                                    </td>
                                </tr>

                                <!-- Service / Subject -->
                                @if(!empty($data['service']) || !empty($data['subject']))
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; color: #64748b; font-weight: 500;">
                                        Dịch vụ quan tâm
                                    </td>
                                    <td style="padding: 12px 8px; color: #0f172a; font-weight: 600;">
                                        <span style="display: inline-block; background-color: #eff6ff; color: #1d4ed8; padding: 3px 10px; border-radius: 6px; font-size: 13px;">
                                            {{ $data['service'] ?? $data['subject'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endif

                                <!-- Company -->
                                @if(!empty($data['company']) || !empty($data['company_name']))
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; color: #64748b; font-weight: 500;">
                                        Doanh nghiệp / Công ty
                                    </td>
                                    <td style="padding: 12px 8px; color: #0f172a; font-weight: 600;">
                                        {{ $data['company'] ?? $data['company_name'] }}
                                    </td>
                                </tr>
                                @endif

                                <!-- Message -->
                                @if(!empty($data['message']))
                                <tr>
                                    <td valign="top" style="padding: 12px 8px; color: #64748b; font-weight: 500;">
                                        Nội dung / Lời nhắn
                                    </td>
                                    <td style="padding: 12px 8px; color: #1e293b; background-color: #f8fafc; border-radius: 8px; font-style: normal; line-height: 1.6;">
                                        {!! nl2br(e($data['message'])) !!}
                                    </td>
                                </tr>
                                @endif

                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 36px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 4px;">
                                {{ $siteName }}
                            </div>
                            <div>
                                Email thông báo tự động từ hệ thống website. Vui lòng không trả lời trực tiếp email này nếu không có địa chỉ người gửi.
                            </div>
                            <div style="margin-top: 8px; color: #cbd5e1; font-size: 11px;">
                                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

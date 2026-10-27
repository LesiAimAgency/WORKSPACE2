<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm ơn bạn đã liên hệ với {{ $siteName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #334155; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container (max-width: 600px) -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #131313 0%, #1e293b 100%); padding: 36px 40px; border-bottom: 4px solid #EC460B; text-align: center;">
                            <div style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; margin-bottom: 6px;">
                                in <span style="color: #EC460B;">•</span> between
                            </div>
                            <div style="font-size: 13px; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 2px;">
                                Gateway to Vietnam
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 40px 28px;">
                            <h2 style="margin: 0 0 16px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Xin chào {{ $fullname }},
                            </h2>
                            <p style="margin: 0 0 16px; font-size: 15px; color: #475569; line-height: 1.7;">
                                Cảm ơn bạn đã quan tâm và để lại thông tin liên hệ với <strong>{{ $siteName }}</strong>. Chúng tôi đã nhận được thông tin yêu cầu của bạn thành công.
                            </p>
                            <p style="margin: 0 0 24px; font-size: 15px; color: #475569; line-height: 1.7;">
                                Đội ngũ chuyên gia của chúng tôi đang xem xét yêu cầu và sẽ chủ động liên hệ lại với bạn trong vòng <strong>24 giờ làm việc</strong> qua số điện thoại hoặc email đã cung cấp.
                            </p>

                            <!-- Submission Summary Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px;">
                                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 12px;">
                                    Thông tin bạn đã gửi:
                                </div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
                                    @if(!empty($data['phone']))
                                    <tr>
                                        <td width="35%" style="padding: 6px 0; color: #64748b;">Số điện thoại:</td>
                                        <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">{{ $data['phone'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($data['service']))
                                    <tr>
                                        <td style="padding: 6px 0; color: #64748b;">Dịch vụ quan tâm:</td>
                                        <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">{{ $data['service'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($data['message']))
                                    <tr>
                                        <td valign="top" style="padding: 6px 0; color: #64748b;">Nội dung:</td>
                                        <td style="padding: 6px 0; color: #334155; font-style: italic;">"{{ $data['message'] }}"</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Need immediate help box -->
                            <div style="background: #fff7ed; border-left: 4px solid #EC460B; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 28px;">
                                <div style="font-size: 14px; font-weight: 600; color: #9a3412; margin-bottom: 4px;">
                                    Cần hỗ trợ gấp?
                                </div>
                                <div style="font-size: 13px; color: #7c2d12; line-height: 1.6;">
                                    Vui lòng liên hệ trực tiếp với chúng tôi qua email: 
                                    <a href="mailto:{{ setting('mail_from_address', 'contact@inbetween.vn') }}" style="color: #EC460B; font-weight: 600; text-decoration: underline;">
                                        {{ setting('mail_from_address', 'contact@inbetween.vn') }}
                                    </a>
                                </div>
                            </div>

                            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Trân trọng,<br>
                                <strong style="color: #0f172a;">Đội ngũ {{ $siteName }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 4px;">
                                {{ $siteName }}
                            </div>
                            <div>
                                Email này được gửi tự động để xác nhận chúng tôi đã nhận được thông tin liên hệ của bạn.
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

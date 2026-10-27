@php
  $targetUrl = $surveyUrl ?? ($invitation->getSurveyUrl());
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>{{ $subject ?? 'Thư mời khảo sát khách hàng' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f4f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111111;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f6f4f0; padding: 30px 15px;">
    <tr>
      <td align="center">
        <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #e5e5e5; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; width: 100%;">
          
          <!-- Header Bar -->
          <tr>
            <td style="padding: 24px 30px; border-bottom: 2px solid #E30613; background-color: #ffffff;">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left">
                    <span style="font-size: 20px; font-weight: 800; letter-spacing: 1px; color: #111111;">WORKSPACE</span>
                    <span style="font-size: 11px; color: #E30613; font-weight: bold; margin-left: 6px;">® INTERIOR</span>
                  </td>
                  <td align="right">
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #888888;">Khảo sát chất lượng</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Main Body -->
          <tr>
            <td style="padding: 35px 30px 25px 30px;">
              <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 16px 0; color: #111111; line-height: 1.3;">
                Kính gửi Quý Khách hàng{{ !empty($invitation->recipient_name) ? ' ' . $invitation->recipient_name : '' }},
              </h2>
              
              <div style="font-size: 14px; line-height: 1.7; color: #333333; margin-bottom: 20px;">
                {!! nl2br(e($customMessage ?? "Nhằm không ngừng hoàn thiện và nâng cao chất lượng dịch vụ thi công nội thất, Workspace trân trọng kính mời Quý khách dành ít phút đánh giá sự hài lòng về dự án vừa qua.\n\nÝ kiến đóng góp quý báu của Quý vị là kim chỉ nam giúp đội ngũ chúng tôi hoàn thiện hơn mỗi ngày.")) !!}
              </div>

              @if(!empty($invitation->project_name))
                <div style="background-color: #fff8f8; border-left: 4px solid #E30613; padding: 12px 18px; margin-bottom: 25px;">
                  <span style="font-size: 12px; text-transform: uppercase; color: #888888; letter-spacing: 0.5px;">Dự án đánh giá:</span>
                  <div style="font-size: 15px; font-weight: bold; color: #111111; margin-top: 2px;">{{ $invitation->project_name }}</div>
                </div>
              @endif

              <!-- CTA Button -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 30px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ $targetUrl }}"
                       target="_blank"
                       style="display: inline-block; background-color: #E30613; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; letter-spacing: 1px; padding: 14px 34px; border-radius: 4px; box-shadow: 0 4px 12px rgba(227,6,19,0.3);">
                      BẮT ĐẦU ĐÁNH GIÁ NGAY &rarr;
                    </a>
                  </td>
                </tr>
              </table>

              <p style="font-size: 12px; color: #888888; text-align: center; margin: 20px 0 0 0; line-height: 1.5;">
                Nếu không thể nhấp vào nút trên, Quý khách vui lòng sao chép liên kết này vào trình duyệt:<br>
                <a href="{{ $targetUrl }}" style="color: #E30613; word-break: break-all;">{{ $targetUrl }}</a>
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding: 20px 30px; background-color: #111111; text-align: center; color: #ffffff;">
              <p style="margin: 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #aaaaaa;">
                WORKSPACE DESIGN &amp; BUILD SOLUTIONS
              </p>
              <p style="margin: 4px 0 0 0; font-size: 11px; color: #666666;">
                Trân trọng cảm ơn Quý Khách hàng đã tin tưởng và đồng hành cùng chúng tôi.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>

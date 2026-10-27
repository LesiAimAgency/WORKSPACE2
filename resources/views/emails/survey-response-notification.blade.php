<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Thông báo phản hồi khảo sát mới</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f4f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111111;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f6f4f0; padding: 30px 15px;">
    <tr>
      <td align="center">
        <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #e5e5e5; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; width: 100%;">
          
          <!-- Header Bar -->
          <tr>
            <td style="padding: 20px 30px; border-bottom: 2px solid #16A34A; background-color: #ffffff;">
              <span style="font-size: 16px; font-weight: bold; color: #16A34A;">🔔 THÔNG BÁO KHẢO SÁT KHÁCH HÀNG MỚI</span>
            </td>
          </tr>

          <!-- Main Body -->
          <tr>
            <td style="padding: 30px;">
              <h3 style="margin: 0 0 16px 0; font-size: 18px; color: #111111;">
                Khách hàng vừa hoàn thành khảo sát chất lượng:
              </h3>

              <table width="100%" border="0" cellspacing="0" cellpadding="8" style="background-color: #fcfcf9; border: 1px solid #eeeeee; font-size: 14px; margin-bottom: 20px;">
                <tr>
                  <td width="35%" style="color: #666666; font-weight: 600;">Khách hàng / Công ty:</td>
                  <td style="color: #111111; font-weight: bold;">{{ $surveyResponse->customer_name }}</td>
                </tr>
                <tr>
                  <td style="color: #666666; font-weight: 600;">Tên dự án:</td>
                  <td style="color: #111111; font-weight: bold;">{{ $surveyResponse->project_name }}</td>
                </tr>
                <tr>
                  <td style="color: #666666; font-weight: 600;">Ngày bàn giao:</td>
                  <td>{{ $surveyResponse->handover_date ? $surveyResponse->handover_date->format('d/m/Y') : 'Chưa nhập' }}</td>
                </tr>
                @if($surveyResponse->pm_name)
                <tr>
                  <td style="color: #666666; font-weight: 600;">QLDA phụ trách:</td>
                  <td>{{ $surveyResponse->pm_name }}</td>
                </tr>
                @endif
                @if($surveyResponse->customer_email)
                <tr>
                  <td style="color: #666666; font-weight: 600;">Email:</td>
                  <td>{{ $surveyResponse->customer_email }}</td>
                </tr>
                @endif
              </table>

              <!-- Score Summary Badges -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 25px;">
                <tr>
                  <td width="32%" style="background-color: #111111; color: #ffffff; text-align: center; padding: 15px; border-radius: 4px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #aaaaaa; letter-spacing: 0.5px;">Phần A (Bàn giao)</div>
                    <div style="font-size: 22px; font-weight: bold; margin-top: 4px;">{{ $surveyResponse->score_a !== null ? number_format($surveyResponse->score_a, 1) : '--' }} / 5.0</div>
                  </td>
                  <td width="2%">&nbsp;</td>
                  <td width="32%" style="background-color: #E30613; color: #ffffff; text-align: center; padding: 15px; border-radius: 4px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #ffcccc; letter-spacing: 0.5px;">Phần B (Bảo hành)</div>
                    <div style="font-size: 22px; font-weight: bold; margin-top: 4px;">{{ $surveyResponse->score_b !== null ? number_format($surveyResponse->score_b, 1) : '--' }} / 5.0</div>
                  </td>
                  <td width="2%">&nbsp;</td>
                  <td width="32%" style="background-color: #16A34A; color: #ffffff; text-align: center; padding: 15px; border-radius: 4px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #ccffdd; letter-spacing: 0.5px;">Điểm NPS</div>
                    <div style="font-size: 22px; font-weight: bold; margin-top: 4px;">{{ $surveyResponse->nps_score ?? '--' }} / 10</div>
                  </td>
                </tr>
              </table>

              @if(!empty($surveyResponse->open_feedback_best))
                <div style="margin-bottom: 15px;">
                  <strong style="font-size: 13px; color: #166534;">✓ Điều làm tốt nhất:</strong>
                  <div style="font-size: 14px; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px 14px; margin-top: 5px; border-radius: 4px;">
                    {{ $surveyResponse->open_feedback_best }}
                  </div>
                </div>
              @endif

              @if(!empty($surveyResponse->open_feedback_improve))
                <div style="margin-bottom: 20px;">
                  <strong style="font-size: 13px; color: #991b1b;">⚠️ Điều cần cải thiện:</strong>
                  <div style="font-size: 14px; background-color: #fef2f2; border: 1px solid #fecaca; padding: 10px 14px; margin-top: 5px; border-radius: 4px;">
                    {{ $surveyResponse->open_feedback_improve }}
                  </div>
                </div>
              @endif

              <!-- Admin Link Button -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 25px;">
                <tr>
                  <td align="center">
                    <a href="{{ route('admin.surveys.responses.show', $surveyResponse->id) }}"
                       style="display: inline-block; background-color: #111111; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: bold; padding: 12px 28px; border-radius: 4px;">
                      XEM CHI TIẾT TRONG ADMIN PORTAL &rarr;
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>

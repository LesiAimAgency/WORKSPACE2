<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hóa đơn #{{ $order->order_number }}</title>
  <style>
    @page {
      size: A5;
      margin: 10mm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      line-height: 1.4;
      margin: 0;
      padding: 0;
      font-size: 13px;
    }
    .invoice-box {
      width: 100%;
      max-width: 148mm; /* A5 width */
      margin: auto;
      background: #fff;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }
    .header .logo {
      max-width: 120px;
    }
    .header .company-info {
      text-align: right;
      font-size: 12px;
    }
    .invoice-title {
      text-align: center;
      margin-bottom: 15px;
    }
    .invoice-title h1 {
      font-size: 20px;
      margin: 0 0 5px 0;
      text-transform: uppercase;
    }
    .invoice-title p {
      margin: 0;
      font-size: 12px;
      font-style: italic;
    }
    .info-section {
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
    }
    .info-section div {
      width: 48%;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: left;
    }
    table.data-table th {
      background-color: #f2f2f2;
      font-weight: bold;
      text-align: center;
    }
    table.data-table td.text-right {
      text-align: right;
    }
    table.data-table td.text-center {
      text-align: center;
    }
    .footer {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      text-align: center;
      padding-bottom: 50px;
    }
    .footer .signature {
      width: 45%;
    }
    .header-controls {
      text-align: center;
      margin-bottom: 20px;
    }
    .btn-print {
      padding: 8px 15px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    @media print {
      .header-controls {
        display: none;
      }
      body {
        padding: 0;
      }
    }
  </style>
</head>
<body>
  <div class="header-controls">
    <button class="btn-print" onclick="window.print()">️ In Hóa Đơn (A5)</button>
  </div>

  <div class="invoice-box">
    <div class="header">
      <div class="logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width: 100%; height: auto;" onerror="this.style.display='none'">
      </div>
      <div class="company-info">
        <strong>VGT Demo Company</strong><br>
        Địa chỉ: 123 Đường ABC, Quận XYZ, TP. HCM<br>
        Điện thoại: 0123 456 789
      </div>
    </div>

    <div class="invoice-title">
      <h1>HÓA ĐƠN BÁN HÀNG</h1>
      <p>Mã HĐ: {{ $order->order_number }} - Ngày: {{ $order->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="info-section">
      <div>
        <strong>Khách hàng:</strong> {{ $order->customer_name }}<br>
        <strong>Điện thoại:</strong> {{ $order->customer_phone ?? '---' }}<br>
        <strong>Email:</strong> {{ $order->customer_email ?? '---' }}
      </div>
      <div>
        <strong>Giao tới:</strong><br>
        {{ $order->shipping_address['address'] ?? '' }}
        @if(!empty($order->shipping_address['city']) || !empty($order->shipping_address['state']))
          <br>{{ $order->shipping_address['city'] ?? '' }}@if(!empty($order->shipping_address['city']) && !empty($order->shipping_address['state'])), @endif{{ $order->shipping_address['state'] ?? '' }}
        @endif
        @if(!empty($order->shipping_address['postal_code']) || !empty($order->shipping_address['country']))
          <br>{{ $order->shipping_address['postal_code'] ?? '' }} @if(!empty($order->shipping_address['postal_code']) && !empty($order->shipping_address['country']))- @endif{{ $order->shipping_address['country'] ?? '' }}
        @endif
      </div>
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%">STT</th>
          <th style="width: 40%">Sản phẩm</th>
          <th style="width: 10%">SL</th>
          <th style="width: 20%">Đơn giá</th>
          <th style="width: 25%">Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        @forelse($order->items as $index => $item)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td>{{ $item->product_name }}</td>
          <td class="text-center">{{ $item->quantity }}</td>
          <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
          <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center">Không có chi tiết sản phẩm</td>
        </tr>
        @endforelse
        <tr>
          <td colspan="4" class="text-right"><strong>Tạm tính:</strong></td>
          <td class="text-right">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
        </tr>
        @if($order->shipping_amount > 0)
        <tr>
          <td colspan="4" class="text-right"><strong>Vận chuyển:</strong></td>
          <td class="text-right">{{ number_format($order->shipping_amount, 0, ',', '.') }}đ</td>
        </tr>
        @endif
        @if($order->discount_amount > 0)
        <tr>
          <td colspan="4" class="text-right"><strong>Giảm giá:</strong></td>
          <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
        </tr>
        @endif
        <tr>
          <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
          <td class="text-right"><strong>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</strong></td>
        </tr>
      </tbody>
    </table>

    <div style="margin-bottom: 10px;">
      <strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method) }} 
      (@if($order->payment_status == 'paid') Đã thanh toán @else Chưa thanh toán @endif)<br>
      <strong>Ghi chú:</strong> {{ $order->notes ?? 'Không có' }}
    </div>

    <div class="footer">
      <div class="signature">
        <strong>Người mua hàng</strong><br>
        <em>(Ký, ghi rõ họ tên)</em>
      </div>
      <div class="signature">
        <strong>Người bán hàng</strong><br>
        <em>(Ký, ghi rõ họ tên)</em>
      </div>
    </div>
  </div>
</body>
</html>

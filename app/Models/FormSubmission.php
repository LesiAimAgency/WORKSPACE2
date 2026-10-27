<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'tenant_id',
        'form_name',
        'form_template_id',
        'modal_form_id',
        'data',
        'ip_address',
        'user_agent',
        'status',
        'admin_note',
        'source', // 'modal', 'widget', 'page', 'contact'
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function getDataAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value ?? '[]', true) ?: [];
    }

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class);
    }

    public function modalForm()
    {
        return $this->belongsTo(ModalForm::class);
    }

    public static function fieldLabels(): array
    {
        return [
            'fullname' => 'Họ và tên',
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'email' => 'Địa chỉ Email',
            'service' => 'Dịch vụ quan tâm',
            'subject' => 'Tiêu đề / Dịch vụ',
            'message' => 'Nội dung ghi chú',
            'note' => 'Ghi chú',
            'page_url' => 'Trang gửi (URL)',
            'product_title' => 'Tên sản phẩm',
            'product_sku' => 'Mã sản phẩm (SKU)',
            'product_price' => 'Đơn giá tham khảo',
            'product_quantity' => 'Số lượng yêu cầu',
            'product_variation' => 'Biến thể / Thuộc tính',
            'company' => 'Công ty / Đơn vị',
            'company_name' => 'Công ty / Đơn vị',
            'address' => 'Địa chỉ',
            'source' => 'Nguồn gửi',
            'privacy_consent' => 'Đồng ý chính sách',
            'newsletter' => 'Đăng ký nhận tin',
            'form_name' => 'Biểu mẫu',
        ];
    }

    public static function serviceLabels(): array
    {
        return [
            'bao-gia-san-pham' => 'Yêu cầu báo giá sản phẩm',
            'vat-tu-thiet-bi-dien' => 'Vật tư thiết bị điện',
            'cot-be-tong-ly-tam' => 'Cột bê tông ly tâm',
            'thi-cong-xay-lap' => 'Thi công xây lắp điện',
            'thiet-bi-nang-luong-mat-troi' => 'Năng lượng mặt trời',
            'tu-van-thiet-ke' => 'Tư vấn thiết kế',
        ];
    }

    public function getFieldLabel(string $key): string
    {
        $normalizedKey = strtolower(trim($key));
        $labels = static::fieldLabels();

        return $labels[$normalizedKey] ?? ucfirst(str_replace('_', ' ', $key));
    }

    public function getFormattedFieldValue(string $key, mixed $value): mixed
    {
        $normalizedKey = strtolower(trim($key));

        if ($normalizedKey === 'service' && is_string($value)) {
            $services = static::serviceLabels();
            return $services[$value] ?? ucfirst(str_replace(['-', '_'], ' ', $value));
        }

        if (is_bool($value)) {
            return $value ? 'Có (Đã đồng ý)' : 'Không';
        }

        if ($normalizedKey === 'newsletter' || $normalizedKey === 'privacy_consent') {
            if ($value === '1' || $value === 1 || $value === true || $value === 'on') {
                return 'Có (Đã đồng ý)';
            }
            if ($value === '0' || $value === 0 || $value === false || empty($value)) {
                return 'Không';
            }
        }

        return $value;
    }
}

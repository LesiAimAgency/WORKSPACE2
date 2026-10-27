<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMyTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'gold' => ['nullable', 'integer', 'min:0'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'deadline' => [
                'required',
                'date',
                Rule::when($this->filled('start_date'), ['after_or_equal:start_date']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên công việc.',
            'title.max' => 'Tên công việc không được vượt quá 255 ký tự.',
            'project_id.required' => 'Vui lòng chọn dự án.',
            'project_id.exists' => 'Dự án không tồn tại.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'deadline.required' => 'Vui lòng chọn deadline.',
            'deadline.date' => 'Deadline không hợp lệ.',
            'deadline.after_or_equal' => 'Deadline phải từ ngày bắt đầu trở đi.',
        ];
    }
}

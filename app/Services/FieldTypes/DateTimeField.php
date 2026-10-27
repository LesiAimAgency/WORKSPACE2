<?php

namespace App\Services\FieldTypes;

class DateTimeField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'datetime-local';
        $attributes['class'] = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 shadow-sm outline-none transition';

        $rawVal = $value ?? $config['default'] ?? '';
        if (! empty($rawVal)) {
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', (string) $rawVal)) {
                    $attributes['value'] = substr((string) $rawVal, 0, 16);
                } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{2}):(\d{2})/', (string) $rawVal, $m)) {
                    $year = $m[3];
                    $month = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                    $hour = $m[4];
                    $min = $m[5];
                    $attributes['value'] = "{$year}-{$month}-{$day}T{$hour}:{$min}";
                } else {
                    $dt = new \DateTime((string) $rawVal);
                    $attributes['value'] = $dt->format('Y-m-d\TH:i');
                }
            } catch (\Throwable $e) {
                $attributes['value'] = (string) $rawVal;
            }
        } else {
            $attributes['value'] = '';
        }

        if (isset($config['min'])) {
            $attributes['min'] = $config['min'];
        }

        if (isset($config['max'])) {
            $attributes['max'] = $config['max'];
        }

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        return true;
    }

    public static function getTypeName(): string
    {
        return 'datetime';
    }
}

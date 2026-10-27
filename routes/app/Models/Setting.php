<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'payload', 'value', 'type', 'group', 'locked', 'project_id', 'tenant_id', 'description'];

    protected $casts = [
        'payload' => 'array',
        'locked' => 'boolean',
    ];

    public static function set($key, $value, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key, 'project_id' => null],
            ['payload' => is_array($value) ? $value : ['value' => $value], 'group' => $group]
        );
    }
}

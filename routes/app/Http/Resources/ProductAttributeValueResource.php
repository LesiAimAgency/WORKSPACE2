<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'slug' => $this->slug,
            'display_value' => $this->display_value,
            'color_code' => $this->color_code,
            'sort_order' => $this->sort_order,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'is_filterable' => $this->is_filterable,
            'is_required' => $this->is_required,
            'group_id' => $this->attribute_group_id,
            'values' => ProductAttributeValueResource::collection($this->values),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class TaxonomyFieldController extends Controller
{
    /**
     * List taxonomies for taxonomy field
     */
    public function list(Request $request)
    {
        $type = $request->get('type', 'category');

        $items = match ($type) {
            'category' => $this->getCategories(),
            'brand' => $this->getBrands(),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    protected function getCategories(): array
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return $categories->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'count' => $c->products_count,
            'parent_id' => $c->parent_id,
        ])->toArray();
    }

    protected function getBrands(): array
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return $brands->map(fn ($b) => [
            'id' => $b->id,
            'name' => $b->name,
            'slug' => $b->slug,
            'count' => $b->products_count,
        ])->toArray();
    }
}

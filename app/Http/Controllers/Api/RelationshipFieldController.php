<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class RelationshipFieldController extends Controller
{
    /**
     * Search items for relationship field
     */
    public function search(Request $request)
    {
        $type = $request->get('type', 'product');
        $query = (string) $request->get('q', '');
        $limit = $request->get('limit', 20);
        $tenantId = $this->getTenantId();

        $items = match ($type) {
            'product' => $this->searchProducts($query, $limit, $tenantId),
            'post' => $this->searchPosts($query, $limit, 'post', $tenantId),
            'page' => $this->searchPosts($query, $limit, 'page', $tenantId),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    /**
     * Get items by IDs
     */
    public function getItems(Request $request)
    {
        $type = $request->get('type', 'product');
        $ids = array_filter(explode(',', $request->get('ids', '')));
        $tenantId = $this->getTenantId();

        if (empty($ids)) {
            return response()->json(['items' => []]);
        }

        $items = match ($type) {
            'product' => $this->getProductsByIds($ids, $tenantId),
            'post' => $this->getPostsByIds($ids, 'post', $tenantId),
            'page' => $this->getPostsByIds($ids, 'page', $tenantId),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    /**
     * Get tenant ID from session, request attributes or container
     */
    protected function getTenantId(): ?int
    {
        // 1. From request attributes (Set by ProjectSubdomainMiddleware)
        if ($project = request()->attributes->get('project')) {
            return $project->id ?? null;
        }

        // 2. From app container binding
        if (app()->bound('current_project_id')) {
            return app('current_project_id');
        }
        if (app()->bound('current_tenant_id')) {
            return app('current_tenant_id');
        }

        // 3. From session
        $currentProject = session('current_project');
        if (\is_array($currentProject)) {
            return $currentProject['id'] ?? null;
        }
        if (\is_object($currentProject)) {
            return $currentProject->id ?? null;
        }

        return session('current_project_id') ?? session('current_tenant_id');
    }

    protected function searchProducts(?string $query, int $limit, ?int $tenantId): array
    {
        $products = Product::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($query, fn ($q) => $q->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            }))
            ->where('status', 'published')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return $products->map(fn ($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sale_price' => $p->sale_price,
            'sku' => $p->sku,
            'stock' => $p->stock_quantity ?? 0,
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function searchPosts(?string $query, int $limit, string $postType, ?int $tenantId): array
    {
        $posts = Post::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($query, fn ($q) => $q->where('title', 'like', "%{$query}%"))
            ->where('post_type', $postType)
            ->where('status', 'published')
            ->orderBy('title')
            ->limit($limit)
            ->get();

        return $posts->map(fn ($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->featured_image ?? ($p->thumbnail ?? null),
            'excerpt' => \Str::limit(strip_tags($p->excerpt ?? $p->content ?? ''), 80),
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function getProductsByIds(array $ids, ?int $tenantId): array
    {
        $products = Product::query()
            ->whereIn('id', $ids)
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return $products->map(fn ($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sale_price' => $p->sale_price,
            'sku' => $p->sku,
            'stock' => $p->stock_quantity ?? 0,
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function getPostsByIds(array $ids, string $postType, ?int $tenantId): array
    {
        $posts = Post::query()
            ->whereIn('id', $ids)
            ->where('post_type', $postType)
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return $posts->map(fn ($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->featured_image ?? ($p->thumbnail ?? null),
            'excerpt' => \Str::limit(strip_tags($p->excerpt ?? $p->content ?? ''), 80),
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }
}

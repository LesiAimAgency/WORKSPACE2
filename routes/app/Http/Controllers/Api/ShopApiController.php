<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ShopApiController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $limit = min((int) ($request->get('limit', 12)), 50);
        $query = Product::query();

        if (Schema::hasColumn('products', 'status')) {
            $query->whereIn('status', ['published', 'active', 1]);
        }

        if ($request->filled('category')) {
            $catSlug = $request->get('category');
            $query->whereHas('categories', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        if ($request->filled('q')) {
            $search = trim($request->get('q'));
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('is_featured')) {
            if (Schema::hasColumn('products', 'is_featured')) {
                $query->where('is_featured', 1);
            }
        }

        if ($request->filled('on_sale')) {
            if (Schema::hasColumn('products', 'sale_price')) {
                $query->whereNotNull('sale_price');
            }
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function product(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::query();
        if (Schema::hasColumn('categories', 'is_active')) {
            $categories->where('is_active', true);
        }
        if (Schema::hasColumn('categories', 'parent_id')) {
            $categories->whereNull('parent_id')->with('children');
        }

        return response()->json([
            'success' => true,
            'data' => $categories->get(),
        ]);
    }

    public function cart(Request $request): JsonResponse
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => array_values($cart),
                'total_items' => count($cart),
                'total_amount' => $total,
            ],
        ]);
    }

    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = (int) ($request->quantity ?? 1);

        $product = Product::find($productId);
        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.'], 404);
        }

        $cart = session()->get('cart', []);
        $cartKey = (string) $productId;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price ?? 0,
                'image' => $product->featured_image ?? $product->image ?? '',
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'data' => [
                'cart_count' => count($cart),
                'items' => array_values($cart),
            ],
        ]);
    }

    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $cartKey = (string) $request->product_id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng!',
            'data' => array_values($cart),
        ]);
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        $cartKey = (string) $request->product_id;

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'data' => array_values($cart),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng đang trống.'], 422);
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        $orderNumber = 'ORD-'.strtoupper(Str::random(8));

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email ?? '',
                'shipping_address' => json_encode(['address' => $request->shipping_address]),
                'billing_address' => json_encode(['address' => $request->shipping_address]),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => 'pending',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cart as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'] ?? '',
                    'product_sku' => 'SKU-'.$item['product_id'],
                    'unit_price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                    'total_amount' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            session()->forget('cart');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'total_amount' => $subtotal,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đơn hàng: '.$e->getMessage(),
            ], 500);
        }
    }
}

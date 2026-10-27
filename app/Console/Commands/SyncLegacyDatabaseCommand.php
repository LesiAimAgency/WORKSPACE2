<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SyncLegacyDatabaseCommand extends Command
{
    protected $signature = 'migrate:legacy-vtm 
                            {project_id? : The project ID to import into (default: auto-detect VTM project / 10)} 
                            {tenant_id? : The tenant ID to import into (default: auto-detect VTM tenant / 3)}';

    protected $description = 'Sync legacy VTM database to the new VGTDemo core multi-tenant schema';

    public function handle()
    {
        $projectId = $this->argument('project_id');
        $tenantId = $this->argument('tenant_id');

        if (! $projectId) {
            $project = Project::where('code', 'viettinmart-eco')
                ->orWhere('code', 'viettinmart')
                ->orWhere('name', 'like', '%Viettinmart%')
                ->orWhere('id', 10)
                ->first();
            $projectId = $project ? $project->id : 10;
        } else {
            $project = Project::find($projectId);
        }

        if (isset($project) && $project) {
            $updated = false;
            if (empty($project->client_name)) {
                $project->client_name = 'VietTin Mart';
                $updated = true;
            }
            if (empty($project->department_id)) {
                $project->department_id = 2; // Phòng IT
                $updated = true;
            }
            if (empty($project->admin_id)) {
                $rootAdmin = User::where('email', 'admin@example.com')->first();
                $project->admin_id = $rootAdmin ? $rootAdmin->id : 41;
                $updated = true;
            }
            if (empty($project->created_by)) {
                $rootAdmin = User::where('email', 'admin@example.com')->first();
                $project->created_by = $rootAdmin ? $rootAdmin->id : 41;
                $updated = true;
            }
            if ($updated) {
                $project->save();
                $this->info("Updated Project [{$project->code}] metadata (client_name, department_id, admin_id).");
            }
        }

        if (! $tenantId) {
            if (isset($project) && $project && $project->tenant_id) {
                $tenantId = $project->tenant_id;
            } else {
                $tenant = Tenant::where('name', 'like', '%Viettinmart%')
                    ->orWhere('id', 3)
                    ->first();
                $tenantId = $tenant ? $tenant->id : 3;
            }
        }

        $this->info("Starting migration for Project ID: {$projectId}, Tenant ID: {$tenantId}");

        // Dynamically add 'vtm' connection
        Config::set('database.connections.vtm', array_merge(
            config('database.connections.mysql'),
            ['database' => 'vtm']
        ));

        try {
            DB::connection('vtm')->getPdo();
            $this->info("Successfully connected to legacy 'vtm' database.");
        } catch (\Exception $e) {
            $this->error('Could not connect to the legacy database. Error: '.$e->getMessage());

            return Command::FAILURE;
        }

        // 1. Sync Users
        $this->syncUsers($projectId, $tenantId);

        // 2. Sync Products
        $this->syncProducts($projectId, $tenantId);

        // 3. Sync Settings
        $this->syncSettings($projectId, $tenantId);

        // 4. Sync Categories
        $this->syncCategories($projectId, $tenantId);

        // 5. Sync Posts
        $this->syncPosts($projectId, $tenantId);

        // 6. Sync Orders
        $this->syncOrders($projectId, $tenantId);

        // 7. Sync Order Items
        $this->syncOrderItems($projectId, $tenantId);

        // 8. Sync Widgets
        $this->syncWidgets($projectId, $tenantId);

        // 9. Sync Coupons
        $this->syncCoupons($projectId, $tenantId);

        // 10. Sync Reviews
        $this->syncReviews($projectId, $tenantId);

        // 11. Sync Agents
        $this->syncAgents($projectId, $tenantId);

        // 12. Sync User Addresses
        $this->syncUserAddresses($projectId, $tenantId);

        // 13. Sync Form Templates
        $this->syncFormTemplates($projectId, $tenantId);

        // 14. Sync Modal Forms
        $this->syncModalForms($projectId, $tenantId);

        // 15. Sync Flash Sales
        $this->syncFlashSales($projectId, $tenantId);

        // 16. Sync Pages
        $this->syncPages($projectId, $tenantId);

        // 17. Sync Category Product Relationships
        $this->syncCategoryProduct($projectId, $tenantId);

        // 18. Sync Translations
        $this->syncTranslations($projectId, $tenantId);

        $this->info('Migration completed successfully!');

        return Command::SUCCESS;
    }

    private function syncUsers($projectId, $tenantId)
    {
        $this->info('Syncing Users...');
        $legacyUsers = DB::connection('vtm')->table('users')->get();
        $count = 0;

        foreach ($legacyUsers as $lUser) {
            $user = DB::table('users')->where('email', $lUser->email)->first();

            if (! $user) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $lUser->name,
                    'email' => $lUser->email,
                    'password' => $lUser->password,
                    'phone' => $lUser->phone ?? null,
                    'avatar' => $lUser->avatar ?? null,
                    'email_verified_at' => $lUser->email_verified_at ?? null,
                    'status' => isset($lUser->status) ? (($lUser->status === 'active' || $lUser->status === 1) ? 1 : 0) : 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $userId = $user->id;
                DB::table('users')->where('id', $userId)->update([
                    'name' => $lUser->name,
                    'password' => $lUser->password,
                    'phone' => $lUser->phone ?? null,
                    'avatar' => $lUser->avatar ?? null,
                    'email_verified_at' => $lUser->email_verified_at ?? null,
                    'status' => isset($lUser->status) ? (($lUser->status === 'active' || $lUser->status === 1) ? 1 : 0) : 1,
                    'updated_at' => now(),
                ]);
            }
            $userModel = User::find($userId);
            if ($userModel) {
                $userModel->assignToProject($projectId);
            }
            $count++;
        }
        $this->info("Synced {$count} users.");
    }

    private function syncProducts($projectId, $tenantId)
    {
        $this->info('Syncing Products...');
        $legacyProducts = DB::connection('vtm')->table('products')->get();
        $count = 0;

        foreach ($legacyProducts as $lProd) {
            $slug = $lProd->slug ?? Str::slug($lProd->name);
            // Check if product exists with this slug
            Product::updateOrCreate(
                [
                    'slug' => $slug,
                ],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $tenantId,
                    'name' => $lProd->name,
                    'short_description' => $lProd->short_description ?? null,
                    'description' => $lProd->description ?? null,
                    'sku' => ! empty($lProd->sku) ? $lProd->sku : 'SKU-'.$lProd->id.'-'.uniqid(),
                    'price' => (! empty($lProd->compare_price) && $lProd->compare_price > 0) ? $lProd->compare_price : ($lProd->price ?? 0),
                    'sale_price' => (! empty($lProd->compare_price) && $lProd->compare_price > 0) ? $lProd->price : null,
                    'has_price' => 1,
                    'stock_quantity' => $lProd->stock ?? 0,
                    'manage_stock' => 1,
                    'stock_status' => $lProd->stock_status ?? 'in_stock',
                    'featured_image' => $lProd->image ?? (isset($lProd->featured_image) ? $lProd->featured_image : null),
                    'gallery' => isset($lProd->images) ? $lProd->images : (isset($lProd->gallery) ? $lProd->gallery : $this->fetchProductImages($lProd->id)),
                    'status' => ($lProd->status === 'active' || $lProd->status === 'published' || $lProd->status === 1) ? 'published' : 'draft',
                    'is_featured' => $lProd->is_featured ?? 0,
                    'created_at' => $lProd->created_at ?? now(),
                    'updated_at' => $lProd->updated_at ?? now(),
                ]
            );
            $count++;
        }
        $this->info("Synced {$count} products.");
    }

    private function fetchProductImages($productId)
    {
        try {
            if (DB::connection('vtm')->getSchemaBuilder()->hasTable('product_images')) {
                $images = DB::connection('vtm')->table('product_images')->where('product_id', $productId)->pluck('image')->toArray();
                if (! empty($images)) {
                    return json_encode($images);
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return null;
    }

    private function syncSettings($projectId, $tenantId)
    {

        $this->info('Syncing Settings...');
        $legacySettings = DB::connection('vtm')->table('settings')->get();
        $count = 0;

        foreach ($legacySettings as $lSet) {
            DB::table('project_settings')->updateOrInsert(
                [
                    'project_id' => $projectId,
                    'key' => $lSet->key,
                ],
                [
                    'value' => $lSet->value,
                ]
            );
            $count++;
        }
        $this->info("Synced {$count} settings.");
    }

    private function syncCategories($projectId, $tenantId)
    {
        $this->info('Syncing Categories...');
        $legacyCategories = DB::connection('vtm')->table('categories')->get();
        $count = 0;

        foreach ($legacyCategories as $lCat) {
            $taxonomyType = $lCat->type ?? 'category';
            if ($taxonomyType == 'product') {
                $taxonomyType = 'product_category';
            } elseif ($taxonomyType == 'post') {
                $taxonomyType = 'category';
            }

            DB::table('taxonomies')->updateOrInsert(
                [
                    'project_id' => $projectId,
                    'slug' => $lCat->slug ?? Str::slug($lCat->name),
                ],
                [
                    'tenant_id' => $tenantId,
                    'name' => $lCat->name,
                    'taxonomy' => $taxonomyType,
                    'description' => $lCat->description ?? null,
                    'parent_id' => $lCat->parent_id ?? null,
                    'status' => $lCat->is_active ? 'active' : 'inactive',
                    'created_at' => $lCat->created_at ?? now(),
                    'updated_at' => $lCat->updated_at ?? now(),
                ]
            );

            if (Schema::hasTable('product_categories')) {
                DB::table('product_categories')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'slug' => $lCat->slug ?? Str::slug($lCat->name),
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'name' => $lCat->name,
                        'description' => $lCat->description ?? null,
                        'image' => $lCat->image ?? (isset($lCat->thumbnail) ? $lCat->thumbnail : (isset($lCat->icon) ? $lCat->icon : null)),
                        'parent_id' => $lCat->parent_id ?? null,
                        'is_active' => $lCat->is_active ?? true,
                        'meta_title' => $lCat->meta_title ?? null,
                        'meta_description' => $lCat->meta_description ?? null,
                        'created_at' => $lCat->created_at ?? now(),
                        'updated_at' => $lCat->updated_at ?? now(),
                    ]
                );
            }
            $count++;
        }
        $this->info("Synced {$count} categories.");
    }

    private function syncPosts($projectId, $tenantId)
    {
        $this->info('Syncing Posts...');
        $legacyPosts = DB::connection('vtm')->table('posts')->get();
        $count = 0;

        foreach ($legacyPosts as $lPost) {
            DB::table('posts')->updateOrInsert(
                [
                    'slug' => $lPost->slug ?? Str::slug($lPost->title),
                ],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $tenantId,
                    'title' => $lPost->title,
                    'excerpt' => $lPost->excerpt ?? null,
                    'content' => $lPost->content ?? null,
                    'featured_image' => $lPost->thumbnail ?? null,
                    'post_type' => 'post',
                    'status' => $lPost->status ?? 'draft',
                    'meta_title' => $lPost->meta_title ?? null,
                    'meta_description' => $lPost->meta_description ?? null,
                    'published_at' => $lPost->published_at ?? null,
                    'created_at' => $lPost->created_at ?? now(),
                    'updated_at' => $lPost->updated_at ?? now(),
                ]
            );
            $count++;
        }
        $this->info("Synced {$count} posts.");
    }

    private function syncOrders($projectId, $tenantId)
    {
        $this->info('Syncing Orders...');
        $legacyOrders = DB::connection('vtm')->table('orders')->get();
        $count = 0;

        foreach ($legacyOrders as $lOrder) {
            $orderStatus = $lOrder->status ?? 'pending';
            if ($orderStatus === 'completed') {
                $orderStatus = 'delivered';
            } elseif ($orderStatus === 'confirmed') {
                $orderStatus = 'processing';
            }

            $paymentStatus = $lOrder->payment_status ?? 'pending';
            if ($paymentStatus === 'unpaid') {
                $paymentStatus = 'pending';
            }

            DB::table('orders')->updateOrInsert(
                [
                    'order_number' => $lOrder->order_number,
                ],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $tenantId,
                    'status' => $orderStatus,
                    'subtotal' => $lOrder->subtotal ?? 0,
                    'shipping_amount' => $lOrder->shipping_fee ?? 0,
                    'discount_amount' => $lOrder->discount ?? 0,
                    'total_amount' => $lOrder->total ?? 0,
                    'customer_name' => $lOrder->customer_name ?? null,
                    'customer_email' => $lOrder->customer_email ?? null,
                    'customer_phone' => $lOrder->customer_phone ?? null,
                    'shipping_address' => $lOrder->shipping_address ? json_encode(['address' => $lOrder->shipping_address]) : null,
                    'payment_method' => $lOrder->payment_method ?? null,
                    'payment_status' => $paymentStatus,
                    'customer_notes' => $lOrder->customer_note ?? null,
                    'internal_notes' => $lOrder->admin_note ?? null,
                    'created_at' => $lOrder->created_at ?? now(),
                    'updated_at' => $lOrder->updated_at ?? now(),
                ]
            );
            $count++;
        }
        $this->info("Synced {$count} orders.");
    }

    private function syncOrderItems($projectId, $tenantId)
    {
        $this->info('Syncing Order Items...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('order_items')) {
                $this->warn('Legacy order_items table does not exist.');

                return;
            }

            $legacyItems = DB::connection('vtm')->table('order_items')->get();
            $count = 0;

            foreach ($legacyItems as $item) {
                $legacyOrder = DB::connection('vtm')->table('orders')->where('id', $item->order_id)->first();
                if (! $legacyOrder) {
                    continue;
                }

                $coreOrder = DB::table('orders')->where('order_number', $legacyOrder->order_number)->first();
                if (! $coreOrder) {
                    continue;
                }

                $coreProduct = DB::table('products_enhanced')->where('project_id', $projectId)->where('name', $item->product_name ?? '')->first();
                $productId = $coreProduct ? $coreProduct->id : $item->product_id;

                DB::table('order_items')->updateOrInsert(
                    [
                        'order_id' => $coreOrder->id,
                        'product_name' => $item->product_name ?? 'Product #'.$item->product_id,
                    ],
                    [
                        'project_id' => $projectId,
                        'tenant_id' => $tenantId,
                        'product_id' => $productId,
                        'product_sku' => $item->product_sku ?? 'SKU-'.$item->product_id,
                        'unit_price' => $item->price ?? 0,
                        'quantity' => $item->quantity ?? 1,
                        'total_price' => $item->subtotal ?? (($item->price ?? 0) * ($item->quantity ?? 1)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} order items.");
        } catch (\Exception $e) {
            $this->warn('Error syncing order items: '.$e->getMessage());
        }
    }

    private function syncWidgets($projectId, $tenantId)
    {
        $this->info('Syncing Widgets...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('widgets')) {
                $this->warn('Legacy widgets table does not exist.');

                return;
            }

            $legacyWidgets = DB::connection('vtm')->table('widgets')->get();
            $count = 0;

            $typeMap = [
                'hero_main' => 'vtm_hero_slider',
                'hero_slider' => 'vtm_hero_slider',
                'feature_icons' => 'vtm_feature_icons',
                'prod_featured' => 'vtm_product_featured',
                'deal_flash' => 'vtm_deal_flash',
                'prod_tabs' => 'vtm_product_tabs',
                'promo_banners' => 'vtm_promo_banners',
                'top_trending' => 'vtm_top_trending',
                'posts_latest' => 'vtm_posts_latest',
                'form_widget' => 'vtm_form_widget',
            ];

            foreach ($legacyWidgets as $lWidget) {
                $mappedType = $typeMap[$lWidget->type] ?? ('vtm_'.$lWidget->type);

                DB::table('widgets')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'name' => $lWidget->name,
                        'type' => $mappedType,
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'area' => $lWidget->area ?? 'homepage',
                        'settings' => $lWidget->config ?? null,
                        'sort_order' => $lWidget->sort_order ?? 0,
                        'is_active' => $lWidget->is_active ? 1 : 0,
                        'created_at' => $lWidget->created_at ?? now(),
                        'updated_at' => $lWidget->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} widgets.");
        } catch (\Exception $e) {
            $this->warn('Error syncing widgets: '.$e->getMessage());
        }
    }

    private function syncCoupons($projectId, $tenantId)
    {
        $this->info('Syncing Coupons...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('coupons')) {
                $this->warn('Legacy coupons table does not exist.');

                return;
            }

            if (! Schema::hasTable('coupons')) {
                $this->warn('Core coupons table does not exist.');

                return;
            }

            $legacyCoupons = DB::connection('vtm')->table('coupons')->get();
            $count = 0;

            foreach ($legacyCoupons as $coupon) {
                DB::table('coupons')->updateOrInsert(
                    ['code' => $coupon->code],
                    [
                        'project_id' => $projectId,
                        'tenant_id' => $tenantId,
                        'name' => $coupon->name ?? $coupon->code,
                        'type' => $coupon->type ?? 'fixed',
                        'value' => $coupon->value ?? 0,
                        'min_order_value' => $coupon->min_order_value ?? 0,
                        'max_discount_value' => $coupon->max_discount_value ?? null,
                        'start_date' => $coupon->start_date ?? null,
                        'end_date' => $coupon->end_date ?? null,
                        'usage_limit' => $coupon->usage_limit ?? null,
                        'usage_limit_per_user' => $coupon->usage_limit_per_user ?? 1,
                        'usage_count' => $coupon->usage_count ?? 0,
                        'is_active' => $coupon->is_active ?? 1,
                        'created_at' => $coupon->created_at ?? now(),
                        'updated_at' => $coupon->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} coupons.");
        } catch (\Exception $e) {
            $this->warn('Error syncing coupons: '.$e->getMessage());
        }
    }

    private function syncReviews($projectId, $tenantId)
    {
        $this->info('Syncing Reviews...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('reviews')) {
                $this->warn('Legacy reviews table does not exist.');

                return;
            }

            $targetTable = Schema::hasTable('product_reviews') ? 'product_reviews' : 'reviews';
            if (! Schema::hasTable($targetTable)) {
                $this->warn('Core reviews table does not exist.');

                return;
            }

            $legacyReviews = DB::connection('vtm')->table('reviews')->get();
            $count = 0;

            foreach ($legacyReviews as $rev) {
                $lProd = DB::connection('vtm')->table('products')->where('id', $rev->product_id)->first();
                $coreProdId = null;
                if ($lProd) {
                    $slug = $lProd->slug ?? Str::slug($lProd->name);
                    $coreProd = DB::table('products_enhanced')->where('project_id', $projectId)->where('slug', $slug)->first();
                    $coreProdId = $coreProd ? $coreProd->id : null;
                }

                if (! $coreProdId) {
                    continue;
                }

                DB::table('product_reviews')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'product_id' => $coreProdId,
                        'reviewer_name' => $rev->name ?? ($rev->author_name ?? 'Khách'),
                        'comment' => $rev->comment ?? ($rev->content ?? ''),
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'reviewer_email' => ! empty($rev->email) ? $rev->email : ('khach_'.$rev->id.'@viettinmart.com'),
                        'rating' => $rev->rating ?? 5,
                        'status' => $rev->status ?? 'approved',
                        'is_verified' => 1,
                        'created_at' => $rev->created_at ?? now(),
                        'updated_at' => $rev->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} reviews.");
        } catch (\Exception $e) {
            $this->warn('Error syncing reviews: '.$e->getMessage());
        }
    }

    private function syncAgents($projectId, $tenantId)
    {
        $this->info('Syncing Agents...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('agents')) {
                $this->warn('Legacy agents table does not exist.');

                return;
            }

            $legacyAgents = DB::connection('vtm')->table('agents')->get();
            $count = 0;

            foreach ($legacyAgents as $lAgent) {
                $coreUserId = null;
                if ($lAgent->user_id) {
                    $lUser = DB::connection('vtm')->table('users')->where('id', $lAgent->user_id)->first();
                    if ($lUser) {
                        $coreUser = DB::table('users')->where('email', $lUser->email)->first();
                        $coreUserId = $coreUser ? $coreUser->id : null;
                    }
                }

                DB::table('agents')->updateOrInsert(
                    ['code' => $lAgent->code ?? ('AGENT-'.$lAgent->id)],
                    [
                        'project_id' => $projectId,
                        'tenant_id' => $tenantId,
                        'name' => $lAgent->name,
                        'phone' => $lAgent->phone ?? null,
                        'email' => $lAgent->email ?? null,
                        'address' => $lAgent->address ?? null,
                        'contact_person' => $lAgent->contact_person ?? null,
                        'region' => $lAgent->region ?? null,
                        'type' => $lAgent->type ?? 'retailer',
                        'commission_rate' => $lAgent->commission_rate ?? 0,
                        'is_active' => $lAgent->is_active ? 1 : 0,
                        'notes' => $lAgent->notes ?? null,
                        'user_id' => $coreUserId,
                        'created_at' => $lAgent->created_at ?? now(),
                        'updated_at' => $lAgent->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} agents.");
        } catch (\Exception $e) {
            $this->warn('Error syncing agents: '.$e->getMessage());
        }
    }

    private function syncUserAddresses($projectId, $tenantId)
    {
        $this->info('Syncing User Addresses...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('user_addresses')) {
                $this->warn('Legacy user_addresses table does not exist.');

                return;
            }

            $legacyAddresses = DB::connection('vtm')->table('user_addresses')->get();
            $count = 0;

            foreach ($legacyAddresses as $lAddr) {
                $coreUserId = null;
                $lUser = DB::connection('vtm')->table('users')->where('id', $lAddr->user_id)->first();
                if ($lUser) {
                    $coreUser = DB::table('users')->where('email', $lUser->email)->first();
                    $coreUserId = $coreUser ? $coreUser->id : null;
                }

                if (! $coreUserId) {
                    continue;
                }

                DB::table('user_addresses')->updateOrInsert(
                    [
                        'user_id' => $coreUserId,
                        'receiver_name' => $lAddr->receiver_name,
                        'receiver_phone' => $lAddr->receiver_phone,
                    ],
                    [
                        'province_code' => $lAddr->province_code ?? null,
                        'ward_code' => $lAddr->ward_code ?? null,
                        'province_name' => $lAddr->province_name ?? null,
                        'district_name' => $lAddr->district_name ?? null,
                        'ward_name' => $lAddr->ward_name ?? null,
                        'address_detail' => $lAddr->address_detail ?? null,
                        'full_address' => $lAddr->full_address ?? null,
                        'is_default' => $lAddr->is_default ?? false,
                        'created_at' => $lAddr->created_at ?? now(),
                        'updated_at' => $lAddr->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} user addresses.");
        } catch (\Exception $e) {
            $this->warn('Error syncing user addresses: '.$e->getMessage());
        }
    }

    private function syncFormTemplates($projectId, $tenantId)
    {
        $this->info('Syncing Form Templates...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('form_templates')) {
                $this->warn('Legacy form_templates table does not exist.');

                return;
            }

            $legacyTemplates = DB::connection('vtm')->table('form_templates')->get();
            $count = 0;

            foreach ($legacyTemplates as $lTmpl) {
                DB::table('form_templates')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'name' => $lTmpl->name,
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'description' => $lTmpl->description ?? null,
                        'fields' => is_string($lTmpl->fields) ? $lTmpl->fields : json_encode($lTmpl->fields),
                        'is_active' => $lTmpl->is_active ?? 1,
                        'created_at' => $lTmpl->created_at ?? now(),
                        'updated_at' => $lTmpl->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} form templates.");
        } catch (\Exception $e) {
            $this->warn('Error syncing form templates: '.$e->getMessage());
        }
    }

    private function syncModalForms($projectId, $tenantId)
    {
        $this->info('Syncing Modal Forms...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('modal_forms')) {
                $this->warn('Legacy modal_forms table does not exist.');

                return;
            }

            $legacyModals = DB::connection('vtm')->table('modal_forms')->get();
            $count = 0;

            foreach ($legacyModals as $lModal) {
                $coreTmplId = null;
                if ($lModal->form_template_id) {
                    $lTmpl = DB::connection('vtm')->table('form_templates')->where('id', $lModal->form_template_id)->first();
                    if ($lTmpl) {
                        $coreTmpl = DB::table('form_templates')->where('project_id', $projectId)->where('name', $lTmpl->name)->first();
                        $coreTmplId = $coreTmpl ? $coreTmpl->id : null;
                    }
                }

                DB::table('modal_forms')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'name' => $lModal->name,
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'title' => $lModal->title,
                        'description' => $lModal->description ?? null,
                        'form_template_id' => $coreTmplId,
                        'config' => is_string($lModal->config) ? $lModal->config : json_encode($lModal->config),
                        'is_active' => $lModal->is_active ?? 1,
                        'trigger_type' => $lModal->trigger_type ?? 'delay',
                        'trigger_delay' => $lModal->trigger_delay ?? 3,
                        'trigger_scroll' => $lModal->trigger_scroll ?? 50,
                        'show_frequency' => $lModal->show_frequency ?? 'once_per_session',
                        'created_at' => $lModal->created_at ?? now(),
                        'updated_at' => $lModal->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} modal forms.");
        } catch (\Exception $e) {
            $this->warn('Error syncing modal forms: '.$e->getMessage());
        }
    }

    private function syncFlashSales($projectId, $tenantId)
    {
        $this->info('Syncing Flash Sales...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('flash_sale_campaigns')) {
                $this->warn('Legacy flash_sale_campaigns table does not exist.');

                return;
            }

            $legacyCampaigns = DB::connection('vtm')->table('flash_sale_campaigns')->get();
            $count = 0;

            foreach ($legacyCampaigns as $camp) {
                DB::table('flash_sale_campaigns')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'name' => $camp->name,
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'description' => $camp->description ?? null,
                        'starts_at' => $camp->starts_at,
                        'ends_at' => $camp->ends_at,
                        'status' => $camp->status ?? 'active',
                        'apply_to_all' => $camp->apply_to_all ?? 0,
                        'created_at' => $camp->created_at ?? now(),
                        'updated_at' => $camp->updated_at ?? now(),
                    ]
                );

                $coreCamp = DB::table('flash_sale_campaigns')->where('project_id', $projectId)->where('name', $camp->name)->first();
                if ($coreCamp && DB::connection('vtm')->getSchemaBuilder()->hasTable('flash_sale_items')) {
                    $items = DB::connection('vtm')->table('flash_sale_items')->where('campaign_id', $camp->id)->get();
                    foreach ($items as $item) {
                        $lProd = DB::connection('vtm')->table('products')->where('id', $item->product_id)->first();
                        $coreProdId = null;
                        if ($lProd) {
                            $slug = $lProd->slug ?? Str::slug($lProd->name);
                            $coreProd = DB::table('products_enhanced')->where('project_id', $projectId)->where('slug', $slug)->first();
                            $coreProdId = $coreProd ? $coreProd->id : null;
                        }

                        DB::table('flash_sale_items')->updateOrInsert(
                            [
                                'campaign_id' => $coreCamp->id,
                                'product_id' => $coreProdId,
                            ],
                            [
                                'category_id' => $item->category_id ?? null,
                                'discount_type' => $item->discount_type ?? 'percent',
                                'discount_value' => $item->discount_value ?? 0,
                                'sale_limit' => $item->sale_limit ?? null,
                                'sold_count' => $item->sold_count ?? 0,
                                'created_at' => $item->created_at ?? now(),
                                'updated_at' => $item->updated_at ?? now(),
                            ]
                        );
                    }
                }
                $count++;
            }
            $this->info("Synced {$count} flash sale campaigns and items.");
        } catch (\Exception $e) {
            $this->warn('Error syncing flash sales: '.$e->getMessage());
        }
    }

    private function syncPages($projectId, $tenantId)
    {
        $this->info('Syncing Pages...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('pages')) {
                $this->warn('Legacy pages table does not exist.');

                return;
            }

            $legacyPages = DB::connection('vtm')->table('pages')->get();
            $count = 0;

            foreach ($legacyPages as $lPage) {
                $slug = Str::slug($lPage->slug ?? $lPage->title);

                $existing = DB::table('posts')->where('slug', $slug)->first();
                if ($existing && $existing->project_id != $projectId) {
                    $slug = $slug.'-'.$projectId;
                }

                DB::table('posts')->updateOrInsert(
                    [
                        'project_id' => $projectId,
                        'slug' => $slug,
                        'post_type' => 'page',
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'title' => $lPage->title,
                        'excerpt' => $lPage->excerpt ?? null,
                        'content' => $lPage->content ?? null,
                        'featured_image' => $lPage->image ?? null,
                        'status' => ($lPage->status === 'published' || $lPage->status === 'active' || $lPage->status === 1) ? 'published' : 'draft',
                        'meta_title' => $lPage->meta_title ?? $lPage->title,
                        'meta_description' => $lPage->meta_description ?? null,
                        'published_at' => $lPage->published_at ?? now(),
                        'created_at' => $lPage->created_at ?? now(),
                        'updated_at' => $lPage->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} pages into posts table.");
        } catch (\Exception $e) {
            $this->warn('Error syncing pages: '.$e->getMessage());
        }
    }

    private function syncCategoryProduct($projectId, $tenantId)
    {
        $this->info('Syncing Category Product Relationships...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('category_product')) {
                $this->warn('Legacy category_product table does not exist.');

                return;
            }

            $relations = DB::connection('vtm')->table('category_product')->get();
            $count = 0;

            foreach ($relations as $rel) {
                $lProd = DB::connection('vtm')->table('products')->where('id', $rel->product_id)->first();
                $lCat = DB::connection('vtm')->table('categories')->where('id', $rel->category_id)->first();

                if (! $lProd || ! $lCat) {
                    continue;
                }

                $prodSlug = $lProd->slug ?? Str::slug($lProd->name);
                $catSlug = $lCat->slug ?? Str::slug($lCat->name);

                $coreProd = DB::table('products_enhanced')->where('project_id', $projectId)->where('slug', $prodSlug)->first();
                $coreCat = DB::table('taxonomies')->where('project_id', $projectId)->where('slug', $catSlug)->first();
                $coreProdCat = DB::table('product_categories')->where('project_id', $projectId)->where('slug', $catSlug)->first();

                $catId = $coreProdCat ? $coreProdCat->id : ($coreCat ? $coreCat->id : null);

                if ($coreProd && $catId) {
                    DB::table('products_enhanced')->where('id', $coreProd->id)->update([
                        'product_category_id' => $catId,
                    ]);

                    if ($coreCat && Schema::hasTable('term_relationships')) {
                        DB::table('term_relationships')->updateOrInsert(
                            [
                                'object_id' => $coreProd->id,
                                'term_taxonomy_id' => $coreCat->id,
                            ],
                            ['order' => 0]
                        );
                    }
                    $count++;
                }
            }
            $this->info("Synced {$count} category-product relationships.");
        } catch (\Exception $e) {
            $this->warn('Error syncing category product relations: '.$e->getMessage());
        }
    }

    private function syncTranslations($projectId, $tenantId)
    {
        $this->info('Syncing Translations...');
        try {
            if (! DB::connection('vtm')->getSchemaBuilder()->hasTable('translations')) {
                $this->warn('Legacy translations table does not exist.');

                return;
            }

            $legacyTranslations = DB::connection('vtm')->table('translations')->get();
            $count = 0;

            foreach ($legacyTranslations as $lTrans) {
                $coreId = $lTrans->translatable_id;
                if ($lTrans->translatable_type === 'App\\Models\\Product' || $lTrans->translatable_type === 'Product') {
                    $lProd = DB::connection('vtm')->table('products')->where('id', $lTrans->translatable_id)->first();
                    if ($lProd) {
                        $slug = $lProd->slug ?? Str::slug($lProd->name);
                        $coreProd = DB::table('products_enhanced')->where('project_id', $projectId)->where('slug', $slug)->first();
                        if ($coreProd) {
                            $coreId = $coreProd->id;
                        }
                    }
                }

                DB::table('translations')->updateOrInsert(
                    [
                        'translatable_type' => $lTrans->translatable_type,
                        'translatable_id' => $coreId,
                        'locale' => $lTrans->locale,
                        'field' => $lTrans->field,
                    ],
                    [
                        'value' => $lTrans->value ?? '',
                        'created_at' => $lTrans->created_at ?? now(),
                        'updated_at' => $lTrans->updated_at ?? now(),
                    ]
                );
                $count++;
            }
            $this->info("Synced {$count} translations.");
        } catch (\Exception $e) {
            $this->warn('Error syncing translations: '.$e->getMessage());
        }
    }
}

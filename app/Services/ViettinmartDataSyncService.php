<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Widget;
use Database\Seeders\ViettinmartWidgetsSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ViettinmartDataSyncService
{
    /**
     * All tables known to contain project_id for Viettinmart
     */
    protected array $projectTables = [
        'widgets',
        'products_enhanced',
        'product_categories',
        'product_reviews',
        'posts',
        'settings',
        'project_settings',
        'menus',
        'menu_items',
        'orders',
        'order_items',
        'order_status_histories',
        'product_variations',
        'product_attribute_value_mappings',
        'coupons',
        'brands',
        'form_templates',
        'modal_forms',
        'taxonomies',
        'shipping_carriers',
        'flash_sale_campaigns',
        'agents',
    ];

    /**
     * Synchronize and auto-heal Viettinmart data for the target project ID and tenant ID.
     */
    public function syncProjectId(int $targetProjectId, ?int $targetTenantId = null): array
    {
        $log = [];
        $project = Project::find($targetProjectId);
        if (! $project) {
            $project = Project::where('code', 'viettinmart-eco')->orWhere('code', 'viettinmart')->first();
        }

        // 1. Resolve target tenant ID
        if (! $targetTenantId) {
            $targetTenantId = $project?->tenant_id;
            if (! $targetTenantId && Schema::hasTable('tenants')) {
                $tenant = Tenant::where('code', 'viettinmart')
                    ->orWhere('code', 'viettinmart-eco')
                    ->orWhere('name', 'like', '%Viettinmart%')
                    ->first();

                if (! $tenant) {
                    $tenant = Tenant::find(3) ?? Tenant::firstOrCreate(
                        ['code' => 'viettinmart-eco'],
                        ['name' => 'Viettinmart E-commerce', 'status' => 'active']
                    );
                }
                $targetTenantId = $tenant?->id ?? 3;
            } else {
                $targetTenantId = $targetTenantId ?: 3;
            }
        }

        // 2. Ensure project table has tenant_id set
        if ($project && Schema::hasColumn('projects', 'tenant_id')) {
            if ($project->tenant_id !== $targetTenantId) {
                try {
                    $project->update(['tenant_id' => $targetTenantId]);
                    $log[] = "Updated project #{$targetProjectId} tenant_id to {$targetTenantId}";
                } catch (\Throwable $e) {
                    // Ignore column update failure
                }
            }
        }

        // 3. Remap tables from legacy project_id = 10 to targetProjectId if needed & normalize tenant_id
        $remappedTables = [];
        foreach ($this->projectTables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if ($targetProjectId !== 10 && Schema::hasColumn($table, 'project_id')) {
                $count10 = DB::table($table)->where('project_id', 10)->count();

                if ($count10 > 0) {
                    DB::table($table)->where('project_id', 10)->update([
                        'project_id' => $targetProjectId,
                    ]);
                    $remappedTables[$table] = $count10;
                }
            }

            // Always ensure tenant_id is normalized to targetTenantId
            if (Schema::hasColumn($table, 'tenant_id') && $targetTenantId) {
                DB::table($table)
                    ->where(function ($q) use ($targetProjectId) {
                        $q->where('project_id', $targetProjectId);
                        if ($targetProjectId !== 10) {
                            $q->orWhere('project_id', 10);
                        }
                    })
                    ->where(function ($q) use ($targetTenantId) {
                        $q->whereNull('tenant_id')
                            ->orWhere('tenant_id', '!=', $targetTenantId);
                    })
                    ->update(['tenant_id' => $targetTenantId]);
            }
        }

        if (! empty($remappedTables)) {
            $log[] = 'Remapped tables from project_id=10: '.json_encode($remappedTables);
            Log::info("ViettinmartDataSyncService: Remapped tables for project {$targetProjectId}", $remappedTables);
        }

        // 4. Deduplicate product_categories by slug for tenant / project
        if (Schema::hasTable('product_categories')) {
            $duplicateSlugs = DB::table('product_categories')
                ->where(function ($q) use ($targetProjectId, $targetTenantId) {
                    $q->where('tenant_id', $targetTenantId)
                        ->orWhere('project_id', $targetProjectId);
                    if ($targetProjectId !== 10) {
                        $q->orWhere('project_id', 10);
                    }
                })
                ->whereNull('deleted_at')
                ->select('slug', DB::raw('count(*) as total'))
                ->groupBy('slug')
                ->having('total', '>', 1)
                ->pluck('slug');

            foreach ($duplicateSlugs as $slug) {
                $categories = DB::table('product_categories')
                    ->where('slug', $slug)
                    ->where(function ($q) use ($targetProjectId, $targetTenantId) {
                        $q->where('tenant_id', $targetTenantId)
                            ->orWhere('project_id', $targetProjectId);
                        if ($targetProjectId !== 10) {
                            $q->orWhere('project_id', 10);
                        }
                    })
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'asc')
                    ->get();

                if ($categories->count() > 1) {
                    $primaryCategory = $categories->first();
                    $duplicateIds = $categories->slice(1)->pluck('id')->toArray();

                    // Remap product references to primary category
                    if (Schema::hasTable('products') && Schema::hasColumn('products', 'product_category_id')) {
                        DB::table('products')->whereIn('product_category_id', $duplicateIds)->update(['product_category_id' => $primaryCategory->id]);
                    }
                    if (Schema::hasTable('products_enhanced') && Schema::hasColumn('products_enhanced', 'product_category_id')) {
                        DB::table('products_enhanced')->whereIn('product_category_id', $duplicateIds)->update(['product_category_id' => $primaryCategory->id]);
                    }
                    if (Schema::hasTable('product_category_product') && Schema::hasColumn('product_category_product', 'product_category_id')) {
                        DB::table('product_category_product')->whereIn('product_category_id', $duplicateIds)->update(['product_category_id' => $primaryCategory->id]);
                    }

                    // Delete duplicate category records
                    DB::table('product_categories')->whereIn('id', $duplicateIds)->delete();
                    $log[] = "Deduplicated category '{$slug}': kept ID {$primaryCategory->id}, removed IDs: ".implode(', ', $duplicateIds);
                }
            }
        }

        // 5. Sanitize widgets settings JSON to replace project_id and tenant_id
        if (Schema::hasTable('widgets')) {
            $widgets = Widget::withoutGlobalScopes()
                ->where('project_id', $targetProjectId)
                ->get();

            foreach ($widgets as $widget) {
                $settings = $widget->settings ?? [];
                $needsUpdate = false;

                if (! empty($settings['project_id']) && $settings['project_id'] != $targetProjectId) {
                    $settings['project_id'] = $targetProjectId;
                    $needsUpdate = true;
                }
                if ($targetTenantId && ! empty($settings['tenant_id']) && $settings['tenant_id'] != $targetTenantId) {
                    $settings['tenant_id'] = $targetTenantId;
                    $needsUpdate = true;
                }
                if ($widget->tenant_id != $targetTenantId && $targetTenantId) {
                    $widget->tenant_id = $targetTenantId;
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $widget->settings = $settings;
                    $widget->saveQuietly();
                }
            }
        }

        // 5. Update users project_ids so Viettinmart users have access to targetProjectId
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'project_ids')) {
            $users = User::withoutGlobalScopes()->get();
            foreach ($users as $user) {
                $pids = is_array($user->project_ids) ? $user->project_ids : json_decode($user->project_ids ?? '[]', true);
                if (! is_array($pids)) {
                    $pids = [];
                }

                // If user has project 10 or is a viettinmart-specific user, ensure targetProjectId is present
                $isVtmUser = in_array(10, $pids)
                    || str_contains(strtolower($user->email ?? ''), 'viettinmart')
                    || str_contains(strtolower($user->username ?? ''), 'viettinmart')
                    || str_contains(strtolower($user->name ?? ''), 'viettinmart');

                if ($isVtmUser && ! in_array($targetProjectId, $pids)) {
                    $pids[] = $targetProjectId;
                    $user->project_ids = array_values(array_unique($pids));
                    $user->saveQuietly();
                    $log[] = "Added project #{$targetProjectId} to user {$user->username}";
                }
            }
        }

        // 6. If target project still has 0 widgets, run ViettinmartWidgetsSeeder
        $widgetCount = Schema::hasTable('widgets')
            ? Widget::withoutGlobalScopes()->where('project_id', $targetProjectId)->count()
            : 0;

        if ($widgetCount === 0 && class_exists(ViettinmartWidgetsSeeder::class)) {
            try {
                (new ViettinmartWidgetsSeeder)->run($targetProjectId, $targetTenantId);
                $widgetCount = Widget::withoutGlobalScopes()->where('project_id', $targetProjectId)->count();
                $log[] = "Auto-seeded {$widgetCount} widgets for project #{$targetProjectId}";
            } catch (\Throwable $e) {
                $log[] = 'Widget seeder error: '.$e->getMessage();
                Log::warning("ViettinmartDataSyncService: Seeder failed: {$e->getMessage()}");
            }
        }

        // 7. Clear caches
        Cache::flush();

        return [
            'status' => 'success',
            'project_id' => $targetProjectId,
            'tenant_id' => $targetTenantId,
            'widget_count' => $widgetCount,
            'remapped_tables' => $remappedTables,
            'log' => $log,
        ];
    }
}

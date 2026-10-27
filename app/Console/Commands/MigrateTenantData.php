<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateTenantData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:tenant-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from single-site public_html DB to multisite DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Khởi tạo Tenant & Project
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'viettinmartdemo.local'],
            [
                'name' => 'Viettinmart Demo',
                'code' => 'viettinmart',
                'database_name' => 'core',
                'status' => 'active',
            ]
        );

        $project = Project::firstOrCreate(
            ['code' => 'viettinmart-eco'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Viettinmart Ecommerce',
                'status' => 'active',
                'description' => 'Migrated from public_html',
            ]
        );

        $this->info("Tenant ID: {$tenant->id}, Project ID: {$project->id} initialized.");

        // Kết nối DB cũ
        // Config này tạm thời add trực tiếp hoặc thông qua config/database.php
        config(['database.connections.old_db' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'viettinmartdemo_demo1',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        try {
            DB::connection('old_db')->getPdo();
            $this->info('Connected to old database successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to connect to old database: '.$e->getMessage());

            return;
        }

        // Bước 2: Migrate Category
        // Lấy dữ liệu từ old categories
        if (Schema::connection('old_db')->hasTable('categories')) {
            $oldCategories = DB::connection('old_db')->table('categories')->get();
            foreach ($oldCategories as $oldCat) {
                DB::table('product_categories')->updateOrInsert(
                    ['slug' => $oldCat->slug, 'tenant_id' => $tenant->id],
                    [
                        'name' => $oldCat->name,
                        'description' => $oldCat->description ?? null,
                        'parent_id' => null, // Tạm thời set null, update cây thư mục sau nếu cần
                        'tenant_id' => $tenant->id,
                        'project_id' => $project->id,
                        'created_at' => $oldCat->created_at,
                        'updated_at' => $oldCat->updated_at,
                    ]
                );
            }
            $this->info('Migrated Categories');
        }

        // Bước 3: Migrate Products
        if (Schema::connection('old_db')->hasTable('products')) {
            $oldProducts = DB::connection('old_db')->table('products')->get();
            foreach ($oldProducts as $oldProd) {
                // Mapping category (cần category ID mới)
                $newCatId = null;
                if (isset($oldProd->category_id) && $oldProd->category_id) {
                    $oldCat = DB::connection('old_db')->table('categories')->where('id', $oldProd->category_id)->first();
                    if ($oldCat) {
                        $newCat = DB::table('product_categories')->where('slug', $oldCat->slug)->where('tenant_id', $tenant->id)->first();
                        if ($newCat) {
                            $newCatId = $newCat->id;
                        }
                    }
                }

                DB::table('products_enhanced')->updateOrInsert(
                    ['slug' => $oldProd->slug, 'tenant_id' => $tenant->id],
                    [
                        'name' => $oldProd->name,
                        'sku' => ! empty($oldProd->sku) ? $oldProd->sku : 'SKU-'.strtoupper(uniqid()),
                        'short_description' => $oldProd->short_description ?? null,
                        'description' => $oldProd->description ?? null,
                        'price' => $oldProd->price ?? 0,
                        'sale_price' => $oldProd->sale_price ?? null,
                        'product_category_id' => $newCatId,
                        'status' => ($oldProd->status == 'active') ? 'published' : 'draft',
                        'tenant_id' => $tenant->id,
                        'project_id' => $project->id,
                        'created_at' => $oldProd->created_at ?? now(),
                        'updated_at' => $oldProd->updated_at ?? now(),
                    ]
                );
            }
            $this->info('Migrated Products');
        }

        $this->migrateSettings($tenant, $project);
        $this->migrateWidgets($tenant, $project);

        $this->info('Migration basic data completed successfully.');
    }

    protected function migrateSettings($tenant, $project)
    {
        if (! Schema::connection('old_db')->hasTable('settings')) {
            $this->warn('Old settings table not found. Skipping settings migration.');

            return;
        }

        $oldSettings = DB::connection('old_db')->table('settings')->get();
        foreach ($oldSettings as $oldSetting) {
            DB::table('settings')->updateOrInsert(
                [
                    'key' => $oldSetting->key,
                    'tenant_id' => $tenant->id,
                    'project_id' => $project->id,
                ],
                [
                    'value' => $oldSetting->value,
                    'type' => $oldSetting->type ?? 'text',
                    'group' => $oldSetting->group ?? 'general',
                    'locked' => false,
                    'created_at' => $oldSetting->created_at ?? now(),
                    'updated_at' => $oldSetting->updated_at ?? now(),
                ]
            );
        }
        $this->info('Migrated Settings');
    }

    protected function migrateWidgets($tenant, $project)
    {
        if (! Schema::connection('old_db')->hasTable('widgets')) {
            $this->warn('Old widgets table not found. Skipping widgets migration.');

            return;
        }

        $oldWidgets = DB::connection('old_db')->table('widgets')->get();

        $typeMapping = [
            'hero_slider' => 'inbetween_hero_slider',
            'feature_icons' => 'inbetween_feature_icons',
            'prod_featured' => 'inbetween_product_featured',
            'deal_flash' => 'inbetween_deal_flash',
            'prod_tabs' => 'inbetween_product_tabs',
            'promo_banners' => 'inbetween_promo_banners',
            'top_trending' => 'inbetween_top_trending',
            'posts_latest' => 'inbetween_posts_latest',
            'form_widget' => 'inbetween_form_widget',
        ];

        foreach ($oldWidgets as $oldWidget) {
            $newType = $typeMapping[$oldWidget->type] ?? 'inbetween_'.$oldWidget->type;

            DB::table('widgets')->updateOrInsert(
                [
                    'name' => $oldWidget->name,
                    'tenant_id' => $tenant->id,
                    'project_id' => $project->id,
                    'area' => $oldWidget->area,
                ],
                [
                    'type' => $newType,
                    'settings' => $oldWidget->config, // config json maps to settings json
                    'sort_order' => $oldWidget->sort_order,
                    'is_active' => $oldWidget->is_active ?? 1,
                    'created_at' => $oldWidget->created_at ?? now(),
                    'updated_at' => $oldWidget->updated_at ?? now(),
                ]
            );
        }
        $this->info('Migrated Widgets');
    }
}

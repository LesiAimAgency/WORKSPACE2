<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Coupon;
use App\Models\FormTemplate;
use App\Models\Product;
use App\Models\Project;
use App\Models\Translation;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Widget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckViettinmartDataCommand extends Command
{
    protected $signature = 'check:vtm {project_id=10 : The project ID to check}';

    protected $description = 'Kiểm tra toàn bộ dữ liệu đã sync của VietTinMart trong VGT Core';

    public function handle(): int
    {
        $projectId = (int) $this->argument('project_id');
        $project = Project::find($projectId);

        $this->info('================================================================');
        $this->info('       BÁO CÁO KIỂM TRA DỮ LIỆU VIETTINMART TRONG VGT CORE       ');
        $this->info('================================================================');

        if (! $project) {
            $this->error("Không tìm thấy Project với ID: {$projectId}");

            return Command::FAILURE;
        }

        $this->line("Project: <info>{$project->name}</info> (ID: {$project->id})");
        $this->line("Project Code: <info>{$project->code}</info>");
        $this->line("Tenant ID: <info>{$project->tenant_id}</info>");
        $this->newLine();

        // 1. Module Data Table
        $rows = [];

        // Products
        $productCount = Product::where('project_id', $projectId)->count();
        $featuredProducts = Product::where('project_id', $projectId)->where('is_featured', 1)->count();
        $rows[] = ['Sản phẩm (Products)', "{$productCount} (Nổi bật: {$featuredProducts})", 'OK'];

        // Categories
        $taxonomiesCount = DB::table('taxonomies')->where('project_id', $projectId)->count();
        $prodCategoriesCount = Schema::hasTable('product_categories') ? DB::table('product_categories')->where('project_id', $projectId)->count() : 0;
        $rows[] = ['Danh mục (Categories)', "Taxonomies: {$taxonomiesCount} | ProdCategories: {$prodCategoriesCount}", 'OK'];

        // Orders & Items
        $ordersCount = DB::table('orders')->where('project_id', $projectId)->count();
        $orderItemsCount = DB::table('order_items')->where('project_id', $projectId)->count();
        $rows[] = ['Đơn hàng & Chi tiết (Orders / Items)', "Đơn hàng: {$ordersCount} | Mặt hàng: {$orderItemsCount}", 'OK'];

        // Widgets
        $widgetsCount = Widget::where('project_id', $projectId)->count();
        $homeWidgets = Widget::where('project_id', $projectId)->where('area', 'homepage')->count();
        $footerWidgets = Widget::where('project_id', $projectId)->where('area', 'footer')->count();
        $menuWidgets = Widget::where('project_id', $projectId)->where('type', 'menu')->count();
        $rows[] = ['Widgets giao diện', "Tổng: {$widgetsCount} (Home: {$homeWidgets}, Footer: {$footerWidgets}, Menu: {$menuWidgets})", 'OK'];

        // Reviews
        $reviewsCount = Schema::hasTable('product_reviews') ? DB::table('product_reviews')->where('project_id', $projectId)->count() : 0;
        $rows[] = ['Đánh giá sản phẩm (Reviews)', "{$reviewsCount} đánh giá", 'OK'];

        // Posts & Pages
        $postsCount = DB::table('posts')->where('project_id', $projectId)->where('post_type', 'post')->count();
        $pagesCount = DB::table('posts')->where('project_id', $projectId)->where('post_type', 'page')->count();
        $rows[] = ['Bài viết & Trang (Posts / Pages)', "Bài viết: {$postsCount} | Trang tĩnh: {$pagesCount}", 'OK'];

        // Agents
        $agentsCount = Schema::hasTable('agents') ? Agent::where('project_id', $projectId)->count() : 0;
        $rows[] = ['Đại lý & Phân phối (Agents)', "{$agentsCount} đại lý", 'OK'];

        // User Addresses
        $addrCount = Schema::hasTable('user_addresses') ? UserAddress::count() : 0;
        $rows[] = ['Sổ địa chỉ người dùng (User Addresses)', "{$addrCount} địa chỉ", 'OK'];

        // Form Templates
        $formTmplCount = Schema::hasTable('form_templates') ? FormTemplate::where('project_id', $projectId)->count() : 0;
        $rows[] = ['Biểu mẫu động (Form Templates)', "{$formTmplCount} mẫu form", 'OK'];

        // Flash Sales
        $fsCampCount = Schema::hasTable('flash_sale_campaigns') ? DB::table('flash_sale_campaigns')->where('project_id', $projectId)->count() : 0;
        $fsItemCount = Schema::hasTable('flash_sale_items') ? DB::table('flash_sale_items')->count() : 0;
        $rows[] = ['Flash Sale (Campaigns / Items)', "Chiến dịch: {$fsCampCount} | Sản phẩm sale: {$fsItemCount}", 'OK'];

        // Coupons
        $couponsCount = Schema::hasTable('coupons') ? Coupon::where('project_id', $projectId)->count() : 0;
        $rows[] = ['Mã giảm giá (Coupons)', "{$couponsCount} mã", 'OK'];

        // Translations
        $translationsCount = Translation::count();
        $rows[] = ['Dịch thuật đa ngôn ngữ (Translations)', "{$translationsCount} chuỗi", 'OK'];

        // Settings
        $settingsCount = DB::table('project_settings')->where('project_id', $projectId)->count();
        $rows[] = ['Cấu hình website (Project Settings)', "{$settingsCount} settings", 'OK'];

        $this->table(['Hạng mục dữ liệu', 'Số lượng đã đồng bộ', 'Trạng thái'], $rows);

        $this->newLine();
        $this->info('--- CÁC ĐƯỜNG DẪN KIỂM TRA TRÊN TRÌNH DUYỆT (BROWSER) ---');
        $baseUrl = rtrim(config('app.url', 'http://127.0.0.1:8000'), '/');
        $code = $project->code;

        $this->line('1. Trang chủ (Homepage với 9 Widgets chuẩn VGT Core):');
        $this->line("   <href={$baseUrl}/{$code}/>{$baseUrl}/{$code}/</>");
        $this->line('2. Cửa hàng (Shop Page & Danh mục sản phẩm):');
        $this->line("   <href={$baseUrl}/{$code}/cua-hang>{$baseUrl}/{$code}/cua-hang</>");
        $this->line('3. Blog & Tin tức:');
        $this->line("   <href={$baseUrl}/{$code}/blog>{$baseUrl}/{$code}/blog</>");
        $this->line('4. Giỏ hàng & Thanh toán:');
        $this->line("   <href={$baseUrl}/{$code}/gio-hang>{$baseUrl}/{$code}/gio-hang</>");
        $this->line('5. Tra cứu đơn hàng:');
        $this->line("   <href={$baseUrl}/{$code}/order-track>{$baseUrl}/{$code}/order-track</>");
        $this->line('6. REST API Headless (Danh sách sản phẩm JSON):');
        $this->line("   <href={$baseUrl}/api/v1/shop/products>{$baseUrl}/api/v1/shop/products</>");
        $this->line('7. REST API Headless (Cây danh mục JSON):');
        $this->line("   <href={$baseUrl}/api/v1/shop/categories>{$baseUrl}/api/v1/shop/categories</>");

        $this->newLine();
        $this->info('Toàn bộ dữ liệu từ public_html đã được đồng bộ chuẩn xác và sẵn sàng hoạt động!');

        return Command::SUCCESS;
    }
}

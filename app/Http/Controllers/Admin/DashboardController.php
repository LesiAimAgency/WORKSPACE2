<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Ehenho\Conversation;
use App\Models\Ehenho\Message;
use App\Models\Ehenho\Profile;
use App\Models\FormSubmission;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectSetting;
use App\Models\User;
use App\Models\VisitorLog;
use App\Models\Widget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->projectDashboard($request);
    }

    /**
     * Dashboard route: Redirect directly to project CMS dashboard in standalone mode.
     */
    public function superAdminDashboard(): RedirectResponse
    {
        return redirect()->route('project.admin.dashboard');
    }

    public function projectDashboard(Request $request)
    {
        $project = $request->attributes->get('project');
        $code = $request->route('projectCode') ?? $request->segment(1);
        if (! $project && $code && $code !== 'admin') {
            $project = Project::where('code', $code)->first();
        }
        if (! $project) {
            $defaultCode = env('PROJECT_CODE', 'DA005');
            $project = Project::where('code', $defaultCode)->first() ?: Project::first();
        }
        if ($project) {
            $request->attributes->set('project', $project);
            view()->share('currentProject', $project);
            view()->share('project', $project);
            $isStandalone = config('app.standalone_mode') || env('STANDALONE_MODE') || $request->segment(1) === 'admin';
            view()->share('projectCode', $isStandalone ? null : $project->code);
        }

        $isInbetweenV2 = ($project && ($project->code === 'DA005' || $project->code === 'inbetween_v2' || ($project->features['theme'] ?? null) === 'inbetween_v2'))
            || in_array($code, ['DA005', 'inbetween_v2', 'inbetween-v2', 'inbetween', 'inbetwen'])
            || env('PROJECT_CODE') === 'DA005'
            || env('THEME') === 'inbetween_v2';

        if ($isInbetweenV2) {
            $inbetweenData = $this->getInbetweenV2DashboardData($project);
            if (view()->exists('themes.inbetween_v2.admin.dashboard')) {
                return view('themes.inbetween_v2.admin.dashboard', $inbetweenData);
            }

            return view('cms.dashboard', $inbetweenData);
        }

        $data = $this->getDashboardData($project);

        $isViettinmart = ($project && ($project->code === 'viettinmart-eco' || ($project->features['theme'] ?? null) === 'viettinmartdemo'))
            || ($request->route('projectCode') === 'viettinmart-eco');

        if ($isViettinmart && view()->exists('frontend.themes.viettinmartdemo.admin.dashboard')) {
            return view('frontend.themes.viettinmartdemo.admin.dashboard', $data);
        }

        $isWkcomputer = ($project && ($project->code === 'wkcomputer' || ($project->features['theme'] ?? null) === 'wkcomputerdemo'))
            || ($request->route('projectCode') === 'wkcomputer');

        if ($isWkcomputer && view()->exists('frontend.themes.wkcomputerdemo.admin.dashboard')) {
            return view('frontend.themes.wkcomputerdemo.admin.dashboard', $data);
        }

        $isEhenho = ($project && ($project->code === 'ehenho' || ($project->features['theme'] ?? null) === 'ehenho'))
            || ($request->route('projectCode') === 'ehenho');

        if ($isEhenho && view()->exists('themes.ehenho.admin.dashboard')) {
            $ehenhoData = $this->getEhenhoDashboardData($project);

            return view('themes.ehenho.admin.dashboard', array_merge($data, $ehenhoData));
        }

        // For non-ecommerce projects, use the cleaned visitor and form dashboard
        $hasCommerce = $project && ($project->hasFeature('commerce') || $project->hasFeature('product_listing'));
        if (! $hasCommerce) {
            $inbetweenData = $this->getInbetweenV2DashboardData($project);

            return view('cms.dashboard', array_merge($data, $inbetweenData));
        }

        return view('cms.dashboard.index', $data);
    }

    /**
     * Build aggregated dashboard dataset for either single-project or global CMS.
     */
    private function getDashboardData(?Project $project = null): array
    {
        $projectId = $project?->id;
        $now = now();
        $todayStart = today()->startOfDay();

        $hasOrdersTable = Schema::hasTable('orders');
        $hasProductsTable = Schema::hasTable('products');
        $hasUsersTable = Schema::hasTable('users');
        $hasOrderItemsTable = Schema::hasTable('order_items');
        $hasVisitorLogsTable = Schema::hasTable('visitor_logs');

        // Base scoped queries (only when tables exist)
        $orderQuery = $hasOrdersTable ? Order::withoutGlobalScopes() : null;
        $productQuery = $hasProductsTable ? Product::withoutGlobalScopes() : null;
        $userQuery = $hasUsersTable ? User::withoutGlobalScopes() : null;

        if ($projectId) {
            if ($orderQuery) {
                $orderQuery->where(function ($q) use ($projectId) {
                    $q->where('project_id', $projectId)->orWhere('tenant_id', $projectId);
                });
            }
            if ($productQuery) {
                $productQuery->where(function ($q) use ($projectId) {
                    $q->where('project_id', $projectId)->orWhere('tenant_id', $projectId);
                });
            }

            if ($userQuery) {
                $userQuery->where(function ($q) use ($projectId, $project) {
                    if ($project && $project->tenant_id) {
                        $q->where('tenant_id', $project->tenant_id);
                    } elseif (Schema::hasColumn('users', 'tenant_id')) {
                        $q->where('tenant_id', $projectId);
                    }

                    $driver = DB::connection()->getDriverName();
                    if ($driver === 'mysql' || $driver === 'mariadb') {
                        $q->orWhereJsonContains('project_ids', (int) $projectId)
                            ->orWhereJsonContains('project_ids', (string) $projectId);
                    } else {
                        $q->orWhere('project_ids', 'like', '%"'.$projectId.'"%')
                            ->orWhere('project_ids', 'like', '%'.$projectId.'%');
                    }

                    if (Schema::hasColumn('users', 'project_id')) {
                        $q->orWhere('project_id', $projectId);
                    }
                });
            }
        }

        // Default values for Orders & Revenue metrics
        $pendingOrders = 0;
        $processingOrders = 0;
        $shippingOrders = 0;
        $completedToday = 0;
        $unassignedOrders = 0;
        $recentUnassigned = collect();
        $todayRevenue = 0.0;
        $monthlyRevenue = 0.0;
        $totalRevenue = 0.0;
        $revenue3m = 0.0;
        $revenue6m = 0.0;
        $totalOrders = 0;
        $todayOrders = 0;
        $recentOrders = collect();
        $revenueChartMonthly = collect();
        $revenueChart = collect();
        $ordersChart = collect();

        // Pipeline Metrics & Revenue
        if ($orderQuery) {
            try {
                $pendingOrders = (clone $orderQuery)->where('status', 'pending')->count();
                $processingOrders = (clone $orderQuery)->where('status', 'processing')->count();
                $shippingOrders = (clone $orderQuery)->whereIn('status', ['shipping', 'shipped'])->count();
                $completedToday = (clone $orderQuery)->whereIn('status', ['completed', 'delivered'])
                    ->where('updated_at', '>=', $todayStart)
                    ->count();

                $hasAgentId = Schema::hasColumn('orders', 'agent_id');
                if ($hasAgentId) {
                    $unassignedOrders = (clone $orderQuery)->whereNull('agent_id')->whereNotIn('status', ['cancelled', 'refunded'])->count();
                } else {
                    $unassignedOrders = $pendingOrders;
                }

                $recentUnassigned = (clone $orderQuery)
                    ->when($hasAgentId, fn ($q) => $q->whereNull('agent_id'))
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->latest()
                    ->take(5)
                    ->get();

                $todayRevenue = (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])
                    ->where('created_at', '>=', $todayStart)
                    ->sum('total_amount');

                $monthlyRevenue = (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->sum('total_amount');

                $totalRevenue = (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])
                    ->sum('total_amount');

                $revenue3m = (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])
                    ->where('created_at', '>=', $now->copy()->subMonths(3))
                    ->sum('total_amount');

                $revenue6m = (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])
                    ->where('created_at', '>=', $now->copy()->subMonths(6))
                    ->sum('total_amount');

                $totalOrders = (clone $orderQuery)->count();
                $todayOrders = (clone $orderQuery)->where('created_at', '>=', $todayStart)->count();
                $recentOrders = (clone $orderQuery)->latest()->take(6)->get();

                // 6 Months Revenue Chart for public_html dashboard
                for ($i = 5; $i >= 0; $i--) {
                    $m = now()->subMonths($i);
                    $mStart = $m->copy()->startOfMonth();
                    $mEnd = $m->copy()->endOfMonth();
                    $mLabel = $m->format('m/Y');

                    $mRev = (float) (clone $orderQuery)
                        ->whereNotIn('status', ['cancelled', 'refunded'])
                        ->whereBetween('created_at', [$mStart, $mEnd])
                        ->sum('total_amount');

                    $revenueChartMonthly->push((object) [
                        'month' => $mLabel,
                        'month_key' => $m->format('Y-m'),
                        'revenue' => $mRev,
                    ]);
                }

                // 7 Days Chart Data for legacy/CMS views
                for ($i = 6; $i >= 0; $i--) {
                    $day = now()->subDays($i);
                    $dayStart = $day->copy()->startOfDay();
                    $dayEnd = $day->copy()->endOfDay();
                    $dayLabel = $day->format('d/m');

                    $dayRev = (float) (clone $orderQuery)
                        ->whereNotIn('status', ['cancelled', 'refunded'])
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->sum('total_amount');

                    $dayOrd = (int) (clone $orderQuery)
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->count();

                    $revenueChart->push([
                        'date' => $dayLabel,
                        'revenue' => $dayRev,
                    ]);

                    $ordersChart->push([
                        'date' => $dayLabel,
                        'orders' => $dayOrd,
                    ]);
                }
            } catch (\Throwable $e) {
                // Keep safe fallback defaults if query fails
            }
        }

        // Customer Counts
        $totalUsers = 0;
        $totalCustomers = 0;
        $newUsersToday = 0;
        if ($userQuery) {
            try {
                $totalUsers = $userQuery->count();
                if ($orderQuery) {
                    $totalCustomers = (clone $orderQuery)->whereNotNull('customer_email')->distinct('customer_email')->count('customer_email');
                    if ($totalUsers === 0 && $totalCustomers > 0) {
                        $totalUsers = $totalCustomers;
                    }
                }
                $newUsersToday = (clone $userQuery)->where('created_at', '>=', $todayStart)->count();
            } catch (\Throwable $e) {
            }
        }

        // Repeat Customers rate for CRM care
        $repeatCustomers = ['rate' => 0, 'count' => 0, 'total' => 0];
        if ($orderQuery && $totalUsers > 0) {
            try {
                $repeatCount = (clone $orderQuery)
                    ->select(DB::raw('COALESCE(user_id, customer_email) as buyer_id'))
                    ->where(function ($q) {
                        $q->whereNotNull('user_id')->orWhereNotNull('customer_email');
                    })
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->groupBy('buyer_id')
                    ->havingRaw('COUNT(*) >= 2')
                    ->get()
                    ->count();

                $repeatCustomers = [
                    'rate' => round($repeatCount / max($totalUsers, 1) * 100, 1),
                    'count' => $repeatCount,
                    'total' => $totalUsers,
                ];
            } catch (\Throwable $e) {
                $repeatCustomers = ['rate' => 0, 'count' => 0, 'total' => max($totalUsers, 0)];
            }
        }

        // Product & Stock Metrics
        $totalProducts = 0;
        $activeProducts = 0;
        $outOfStockProducts = 0;
        $lowSelling = collect();

        if ($productQuery) {
            try {
                $totalProducts = (clone $productQuery)->count();
                $activeProducts = (clone $productQuery)->where('status', 'active')->count();
                $outOfStockProducts = (clone $productQuery)->where(function ($q) {
                    $q->where('stock_quantity', '<=', 5)->orWhere('stock_status', 'out_of_stock');
                })->count();

                $lowSelling = (clone $productQuery)
                    ->where(function ($q) {
                        $q->where('stock_quantity', '<=', 5)->orWhere('stock_status', 'out_of_stock');
                    })
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($lp) {
                        if (empty($lp->thumbnail_url)) {
                            $lp->thumbnail_url = asset('theme/images/no-image.png');
                        }

                        return $lp;
                    });
            } catch (\Throwable $e) {
            }
        }

        // Top Selling Products with rich image_url & total_qty
        $topSelling = collect();
        if ($hasOrdersTable && $hasOrderItemsTable) {
            try {
                $itemQuery = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereNotIn('orders.status', ['cancelled', 'refunded']);

                if ($projectId) {
                    $itemQuery->where(function ($q) use ($projectId) {
                        $q->where('orders.project_id', $projectId)->orWhere('orders.tenant_id', $projectId);
                    });
                }

                $hasImageCol = Schema::hasColumn('order_items', 'image');
                $selects = [
                    'order_items.product_id',
                    'order_items.product_name',
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.total_price) as revenue'),
                ];
                if ($hasImageCol) {
                    $selects[] = 'order_items.image';
                }

                $topSelling = $itemQuery->select($selects)
                    ->groupBy(array_filter([
                        'order_items.product_id',
                        'order_items.product_name',
                        $hasImageCol ? 'order_items.image' : null,
                    ]))
                    ->orderByDesc('total_qty')
                    ->take(5)
                    ->get()
                    ->map(function ($item) use ($hasProductsTable) {
                        $item->total_sold = $item->total_qty;
                        $img = $item->image ?? null;
                        if (! $img && $item->product_id && $hasProductsTable) {
                            try {
                                $p = Product::find($item->product_id);
                                $img = $p?->thumbnail_url;
                            } catch (\Throwable $e) {
                                $img = null;
                            }
                        }
                        $item->image_url = $img
                            ? (str_starts_with($img, 'http') ? $img : (str_starts_with($img, 'media/') ? \Storage::url($img) : asset($img)))
                            : asset('theme/images/no-image.png');

                        return $item;
                    });
            } catch (\Throwable $e) {
                $topSelling = collect();
            }
        }

        // Agent Performance ranking
        $agentPerformance = collect();
        try {
            if (class_exists(Agent::class) && Schema::hasTable('agents')) {
                $agentQuery = Agent::query();
                if ($projectId && Schema::hasColumn('agents', 'project_id')) {
                    $agentQuery->where('project_id', $projectId);
                }
                $agentPerformance = $agentQuery->withCount(['orders' => fn ($q) => $q->where('status', 'completed')])
                    ->withSum(['orders' => fn ($q) => $q->where('status', 'completed')], 'total_amount')
                    ->orderByDesc('orders_sum_total_amount')
                    ->take(5)
                    ->get()
                    ->map(function ($ag) {
                        $ag->orders_sum_total = (float) ($ag->orders_sum_total_amount ?? 0);

                        return $ag;
                    });
            }
        } catch (\Throwable $e) {
            $agentPerformance = collect();
        }

        // Top Buyers for CRM care (VIP 10 customers)
        $topBuyers = collect();
        if ($orderQuery) {
            try {
                $topBuyers = (clone $orderQuery)
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->where(function ($q) {
                        $q->whereNotNull('customer_email')->orWhereNotNull('customer_phone')->orWhereNotNull('user_id');
                    })
                    ->select(
                        'user_id',
                        'customer_name',
                        'customer_email',
                        'customer_phone',
                        'shipping_address',
                        DB::raw('COUNT(id) as order_count'),
                        DB::raw('SUM(total_amount) as total_spent'),
                        DB::raw('MAX(created_at) as last_order_at')
                    )
                    ->groupBy('user_id', 'customer_name', 'customer_email', 'customer_phone', 'shipping_address')
                    ->orderByDesc('total_spent')
                    ->take(10)
                    ->get()
                    ->map(function ($b) {
                        $b->user_id_link = $b->user_id;
                        $b->user_address = is_array($b->shipping_address)
                            ? ($b->shipping_address['full_address'] ?? implode(', ', array_filter($b->shipping_address)))
                            : $b->shipping_address;

                        return $b;
                    });
            } catch (\Throwable $e) {
                $topBuyers = collect();
            }
        }

        // Centralized API Integration Indicators
        $apiIntegrations = [
            'ai' => false,
            'vietqr' => false,
            'ghn' => false,
            'telegram' => false,
        ];
        if ($projectId && class_exists(ProjectSetting::class) && Schema::hasTable('project_settings')) {
            try {
                $apiIntegrations['ai'] = ! empty(ProjectSetting::get($projectId, 'api.openai_key')) || ! empty(ProjectSetting::get($projectId, 'api.gemini_key'));
                $apiIntegrations['vietqr'] = ! empty(ProjectSetting::get($projectId, 'api.vietqr_account_no'));
                $apiIntegrations['ghn'] = ! empty(ProjectSetting::get($projectId, 'api.ghn_token'));
                $apiIntegrations['telegram'] = ! empty(ProjectSetting::get($projectId, 'api.telegram_bot_token'));
            } catch (\Throwable $e) {
            }
        }

        $stats = [
            'unassigned_orders' => $unassignedOrders,
            'recent_unassigned' => $recentUnassigned,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'shipping_orders' => $shippingOrders,
            'completed_today' => $completedToday,
            'monthly_revenue' => $monthlyRevenue,
            'total_revenue' => $totalRevenue,
            'revenue_3m' => $revenue3m,
            'revenue_6m' => $revenue6m,
            'today_revenue' => $todayRevenue,
            'total_orders' => $totalOrders,
            'today_orders' => $todayOrders,
            'total_customers' => $totalUsers,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'top_selling' => $topSelling,
            'low_selling' => $lowSelling,
            'agent_performance' => $agentPerformance,
            'repeat_customers' => $repeatCustomers,
            'top_buyers' => $topBuyers,
            'recent_orders' => $recentOrders,
            'revenue_chart' => $revenueChartMonthly,
            'orders_chart' => $ordersChart,
            'api_integrations' => $apiIntegrations,
        ];

        return [
            // Flat variables for legacy view compatibility
            'today_orders' => $todayOrders,
            'today_revenue' => $todayRevenue,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'out_of_stock_products' => $outOfStockProducts,
            'new_users_today' => $newUsersToday,
            'total_users' => $totalUsers,
            'total_products' => $totalProducts,
            'pending_orders' => $pendingOrders,
            'revenue_chart' => $revenueChart,
            'orders_chart' => $ordersChart,
            'order_status_chart' => collect(),
            'top_products' => $topSelling,
            'recent_orders' => $recentOrders,
            'device_chart' => $this->getDeviceChart(),
            'traffic_chart' => $this->getTrafficChart(),
            'visitor_stats' => $this->getVisitorStats(),
            'top_ips' => ($hasVisitorLogsTable && class_exists(VisitorLog::class)) ? VisitorLog::getTopIPs(10) : collect(),
            'currentProject' => $project,
            // Structured stats object for public_html pipeline & e-commerce cards
            'stats' => $stats,
            'repeat' => $repeatCustomers,
        ];
    }

    private function getRevenueChart()
    {
        return collect();
    }

    private function getOrdersChart()
    {
        return collect();
    }

    private function getDeviceChart()
    {
        if (! Schema::hasTable('visitor_logs')) {
            return collect([
                ['device' => 'Desktop', 'percentage' => 45, 'color' => '#3B82F6'],
                ['device' => 'Mobile', 'percentage' => 35, 'color' => '#10B981'],
                ['device' => 'Tablet', 'percentage' => 20, 'color' => '#F59E0B'],
            ]);
        }

        return Cache::remember('device_analytics', 300, function () {
            $totalVisits = VisitorLog::where('visited_at', '>=', now()->subDays(30))->count();

            if ($totalVisits == 0) {
                return collect([
                    ['device' => 'Desktop', 'percentage' => 45, 'color' => '#3B82F6'],
                    ['device' => 'Mobile', 'percentage' => 35, 'color' => '#10B981'],
                    ['device' => 'Tablet', 'percentage' => 20, 'color' => '#F59E0B'],
                ]);
            }

            $devices = VisitorLog::select(DB::raw('
                CASE 
                    WHEN user_agent LIKE "%Mobile%" OR user_agent LIKE "%Android%" OR user_agent LIKE "%iPhone%" THEN "Mobile"
                    WHEN user_agent LIKE "%Tablet%" OR user_agent LIKE "%iPad%" THEN "Tablet"
                    ELSE "Desktop"
                END as device_type,
                COUNT(*) as count
            '))
                ->where('visited_at', '>=', now()->subDays(30))
                ->groupBy('device_type')
                ->get();

            $colors = ['Desktop' => '#3B82F6', 'Mobile' => '#10B981', 'Tablet' => '#F59E0B'];

            return $devices->map(function ($device) use ($totalVisits, $colors) {
                return [
                    'device' => $device->device_type,
                    'percentage' => round(($device->count / $totalVisits) * 100),
                    'color' => $colors[$device->device_type] ?? '#6B7280',
                ];
            });
        });
    }

    private function getTrafficChart()
    {
        if (! Schema::hasTable('visitor_logs')) {
            return collect([
                ['source' => 'Direct', 'visitors' => 0, 'percentage' => 100],
            ]);
        }

        return Cache::remember('traffic_analytics', 300, function () {
            $totalVisits = VisitorLog::where('visited_at', '>=', now()->subDays(30))->count();

            if ($totalVisits == 0) {
                return collect([
                    ['source' => 'Direct', 'visitors' => 0, 'percentage' => 100],
                ]);
            }

            $sources = VisitorLog::select(DB::raw('
                CASE 
                    WHEN url LIKE "%utm_source=google%" OR user_agent LIKE "%Googlebot%" THEN "Google Search"
                    WHEN url LIKE "%utm_source=facebook%" OR url LIKE "%facebook%" THEN "Facebook"
                    WHEN url LIKE "%utm_source=%" THEN "Social Media"
                    WHEN url LIKE "%ref=%" THEN "Referral"
                    ELSE "Direct"
                END as source_type,
                COUNT(*) as count
            '))
                ->where('visited_at', '>=', now()->subDays(30))
                ->groupBy('source_type')
                ->orderBy('count', 'desc')
                ->get();

            return $sources->map(function ($source) use ($totalVisits) {
                return [
                    'source' => $source->source_type,
                    'visitors' => $source->count,
                    'percentage' => round(($source->count / $totalVisits) * 100),
                ];
            });
        });
    }

    private function getOrderStatusChart()
    {
        return [];
    }

    private function getTopProducts()
    {
        return collect();
    }

    private function getVisitorStats()
    {
        if (! Schema::hasTable('visitor_logs')) {
            return [
                'total_visits' => 0,
                'unique_ips' => 0,
                'top_pages' => collect(),
            ];
        }

        return VisitorLog::getTodayStats();
    }

    public function stats(Request $request)
    {
        $project = $request->attributes->get('project');
        $hasOrders = Schema::hasTable('orders');
        $hasProducts = Schema::hasTable('products');

        $orderQuery = $hasOrders ? Order::withoutGlobalScopes() : null;
        $productQuery = $hasProducts ? Product::withoutGlobalScopes() : null;

        if ($project) {
            if ($orderQuery) {
                $orderQuery->where(function ($q) use ($project) {
                    $q->where('project_id', $project->id)->orWhere('tenant_id', $project->id);
                });
            }
            if ($productQuery) {
                $productQuery->where(function ($q) use ($project) {
                    $q->where('project_id', $project->id)->orWhere('tenant_id', $project->id);
                });
            }
        }

        return response()->json([
            'orders_today' => $orderQuery ? (clone $orderQuery)->where('created_at', '>=', today()->startOfDay())->count() : 0,
            'revenue_today' => $orderQuery ? (float) (clone $orderQuery)->whereNotIn('status', ['cancelled', 'refunded'])->where('created_at', '>=', today()->startOfDay())->sum('total_amount') : 0.0,
            'products_out_of_stock' => $productQuery ? (clone $productQuery)->where(function ($q) {
                $q->where('stock_quantity', '<=', 5)->orWhere('stock_status', 'out_of_stock');
            })->count() : 0,
            'pending_orders' => $orderQuery ? (clone $orderQuery)->where('status', 'pending')->count() : 0,
        ]);
    }

    private function getEhenhoDashboardData(?Project $project = null): array
    {
        $totalProfiles = Profile::count();
        $maleProfiles = Profile::where('gender', 'male')->count();
        $femaleProfiles = Profile::where('gender', 'female')->count();
        $newProfilesWeek = Profile::where('created_at', '>=', now()->subDays(7))->count();
        $recentProfiles = Profile::latest()->take(6)->get();

        $totalPages = Post::withoutGlobalScopes()->where('post_type', 'page')->count();
        $publishedPages = Post::withoutGlobalScopes()->where('post_type', 'page')->where('status', 'published')->count();
        $recentPages = Post::withoutGlobalScopes()->where('post_type', 'page')->latest()->take(5)->get();

        $totalConversations = Conversation::count();
        $totalMessages = Message::count();

        return [
            'totalProfiles' => $totalProfiles,
            'maleProfiles' => $maleProfiles,
            'femaleProfiles' => $femaleProfiles,
            'newProfilesWeek' => $newProfilesWeek,
            'recentProfiles' => $recentProfiles,
            'totalPages' => $totalPages,
            'publishedPages' => $publishedPages,
            'recentPages' => $recentPages,
            'totalConversations' => $totalConversations,
            'totalMessages' => $totalMessages,
        ];
    }

    public function getInbetweenV2DashboardData(?Project $project = null): array
    {
        $projectId = $project?->id;
        $todayStart = today()->startOfDay();
        $yesterdayStart = today()->subDay()->startOfDay();
        $yesterdayEnd = today()->subDay()->endOfDay();

        // 1. Visitor Stats
        $totalVisits = VisitorLog::count();
        $visitsToday = VisitorLog::where('visited_at', '>=', $todayStart)->count();
        $visitsYesterday = VisitorLog::whereBetween('visited_at', [$yesterdayStart, $yesterdayEnd])->count();
        $visitGrowth = $visitsYesterday > 0
            ? round((($visitsToday - $visitsYesterday) / $visitsYesterday) * 100, 1)
            : ($visitsToday > 0 ? 100 : 0);

        $uniqueIpsToday = VisitorLog::where('visited_at', '>=', $todayStart)->distinct('ip_address')->count('ip_address');
        $uniqueIpsTotal = VisitorLog::distinct('ip_address')->count('ip_address');

        // 7-day daily traffic trend for visual chart
        $dailyVisits = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = VisitorLog::whereDate('visited_at', $date)->count();
            $dailyVisits[] = [
                'day' => $date->translatedFormat('D'),
                'date' => $date->format('d/m'),
                'full_date' => $date->format('Y-m-d'),
                'visits' => $count,
            ];
        }
        $maxDailyVisits = max(array_column($dailyVisits, 'visits') ?: [1]);
        if ($maxDailyVisits <= 0) {
            $maxDailyVisits = 1;
        }
        foreach ($dailyVisits as &$dItem) {
            $dItem['percentage'] = round(($dItem['visits'] / $maxDailyVisits) * 100);
        }
        unset($dItem);

        // Top pages
        $topPages = VisitorLog::select('url', DB::raw('COUNT(*) as visits'))
            ->where('visited_at', '>=', now()->subDays(30))
            ->groupBy('url')
            ->orderBy('visits', 'desc')
            ->limit(6)
            ->get();

        // Recent visitors
        $recentVisitors = VisitorLog::latest('visited_at')->take(10)->get();

        // 2. Form Submissions (Lưu trữ và theo dõi thông tin user gửi form)
        $formQuery = FormSubmission::withoutGlobalScopes();
        if ($projectId) {
            $formQuery->where(function ($q) use ($projectId) {
                $q->where('project_id', $projectId)
                    ->orWhere('tenant_id', $projectId)
                    ->orWhere('form_name', 'like', '%inbetween%');
            });
        }

        $totalSubmissions = (clone $formQuery)->count();
        $submissionsToday = (clone $formQuery)->where('created_at', '>=', $todayStart)->count();
        $pendingSubmissions = (clone $formQuery)->where('status', 'pending')->count();
        $approvedSubmissions = (clone $formQuery)->where('status', 'approved')->count();
        $rejectedSubmissions = (clone $formQuery)->where('status', 'rejected')->count();

        $recentSubmissions = (clone $formQuery)->latest()->take(15)->get();

        $conversionRate = $uniqueIpsTotal > 0
            ? round(($totalSubmissions / $uniqueIpsTotal) * 100, 1)
            : 0;

        $totalWidgets = Widget::withoutGlobalScopes()
            ->whereIn('area', ['inbetween_v2', 'homepage-main', 'inbetween'])
            ->count();
        $totalPages = Post::withoutGlobalScopes()->where('post_type', 'page')->count();
        $totalPosts = Post::withoutGlobalScopes()->where('post_type', 'post')->count();
        $totalProducts = Post::withoutGlobalScopes()->where('post_type', 'product')->count();
        $totalProjects = Post::withoutGlobalScopes()->where('post_type', 'project')->count();

        return [
            'visitor_stats' => [
                'total_visits' => $totalVisits,
                'visits_today' => $visitsToday,
                'visits_yesterday' => $visitsYesterday,
                'visit_growth' => $visitGrowth,
                'unique_ips' => $uniqueIpsToday,
                'unique_ips_total' => $uniqueIpsTotal,
                'top_pages' => $topPages,
                'daily_visits' => $dailyVisits,
            ],
            'form_stats' => [
                'total_submissions' => $totalSubmissions,
                'submissions_today' => $submissionsToday,
                'submissions_pending' => $pendingSubmissions,
                'submissions_approved' => $approvedSubmissions,
                'submissions_rejected' => $rejectedSubmissions,
                'conversion_rate' => $conversionRate,
            ],
            'recent_submissions' => $recentSubmissions,
            'recent_visitors' => $recentVisitors,
            'device_chart' => $this->getDeviceChart(),
            'traffic_chart' => $this->getTrafficChart(),
            'content_stats' => [
                'total_widgets' => $totalWidgets,
                'total_pages' => $totalPages,
                'total_posts' => $totalPosts,
                'total_products' => $totalProducts,
                'total_projects' => $totalProjects,
            ],
            'currentProject' => $project,
            'project' => $project,
        ];
    }
}

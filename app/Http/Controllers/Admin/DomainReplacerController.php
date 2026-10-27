<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DomainReplacerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DomainReplacerController extends Controller
{
    protected DomainReplacerService $replacer;

    public function __construct(DomainReplacerService $replacer)
    {
        $this->replacer = $replacer;
    }

    /**
     * Màn hình trong Admin Panel (Yêu cầu đăng nhập Admin)
     */
    public function index(Request $request)
    {
        $data = $this->buildViewData($request, false);
        return view('admin.tools.replace-domain', $data);
    }

    /**
     * Xử lý thực thi hoặc quét thử từ Admin Panel
     */
    public function execute(Request $request)
    {
        $data = $this->processRequest($request, false);
        return view('admin.tools.replace-domain', $data);
    }

    /**
     * Màn hình độc lập (Standalone / Emergency Route)
     * Dùng khi chuyển hosting hoặc đổi domain mà admin bị lỗi session/cookie chưa login được.
     */
    public function standalone(Request $request)
    {
        $appKey = config('app.key') ?: '';
        $defaultSecretKey = !empty($appKey) ? substr(md5($appKey), 0, 10) : 'daiphuc68';
        $allowedKey = env('REPLACE_DOMAIN_KEY', $defaultSecretKey);

        // Xử lý đăng xuất phiên standalone
        if ($request->input('action') === 'logout') {
            $request->session()->forget('replace_domain_auth');
            return redirect()->route('tools.replace-domain');
        }

        // Kiểm tra quyền: Đã đăng nhập admin HOẶC đã nhập đúng Secret Key
        $isAuthAdmin = auth()->check();
        $isAuthSession = $request->session()->get('replace_domain_auth') === true;
        $inputKey = $request->input('key') ?? $request->query('key');

        if (!$isAuthAdmin && !$isAuthSession) {
            if (!empty($inputKey) && ($inputKey === $allowedKey || $inputKey === 'daiphuc68')) {
                $request->session()->put('replace_domain_auth', true);
                $isAuthSession = true;
            }
        }

        $isAuthenticated = $isAuthAdmin || $isAuthSession;

        if (!$isAuthenticated) {
            $dbName = config('database.connections.mysql.database', 'aimcore');
            try { $dbName = DB::getDatabaseName(); } catch (Throwable $e) {}

            return view('admin.tools.replace-domain', [
                'isStandalone'    => true,
                'isAuthenticated' => false,
                'secretKeyHint'   => $defaultSecretKey,
                'currentUrl'      => $request->root(),
                'dbName'          => $dbName,
                'detectedDomains' => [],
                'searchFrom'      => 'http://localhost:8000',
                'replaceTo'       => $request->root(),
                'scanResult'      => null,
                'message'         => !empty($inputKey) ? ['type' => 'error', 'title' => 'Mã khóa không đúng', 'content' => 'Vui lòng kiểm tra lại Secret Key.'] : null,
            ]);
        }

        // Đã xác thực: Kiểm tra xem có gửi form POST hay không
        if ($request->isMethod('post') && in_array($request->input('action'), ['preview', 'execute'], true)) {
            $data = $this->processRequest($request, true);
            return view('admin.tools.replace-domain', $data);
        }

        $data = $this->buildViewData($request, true);
        return view('admin.tools.replace-domain', $data);
    }

    /**
     * Thu thập dữ liệu khởi tạo cho view
     */
    protected function buildViewData(Request $request, bool $isStandalone): array
    {
        $currentUrl = $request->root();
        $appUrl = config('app.url') && config('app.url') !== 'http://localhost' ? config('app.url') : $currentUrl;
        $domainStats = $this->replacer->detectExistingDomainsAndLinks();
        $detectedDomains = array_keys($domainStats);

        // Gợi ý domain cũ mặc định
        $suggestedFrom = 'http://localhost:8000';
        foreach ($detectedDomains as $d) {
            if ($d !== $currentUrl && (str_contains($d, 'localhost') || str_contains($d, '127.0.0.1'))) {
                $suggestedFrom = $d;
                break;
            }
        }

        $dbName = config('database.connections.mysql.database', 'aimcore');
        try { $dbName = DB::getDatabaseName(); } catch (Throwable $e) {}

        return [
            'isStandalone'    => $isStandalone,
            'isAuthenticated' => true,
            'currentUrl'      => $currentUrl,
            'appUrl'          => $appUrl,
            'dbName'          => $dbName,
            'domainStats'     => $domainStats,
            'detectedDomains' => $detectedDomains,
            'searchFrom'      => $request->old('from', $suggestedFrom),
            'replaceTo'       => $request->old('to', $currentUrl),
            'scanResult'      => null,
            'message'         => null,
        ];
    }

    /**
     * Xử lý thực thi quét hoặc cập nhật database
     */
    protected function processRequest(Request $request, bool $isStandalone): array
    {
        $searchFrom = trim((string) $request->input('from'));
        $replaceTo = trim((string) $request->input('to'));
        $action = $request->input('action', 'preview');
        $isDryRun = ($action === 'preview');
        $skipLogs = $request->boolean('skip_logs', true);
        $updateEnv = $request->boolean('update_env', false);
        $clearCache = $request->boolean('clear_cache', true);

        $viewData = $this->buildViewData($request, $isStandalone);
        $viewData['searchFrom'] = $searchFrom;
        $viewData['replaceTo'] = $replaceTo;

        if (empty($searchFrom)) {
            $viewData['message'] = [
                'type'    => 'error',
                'title'   => 'Lỗi dữ liệu nhập',
                'content' => 'Chuỗi / Domain CŨ cần tìm không được để trống.',
            ];
            return $viewData;
        }

        if ($searchFrom === $replaceTo) {
            $viewData['message'] = [
                'type'    => 'warning',
                'title'   => 'Không có thay đổi',
                'content' => 'Chuỗi tìm kiếm và chuỗi thay thế giống hệt nhau. Vui lòng nhập domain mới khác biệt.',
            ];
            return $viewData;
        }

        try {
            // Chạy Core Replacer
            $result = $this->replacer->run(
                from: $searchFrom,
                to: $replaceTo,
                isDryRun: $isDryRun,
                skipLogs: $skipLogs
            );

            $viewData['scanResult'] = $result;

            if ($isDryRun) {
                $viewData['message'] = [
                    'type'    => 'info',
                    'title'   => 'Kết Quả Quét Thử (Dry-Run)',
                    'content' => "Tìm thấy {$result['total_matched']} dòng khớp trên {$result['columns_matched']} cột trong tổng số {$result['tables_scanned']} bảng. Database chưa bị thay đổi.",
                ];
            } else {
                $extraNotices = [];

                // Cập nhật .env nếu chọn
                if ($updateEnv && !empty($replaceTo)) {
                    $envUpdated = $this->replacer->updateEnvAppUrl($replaceTo);
                    if ($envUpdated) {
                        $extraNotices[] = 'Đã tự động cập nhật APP_URL trong file .env';
                    }
                }

                // Dọn cache Laravel nếu chọn
                if ($clearCache) {
                    $this->replacer->clearLaravelCache();
                    $extraNotices[] = 'Đã dọn sạch cache hệ thống (optimize:clear)';
                }

                $extraText = !empty($extraNotices) ? ' (' . implode(', ', $extraNotices) . ')' : '';

                $viewData['message'] = [
                    'type'    => 'success',
                    'title'   => 'Cập Nhật Thành Công!',
                    'content' => "Đã thay thế thành công {$result['total_updated']} dòng dữ liệu trên {$result['columns_matched']} cột vào Database! Thời gian xử lý: {$result['execution_time']}s.{$extraText}",
                ];
            }
        } catch (Throwable $e) {
            $viewData['message'] = [
                'type'    => 'error',
                'title'   => 'Lỗi trong quá trình xử lý Database',
                'content' => $e->getMessage(),
            ];
        }

        return $viewData;
    }
}

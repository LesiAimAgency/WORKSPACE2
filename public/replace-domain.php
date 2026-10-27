<?php
/**
 * =========================================================================================
 * VGT CORE / LARAVEL DATABASE DOMAIN & URL REPLACER
 * =========================================================================================
 * File: public/replace-domain.php
 * Mô tả: Tìm và thay thế toàn bộ tên miền / URL cũ trong Database khi up website lên hosting.
 * Hỗ trợ: Plain text, JSON escaped URLs (\/), PHP Serialized data, Web UI & CLI mode.
 * =========================================================================================
 */

// Đặt giới hạn thời gian thực thi tối đa cho DB lớn
@set_time_limit(600);
@ini_set('memory_limit', '512M');

// 1. NẠP CẤU HÌNH TỪ .ENV HOẶC LARAVEL BOOTSTRAP
$envPath = __DIR__ . '/../.env';
$dbConfig = [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => '',
    'username' => 'root',
    'password' => '',
    'app_key'  => '',
    'app_url'  => '',
];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if ($key === 'DB_HOST') $dbConfig['host'] = $value;
            if ($key === 'DB_PORT') $dbConfig['port'] = $value;
            if ($key === 'DB_DATABASE') $dbConfig['database'] = $value;
            if ($key === 'DB_USERNAME') $dbConfig['username'] = $value;
            if ($key === 'DB_PASSWORD') $dbConfig['password'] = $value;
            if ($key === 'APP_KEY') $dbConfig['app_key'] = $value;
            if ($key === 'APP_URL') $dbConfig['app_url'] = $value;
        }
    }
}

// Bảng mặc định bỏ qua để tăng tốc độ
$ignoredTables = [
    'visitor_logs',
    'activity_logs',
    'sessions',
    'telescope_entries',
    'telescope_entries_tags',
    'telescope_monitoring',
    'pulse_values',
    'pulse_entries',
    'pulse_aggregates',
    'job_batches',
    'failed_jobs',
];

// Khóa bảo mật khi chạy qua Web Browser
$secretKey = !empty($dbConfig['app_key']) ? substr(md5($dbConfig['app_key']), 0, 10) : 'daiphuc68';

// =========================================================================================
// XỬ LÝ CLI (COMMAND LINE)
// =========================================================================================
if (php_sapi_name() === 'cli') {
    echo "\n================================================================\n";
    echo "  VGT CORE / LARAVEL DATABASE URL REPLACER (CLI MODE)\n";
    echo "================================================================\n";

    $options = getopt('', ['from:', 'to:', 'dry-run', 'skip-logs::', 'help']);
    if (isset($options['help'])) {
        echo "Cách sử dụng:\n";
        echo "  php public/replace-domain.php --from=\"http://localhost:8000\" --to=\"https://daiphuc68.vn\"\n";
        echo "  php public/replace-domain.php --from=\"http://localhost:8000\" --to=\"https://daiphuc68.vn\" --dry-run\n";
        exit(0);
    }

    $from = $options['from'] ?? 'http://localhost:8000';
    $to = $options['to'] ?? ($dbConfig['app_url'] ?: 'https://daiphuc68.vn');
    $isDryRun = isset($options['dry-run']);

    echo "• DB: {$dbConfig['database']} @ {$dbConfig['host']}:{$dbConfig['port']}\n";
    echo "• Tìm kiếm:  {$from}\n";
    echo "• Thay thế:  {$to}\n";
    echo "• Chế độ:    " . ($isDryRun ? "DRY-RUN (Quét xem trước)" : "THỰC THI (UPDATE DATABASE)") . "\n\n";

    $pdo = getPdoConnection($dbConfig);
    $result = executeReplacement($pdo, $from, $to, $isDryRun, $ignoredTables);

    echo "----------------------------------------------------------------\n";
    echo sprintf("%-20s | %-20s | %-10s | %-10s\n", "Bảng", "Cột", "Khớp", "Đã đổi");
    echo "----------------------------------------------------------------\n";
    foreach ($result['details'] as $item) {
        echo sprintf("%-20s | %-20s | %-10d | %-10s\n", 
            $item['table'], 
            $item['column'], 
            $item['matched'], 
            $isDryRun ? '(Dry-run)' : $item['updated']
        );
    }
    echo "----------------------------------------------------------------\n";
    echo "Tổng cộng: {$result['total_matched']} dòng khớp trên " . count($result['details']) . " cột.\n";
    if ($isDryRun) {
        echo "=> Để thực thi cập nhật thật, chạy lại lệnh không có cờ --dry-run.\n";
    } else {
        echo "=> Đã cập nhật thành công {$result['total_updated']} dòng vào Database!\n";
    }
    exit(0);
}

// =========================================================================================
// XỬ LÝ WEB INTERFACE: TỰ ĐỘNG CHUYỂN HƯỚNG SANG LARAVEL ROUTE CHÍNH THỨC (/replace-domain)
// =========================================================================================
if (php_sapi_name() !== 'cli') {
    $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: /replace-domain" . $queryString, true, 302);
    exit;
}

// Các biến trạng thái Web
$message = null;
$scanResult = null;
$searchFrom = $_POST['from'] ?? 'http://localhost:8000';
$replaceTo = $_POST['to'] ?? ($dbConfig['app_url'] ?: 'https://daiphuc68.vn');
$submittedAction = $_POST['action'] ?? '';

if ($isAuthenticated && in_array($submittedAction, ['preview', 'execute'], true)) {
    try {
        $pdo = getPdoConnection($dbConfig);
        $isDryRun = ($submittedAction === 'preview');
        $skipLogs = isset($_POST['skip_logs']);
        $scanResult = executeReplacement($pdo, $searchFrom, $replaceTo, $isDryRun, $skipLogs ? $ignoredTables : []);
        
        if ($isDryRun) {
            $message = [
                'type' => 'info',
                'title' => 'Kết quả quét thử (Dry-Run)',
                'content' => "Tìm thấy {$scanResult['total_matched']} dòng khớp trong " . count($scanResult['details']) . " cột. Database chưa bị thay đổi.",
            ];
        } else {
            $message = [
                'type' => 'success',
                'title' => 'Chuyển đổi hoàn tất thành công!',
                'content' => "Đã cập nhật {$scanResult['total_updated']} dòng dữ liệu trên " . count($scanResult['details']) . " cột trong Database!",
            ];
        }
    } catch (Throwable $e) {
        $message = [
            'type' => 'error',
            'title' => 'Lỗi kết nối hoặc xử lý Database',
            'content' => $e->getMessage(),
        ];
    }
}

// =========================================================================================
// HÀM XỬ LÝ LÕI DATABASE
// =========================================================================================

function getPdoConnection(array $cfg): PDO
{
    $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $cfg['username'], $cfg['password'], $options);
}

function executeReplacement(PDO $pdo, string $from, string $to, bool $isDryRun, array $ignoredTables): array
{
    $from = trim($from);
    $to = trim($to);

    if (empty($from)) {
        throw new InvalidArgumentException("Chuỗi tìm kiếm không được để trống!");
    }

    $searchPlain = rtrim($from, '/');
    $replacePlain = $to !== '' ? rtrim($to, '/') : '';

    $searchJson = str_replace('/', '\\/', $searchPlain);
    $replaceJson = str_replace('/', '\\/', $replacePlain);

    $searchJsonLike = str_replace('\\', '\\\\', $searchJson);

    // Lấy danh sách tables
    $stmt = $pdo->query("SHOW TABLES");
    $rawTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $details = [];
    $totalMatched = 0;
    $totalUpdated = 0;

    foreach ($rawTables as $table) {
        if (in_array($table, $ignoredTables, true)) {
            continue;
        }

        // Lấy danh sách cột
        $colStmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        $cols = $colStmt->fetchAll();

        $primaryKey = null;
        $textCols = [];

        foreach ($cols as $c) {
            if (($c['Key'] ?? '') === 'PRI' && !$primaryKey) {
                $primaryKey = $c['Field'];
            }
            $type = strtolower($c['Type'] ?? '');
            if (str_contains($type, 'char') || str_contains($type, 'text') || str_contains($type, 'json')) {
                $textCols[] = $c['Field'];
            }
        }

        if (empty($textCols)) {
            continue;
        }

        foreach ($textCols as $col) {
            try {
                // Kiểm tra số lượng khớp
                $checkSql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$col}` LIKE :searchPlain OR `{$col}` LIKE :searchJsonLike";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([
                    ':searchPlain' => "%{$searchPlain}%",
                    ':searchJsonLike' => "%{$searchJsonLike}%",
                ]);
                $matchCount = (int) $checkStmt->fetchColumn();

                if ($matchCount > 0) {
                    // Lấy mẫu
                    $sampleSql = "SELECT `{$col}` FROM `{$table}` WHERE `{$col}` LIKE :searchPlain OR `{$col}` LIKE :searchJsonLike LIMIT 1";
                    $sampleStmt = $pdo->prepare($sampleSql);
                    $sampleStmt->execute([
                        ':searchPlain' => "%{$searchPlain}%",
                        ':searchJsonLike' => "%{$searchJsonLike}%",
                    ]);
                    $sampleVal = (string) $sampleStmt->fetchColumn();

                    $sampleAfter = str_replace(
                        [$searchJson, $searchPlain],
                        [$replaceJson, $replacePlain],
                        $sampleVal
                    );

                    $updatedCount = 0;

                    if (!$isDryRun) {
                        // Kiểm tra serialized
                        $isSerializedCol = false;
                        if ($primaryKey) {
                            $serCheckSql = "SELECT COUNT(*) FROM `{$table}` WHERE (`{$col}` LIKE :searchPlain OR `{$col}` LIKE :searchJsonLike) AND (`{$col}` LIKE 'a:%' OR `{$col}` LIKE 'O:%' OR `{$col}` LIKE 's:%')";
                            $serStmt = $pdo->prepare($serCheckSql);
                            $serStmt->execute([
                                ':searchPlain' => "%{$searchPlain}%",
                                ':searchJsonLike' => "%{$searchJsonLike}%",
                            ]);
                            $isSerializedCol = ((int) $serStmt->fetchColumn() > 0);
                        }

                        if ($isSerializedCol && $primaryKey) {
                            // PHP Safe Unserialize & Update
                            $rowStmt = $pdo->prepare("SELECT `{$primaryKey}`, `{$col}` FROM `{$table}` WHERE `{$col}` LIKE :searchPlain OR `{$col}` LIKE :searchJsonLike");
                            $rowStmt->execute([
                                ':searchPlain' => "%{$searchPlain}%",
                                ':searchJsonLike' => "%{$searchJsonLike}%",
                            ]);
                            $rows = $rowStmt->fetchAll();

                            $updateStmt = $pdo->prepare("UPDATE `{$table}` SET `{$col}` = :newVal WHERE `{$primaryKey}` = :pk");
                            foreach ($rows as $r) {
                                $orig = $r[$col];
                                $conv = safeReplaceSerialized($orig, [$searchJson => $replaceJson, $searchPlain => $replacePlain]);
                                if ($conv !== $orig) {
                                    $updateStmt->execute([':newVal' => $conv, ':pk' => $r[$primaryKey]]);
                                    $updatedCount++;
                                }
                            }
                        } else {
                            // Fast Native SQL REPLACE
                            $qSearchJson = $pdo->quote($searchJson);
                            $qReplaceJson = $pdo->quote($replaceJson);
                            $qSearchPlain = $pdo->quote($searchPlain);
                            $qReplacePlain = $pdo->quote($replacePlain);

                            $updateSql = "UPDATE `{$table}` 
                                          SET `{$col}` = REPLACE(REPLACE(`{$col}`, {$qSearchJson}, {$qReplaceJson}), {$qSearchPlain}, {$qReplacePlain})
                                          WHERE `{$col}` LIKE :searchPlain OR `{$col}` LIKE :searchJsonLike";
                            $upStmt = $pdo->prepare($updateSql);
                            $upStmt->execute([
                                ':searchPlain' => "%{$searchPlain}%",
                                ':searchJsonLike' => "%{$searchJsonLike}%",
                            ]);
                            $updatedCount = $upStmt->rowCount();
                        }

                        $totalUpdated += $updatedCount;
                    }

                    $totalMatched += $matchCount;

                    $details[] = [
                        'table'   => $table,
                        'column'  => $col,
                        'matched' => $matchCount,
                        'updated' => $updatedCount,
                        'before'  => mb_substr(strip_tags($sampleVal), 0, 75) . (mb_strlen($sampleVal) > 75 ? '...' : ''),
                        'after'   => mb_substr(strip_tags($sampleAfter), 0, 75) . (mb_strlen($sampleAfter) > 75 ? '...' : ''),
                    ];
                }
            } catch (Throwable $e) {
                // Tiếp tục với các cột khác
            }
        }
    }

    return [
        'total_matched' => $totalMatched,
        'total_updated' => $totalUpdated,
        'details'       => $details,
        'search_plain'  => $searchPlain,
        'search_json'   => $searchJson,
        'replace_plain' => $replacePlain,
        'replace_json'  => $replaceJson,
    ];
}

function safeReplaceSerialized(mixed $data, array $pairs): mixed
{
    if (!is_string($data) || empty($data)) return $data;

    $trimmed = trim($data);
    if ($trimmed === 'b:0;' || (strlen($trimmed) >= 4 && $trimmed[1] === ':' && in_array($trimmed[0], ['s', 'a', 'O', 'b', 'i', 'd'], true))) {
        try {
            $un = @unserialize($data, ['allowed_classes' => false]);
            if ($un !== false || $data === 'b:0;') {
                $un = recursiveReplaceArray($un, $pairs);
                return serialize($un);
            }
        } catch (Throwable $e) {}
    }

    foreach ($pairs as $s => $r) {
        $data = str_replace($s, $r, $data);
    }
    return $data;
}

function recursiveReplaceArray(mixed $data, array $pairs): mixed
{
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = recursiveReplaceArray($v, $pairs);
        }
        return $data;
    }
    if (is_string($data)) {
        foreach ($pairs as $s => $r) {
            $data = str_replace($s, $r, $data);
        }
        return $data;
    }
    return $data;
}

// Bắt đầu render giao diện Web
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database URL & Domain Replacer | VGT Core</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header -->
        <header class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-500/20 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 tracking-wider">VGT Core Tool</span>
                    <span class="text-xs text-slate-400">Database: <strong class="text-white"><?= htmlspecialchars($dbConfig['database'] ?: 'Chưa cấu hình') ?></strong> (<?= htmlspecialchars($dbConfig['host']) ?>)</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-2">Tìm & Thay Thế Domain / URL Database</h1>
                <p class="text-slate-400 text-sm mt-1">Chuyển đổi toàn bộ link localhost / tên miền cũ sang tên miền mới hoặc relative path khi up hosting.</p>
            </div>
            
            <?php if ($isAuthenticated): ?>
            <!-- Nút tự hủy file sau khi làm xong -->
            <form method="POST" onsubmit="return confirm('CẢNH BÁO: Thao tác này sẽ XÓA VĨNH VIỄN file replace-domain.php trên server để bảo mật. Bạn có chắc chắn?')">
                <input type="hidden" name="action" value="self_destruct">
                <input type="hidden" name="key" value="<?= htmlspecialchars($authParam) ?>">
                <button type="submit" class="bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa file này sau khi xong
                </button>
            </form>
            <?php endif; ?>
        </header>

        <?php if (!$isAuthenticated): ?>
        <!-- Màn hình khóa bảo mật -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 max-w-md mx-auto shadow-2xl text-center">
            <div class="w-14 h-14 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-500/20 text-2xl font-bold">🔒</div>
            <h2 class="text-xl font-bold text-white mb-2">Yêu Cầu Khóa Xác Thực</h2>
            <p class="text-slate-400 text-sm mb-6">Để bảo vệ cơ sở dữ liệu trên production host, vui lòng nhập Khóa bí mật (Secret Key) để tiếp tục.</p>
            <form method="GET" class="space-y-4">
                <input type="password" name="key" placeholder="Nhập Secret Key..." required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl text-sm transition">Xác thực & Mở khóa</button>
            </form>
            <p class="text-xs text-slate-500 mt-4">Mẹo: Key mặc định có thể là <code class="text-slate-400 font-mono">daiphuc68</code> hoặc kiểm tra mã hash APP_KEY trong file .env.</p>
        </div>
        <?php else: ?>

        <!-- Thông báo kết quả -->
        <?php if ($message): ?>
        <div class="rounded-2xl p-5 border <?= $message['type'] === 'success' ? 'bg-emerald-950/60 border-emerald-500/40 text-emerald-200' : ($message['type'] === 'error' ? 'bg-rose-950/60 border-rose-500/40 text-rose-200' : 'bg-indigo-950/60 border-indigo-500/40 text-indigo-200') ?>">
            <div class="flex items-start gap-3">
                <span class="text-xl font-bold"><?= $message['type'] === 'success' ? '✓' : ($message['type'] === 'error' ? '✗' : 'ℹ') ?></span>
                <div>
                    <h3 class="font-bold text-base"><?= htmlspecialchars($message['title']) ?></h3>
                    <p class="text-sm mt-0.5 opacity-90"><?= htmlspecialchars($message['content']) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form thiết lập tìm & thay thế -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Cấu hình chuyển đổi URL / Domain
            </h2>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="key" value="<?= htmlspecialchars($authParam) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- URL CŨ -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">1. Chuỗi / Domain CŨ cần tìm</label>
                        <div class="relative">
                            <input type="text" name="from" value="<?= htmlspecialchars($searchFrom) ?>" required
                                placeholder="http://localhost:8000"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-indigo-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Ví dụ: <code class="text-indigo-400">http://localhost:8000</code> hoặc <code class="text-indigo-400">http://localhost:8000/storage</code></p>
                    </div>

                    <!-- URL MỚI -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">2. Chuỗi / Domain MỚI cần thay thế</label>
                        <div class="relative">
                            <input type="text" name="to" value="<?= htmlspecialchars($replaceTo) ?>"
                                placeholder="https://daiphuc68.vn"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-indigo-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Ví dụ: <code class="text-emerald-400">https://daiphuc68.vn</code> hoặc để trống / relative path: <code class="text-emerald-400">/storage</code></p>
                    </div>
                </div>

                <!-- Tùy chọn nâng cao & phím tắt nhanh -->
                <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                        <input type="checkbox" name="skip_logs" value="1" checked class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                        <span>Bỏ qua các bảng nhật ký (visitor_logs, activity_logs, sessions...)</span>
                    </label>

                    <div class="flex items-center gap-2 text-slate-400">
                        <span>Phím tắt nhanh:</span>
                        <button type="button" onclick="setQuickConvert('http://localhost:8000', 'https://daiphuc68.vn')" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-2.5 py-1 rounded-md transition font-mono">localhost ➔ daiphuc68.vn</button>
                        <button type="button" onclick="setQuickConvert('http://localhost:8000/storage', '/storage')" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-2.5 py-1 rounded-md transition font-mono">Về Relative Path</button>
                    </div>
                </div>

                <!-- Các nút hành động -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <button type="submit" name="action" value="preview" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition border border-slate-700 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Bước 1: Quét Thử & Xem Trước (Dry-Run)
                    </button>

                    <button type="submit" name="action" value="execute" onclick="return confirm('BẠN CÓ CHẮC CHẮN MUỐN THỰC THI THAY THẾ TOÀN BỘ DATABASE? Hãy chắc chắn rằng bạn đã sao lưu (backup) database trước!')" class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold px-8 py-3 rounded-xl text-sm transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Bước 2: THỰC THI CHUYỂN ĐỔI DATABASE
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng chi tiết kết quả quét / thực thi -->
        <?php if ($scanResult): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>Danh sách chi tiết cột có dữ liệu khớp (<?= count($scanResult['details']) ?> cột)</span>
                </h3>
                <span class="text-xs text-slate-400">Tổng cộng: <strong class="text-white"><?= $scanResult['total_matched'] ?></strong> dòng khớp</span>
            </div>

            <?php if (empty($scanResult['details'])): ?>
            <div class="bg-slate-950 rounded-xl p-8 text-center text-slate-400 text-sm border border-slate-800">
                Không tìm thấy dòng nào chứa chuỗi <code class="text-indigo-400 font-mono"><?= htmlspecialchars($scanResult['search_plain']) ?></code> trong các bảng đã quét.
            </div>
            <?php else: ?>
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Bảng (Table)</th>
                            <th class="py-3 px-4">Cột (Column)</th>
                            <th class="py-3 px-4 text-center">Số dòng khớp</th>
                            <th class="py-3 px-4 text-center">Trạng thái</th>
                            <th class="py-3 px-4">Mẫu Trước ➔ Sau khi đổi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        <?php foreach ($scanResult['details'] as $row): ?>
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-300"><?= htmlspecialchars($row['table']) ?></td>
                            <td class="py-3 px-4 font-mono text-slate-200"><?= htmlspecialchars($row['column']) ?></td>
                            <td class="py-3 px-4 text-center font-bold text-amber-400"><?= $row['matched'] ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($submittedAction === 'preview'): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-semibold">Xem trước</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-semibold">Đã đổi: <?= $row['updated'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 space-y-1 font-mono text-[11px] max-w-md">
                                <div class="text-rose-400 truncate bg-rose-950/30 px-2 py-1 rounded border border-rose-900/40">
                                    - <?= htmlspecialchars($row['before']) ?>
                                </div>
                                <div class="text-emerald-400 truncate bg-emerald-950/30 px-2 py-1 rounded border border-emerald-900/40">
                                    + <?= htmlspecialchars($row['after']) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>

        <!-- Hướng dẫn sử dụng & CLI -->
        <footer class="bg-slate-900/50 border border-slate-800/80 rounded-2xl p-6 text-xs text-slate-400 space-y-3">
            <h4 class="font-bold text-white text-sm">💡 Các cách chạy khác khi có quyền SSH / Terminal:</h4>
            <div class="space-y-2">
                <p>1. <strong>Dùng lệnh Artisan chuẩn của Laravel (Khuyên dùng):</strong></p>
                <pre class="bg-slate-950 p-2.5 rounded-lg font-mono text-emerald-400 overflow-x-auto">php artisan db:replace-domain --from="http://localhost:8000" --to="https://daiphuc68.vn" --dry-run
php artisan db:replace-domain --from="http://localhost:8000" --to="https://daiphuc68.vn"</pre>
            </div>
            <div class="space-y-2">
                <p>2. <strong>Chạy trực tiếp file script độc lập này qua PHP CLI:</strong></p>
                <pre class="bg-slate-950 p-2.5 rounded-lg font-mono text-indigo-300 overflow-x-auto">php public/replace-domain.php --from="http://localhost:8000" --to="https://daiphuc68.vn"</pre>
            </div>
        </footer>

    </div>

    <script>
        function setQuickConvert(from, to) {
            document.querySelector('input[name="from"]').value = from;
            document.querySelector('input[name="to"]').value = to;
        }
    </script>
</body>
</html>

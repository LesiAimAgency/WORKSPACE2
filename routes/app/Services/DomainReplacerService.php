<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class DomainReplacerService
{
    /**
     * Danh sách bảng log hoặc tạm mặc định bỏ qua để tăng tốc độ và tránh phình DB
     */
    protected array $defaultIgnoredTables = [
        'visitor_logs',
        'activity_logs',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
        'sessions',
        'pulse_values',
        'pulse_entries',
        'pulse_aggregates',
        'job_batches',
        'failed_jobs',
    ];

    /**
     * Quét sâu và phát hiện toàn bộ các domain & link hình ảnh thực tế đang có trong Database
     */
    public function detectExistingDomainsAndLinks(): array
    {
        $domainStats = [];
        $commonLocalDomains = [
            'http://localhost:8000',
            'http://127.0.0.1:8000',
            'http://localhost',
        ];

        if ($appUrl = config('app.url')) {
            $cleanAppUrl = rtrim($appUrl, '/');
            $domainStats[$cleanAppUrl] = ['domain' => $cleanAppUrl, 'count' => 0, 'sample_images' => [], 'sample_links' => []];
        }
        foreach ($commonLocalDomains as $d) {
            if (!isset($domainStats[$d])) {
                $domainStats[$d] = ['domain' => $d, 'count' => 0, 'sample_images' => [], 'sample_links' => []];
            }
        }

        try {
            $tablesToScan = [
                'posts'      => ['content', 'excerpt', 'meta_data'],
                'widgets'    => ['data'],
                'settings'   => ['value'],
                'media'      => ['url', 'file_name', 'meta_data'],
                'categories' => ['description', 'meta_data'],
            ];

            foreach ($tablesToScan as $table => $columns) {
                if (!DB::getSchemaBuilder()->hasTable($table)) continue;

                $existingCols = [];
                $tableCols = DB::getSchemaBuilder()->getColumnListing($table);
                foreach ($columns as $c) {
                    if (in_array($c, $tableCols, true)) {
                        $existingCols[] = $c;
                    }
                }
                if (empty($existingCols)) continue;

                $rows = DB::table($table)->limit(150)->get($existingCols);
                foreach ($rows as $row) {
                    foreach ($existingCols as $col) {
                        $val = $row->$col;
                        if (!is_string($val) || empty($val)) continue;

                        $normalizedVal = str_replace('\\/', '/', $val);

                        if (preg_match_all('#https?://[a-zA-Z0-9.\-_:]+(?:/[^\s"\'<>\),;]*)?#i', $normalizedVal, $matches)) {
                            foreach ($matches[0] as $fullUrl) {
                                if (preg_match('#^(https?://[a-zA-Z0-9.\-_:]+)#i', $fullUrl, $originMatch)) {
                                    $origin = rtrim($originMatch[1], '/');

                                    if (
                                        str_contains($origin, 'google') ||
                                        str_contains($origin, 'w3.org') ||
                                        str_contains($origin, 'schema.org') ||
                                        str_contains($origin, 'facebook.com') ||
                                        str_contains($origin, 'cdn.') ||
                                        str_contains($origin, 'unpkg.com') ||
                                        str_contains($origin, 'jsdelivr.net')
                                    ) {
                                        continue;
                                    }

                                    if (!isset($domainStats[$origin])) {
                                        $domainStats[$origin] = ['domain' => $origin, 'count' => 0, 'sample_images' => [], 'sample_links' => []];
                                    }

                                    $domainStats[$origin]['count']++;

                                    $cleanUrl = rtrim($fullUrl, '/');
                                    if (preg_match('#\.(jpg|jpeg|png|webp|gif|svg|ico)(\?.*)?$#i', $cleanUrl) || str_contains($cleanUrl, '/storage/')) {
                                        if (count($domainStats[$origin]['sample_images']) < 6 && !in_array($cleanUrl, $domainStats[$origin]['sample_images'], true)) {
                                            $domainStats[$origin]['sample_images'][] = $cleanUrl;
                                        }
                                    } else {
                                        if (count($domainStats[$origin]['sample_links']) < 4 && !in_array($cleanUrl, $domainStats[$origin]['sample_links'], true)) {
                                            $domainStats[$origin]['sample_links'][] = $cleanUrl;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {}

        uasort($domainStats, fn($a, $b) => $b['count'] <=> $a['count']);

        return $domainStats;
    }

    /**
     * Quét và phát hiện các domain hoặc URL đang có sẵn trong cơ sở dữ liệu
     */
    public function detectExistingDomains(): array
    {
        $stats = $this->detectExistingDomainsAndLinks();
        return array_keys($stats);
    }

    /**
     * Thực thi quét hoặc thay thế domain trong Database
     */
    public function run(
        string $from,
        string $to,
        bool $isDryRun = false,
        bool $skipLogs = true,
        ?string $specificTable = null
    ): array {
        $startTime = microtime(true);
        $from = trim($from);
        $to = trim($to);

        if (empty($from)) {
            throw new InvalidArgumentException("Chuỗi tìm kiếm (domain cũ) không được để trống!");
        }

        if ($from === $to) {
            throw new InvalidArgumentException("Chuỗi tìm kiếm và chuỗi thay thế giống hệt nhau.");
        }

        $searchPlain = rtrim($from, '/');
        $replacePlain = $to !== '' ? rtrim($to, '/') : '';

        $searchJson = str_replace('/', '\\/', $searchPlain);
        $replaceJson = str_replace('/', '\\/', $replacePlain);

        $searchJsonLike = str_replace('\\', '\\\\', $searchJson);

        // Lấy danh sách bảng trong Database
        $dbName = DB::getDatabaseName();
        $tablesRaw = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $dbName;

        $targetTables = [];
        foreach ($tablesRaw as $t) {
            $tableName = $t->$tableKey ?? current((array) $t);
            if ($specificTable && $tableName !== $specificTable) {
                continue;
            }
            if ($skipLogs && in_array($tableName, $this->defaultIgnoredTables, true)) {
                continue;
            }
            $targetTables[] = $tableName;
        }

        $results = [];
        $totalMatchedRows = 0;
        $totalUpdatedRows = 0;

        foreach ($targetTables as $table) {
            try {
                $columnsRaw = DB::select("SHOW COLUMNS FROM `{$table}`");
            } catch (Throwable $e) {
                continue;
            }

            $primaryKey = null;
            $textColumns = [];

            foreach ($columnsRaw as $c) {
                if (isset($c->Key) && $c->Key === 'PRI' && !$primaryKey) {
                    $primaryKey = $c->Field;
                }
                $type = strtolower($c->Type ?? '');
                if (
                    str_contains($type, 'char') ||
                    str_contains($type, 'text') ||
                    str_contains($type, 'json')
                ) {
                    $textColumns[] = $c->Field;
                }
            }

            if (empty($textColumns)) {
                continue;
            }

            // Kiểm tra xem bảng có dữ liệu không, nếu bảng rỗng thì bỏ qua ngay để tăng tốc độ
            try {
                if (DB::table($table)->count() === 0) {
                    continue;
                }
            } catch (Throwable $e) {
                continue;
            }

            foreach ($textColumns as $col) {
                try {
                    $matchCount = DB::table($table)
                        ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                            $q->where($col, 'LIKE', "%{$searchPlain}%")
                              ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                        })
                        ->count();

                    if ($matchCount > 0) {
                        $sampleRecord = DB::table($table)
                            ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                                $q->where($col, 'LIKE', "%{$searchPlain}%")
                                  ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                            })
                            ->first();

                        $sampleBefore = $sampleRecord ? (string) $sampleRecord->$col : '';
                        $sampleAfter = str_replace(
                            [$searchJson, $searchPlain],
                            [$replaceJson, $replacePlain],
                            $sampleBefore
                        );

                        $snippetBefore = $this->extractContextSnippet($sampleBefore, $searchPlain, $searchJson, 55);
                        $snippetAfter = $this->extractContextSnippet($sampleAfter, $replacePlain, $replaceJson, 55);
                        $sampleUrlBefore = $this->extractMatchedFullUrl($sampleBefore, $searchPlain);
                        $sampleUrlAfter = $sampleUrlBefore ? str_replace([$searchJson, $searchPlain], [$replaceJson, $replacePlain], $sampleUrlBefore) : null;

                        $updatedCount = 0;

                        if (!$isDryRun) {
                            $hasSerialized = false;
                            if ($primaryKey) {
                                $serializedCheck = DB::table($table)
                                    ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                                        $q->where($col, 'LIKE', "%{$searchPlain}%")
                                          ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                                    })
                                    ->where(function ($q) use ($col) {
                                        $q->where($col, 'LIKE', 'a:%')
                                          ->orWhere($col, 'LIKE', 'O:%')
                                          ->orWhere($col, 'LIKE', 's:%');
                                    })
                                    ->count();
                                $hasSerialized = ($serializedCheck > 0);
                            }

                            if ($hasSerialized && $primaryKey) {
                                $rows = DB::table($table)
                                    ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                                        $q->where($col, 'LIKE', "%{$searchPlain}%")
                                          ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                                    })
                                    ->get([$primaryKey, $col]);

                                foreach ($rows as $r) {
                                    $orig = $r->$col;
                                    $conv = $this->replaceSerializedSafe($orig, [
                                        $searchJson => $replaceJson,
                                        $searchPlain => $replacePlain,
                                    ]);

                                    if ($conv !== $orig) {
                                        DB::table($table)->where($primaryKey, $r->$primaryKey)->update([
                                            $col => $conv,
                                        ]);
                                        $updatedCount++;
                                    }
                                }
                            } else {
                                $pdo = DB::getPdo();
                                $qSearchPlain = $pdo->quote($searchPlain);
                                $qReplacePlain = $pdo->quote($replacePlain);
                                $qSearchJson = $pdo->quote($searchJson);
                                $qReplaceJson = $pdo->quote($replaceJson);

                                $rawSql = "REPLACE(REPLACE(`{$col}`, {$qSearchJson}, {$qReplaceJson}), {$qSearchPlain}, {$qReplacePlain})";

                                $updatedCount = DB::table($table)
                                    ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                                        $q->where($col, 'LIKE', "%{$searchPlain}%")
                                          ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                                    })
                                    ->update([
                                        $col => DB::raw($rawSql),
                                    ]);
                            }

                            $totalUpdatedRows += $updatedCount;
                        }

                        $totalMatchedRows += $matchCount;

                        $results[] = [
                            'table'             => $table,
                            'column'            => $col,
                            'matched'           => $matchCount,
                            'updated'           => $isDryRun ? '(Dry-run)' : $updatedCount,
                            'before'            => $snippetBefore,
                            'after'             => $snippetAfter,
                            'sample_url_before' => $sampleUrlBefore,
                            'sample_url_after'  => $sampleUrlAfter,
                            'is_image'          => $sampleUrlBefore ? (bool) preg_match('#\.(jpg|jpeg|png|webp|gif|svg|ico)(\?.*)?$#i', $sampleUrlBefore) : false,
                        ];
                    }
                } catch (Throwable $e) {
                    // Tiếp tục với các cột khác
                }
            }
        }

        $elapsed = microtime(true) - $startTime;

        return [
            'total_matched'  => $totalMatchedRows,
            'total_updated'  => $totalUpdatedRows,
            'tables_scanned' => count($targetTables),
            'columns_matched'=> count($results),
            'details'        => $results,
            'from'           => $searchPlain,
            'to'             => $replacePlain,
            'is_dry_run'     => $isDryRun,
            'execution_time' => round($elapsed, 3),
        ];
    }

    /**
     * Cập nhật APP_URL trong file .env
     */
    public function updateEnvAppUrl(string $newUrl): bool
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return false;
        }

        $content = file_get_contents($envPath);
        $cleanUrl = rtrim($newUrl, '/');

        if (preg_match('/^APP_URL=(.*)$/m', $content)) {
            $newContent = preg_replace('/^APP_URL=(.*)$/m', "APP_URL={$cleanUrl}", $content);
        } else {
            $newContent = $content . "\nAPP_URL={$cleanUrl}\n";
        }

        return file_put_contents($envPath, $newContent) !== false;
    }

    /**
     * Dọn sạch toàn bộ cache của Laravel
     */
    public function clearLaravelCache(): array
    {
        $outputs = [];
        try {
            Artisan::call('optimize:clear');
            $outputs['optimize'] = trim(Artisan::output());
        } catch (Throwable $e) {
            $outputs['optimize_error'] = $e->getMessage();
        }

        return $outputs;
    }

    /**
     * Thay thế chuỗi an toàn cho cả PHP Serialized string lẫn plain text
     */
    protected function replaceSerializedSafe(mixed $data, array $searchReplacePairs): mixed
    {
        if (!is_string($data) || empty($data)) {
            return $data;
        }

        if ($this->isSerialized($data)) {
            try {
                $unserialized = @unserialize($data, ['allowed_classes' => false]);
                if ($unserialized !== false || $data === 'b:0;') {
                    $unserialized = $this->recursiveReplace($unserialized, $searchReplacePairs);
                    return serialize($unserialized);
                }
            } catch (Throwable $e) {
                // Fallback nếu không unserialize được
            }
        }

        foreach ($searchReplacePairs as $search => $replace) {
            $data = str_replace($search, $replace, $data);
        }

        return $data;
    }

    /**
     * Thay thế chuỗi đệ quy trong Array / Object
     */
    protected function recursiveReplace(mixed $data, array $searchReplacePairs): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->recursiveReplace($value, $searchReplacePairs);
            }
            return $data;
        }

        if (is_string($data)) {
            foreach ($searchReplacePairs as $search => $replace) {
                $data = str_replace($search, $replace, $data);
            }
            return $data;
        }

        return $data;
    }

    /**
     * Kiểm tra chuỗi có phải PHP Serialized hay không
     */
    protected function isSerialized(string $data): bool
    {
        $data = trim($data);
        if ($data === 'b:0;') {
            return true;
        }
        if (strlen($data) < 4 || $data[1] !== ':') {
            return false;
        }
        $last = substr($data, -1);
        if ($last !== ';' && $last !== '}') {
            return false;
        }
        $token = $data[0];
        return in_array($token, ['s', 'a', 'O', 'b', 'i', 'd'], true);
    }

    /**
     * Trích xuất ngữ cảnh xung quanh chuỗi tìm kiếm (giữ nguyên thẻ HTML để thấy rõ link và ảnh)
     */
    protected function extractContextSnippet(string $text, string $searchPlain, string $searchJson, int $pad = 50): string
    {
        $pos = mb_stripos($text, $searchPlain);
        $searchLen = mb_strlen($searchPlain);

        if ($pos === false && $searchJson !== $searchPlain) {
            $pos = mb_stripos($text, $searchJson);
            $searchLen = mb_strlen($searchJson);
        }

        if ($pos === false) {
            return mb_substr($text, 0, 100) . (mb_strlen($text) > 100 ? '...' : '');
        }

        $start = max(0, $pos - $pad);
        $length = $searchLen + ($pad * 2);
        $snippet = mb_substr($text, $start, $length);

        $prefix = $start > 0 ? '...' : '';
        $suffix = ($start + $length) < mb_strlen($text) ? '...' : '';

        return $prefix . trim($snippet) . $suffix;
    }

    /**
     * Trích xuất link URL đầy đủ đầu tiên khớp trong văn bản (ví dụ link ảnh)
     */
    protected function extractMatchedFullUrl(string $text, string $searchPlain): ?string
    {
        $pattern = '#' . preg_quote($searchPlain, '#') . '[^\s"\'<>\),;]+#i';
        if (preg_match($pattern, $text, $match)) {
            return $match[0];
        }

        $searchJson = str_replace('/', '\\/', $searchPlain);
        $patternJson = '#' . preg_quote($searchJson, '#') . '[^\s"\'<>\),;]+#i';
        if (preg_match($patternJson, $text, $matchJson)) {
            return str_replace('\\/', '/', $matchJson[0]);
        }

        return null;
    }
}

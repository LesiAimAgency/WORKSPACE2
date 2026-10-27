<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseReplaceDomainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:replace-domain
                            {--from= : Chuỗi hoặc URL cũ cần tìm (ví dụ: http://localhost:8000)}
                            {--to= : Chuỗi hoặc URL mới cần thay thế (ví dụ: https://daiphuc68.com hoặc để trống để tạo relative path)}
                            {--dry-run : Chỉ quét kiểm tra trước, không ghi đè vào database}
                            {--skip-logs : Bỏ qua các bảng log như visitor_logs, activity_logs, sessions (mặc định: có)}
                            {--table= : Chỉ quét và thay thế trên 1 bảng cụ thể}
                            {--force : Bỏ qua bước xác nhận (dùng cho deploy script)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tìm và thay thế domain/URL hoặc chuỗi trên toàn bộ bảng và cột trong Database (hỗ trợ cả JSON & Serialized data)';

    /**
     * Danh sách các bảng log hệ thống có thể bỏ qua để tối ưu tốc độ
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

    public function handle(): int
    {
        $this->info('================================================================');
        $this->info('  CÔNG CỤ TÌM VÀ THAY THẾ DOMAIN / URL TOÀN BỘ CƠ SỞ DỮ LIỆU   ');
        $this->info('================================================================');

        $from = $this->option('from');
        $to = $this->option('to');
        $isDryRun = (bool) $this->option('dry-run');
        $specificTable = $this->option('table');
        $skipLogs = $this->option('skip-logs') !== false;

        // 1. Nhận giá trị tìm kiếm
        if (empty($from)) {
            $from = $this->ask('Nhập URL hoặc chuỗi CŨ cần tìm', 'http://localhost:8000');
        }

        // 2. Nhận giá trị thay thế
        if ($to === null) {
            $defaultTo = config('app.url') && config('app.url') !== 'http://localhost' ? config('app.url') : 'https://daiphuc68.vn';
            $to = $this->ask('Nhập URL hoặc chuỗi MỚI cần thay thế (hoặc nhập relative path)', $defaultTo);
        }

        if (empty($from)) {
            $this->error('Lỗi: Chuỗi tìm kiếm không được để trống!');
            return Command::FAILURE;
        }

        if ($from === $to) {
            $this->warn('Chuỗi tìm kiếm và chuỗi thay thế giống hệt nhau. Không có gì cần thay đổi.');
            return Command::SUCCESS;
        }

        // Chuẩn bị các biến thể plain và JSON escaped
        $searchPlain = rtrim($from, '/');
        $replacePlain = $to !== '' ? rtrim($to, '/') : '';

        $searchJson = str_replace('/', '\\/', $searchPlain);
        $replaceJson = str_replace('/', '\\/', $replacePlain);

        $this->line("• Chuỗi tìm kiếm (Plain): <comment>{$searchPlain}</comment>");
        $this->line("• Chuỗi tìm kiếm (JSON):  <comment>{$searchJson}</comment>");
        $this->line("• Chuỗi thay thế (Plain): <info>{$replacePlain}</info>");
        $this->line("• Chuỗi thay thế (JSON):  <info>{$replaceJson}</info>");
        $this->line("• Chế độ: " . ($isDryRun ? '<bg=yellow;fg=black> DRY-RUN (CHỈ QUÉT XEM TRƯỚC, KHÔNG SỬA DB) </>' : '<bg=green;fg=white> THỰC THI (UPDATE DATABASE THẬT) </>'));
        $this->newLine();

        if (! $isDryRun && ! $this->option('force')) {
            if (! $this->confirm('BẠN CÓ CHẮC CHẮN MUỐN THỰC THI THAY THẾ TRỰC TIẾP TRÊN DATABASE?', false)) {
                $this->warn('Đã hủy thao tác.');
                return Command::SUCCESS;
            }
        }

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

        $this->info("Bắt đầu quét " . count($targetTables) . " bảng trong Database [{$dbName}]...");

        $results = [];
        $totalMatchedRows = 0;
        $totalUpdatedRows = 0;

        $bar = $this->output->createProgressBar(count($targetTables));
        $bar->start();

        $searchJsonLike = str_replace('\\', '\\\\', $searchJson);

        foreach ($targetTables as $table) {
            try {
                $columnsRaw = DB::select("SHOW COLUMNS FROM `{$table}`");
            } catch (Throwable $e) {
                $bar->advance();
                continue;
            }

            // Lấy primary key nếu có
            $primaryKey = null;
            $textColumns = [];

            foreach ($columnsRaw as $c) {
                if (isset($c->Key) && $c->Key === 'PRI' && ! $primaryKey) {
                    $primaryKey = $c->Field;
                }
                $type = strtolower($c->Type);
                if (
                    str_contains($type, 'char') ||
                    str_contains($type, 'text') ||
                    str_contains($type, 'json')
                ) {
                    $textColumns[] = $c->Field;
                }
            }

            if (empty($textColumns)) {
                $bar->advance();
                continue;
            }

            foreach ($textColumns as $col) {
                try {
                    // Kiểm tra xem cột có chứa chuỗi tìm kiếm (plain hoặc json) hay không
                    $countPlain = DB::table($table)->where($col, 'LIKE', "%{$searchPlain}%")->count();
                    $countJson = ($searchJson !== $searchPlain)
                        ? DB::table($table)->where($col, 'LIKE', "%{$searchJsonLike}%")->count()
                        : 0;

                    $matchCount = max($countPlain, $countJson);

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

                        // Trích xuất đoạn ngắn để hiển thị báo cáo
                        $shortBefore = mb_substr(strip_tags($sampleBefore), 0, 70) . (mb_strlen($sampleBefore) > 70 ? '...' : '');
                        $shortAfter = mb_substr(strip_tags($sampleAfter), 0, 70) . (mb_strlen($sampleAfter) > 70 ? '...' : '');

                        $updatedCount = 0;

                        if (! $isDryRun) {
                            // Kiểm tra có chứa PHP serialized string hay không
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
                                // Xử lý an toàn cho serialized data
                                $rows = DB::table($table)
                                    ->where(function ($q) use ($col, $searchPlain, $searchJsonLike) {
                                        $q->where($col, 'LIKE', "%{$searchPlain}%")
                                          ->orWhere($col, 'LIKE', "%{$searchJsonLike}%");
                                    })
                                    ->get([$primaryKey, $col]);

                                foreach ($rows as $r) {
                                    $original = $r->$col;
                                    $converted = $this->replaceSerializedSafe($original, [
                                        $searchJson => $replaceJson,
                                        $searchPlain => $replacePlain,
                                    ]);

                                    if ($converted !== $original) {
                                        DB::table($table)->where($primaryKey, $r->$primaryKey)->update([
                                            $col => $converted,
                                        ]);
                                        $updatedCount++;
                                    }
                                }
                            } else {
                                // Sử dụng trực tiếp SQL Native REPLACE() với tốc độ cực nhanh
                                $pdo = DB::getPdo();
                                $quotedSearchPlain = $pdo->quote($searchPlain);
                                $quotedReplacePlain = $pdo->quote($replacePlain);
                                $quotedSearchJson = $pdo->quote($searchJson);
                                $quotedReplaceJson = $pdo->quote($replaceJson);

                                $rawSql = "REPLACE(REPLACE(`{$col}`, {$quotedSearchJson}, {$quotedReplaceJson}), {$quotedSearchPlain}, {$quotedReplacePlain})";

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
                            'table' => $table,
                            'column' => $col,
                            'matched' => $matchCount,
                            'updated' => $isDryRun ? '(Dry-run)' : $updatedCount,
                            'before' => $shortBefore,
                            'after' => $shortAfter,
                        ];
                    }
                } catch (Throwable $e) {
                    // Bỏ qua lỗi với view hoặc computed column không cho update
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Hiển thị kết quả dạng bảng
        if (empty($results)) {
            $this->info("Không tìm thấy dòng nào khớp với '{$searchPlain}' trong các bảng đã quét.");
            return Command::SUCCESS;
        }

        $tableRows = [];
        foreach ($results as $res) {
            $tableRows[] = [
                $res['table'],
                $res['column'],
                $res['matched'],
                $res['updated'],
                $res['before'],
                $res['after'],
            ];
        }

        $this->table(
            ['Bảng (Table)', 'Cột (Column)', 'Khớp (Rows)', 'Cập nhật', 'Mẫu Trước Khi Đổi', 'Mẫu Sau Khi Đổi'],
            $tableRows
        );

        $this->newLine();
        if ($isDryRun) {
            $this->warn("KẾT QUẢ QUÉT THỬ: Tìm thấy {$totalMatchedRows} dòng dữ liệu khớp trong " . count($results) . " cột.");
            $this->line("Để thực thi thay thế thật vào Database, hãy chạy lệnh:");
            $this->line("<comment>php artisan db:replace-domain --from=\"{$from}\" --to=\"{$to}\"</comment>");
        } else {
            $this->info("HOÀN TẤT THÀNH CÔNG: Đã cập nhật {$totalUpdatedRows} dòng dữ liệu trong " . count($results) . " cột.");
            $this->line("Gợi ý tiếp theo: Hãy làm sạch cache hệ thống bằng cách chạy:");
            $this->line("<comment>php artisan cache:clear && php artisan config:clear && php artisan view:clear</comment>");
        }

        return Command::SUCCESS;
    }

    /**
     * Thay thế an toàn cho cả PHP Serialized string lẫn plain text
     */
    protected function replaceSerializedSafe(mixed $data, array $searchReplacePairs): mixed
    {
        if (! is_string($data) || empty($data)) {
            return $data;
        }

        // Kiểm tra xem chuỗi có phải PHP serialized hay không
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

        // Chuỗi thông thường
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
}

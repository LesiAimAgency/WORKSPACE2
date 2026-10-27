<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    /**
     * Get media storage path based on project or tenant
     */
    private function getMediaPath(?Request $request = null)
    {
        return 'media';
    }

    /**
     * @deprecated Use getMediaPath() instead
     */
    private function getTenantPath()
    {
        return $this->getMediaPath(request());
    }

    public function list(Request $request)
    {
        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $path = ltrim(str_replace('\\', '/', $path), '/');

        $fullPath = $path ? $basePath.'/'.$path : $basePath;

        // Ensure base directory exists
        if (! Storage::disk('public')->exists($basePath)) {
            Storage::disk('public')->makeDirectory($basePath);
        }

        // Get folders: collect from media directory
        $dirList = collect(Storage::disk('public')->exists($fullPath) ? Storage::disk('public')->directories($fullPath) : [])
            ->reject(function ($dir) {
                $base = basename($dir);

                return str_starts_with($base, 'project-') || str_starts_with($base, 'tenant-');
            });

        $folders = $dirList->map(function ($dir) use ($basePath) {
            $relPath = ltrim(substr($dir, strlen($basePath)), '/');
            if (! $relPath) {
                $relPath = basename($dir);
            }

            return [
                'name' => basename($dir),
                'path' => $relPath,
            ];
        })->unique('name')->values();

        // Get files: collect from media directory
        $fileList = collect(Storage::disk('public')->exists($fullPath) ? Storage::disk('public')->files($fullPath) : []);

        $media = $fileList->filter(function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'mp4', 'webm', 'mov', 'avi', 'mkv']);
        })->unique(function ($file) {
            return basename($file);
        })->map(function ($file) {
            $cleanFile = preg_replace('#^(storage/|/storage/)+#', '', ltrim($file, '/'));
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $cleanFile)));

            $baseName = basename($file);
            $timestamp = 0;
            try {
                $timestamp = Storage::disk('public')->lastModified($file);
            } catch (\Throwable $e) {
                $timestamp = 0;
            }
            if (preg_match('/^(\d{10,})_/', $baseName, $matches)) {
                $prefixTime = (int) $matches[1];
                if ($prefixTime > $timestamp) {
                    $timestamp = $prefixTime;
                }
            }

            return [
                'id' => $file,
                'name' => basename($file),
                'url' => clean_asset_url(asset('storage/'.$encodedPath)),
                'path' => $file,
                'last_modified' => $timestamp,
            ];
        })->sortByDesc('last_modified')->values();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'folders' => $folders,
                'files' => $media,
            ]);
        }

        return view('cms.media.list');
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:1048576',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: '.implode(', ', $e->validator->errors()->all()),
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }

        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/') : $basePath;

        // Ensure directory exists
        if (! Storage::disk('public')->exists($fullPath)) {
            Storage::disk('public')->makeDirectory($fullPath);
        }

        $uploaded = [];
        $warnings = [];
        $files = $request->file('files');
        $paths = $request->input('paths', []);

        if (empty($files)) {
            return response()->json([
                'success' => false,
                'message' => 'No files provided',
            ], 400);
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'mp4', 'mp3'];
        $dangerousExtensions = ['.php', '.js', '.sh', '.exe', '.bat', '.py', '.pl', '.jsp', '.asp'];

        $currentUser = auth()->user();
        $userInfo = $currentUser ? ($currentUser->email ?? $currentUser->username ?? 'ID: '.$currentUser->id) : 'Khách (Guest)';

        $projectCode = $request->route('projectCode') ?? 'main';
        $historyLogPath = storage_path("logs/file-changes-{$projectCode}.log");

        foreach ($files as $index => $file) {
            try {
                $extension = strtolower($file->getClientOriginalExtension());
                $filenameOriginal = $file->getClientOriginalName();

                $isDangerous = false;
                foreach ($dangerousExtensions as $danger) {
                    if (str_contains(strtolower($filenameOriginal), $danger)) {
                        $isDangerous = true;
                        break;
                    }
                }

                if (! in_array($extension, $allowedExtensions) || $isDangerous) {
                    $warnings[] = 'Đã từ chối file không hợp lệ hoặc nguy hiểm: '.$filenameOriginal;
                    \Log::warning('SECURITY ALERT: Upload Blocked', [
                        'user' => $userInfo,
                        'file_name' => $filenameOriginal,
                        'action' => 'Rejected malicious/invalid file upload',
                        'ip' => request()->ip(),
                    ]);

                    File::append($historyLogPath, json_encode([
                        'timestamp' => now()->timezone('Asia/Ho_Chi_Minh')->toIso8601String(),
                        'action' => '️ Chặn File Độc Hại',
                        'route' => 'Tải lên Media',
                        'method' => 'POST',
                        'user_name' => $currentUser->name ?? $currentUser->username ?? 'Khách',
                        'user_email' => $currentUser->email ?? '',
                        'data_summary' => [
                            'Bị chặn' => $filenameOriginal,
                            'Lý do' => 'Đuôi file không được phép hoặc chứa mã độc',
                        ],
                    ])."\n");

                    continue;
                }

                $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filenameOriginal);
                $targetPath = $fullPath;

                if (isset($paths[$index]) && $paths[$index] !== '') {
                    $dirName = dirname($paths[$index]);
                    if ($dirName !== '.') {
                        $dirParts = explode('/', $dirName);
                        $sanitizedParts = array_map(function ($part) {
                            return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $part);
                        }, $dirParts);
                        $sanitizedDirName = implode('/', $sanitizedParts);

                        $targetPath = $fullPath.'/'.$sanitizedDirName;
                        if (! Storage::disk('public')->exists($targetPath)) {
                            Storage::disk('public')->makeDirectory($targetPath);
                        }
                    }
                }

                // --- ZIP HANDLING ---
                if ($extension === 'zip') {
                    $tempZipPath = $file->storeAs('temp', $filename, 'public');
                    $zip = new \ZipArchive;
                    $absoluteZipPath = Storage::disk('public')->path($tempZipPath);
                    $tempExtractPath = Storage::disk('public')->path('temp/extracted_'.time().'_'.rand(1000, 9999));

                    if ($zip->open($absoluteZipPath) === true) {
                        mkdir($tempExtractPath, 0755, true);
                        $zip->extractTo($tempExtractPath);
                        $zip->close();

                        // Recursive scan and move
                        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempExtractPath));
                        foreach ($iterator as $item) {
                            if ($item->isFile()) {
                                $extractedOriginalName = $item->getFilename();
                                $extractedExt = strtolower($item->getExtension());

                                $isDangerousZip = false;
                                foreach ($dangerousExtensions as $danger) {
                                    if (str_contains(strtolower($extractedOriginalName), $danger)) {
                                        $isDangerousZip = true;
                                        break;
                                    }
                                }

                                $relPath = str_replace($tempExtractPath.DIRECTORY_SEPARATOR, '', $item->getPathname());
                                // convert windows slashes to forward slashes for Storage
                                $relPath = str_replace('\\', '/', $relPath);

                                if (! in_array($extractedExt, $allowedExtensions) || $isDangerousZip) {
                                    $warnings[] = 'Phát hiện và xóa file nguy hiểm trong ZIP: '.$relPath;
                                    \Log::alert('SECURITY ALERT: Malicious File inside ZIP Deleted', [
                                        'user' => $userInfo,
                                        'zip_file' => $filenameOriginal,
                                        'malicious_file' => $relPath,
                                        'action' => 'Extracted but deleted malicious file',
                                        'ip' => request()->ip(),
                                    ]);

                                    File::append($historyLogPath, json_encode([
                                        'timestamp' => now()->timezone('Asia/Ho_Chi_Minh')->toIso8601String(),
                                        'action' => '️ Xóa File ZIP Độc Hại',
                                        'route' => 'Tải lên Media (từ file ZIP)',
                                        'method' => 'DELETE',
                                        'user_name' => $currentUser->name ?? $currentUser->username ?? 'Khách',
                                        'user_email' => $currentUser->email ?? '',
                                        'data_summary' => [
                                            'File ZIP' => $filenameOriginal,
                                            'File chứa mã độc bị diệt' => $relPath,
                                        ],
                                    ])."\n");

                                    unlink($item->getPathname());

                                    continue;
                                }

                                // Move safe file to target
                                $finalPath = $targetPath.'/'.dirname($relPath);
                                if (dirname($relPath) === '.') {
                                    $finalPath = $targetPath;
                                }

                                if (! Storage::disk('public')->exists($finalPath)) {
                                    Storage::disk('public')->makeDirectory($finalPath);
                                }

                                $safeFilename = time().'_'.preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $extractedOriginalName);
                                $finalFilePath = $finalPath.'/'.$safeFilename;
                                $absolutePath = Storage::disk('public')->path($finalFilePath);

                                copy($item->getPathname(), $absolutePath);

                                if (in_array($extractedExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                                    $this->applyWatermark($absolutePath);
                                }

                                $cleanZipFilePath = preg_replace('#^(storage/|/storage/)+#', '', ltrim($finalFilePath, '/'));
                                $encodedZipFilePath = implode('/', array_map('rawurlencode', explode('/', $cleanZipFilePath)));
                                $uploaded[] = [
                                    'id' => $finalFilePath,
                                    'name' => basename($finalFilePath),
                                    'url' => clean_asset_url(asset('storage/'.$encodedZipFilePath)),
                                    'path' => $finalFilePath,
                                ];
                            }
                        }

                        // Cleanup
                        $this->deleteDir($tempExtractPath);
                    } else {
                        $warnings[] = 'Không thể đọc file ZIP: '.$filenameOriginal;
                    }

                    Storage::disk('public')->delete($tempZipPath);
                } else {
                    // Normal file upload
                    $filePath = $file->storeAs($targetPath, $filename, 'public');
                    $absolutePath = Storage::disk('public')->path($filePath);

                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $this->applyWatermark($absolutePath);
                    }

                    $cleanNormalFilePath = preg_replace('#^(storage/|/storage/)+#', '', ltrim($filePath, '/'));
                    $encodedNormalFilePath = implode('/', array_map('rawurlencode', explode('/', $cleanNormalFilePath)));
                    $uploaded[] = [
                        'id' => $filePath,
                        'name' => basename($filePath),
                        'url' => clean_asset_url(asset('storage/'.$encodedNormalFilePath)),
                        'path' => $filePath,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Upload file failed: '.$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
            'warnings' => $warnings,
        ]);
    }

    private function deleteDir($dirPath)
    {
        if (! is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath.'*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    protected function applyWatermark($absolutePath)
    {
        // Increase memory limit to handle very large images (e.g. 7500x7500 requires ~225MB RAM in GD)
        ini_set('memory_limit', '512M');

        try {
            $watermark = setting('watermark', []);
            \Log::info("applyWatermark called for: $absolutePath", ['watermark' => $watermark]);

            if (empty($watermark['enabled'])) {
                \Log::info('Watermark not enabled');

                return;
            }

            if (! file_exists($absolutePath)) {
                \Log::info("Target file does not exist: $absolutePath");

                return;
            }

            $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                \Log::info("Invalid extension for watermark: $ext");

                return;
            }

            [$width, $height] = getimagesize($absolutePath);
            \Log::info("Target image size: $width x $height");
            if (! $width || ! $height) {
                \Log::info('Failed to get image size');

                return;
            }

            if (in_array($ext, ['jpg', 'jpeg'])) {
                $img = @imagecreatefromjpeg($absolutePath);
            } elseif ($ext === 'png') {
                $img = @imagecreatefrompng($absolutePath);
            } elseif ($ext === 'webp') {
                $img = @imagecreatefromwebp($absolutePath);
            } else {
                return;
            }

            if (! $img) {
                return;
            }
            imagealphablending($img, true);
            imagesavealpha($img, true);

            $width = imagesx($img);
            $height = imagesy($img);
            $position = $watermark['position'] ?? 'bottom-right';
            $offsetX = (int) ($watermark['offset_x'] ?? 10);
            $offsetY = (int) ($watermark['offset_y'] ?? 10);

            $wmImage = $watermark['image'] ?? '';

            // Handle absolute URL stored in settings
            if (str_starts_with($wmImage, 'http')) {
                $parsedUrl = parse_url($wmImage, PHP_URL_PATH);
                $wmImage = preg_replace('/^\/?storage\//', '', $parsedUrl);
            }
            // Decode URL encoding (e.g. space -> %20)
            $wmImage = urldecode($wmImage);

            if ($wmImage && Storage::disk('public')->exists($wmImage)) {
                $wmPath = Storage::disk('public')->path($wmImage);
                $wmExt = strtolower(pathinfo($wmPath, PATHINFO_EXTENSION));

                if (in_array($wmExt, ['jpg', 'jpeg'])) {
                    $wm = @imagecreatefromjpeg($wmPath);
                } elseif ($wmExt === 'png') {
                    $wm = @imagecreatefrompng($wmPath);
                } elseif ($wmExt === 'webp') {
                    $wm = @imagecreatefromwebp($wmPath);
                }

                if (isset($wm) && $wm) {
                    $wmW = imagesx($wm);
                    $wmH = imagesy($wm);

                    // Scale watermark if setting exists
                    $scale = (int) ($watermark['scale'] ?? 20);
                    if ($scale > 0 && $scale <= 100) {
                        $newWmW = (int) ($width * ($scale / 100));
                        $newWmH = (int) ($wmH * ($newWmW / $wmW));

                        $resizedWm = imagecreatetruecolor(max(1, $newWmW), max(1, $newWmH));
                        imagealphablending($resizedWm, false);
                        imagesavealpha($resizedWm, true);
                        $transparent = imagecolorallocatealpha($resizedWm, 255, 255, 255, 127);
                        imagefilledrectangle($resizedWm, 0, 0, $newWmW, $newWmH, $transparent);
                        imagecopyresampled($resizedWm, $wm, 0, 0, 0, 0, $newWmW, $newWmH, $wmW, $wmH);

                        imagedestroy($wm);
                        $wm = $resizedWm;
                        $wmW = $newWmW;
                        $wmH = $newWmH;
                    }

                    $dstX = $offsetX;
                    $dstY = $offsetY;

                    if (str_contains($position, 'right')) {
                        $dstX = $width - $wmW - $offsetX;
                    } elseif (str_contains($position, 'center')) {
                        $dstX = (int) (($width / 2) - ($wmW / 2));
                    }

                    if (str_contains($position, 'bottom')) {
                        $dstY = $height - $wmH - $offsetY;
                    } elseif (str_contains($position, 'center') || str_contains($position, 'middle')) {
                        $dstY = (int) (($height / 2) - ($wmH / 2));
                    }

                    // Simple merge (GD imagecopy doesn't handle opacity well for PNG with alpha channel)
                    // We just use imagecopy for full opacity PNGs
                    imagecopy($img, $wm, $dstX, $dstY, 0, 0, $wmW, $wmH);
                    imagedestroy($wm);
                }
            }
            if (in_array($ext, ['jpg', 'jpeg'])) {
                imagejpeg($img, $absolutePath, 90);
            } elseif ($ext === 'png') {
                imagepng($img, $absolutePath);
            } elseif ($ext === 'webp') {
                imagewebp($img, $absolutePath, 90);
            }
            imagedestroy($img);
        } catch (\Throwable $e) {
            \Log::error('Error in applyWatermark: '.$e->getMessage());
        }
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string',
        ]);

        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/').'/'.$request->name : $basePath.'/'.$request->name;

        try {
            Storage::disk('public')->makeDirectory($fullPath);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function deleteFolder(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $basePath = $this->getMediaPath($request);
        $rawPath = ltrim(str_replace('\\', '/', $request->path), '/');

        if (str_starts_with($rawPath, 'storage/')) {
            $rawPath = substr($rawPath, 8);
        }

        if (str_starts_with($rawPath, $basePath . '/')) {
            $fullPath = $rawPath;
        } elseif ($rawPath === $basePath || $rawPath === '') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa thư mục gốc.',
            ], 400);
        } else {
            $fullPath = $basePath . '/' . $rawPath;
        }

        if (Storage::disk('public')->directoryExists($fullPath) || Storage::disk('public')->exists($fullPath) || is_dir(Storage::disk('public')->path($fullPath))) {
            Storage::disk('public')->deleteDirectory($fullPath);
        }

        $publicStoragePath = public_path('storage/' . $fullPath);
        if (file_exists($publicStoragePath) && is_dir($publicStoragePath)) {
            File::deleteDirectory($publicStoragePath);
        }

        $publicMediaFilesPath = public_path('media-files/' . $fullPath);
        if (file_exists($publicMediaFilesPath) && is_dir($publicMediaFilesPath)) {
            File::deleteDirectory($publicMediaFilesPath);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thư mục thành công.',
        ]);
    }

    public function move(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'type' => 'required|in:file,folder',
        ]);

        $basePath = $this->getMediaPath($request);
        $fromPath = $request->type === 'folder' ? $basePath.'/'.ltrim($request->from, '/') : $basePath.'/'.ltrim($request->from, '/');
        $toPath = $basePath.'/'.ltrim($request->to, '/').'/'.basename($fromPath);

        if ($request->type === 'folder') {
            if (Storage::disk('public')->exists($fromPath)) {
                Storage::disk('public')->move($fromPath, $toPath);

                return response()->json(['success' => true]);
            }
        } else {
            if (Storage::disk('public')->exists($fromPath)) {
                Storage::disk('public')->move($fromPath, $toPath);

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    public function destroy(Request $request, ...$args)
    {
        $id = $request->route('id');
        if (! $id && count($args) > 0) {
            $id = end($args); // fallback to last parameter if route('id') fails
        }

        if (! $id) {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }

        $id = ltrim(str_replace('\\', '/', (string) $id), '/');
        if (str_starts_with($id, 'storage/')) {
            $id = substr($id, 8);
        }

        // If it's a directory
        if (Storage::disk('public')->directoryExists($id) || is_dir(Storage::disk('public')->path($id))) {
            Storage::disk('public')->deleteDirectory($id);
            $pubPath = public_path('storage/' . $id);
            if (file_exists($pubPath) && is_dir($pubPath)) {
                File::deleteDirectory($pubPath);
            }

            return response()->json(['success' => true]);
        }

        // If it's a file
        if (Storage::disk('public')->exists($id) || file_exists(Storage::disk('public')->path($id))) {
            Storage::disk('public')->delete($id);
            $pubPath = public_path('storage/' . $id);
            if (file_exists($pubPath) && is_file($pubPath)) {
                @unlink($pubPath);
            }

            return response()->json(['success' => true]);
        }

        \Log::error('File not found for deletion', ['id' => $id]);

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string',
        ]);

        $deleted = 0;
        foreach ($request->ids as $id) {
            $path = ltrim($id, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            if (Storage::disk('public')->directoryExists($path)) {
                Storage::disk('public')->deleteDirectory($path);
                $deleted++;
            } elseif (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted++;
            }
        }

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }
}

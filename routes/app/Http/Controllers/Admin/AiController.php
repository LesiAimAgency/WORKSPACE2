<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function test(Request $request)
    {
        $provider = $request->input('provider', 'gemini');
        $apiKey = $request->input('api_key');
        $model = $request->input('model');

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập API Key']);
        }

        try {
            switch ($provider) {
                case 'openai':
                    return $this->testOpenAI($apiKey, $model);

                case 'gemini':
                    return $this->testGemini($apiKey, $model);

                default:
                    return response()->json(['success' => false, 'message' => 'Provider không hợp lệ']);
            }
        } catch (\Throwable $e) {
            Log::error('AI Test Exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['success' => false, 'message' => 'Lỗi kết nối: '.$e->getMessage()]);
        }
    }

    private function testOpenAI($apiKey, $model)
    {
        $modelName = $model ?: 'gpt-3.5-turbo';

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $modelName,
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 10,
        ]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => ' Kết nối thành công! Model: '.$modelName]);
        }

        $errorMessage = $response->json()['error']['message'] ?? $response->body() ?? 'Unknown error';

        return response()->json(['success' => false, 'message' => 'Lỗi: '.$errorMessage]);
    }

    private function testGemini($apiKey, $model)
    {
        $validModel = $this->getValidGeminiModel($model);

        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $apiKey,
        ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$validModel}:generateContent", [
            'contents' => [['parts' => [['text' => 'Hello']]]],
        ]);

        // If the chosen model is overloaded (503) or not found (404) and was not gemini-3.5-flash, fallback to gemini-3.5-flash
        if (! $response->successful() && $validModel !== 'gemini-3.5-flash') {
            $fallbackResp = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->timeout(30)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent', [
                'contents' => [['parts' => [['text' => 'Hello']]]],
            ]);

            if ($fallbackResp->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => " Kết nối thành công! (Model {$validModel} bận, tự động chuyển sang gemini-3.5-flash hoạt động tốt)",
                ]);
            }
        }

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => ' Kết nối thành công! Model: '.$validModel]);
        }

        $errorMessage = 'Unknown error';
        $json = $response->json();
        if ($json && isset($json['error']['message'])) {
            $errorMessage = $json['error']['message'];

            // Handle common errors with user-friendly messages
            if (str_contains($errorMessage, 'denied access') || str_contains($errorMessage, 'PERMISSION_DENIED')) {
                $errorMessage = '❌ Project/API Key này đã bị Google khóa quyền truy cập ("Your project has been denied access"). Vui lòng vào aistudio.google.com tạo API Key trong một "New Project" mới hoặc đổi tài khoản Gmail khác!';
            } elseif (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid') || str_contains($errorMessage, 'API_KEY_INVALID')) {
                $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới tại aistudio.google.com';
            } elseif (str_contains($errorMessage, 'not found') || str_contains($errorMessage, 'is not supported') || str_contains($errorMessage, 'no longer available')) {
                $errorMessage = '⚠️ Model '.$validModel.' đã ngừng hỗ trợ hoặc không tồn tại. Vui lòng chuyển sang gemini-flash-latest hoặc gemini-3.5-flash.';
            } elseif (str_contains($errorMessage, 'high demand') || str_contains($errorMessage, '503') || str_contains($errorMessage, 'UNAVAILABLE')) {
                $errorMessage = '⏳ Model '.$validModel.' đang quá tải tạm thời tại Google. Hãy thử lại hoặc chuyển sang gemini-flash-latest / gemini-3.1-flash-lite.';
            } elseif (str_contains($errorMessage, 'quota') || str_contains($errorMessage, 'RESOURCE_EXHAUSTED')) {
                $errorMessage = '⚠️ Đã hết quota API! Vui lòng kiểm tra mức sử dụng tại Google AI Studio.';
            }
        }

        return response()->json(['success' => false, 'message' => 'Lỗi: '.$errorMessage]);
    }

    public function generate(Request $request)
    {
        try {
            $ai = setting('ai', []);
            if (is_string($ai)) {
                $ai = json_decode($ai, true) ?: [];
            }

            if (! ($ai['enabled'] ?? false)) {
                return response()->json(['error' => 'AI chưa được bật trong cấu hình']);
            }

            $openaiKey = $ai['openai_key'] ?? '';
            $geminiKey = $ai['gemini_key'] ?? '';

            if (! empty($geminiKey)) {
                $provider = 'gemini';
                $apiKey = $geminiKey;
                $model = $ai['gemini_model'] ?? 'gemini-flash-latest';
            } elseif (! empty($openaiKey)) {
                $provider = 'openai';
                $apiKey = $openaiKey;
                $model = $ai['openai_model'] ?? 'gpt-3.5-turbo';
            } else {
                return response()->json(['error' => 'Chưa cấu hình API key']);
            }

            $temperature = (float) ($ai['temperature'] ?? 0.7);
            $maxTokens = (int) ($ai['max_tokens'] ?? 4096);
            $rawPrompt = $request->input('prompt');

            if (empty($rawPrompt)) {
                return response()->json(['error' => 'Prompt không được để trống']);
            }

            $postType = $request->input('post_type', 'post');
            $customMasterPrompt = $request->input('master_prompt');
            $sourceData = $request->input('source_data');
            $existingData = $request->input('existing_data');
            $tone = $request->input('tone', 'professional');
            $lang = $request->input('lang', 'vi');
            $isStructured = $request->boolean('structured') || $request->input('format') === 'structured';

            // Resolve Master Prompt from settings or fallback to default template
            $configuredMasterPrompt = '';
            if (! empty($ai['post_types'][$postType]['master_prompt'])) {
                $configuredMasterPrompt = trim($ai['post_types'][$postType]['master_prompt']);
            }
            $masterPrompt = ! empty($customMasterPrompt) ? trim($customMasterPrompt) : $configuredMasterPrompt;

            if ($isStructured) {
                $siteContext = [
                    'brand_name' => setting('site_name', config('app.name', 'Website')),
                    'language' => $lang,
                    'tone' => $tone,
                ];

                $prompt = \App\Services\AiPromptTemplateService::buildPrompt(
                    postType: $postType,
                    userRequest: $rawPrompt,
                    sourceData: $sourceData,
                    existingData: $existingData,
                    tone: $tone,
                    customMasterPrompt: $masterPrompt,
                    siteContext: $siteContext
                );
            } else {
                $prompt = '';
                if (! empty($masterPrompt)) {
                    $prompt .= "{$masterPrompt}\n\n";
                }
                $prompt .= $rawPrompt;
            }

            Log::info('AI Generate Request', [
                'provider' => $provider,
                'model' => $model,
                'post_type' => $postType,
                'structured' => $isStructured,
            ]);

            switch ($provider) {
                case 'openai':
                    $result = $this->generateOpenAI($apiKey, $model, $prompt, $temperature, $maxTokens);
                    break;

                case 'gemini':
                    $result = $this->generateGemini($apiKey, $model, $prompt, $temperature, $maxTokens);
                    break;
                default:
                    return response()->json(['error' => 'Provider không được hỗ trợ']);
            }

            // Post-process structured response if requested
            if ($isStructured && $result->getStatusCode() === 200) {
                $data = json_decode($result->getContent(), true);

                if (isset($data['error'])) {
                    return response()->json(['error' => $data['error']], 400);
                }

                $content = $data['content'] ?? '';
                if (empty(trim($content))) {
                    return response()->json(['error' => 'AI không trả về nội dung. Vui lòng thử lại!'], 500);
                }

                // Strip markdown code block wrappers if any
                $cleanContent = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
                $cleanContent = preg_replace('/\s*```$/i', '', $cleanContent);

                $siteName = setting('site_name', config('app.name', 'Website'));

                if (preg_match('/\{[\s\S]*\}/', $cleanContent, $matches)) {
                    $parsed = json_decode($matches[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $title = $parsed['title'] ?? ($parsed['name'] ?? '');
                        $slug = $parsed['slug'] ?? '';
                        $excerpt = $parsed['excerpt'] ?? ($parsed['short_description'] ?? '');
                        $htmlContent = $parsed['content'] ?? ($parsed['description'] ?? '');
                        $focusKeyword = $parsed['focus_keyword'] ?? '';
                        $metaTitle = $parsed['meta_title'] ?? ($parsed['title'] ?? ($parsed['name'] ?? ''));
                        $metaDesc = $parsed['meta_description'] ?? ($parsed['excerpt'] ?? ($parsed['short_description'] ?? ''));
                        $schemaType = $parsed['schema_type'] ?? null;

                        if (! empty($title) && ! empty($htmlContent)) {
                            // Tối ưu hóa toàn diện để đạt 100/100 checklist SEO
                            $seoPackage = \App\Services\AiPromptTemplateService::optimizeSeoPackage(
                                title: $title,
                                content: $htmlContent,
                                focusKeyword: $focusKeyword,
                                metaTitle: $metaTitle,
                                metaDesc: $metaDesc,
                                slug: $slug,
                                schemaType: $schemaType,
                                postType: $postType,
                                siteName: $siteName
                            );

                            return response()->json([
                                'success' => true,
                                'structured' => true,
                                'title' => $title,
                                'slug' => $seoPackage['slug'],
                                'excerpt' => $excerpt,
                                'content' => $seoPackage['content'],
                                'focus_keyword' => $seoPackage['focus_keyword'],
                                'meta_title' => $seoPackage['meta_title'],
                                'meta_description' => $seoPackage['meta_description'],
                                'schema_type' => $seoPackage['schema_type'],
                                'raw' => $content,
                            ]);
                        }
                    }
                }

                // If strict JSON decode failed, extract via regex
                $extractedTitle = '';
                $extractedSlug = '';
                $extractedExcerpt = '';
                $extractedContent = '';
                $extractedKeyword = '';
                if (preg_match('/"(?:title|name)"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $cleanContent, $m)) {
                    $extractedTitle = stripslashes($m[1]);
                }
                if (preg_match('/"slug"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $cleanContent, $m)) {
                    $extractedSlug = stripslashes($m[1]);
                }
                if (preg_match('/"(?:excerpt|short_description)"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $cleanContent, $m)) {
                    $extractedExcerpt = stripslashes($m[1]);
                }
                if (preg_match('/"(?:content|description)"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $cleanContent, $m)) {
                    $extractedContent = stripslashes($m[1]);
                }
                if (preg_match('/"focus_keyword"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $cleanContent, $m)) {
                    $extractedKeyword = stripslashes($m[1]);
                }

                if ($extractedTitle && $extractedContent) {
                    $seoPackage = \App\Services\AiPromptTemplateService::optimizeSeoPackage(
                        title: $extractedTitle,
                        content: $extractedContent,
                        focusKeyword: $extractedKeyword,
                        metaTitle: $extractedTitle,
                        metaDesc: $extractedExcerpt,
                        slug: $extractedSlug,
                        schemaType: null,
                        postType: $postType,
                        siteName: $siteName
                    );

                    return response()->json([
                        'success' => true,
                        'structured' => true,
                        'title' => $extractedTitle,
                        'slug' => $seoPackage['slug'],
                        'excerpt' => $extractedExcerpt,
                        'content' => $seoPackage['content'],
                        'focus_keyword' => $seoPackage['focus_keyword'],
                        'meta_title' => $seoPackage['meta_title'],
                        'meta_description' => $seoPackage['meta_description'],
                        'schema_type' => $seoPackage['schema_type'],
                        'raw' => $content,
                    ]);
                }

                return response()->json([
                    'error' => 'AI phản hồi không đúng định dạng bài viết. Vui lòng bấm tạo lại!',
                    'raw' => $content,
                ], 422);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('AI Generate Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Lỗi xử lý AI: '.$e->getMessage()]);
        }
    }

    /**
     * Tối ưu hóa SEO độc lập (1-Click SEO Optimizer)
     * Phân tích nội dung / tiêu đề và tạo ra focus_keyword, meta_title (50-60 ký tự),
     * meta_description (125-155 ký tự), slug, schema_type đạt 100/100 điểm.
     */
    public function optimizeSeo(Request $request)
    {
        try {
            $title = $request->input('title', '');
            $content = $request->input('content', '');
            $postType = $request->input('post_type', 'post');
            $focusKeyword = $request->input('focus_keyword', '');
            $metaTitle = $request->input('meta_title', '');
            $metaDesc = $request->input('meta_description', '');
            $slug = $request->input('slug', '');
            $schemaType = $request->input('schema_type', '');

            if (empty(trim($title)) && empty(trim($content)) && empty(trim($focusKeyword))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập Tiêu đề hoặc Nội dung bài viết trước khi tối ưu SEO.',
                ], 422);
            }

            $siteName = setting('site_name', config('app.name', 'Website'));

            // Kiểm tra cấu hình AI để sử dụng LLM sáng tạo nội dung SEO hấp dẫn nếu có
            $ai = setting('ai', []);
            if (is_string($ai)) {
                $ai = json_decode($ai, true) ?: [];
            }

            $openaiKey = $ai['openai_key'] ?? '';
            $geminiKey = $ai['gemini_key'] ?? '';
            $canUseAi = ($ai['enabled'] ?? false) && (! empty($geminiKey) || ! empty($openaiKey));

            if ($canUseAi) {
                try {
                    $plainContent = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($content))), 0, 500, 'UTF-8');
                    $prompt = "Bạn là chuyên gia SEO hàng đầu (Senior SEO Specialist). Nhiệm vụ là tối ưu hóa toàn bộ dữ liệu SEO cho bài viết/sản phẩm sau để đạt chuẩn 100/100 điểm trên checklist SEO CMS:\n\n";
                    $prompt .= "- Tiêu đề: {$title}\n";
                    $prompt .= "- Loại bài viết (Post Type): {$postType}\n";
                    if (! empty($focusKeyword)) {
                        $prompt .= "- Từ khóa mục tiêu mong muốn: {$focusKeyword}\n";
                    }
                    if (! empty($plainContent)) {
                        $prompt .= "- Tóm tắt nội dung: {$plainContent}\n";
                    }
                    $prompt .= "\n=== NGUYÊN TẮC BẮT BUỘC ĐỂ ĐẠT 100/100 ĐIỂM ===\n";
                    $prompt .= "1. focus_keyword: 2-4 từ quan trọng nhất (viết thường tiếng Việt, ví dụ: 'đèn năng lượng mặt trời 300w').\n";
                    $prompt .= "2. meta_title: BẮT BUỘC TỪ 50 ĐẾN 60 KÝ TỰ (Đếm chính xác ký tự), BẮT BUỘC CHỨA NGUYÊN VẸN focus_keyword.\n";
                    $prompt .= "3. meta_description: BẮT BUỘC TỪ 125 ĐẾN 155 KÝ TỰ (Đếm chính xác ký tự), BẮT BUỘC CHỨA NGUYÊN VẸN focus_keyword và có lời kêu gọi hành động hấp dẫn.\n";
                    $prompt .= "4. slug: Tiếng Việt không dấu nối gạch ngang, BẮT BUỘC chứa focus_keyword.\n";
                    $prompt .= "5. schema_type: ".($postType === 'product' ? 'Product' : ($postType === 'faq' ? 'FAQPage' : ($postType === 'page' ? 'Organization' : 'Article')))."\n\n";
                    $prompt .= "Trả về DUY NHẤT một chuỗi JSON hợp lệ (KHÔNG kèm markdown code fence ```json, KHÔNG giải thích):\n";
                    $prompt .= '{"focus_keyword": "...", "meta_title": "...", "meta_description": "...", "slug": "...", "schema_type": "..."}';

                    $provider = ! empty($geminiKey) ? 'gemini' : 'openai';
                    $apiKey = ! empty($geminiKey) ? $geminiKey : $openaiKey;
                    $model = $provider === 'gemini' ? ($ai['gemini_model'] ?? 'gemini-flash-latest') : ($ai['openai_model'] ?? 'gpt-3.5-turbo');

                    $resp = $provider === 'gemini'
                        ? $this->generateGemini($apiKey, $model, $prompt, 0.4, 1024)
                        : $this->generateOpenAI($apiKey, $model, $prompt, 0.4, 1024);

                    if ($resp->getStatusCode() === 200) {
                        $respData = json_decode($resp->getContent(), true);
                        $rawText = $respData['content'] ?? '';
                        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($rawText));
                        $clean = preg_replace('/\s*```$/i', '', $clean);
                        if (preg_match('/\{[\s\S]*\}/', $clean, $m)) {
                            $aiParsed = json_decode($m[0], true);
                            if (is_array($aiParsed)) {
                                if (! empty($aiParsed['focus_keyword'])) {
                                    $focusKeyword = $aiParsed['focus_keyword'];
                                }
                                if (! empty($aiParsed['meta_title'])) {
                                    $metaTitle = $aiParsed['meta_title'];
                                }
                                if (! empty($aiParsed['meta_description'])) {
                                    $metaDesc = $aiParsed['meta_description'];
                                }
                                if (! empty($aiParsed['slug'])) {
                                    $slug = $aiParsed['slug'];
                                }
                                if (! empty($aiParsed['schema_type'])) {
                                    $schemaType = $aiParsed['schema_type'];
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('AI SEO Optimizer LLM error, falling back to algorithmic optimizer: '.$e->getMessage());
                }
            }

            // Luôn áp dụng bộ tối ưu hóa chuẩn SEO bảo đảm 100/100 điểm
            $seoPackage = \App\Services\AiPromptTemplateService::optimizeSeoPackage(
                title: $title,
                content: $content,
                focusKeyword: $focusKeyword,
                metaTitle: $metaTitle,
                metaDesc: $metaDesc,
                slug: $slug,
                schemaType: $schemaType,
                postType: $postType,
                siteName: $siteName
            );

            return response()->json([
                'success' => true,
                'message' => 'Tối ưu hóa SEO thành công!',
                'data' => $seoPackage,
            ]);
        } catch (\Throwable $e) {
            Log::error('Optimize SEO Exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tối ưu hóa SEO: '.$e->getMessage(),
            ], 500);
        }
    }

    private function generateOpenAI($apiKey, $model, $prompt, $temperature, $maxTokens)
    {
        $modelName = $model ?: 'gpt-3.5-turbo';

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $modelName,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if ($response->successful()) {
            $content = $response->json()['choices'][0]['message']['content'] ?? '';
            if (isset($response->json()['usage'])) {
                $this->updateStats($response->json()['usage']);
            }

            return response()->json(['content' => $content]);
        }

        return response()->json(['error' => $response->json()['error']['message'] ?? $response->body() ?? 'Unknown error'], 400);
    }

    private function generateGemini($apiKey, $model, $prompt, $temperature, $maxTokens)
    {
        $validModel = $this->getValidGeminiModel($model);

        // Danh sách model ưu tiên thử nghiệm tuần tự nếu gặp 503 (quá tải) hoặc 404
        $modelCandidates = array_values(array_unique([
            $validModel,
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-flash-lite-latest',
            'gemini-3.8-flash',
            'gemini-3.5-flash',
            'gemini-flash-latest',
        ]));

        $lastError = 'Unknown error';

        foreach ($modelCandidates as $tryModel) {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])->timeout(35)->post("https://generativelanguage.googleapis.com/v1beta/models/{$tryModel}:generateContent", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                    ],
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                        $content = $responseData['candidates'][0]['content']['parts'][0]['text'];

                        if (isset($responseData['usageMetadata'])) {
                            $this->updateStats([
                                'total_tokens' => $responseData['usageMetadata']['totalTokenCount'] ?? 0,
                            ]);
                        }

                        return response()->json(['content' => $content]);
                    }
                }

                $json = $response->json();
                $lastError = $json['error']['message'] ?? $response->body() ?? 'Lỗi từ Google AI';

                // Nếu là lỗi sai API key, bị khóa quyền truy cập, hoặc hết quota tài khoản thì ngắt ngay, không cần thử model khác
                if (str_contains($lastError, 'API_KEY_INVALID') || str_contains($lastError, 'RESOURCE_EXHAUSTED') || str_contains($lastError, 'denied access') || str_contains($lastError, 'PERMISSION_DENIED')) {
                    break;
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        $errorMessage = $lastError;
        if (str_contains($errorMessage, 'denied access') || str_contains($errorMessage, 'PERMISSION_DENIED')) {
            $errorMessage = '❌ Project/API Key này đã bị Google khóa quyền truy cập ("Your project has been denied access"). Vui lòng tạo API Key trong "New Project" mới trên Google AI Studio hoặc đổi tài khoản Gmail khác!';
        } elseif (str_contains($errorMessage, 'quota') || str_contains($errorMessage, 'RESOURCE_EXHAUSTED')) {
            $errorMessage = '⚠️ Đã hết quota API miễn phí! Vui lòng đợi reset hoặc kiểm tra tại Google AI Studio';
        } elseif (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid') || str_contains($errorMessage, 'API_KEY_INVALID')) {
            $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới tại aistudio.google.com';
        } elseif (str_contains($errorMessage, 'high demand') || str_contains($errorMessage, '503') || str_contains($errorMessage, 'UNAVAILABLE')) {
            $errorMessage = '⏳ Tất cả các model Gemini đang quá tải tạm thời tại Google. Vui lòng thử lại sau giây lát!';
        }

        return response()->json(['error' => $errorMessage], 400);
    }

    private function updateStats($usage)
    {
        try {
            $ai = setting('ai', []);
            if (is_string($ai)) {
                $ai = json_decode($ai, true) ?: [];
            }
            $stats = $ai['stats'] ?? ['total_requests' => 0, 'total_tokens' => 0, 'estimated_cost' => 0];

            $stats['total_requests']++;
            $tokens = $usage['total_tokens'] ?? 0;
            $stats['total_tokens'] += $tokens;
            $stats['estimated_cost'] += $tokens * 0.00002;

            $ai['stats'] = $stats;
            setting(['ai' => $ai]);
        } catch (\Throwable $e) {
            Log::warning('Could not update AI stats: '.$e->getMessage());
        }
    }

    public function listModels(Request $request)
    {
        $apiKey = $request->input('api_key');

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng cung cấp API key']);
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-goog-api-key' => $apiKey,
            ])->timeout(15)->get('https://generativelanguage.googleapis.com/v1beta/models');

            if ($response->successful()) {
                $models = collect($response->json()['models'] ?? [])
                    ->filter(function ($model) {
                        return str_contains($model['name'], 'gemini') &&
                            in_array('generateContent', $model['supportedGenerationMethods'] ?? []);
                    })
                    ->map(function ($model) {
                        return [
                            'name' => str_replace('models/', '', $model['name']),
                            'displayName' => $model['displayName'] ?? $model['name'],
                            'description' => $model['description'] ?? '',
                        ];
                    })
                    ->values();

                return response()->json(['success' => true, 'models' => $models]);
            }

            $errorMessage = 'Không thể lấy danh sách models';
            $json = $response->json();
            if ($json && isset($json['error']['message'])) {
                $errorMessage = $json['error']['message'];

                if (str_contains($errorMessage, 'denied access') || str_contains($errorMessage, 'PERMISSION_DENIED')) {
                    $errorMessage = '❌ Project/API Key này đã bị Google khóa quyền truy cập ("Your project has been denied access"). Vui lòng tạo API Key trong "New Project" mới trên Google AI Studio.';
                } elseif (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid') || str_contains($errorMessage, 'API_KEY_INVALID')) {
                    $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới';
                }
            }

            return response()->json(['success' => false, 'message' => $errorMessage]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getValidGeminiModel($model)
    {
        if (empty($model)) {
            return 'gemini-3.5-flash';
        }

        $model = str_replace('models/', '', trim($model));

        // Remap deprecated/unavailable Google Gemini models
        $deprecated = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro',
            'gemini-1.5-pro-latest',
            'gemini-1.0-pro',
            'gemini-pro',
            'gemini-2.5-flash',
            'gemini-2.5-flash-lite',
            'chat-bison-001',
        ];

        if (in_array($model, $deprecated)) {
            return 'gemini-3.5-flash';
        }

        return $model;
    }
}

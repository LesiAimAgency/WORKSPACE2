<?php

namespace App\Services;

use Illuminate\Support\Str;

class TocService
{
    /**
     * Phân tích và sinh dữ liệu mục lục (TOC) từ nội dung HTML bài viết.
     *
     * @param string|null $content Nội dung bài viết (HTML)
     * @param string|null $postType Loại bài viết (news, post, page, product, project...)
     * @param array|null $customConfig Cấu hình tùy biến ghi đè
     * @return array
     */
    public function generate(?string $content, ?string $postType = 'news', ?array $customConfig = null): array
    {
        $content = function_exists('sanitize_editor_html') ? sanitize_editor_html($content) : $content;
        $config = $customConfig ?? setting('toc', []);

        // Giá trị mặc định
        $enabled = isset($config['enabled']) ? (bool) $config['enabled'] : true;
        $headingLevels = is_array($config['heading_levels'] ?? null) && ! empty($config['heading_levels'])
            ? array_map('strtolower', $config['heading_levels'])
            : ['h2', 'h3'];
        $minHeadings = (int) ($config['min_headings'] ?? 2);
        $title = ! empty($config['title']) ? $config['title'] : 'Mục lục bài viết';
        $style = $config['style'] ?? 'style-1';
        $showNumbers = isset($config['show_numbers']) ? (bool) $config['show_numbers'] : true;
        $collapsible = isset($config['collapsible']) ? (bool) $config['collapsible'] : true;
        $initialCollapsed = isset($config['initial_collapsed']) ? (bool) $config['initial_collapsed'] : false;
        $smoothScroll = isset($config['smooth_scroll']) ? (bool) $config['smooth_scroll'] : true;
        $highlightActive = isset($config['highlight_active']) ? (bool) $config['highlight_active'] : true;
        $stickyToc = isset($config['sticky_toc']) ? (bool) $config['sticky_toc'] : true;
        $position = $config['position'] ?? 'before_content';
        $scrollOffset = (int) ($config['scroll_offset'] ?? 100);
        $postTypes = is_array($config['post_types'] ?? null) && ! empty($config['post_types'])
            ? $config['post_types']
            : ['news', 'post', 'page', 'product', 'project'];

        $resolvedConfig = [
            'enabled' => $enabled,
            'title' => $title,
            'position' => $position,
            'min_headings' => $minHeadings,
            'heading_levels' => $headingLevels,
            'style' => $style,
            'show_numbers' => $showNumbers,
            'collapsible' => $collapsible,
            'initial_collapsed' => $initialCollapsed,
            'smooth_scroll' => $smoothScroll,
            'highlight_active' => $highlightActive,
            'sticky_toc' => $stickyToc,
            'scroll_offset' => $scrollOffset,
            'post_types' => $postTypes,
        ];

        // Chuẩn hóa alias post_type nếu cần (post -> news, article -> news)
        $normalizedPostType = match ($postType) {
            'post', 'article' => 'news',
            default => $postType,
        };

        // Kiểm tra bật/tắt toàn cục hoặc theo post_type
        if (! $enabled || ($normalizedPostType && ! in_array($normalizedPostType, $postTypes) && ! in_array($postType, $postTypes))) {
            // Dọn dẹp shortcode [toc] nếu có trong bài khi tắt TOC
            $cleanContent = $content ? preg_replace('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', '', $content) : '';
            return [
                'content' => $cleanContent,
                'headings' => [],
                'toc' => [],
                'has_toc' => false,
                'enabled' => false,
                'config' => $resolvedConfig,
                'html' => '',
            ];
        }

        if (empty($content)) {
            return [
                'content' => '',
                'headings' => [],
                'toc' => [],
                'has_toc' => false,
                'enabled' => false,
                'config' => $resolvedConfig,
                'html' => '',
            ];
        }

        // Tạo regex tìm các thẻ heading theo danh sách cấu hình theo thứ tự xuất hiện tự nhiên
        $levelsRegex = implode('|', $headingLevels);
        $pattern = '/<(' . $levelsRegex . ')([^>]*)>(.*?)<\/\1>/isu';

        $headings = [];
        $usedIds = [];
        $counter = [];

        // Tìm cấp độ nhỏ nhất trong cấu hình (thường là h2 -> 2)
        $levelNums = array_map(fn ($l) => (int) substr($l, 1), $headingLevels);
        $baseLevel = ! empty($levelNums) ? min($levelNums) : 2;

        $newContent = preg_replace_callback($pattern, function ($matches) use (
            &$headings,
            &$usedIds,
            &$counter,
            $baseLevel
        ) {
            $tag = strtolower($matches[1]);
            $attrs = $matches[2];
            $innerHtml = $matches[3];

            $rawText = trim(strip_tags($innerHtml));
            $decodedText = trim(html_entity_decode($rawText, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $decodedText = trim(str_replace(["\xc2\xa0", "\u{00A0}", '&nbsp;'], '', $decodedText));
            if ($rawText === '' || $decodedText === '') {
                return $matches[0];
            }

            // Kiểm tra xem đã có id chưa
            $id = '';
            if (preg_match('/id=["\']([^"\']+)["\']/i', $attrs, $idMatch)) {
                $id = $idMatch[1];
            } else {
                // Tách bỏ số thứ tự đầu dòng (nếu có) để tạo slug gọn đẹp
                $cleanText = preg_replace('/^\d+([.\-]\d+)*[.\s\-:]+/u', '', $rawText);
                $cleanText = trim($cleanText);

                $slugCandidate = Str::slug($cleanText ?: $rawText);
                if (empty($slugCandidate)) {
                    $slugCandidate = 'muc-' . (count($headings) + 1);
                }

                $id = $slugCandidate;
                $idx = 1;
                while (in_array($id, $usedIds, true)) {
                    $idx++;
                    $id = $slugCandidate . '-' . $idx;
                }

                $attrs = ' id="' . htmlspecialchars($id) . '"' . $attrs;
            }

            $usedIds[] = $id;

            // Xử lý đánh số thứ tự phân cấp
            $levelNum = (int) substr($tag, 1);
            $counter[$levelNum] = ($counter[$levelNum] ?? 0) + 1;

            // Reset bộ đếm cho các cấp sâu hơn
            for ($i = $levelNum + 1; $i <= 6; $i++) {
                $counter[$i] = 0;
            }

            $numberParts = [];
            for ($i = $baseLevel; $i <= $levelNum; $i++) {
                if (isset($counter[$i]) && $counter[$i] > 0) {
                    $numberParts[] = $counter[$i];
                }
            }
            $numberStr = implode('.', $numberParts);

            $cleanText = preg_replace('/^\d+([.\-]\d+)*[.\s\-:]+/u', '', $rawText);
            $cleanText = trim($cleanText) ?: $rawText;

            $headings[] = [
                'id' => $id,
                'text' => $rawText,
                'clean_text' => $cleanText,
                'level' => $levelNum,
                'tag' => $tag,
                'number' => $numberStr,
            ];

            return '<' . $tag . $attrs . '>' . $innerHtml . '</' . $tag . '>';
        }, $content);

        $isTocActive = count($headings) >= $minHeadings;

        // Xử lý vị trí chèn và dọn dẹp shortcode [toc]
        if ($isTocActive) {
            $hasManualShortcode = preg_match('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', $newContent);

            if ($position === 'manual') {
                if ($hasManualShortcode) {
                    $first = true;
                    $newContent = preg_replace_callback('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', function () use (&$first) {
                        if ($first) {
                            $first = false;
                            return '<div id="toc-placeholder" data-position="manual" class="my-6"></div>';
                        }
                        return '';
                    }, $newContent);
                }
            } elseif ($position === 'after_first_heading') {
                // Xóa shortcode thừa nếu có
                $newContent = preg_replace('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', '', $newContent);

                // Chèn placeholder sau thẻ heading đầu tiên
                $firstHeadingPattern = '/(<\/(?:' . implode('|', $headingLevels) . ')>)/i';
                $newContent = preg_replace($firstHeadingPattern, '$1' . "\n" . '<div id="toc-placeholder" data-position="after_first_heading" class="my-6"></div>', $newContent, 1);
            } else { // before_content
                // Xóa shortcode thừa nếu có
                $newContent = preg_replace('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', '', $newContent);
            }
        } else {
            // Khi không đủ heading hoặc tắt, dọn dẹp sạch shortcode
            $newContent = preg_replace('/\[(toc|table-of-contents)\]|<!--\s*toc\s*-->/iu', '', $newContent);
        }

        return [
            'content' => $newContent,
            'headings' => $headings,
            'toc' => $headings,
            'has_toc' => $isTocActive,
            'enabled' => $isTocActive,
            'config' => $resolvedConfig,
            'html' => $isTocActive ? $this->renderToc($headings, $resolvedConfig) : '',
        ];
    }

    /**
     * Render giao diện HTML mặc định của TOC nếu cần dùng độc lập.
     */
    public function renderToc(array $headings, array $config): string
    {
        $title = $config['title'] ?? 'Table of contents';
        $style = $config['style'] ?? 'style-1';
        $showNumbers = $config['show_numbers'] ?? true;
        $collapsible = $config['collapsible'] ?? true;
        $stickyToc = $config['sticky_toc'] ?? false;

        $html = '<nav class="w-full my-8 py-4 px-6 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm toc-container toc-' . htmlspecialchars($style) . '" id="article-toc" aria-label="Table of contents">';
        $html .= '<div class="flex items-center justify-between gap-3 mb-4 pb-2 border-b border-slate-200/60">';
        $html .= '<h3 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">';
        $html .= '<svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg>';
        $html .= htmlspecialchars($title);
        $html .= '</h3>';

        if ($collapsible) {
            $html .= '<button type="button" class="text-xs font-semibold text-sky-600 hover:text-sky-700 bg-white border border-slate-200 px-2.5 py-1 rounded-lg cursor-pointer" onclick="toggleArticleToc(this)">Thu gọn ▲</button>';
        }

        $html .= '</div>';
        $html .= '<div class="space-y-2 text-sm" id="article-toc-body">';

        foreach ($headings as $item) {
            $indent = ($item['level'] - 2) * 16;
            $number = $showNumbers ? htmlspecialchars($item['number']) . '. ' : '';
            $html .= sprintf(
                '<div style="padding-left: %dpx"><a href="#%s" class="text-slate-700 hover:text-sky-600 font-medium transition-colors" data-toc-link="%s"><span class="text-sky-600 font-bold">%s</span>%s</a></div>',
                $indent,
                htmlspecialchars($item['id']),
                htmlspecialchars($item['id']),
                $number,
                htmlspecialchars($item['clean_text'])
            );
        }

        $html .= '</div></nav>';

        return $html;
    }
}

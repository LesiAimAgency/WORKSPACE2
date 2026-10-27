<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\WidgetPermissionService;
use App\Services\WidgetRenderingService;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    /**
     * Show the page builder interface
     */
    public function index(Request $request)
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            abort(403, 'You do not have permission to use page builder');
        }

        $pages = Page::orderBy('title')->get();
        $availableWidgets = $permissionService->getAccessibleWidgetsByCategory();

        return view('cms.page-builder.index', compact('pages', 'availableWidgets'));
    }

    /**
     * Show page builder for specific page
     */
    public function edit(Page $page)
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            abort(403, 'You do not have permission to edit pages');
        }

        $sections = $page->sections()->ordered()->get();
        $availableWidgets = $permissionService->getAccessibleWidgetsByCategory();

        return view('cms.page-builder.edit', compact('page', 'sections', 'availableWidgets'));
    }

    /**
     * Add section to page
     */
    public function addSection(Request $request, Page $page)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
            'order' => 'nullable|integer',
        ]);

        // Validate widget type exists
        if (! WidgetRegistry::exists($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' not found",
            ], 422);
        }

        // Check permissions
        $permissionService = new WidgetPermissionService;
        if (! $permissionService->canAccessWidget($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "You do not have permission to use '{$validated['type']}' widget",
            ], 403);
        }

        try {
            $section = $page->addSection(
                $validated['type'],
                $validated['settings'] ?? [],
                $validated['order'] ?? null
            );

            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section added successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add section: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update section
     */
    public function updateSection(Request $request, PageSection $section)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Validate widget type exists
        if (! WidgetRegistry::exists($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' not found",
            ], 422);
        }

        try {
            $section->update($validated);

            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update section: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete section
     */
    public function deleteSection(PageSection $section)
    {
        try {
            $section->delete();

            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete section: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder sections
     */
    public function reorderSections(Request $request, Page $page)
    {
        $validated = $request->validate([
            'section_ids' => 'required|array',
            'section_ids.*' => 'integer|exists:page_sections,id',
        ]);

        try {
            PageSection::reorderSections($page->id, $validated['section_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Sections reordered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder sections: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move section up
     */
    public function moveSectionUp(PageSection $section)
    {
        try {
            $moved = $section->moveUp();

            return response()->json([
                'success' => $moved,
                'message' => $moved ? 'Section moved up' : 'Section is already at the top',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move section: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move section down
     */
    public function moveSectionDown(PageSection $section)
    {
        try {
            $moved = $section->moveDown();

            return response()->json([
                'success' => $moved,
                'message' => $moved ? 'Section moved down' : 'Section is already at the bottom',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move section: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Preview page with sections
     */
    public function preview(Page $page)
    {
        try {
            $content = $page->getRenderedContent();

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get section preview
     */
    public function previewSection(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
        ]);

        try {
            $renderingService = new WidgetRenderingService;
            $preview = $renderingService->render(
                $validated['type'],
                $validated['settings'] ?? []
            );

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview generation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Duplicate section
     */
    public function duplicateSection(PageSection $section)
    {
        try {
            $newSection = $section->replicate();
            $newSection->order = $section->page->sections()->max('order') + 1;
            $newSection->save();

            return response()->json([
                'success' => true,
                'section' => $newSection,
                'message' => 'Section duplicated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate section: '.$e->getMessage(),
            ], 500);
        }
    }
}

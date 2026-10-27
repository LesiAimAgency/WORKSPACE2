<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    use HasCrudAlerts;

    public function index(Request $request, $projectCode = null): View
    {
        $query = Review::ordered();

        $query->when($request->status, fn ($q) => $q->where('status', $request->status));
        $query->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
            $inner->where('reviewer_name', 'like', "%{$request->search}%")
                ->orWhere('content', 'like', "%{$request->search}%");
        }));

        $reviews = $query->paginate(20)->withQueryString();

        return view('cms.reviews.index', compact('reviews'));
    }

    public function create($projectCode = null): View
    {
        return view('cms.reviews.create');
    }

    public function store(Request $request, $projectCode = null): RedirectResponse
    {
        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'reviewer_avatar' => 'nullable|string|max:500',
            'reviewer_title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? Review::max('sort_order') + 1;
        $validated['tenant_id'] = session('current_tenant_id');

        Review::create($validated);

        $this->alertCreated('đánh giá');

        $code = $projectCode ?? request()->route('projectCode');

        return redirect()->route('project.admin.reviews.index', $code);
    }

    public function edit($projectCode, $reviewId = null): View
    {
        $id = $reviewId ?? $projectCode;
        $review = Review::findOrFail($id);

        return view('cms.reviews.edit', compact('review'));
    }

    public function update(Request $request, $projectCode, $reviewId = null): RedirectResponse
    {
        $id = $reviewId ?? $projectCode;
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'reviewer_avatar' => 'nullable|string|max:500',
            'reviewer_title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $review->update($validated);

        $this->alertUpdated('đánh giá');

        $code = request()->route('projectCode');

        return redirect()->route('project.admin.reviews.edit', [$code, $review->id]);
    }

    public function destroy($projectCode, $reviewId = null): RedirectResponse
    {
        $id = $reviewId ?? $projectCode;
        $review = Review::findOrFail($id);
        $review->delete();

        $this->alertDeleted('đánh giá');

        return redirect()->back();
    }

    public function updateStatus(Request $request, $projectCode, $reviewId = null): RedirectResponse
    {
        $id = $reviewId ?? $projectCode;
        $review = Review::findOrFail($id);

        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $review->update(['status' => $request->status]);

        $this->alertSuccess('Đã cập nhật trạng thái đánh giá.');

        return redirect()->back();
    }

    public function reorder(Request $request, $projectCode = null): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $order => $id) {
            Review::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}

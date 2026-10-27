<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    /**
     * Display a listing of surveys and survey metrics.
     */
    public function index(Request $request): View
    {
        $surveys = Survey::withCount(['responses', 'questions', 'invitations'])->get();

        $totalResponses = SurveyResponse::count();
        $avgScoreA = SurveyResponse::avg('score_a');
        $avgScoreB = SurveyResponse::avg('score_b');
        $avgNps = SurveyResponse::avg('nps_score');

        $promoters = SurveyResponse::where('nps_score', '>=', 9)->count();
        $detractors = SurveyResponse::where('nps_score', '<=', 6)->count();
        $npsIndex = $totalResponses > 0 ? round((($promoters - $detractors) / $totalResponses) * 100, 1) : 0;

        return view('cms.surveys.index', compact(
            'surveys',
            'totalResponses',
            'avgScoreA',
            'avgScoreB',
            'avgNps',
            'npsIndex'
        ));
    }

    /**
     * Show form to edit survey details & manage questions.
     */
    public function edit(Survey $survey): View
    {
        $questionsA = $survey->questions()->where('section', 'A')->orderBy('sort_order')->get();
        $questionsB = $survey->questions()->where('section', 'B')->orderBy('sort_order')->get();
        $questionsNps = $survey->questions()->where('section', 'NPS')->orderBy('sort_order')->get();
        $questionsOpen = $survey->questions()->where('section', 'OPEN')->orderBy('sort_order')->get();

        return view('cms.surveys.edit', compact(
            'survey',
            'questionsA',
            'questionsB',
            'questionsNps',
            'questionsOpen'
        ));
    }

    /**
     * Update the survey information.
     */
    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'intro_note' => 'nullable|string',
            'thank_you_note' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $survey->update($validated);

        return redirect()->route('admin.surveys.edit', $survey->id)->with('success', 'Cập nhật thông tin biểu mẫu khảo sát thành công!');
    }

    /**
     * Store a newly created question for a survey.
     */
    public function storeQuestion(Request $request, Survey $survey): RedirectResponse
    {
        $validated = $request->validate([
            'section' => 'required|string|in:A,B,NPS,OPEN',
            'code' => 'required|string|max:50',
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|string|in:rating_5,rating_10_nps,textarea,text,select,radio',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'code.required' => 'Vui lòng nhập Mã câu hỏi (ví dụ: A7, B5).',
            'question_text.required' => 'Vui lòng nhập Nội dung câu hỏi.',
        ]);

        $validated['survey_id'] = $survey->id;
        $validated['is_required'] = $request->has('is_required');
        $validated['sort_order'] = (int) ($request->input('sort_order') ?? ($survey->questions()->count() + 1));

        SurveyQuestion::create($validated);

        return redirect()->route('admin.surveys.edit', $survey->id)->with('success', 'Đã thêm câu hỏi mới thành công!');
    }

    /**
     * Update an existing question.
     */
    public function updateQuestion(Request $request, SurveyQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'section' => 'required|string|in:A,B,NPS,OPEN',
            'code' => 'required|string|max:50',
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|string|in:rating_5,rating_10_nps,textarea,text,select,radio',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['sort_order'] = (int) ($request->input('sort_order') ?? 0);

        $question->update($validated);

        return redirect()->route('admin.surveys.edit', $question->survey_id)->with('success', 'Cập nhật câu hỏi thành công!');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(SurveyQuestion $question): RedirectResponse
    {
        $surveyId = $question->survey_id;
        $question->delete();

        return redirect()->route('admin.surveys.edit', $surveyId)->with('success', 'Đã xóa câu hỏi thành công!');
    }

    /**
     * Reorder questions in bulk.
     */
    public function reorderQuestions(Request $request): JsonResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (! empty($item['id'])) {
                SurveyQuestion::where('id', $item['id'])->update(['sort_order' => (int) ($item['order'] ?? 0)]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * List all customer responses for a survey.
     */
    public function responses(Request $request, Survey $survey): View
    {
        $query = $survey->responses()->with('answers');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                    ->orWhere('project_name', 'like', "%{$s}%")
                    ->orWhere('pm_name', 'like', "%{$s}%")
                    ->orWhere('customer_email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('nps_filter')) {
            $filter = $request->nps_filter;
            if ($filter === 'promoter') {
                $query->where('nps_score', '>=', 9);
            } elseif ($filter === 'passive') {
                $query->whereBetween('nps_score', [7, 8]);
            } elseif ($filter === 'detractor') {
                $query->where('nps_score', '<=', 6);
            }
        }

        $responses = $query->latest()->paginate(20)->withQueryString();

        return view('cms.surveys.responses', compact('survey', 'responses'));
    }

    /**
     * Show detail of a single response.
     */
    public function showResponse(SurveyResponse $response): View
    {
        $response->load(['survey', 'answers.question', 'invitation']);

        return view('cms.surveys.response-detail', compact('response'));
    }

    /**
     * Delete a single survey response.
     */
    public function destroyResponse(SurveyResponse $response): RedirectResponse
    {
        $surveyId = $response->survey_id;
        $response->delete();

        return redirect()->route('admin.surveys.responses.index', $surveyId)->with('success', 'Đã xóa phản hồi khảo sát thành công!');
    }
}

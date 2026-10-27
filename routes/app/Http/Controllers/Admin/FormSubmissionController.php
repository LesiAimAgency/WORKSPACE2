<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormSubmissionController extends Controller
{
    use HasCrudAlerts;

    public function index(Request $request, $projectCode = null): View
    {
        $query = FormSubmission::latest();

        $query->when($request->status, fn ($q) => $q->where('status', $request->status));
        $query->when($request->form_name, fn ($q) => $q->where('form_name', $request->form_name));
        $query->when($request->search, function ($q) use ($request) {
            $q->where('data', 'like', "%{$request->search}%");
        });

        $submissions = $query->paginate(20)->withQueryString();

        $formNames = FormSubmission::distinct()->pluck('form_name')->filter()->values();

        return view('cms.form-submissions.index', compact('submissions', 'formNames'));
    }

    public function show($projectCode, $submissionId = null): View
    {
        $id = $submissionId ?? $projectCode;
        $submission = FormSubmission::findOrFail($id);

        return view('cms.form-submissions.show', compact('submission'));
    }

    public function updateStatus(Request $request, $projectCode, $submissionId = null): RedirectResponse
    {
        $id = $submissionId ?? $projectCode;
        $submission = FormSubmission::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        $this->alertSuccess('Đã cập nhật trạng thái.');

        return redirect()->back();
    }

    public function destroy($projectCode, $submissionId = null): RedirectResponse
    {
        $id = $submissionId ?? $projectCode;
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        $this->alertDeleted('form submission');

        return redirect()->back();
    }

    public function destroyAll(Request $request, $projectCode = null): RedirectResponse
    {
        $query = FormSubmission::query();

        if ($request->boolean('filtered', false)) {
            $query->when($request->status, fn ($q) => $q->where('status', $request->status));
            $query->when($request->form_name, fn ($q) => $q->where('form_name', $request->form_name));
            $query->when($request->search, function ($q) use ($request) {
                $q->where('data', 'like', "%{$request->search}%");
            });
            $count = $query->count();
            $query->delete();
            $this->alertSuccess("Đã xóa {$count} form liên hệ theo bộ lọc thành công!");
        } else {
            $count = $query->count();
            $query->delete();
            $this->alertSuccess("Đã xóa toàn bộ {$count} form liên hệ thành công!");
        }

        return redirect()->route('admin.form-submissions.index');
    }

    public function bulkDelete(Request $request, $projectCode = null): RedirectResponse
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }

        $ids = array_filter(array_map('intval', (array) $ids));

        if (!empty($ids)) {
            $count = FormSubmission::whereIn('id', $ids)->delete();
            $this->alertSuccess("Đã xóa {$count} form liên hệ đã chọn thành công!");
        } else {
            $this->alertWarning("Vui lòng chọn ít nhất một form liên hệ để xóa.");
        }

        return redirect()->back();
    }
}

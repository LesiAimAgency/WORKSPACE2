<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormSubmissionController extends Controller
{
    public function submit(Request $request)
    {
        $formName = $request->input('form_name', 'Contact');
        $forms = json_decode(setting('forms', '[]'), true);
        $form = collect($forms)->firstWhere('name', $formName);

        $data = [];
        if (! $form) {
            $inputs = $request->except(['_token', 'form_name', 'project_id']);
            foreach ($inputs as $key => $value) {
                $data[$key] = is_string($value) ? strip_tags($value) : $value;
            }
        } else {
            // Build validation rules
            $rules = [];
            foreach ($form['fields'] as $field) {
                $fieldRules = [];
                if ($field['required']) {
                    $fieldRules[] = 'required';
                }
                if ($field['type'] === 'email') {
                    $fieldRules[] = 'email';
                }
                if ($field['type'] === 'phone') {
                    $fieldRules[] = 'regex:/^[0-9]{10,11}$/';
                }
                $rules[$field['label']] = $fieldRules;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Sanitize data
            foreach ($form['fields'] as $field) {
                $value = $request->input($field['label']);
                $data[$field['label']] = is_string($value) ? strip_tags($value) : $value;
            }
        }

        // Save to database
        $submission = FormSubmission::create([
            'form_name' => $formName,
            'data' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending',
            'project_id' => $request->input('project_id'),
            'tenant_id' => $request->input('project_id'),
        ]);

        // Send email notifications in background after response
        \App\Services\MailConfigService::dispatchAsync($submission);

        return response()->json(['success' => true, 'message' => 'Đã gửi thành công']);
    }

    public function index()
    {
        $submissions = FormSubmission::latest()->paginate(20);

        return view('cms.form-submissions.index', compact('submissions'));
    }

    public function updateStatus(Request $request, $id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái');
    }

    public function destroy($id)
    {
        FormSubmission::findOrFail($id)->delete();

        return back()->with('success', 'Đã xóa');
    }
}

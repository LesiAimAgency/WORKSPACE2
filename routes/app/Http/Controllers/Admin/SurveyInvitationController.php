<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SurveyInvitationMail;
use App\Models\Survey;
use App\Models\SurveyInvitation;
use App\Services\MailConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SurveyInvitationController extends Controller
{
    /**
     * Display a listing of invitations and sending form.
     */
    public function index(Request $request, Survey $survey): View
    {
        $query = $survey->invitations()->with('response');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('recipient_email', 'like', "%{$s}%")
                    ->orWhere('recipient_name', 'like', "%{$s}%")
                    ->orWhere('project_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invitations = $query->latest()->paginate(20)->withQueryString();

        $defaultSubject = setting('survey_mail_subject', 'Thư mời đánh giá chất lượng dịch vụ & công trình - Workspace');
        $defaultTemplate = setting('survey_mail_template', "Kính gửi Quý Khách hàng,\n\nNhằm không ngừng nâng cao chất lượng thi công và dịch vụ bảo hành, Workspace trân trọng kính mời Quý Khách hàng dành ít phút đánh giá sự hài lòng về dự án vừa qua.\n\nÝ kiến đóng góp quý báu của Quý vị là kim chỉ nam giúp chúng tôi hoàn thiện hơn mỗi ngày.\n\nTrân trọng cảm ơn!");

        return view('cms.surveys.invitations', compact(
            'survey',
            'invitations',
            'defaultSubject',
            'defaultTemplate'
        ));
    }

    /**
     * Send survey invitations to single or multiple recipients.
     */
    public function send(Request $request, Survey $survey): RedirectResponse
    {
        $validated = $request->validate([
            'emails' => 'required|string',
            'recipient_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'custom_message' => 'required|string|max:5000',
        ], [
            'emails.required' => 'Vui lòng nhập ít nhất một địa chỉ email người nhận.',
            'subject.required' => 'Vui lòng nhập Tiêu đề email.',
            'custom_message.required' => 'Vui lòng nhập Nội dung thư mời.',
        ]);

        // Parse emails (split by comma, semicolon, space, or newline)
        $rawEmails = preg_split('/[\r\n,; ]+/', $validated['emails']);
        $emails = array_filter(array_unique(array_map('trim', $rawEmails)), function ($e) {
            return filter_var($e, FILTER_VALIDATE_EMAIL);
        });

        if (empty($emails)) {
            return back()->withErrors(['emails' => 'Không tìm thấy địa chỉ email hợp lệ nào trong danh sách.'])->withInput();
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        // Apply runtime SMTP configuration from DB
        MailConfigService::applyConfig();

        foreach ($emails as $email) {
            $token = Str::random(40);

            $invitation = SurveyInvitation::create([
                'survey_id' => $survey->id,
                'recipient_email' => $email,
                'recipient_name' => $validated['recipient_name'] ?: null,
                'project_name' => $validated['project_name'] ?: null,
                'token' => $token,
                'status' => 'pending',
                'attempts' => 1,
            ]);

            try {
                Mail::to($email)->send(
                    new SurveyInvitationMail($invitation, $validated['custom_message'], $validated['subject'])
                );

                $invitation->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]);

                $successCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $cleanError = preg_replace('/password=[^&]+/i', 'password=***', $e->getMessage());
                $invitation->update([
                    'status' => 'failed',
                    'error_message' => $cleanError,
                ]);

                $errors[] = "Lỗi gửi tới {$email}: " . $cleanError;
                Log::error("Failed sending survey invitation to {$email}: " . $cleanError);
            }
        }

        $msg = "Đã xử lý gửi thư mời: {$successCount} email thành công";
        if ($failedCount > 0) {
            $msg .= ", {$failedCount} thất bại (Vui lòng kiểm tra lại cấu hình SMTP trong Cài đặt Hệ thống).";
        }

        return redirect()->route('admin.surveys.invitations.index', $survey->id)
            ->with($failedCount > 0 ? 'warning' : 'success', $msg)
            ->with('mail_errors', $errors);
    }

    /**
     * Retry sending a failed invitation.
     */
    public function retry(SurveyInvitation $invitation): RedirectResponse
    {
        MailConfigService::applyConfig();

        $invitation->increment('attempts');

        try {
            Mail::to($invitation->recipient_email)->send(
                new SurveyInvitationMail($invitation)
            );

            $invitation->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            return back()->with('success', "Đã gửi lại thành công thư mời cho {$invitation->recipient_email}!");
        } catch (\Throwable $e) {
            $cleanError = preg_replace('/password=[^&]+/i', 'password=***', $e->getMessage());
            $invitation->update([
                'status' => 'failed',
                'error_message' => $cleanError,
            ]);

            return back()->with('error', "Gửi lại thất bại: " . $cleanError);
        }
    }

    /**
     * Delete an invitation.
     */
    public function destroy(SurveyInvitation $invitation): RedirectResponse
    {
        $surveyId = $invitation->survey_id;
        $invitation->delete();

        return redirect()->route('admin.surveys.invitations.index', $surveyId)
            ->with('success', 'Đã xóa bản ghi thư mời thành công!');
    }
}

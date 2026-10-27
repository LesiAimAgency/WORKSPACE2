<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\SurveyInvitationMail;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyInvitation;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Services\MailConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CustomerSurveyController extends Controller
{
    /**
     * Display the Customer Satisfaction Survey form.
     */
    public function show(Request $request): View
    {
        $survey = Survey::where('is_active', true)->first();

        if (! $survey) {
            $survey = Survey::firstOrCreate(
                ['code' => 'online_khao_sat_kh'],
                [
                    'title' => 'KHẢO SÁT HÀI LÒNG',
                    'subtitle' => 'WORKSPACE®',
                    'intro_note' => 'Kính chào Quý Khách hàng, nhằm không ngừng nâng cao chất lượng dịch vụ, chúng tôi kính mong Quý vị dành ít phút đánh giá về chất lượng công trình và dịch vụ của chúng tôi.',
                    'thank_you_note' => 'Cảm ơn Quý Khách hàng đã tin tưởng và đồng hành cùng chúng tôi',
                    'footer_text' => 'Trân trọng cảm ơn Quý Khách hàng!',
                    'is_active' => true,
                ]
            );
        }

        $questionsA = $survey->questions()->where('section', 'A')->orderBy('sort_order')->get();
        $questionsB = $survey->questions()->where('section', 'B')->orderBy('sort_order')->get();
        $questionNps = $survey->questions()->where('section', 'NPS')->first();
        $openQuestions = $survey->questions()->where('section', 'OPEN')->orderBy('sort_order')->get();

        // Check for invitation token
        $invitation = null;
        $prefill = [
            'tenKH' => '',
            'tenDA' => '',
            'ngayBG' => '',
            'qlda' => '',
            'email' => '',
        ];

        $token = $request->query('invitation') ?: $request->query('token');
        if ($token) {
            $invitation = SurveyInvitation::where('token', $token)->first();
            if ($invitation) {
                $prefill['tenKH'] = $invitation->recipient_name ?: '';
                $prefill['tenDA'] = $invitation->project_name ?: '';
                $prefill['email'] = $invitation->recipient_email ?: '';
            }
        }

        return view('pages.customer-survey', compact(
            'survey',
            'questionsA',
            'questionsB',
            'questionNps',
            'openQuestions',
            'invitation',
            'prefill'
        ));
    }

    /**
     * Handle survey submission, validation, and storage.
     */
    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'survey_id' => 'nullable|exists:surveys,id',
            'tenKH' => 'required|string|max:255',
            'tenDA' => 'required|string|max:255',
            'ngayBG' => 'required|date',
            'qlda' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'answers_a' => 'nullable|array',
            'answers_b' => 'nullable|array',
            'nps_score' => 'required|integer|min:0|max:10',
            'open_feedback_best' => 'nullable|string|max:5000',
            'open_feedback_improve' => 'nullable|string|max:5000',
            'invitation_token' => 'nullable|string|max:64',
        ], [
            'tenKH.required' => 'Vui lòng nhập Tên khách hàng / Công ty.',
            'tenDA.required' => 'Vui lòng nhập Tên dự án.',
            'ngayBG.required' => 'Vui lòng chọn Ngày bàn giao công trình.',
            'ngayBG.date' => 'Ngày bàn giao không đúng định dạng ngày tháng.',
            'nps_score.required' => 'Vui lòng chọn điểm đánh giá mức độ sẵn sàng giới thiệu (0-10).',
            'nps_score.integer' => 'Điểm NPS phải là số nguyên từ 0 đến 10.',
            'customer_email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $surveyId = $validated['survey_id'] ?? null;
        if (! $surveyId) {
            $survey = Survey::where('is_active', true)->first();
            $surveyId = $survey ? $survey->id : 1;
        }

        // Calculate Average Scores
        $scoresA = array_filter($request->input('answers_a', []), fn ($v) => $v !== null && $v !== '');
        $avgScoreA = count($scoresA) > 0 ? round(array_sum($scoresA) / count($scoresA), 1) : null;

        $scoresB = array_filter($request->input('answers_b', []), fn ($v) => $v !== null && $v !== '');
        $avgScoreB = count($scoresB) > 0 ? round(array_sum($scoresB) / count($scoresB), 1) : null;

        $invitation = null;
        if (! empty($validated['invitation_token'])) {
            $invitation = SurveyInvitation::where('token', $validated['invitation_token'])->first();
        }

        $responseRecord = null;

        DB::transaction(function () use ($validated, $surveyId, $avgScoreA, $avgScoreB, $invitation, $request, &$responseRecord) {
            $responseRecord = SurveyResponse::create([
                'survey_id' => $surveyId,
                'invitation_id' => $invitation?->id,
                'customer_name' => $validated['tenKH'],
                'project_name' => $validated['tenDA'],
                'handover_date' => $validated['ngayBG'],
                'pm_name' => $validated['qlda'] ?? null,
                'customer_email' => $validated['customer_email'] ?? ($invitation?->recipient_email ?? null),
                'customer_phone' => $validated['customer_phone'] ?? null,
                'score_a' => $avgScoreA,
                'score_b' => $avgScoreB,
                'nps_score' => (int) $validated['nps_score'],
                'open_feedback_best' => $validated['open_feedback_best'] ?? null,
                'open_feedback_improve' => $validated['open_feedback_improve'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'completed',
                'submitted_at' => now(),
            ]);

            // Save Part A answers
            $rawAnswersA = $request->input('answers_a', []);
            foreach ($rawAnswersA as $code => $val) {
                if ($val !== null && $val !== '') {
                    $question = SurveyQuestion::where('survey_id', $surveyId)
                        ->where('code', $code)
                        ->first();

                    SurveyAnswer::create([
                        'response_id' => $responseRecord->id,
                        'question_id' => $question?->id,
                        'question_code' => (string) $code,
                        'rating_value' => is_numeric($val) ? (int) $val : null,
                        'text_value' => is_string($val) ? $val : null,
                    ]);
                }
            }

            // Save Part B answers
            $rawAnswersB = $request->input('answers_b', []);
            foreach ($rawAnswersB as $code => $val) {
                if ($val !== null && $val !== '') {
                    $question = SurveyQuestion::where('survey_id', $surveyId)
                        ->where('code', $code)
                        ->first();

                    SurveyAnswer::create([
                        'response_id' => $responseRecord->id,
                        'question_id' => $question?->id,
                        'question_code' => (string) $code,
                        'rating_value' => is_numeric($val) ? (int) $val : null,
                        'text_value' => is_string($val) ? $val : null,
                    ]);
                }
            }

            // Save NPS answer
            $qNps = SurveyQuestion::where('survey_id', $surveyId)->where('section', 'NPS')->first();
            SurveyAnswer::create([
                'response_id' => $responseRecord->id,
                'question_id' => $qNps?->id,
                'question_code' => 'B5',
                'rating_value' => (int) $validated['nps_score'],
                'text_value' => (string) $validated['nps_score'],
            ]);

            // Save Open Question answers
            if (! empty($validated['open_feedback_best'])) {
                $qBest = SurveyQuestion::where('survey_id', $surveyId)->where('code', 'OPEN_1')->first();
                SurveyAnswer::create([
                    'response_id' => $responseRecord->id,
                    'question_id' => $qBest?->id,
                    'question_code' => 'OPEN_1',
                    'text_value' => $validated['open_feedback_best'],
                ]);
            }

            if (! empty($validated['open_feedback_improve'])) {
                $qImp = SurveyQuestion::where('survey_id', $surveyId)->where('code', 'OPEN_2')->first();
                SurveyAnswer::create([
                    'response_id' => $responseRecord->id,
                    'question_id' => $qImp?->id,
                    'question_code' => 'OPEN_2',
                    'text_value' => $validated['open_feedback_improve'],
                ]);
            }

            // Mark invitation responded
            if ($invitation) {
                $invitation->update([
                    'status' => 'responded',
                    'responded_at' => now(),
                ]);
            }
        });

        // Send Email Notification to Admin if configured
        try {
            $adminEmail = MailConfigService::getAdminReceiverEmail();
            $enableNotification = setting('mail_admin_notification_enabled', '1') !== '0';
            if ($enableNotification && ! empty($adminEmail) && $responseRecord) {
                MailConfigService::applyConfig();
                Mail::send('emails.survey-response-notification', [
                    'surveyResponse' => $responseRecord,
                ], function ($message) use ($adminEmail, $responseRecord) {
                    $message->to($adminEmail)
                        ->subject("🔔 Đánh giá khách hàng mới: {$responseRecord->customer_name} - {$responseRecord->project_name}");
                });
            }
        } catch (\Throwable $e) {
            Log::warning('CustomerSurveyController: Could not send admin notification email: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Gửi đánh giá thành công! Cảm ơn Quý khách hàng.',
                'response_id' => $responseRecord?->id,
                'customer_name' => $responseRecord?->customer_name,
                'project_name' => $responseRecord?->project_name,
                'score_a' => $avgScoreA !== null ? number_format($avgScoreA, 1) : '--',
                'score_b' => $avgScoreB !== null ? number_format($avgScoreB, 1) : '--',
                'nps_score' => $responseRecord?->nps_score,
            ]);
        }

        return redirect()->route('customer-survey.show')
            ->with('success', 'Gửi đánh giá thành công!')
            ->with('submitted_response', $responseRecord);
    }
}

<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;
use Illuminate\Support\Facades\DB;

class FormAnalyticsService
{
    /**
     * Get aggregate analytics summary across all forms
     */
    public function getAllFormsSummary(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        // Check if we have any analytics session data
        $hasAnalyticsData = SlickFormAnalyticsSession::exists();

        if (! $hasAnalyticsData) {
            // Fallback to submission-based stats
            return $this->getAllFormsSubmissionBasedSummary($startDate);
        }

        return [
            'total_views' => $this->getAllFormsTotalViews($startDate),
            'total_starts' => $this->getAllFormsTotalStarts($startDate),
            'total_submissions' => $this->getAllFormsTotalSubmissions($startDate),
            'total_abandoned' => $this->getAllFormsTotalAbandoned($startDate),
            'completion_rate' => $this->getAllFormsCompletionRate($startDate),
            'average_time_seconds' => $this->getAllFormsAverageCompletionTime($startDate),
            'abandonment_rate' => $this->getAllFormsAbandonmentRate($startDate),
        ];
    }

    /**
     * Get analytics summary for a form
     */
    public function getFormSummary(int $formId, ?int $days = 30): array
    {
        $form = CustomForm::findOrFail($formId);

        $startDate = now()->subDays($days);

        // Check if we have analytics session data
        $hasAnalyticsData = SlickFormAnalyticsSession::where('slick_form_id', $formId)->exists();

        if (! $hasAnalyticsData) {
            // Fallback to submission-based stats
            return $this->getSubmissionBasedSummary($formId, $startDate);
        }

        return [
            'total_views' => $this->getTotalViews($formId, $startDate),
            'total_starts' => $this->getTotalStarts($formId, $startDate),
            'total_submissions' => $this->getTotalSubmissions($formId, $startDate),
            'total_abandoned' => $this->getTotalAbandoned($formId, $startDate),
            'completion_rate' => $this->getCompletionRate($formId, $startDate),
            'average_time_seconds' => $this->getAverageCompletionTime($formId, $startDate),
            'abandonment_rate' => $this->getAbandonmentRate($formId, $startDate),
        ];
    }

    /**
     * Get submission count over time
     */
    public function getSubmissionsOverTime(int $formId, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        // Check if we have analytics session data
        $hasAnalyticsData = SlickFormAnalyticsSession::where('slick_form_id', $formId)->exists();

        if (! $hasAnalyticsData) {
            // Fallback to submission data
            return \DigitalisStudios\SlickForms\Models\CustomFormSubmission::where('slick_form_id', $formId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        }

        return SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get field completion rates
     */
    public function getFieldCompletionRates(int $formId): array
    {
        $form = CustomForm::with('fields')->findOrFail($formId);

        $totalStarts = $this->getTotalStarts($formId);

        if ($totalStarts === 0) {
            return [];
        }

        $fieldStats = [];

        foreach ($form->fields as $field) {
            $interactions = DB::table('slick_form_analytics_events')
                ->join('slick_form_analytics_sessions', 'slick_form_analytics_events.slick_form_analytics_session_id', '=', 'slick_form_analytics_sessions.id')
                ->where('slick_form_analytics_sessions.slick_form_id', $formId)
                ->where('slick_form_analytics_events.slick_form_field_id', $field->id)
                ->whereIn('slick_form_analytics_events.event_type', ['field_focus', 'field_change'])
                ->distinct('slick_form_analytics_sessions.id')
                ->count('slick_form_analytics_sessions.id');

            $fieldStats[] = [
                'field_id' => $field->id,
                'field_label' => $field->label,
                'field_type' => $field->field_type,
                'interactions' => $interactions,
                'completion_rate' => $totalStarts > 0 ? round(($interactions / $totalStarts) * 100, 2) : 0,
            ];
        }

        return $fieldStats;
    }

    /**
     * Get device breakdown
     */
    public function getDeviceBreakdown(int $formId, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

    /**
     * Get browser breakdown
     */
    public function getBrowserBreakdown(int $formId, ?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('browser')
            ->selectRaw('browser, COUNT(*) as count')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get drop-off points for multi-page forms
     */
    public function getDropOffPoints(int $formId): array
    {
        $form = CustomForm::findOrFail($formId);

        if (! $form->isMultiPage()) {
            return [];
        }

        $pages = $form->pages()->orderBy('order')->get();

        $dropOffStats = [];

        foreach ($pages as $index => $page) {
            $viewedThisPage = SlickFormAnalyticsSession::where('slick_form_id', $formId)
                ->where('current_page_index', '>=', $index)
                ->count();

            $leftOnThisPage = SlickFormAnalyticsSession::where('slick_form_id', $formId)
                ->where('current_page_index', $index)
                ->whereNotNull('abandoned_at')
                ->count();

            $dropOffStats[] = [
                'page_index' => $index,
                'page_title' => $page->title,
                'views' => $viewedThisPage,
                'abandoned' => $leftOnThisPage,
                'drop_off_rate' => $viewedThisPage > 0 ? round(($leftOnThisPage / $viewedThisPage) * 100, 2) : 0,
            ];
        }

        return $dropOffStats;
    }

    /**
     * Get most common validation errors
     */
    public function getCommonValidationErrors(int $formId, int $limit = 10): array
    {
        return DB::table('slick_form_analytics_events')
            ->join('slick_form_analytics_sessions', 'slick_form_analytics_events.slick_form_analytics_session_id', '=', 'slick_form_analytics_sessions.id')
            ->join('slick_form_fields', 'slick_form_analytics_events.slick_form_field_id', '=', 'slick_form_fields.id')
            ->where('slick_form_analytics_sessions.slick_form_id', $formId)
            ->where('slick_form_analytics_events.event_type', 'validation_error')
            ->selectRaw('slick_form_fields.label, slick_form_fields.field_type, COUNT(*) as error_count')
            ->groupBy('slick_form_fields.id', 'slick_form_fields.label', 'slick_form_fields.field_type')
            ->orderByDesc('error_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    // Helper methods

    protected function getTotalViews(int $formId, ?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::where('slick_form_id', $formId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getTotalStarts(int $formId, ?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->whereNotNull('started_at');

        if ($startDate) {
            $query->where('started_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getTotalSubmissions(int $formId, ?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->whereNotNull('submitted_at');

        if ($startDate) {
            $query->where('submitted_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getTotalAbandoned(int $formId, ?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->whereNotNull('abandoned_at')
            ->whereNull('submitted_at');

        if ($startDate) {
            $query->where('abandoned_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getCompletionRate(int $formId, ?\Carbon\Carbon $startDate = null): float
    {
        $starts = $this->getTotalStarts($formId, $startDate);

        if ($starts === 0) {
            return 0.0;
        }

        $submissions = $this->getTotalSubmissions($formId, $startDate);

        return round(($submissions / $starts) * 100, 2);
    }

    protected function getAbandonmentRate(int $formId, ?\Carbon\Carbon $startDate = null): float
    {
        $starts = $this->getTotalStarts($formId, $startDate);

        if ($starts === 0) {
            return 0.0;
        }

        $abandoned = $this->getTotalAbandoned($formId, $startDate);

        return round(($abandoned / $starts) * 100, 2);
    }

    protected function getAverageCompletionTime(int $formId, ?\Carbon\Carbon $startDate = null): ?int
    {
        $query = SlickFormAnalyticsSession::where('slick_form_id', $formId)
            ->whereNotNull('submitted_at')
            ->whereNotNull('time_spent_seconds');

        if ($startDate) {
            $query->where('submitted_at', '>=', $startDate);
        }

        return (int) $query->avg('time_spent_seconds');
    }

    /**
     * Get summary based on submission data (fallback when no analytics data)
     */
    protected function getSubmissionBasedSummary(int $formId, ?\Carbon\Carbon $startDate = null): array
    {
        $submissionsQuery = \DigitalisStudios\SlickForms\Models\CustomFormSubmission::where('slick_form_id', $formId);

        if ($startDate) {
            $submissionsQuery->where('created_at', '>=', $startDate);
        }

        $totalSubmissions = $submissionsQuery->count();

        return [
            'total_views' => $totalSubmissions, // We don't track views without analytics
            'total_starts' => $totalSubmissions, // Assume all submissions = starts
            'total_submissions' => $totalSubmissions,
            'total_abandoned' => 0, // Can't calculate without analytics
            'completion_rate' => 100.0, // 100% since we only have successful submissions
            'average_time_seconds' => null, // Can't calculate without analytics
            'abandonment_rate' => 0.0, // Can't calculate without analytics
        ];
    }

    // Aggregate methods for all forms

    protected function getAllFormsTotalViews(?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getAllFormsTotalStarts(?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::whereNotNull('started_at');

        if ($startDate) {
            $query->where('started_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getAllFormsTotalSubmissions(?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::whereNotNull('submitted_at');

        if ($startDate) {
            $query->where('submitted_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getAllFormsTotalAbandoned(?\Carbon\Carbon $startDate = null): int
    {
        $query = SlickFormAnalyticsSession::whereNotNull('abandoned_at')
            ->whereNull('submitted_at');

        if ($startDate) {
            $query->where('abandoned_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getAllFormsCompletionRate(?\Carbon\Carbon $startDate = null): float
    {
        $starts = $this->getAllFormsTotalStarts($startDate);

        if ($starts === 0) {
            return 0.0;
        }

        $submissions = $this->getAllFormsTotalSubmissions($startDate);

        return round(($submissions / $starts) * 100, 2);
    }

    protected function getAllFormsAbandonmentRate(?\Carbon\Carbon $startDate = null): float
    {
        $starts = $this->getAllFormsTotalStarts($startDate);

        if ($starts === 0) {
            return 0.0;
        }

        $abandoned = $this->getAllFormsTotalAbandoned($startDate);

        return round(($abandoned / $starts) * 100, 2);
    }

    protected function getAllFormsAverageCompletionTime(?\Carbon\Carbon $startDate = null): ?int
    {
        $query = SlickFormAnalyticsSession::whereNotNull('submitted_at')
            ->whereNotNull('time_spent_seconds');

        if ($startDate) {
            $query->where('submitted_at', '>=', $startDate);
        }

        return (int) $query->avg('time_spent_seconds');
    }

    protected function getAllFormsSubmissionBasedSummary(?\Carbon\Carbon $startDate = null): array
    {
        $submissionsQuery = \DigitalisStudios\SlickForms\Models\CustomFormSubmission::query();

        if ($startDate) {
            $submissionsQuery->where('created_at', '>=', $startDate);
        }

        $totalSubmissions = $submissionsQuery->count();

        return [
            'total_views' => $totalSubmissions,
            'total_starts' => $totalSubmissions,
            'total_submissions' => $totalSubmissions,
            'total_abandoned' => 0,
            'completion_rate' => 100.0,
            'average_time_seconds' => null,
            'abandonment_rate' => 0.0,
        ];
    }

    /**
     * Get device breakdown across all forms
     */
    public function getAllFormsDeviceBreakdown(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return SlickFormAnalyticsSession::where('created_at', '>=', $startDate)
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

    /**
     * Get submissions over time across all forms
     */
    public function getAllFormsSubmissionsOverTime(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $hasAnalyticsData = SlickFormAnalyticsSession::exists();

        if (! $hasAnalyticsData) {
            return \DigitalisStudios\SlickForms\Models\CustomFormSubmission::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        }

        return SlickFormAnalyticsSession::whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}

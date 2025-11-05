<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Services\FormAnalyticsService;
use Livewire\Component;

class ManageStats extends Component
{
    public int $days = 30;

    public function render()
    {
        // Don't render anything if analytics feature is disabled
        if (! slick_forms_feature_enabled('analytics')) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        $analyticsService = app(FormAnalyticsService::class);

        $analytics = $analyticsService->getAllFormsSummary($this->days);
        $deviceBreakdown = $analyticsService->getAllFormsDeviceBreakdown($this->days);
        $submissionsOverTime = $analyticsService->getAllFormsSubmissionsOverTime($this->days);

        return view('slick-forms::livewire.manage-stats', [
            'analytics' => $analytics,
            'deviceBreakdown' => $deviceBreakdown,
            'submissionsOverTime' => $submissionsOverTime,
        ]);
    }
}

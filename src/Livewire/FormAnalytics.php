<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\FormAnalyticsService;
use Livewire\Component;

class FormAnalytics extends Component
{
    public CustomForm $form;

    public int $days = 30;

    public array $summary = [];

    public array $submissionsOverTime = [];

    public array $fieldCompletionRates = [];

    public array $deviceBreakdown = [];

    public array $browserBreakdown = [];

    public array $dropOffPoints = [];

    public array $validationErrors = [];

    public function mount(int $formId): void
    {
        $this->form = CustomForm::findOrFail($formId);

        if (slick_forms_feature_enabled('analytics')) {
            $this->loadAnalytics();
        }
    }

    public function updatedDays(): void
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics(): void
    {
        $analyticsService = app(FormAnalyticsService::class);

        $this->summary = $analyticsService->getFormSummary($this->form->id, $this->days);
        $this->submissionsOverTime = $analyticsService->getSubmissionsOverTime($this->form->id, $this->days);
        $this->fieldCompletionRates = $analyticsService->getFieldCompletionRates($this->form->id);
        $this->deviceBreakdown = $analyticsService->getDeviceBreakdown($this->form->id, $this->days);
        $this->browserBreakdown = $analyticsService->getBrowserBreakdown($this->form->id, $this->days);
        $this->validationErrors = $analyticsService->getCommonValidationErrors($this->form->id);

        if ($this->form->isMultiPage()) {
            $this->dropOffPoints = $analyticsService->getDropOffPoints($this->form->id);
        }
    }

    public function render()
    {
        // Don't render anything if analytics feature is disabled
        if (! slick_forms_feature_enabled('analytics')) {
            return view('slick-forms::livewire.feature-disabled', [
                'feature' => 'Analytics',
                'message' => 'The analytics feature is currently disabled.',
            ]);
        }

        return view('slick-forms::livewire.form-analytics');
    }
}

<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormSpamLog;
use Livewire\Component;
use Livewire\WithPagination;

class SpamLogsViewer extends Component
{
    use WithPagination;

    public CustomForm $form;

    public string $methodFilter = 'all'; // all, honeypot, rate_limit, recaptcha, hcaptcha

    public string $searchQuery = '';

    public string $dateFilter = 'last_7_days'; // all, today, last_7_days, last_30_days

    public ?int $selectedLogId = null;

    public function mount(int $formId): void
    {
        $this->form = CustomForm::findOrFail($formId);

        // Feature check happens in render()
    }

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function viewLogDetails(int $logId): void
    {
        $this->selectedLogId = $logId;
    }

    public function closeLogDetails(): void
    {
        $this->selectedLogId = null;
    }

    public function getLogsProperty()
    {
        $query = FormSpamLog::query()
            ->where('form_id', $this->form->id);

        // Method filter
        if ($this->methodFilter !== 'all') {
            $query->where('detection_method', $this->methodFilter);
        }

        // Date filter
        if ($this->dateFilter !== 'all') {
            $date = match ($this->dateFilter) {
                'today' => now()->startOfDay(),
                'last_7_days' => now()->subDays(7),
                'last_30_days' => now()->subDays(30),
                default => null,
            };

            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }

        // Search filter (search in IP address)
        if (! empty($this->searchQuery)) {
            $query->where('ip_address', 'like', '%'.$this->searchQuery.'%');
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getSelectedLogProperty()
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return FormSpamLog::find($this->selectedLogId);
    }

    public function render()
    {
        // Don't render if spam logs feature is disabled
        if (! slick_forms_feature_enabled('spam_logs')) {
            return view('slick-forms::livewire.feature-disabled', [
                'feature' => 'Spam Protection Logs',
                'message' => 'The spam protection logging feature is currently disabled.',
            ]);
        }

        return view('slick-forms::livewire.spam-logs-viewer', [
            'logs' => $this->logs,
            'selectedLog' => $this->selectedLog,
        ]);
    }
}

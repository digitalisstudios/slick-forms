<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormEmailLog;
use Livewire\Component;
use Livewire\WithPagination;

class EmailLogsViewer extends Component
{
    use WithPagination;

    public CustomForm $form;

    public string $statusFilter = 'all'; // all, sent, failed, queued

    public string $searchQuery = '';

    public string $dateFilter = 'last_7_days'; // all, today, last_7_days, last_30_days

    public ?int $selectedLogId = null;

    public function mount(int $formId): void
    {
        $this->form = CustomForm::findOrFail($formId);
    }

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
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
        $query = FormEmailLog::query()
            ->whereHas('submission', function ($q) {
                $q->where('slick_form_id', $this->form->id);
            })
            ->with(['submission', 'template']);

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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
                $query->where('sent_at', '>=', $date);
            }
        }

        // Search filter (search in recipient email)
        if (! empty($this->searchQuery)) {
            $query->where('recipient_email', 'like', '%'.$this->searchQuery.'%');
        }

        return $query->orderBy('sent_at', 'desc')->paginate(20);
    }

    public function getSelectedLogProperty()
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return FormEmailLog::with(['submission', 'template'])->find($this->selectedLogId);
    }

    public function render()
    {
        return view('slick-forms::livewire.email-logs-viewer', [
            'logs' => $this->logs,
            'selectedLog' => $this->selectedLog,
        ]);
    }
}

<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormWebhookLog;
use DigitalisStudios\SlickForms\Services\WebhookService;
use Livewire\Component;
use Livewire\WithPagination;

class WebhookLogsViewer extends Component
{
    use WithPagination;

    public CustomForm $form;

    public ?int $selectedWebhookId = null;

    public string $statusFilter = 'all';

    public ?int $selectedLogId = null;

    public ?FormWebhookLog $selectedLog = null;

    public bool $showLogDetails = false;

    protected $paginationTheme = 'bootstrap';

    public function mount(int $formId): void
    {
        $this->form = CustomForm::findOrFail($formId);
    }

    /**
     * Get filtered webhook logs with pagination
     */
    public function getLogsProperty()
    {
        $query = FormWebhookLog::query()
            ->whereHas('webhook', function ($q) {
                $q->where('form_id', $this->form->id);
            })
            ->with(['webhook', 'submission'])
            ->latest();

        // Filter by webhook
        if ($this->selectedWebhookId) {
            $query->where('webhook_id', $this->selectedWebhookId);
        }

        // Filter by status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate(20);
    }

    /**
     * Get list of webhooks for filter dropdown
     */
    public function getWebhooksProperty()
    {
        return $this->form->webhooks()
            ->orderBy('name')
            ->get();
    }

    /**
     * View log details
     */
    public function viewLogDetails(int $logId): void
    {
        $this->selectedLog = FormWebhookLog::with(['webhook', 'submission'])->findOrFail($logId);
        $this->showLogDetails = true;
    }

    /**
     * Close log details modal
     */
    public function closeLogDetails(): void
    {
        $this->showLogDetails = false;
        $this->selectedLog = null;
    }

    /**
     * Retry failed webhook
     */
    public function retryWebhook(int $logId): void
    {
        $log = FormWebhookLog::findOrFail($logId);

        if ($log->status !== 'failed') {
            session()->flash('error', 'Only failed webhooks can be retried.');

            return;
        }

        $webhookService = app(WebhookService::class);
        $webhookService->retryFailedWebhook($log);

        session()->flash('success', 'Webhook retry has been queued.');
        $this->resetPage();
    }

    /**
     * Update filter and reset pagination
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Update webhook filter and reset pagination
     */
    public function updatedSelectedWebhookId(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->selectedWebhookId = null;
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function render()
    {
        return view('slick-forms::livewire.webhook-logs-viewer', [
            'logs' => $this->logs,
            'webhooks' => $this->webhooks,
        ]);
    }
}

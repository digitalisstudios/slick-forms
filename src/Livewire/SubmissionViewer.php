<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Services\SubmissionExportService;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class SubmissionViewer extends Component
{
    use WithPagination;

    #[Locked]
    public int $formId;

    #[Locked]
    public ?int $selectedSubmissionId = null;

    public bool $showSubmissionModal = false;

    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    protected string $paginationTheme = 'bootstrap';

    public function mount(int $formId): void
    {
        $this->formId = $formId;

        // Verify form exists
        CustomForm::findOrFail($formId);
    }

    public function getFormProperty(): CustomForm
    {
        return CustomForm::with('fields')->findOrFail($this->formId);
    }

    public function viewSubmission(int $submissionId): void
    {
        $this->selectedSubmissionId = $submissionId;
        $this->showSubmissionModal = true;
    }

    public function closeSubmissionModal(): void
    {
        $this->showSubmissionModal = false;
        $this->selectedSubmissionId = null;
    }

    public function deleteSubmission(int $submissionId): void
    {
        CustomFormSubmission::destroy($submissionId);

        if ($this->selectedSubmissionId === $submissionId) {
            $this->closeSubmissionModal();
        }

        $this->resetPage();
    }

    public function getSelectedSubmissionProperty(): ?CustomFormSubmission
    {
        if (! $this->selectedSubmissionId) {
            return null;
        }

        return CustomFormSubmission::with(['fieldValues.field', 'user'])
            ->find($this->selectedSubmissionId);
    }

    protected function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Convert to UTF-8 if not already, removing invalid characters
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

    public function dehydrate(): void
    {
        // Sanitize all text properties before Livewire serializes them
        $this->search = $this->sanitizeString($this->search);
        $this->startDate = $this->sanitizeString($this->startDate);
        $this->endDate = $this->sanitizeString($this->endDate);
    }

    public function updatedSearch(): void
    {
        // Sanitize immediately on update
        $this->search = $this->sanitizeString($this->search);
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        // Sanitize immediately on update
        $this->startDate = $this->sanitizeString($this->startDate);
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        // Sanitize immediately on update
        $this->endDate = $this->sanitizeString($this->endDate);
        $this->resetPage();
    }

    public function exportCsv()
    {
        return redirect()->to(
            route('slick-forms.submissions.export.csv', [
                'form' => $this->form,
                'search' => $this->search,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ])
        );
    }

    public function exportExcel()
    {
        return redirect()->to(
            route('slick-forms.submissions.export.excel', [
                'form' => $this->form,
                'search' => $this->search,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ])
        );
    }

    public function exportPdf()
    {
        return redirect()->to(
            route('slick-forms.submissions.export.pdf', [
                'form' => $this->form,
                'search' => $this->search,
                'startDate' => $this->endDate,
                'endDate' => $this->endDate,
            ])
        );
    }

    public function getExcelAvailableProperty(): bool
    {
        $exportService = app(SubmissionExportService::class);

        return $exportService->isExcelAvailable();
    }

    public function getPdfAvailableProperty(): bool
    {
        $exportService = app(SubmissionExportService::class);

        return $exportService->isPdfAvailable();
    }

    public function render()
    {
        $query = $this->form->submissions()
            ->with('user')
            ->latest('submitted_at');

        // Apply filters
        if ($this->search) {
            $query->whereHas('fieldValues', function ($q) {
                $q->where('value', 'like', "%{$this->search}%");
            });
        }

        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate.' 23:59:59');
        }

        return view('slick-forms::livewire.submission-viewer', [
            'submissions' => $query->paginate(15),
        ]);
    }
}

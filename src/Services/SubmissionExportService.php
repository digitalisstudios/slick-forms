<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SubmissionExportService
{
    /**
     * Check if Excel export is available (feature enabled AND package installed)
     */
    public function canExportExcel(): bool
    {
        return slick_forms_feature_enabled('exports')
            && class_exists(\Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Check if PDF export is available (feature enabled AND package installed)
     */
    public function canExportPdf(): bool
    {
        return slick_forms_feature_enabled('exports')
            && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class);
    }

    /**
     * Check if CSV export is available (feature enabled, uses Excel package)
     */
    public function canExportCsv(): bool
    {
        return slick_forms_feature_enabled('exports')
            && class_exists(\Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Check if Excel package is installed (legacy method)
     *
     * @deprecated Use canExportExcel() instead
     */
    public function isExcelAvailable(): bool
    {
        return class_exists(\Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Check if PDF package is installed (legacy method)
     *
     * @deprecated Use canExportPdf() instead
     */
    public function isPdfAvailable(): bool
    {
        return class_exists(\Barryvdh\DomPDF\Facade\Pdf::class);
    }

    /**
     * Export submissions to CSV
     */
    public function exportToCsv(CustomForm $form, ?string $search = null, ?string $startDate = null, ?string $endDate = null): BinaryFileResponse
    {
        if (! $this->isExcelAvailable()) {
            abort(503, 'Excel export is not available. Please install maatwebsite/excel package.');
        }

        $submissions = $this->getFilteredSubmissions($form, $search, $startDate, $endDate);
        $fields = $this->getFieldsForExport($form);
        $filename = $this->generateFilename($form->name, 'csv');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \DigitalisStudios\SlickForms\Exports\SubmissionsExport($submissions, $fields, $form->name),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Export submissions to Excel
     */
    public function exportToExcel(CustomForm $form, ?string $search = null, ?string $startDate = null, ?string $endDate = null): BinaryFileResponse
    {
        if (! $this->isExcelAvailable()) {
            abort(503, 'Excel export is not available. Please install maatwebsite/excel package.');
        }

        $submissions = $this->getFilteredSubmissions($form, $search, $startDate, $endDate);
        $fields = $this->getFieldsForExport($form);
        $filename = $this->generateFilename($form->name, 'xlsx');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \DigitalisStudios\SlickForms\Exports\SubmissionsExport($submissions, $fields, $form->name),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Export submissions to PDF
     */
    public function exportToPdf(CustomForm $form, ?string $search = null, ?string $startDate = null, ?string $endDate = null): Response
    {
        if (! $this->isPdfAvailable()) {
            abort(503, 'PDF export is not available. Please install barryvdh/laravel-dompdf package.');
        }

        $submissions = $this->getFilteredSubmissions($form, $search, $startDate, $endDate);
        $fields = $this->getFieldsForExport($form);

        $data = [
            'form' => $form,
            'submissions' => $submissions,
            'fields' => $fields,
        ];

        $Pdf = \Barryvdh\DomPDF\Facade\Pdf::class;
        $pdf = $Pdf::loadView('slick-forms::exports.submissions-pdf', $data);
        $filename = $this->generateFilename($form->name, 'pdf');

        return $pdf->download($filename);
    }

    /**
     * Get filtered submissions based on search and date range
     */
    protected function getFilteredSubmissions(CustomForm $form, ?string $search = null, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = CustomFormSubmission::where('slick_form_id', $form->id)
            ->with('fieldValues')
            ->orderBy('created_at', 'desc');

        // Date range filter
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate.' 23:59:59');
        }

        // Search filter
        if ($search) {
            $query->whereHas('fieldValues', function ($q) use ($search) {
                $q->where('value', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get fields for export (exclude display-only fields)
     */
    protected function getFieldsForExport(CustomForm $form): array
    {
        return $form->fields()
            ->whereNotIn('field_type', ['header', 'paragraph', 'code'])
            ->orderBy('order')
            ->get()
            ->map(function ($field) {
                return [
                    'id' => $field->id,
                    'label' => $field->label,
                    'name' => $field->name,
                    'field_type' => $field->field_type,
                ];
            })
            ->toArray();
    }

    /**
     * Generate filename for export
     */
    protected function generateFilename(string $formName, string $extension): string
    {
        $slug = \Illuminate\Support\Str::slug($formName);
        $timestamp = now()->format('Y-m-d_His');

        return "{$slug}_submissions_{$timestamp}.{$extension}";
    }
}

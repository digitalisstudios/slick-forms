# Submission Export Guide

## Overview

Slick Forms provides comprehensive export functionality to download form submissions in multiple formats: CSV, Excel (XLSX), and PDF. This guide covers installation, usage, customization, and optimization of the export system.

## Table of Contents

- [Installation & Requirements](#installation--requirements)
- [Export Formats](#export-formats)
- [Using Exports](#using-exports)
- [Filtering Exports](#filtering-exports)
- [CSV Export](#csv-export)
- [Excel Export](#excel-export)
- [PDF Export](#pdf-export)
- [Customization](#customization)
- [Scheduled Exports](#scheduled-exports)
- [Performance Optimization](#performance-optimization)
- [Troubleshooting](#troubleshooting)

---

## Installation & Requirements

### Base Requirements

**CSV Export** requires the Excel package:
```bash
composer require maatwebsite/excel
```

**Excel Export** uses the same package:
```bash
composer require maatwebsite/excel
```

**PDF Export** requires DomPDF:
```bash
composer require barryvdh/laravel-dompdf
```

### Verify Installation

```php
use DigitalisStudios\SlickForms\Services\SubmissionExportService;

$exportService = app(SubmissionExportService::class);

// Check if Excel is available
if ($exportService->isExcelAvailable()) {
    echo "CSV and Excel exports are available";
}

// Check if PDF is available
if ($exportService->isPdfAvailable()) {
    echo "PDF export is available";
}
```

### Package Versions

**Recommended Versions:**
- `maatwebsite/excel` ^3.1
- `barryvdh/laravel-dompdf` ^2.0

**Compatibility:**
- Laravel 11-12
- PHP 8.2+

---

## Export Formats

### Supported Formats

| Format | Package Required | File Extension | Best For |
|--------|-----------------|----------------|----------|
| **CSV** | maatwebsite/excel | `.csv` | Data analysis, imports |
| **Excel** | maatwebsite/excel | `.xlsx` | Spreadsheet analysis |
| **PDF** | barryvdh/laravel-dompdf | `.pdf` | Printing, archiving |

### Format Comparison

**CSV:**
- ✅ Universal compatibility
- ✅ Lightweight file size
- ✅ Easy to import into databases
- ❌ No formatting or styling
- ❌ Single sheet only

**Excel:**
- ✅ Preserves data types
- ✅ Professional appearance
- ✅ Can include multiple sheets
- ✅ Supports formulas
- ❌ Larger file size
- ❌ Requires Excel or compatible software

**PDF:**
- ✅ Professional presentation
- ✅ Consistent appearance across devices
- ✅ Cannot be easily modified
- ✅ Perfect for printing
- ❌ Largest file size
- ❌ Not suitable for data analysis

---

## Using Exports

### Via Web Interface

**Export Buttons in Submission Viewer:**

1. Navigate to `/slick-forms/submissions/{form}`
2. Click "Export" dropdown button
3. Select format: CSV, Excel, or PDF
4. File downloads automatically

**Export Button Location:**
- Top right of submission viewer
- Available when form has submissions
- Disabled if required package not installed

### Via Service (Programmatic)

```php
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\SubmissionExportService;

$form = CustomForm::find($formId);
$exportService = app(SubmissionExportService::class);

// Export to CSV
return $exportService->exportToCsv($form);

// Export to Excel
return $exportService->exportToExcel($form);

// Export to PDF
return $exportService->exportToPdf($form);
```

### Via Routes

**Built-in Export Routes:**

```php
// CSV Export
GET /slick-forms/submissions/{form}/export/csv

// Excel Export
GET /slick-forms/submissions/{form}/export/excel

// PDF Export
GET /slick-forms/submissions/{form}/export/pdf
```

**Example Request:**
```bash
curl -O https://your-app.test/slick-forms/submissions/5/export/csv
```

---

## Filtering Exports

### By Date Range

```php
// Export submissions from last 30 days
$exportService->exportToCsv(
    $form,
    search: null,
    startDate: now()->subDays(30)->toDateString(),
    endDate: now()->toDateString()
);

// Export submissions for January 2025
$exportService->exportToExcel(
    $form,
    search: null,
    startDate: '2025-01-01',
    endDate: '2025-01-31'
);
```

### By Search Term

```php
// Export submissions containing "premium"
$exportService->exportToCsv(
    $form,
    search: 'premium',
    startDate: null,
    endDate: null
);

// Search is case-insensitive and searches all field values
```

### By Date Range AND Search

```php
// Export Q1 2025 submissions containing "feedback"
$exportService->exportToExcel(
    $form,
    search: 'feedback',
    startDate: '2025-01-01',
    endDate: '2025-03-31'
);
```

### Custom Filtering

For advanced filtering, extend the service:

```php
use DigitalisStudios\SlickForms\Services\SubmissionExportService;

class CustomExportService extends SubmissionExportService
{
    public function exportByStatus(CustomForm $form, string $status)
    {
        $submissions = $form->submissions()
            ->whereHas('fieldValues.field', function ($query) use ($status) {
                $query->where('name', 'status')
                      ->whereHas('values', function ($q) use ($status) {
                          $q->where('value', $status);
                      });
            })
            ->with('fieldValues')
            ->get();

        $fields = $this->getFieldsForExport($form);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \DigitalisStudios\SlickForms\Exports\SubmissionsExport($submissions, $fields, $form->name),
            "status-{$status}.xlsx"
        );
    }
}
```

---

## CSV Export

### What's Included

**Columns:**
1. **ID** - Submission ID
2. **Submitted At** - Timestamp (YYYY-MM-DD HH:MM:SS)
3. **IP Address** - Submitter IP
4. **Field Columns** - One column per form field (labeled with field label)

**Example CSV Output:**
```csv
ID,Submitted At,IP Address,Name,Email,Phone,Message
1,2025-01-15 10:30:00,192.168.1.100,John Doe,john@example.com,555-1234,"Great service!"
2,2025-01-15 11:45:00,192.168.1.101,Jane Smith,jane@example.com,555-5678,"Need more info"
```

### Features

**Multi-Value Fields:**
- Checkboxes, tags, and multi-select fields are converted to comma-separated values
- Example: `["red", "blue", "green"]` becomes `"red, blue, green"`

**Field Exclusions:**
- Display-only fields are automatically excluded:
  - Header fields
  - Paragraph fields
  - Code fields

**Filename Format:**
```
{form-slug}_submissions_{timestamp}.csv

Example: contact-form_submissions_2025-01-15_103000.csv
```

### Usage

```php
// Basic CSV export
return $exportService->exportToCsv($form);

// With filtering
return $exportService->exportToCsv(
    $form,
    search: 'urgent',
    startDate: '2025-01-01',
    endDate: '2025-01-31'
);
```

---

## Excel Export

### What's Included

**Same structure as CSV but with:**
- ✅ XLSX format (Excel 2007+)
- ✅ Formatted headers (bold, gray background)
- ✅ Auto-sized columns
- ✅ Sheet name = form name (truncated to 31 characters)
- ✅ Preserved data types

### Excel-Specific Features

**Sheet Naming:**
```php
// Sheet name is form name (max 31 chars)
$form->name = "Customer Feedback Survey"; // Full name
// Sheet tab shows: "Customer Feedback Survey"

$form->name = "Very Long Form Name That Exceeds Excel Limit";
// Sheet tab shows: "Very Long Form Name That Exc..." (truncated to 31)
```

**Header Formatting:**
- Bold font weight
- Gray background (#f5f5f5)
- Border around cells

**Data Types:**
- Numbers stored as numeric (not text)
- Dates formatted correctly
- Text fields preserved as text

### Usage

```php
// Basic Excel export
return $exportService->exportToExcel($form);

// With filtering
return $exportService->exportToExcel(
    $form,
    search: null,
    startDate: now()->subWeek()->toDateString(),
    endDate: now()->toDateString()
);
```

---

## PDF Export

### What's Included

**PDF Layout:**
- **Header Section:**
  - Form name
  - Generation timestamp
  - Total submission count
- **Data Table:**
  - ID column (50px wide)
  - Submitted At column (120px)
  - Field columns (auto-width)
  - Striped rows for readability
- **Footer Section:**
  - "Exported from Slick Forms"
  - Application name

### PDF Styling

**Default Styles:**
- Font: Arial, sans-serif
- Font Size: 12px body, 18px heading
- Colors: #333 text, #666 meta info
- Table borders: 1px solid #ddd
- Alternating row colors: white / #f9f9f9

### Usage

```php
// Basic PDF export
return $exportService->exportToPdf($form);

// With filtering
return $exportService->exportToPdf(
    $form,
    search: 'feedback',
    startDate: '2025-01-01',
    endDate: '2025-12-31'
);
```

### PDF Customization

**Override PDF Template:**

1. Publish the template:
```bash
php artisan vendor:publish --tag=slick-forms-views
```

2. Edit template:
```
resources/views/vendor/slick-forms/exports/submissions-pdf.blade.php
```

3. Customize styling:
```html
<style>
    body {
        font-family: 'Times New Roman', serif;
        font-size: 14px;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    /* Add your custom styles */
</style>
```

**Landscape Orientation:**

```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('slick-forms::exports.submissions-pdf', $data)
    ->setPaper('a4', 'landscape');

return $pdf->download('submissions.pdf');
```

---

## Customization

### Custom Export Class

Create your own export class:

```php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomSubmissionsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $submissions;

    public function __construct($submissions)
    {
        $this->submissions = $submissions;
    }

    public function collection()
    {
        return $this->submissions->map(function ($submission) {
            return [
                'id' => $submission->id,
                'name' => $submission->getFieldValue('name'),
                'email' => $submission->getFieldValue('email'),
                'score' => $this->calculateScore($submission),
                'status' => $this->determineStatus($submission),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Score', 'Status'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    private function calculateScore($submission): int
    {
        // Custom logic
        return 85;
    }

    private function determineStatus($submission): string
    {
        // Custom logic
        return 'Approved';
    }
}
```

**Usage:**
```php
use Maatwebsite\Excel\Facades\Excel;

return Excel::download(new CustomSubmissionsExport($submissions), 'custom.xlsx');
```

### Adding Calculated Columns

```php
use DigitalisStudios\SlickForms\Exports\SubmissionsExport;

class EnhancedSubmissionsExport extends SubmissionsExport
{
    public function headings(): array
    {
        $baseHeadings = parent::headings();

        // Add calculated columns
        $baseHeadings[] = 'Days Since Submission';
        $baseHeadings[] = 'Response Status';

        return $baseHeadings;
    }

    public function map($submission): array
    {
        $baseRow = parent::map($submission);

        // Add calculated values
        $baseRow[] = now()->diffInDays($submission->created_at);
        $baseRow[] = $this->getResponseStatus($submission);

        return $baseRow;
    }

    private function getResponseStatus($submission): string
    {
        // Custom logic to determine status
        return 'Pending';
    }
}
```

### Excluding Specific Fields

```php
// In SubmissionExportService
protected function getFieldsForExport(CustomForm $form): array
{
    return $form->fields()
        ->whereNotIn('field_type', ['header', 'paragraph', 'code'])
        ->whereNotIn('name', ['internal_notes', 'admin_comments']) // Exclude specific fields
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
```

---

## Scheduled Exports

### Daily Export Email

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $form = CustomForm::find(5);
        $exportService = app(SubmissionExportService::class);

        // Export yesterday's submissions
        $csv = $exportService->exportToCsv(
            $form,
            startDate: yesterday()->toDateString(),
            endDate: yesterday()->toDateString()
        );

        // Email to admin
        Mail::to('admin@example.com')
            ->send(new DailySubmissionsExport($csv));
    })->dailyAt('08:00');
}
```

### Weekly Report

```php
$schedule->call(function () {
    $forms = CustomForm::where('is_active', true)->get();

    foreach ($forms as $form) {
        $exportService = app(SubmissionExportService::class);

        $excel = $exportService->exportToExcel(
            $form,
            startDate: now()->subWeek()->toDateString(),
            endDate: now()->toDateString()
        );

        // Store in S3 for archiving
        Storage::disk('s3')->put(
            "exports/{$form->id}/weekly-" . now()->format('Y-m-d') . ".xlsx",
            $excel->getContent()
        );
    }
})->weeklyOn(1, '09:00'); // Every Monday at 9 AM
```

---

## Performance Optimization

### Large Datasets (10,000+ Submissions)

**Problem:** Memory exhaustion when exporting large datasets

**Solution 1: Chunking**

```php
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ChunkedSubmissionsExport implements FromQuery, WithChunkReading
{
    protected $formId;

    public function __construct(int $formId)
    {
        $this->formId = $formId;
    }

    public function query()
    {
        return SlickFormSubmission::where('slick_form_id', $this->formId)
            ->with('fieldValues');
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
```

**Solution 2: Queue Large Exports**

```php
use Maatwebsite\Excel\Concerns\ShouldQueue;

class QueuedSubmissionsExport implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes

    // ... implementation
}

// Usage
Excel::queue(new QueuedSubmissionsExport($formId), 'submissions.xlsx');
```

**Solution 3: Increase Memory Limit**

```php
// In controller
ini_set('memory_limit', '512M');

return $exportService->exportToExcel($form);
```

### Optimizing Query Performance

```php
// Eager load relationships
$submissions = CustomFormSubmission::where('slick_form_id', $formId)
    ->with(['fieldValues' => function ($query) {
        $query->orderBy('slick_form_field_id');
    }])
    ->orderBy('created_at', 'desc')
    ->get();
```

### PDF Optimization

For large PDFs (100+ submissions):

```php
// Split into multiple PDFs
$chunks = $submissions->chunk(50);

foreach ($chunks as $index => $chunk) {
    $pdf = Pdf::loadView('slick-forms::exports.submissions-pdf', [
        'form' => $form,
        'submissions' => $chunk,
        'fields' => $fields,
    ]);

    $pdf->save("submissions-part-{$index}.pdf");
}
```

---

## Troubleshooting

### Package Not Installed

**Error:**
```
Excel export is not available. Please install maatwebsite/excel package.
```

**Solution:**
```bash
composer require maatwebsite/excel
```

### Memory Exhaustion

**Error:**
```
Fatal error: Allowed memory size of 134217728 bytes exhausted
```

**Solutions:**

1. **Increase PHP memory limit:**
```ini
; php.ini
memory_limit = 512M
```

2. **Use chunking:**
```php
class ChunkedExport implements WithChunkReading
{
    public function chunkSize(): int
    {
        return 1000;
    }
}
```

3. **Filter submissions:**
```php
// Export only last 30 days instead of all time
$exportService->exportToCsv(
    $form,
    startDate: now()->subDays(30)->toDateString()
);
```

### PDF Rendering Issues

**Error:**
```
DOMPDF (powered by HTML5LIB): Unexpected tag type: comment
```

**Solution:**
Remove HTML comments from PDF template:
```blade
{{-- Blade comments are fine --}}
<!-- Remove HTML comments -->
```

**Error:**
```
Cannot write to temporary directory
```

**Solution:**
Ensure temp directory is writable:
```bash
chmod 775 storage/app/
```

### Excel Column Limit

**Error:**
```
Maximum column limit (16,384) exceeded
```

**Solution:**
Your form has too many fields for Excel. Options:

1. Export to CSV instead (no column limit)
2. Split into multiple exports by field groups
3. Exclude non-essential fields

```php
protected function getFieldsForExport(CustomForm $form): array
{
    return $form->fields()
        ->where('is_required', true) // Only required fields
        ->orderBy('order')
        ->get()
        ->take(100) // Limit to first 100 fields
        ->map(/* ... */)
        ->toArray();
}
```

### Filename Sanitization

**Issue:** Special characters in form name cause download issues

**Solution:** Already handled by `generateFilename()`:
```php
protected function generateFilename(string $formName, string $extension): string
{
    $slug = \Illuminate\Support\Str::slug($formName); // Removes special chars
    $timestamp = now()->format('Y-m-d_His');
    return "{$slug}_submissions_{$timestamp}.{$extension}";
}

// "Contact Form!" becomes "contact-form_submissions_2025-01-15_103000.csv"
```

### Export Takes Too Long

**Issue:** Export request times out after 60 seconds

**Solutions:**

1. **Increase timeout:**
```php
set_time_limit(300); // 5 minutes
return $exportService->exportToPdf($form);
```

2. **Use queue:**
```php
Excel::queue(new SubmissionsExport($formId), 'submissions.xlsx')
    ->chain([
        new NotifyUserOfCompletedExport($user),
    ]);
```

3. **Filter to reduce size:**
```php
// Export only last 7 days
$exportService->exportToExcel(
    $form,
    startDate: now()->subDays(7)->toDateString()
);
```

---

## API Reference

### SubmissionExportService Methods

```php
// Check package availability
isExcelAvailable(): bool
isPdfAvailable(): bool

// Export methods
exportToCsv(
    CustomForm $form,
    ?string $search = null,
    ?string $startDate = null,
    ?string $endDate = null
): BinaryFileResponse

exportToExcel(
    CustomForm $form,
    ?string $search = null,
    ?string $startDate = null,
    ?string $endDate = null
): BinaryFileResponse

exportToPdf(
    CustomForm $form,
    ?string $search = null,
    ?string $startDate = null,
    ?string $endDate = null
): Response

// Protected helpers
getFilteredSubmissions(
    CustomForm $form,
    ?string $search,
    ?string $startDate,
    ?string $endDate
): Collection

getFieldsForExport(CustomForm $form): array
generateFilename(string $formName, string $extension): string
```

---

## Related Documentation

- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)
- [Analytics Guide](ANALYTICS.md)
- [Form Builder Guide](CUSTOM_FIELD_TYPES.md)
- [Multi-Page Forms](../MULTI_PAGE_FORMS.md)

---

**Need Help?** Visit the [Slick Forms repository](https://bitbucket.org/bmooredigitalisstudios/slick-forms) for support.

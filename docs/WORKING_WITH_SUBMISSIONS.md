# Working with Submissions

## Overview

This guide covers how to programmatically access, query, filter, export, and process form submissions in Slick Forms.

## Table of Contents

- [Accessing Submissions](#accessing-submissions)
- [Querying Submissions](#querying-submissions)
- [Retrieving Field Values](#retrieving-field-values)
- [Filtering and Searching](#filtering-and-searching)
- [Exporting Submissions](#exporting-submissions)
- [Processing Submissions](#processing-submissions)
- [Bulk Operations](#bulk-operations)
- [Submission Data Structure](#submission-data-structure)
- [Events and Listeners](#events-and-listeners)

---

## Accessing Submissions

### Via Eloquent Model

```php
use DigitalisStudios\SlickForms\Models\SlickFormSubmission;

// Get all submissions for a form
$submissions = SlickFormSubmission::where('slick_form_id', $formId)->get();

// Get single submission
$submission = SlickFormSubmission::find($submissionId);

// Get with relationships
$submission = SlickFormSubmission::with(['form', 'fieldValues'])->find($submissionId);
```

### Via Form Relationship

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

$form = CustomForm::find($formId);
$submissions = $form->submissions;

// With eager loading
$form = CustomForm::with('submissions.fieldValues')->find($formId);
```

### Via Built-in Viewer

**Route:** `/slick-forms/submissions/{form}`

**Livewire Component:** `<livewire:slick-forms::submission-viewer :form="$form" />`

---

## Querying Submissions

### Basic Queries

```php
// Latest submissions first
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->orderBy('created_at', 'desc')
    ->get();

// Paginated results
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->latest()
    ->paginate(25);

// Count submissions
$count = SlickFormSubmission::where('slick_form_id', $formId)->count();
```

### Date Range Queries

```php
// Submissions today
$today = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereDate('created_at', today())
    ->get();

// Submissions this month
$thisMonth = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->get();

// Submissions between dates
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();

// Last 30 days
$lastThirtyDays = SlickFormSubmission::where('slick_form_id', $formId)
    ->where('created_at', '>=', now()->subDays(30))
    ->get();
```

### IP Address Queries

```php
// Submissions from specific IP
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->where('ip_address', '192.168.1.100')
    ->get();

// Count submissions per IP
$ipCounts = SlickFormSubmission::where('slick_form_id', $formId)
    ->select('ip_address', DB::raw('count(*) as total'))
    ->groupBy('ip_address')
    ->orderBy('total', 'desc')
    ->get();

// Detect potential spam (multiple submissions from same IP)
$potentialSpam = SlickFormSubmission::where('slick_form_id', $formId)
    ->select('ip_address', DB::raw('count(*) as total'))
    ->groupBy('ip_address')
    ->having('total', '>', 5)
    ->get();
```

---

## Retrieving Field Values

### Getting All Field Values

```php
$submission = SlickFormSubmission::with('fieldValues.field')->find($submissionId);

foreach ($submission->fieldValues as $fieldValue) {
    $fieldName = $fieldValue->field->name;
    $label = $fieldValue->field->label;
    $value = $fieldValue->value;

    echo "{$label}: {$value}\n";
}
```

### Getting Specific Field Value

```php
// By field name
$emailValue = $submission->fieldValues()
    ->whereHas('field', function ($query) {
        $query->where('name', 'email');
    })
    ->first()?->value;

// More efficient with eager loading
$submission = SlickFormSubmission::with(['fieldValues' => function ($query) {
    $query->whereHas('field', function ($q) {
        $q->whereIn('name', ['email', 'phone', 'name']);
    });
}])->find($submissionId);
```

### Helper Method for Field Access

Create a custom accessor:

```php
// In SlickFormSubmission model
public function getFieldValue(string $fieldName): mixed
{
    return $this->fieldValues()
        ->whereHas('field', function ($query) use ($fieldName) {
            $query->where('name', $fieldName);
        })
        ->first()?->value;
}

// Usage
$email = $submission->getFieldValue('email');
$phone = $submission->getFieldValue('phone');
```

### Converting to Array

```php
// Convert submission to associative array
public function toFieldArray(): array
{
    $data = [];

    foreach ($this->fieldValues as $fieldValue) {
        $data[$fieldValue->field->name] = $fieldValue->value;
    }

    return $data;
}

// Usage
$submissionData = $submission->toFieldArray();
// Returns: ['email' => 'test@example.com', 'phone' => '555-1234', ...]
```

---

## Filtering and Searching

### Search by Field Value

```php
// Search for submissions containing specific value
$searchTerm = 'john@example.com';

$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereHas('fieldValues', function ($query) use ($searchTerm) {
        $query->where('value', 'LIKE', "%{$searchTerm}%");
    })
    ->get();
```

### Filter by Specific Field

```php
// Get submissions where email field equals specific value
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereHas('fieldValues.field', function ($query) use ($email) {
        $query->where('name', 'email')
              ->whereHas('values', function ($q) use ($email) {
                  $q->where('value', $email);
              });
    })
    ->get();
```

### Advanced Filtering

```php
// Complex filter: submissions with status "approved" AND category "feedback"
$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->whereHas('fieldValues', function ($query) {
        $query->whereHas('field', function ($q) {
            $q->where('name', 'status');
        })->where('value', 'approved');
    })
    ->whereHas('fieldValues', function ($query) {
        $query->whereHas('field', function ($q) {
            $q->where('name', 'category');
        })->where('value', 'feedback');
    })
    ->get();
```

### Filter by Multiple Criteria

```php
class SubmissionFilter
{
    public function __construct(
        public ?string $search = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $ipAddress = null,
    ) {}

    public function apply($query)
    {
        if ($this->search) {
            $query->whereHas('fieldValues', function ($q) {
                $q->where('value', 'LIKE', "%{$this->search}%");
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->ipAddress) {
            $query->where('ip_address', $this->ipAddress);
        }

        return $query;
    }
}

// Usage
$filter = new SubmissionFilter(
    search: 'john',
    dateFrom: '2025-01-01',
    dateTo: '2025-01-31'
);

$submissions = $filter->apply(
    SlickFormSubmission::where('slick_form_id', $formId)
)->get();
```

---

## Exporting Submissions

### Export to CSV (Built-in)

```php
use DigitalisStudios\SlickForms\Services\SubmissionExportService;

$exportService = app(SubmissionExportService::class);

// Export all submissions
$csv = $exportService->exportToCsv($formId);

return response($csv)
    ->header('Content-Type', 'text/csv')
    ->header('Content-Disposition', 'attachment; filename="submissions.csv"');
```

### Export to Excel (Requires maatwebsite/excel)

```bash
composer require maatwebsite/excel
```

```php
use DigitalisStudios\SlickForms\Exports\SubmissionsExport;
use Maatwebsite\Excel\Facades\Excel;

// Export all submissions
return Excel::download(new SubmissionsExport($formId), 'submissions.xlsx');

// Export filtered submissions
$submissionIds = [1, 2, 3, 4, 5];
return Excel::download(
    new SubmissionsExport($formId, $submissionIds),
    'filtered-submissions.xlsx'
);
```

### Export to PDF (Requires barryvdh/laravel-dompdf)

```bash
composer require barryvdh/laravel-dompdf
```

```php
use Barryvdh\DomPDF\Facade\Pdf;

$submissions = SlickFormSubmission::where('slick_form_id', $formId)
    ->with('fieldValues.field')
    ->get();

$pdf = Pdf::loadView('slick-forms::exports.submissions-pdf', [
    'form' => $form,
    'submissions' => $submissions,
]);

return $pdf->download('submissions.pdf');
```

### Custom CSV Export

```php
public function exportCustomCsv($formId)
{
    $submissions = SlickFormSubmission::where('slick_form_id', $formId)
        ->with('fieldValues.field')
        ->get();

    // Get unique field names
    $fields = $submissions->first()
        ?->fieldValues
        ->pluck('field.label')
        ->toArray() ?? [];

    // Build CSV header
    $csv = implode(',', array_merge(['Submission Date', 'IP Address'], $fields)) . "\n";

    // Build CSV rows
    foreach ($submissions as $submission) {
        $row = [
            $submission->created_at->format('Y-m-d H:i:s'),
            $submission->ip_address,
        ];

        foreach ($submission->fieldValues as $fieldValue) {
            $row[] = '"' . str_replace('"', '""', $fieldValue->value) . '"';
        }

        $csv .= implode(',', $row) . "\n";
    }

    return response($csv)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="submissions.csv"');
}
```

---

## Processing Submissions

### Sending Email Notifications

```php
use Illuminate\Support\Facades\Mail;

// Listen for submission created event
Event::listen(function (SubmissionCreated $event) {
    $submission = $event->submission;
    $form = $submission->form;

    // Send to form administrator
    Mail::to($form->notification_email)
        ->send(new NewSubmissionNotification($submission));

    // Send confirmation to user
    $userEmail = $submission->getFieldValue('email');
    if ($userEmail) {
        Mail::to($userEmail)
            ->send(new SubmissionConfirmation($submission));
    }
});
```

### Webhook Integration

```php
use Illuminate\Support\Facades\Http;

Event::listen(function (SubmissionCreated $event) {
    $submission = $event->submission;

    // Get webhook URL from form settings
    $webhookUrl = $submission->form->settings['webhook_url'] ?? null;

    if ($webhookUrl) {
        Http::post($webhookUrl, [
            'form_id' => $submission->slick_form_id,
            'submission_id' => $submission->id,
            'data' => $submission->toFieldArray(),
            'created_at' => $submission->created_at->toIso8601String(),
        ]);
    }
});
```

### Processing with Jobs

```php
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class ProcessSubmission implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public function __construct(
        public SlickFormSubmission $submission
    ) {}

    public function handle()
    {
        // Process submission data
        $data = $this->submission->toFieldArray();

        // Perform business logic
        $this->validateBusinessRules($data);
        $this->updateCRM($data);
        $this->sendNotifications($data);

        // Mark as processed
        $this->submission->update(['processed_at' => now()]);
    }
}

// Dispatch job
ProcessSubmission::dispatch($submission);
```

---

## Bulk Operations

### Bulk Delete

```php
// Delete submissions older than 1 year
$deleted = SlickFormSubmission::where('slick_form_id', $formId)
    ->where('created_at', '<', now()->subYear())
    ->delete();

// Delete by IDs
SlickFormSubmission::whereIn('id', $submissionIds)->delete();
```

### Bulk Update

```php
// Mark submissions as reviewed
SlickFormSubmission::whereIn('id', $submissionIds)
    ->update(['reviewed_at' => now()]);

// Add custom metadata
SlickFormSubmission::where('slick_form_id', $formId)
    ->where('created_at', '>=', today())
    ->update(['metadata' => json_encode(['source' => 'campaign_2025'])]);
```

### Bulk Export

```php
// Export submissions in batches
SlickFormSubmission::where('slick_form_id', $formId)
    ->chunk(1000, function ($submissions) {
        // Process each batch
        foreach ($submissions as $submission) {
            // Export or process each submission
        }
    });
```

---

## Submission Data Structure

### SlickFormSubmission Model

```php
{
    "id": 123,
    "slick_form_id": 5,
    "ip_address": "192.168.1.100",
    "user_id": null, // Optional user association
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
}
```

### SlickFormFieldValue Model

```php
{
    "id": 456,
    "slick_form_submission_id": 123,
    "slick_form_field_id": 78,
    "value": "john@example.com",
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
}
```

### Relationships

```php
// SlickFormSubmission relationships
public function form() // BelongsTo CustomForm
public function fieldValues() // HasMany SlickFormFieldValue
public function user() // BelongsTo User (optional)

// SlickFormFieldValue relationships
public function submission() // BelongsTo SlickFormSubmission
public function field() // BelongsTo SlickFormField
```

### Complete Data Structure

```php
$submission = SlickFormSubmission::with(['form', 'fieldValues.field'])->find($id);

/*
SlickFormSubmission {
    id: 123
    slick_form_id: 5
    ip_address: "192.168.1.100"
    created_at: "2025-01-15 10:30:00"

    form: CustomForm {
        id: 5
        name: "Contact Form"
        ...
    }

    fieldValues: [
        SlickFormFieldValue {
            id: 456
            value: "john@example.com"
            field: SlickFormField {
                id: 78
                name: "email"
                label: "Email Address"
                field_type: "email"
            }
        },
        SlickFormFieldValue {
            id: 457
            value: "555-1234"
            field: SlickFormField {
                id: 79
                name: "phone"
                label: "Phone Number"
                field_type: "phone"
            }
        }
    ]
}
*/
```

---

## Events and Listeners

### Available Events

Slick Forms doesn't fire events by default, but you can implement them:

```php
// app/Events/SubmissionCreated.php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use DigitalisStudios\SlickForms\Models\SlickFormSubmission;

class SubmissionCreated
{
    use Dispatchable;

    public function __construct(
        public SlickFormSubmission $submission
    ) {}
}
```

### Dispatching Events

```php
// In your FormRenderer Livewire component or custom submission handler
use App\Events\SubmissionCreated;

// After saving submission
event(new SubmissionCreated($submission));
```

### Creating Listeners

```php
// app/Listeners/SendSubmissionNotification.php
namespace App\Listeners;

use App\Events\SubmissionCreated;
use Illuminate\Support\Facades\Mail;

class SendSubmissionNotification
{
    public function handle(SubmissionCreated $event)
    {
        $submission = $event->submission;

        // Send email notification
        Mail::to($submission->form->notification_email)
            ->send(new \App\Mail\NewSubmission($submission));
    }
}
```

### Registering Listeners

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    \App\Events\SubmissionCreated::class => [
        \App\Listeners\SendSubmissionNotification::class,
        \App\Listeners\UpdateCRM::class,
        \App\Listeners\TrackAnalytics::class,
    ],
];
```

---

## Common Patterns

### Submission Statistics

```php
class SubmissionStats
{
    public function __construct(private int $formId) {}

    public function totalSubmissions(): int
    {
        return SlickFormSubmission::where('slick_form_id', $this->formId)->count();
    }

    public function submissionsToday(): int
    {
        return SlickFormSubmission::where('slick_form_id', $this->formId)
            ->whereDate('created_at', today())
            ->count();
    }

    public function averagePerDay(int $days = 30): float
    {
        $count = SlickFormSubmission::where('slick_form_id', $this->formId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        return round($count / $days, 2);
    }

    public function peakSubmissionHour(): int
    {
        return SlickFormSubmission::where('slick_form_id', $this->formId)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('total', 'desc')
            ->first()
            ->hour;
    }
}
```

### Submission Validation

```php
class SubmissionValidator
{
    public function validate(SlickFormSubmission $submission): bool
    {
        $rules = [];

        foreach ($submission->form->fields as $field) {
            if ($field->validation_rules) {
                $rules[$field->name] = $field->validation_rules;
            }
        }

        $data = $submission->toFieldArray();

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }
}
```

### Duplicate Detection

```php
public function findDuplicates(SlickFormSubmission $submission): Collection
{
    $email = $submission->getFieldValue('email');

    if (!$email) {
        return collect();
    }

    return SlickFormSubmission::where('slick_form_id', $submission->slick_form_id)
        ->where('id', '!=', $submission->id)
        ->whereHas('fieldValues.field', function ($query) use ($email) {
            $query->where('name', 'email')
                  ->whereHas('values', function ($q) use ($email) {
                      $q->where('value', $email);
                  });
        })
        ->get();
}
```

---

## API Examples

### RESTful API Controller

```php
namespace App\Http\Controllers\Api;

use DigitalisStudios\SlickForms\Models\SlickFormSubmission;

class SubmissionController extends Controller
{
    public function index($formId)
    {
        $submissions = SlickFormSubmission::where('slick_form_id', $formId)
            ->with('fieldValues.field')
            ->paginate(25);

        return response()->json($submissions);
    }

    public function show($submissionId)
    {
        $submission = SlickFormSubmission::with('fieldValues.field')
            ->findOrFail($submissionId);

        return response()->json([
            'id' => $submission->id,
            'form_id' => $submission->slick_form_id,
            'submitted_at' => $submission->created_at,
            'data' => $submission->toFieldArray(),
        ]);
    }

    public function destroy($submissionId)
    {
        SlickFormSubmission::findOrFail($submissionId)->delete();

        return response()->json(['message' => 'Submission deleted']);
    }
}
```

---

## Related Documentation

- [Form Builder Guide](CUSTOM_FIELD_TYPES.md)
- [Analytics Guide](ANALYTICS.md)
- [Export Guide](EXPORTS.md)
- [Multi-Page Forms](../MULTI_PAGE_FORMS.md)

---

**Need Help?** Visit the [Slick Forms repository](https://bitbucket.org/bmooredigitalisstudios/slick-forms) for support.

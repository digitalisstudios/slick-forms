# Livewire Component Reference

**Slick Forms v2.0+** - Complete reference for all Livewire components in the Slick Forms package.

## Overview

Slick Forms includes **10 Livewire components** that power the form builder, renderer, management UI, analytics dashboards, and log viewers. All components are automatically registered with the `slick-forms::` prefix.

**Component Categories**:
- **Core Components** (3) - Form builder, renderer, and submission viewer
- **Management Components** (3) - Form management, statistics, and templates
- **Analytics Components** (1) - Detailed form analytics
- **Log Viewer Components** (3) - Email, spam, and webhook logs

---

## Table of Contents

1. [Core Components](#core-components)
   - [FormBuilder](#formbuilder)
   - [FormRenderer](#formrenderer)
   - [SubmissionViewer](#submissionviewer)
2. [Management Components](#management-components)
   - [Manage](#manage)
   - [ManageStats](#managestats)
   - [FormTemplates](#formtemplates)
3. [Analytics Components](#analytics-components)
   - [FormAnalytics](#formanalytics)
4. [Log Viewer Components](#log-viewer-components)
   - [EmailLogsViewer](#emaillogsviewer)
   - [SpamLogsViewer](#spamlogsviewer)
   - [WebhookLogsViewer](#webhooklogsviewer)
5. [Traits](#traits)
   - [ManagesSchemaProperties](#managesschemaproperties)
6. [Usage Examples](#usage-examples)

---

## Core Components

### FormBuilder

**Purpose**: Drag-and-drop form builder interface for creating and editing forms visually.

**Location**: `src/Livewire/FormBuilder.php`

**Component Tag**:
```blade
<livewire:slick-forms::form-builder :form="$form" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$form` | `CustomForm` | Form model instance to edit |
| `$formName` | `string` | Form name |
| `$formDescription` | `string` | Form description |
| `$formIsActive` | `bool` | Form active status |
| `$formIsPublic` | `bool` | Form public visibility |
| `$formExpiresAt` | `?string` | Form expiration date |
| `$formTimeLimit` | `int` | Form time limit in minutes (0 = no limit) |
| `$selectedField` | `?CustomFormField` | Currently selected field in properties panel |
| `$selectedElement` | `?SlickFormLayoutElement` | Currently selected layout element |
| `$availableFieldTypes` | `array` | Input field types (text, email, number, etc.) |
| `$availableContentTypes` | `array` | Content field types (header, paragraph, etc.) |
| `$availableFormFieldTypes` | `array` | Advanced field types (calculation, repeater, etc.) |
| `$availableLayoutTypes` | `array` | Layout elements (container, row, column, etc.) |
| `$showFieldEditor` | `bool` | Show field properties panel |
| `$showElementEditor` | `bool` | Show element properties panel |
| `$activePropertiesTab` | `string` | Active tab in properties panel (basic, appearance, validation, conditional, advanced) |
| `$formStructure` | `array` | Hierarchical form structure |
| `$previewMode` | `bool` | Preview mode toggle |
| `$viewportMode` | `string` | Viewport mode (desktop, tablet, mobile) |
| `$pickerMode` | `bool` | Field picker mode for conditional logic |
| `$pickerTarget` | `?string` | Target wire:model for field picker |
| `$currentPageId` | `?int` | Current page ID for multi-page forms |
| `$pages` | `array` | Form pages |
| `$showPageEditor` | `bool` | Show page editor modal |
| `$showVersionHistory` | `bool` | Show version history modal |
| `$versions` | `array` | Form versions |

**Public Methods**:

#### Form Management
- `mount(CustomForm $form): void` - Initialize component with form
- `updateFormSettings(): void` - Save form settings (name, description, status)
- `toggleActive(): void` - Toggle form active status
- `deleteForm(): void` - Delete form and redirect to management

#### Field Management
- `addField(string $type, ?int $parentId = null): void` - Add new field to canvas
- `selectField(int $fieldId): void` - Select field for editing
- `updateField(): void` - Save field properties
- `deleteField(int $fieldId): void` - Delete field and cascade to dependents
- `duplicateField(int $fieldId): void` - Duplicate field with new unique name
- `reorderFields(array $orderedIds): void` - Update field order after drag-drop

#### Layout Management
- `addElement(string $type, ?int $parentId = null): void` - Add layout element
- `selectElement(int $elementId): void` - Select element for editing
- `updateElement(): void` - Save element properties
- `deleteElement(int $elementId): void` - Delete element and children
- `reorderElements(array $orderedIds): void` - Update element order

#### Properties Panel
- `changeTab(string $tabName): void` - Switch properties panel tab
- `updateProperty(string $propertyName, mixed $value): void` - Update single property
- `closePropertiesPanel(): void` - Close properties panel

#### Field Picker (Conditional Logic)
- `enterPickerMode(string $target): void` - Enable field picker with crosshair cursor
- `pickField(int $fieldId): void` - Select field and populate conditional logic
- `exitPickerMode(): void` - Disable field picker mode

#### Preview & Viewport
- `togglePreview(): void` - Toggle preview mode
- `setViewport(string $mode): void` - Set viewport (desktop, tablet, mobile)

#### Multi-Page Forms
- `addPage(): void` - Add new page
- `editPage(int $pageId): void` - Edit page settings
- `savePage(): void` - Save page settings
- `deletePage(int $pageId): void` - Delete page
- `reorderPages(array $orderedIds): void` - Update page order
- `navigateToPage(int $pageId): void` - Switch to page

#### Version Management
- `showVersions(): void` - Open version history modal
- `restoreVersion(int $versionId): void` - Restore form to previous version
- `compareVersions(int $versionId): void` - Compare version with current

**Usage Example**:
```blade
{{-- In a route view --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <livewire:slick-forms::form-builder :form="$form" />
    </div>
@endsection
```

**Events Emitted**:
- `field-added` - When field added to canvas
- `field-updated` - When field properties saved
- `field-deleted` - When field deleted
- `element-added` - When layout element added
- `element-updated` - When element properties saved
- `element-deleted` - When element deleted
- `form-updated` - When form settings saved

**Related Documentation**:
- [Schema-Driven Properties System](../CLAUDE.md#schema-driven-properties-system)
- [CSS-Only Hover Controls](../CLAUDE.md#css-only-hover-controls)
- [SortableJS Integration](../CLAUDE.md#sortablejs-integration)

---

### FormRenderer

**Purpose**: User-facing form display with real-time validation and submission handling.

**Location**: `src/Livewire/FormRenderer.php`

**Component Tag**:
```blade
<livewire:slick-forms::form-renderer :form="$form" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$form` | `CustomForm` | Form model instance to render |
| `$formData` | `array` | User input data (field_name => value) |
| `$currentPage` | `int` | Current page number (multi-page forms) |
| `$startTime` | `?int` | Form start timestamp (for analytics) |
| `$sessionId` | `?string` | Analytics session ID |
| `$validationErrors` | `array` | Field-level validation errors |
| `$successMessage` | `?string` | Success message after submission |
| `$redirectUrl` | `?string` | Redirect URL after submission |
| `$visibleFieldIds` | `array` | Fields visible based on conditional logic |
| `$visibleElementIds` | `array` | Elements visible based on conditional logic |
| `$prefillData` | `array` | Pre-filled data from URL or signed link |

**Public Methods**:

#### Form Lifecycle
- `mount(CustomForm $form, ?array $prefillData = null): void` - Initialize form with optional pre-fill data
- `submit(): void` - Validate and submit form data
- `nextPage(): void` - Navigate to next page (multi-page forms)
- `previousPage(): void` - Navigate to previous page
- `goToPage(int $pageNumber): void` - Navigate to specific page

#### Field Interaction
- `updated(string $propertyName): void` - Handle field updates for conditional logic
- `updatedFormData(): void` - Re-evaluate conditional logic on any field change

#### Analytics Tracking
- `trackFormView(): void` - Track form view event
- `trackFormStart(): void` - Track form start event (first interaction)
- `trackFieldInteraction(string $fieldName): void` - Track field interaction
- `trackFieldValidation(string $fieldName, bool $passed): void` - Track validation result
- `trackFormAbandonment(): void` - Track abandonment event

#### Spam Protection
- `validateSpamProtection(): bool` - Run spam protection checks
- `checkHoneypot(): bool` - Validate honeypot field
- `checkRateLimit(): bool` - Check IP rate limit
- `verifyCaptcha(): bool` - Verify CAPTCHA response

**Usage Example**:
```blade
{{-- Public form display route --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <livewire:slick-forms::form-renderer :form="$form" />
        </div>
    </div>
</div>
```

**With Pre-fill Data**:
```blade
{{-- Pre-filled form from encrypted URL --}}
@php
    $prefillData = [
        'email' => 'user@example.com',
        'name' => 'John Doe',
        'company' => 'Acme Corp',
    ];
@endphp

<livewire:slick-forms::form-renderer
    :form="$form"
    :prefillData="$prefillData"
/>
```

**Events Emitted**:
- `form-viewed` - When form initially loaded
- `form-started` - When user interacts with first field
- `field-interacted` - When user focuses on field
- `field-validated` - When field validation runs
- `form-submitted` - When form successfully submitted
- `form-abandoned` - When user leaves without submitting

**Events Dispatched**:
- `FormSubmitted` - Laravel event with submission data
- `FormViewed` - Analytics event
- `FormStarted` - Analytics event
- `FormAbandoned` - Analytics event

**Related Documentation**:
- [Conditional Logic System](CONDITIONAL_LOGIC.md)
- [Spam Protection](SPAM_PROTECTION.md)
- [Analytics](ANALYTICS.md)
- [Success Screens](SUCCESS_SCREENS.md)

---

### SubmissionViewer

**Purpose**: View, search, filter, and export form submissions with pagination.

**Location**: `src/Livewire/SubmissionViewer.php`

**Component Tag**:
```blade
<livewire:slick-forms::submission-viewer :formId="$formId" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$formId` | `int` | Form ID to view submissions for |
| `$form` | `CustomForm` | Form model instance |
| `$submissions` | `Collection` | Paginated submissions |
| `$selectedSubmission` | `?CustomFormSubmission` | Selected submission for detail view |
| `$search` | `string` | Search query |
| `$filterField` | `?string` | Field to filter by |
| `$filterValue` | `string` | Filter value |
| `$sortField` | `string` | Field to sort by (default: created_at) |
| `$sortDirection` | `string` | Sort direction (asc, desc) |
| `$perPage` | `int` | Results per page (default: 25) |
| `$showFilters` | `bool` | Show filter panel |
| `$selectedSubmissions` | `array` | Selected submission IDs for bulk actions |

**Public Methods**:

#### Submission Management
- `mount(int $formId): void` - Initialize component with form ID
- `viewSubmission(int $submissionId): void` - View submission details
- `closeSubmissionModal(): void` - Close submission detail modal
- `deleteSubmission(int $submissionId): void` - Delete single submission
- `bulkDelete(): void` - Delete selected submissions

#### Filtering & Sorting
- `updatedSearch(): void` - Update search results
- `applyFilter(): void` - Apply field filter
- `clearFilters(): void` - Reset all filters
- `sortBy(string $field): void` - Sort by field (toggle direction)
- `setPerPage(int $perPage): void` - Change results per page

#### Export
- `exportCsv(): StreamedResponse` - Export to CSV
- `exportExcel(): BinaryFileResponse` - Export to Excel (requires maatwebsite/excel)
- `exportPdf(): Response` - Export to PDF (requires barryvdh/laravel-dompdf)

**Usage Example**:
```blade
{{-- In a route view --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-3">
            <h2>Form Submissions</h2>
            <a href="{{ route('slick-forms.builder', $formId) }}" class="btn btn-secondary">
                Edit Form
            </a>
        </div>

        <livewire:slick-forms::submission-viewer :formId="$formId" />
    </div>
@endsection
```

**Events Emitted**:
- `submission-viewed` - When submission detail opened
- `submission-deleted` - When submission deleted
- `submissions-exported` - When export initiated

**Related Documentation**:
- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)
- [Exports](EXPORTS.md)

---

## Management Components

### Manage

**Purpose**: Form management list with search, sorting, pagination, and bulk actions.

**Location**: `src/Livewire/Manage.php`

**Component Tag**:
```blade
<livewire:slick-forms::manage />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$forms` | `Collection` | Paginated forms |
| `$search` | `string` | Search query (persisted to query string) |
| `$sortField` | `string` | Field to sort by (name, status, created_at) |
| `$sortDirection` | `string` | Sort direction (asc, desc) |
| `$perPage` | `int` | Results per page (default: 15) |
| `$filterStatus` | `?string` | Filter by status (active, inactive, all) |
| `$filterTemplate` | `?bool` | Filter templates only |
| `$selectedForms` | `array` | Selected form IDs for bulk actions |

**Public Methods**:

#### Form Management
- `mount(): void` - Initialize component
- `createForm(): void` - Navigate to form creation
- `editForm(int $formId): void` - Navigate to form builder
- `viewForm(int $formId): void` - View form details
- `duplicateForm(int $formId): void` - Duplicate form
- `deleteForm(int $formId): void` - Delete form
- `toggleActive(int $formId): void` - Toggle form active status
- `bulkDelete(): void` - Delete selected forms
- `bulkToggleActive(): void` - Toggle active status for selected forms

#### Filtering & Sorting
- `updatedSearch(): void` - Update search results (debounced 300ms)
- `sortBy(string $field): void` - Sort by field (toggle direction)
- `setPerPage(int $perPage): void` - Change results per page
- `filterByStatus(string $status): void` - Filter by status
- `toggleTemplateFilter(): void` - Toggle template filter

#### Templates
- `saveAsTemplate(int $formId): void` - Save form as template
- `useTemplate(int $templateId): void` - Create form from template

**Query String Properties**:
```php
protected $queryString = [
    'search' => ['except' => ''],
    'sortField' => ['except' => 'created_at'],
    'sortDirection' => ['except' => 'desc'],
    'filterStatus' => ['except' => null],
];
```

**Usage Example**:
```blade
{{-- Management dashboard --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h1>Forms</h1>
            <a href="{{ route('slick-forms.create') }}" class="btn btn-primary">
                Create New Form
            </a>
        </div>

        <livewire:slick-forms::manage />
    </div>
@endsection
```

**Events Emitted**:
- `form-created` - When new form created
- `form-duplicated` - When form duplicated
- `form-deleted` - When form deleted
- `form-toggled` - When form active status changed

**Related Documentation**:
- [Templates](TEMPLATES.md)
- [URL Obfuscation](../CLAUDE.md#url-obfuscation-service)

---

### ManageStats

**Purpose**: Dashboard analytics widgets showing form statistics.

**Location**: `src/Livewire/ManageStats.php`

**Component Tag**:
```blade
<livewire:slick-forms::manage-stats />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$totalForms` | `int` | Total forms count |
| `$activeForms` | `int` | Active forms count |
| `$totalViews` | `int` | Total form views |
| `$totalStarts` | `int` | Total form starts |
| `$totalSubmissions` | `int` | Total submissions |
| `$totalAbandoned` | `int` | Total abandoned forms |
| `$completionRate` | `float` | Average completion rate (0-100) |
| `$abandonmentRate` | `float` | Average abandonment rate (0-100) |
| `$averageTime` | `int` | Average completion time in seconds |
| `$deviceBreakdown` | `array` | Device breakdown (desktop, tablet, mobile) |
| `$submissionsOverTime` | `array` | Submissions chart data |
| `$dateRange` | `string` | Date range filter (7d, 30d, 90d, all) |

**Public Methods**:

#### Statistics
- `mount(): void` - Initialize component and load statistics
- `refreshStats(): void` - Reload all statistics
- `setDateRange(string $range): void` - Change date range filter

#### Charts
- `getSubmissionsChartData(): array` - Get chart data for submissions over time
- `getDeviceBreakdownData(): array` - Get pie chart data for devices

**Usage Example**:
```blade
{{-- Dashboard with stats --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Dashboard</h1>

        {{-- Statistics Widgets --}}
        <livewire:slick-forms::manage-stats />

        {{-- Form List Below Stats --}}
        <div class="mt-5">
            <livewire:slick-forms::manage />
        </div>
    </div>
@endsection
```

**Events Emitted**:
- `stats-refreshed` - When statistics reloaded

**Related Documentation**:
- [Analytics](ANALYTICS.md)
- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)

---

### FormTemplates

**Purpose**: Template gallery browser with categorized templates and quick creation.

**Location**: `src/Livewire/FormTemplates.php`

**Component Tag**:
```blade
<livewire:slick-forms::form-templates />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$templates` | `Collection` | Available templates grouped by category |
| `$categories` | `array` | Template categories |
| `$selectedCategory` | `?string` | Active category filter |
| `$search` | `string` | Search query |
| `$expandedCategories` | `array` | Expanded category accordions |
| `$previewTemplate` | `?CustomForm` | Template being previewed |
| `$showPreviewModal` | `bool` | Show preview modal |
| `$newFormName` | `string` | Name for form created from template |

**Public Methods**:

#### Template Management
- `mount(): void` - Initialize component and load templates
- `filterByCategory(string $category): void` - Filter templates by category
- `toggleCategory(string $category): void` - Expand/collapse category accordion
- `previewTemplate(int $templateId): void` - Open template preview modal
- `closePreview(): void` - Close preview modal

#### Form Creation
- `useTemplate(int $templateId): void` - Create form from template
- `createBlankForm(): void` - Create blank form
- `duplicateTemplate(int $templateId): void` - Duplicate template

**Usage Example**:
```blade
{{-- Template gallery page --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <h1 class="text-center mb-4">Choose a Template</h1>
                <p class="text-center text-muted mb-5">
                    Start with a pre-built template or create a blank form
                </p>

                <livewire:slick-forms::form-templates />
            </div>
        </div>
    </div>
@endsection
```

**Events Emitted**:
- `template-previewed` - When template preview opened
- `form-created-from-template` - When form created from template

**Related Documentation**:
- [Templates](TEMPLATES.md)

---

## Analytics Components

### FormAnalytics

**Purpose**: Detailed analytics dashboard for individual forms with conversion funnel, field metrics, and time-based analysis.

**Location**: `src/Livewire/FormAnalytics.php`

**Component Tag**:
```blade
<livewire:slick-forms::form-analytics :formId="$formId" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$formId` | `int` | Form ID to analyze |
| `$form` | `CustomForm` | Form model instance |
| `$dateRange` | `string` | Date range filter (7d, 30d, 90d, all) |
| `$startDate` | `?string` | Custom start date |
| `$endDate` | `?string` | Custom end date |
| `$totalViews` | `int` | Total form views |
| `$totalStarts` | `int` | Total form starts |
| `$totalSubmissions` | `int` | Total submissions |
| `$completionRate` | `float` | Completion rate (0-100) |
| `$abandonmentRate` | `float` | Abandonment rate (0-100) |
| `$averageTime` | `int` | Average completion time in seconds |
| `$deviceBreakdown` | `array` | Device breakdown data |
| `$fieldInteractions` | `array` | Field-level interaction metrics |
| `$conversionFunnel` | `array` | Conversion funnel data |
| `$submissionsOverTime` | `array` | Time-series chart data |
| `$pageDropoff` | `array` | Page dropoff rates (multi-page forms) |

**Public Methods**:

#### Analytics
- `mount(int $formId): void` - Initialize component
- `refreshAnalytics(): void` - Reload analytics data
- `setDateRange(string $range): void` - Change date range
- `setCustomDateRange(string $startDate, string $endDate): void` - Set custom date range

#### Charts
- `getConversionFunnelData(): array` - Get funnel visualization data
- `getSubmissionsChartData(): array` - Get time-series chart data
- `getFieldInteractionData(): array` - Get field metrics data
- `getDeviceBreakdownData(): array` - Get device pie chart data

#### Export
- `exportAnalytics(): BinaryFileResponse` - Export analytics to PDF/Excel

**Usage Example**:
```blade
{{-- Analytics dashboard --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h2>Form Analytics</h2>
            <div class="btn-group">
                <a href="{{ route('slick-forms.builder', $formId) }}" class="btn btn-secondary">
                    Edit Form
                </a>
                <a href="{{ route('slick-forms.submissions', $formId) }}" class="btn btn-secondary">
                    View Submissions
                </a>
            </div>
        </div>

        <livewire:slick-forms::form-analytics :formId="$formId" />
    </div>
@endsection
```

**Events Emitted**:
- `analytics-refreshed` - When analytics data reloaded
- `analytics-exported` - When export initiated

**Related Documentation**:
- [Analytics](ANALYTICS.md)
- [Events](EVENTS.md)

---

## Log Viewer Components

### EmailLogsViewer

**Purpose**: View email notification delivery logs with filtering and retry functionality.

**Location**: `src/Livewire/EmailLogsViewer.php`

**Component Tag**:
```blade
<livewire:slick-forms::email-logs-viewer :formId="$formId" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$formId` | `int` | Form ID to view logs for |
| `$logs` | `Collection` | Paginated email logs |
| `$filterStatus` | `?string` | Filter by status (sent, failed, all) |
| `$search` | `string` | Search query (recipient email) |
| `$sortField` | `string` | Field to sort by (sent_at, status) |
| `$sortDirection` | `string` | Sort direction (asc, desc) |
| `$perPage` | `int` | Results per page (default: 25) |
| `$selectedLog` | `?FormEmailLog` | Selected log for detail view |
| `$showDetailModal` | `bool` | Show log detail modal |

**Public Methods**:

#### Log Management
- `mount(int $formId): void` - Initialize component
- `viewLog(int $logId): void` - View log details
- `closeDetailModal(): void` - Close detail modal
- `retryEmail(int $logId): void` - Retry failed email
- `deleteLog(int $logId): void` - Delete log entry

#### Filtering
- `filterByStatus(string $status): void` - Filter by delivery status
- `updatedSearch(): void` - Update search results
- `sortBy(string $field): void` - Sort by field

**Usage Example**:
```blade
{{-- Email logs page --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Email Notification Logs</h2>
        <livewire:slick-forms::email-logs-viewer :formId="$formId" />
    </div>
@endsection
```

**Events Emitted**:
- `email-retried` - When failed email retried
- `log-deleted` - When log entry deleted

**Related Documentation**:
- [Email Notifications](EMAIL_NOTIFICATIONS.md)
- [Events](EVENTS.md)

---

### SpamLogsViewer

**Purpose**: View spam attempt logs with IP tracking and blacklist management.

**Location**: `src/Livewire/SpamLogsViewer.php`

**Component Tag**:
```blade
<livewire:slick-forms::spam-logs-viewer :formId="$formId" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$formId` | `int` | Form ID to view spam logs for |
| `$logs` | `Collection` | Paginated spam logs |
| `$filterReason` | `?string` | Filter by detection reason |
| `$search` | `string` | Search query (IP address) |
| `$sortField` | `string` | Field to sort by (detected_at, ip_address) |
| `$sortDirection` | `string` | Sort direction (asc, desc) |
| `$perPage` | `int` | Results per page (default: 25) |
| `$selectedLog` | `?FormSpamLog` | Selected log for detail view |
| `$showDetailModal` | `bool` | Show log detail modal |
| `$blacklistedIps` | `array` | Currently blacklisted IPs |

**Public Methods**:

#### Log Management
- `mount(int $formId): void` - Initialize component
- `viewLog(int $logId): void` - View log details
- `closeDetailModal(): void` - Close detail modal
- `deleteLog(int $logId): void` - Delete log entry
- `clearLogs(): void` - Clear all spam logs

#### IP Blacklist Management
- `blacklistIp(string $ipAddress): void` - Add IP to blacklist
- `removeFromBlacklist(string $ipAddress): void` - Remove IP from blacklist
- `getBlacklistedIps(): array` - Get all blacklisted IPs

#### Filtering
- `filterByReason(string $reason): void` - Filter by detection reason
- `updatedSearch(): void` - Update search results
- `sortBy(string $field): void` - Sort by field

**Usage Example**:
```blade
{{-- Spam logs page --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Spam Protection Logs</h2>
        <livewire:slick-forms::spam-logs-viewer :formId="$formId" />
    </div>
@endsection
```

**Events Emitted**:
- `ip-blacklisted` - When IP added to blacklist
- `ip-removed-from-blacklist` - When IP removed from blacklist
- `log-deleted` - When log entry deleted

**Related Documentation**:
- [Spam Protection](SPAM_PROTECTION.md)
- [Events](EVENTS.md)

---

### WebhookLogsViewer

**Purpose**: View webhook delivery attempts with request/response payloads and retry functionality.

**Location**: `src/Livewire/WebhookLogsViewer.php`

**Component Tag**:
```blade
<livewire:slick-forms::webhook-logs-viewer :formId="$formId" />
```

**Properties**:

| Property | Type | Description |
|----------|------|-------------|
| `$formId` | `int` | Form ID to view webhook logs for |
| `$logs` | `Collection` | Paginated webhook logs |
| `$filterStatus` | `?string` | Filter by status (success, failed, all) |
| `$search` | `string` | Search query (webhook URL) |
| `$sortField` | `string` | Field to sort by (sent_at, status) |
| `$sortDirection` | `string` | Sort direction (asc, desc) |
| `$perPage` | `int` | Results per page (default: 25) |
| `$selectedLog` | `?FormWebhookLog` | Selected log for detail view |
| `$showDetailModal` | `bool` | Show log detail modal |

**Public Methods**:

#### Log Management
- `mount(int $formId): void` - Initialize component
- `viewLog(int $logId): void` - View log details with request/response
- `closeDetailModal(): void` - Close detail modal
- `retryWebhook(int $logId): void` - Retry failed webhook
- `deleteLog(int $logId): void` - Delete log entry

#### Filtering
- `filterByStatus(string $status): void` - Filter by delivery status
- `updatedSearch(): void` - Update search results
- `sortBy(string $field): void` - Sort by field

**Usage Example**:
```blade
{{-- Webhook logs page --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Webhook Delivery Logs</h2>
        <livewire:slick-forms::webhook-logs-viewer :formId="$formId" />
    </div>
@endsection
```

**Events Emitted**:
- `webhook-retried` - When failed webhook retried
- `log-deleted` - When log entry deleted

**Related Documentation**:
- [Webhooks](WEBHOOKS.md)
- [Events](EVENTS.md)

---

## Traits

### ManagesSchemaProperties

**Purpose**: Handles loading and saving properties between Livewire components and database for schema-driven properties panel.

**Location**: `src/Livewire/Concerns/ManagesSchemaProperties.php`

**Used By**:
- `FormBuilder` - For field and element property management

**Methods**:

#### Property Loading
- `loadFieldProperties(CustomFormField $field): void` - Load field properties into Livewire properties
- `loadElementProperties(SlickFormLayoutElement $element): void` - Load element properties

#### Property Saving
- `saveFieldProperties(CustomFormField $field): void` - Save Livewire properties to field model
- `saveElementProperties(SlickFormLayoutElement $element): void` - Save properties to element model

#### Property Mapping
- `getPropertyValue(string $propertyName): mixed` - Get property value from Livewire state
- `setPropertyValue(string $propertyName, mixed $value): void` - Set property value in Livewire
- `mapPropertiesToDatabase(array $properties): array` - Map Livewire properties to database JSON

**Usage Example**:
```php
use DigitalisStudios\SlickForms\Livewire\Concerns\ManagesSchemaProperties;

class FormBuilder extends Component
{
    use ManagesSchemaProperties;

    public function selectField(int $fieldId): void
    {
        $this->selectedField = CustomFormField::find($fieldId);

        // Load all field properties into Livewire properties
        $this->loadFieldProperties($this->selectedField);

        $this->showFieldEditor = true;
    }

    public function updateField(): void
    {
        // Save all Livewire properties to field model
        $this->saveFieldProperties($this->selectedField);

        $this->dispatch('field-updated', $this->selectedField->id);
    }
}
```

**Related Documentation**:
- [Schema-Driven Properties System](../CLAUDE.md#schema-driven-properties-system)

---

## Usage Examples

### Complete Form Management Workflow

```blade
{{-- Dashboard with all management components --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Form Management Dashboard</h1>
            <div class="btn-group">
                <a href="{{ route('slick-forms.create') }}" class="btn btn-primary">
                    Create New Form
                </a>
                <a href="{{ route('slick-forms.templates') }}" class="btn btn-secondary">
                    Browse Templates
                </a>
            </div>
        </div>

        {{-- Statistics Widgets --}}
        <livewire:slick-forms::manage-stats />

        {{-- Form List --}}
        <div class="mt-5">
            <livewire:slick-forms::manage />
        </div>
    </div>
@endsection
```

### Form Analytics Dashboard

```blade
{{-- Complete analytics view --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Navigation --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('slick-forms.manage') }}">Forms</a></li>
                <li class="breadcrumb-item"><a href="{{ route('slick-forms.builder', $formId) }}">Edit</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </nav>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('slick-forms.builder', $formId) }}">Builder</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('slick-forms.submissions', $formId) }}">Submissions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active">Analytics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('slick-forms.logs.email', $formId) }}">Email Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('slick-forms.logs.webhook', $formId) }}">Webhook Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('slick-forms.logs.spam', $formId) }}">Spam Logs</a>
            </li>
        </ul>

        {{-- Analytics Component --}}
        <livewire:slick-forms::form-analytics :formId="$formId" />
    </div>
@endsection
```

### Log Management Tabs

```blade
{{-- Tabbed log viewers --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Form Logs: {{ $form->name }}</h2>

        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#email-logs">Email Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#webhook-logs">Webhook Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#spam-logs">Spam Logs</a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content">
            <div class="tab-pane fade show active" id="email-logs">
                <livewire:slick-forms::email-logs-viewer :formId="$formId" />
            </div>
            <div class="tab-pane fade" id="webhook-logs">
                <livewire:slick-forms::webhook-logs-viewer :formId="$formId" />
            </div>
            <div class="tab-pane fade" id="spam-logs">
                <livewire:slick-forms::spam-logs-viewer :formId="$formId" />
            </div>
        </div>
    </div>
@endsection
```

### Public Form with Pre-fill

```blade
{{-- Public form route with encrypted pre-fill data --}}
@extends('layouts.guest')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Form Header --}}
                <div class="text-center mb-5">
                    <h1>{{ $form->name }}</h1>
                    @if($form->description)
                        <p class="lead text-muted">{{ $form->description }}</p>
                    @endif
                </div>

                {{-- Form Renderer --}}
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <livewire:slick-forms::form-renderer
                            :form="$form"
                            :prefillData="$prefillData ?? []"
                        />
                    </div>
                </div>

                {{-- Form Footer --}}
                <div class="text-center mt-4 text-muted small">
                    <p>Powered by Slick Forms</p>
                </div>
            </div>
        </div>
    </div>
@endsection
```

---

## Component Registration

All Livewire components are automatically registered in `SlickFormsServiceProvider`:

```php
// src/SlickFormsServiceProvider.php

public function boot(): void
{
    // Register Livewire components with slick-forms:: prefix
    Livewire::component('slick-forms::form-builder', FormBuilder::class);
    Livewire::component('slick-forms::form-renderer', FormRenderer::class);
    Livewire::component('slick-forms::submission-viewer', SubmissionViewer::class);
    Livewire::component('slick-forms::manage', Manage::class);
    Livewire::component('slick-forms::manage-stats', ManageStats::class);
    Livewire::component('slick-forms::form-templates', FormTemplates::class);
    Livewire::component('slick-forms::form-analytics', FormAnalytics::class);
    Livewire::component('slick-forms::email-logs-viewer', EmailLogsViewer::class);
    Livewire::component('slick-forms::spam-logs-viewer', SpamLogsViewer::class);
    Livewire::component('slick-forms::webhook-logs-viewer', WebhookLogsViewer::class);
}
```

---

## Best Practices

### 1. Component Isolation

Each component is self-contained with its own state management. Avoid sharing state between components through global variables.

**Good**:
```blade
<livewire:slick-forms::form-builder :form="$form" />
```

**Bad**:
```blade
{{-- Don't share state via JavaScript globals --}}
<script>window.currentForm = @json($form);</script>
<livewire:slick-forms::form-builder />
```

### 2. Event Communication

Use Livewire events for communication between components:

```php
// Dispatch event from one component
$this->dispatch('form-updated', $formId);

// Listen in another component
protected $listeners = ['form-updated' => 'refreshForm'];

public function refreshForm($formId): void
{
    $this->form = CustomForm::find($formId);
}
```

### 3. Query String Persistence

Use query strings for shareable URLs:

```php
protected $queryString = [
    'search' => ['except' => ''],
    'sortField' => ['except' => 'created_at'],
    'page' => ['except' => 1],
];
```

This allows users to bookmark filtered/sorted views.

### 4. Loading States

Add loading states for better UX:

```blade
<div wire:loading wire:target="submit" class="spinner-border">
    <span class="visually-hidden">Loading...</span>
</div>

<button type="submit" wire:loading.attr="disabled">
    Submit Form
</button>
```

### 5. Error Handling

Display validation errors clearly:

```blade
@error('formData.email')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
```

### 6. Performance Optimization

Use lazy loading for components not immediately visible:

```blade
{{-- Load component on tab activation --}}
<div class="tab-pane" id="analytics">
    @if($activeTab === 'analytics')
        <livewire:slick-forms::form-analytics :formId="$formId" />
    @endif
</div>
```

---

## Troubleshooting

### Component Not Found

**Error**: `Component [slick-forms::form-builder] not found`

**Solution**: Ensure `SlickFormsServiceProvider` is registered in `config/app.php`:

```php
'providers' => [
    // ...
    DigitalisStudios\SlickForms\SlickFormsServiceProvider::class,
],
```

### Properties Not Updating

**Issue**: Properties panel changes not saving

**Solution**: Check that `wire:model` bindings are correct and component is using `ManagesSchemaProperties` trait:

```blade
{{-- Correct --}}
<input wire:model="label" type="text">

{{-- Incorrect (missing wire:model) --}}
<input type="text" value="{{ $label }}">
```

### Livewire Events Not Firing

**Issue**: Events dispatched but not received

**Solution**: Ensure listener is registered in component:

```php
protected $listeners = ['event-name' => 'methodName'];
```

Or use `#[On('event-name')]` attribute:

```php
#[On('event-name')]
public function methodName(): void
{
    // Handle event
}
```

### Performance Issues with Large Forms

**Issue**: Builder slow with 100+ fields

**Solution**: Enable pagination in builder or use lazy loading:

```php
// In FormBuilder component
public function getFieldsProperty()
{
    return $this->form->fields()
        ->where('page_id', $this->currentPageId)
        ->get(); // Only load current page fields
}
```

---

## Related Documentation

- [CLAUDE.md - Livewire Component Architecture](../CLAUDE.md#livewire-3-component-architecture)
- [EVENTS.md - Event Reference](EVENTS.md)
- [SCHEMA_REFERENCE.md - Properties Schema](SCHEMA_REFERENCE.md)
- [ANALYTICS.md - Analytics System](ANALYTICS.md)
- [TEMPLATES.md - Form Templates](TEMPLATES.md)

---

**Last Updated**: 2025-01-04
**Package Version**: v2.0.0
**Livewire Version**: 3.x

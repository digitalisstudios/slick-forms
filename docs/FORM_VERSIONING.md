# Form Versioning

**Slick Forms v2.0+** - Complete guide for form version tracking, comparison, and restoration

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Database Schema](#database-schema)
- [Automatic Versioning](#automatic-versioning)
- [Manual Version Creation](#manual-version-creation)
- [Viewing Version History](#viewing-version-history)
- [Version Comparison](#version-comparison)
- [Restoring Versions](#restoring-versions)
- [Configuration](#configuration)
- [API Reference](#api-reference)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## Overview

The Form Versioning system allows you to track changes to your forms over time, compare different versions, and restore previous versions if needed. Every time you publish changes to a form, Slick Forms creates a complete snapshot that can be restored later.

### Why Version Control?

- **Safety Net**: Easily revert problematic changes
- **Change Tracking**: See exactly what changed between versions
- **Audit Trail**: Track who made changes and when
- **Experimentation**: Try new designs knowing you can roll back
- **Compliance**: Maintain records of form changes for regulatory requirements

---

## Features

### Automatic Snapshots

When you save changes to a form in the builder, Slick Forms automatically creates a version snapshot containing:

- Form settings (name, description, status)
- All fields with their complete configuration
- Layout structure (containers, rows, columns, cards, etc.)
- Multi-page configuration
- Validation rules and conditional logic

### Version Metadata

Each version includes:

- **Version Number**: Incremental version number (1, 2, 3...)
- **Version Name**: Optional human-readable name
- **Change Summary**: Description of what changed
- **Published By**: User who created the version
- **Published At**: Timestamp of version creation
- **Form Snapshot**: Complete JSON snapshot of form state

### Version Operations

- Create manual versions with custom names
- View complete version history
- Compare two versions side-by-side
- Restore any previous version
- Delete unused versions (with safety checks)

---

## Database Schema

Versions are stored in the `slick_form_versions` table:

```php
Schema::create('slick_form_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('slick_forms')->cascadeOnDelete();
    $table->integer('version_number'); // 1, 2, 3...
    $table->string('version_name')->nullable(); // "Pre-launch", "Bug fixes", etc.
    $table->json('form_snapshot'); // Complete form state
    $table->foreignId('published_by')->nullable()->constrained('users');
    $table->text('change_summary')->nullable();
    $table->timestamp('published_at');
    $table->timestamps();

    $table->index(['form_id', 'version_number']);
});
```

### Snapshot Structure

The `form_snapshot` JSON contains:

```json
{
  "form": {
    "name": "Contact Form",
    "description": "Get in touch with us",
    "is_active": true,
    "settings": {...}
  },
  "fields": [
    {
      "id": 1,
      "field_type": "text",
      "name": "full_name",
      "label": "Full Name",
      "validation_rules": ["required", "max:255"],
      "conditional_logic": {...},
      "options": {...},
      ...
    }
  ],
  "layout_elements": [
    {
      "id": 1,
      "parent_id": null,
      "element_type": "container",
      "settings": {...},
      "order": 0
    }
  ],
  "pages": [
    {
      "id": 1,
      "title": "Page 1",
      "description": null,
      "order": 0,
      "settings": {...}
    }
  ]
}
```

---

## Automatic Versioning

### When Versions Are Created

Slick Forms automatically creates a new version when:

1. **Form Builder Save**: Any changes saved in the form builder
2. **Settings Update**: Form settings are modified
3. **Field Changes**: Fields are added, removed, or modified
4. **Layout Changes**: Layout structure is changed
5. **Conditional Logic**: Conditional logic rules are updated

### Auto-Generated Change Summary

If you don't provide a custom change summary, Slick Forms generates one automatically:

```
Version 5: Form snapshot with 12 fields, 8 layout elements, and 1 pages.
```

### Disabling Auto-Versioning

Auto-versioning cannot be disabled to ensure you always have a history of changes. However, you can control when versions are created by using manual versioning.

---

## Manual Version Creation

Create versions programmatically using the `FormVersionService`:

### Basic Version Creation

```php
use DigitalisStudios\SlickForms\Services\FormVersionService;

$service = app(FormVersionService::class);
$form = CustomForm::find(1);

// Create version with auto-generated summary
$version = $service->createVersion($form);
```

### Named Version with Custom Summary

```php
// Create version with custom name and summary
$version = $service->createVersion(
    form: $form,
    userId: auth()->id(),
    versionName: 'Pre-Launch Backup',
    changeSummary: 'Final form state before public launch'
);

echo "Created version {$version->version_number}: {$version->version_name}";
```

### Version with User Attribution

```php
// Create version attributed to specific user
$version = $service->createVersion(
    form: $form,
    userId: $adminUser->id,
    versionName: 'Marketing Updates',
    changeSummary: 'Updated copy and added analytics tracking fields'
);
```

---

## Viewing Version History

### Get All Versions

```php
$service = app(FormVersionService::class);
$form = CustomForm::find(1);

// Get all versions ordered by version number (newest first)
$versions = $service->getVersionHistory($form);

foreach ($versions as $version) {
    echo "Version {$version->version_number}: {$version->version_name}\n";
    echo "Published: {$version->published_at->format('M d, Y H:i')}\n";
    echo "By: {$version->publisher->name}\n";
    echo "Summary: {$version->change_summary}\n\n";
}
```

### Get Latest Version

```php
// Get the most recent version
$latestVersion = $service->getLatestVersion($form);

if ($latestVersion) {
    echo "Latest version: {$latestVersion->version_number}";
    echo " (created {$latestVersion->published_at->diffForHumans()})";
}
```

### Check for Unsaved Changes

```php
// Check if form has changes since last version
$hasChanges = $service->hasChanges($form);

if ($hasChanges) {
    echo "Form has unsaved changes. Consider creating a new version.";
}
```

### Display in Admin Interface

```blade
{{-- In your admin panel --}}
<h3>Version History</h3>

<table class="table">
    <thead>
        <tr>
            <th>Version</th>
            <th>Name</th>
            <th>Changes</th>
            <th>Published By</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($versions as $version)
        <tr>
            <td>{{ $version->version_number }}</td>
            <td>{{ $version->version_name ?? '-' }}</td>
            <td>{{ $version->change_summary }}</td>
            <td>{{ $version->publisher->name ?? 'System' }}</td>
            <td>{{ $version->published_at->format('M d, Y H:i') }}</td>
            <td>
                <a href="{{ route('forms.versions.view', $version) }}">View</a>
                <a href="{{ route('forms.versions.restore', $version) }}"
                   onclick="return confirm('Restore this version?')">
                    Restore
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

---

## Version Comparison

Compare two versions to see what changed:

### Basic Comparison

```php
$service = app(FormVersionService::class);

$version1 = FormVersion::find(5); // Older version
$version2 = FormVersion::find(6); // Newer version

$differences = $service->compareVersions($version1, $version2);

print_r($differences);
```

### Comparison Output

```php
[
    'form' => [
        'name' => [
            'from' => 'Contact Form',
            'to' => 'Contact Us Form'
        ],
        'is_active' => [
            'from' => false,
            'to' => true
        ]
    ],
    'field_count' => [
        'from' => 10,
        'to' => 12
    ],
    'element_count' => [
        'from' => 8,
        'to' => 10
    ],
    'page_count' => [
        'from' => 1,
        'to' => 1
    ]
]
```

### Display Comparison in UI

```blade
<h3>Comparing Version {{ $version1->version_number }} → {{ $version2->version_number }}</h3>

@if(isset($differences['form']))
    <h4>Form Settings Changed</h4>
    <ul>
        @foreach($differences['form'] as $key => $change)
            <li>
                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                <span class="text-danger">{{ $change['from'] }}</span>
                →
                <span class="text-success">{{ $change['to'] }}</span>
            </li>
        @endforeach
    </ul>
@endif

<h4>Counts</h4>
<ul>
    <li>Fields: {{ $differences['field_count']['from'] }} → {{ $differences['field_count']['to'] }}</li>
    <li>Layout Elements: {{ $differences['element_count']['from'] }} → {{ $differences['element_count']['to'] }}</li>
    <li>Pages: {{ $differences['page_count']['from'] }} → {{ $differences['page_count']['to'] }}</li>
</ul>
```

---

## Restoring Versions

Restore a form to a previous version state:

### Basic Restoration

```php
$service = app(FormVersionService::class);
$form = CustomForm::find(1);
$version = FormVersion::find(5);

// Restore form to version 5
$success = $service->restoreVersion($form, $version);

if ($success) {
    echo "Form restored to version {$version->version_number}";
} else {
    echo "Failed to restore version";
}
```

### What Gets Restored

When restoring a version, the following are completely replaced:

1. **Form Settings**: Name, description, status, all settings
2. **All Fields**: Deleted and recreated from snapshot
3. **Layout Elements**: Deleted and recreated with correct parent/child relationships
4. **Pages**: Deleted and recreated

### Restoration Process

The restoration process:

1. Begins database transaction for safety
2. Updates form settings
3. Deletes all current fields
4. Deletes all current layout elements (including nested)
5. Deletes all current pages
6. Recreates pages from snapshot
7. Recreates layout elements (handles parent_id relationships)
8. Recreates fields (maps to new layout element IDs)
9. Commits transaction

### Safety Features

- **Transactional**: All-or-nothing restoration (rolls back on error)
- **ID Remapping**: Correctly maintains parent/child relationships
- **Deep Nesting**: Handles up to 10 levels of nested layout elements
- **Error Handling**: Throws exception on failure (caught by transaction)

### Example Controller

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormVersion;
use DigitalisStudios\SlickForms\Services\FormVersionService;

class FormVersionController extends Controller
{
    public function restore(Request $request, CustomForm $form, FormVersion $version)
    {
        // Verify version belongs to form
        if ($version->form_id !== $form->id) {
            abort(403, 'Version does not belong to this form');
        }

        $service = app(FormVersionService::class);

        try {
            // Create backup before restoring
            $backup = $service->createVersion(
                form: $form,
                userId: auth()->id(),
                versionName: 'Pre-restore backup',
                changeSummary: "Backup before restoring to version {$version->version_number}"
            );

            // Restore the selected version
            $service->restoreVersion($form, $version);

            return redirect()
                ->route('forms.builder', $form)
                ->with('success', "Form restored to version {$version->version_number}");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore version: ' . $e->getMessage());
        }
    }
}
```

---

## Configuration

Configure versioning behavior in `config/slick-forms.php`:

```php
return [
    'versioning' => [
        // Enable automatic versioning on form save
        'auto_version_on_save' => env('SLICK_FORMS_AUTO_VERSION', true),

        // Automatically create version with custom name on specific events
        'auto_version_events' => [
            'form_published' => 'Published Version',
            'form_unpublished' => 'Unpublished Version',
        ],

        // Maximum versions to keep per form (null = unlimited)
        'max_versions_per_form' => env('SLICK_FORMS_MAX_VERSIONS', null),

        // Automatically prune old versions (keep most recent X)
        'prune_old_versions' => env('SLICK_FORMS_PRUNE_VERSIONS', false),
        'keep_recent_versions' => 50,

        // Version naming strategy
        'version_naming' => [
            'auto_prefix' => 'Version',
            'include_timestamp' => false,
        ],
    ],
];
```

### Environment Variables

```bash
# Enable/disable auto-versioning
SLICK_FORMS_AUTO_VERSION=true

# Maximum versions to keep
SLICK_FORMS_MAX_VERSIONS=100

# Automatically prune old versions
SLICK_FORMS_PRUNE_VERSIONS=false
```

---

## API Reference

### FormVersionService

#### `createVersion()`

Create a new form version snapshot.

```php
public function createVersion(
    CustomForm $form,
    ?int $userId = null,
    ?string $versionName = null,
    ?string $changeSummary = null
): FormVersion
```

**Parameters:**
- `$form` - Form to create version for
- `$userId` - User ID who created the version (optional)
- `$versionName` - Human-readable version name (optional)
- `$changeSummary` - Description of changes (auto-generated if null)

**Returns:** Created `FormVersion` instance

**Example:**
```php
$version = $service->createVersion(
    form: $form,
    userId: 1,
    versionName: 'Launch Day',
    changeSummary: 'Final version before launch'
);
```

---

#### `restoreVersion()`

Restore form to a previous version.

```php
public function restoreVersion(
    CustomForm $form,
    FormVersion $version
): bool
```

**Parameters:**
- `$form` - Form to restore
- `$version` - Version to restore to

**Returns:** `true` on success

**Throws:** `\Exception` on failure (within transaction, so it rolls back)

**Example:**
```php
try {
    $service->restoreVersion($form, $version);
    echo "Restored successfully";
} catch (\Exception $e) {
    echo "Restore failed: " . $e->getMessage();
}
```

---

#### `getVersionHistory()`

Get all versions for a form, ordered by version number descending.

```php
public function getVersionHistory(CustomForm $form): Collection
```

**Parameters:**
- `$form` - Form to get versions for

**Returns:** Laravel `Collection` of `FormVersion` instances (with `publisher` relation loaded)

**Example:**
```php
$versions = $service->getVersionHistory($form);

foreach ($versions as $version) {
    echo "{$version->version_number}: {$version->version_name}\n";
}
```

---

#### `getLatestVersion()`

Get the most recent version for a form.

```php
public function getLatestVersion(CustomForm $form): ?FormVersion
```

**Parameters:**
- `$form` - Form to get latest version for

**Returns:** Latest `FormVersion` or `null` if no versions exist

**Example:**
```php
$latest = $service->getLatestVersion($form);

if ($latest) {
    echo "Latest version: {$latest->version_number}";
}
```

---

#### `compareVersions()`

Compare two versions and return differences.

```php
public function compareVersions(
    FormVersion $version1,
    FormVersion $version2
): array
```

**Parameters:**
- `$version1` - Older version
- `$version2` - Newer version

**Returns:** Array of differences (form settings, field count, element count, page count)

**Example:**
```php
$diffs = $service->compareVersions($oldVersion, $newVersion);

echo "Fields changed: {$diffs['field_count']['from']} → {$diffs['field_count']['to']}";
```

---

#### `hasChanges()`

Check if form has unsaved changes since last version.

```php
public function hasChanges(CustomForm $form): bool
```

**Parameters:**
- `$form` - Form to check

**Returns:** `true` if form has changed, `false` if unchanged or no versions exist

**Example:**
```php
if ($service->hasChanges($form)) {
    echo "You have unsaved changes";
}
```

---

#### `deleteVersion()`

Delete a specific version (admin only).

```php
public function deleteVersion(FormVersion $version): bool
```

**Parameters:**
- `$version` - Version to delete

**Returns:** `true` on success

**Throws:** `\RuntimeException` if version has associated submissions

**Example:**
```php
try {
    $service->deleteVersion($version);
    echo "Version deleted";
} catch (\RuntimeException $e) {
    echo "Cannot delete: " . $e->getMessage();
}
```

---

#### `buildFormSnapshot()`

Build complete snapshot of current form state (used internally).

```php
public function buildFormSnapshot(CustomForm $form): array
```

**Parameters:**
- `$form` - Form to snapshot

**Returns:** Array containing form, fields, layout_elements, and pages

---

## Best Practices

### 1. Name Important Versions

Give meaningful names to major versions:

```php
// Good
$service->createVersion($form, auth()->id(), 'Pre-Launch', 'Final QA approved version');

// Not as helpful
$service->createVersion($form); // Auto-generates generic name
```

### 2. Create Versions Before Major Changes

Always create a named backup before making significant changes:

```php
// Before major redesign
$backup = $service->createVersion(
    form: $form,
    userId: auth()->id(),
    versionName: 'Pre-Redesign Backup',
    changeSummary: 'Backup before v2.0 redesign'
);

// Now make your changes safely
```

### 3. Review Changes Before Restoring

Always compare versions before restoring:

```php
// Compare current state with version you want to restore
$currentVersion = $service->getLatestVersion($form);
$restoreVersion = FormVersion::find($oldVersionId);

$diffs = $service->compareVersions($restoreVersion, $currentVersion);

// Review differences before proceeding
if ($diffs['field_count']['to'] - $diffs['field_count']['from'] > 5) {
    echo "Warning: This will remove {$diffs['field_count']['to'] - $diffs['field_count']['from']} fields!";
}
```

### 4. Implement Version Approval Workflow

For production forms, implement approval workflow:

```php
// Draft versions
$draftVersion = $service->createVersion(
    form: $form,
    userId: $editor->id,
    versionName: 'Draft: Marketing Copy Updates',
    changeSummary: 'Updated all marketing copy per brand guidelines'
);

// After review and approval
$approvedVersion = $service->createVersion(
    form: $form,
    userId: $approver->id,
    versionName: 'Approved: Marketing Copy Updates',
    changeSummary: 'Approved by CMO on ' . now()->format('Y-m-d')
);

// Then restore to production
$service->restoreVersion($productionForm, $approvedVersion);
```

### 5. Monitor Version Count

Keep track of version counts for large forms:

```php
$versionCount = FormVersion::where('form_id', $form->id)->count();

if ($versionCount > 100) {
    // Consider pruning old versions
    $oldVersions = FormVersion::where('form_id', $form->id)
        ->orderBy('version_number')
        ->take($versionCount - 50)
        ->get();

    foreach ($oldVersions as $oldVersion) {
        try {
            $service->deleteVersion($oldVersion);
        } catch (\RuntimeException $e) {
            // Skip versions with submissions
            continue;
        }
    }
}
```

---

## Troubleshooting

### Version Restoration Fails

**Problem**: Restoration throws an exception

**Possible Causes:**
1. Database transaction timeout for very large forms
2. Circular parent_id references in layout elements
3. Invalid field references to deleted layout elements

**Solutions:**
```php
// 1. Increase transaction timeout in config/database.php
'options' => [
    PDO::ATTR_TIMEOUT => 60, // Increase from default 30
],

// 2. Verify version snapshot integrity before restoring
$snapshot = $version->form_snapshot;
$layoutElements = collect($snapshot['layout_elements'] ?? []);

// Check for circular references
foreach ($layoutElements as $element) {
    if ($element['parent_id']) {
        $parent = $layoutElements->firstWhere('id', $element['parent_id']);
        if (!$parent) {
            echo "Warning: Element {$element['id']} has invalid parent {$element['parent_id']}";
        }
    }
}
```

---

### Versions Not Being Created

**Problem**: Auto-versioning not working

**Check:**
1. Configuration is enabled
2. Form save is actually completing
3. Database permissions allow inserts

**Debug:**
```php
// Check configuration
dump(config('slick-forms.versioning.auto_version_on_save'));

// Manually test version creation
try {
    $version = $service->createVersion($form);
    echo "Version created successfully: {$version->id}";
} catch (\Exception $e) {
    echo "Failed: {$e->getMessage()}";
}
```

---

### Snapshot Size Too Large

**Problem**: JSON snapshots exceeding database limits

**Solution:**
Increase `json` column size or use `longtext`:

```php
// In migration
$table->longText('form_snapshot')->change();
```

Or optimize snapshots by removing unnecessary data:

```php
// Custom snapshot builder that excludes analytics
public function buildOptimizedSnapshot(CustomForm $form): array
{
    $snapshot = $service->buildFormSnapshot($form);

    // Remove unnecessary data
    unset($snapshot['form']['analytics_data']);

    foreach ($snapshot['fields'] as &$field) {
        unset($field['cached_preview']);
    }

    return $snapshot;
}
```

---

### Cannot Delete Old Versions

**Problem**: `deleteVersion()` throws "Cannot delete version with existing submissions"

**Solution:**

Versions with submissions cannot be deleted to maintain data integrity. You can:

1. **Archive instead of delete**:
```php
$version->update(['archived' => true]);
```

2. **Only delete versions without submissions**:
```php
$versions = FormVersion::where('form_id', $form->id)
    ->doesntHave('submissions')
    ->oldest()
    ->take(20)
    ->get();

foreach ($versions as $version) {
    $service->deleteVersion($version); // Safe - no submissions
}
```

---

### Version Comparison Shows No Differences

**Problem**: `compareVersions()` returns empty array but forms look different

**Cause:** Comparison only checks high-level metrics (counts, form settings), not field-by-field differences.

**Solution:** Implement detailed comparison:

```php
$snap1 = $version1->form_snapshot;
$snap2 = $version2->form_snapshot;

// Compare field names
$fields1 = collect($snap1['fields'])->pluck('name');
$fields2 = collect($snap2['fields'])->pluck('name');

$addedFields = $fields2->diff($fields1);
$removedFields = $fields1->diff($fields2);

echo "Added fields: " . $addedFields->implode(', ') . "\n";
echo "Removed fields: " . $removedFields->implode(', ') . "\n";
```

---

## Events

### FormVersionPublished

Dispatched when a new version is created.

```php
use DigitalisStudios\SlickForms\Events\FormVersionPublished;

Event::listen(FormVersionPublished::class, function ($event) {
    $version = $event->version;
    $form = $version->form;

    Log::info("New version created", [
        'form_id' => $form->id,
        'version_number' => $version->version_number,
        'version_name' => $version->version_name,
    ]);
});
```

**Event Properties:**
- `$version` - Created `FormVersion` instance
- `$form` - `CustomForm` instance

**Use Cases:**
- Send notifications to admins
- Update external systems
- Trigger backup processes
- Log audit trail

---

## Related Documentation

- [Events Reference](EVENTS.md#formversionpublished) - Event system documentation
- [Form Builder Guide](FORM_BUILDER.md) - Using the form builder interface
- [API Documentation](API.md) - REST API endpoints for versions

---

## Summary

Form versioning provides a complete audit trail and safety net for your forms:

- ✅ Automatic snapshots on every save
- ✅ Manual version creation with custom names
- ✅ Complete version history
- ✅ Side-by-side version comparison
- ✅ One-click version restoration
- ✅ Transactional safety during restoration
- ✅ User attribution and timestamps
- ✅ Event-driven integration

With version control, you can confidently make changes to your forms knowing you can always roll back if needed.

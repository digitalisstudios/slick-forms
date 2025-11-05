# Migration Guide - Upgrading to Schema-Driven Properties

**Status:** Complete Migration Guide for Slick Forms v2.1+
**Last Updated:** 2025-10-27
**Target Audience:** Developers with existing custom field types or layout elements

---

## Table of Contents

1. [Overview](#overview)
2. [What's Changed](#whats-changed)
3. [Why Migrate](#why-migrate)
4. [Before You Begin](#before-you-begin)
5. [Migration Checklist](#migration-checklist)
6. [Step-by-Step Migration](#step-by-step-migration)
7. [Common Migration Scenarios](#common-migration-scenarios)
8. [Troubleshooting](#troubleshooting)
9. [Testing After Migration](#testing-after-migration)
10. [Rollback Plan](#rollback-plan)

---

## Overview

Slick Forms v2.1+ introduces a **schema-driven properties panel** system that replaces the old hardcoded approach. This guide helps you migrate existing custom field types and layout elements to the new system.

### What This Guide Covers

- Migrating custom field types from manual Blade files to schema definitions
- Updating existing field types to use `target` metadata
- Converting hardcoded properties to schema-driven properties
- Testing and validating migrations
- Rollback if issues occur

### What This Guide Does NOT Cover

- Creating new field types from scratch (see `CUSTOM_FIELD_TYPES.md`)
- Creating new layout elements (see `CUSTOM_LAYOUT_ELEMENTS.md`)
- Schema reference (see `SCHEMA_REFERENCE.md`)

---

## What's Changed

### Old System (Pre-v2.1)

**Properties Panel:**
- 1,480 lines of hardcoded Blade templates
- Fixed 5 tabs (basic, options, validation, style, advanced)
- Manual Blade files required for field-specific options
- No extensibility for custom tabs

**FormBuilder Component:**
- 50+ individual Livewire properties (`$fieldLabel`, `$fieldName`, `$fieldPlaceholder`, etc.)
- Manual property management in `editField()` and `saveField()` methods
- Hardcoded logic for each property

**Field Types:**
- Required manual Blade file in `resources/views/livewire/partials/properties-panel/field-options/{field-type}.blade.php`
- No schema definition
- Properties stored in `options` JSON without structure

**Layout Elements:**
- Hardcoded strings, not extensible
- No properties panel for layout customization
- Custom layout elements impossible without modifying package code

### New System (v2.1+)

**Properties Panel:**
- 247 lines (83% reduction)
- Dynamic tabs registered by field/element types
- Auto-generated UI from schema definitions
- Fully extensible

**FormBuilder Component:**
- 2 arrays: `$properties` and `$elementProperties`
- Schema-driven property management via `ManagesSchemaProperties` trait
- `editFieldV3()`, `saveFieldV3()`, `editElementV3()`, `saveElementV3()` methods

**Field Types:**
- Schema definition in `getConfigSchema()` method
- No manual Blade files needed
- Structured properties with `target` metadata

**Layout Elements:**
- Full-fledged types extending `BaseLayoutElementType`
- Schema-driven properties panel
- Custom layout elements easily created

---

## Why Migrate

### Benefits of Migration

1. **Less Code to Maintain**
   - Remove manual Blade templates
   - No property duplication
   - Schema is self-documenting

2. **Automatic UI Generation**
   - Add/remove properties by updating schema
   - Consistent UI across all types
   - No frontend coding required

3. **Better Developer Experience**
   - Clear property definitions
   - Type-safe with defaults
   - Help text and validation built-in

4. **Extensibility**
   - Add custom tabs
   - Custom field types integrate seamlessly
   - Layout elements now customizable

5. **Future-Proof**
   - New features added to schema system automatically benefit your types
   - Easier to upgrade

### When to Migrate

**Migrate if:**
- You have custom field types with manual Blade property files
- You want to add custom tabs to your field types
- You're creating new custom field types
- You want to take advantage of layout element extensibility

**Delay migration if:**
- You need to ship critical features immediately
- You have extensive custom validation that needs careful review
- Your custom types use very complex UI that can't be schema-driven (use custom `view` property instead)

---

## Before You Begin

### Prerequisites

- ✅ Slick Forms v2.1+ installed
- ✅ Backup of existing custom field types
- ✅ Understanding of your current field types' functionality
- ✅ Test environment available
- ✅ Review of `SCHEMA_REFERENCE.md`

### Backup Checklist

1. **Backup Custom Field Type Classes**
   ```bash
   cp -r app/SlickForms/FieldTypes app/SlickForms/FieldTypes.backup
   ```

2. **Backup Custom Blade Files**
   ```bash
   cp -r resources/views/vendor/slick-forms resources/views/vendor/slick-forms.backup
   ```

3. **Backup Configuration**
   ```bash
   cp config/slick-forms.php config/slick-forms.php.backup
   ```

4. **Database Backup** (if modifying existing forms)
   ```bash
   php artisan db:backup  # Or your preferred method
   ```

### Review Documentation

Before migrating, read these guides:
- `SCHEMA_REFERENCE.md` - All available schema options
- `CUSTOM_FIELD_TYPES.md` - Examples of new field types
- `INTEGRATION_GUIDE.md` - How the new system works

---

## Migration Checklist

Use this checklist to track your migration progress:

### Phase 1: Preparation
- [ ] Backup all custom code and database
- [ ] Review `SCHEMA_REFERENCE.md`
- [ ] Identify all custom field types
- [ ] Document current functionality
- [ ] Set up test environment

### Phase 2: Schema Definition
- [ ] Add `getConfigSchema()` method to each field type
- [ ] Add `target` metadata to all schema fields
- [ ] Define default values
- [ ] Add help text for complex options

### Phase 3: Remove Old Code
- [ ] Remove manual Blade files from `field-options/`
- [ ] Remove old FormBuilder properties (if doing full integration)
- [ ] Update service provider registrations (if needed)

### Phase 4: Testing
- [ ] Test field creation in builder
- [ ] Test property editing and saving
- [ ] Test field rendering on forms
- [ ] Test with existing forms
- [ ] Test multi-instance scenarios

### Phase 5: Cleanup
- [ ] Remove backup files (after verification)
- [ ] Update internal documentation
- [ ] Commit changes to version control

---

## Step-by-Step Migration

### Step 1: Identify Custom Field Types

List all your custom field types:

```bash
# Find custom field type classes
find app/SlickForms/FieldTypes -name "*.php"

# Find custom Blade property files
find resources/views/vendor/slick-forms/livewire/partials/properties-panel/field-options -name "*.blade.php"
```

**Example Output:**
```
app/SlickForms/FieldTypes/RatingField.php
app/SlickForms/FieldTypes/LocationField.php
resources/views/vendor/slick-forms/.../field-options/rating.blade.php
resources/views/vendor/slick-forms/.../field-options/location.blade.php
```

### Step 2: Analyze Existing Properties

For each custom field type, document its properties:

**Example - Old RatingField Blade File:**
```blade
{{-- resources/views/vendor/slick-forms/.../field-options/rating.blade.php --}}

<div class="mb-3">
    <label class="form-label">Rating Style</label>
    <select class="form-select" wire:model="ratingStyle">
        <option value="stars">Stars</option>
        <option value="hearts">Hearts</option>
        <option value="thumbs">Thumbs Up/Down</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Maximum Rating</label>
    <input type="number" class="form-control" wire:model="maxRating" min="1" max="10" />
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" wire:model="allowHalfRatings" />
    <label class="form-check-label">Allow Half Ratings</label>
</div>
```

**Properties to Migrate:**
- `ratingStyle` - Select dropdown with 3 options
- `maxRating` - Number input (1-10)
- `allowHalfRatings` - Switch (boolean)

### Step 3: Create Schema Definition

Add `getConfigSchema()` method to your field type class:

```php
<?php

namespace App\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\FieldTypes\BaseFieldType;

class RatingField extends BaseFieldType
{
    // Existing methods...

    /**
     * NEW: Schema definition for properties panel
     */
    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'rating_style' => [
                'type' => 'select',
                'label' => 'Rating Style',
                'tab' => 'options',
                'target' => 'options',  // Save to options JSON
                'default' => 'stars',
                'options' => [
                    'stars' => 'Stars',
                    'hearts' => 'Hearts',
                    'thumbs' => 'Thumbs Up/Down',
                ],
            ],
            'max_rating' => [
                'type' => 'number',
                'label' => 'Maximum Rating',
                'tab' => 'options',
                'target' => 'options',
                'default' => 5,
                'min' => 1,
                'max' => 10,
            ],
            'allow_half_ratings' => [
                'type' => 'switch',
                'label' => 'Allow Half Ratings',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'help' => 'Allow users to select half-star ratings (e.g., 3.5 stars)',
            ],
        ]);
    }
}
```

**Key Changes:**
- **Property Names:** Convert camelCase to snake_case (`maxRating` → `max_rating`)
- **Add `target`:** Specify where to save (usually `'options'` for custom properties)
- **Add `tab`:** Specify which tab to show on (usually `'options'` for field-specific settings)
- **Add `default`:** Provide sensible defaults
- **Add `help`:** Document non-obvious options

### Step 4: Update Property Access in render()

Update how you access properties in your `render()` method:

**Old:**
```php
public function render($field, $value = null): string
{
    // Old way - accessing undefined properties
    $style = $field->ratingStyle ?? 'stars';  // ❌ Won't work
    $max = $field->maxRating ?? 5;
}
```

**New:**
```php
public function render($field, $value = null): string
{
    // New way - access via options JSON
    $options = $field->options ?? [];
    $style = $options['rating_style'] ?? 'stars';  // ✅ Correct
    $max = $options['max_rating'] ?? 5;
    $allowHalf = $options['allow_half_ratings'] ?? false;

    // Use these values in your rendering logic
    return $this->renderRatingField($field, $value, $style, $max, $allowHalf);
}
```

### Step 5: Remove Old Blade File

Once schema is working, delete the old manual Blade file:

```bash
rm resources/views/vendor/slick-forms/livewire/partials/properties-panel/field-options/rating.blade.php
```

### Step 6: Test the Migration

1. **Open Form Builder**
   ```
   /slick-forms/builder/{form-id}
   ```

2. **Add Your Custom Field**
   - Drag field onto canvas
   - Click to edit

3. **Verify Properties Panel**
   - All options appear under "Options" tab
   - All fields render correctly
   - Defaults are applied
   - Help text displays

4. **Save and Reload**
   - Click "Save Field Properties"
   - Refresh page
   - Edit field again
   - Verify values persisted

5. **Test Rendering**
   - Visit form preview: `/slick-forms/form/{form-id}`
   - Verify field renders with correct settings

---

## Common Migration Scenarios

### Scenario 1: Simple Field with Options

**Before:**
```blade
{{-- field-options/custom.blade.php --}}
<div class="mb-3">
    <label>Show Icon</label>
    <input type="checkbox" wire:model="showIcon" />
</div>
```

**After:**
```php
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'show_icon' => [
            'type' => 'switch',
            'label' => 'Show Icon',
            'tab' => 'options',
            'target' => 'options',
            'default' => false,
        ],
    ]);
}
```

---

### Scenario 2: Field with Multiple Related Options

**Before:**
```blade
<div class="mb-3">
    <label>API Endpoint</label>
    <input type="text" wire:model="apiEndpoint" />
</div>
<div class="mb-3">
    <label>API Key</label>
    <input type="password" wire:model="apiKey" />
</div>
<div class="mb-3">
    <label>Cache Results</label>
    <input type="checkbox" wire:model="cacheResults" />
</div>
```

**After (with divider for organization):**
```php
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'heading_api' => [
            'type' => 'heading',
            'label' => 'API Configuration',
            'tab' => 'options',
        ],
        'api_endpoint' => [
            'type' => 'text',
            'label' => 'API Endpoint',
            'tab' => 'options',
            'target' => 'options',
            'placeholder' => 'https://api.example.com/data',
        ],
        'api_key' => [
            'type' => 'text',
            'label' => 'API Key',
            'tab' => 'options',
            'target' => 'options',
            'help' => 'Your API authentication key',
        ],
        'divider_1' => [
            'type' => 'divider',
            'tab' => 'options',
        ],
        'cache_results' => [
            'type' => 'switch',
            'label' => 'Cache Results',
            'tab' => 'options',
            'target' => 'options',
            'default' => true,
            'help' => 'Cache API responses for faster loading',
        ],
    ]);
}
```

---

### Scenario 3: Field with Custom Tab

**Before:**
Manual tab registration in FormBuilder (complicated, not scalable)

**After:**
```php
public function getPropertyTabs(): array
{
    $tabs = parent::getPropertyTabs();

    // Add custom "Data Source" tab
    $tabs['data_source'] = [
        'label' => 'Data Source',
        'icon' => 'bi-database',
        'order' => 25,
        'view' => null,  // Auto-generate from schema
    ];

    return $tabs;
}

public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'source_type' => [
            'type' => 'select',
            'label' => 'Source Type',
            'tab' => 'data_source',  // Custom tab
            'target' => 'options',
            'options' => [
                'static' => 'Static List',
                'database' => 'Database Query',
                'api' => 'External API',
            ],
        ],
    ]);
}
```

---

### Scenario 4: Complex UI That Can't Be Schema-Driven

Some UIs are too complex for schema (e.g., drag-and-drop option reordering, visual color palette builders).

**Solution: Use Custom View**

```php
public function getPropertyTabs(): array
{
    $tabs = parent::getPropertyTabs();

    // Override options tab with custom view
    $tabs['options']['view'] = 'app.slick-forms.field-options.complex-field';

    return $tabs;
}
```

**Create Custom View:**
```blade
{{-- resources/views/app/slick-forms/field-options/complex-field.blade.php --}}

<div class="mb-3">
    {{-- Your complex custom UI here --}}
    {{-- Use wire:model="properties.option_name" for Livewire binding --}}
</div>
```

**Note:** Custom views should be a last resort. Try to use schema where possible.

---

### Scenario 5: Migrating Validation-Related Properties

**Before:**
```blade
<div class="mb-3">
    <label>Minimum Value</label>
    <input type="number" wire:model="minValue" />
</div>
```

**After:**
```php
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'min_value' => [
            'type' => 'number',
            'label' => 'Minimum Value',
            'tab' => 'validation',  // Validation tab
            'target' => 'options',
            'default' => 0,
            'help' => 'Minimum allowed value for this field',
        ],
    ]);
}

// Update validate() method to use options
public function validate($field): array
{
    $options = $field->options ?? [];
    $min = $options['min_value'] ?? 0;

    $rules = ['numeric'];
    if ($min > 0) {
        $rules[] = "min:{$min}";
    }

    return $rules;
}
```

---

## Troubleshooting

### Issue: Properties Not Saving

**Symptoms:**
- Edit field properties
- Click save
- Reload page
- Properties reset to defaults

**Causes:**
1. Missing `target` metadata in schema
2. Wrong `target` value
3. Property name mismatch

**Solutions:**

**Check 1: Verify `target` is set**
```php
'my_option' => [
    'type' => 'text',
    'label' => 'My Option',
    'tab' => 'options',
    'target' => 'options',  // ← Must be present
]
```

**Check 2: Verify target matches storage location**
```php
// If saving to database column
'target' => 'column'  // For: label, name, placeholder, etc.

// If saving to options JSON
'target' => 'options'  // For: custom field-specific settings

// If saving to settings JSON (layout elements)
'target' => 'settings'  // For: layout element settings
```

**Check 3: Verify property names match**
```php
// Schema definition
'my_option' => [...],

// Access in render()
$options['my_option']  // Must match exactly
```

---

### Issue: Properties Panel Shows Old UI

**Symptoms:**
- Schema defined correctly
- Still seeing old hardcoded Blade file

**Causes:**
1. Old Blade file still exists and takes precedence
2. FormBuilder not using new system
3. Caching issues

**Solutions:**

**Step 1: Remove old Blade file**
```bash
rm resources/views/vendor/slick-forms/livewire/partials/properties-panel/field-options/{field-type}.blade.php
```

**Step 2: Clear view cache**
```bash
php artisan view:clear
php artisan cache:clear
```

**Step 3: Hard refresh browser**
- Ctrl+Shift+R (Windows/Linux)
- Cmd+Shift+R (Mac)

---

### Issue: Default Values Not Applied

**Symptoms:**
- Defined `'default' => value` in schema
- Field still shows empty/null

**Cause:**
Default values only apply when creating NEW fields, not when editing existing fields.

**Solution:**
For existing fields, update database:

```php
// In a migration or tinker
CustomFormField::where('field_type', 'my_field')->get()->each(function ($field) {
    $options = $field->options ?? [];
    $options['my_option'] = $options['my_option'] ?? 'default_value';
    $field->options = $options;
    $field->save();
});
```

---

### Issue: Help Text Not Showing

**Symptoms:**
- Defined `'help' => 'Help text'` in schema
- Text not visible in properties panel

**Cause:**
SchemaRenderer might not support help text display (check version).

**Solution:**
Update to latest version or add custom view:

```php
'my_option' => [
    'type' => 'text',
    'label' => 'My Option',
    'tab' => 'options',
    'target' => 'options',
    'help' => 'This is help text',  // Should render as <small> below input
]
```

If help text still not showing, check `SchemaRenderer` implementation.

---

### Issue: Custom Tab Not Appearing

**Symptoms:**
- Added custom tab in `getPropertyTabs()`
- Tab doesn't show in properties panel

**Cause:**
FormBuilder might not be using `TabRegistry` properly.

**Solutions:**

**Check 1: Verify tab registration**
```php
public function getPropertyTabs(): array
{
    $tabs = parent::getPropertyTabs();

    $tabs['my_tab'] = [
        'label' => 'My Tab',
        'icon' => 'bi-star',
        'order' => 25,
        'view' => null,
    ];

    return $tabs;  // ← Must return $tabs
}
```

**Check 2: Verify schema fields reference tab**
```php
'my_option' => [
    'type' => 'text',
    'label' => 'My Option',
    'tab' => 'my_tab',  // ← Must match tab key
    'target' => 'options',
]
```

**Check 3: Check FormBuilder integration**
Verify FormBuilder is using `TabRegistry`:

```php
// In FormBuilder or properties-panel.blade.php
$tabs = $tabRegistry->getFieldTabs($fieldType);
```

---

## Testing After Migration

### Manual Testing Checklist

Run through this checklist for each migrated field type:

#### 1. Builder - Add Field
- [ ] Open form builder
- [ ] Find field type in palette
- [ ] Drag onto canvas
- [ ] Verify field appears
- [ ] Click to edit
- [ ] Properties panel opens

#### 2. Properties Panel - UI
- [ ] All tabs appear (basic, options, validation, style, advanced, custom tabs)
- [ ] Click each tab
- [ ] All schema fields render
- [ ] Default values populated
- [ ] Help text visible
- [ ] Required fields marked with asterisk

#### 3. Properties Panel - Interaction
- [ ] Change text field - value updates
- [ ] Change select dropdown - value updates
- [ ] Toggle switch - value updates
- [ ] Change number input - value updates
- [ ] Pick color - value updates

#### 4. Save & Persist
- [ ] Click "Save Field Properties"
- [ ] Success message appears
- [ ] Properties panel closes
- [ ] Refresh page
- [ ] Click field again
- [ ] All values persisted correctly

#### 5. Rendering
- [ ] Visit form preview: `/slick-forms/form/{form-id}`
- [ ] Field renders correctly
- [ ] Custom options applied (styling, behavior, etc.)
- [ ] JavaScript behavior works (if applicable)

#### 6. Submission
- [ ] Fill out form
- [ ] Submit form
- [ ] Check submission data
- [ ] Field value saved correctly

#### 7. Edge Cases
- [ ] Create multiple instances of same field type
- [ ] Delete and re-create field
- [ ] Duplicate field
- [ ] Change field type and back
- [ ] Test with empty/null values

---

### Automated Testing

Create unit tests for your migrated field types:

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\SlickForms\FieldTypes\RatingField;

class RatingFieldMigrationTest extends TestCase
{
    protected RatingField $ratingField;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ratingField = new RatingField;
    }

    public function test_schema_includes_all_options()
    {
        $schema = $this->ratingField->getConfigSchema();

        // Verify all custom options present
        $this->assertArrayHasKey('rating_style', $schema);
        $this->assertArrayHasKey('max_rating', $schema);
        $this->assertArrayHasKey('allow_half_ratings', $schema);
    }

    public function test_schema_has_correct_types()
    {
        $schema = $this->ratingField->getConfigSchema();

        $this->assertEquals('select', $schema['rating_style']['type']);
        $this->assertEquals('number', $schema['max_rating']['type']);
        $this->assertEquals('switch', $schema['allow_half_ratings']['type']);
    }

    public function test_schema_has_target_metadata()
    {
        $schema = $this->ratingField->getConfigSchema();

        $this->assertEquals('options', $schema['rating_style']['target']);
        $this->assertEquals('options', $schema['max_rating']['target']);
        $this->assertEquals('options', $schema['allow_half_ratings']['target']);
    }

    public function test_schema_has_defaults()
    {
        $schema = $this->ratingField->getConfigSchema();

        $this->assertEquals('stars', $schema['rating_style']['default']);
        $this->assertEquals(5, $schema['max_rating']['default']);
        $this->assertFalse($schema['allow_half_ratings']['default']);
    }

    public function test_render_uses_options_correctly()
    {
        $field = \DigitalisStudios\SlickForms\Models\CustomFormField::factory()->create([
            'field_type' => 'rating',
            'options' => [
                'rating_style' => 'hearts',
                'max_rating' => 10,
                'allow_half_ratings' => true,
            ],
        ]);

        $html = $this->ratingField->render($field, null);

        // Assert HTML contains expected elements based on options
        $this->assertStringContainsString('hearts', $html);
        // Add more assertions based on your render logic
    }
}
```

---

## Rollback Plan

If migration causes issues, you can roll back:

### Step 1: Restore Backup Files

```bash
# Restore custom field type classes
rm -rf app/SlickForms/FieldTypes
cp -r app/SlickForms/FieldTypes.backup app/SlickForms/FieldTypes

# Restore custom Blade files
rm -rf resources/views/vendor/slick-forms
cp -r resources/views/vendor/slick-forms.backup resources/views/vendor/slick-forms

# Restore configuration
cp config/slick-forms.php.backup config/slick-forms.php
```

### Step 2: Clear Caches

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Step 3: Restore Database (if needed)

```bash
# Restore from backup
php artisan db:restore  # Or your backup method
```

### Step 4: Verify Rollback

- Open form builder
- Edit existing fields
- Verify old properties panel appears
- Test field rendering

---

## Summary

Migration to the schema-driven system involves:

1. ✅ **Analyze** existing custom field types
2. ✅ **Define** schema in `getConfigSchema()`
3. ✅ **Add** `target` metadata to all properties
4. ✅ **Update** property access in `render()` and other methods
5. ✅ **Remove** old manual Blade files
6. ✅ **Test** thoroughly in builder and forms
7. ✅ **Document** changes for your team

**Benefits:**
- 80% faster to add new properties
- No manual UI coding
- Consistent experience
- Easier to maintain

**Resources:**
- `SCHEMA_REFERENCE.md` - Complete schema options
- `CUSTOM_FIELD_TYPES.md` - Field type examples
- `CUSTOM_LAYOUT_ELEMENTS.md` - Layout element examples
- `INTEGRATION_GUIDE.md` - System integration steps

---

**Questions or Issues?**

- Check built-in field types in `src/FieldTypes/` for examples
- Review `SchemaRenderer` source in `src/Services/SchemaRenderer.php`
- Consult `ManagesSchemaProperties` trait in `src/Livewire/Concerns/ManagesSchemaProperties.php`

Happy migrating! 🚀

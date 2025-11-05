# Repeater Fields - User Guide

## Overview

Repeater Fields allow form users to dynamically add or remove multiple instances of a field group. This is perfect for collecting variable amounts of related information like:

- Multiple addresses (home, work, mailing)
- Family members information
- Work experience entries
- Product variants
- Emergency contacts
- Educational history

## Quick Start

### 1. Create a Repeater Field

1. Drag **"Repeater Group"** from the field palette to your form
2. The repeater appears as a bordered container with a collection icon
3. Configure the repeater settings in the properties panel

### 2. Add Child Fields

1. Drag any field type from the palette **into** the repeater container
2. The field becomes a child of the repeater
3. Add as many child fields as needed
4. Child fields will repeat for each instance users create

### 3. Configure Settings

In the properties panel:
- **Min/Max Instances**: Control how many instances users can add (1-100)
- **Initial Instances**: How many instances to show when form loads
- **Button Text**: Customize "Add Another" and "Remove" button labels
- **Layout Style**: Choose Card, Accordion, or Plain visual style
- **Show Instance Number**: Display "Contact #1", "Contact #2", etc.
- **Allow Reorder**: Enable drag-to-reorder instances

### 4. Preview and Test

- Use the form renderer to test the repeater
- Click "Add Another" to create new instances
- Click "Remove" to delete instances
- Drag instances to reorder (if enabled)

## Configuration Options

### Min Instances
**Default**: 1
**Range**: 0-100

Minimum number of instances required. Users cannot remove instances below this number.

**Use Cases**:
- `0`: Optional repeater (e.g., "Add references if available")
- `1`: At least one instance required (e.g., "Primary contact")
- `2+`: Multiple instances required (e.g., "At least 2 emergency contacts")

### Max Instances
**Default**: 10
**Range**: 1-100

Maximum number of instances allowed. "Add Another" button disables when reached.

**Use Cases**:
- `3-5`: Limited selections (e.g., "Up to 3 favorite colors")
- `10`: Reasonable limit for most use cases
- `50-100`: Large lists (e.g., "Product inventory")

### Initial Instances
**Default**: 1
**Range**: 0-100

Number of instances displayed when form first loads.

**Use Cases**:
- `0`: Start empty, user adds as needed
- `1`: Show one blank instance to guide users
- `2+`: Pre-populate multiple instances

**Note**: Must be between `min_instances` and `max_instances`.

### Add Button Text
**Default**: "Add Another"

Customize the button label for adding instances.

**Examples**:
- "Add Another Contact"
- "Add Family Member"
- "+ Add Address"
- "Add More"

### Remove Button Text
**Default**: "Remove"

Customize the button label for removing instances.

**Examples**:
- "Delete"
- "Remove Contact"
- "✕ Remove"

### Layout Style

#### Card (Default)
- Each instance in a Bootstrap card
- Header shows instance number and remove button
- Clean, professional appearance
- Best for: Forms with many fields per instance

#### Accordion
- Collapsible accordion items
- Click to expand/collapse each instance
- Saves vertical space
- Best for: Forms with long field groups

#### Plain
- Minimal styling with border separator
- Compact layout
- Simple appearance
- Best for: Simple repeaters with few fields

### Show Instance Number
**Default**: Checked

Display instance numbers in headers:
- Enabled: "Contact #1", "Contact #2", "Contact #3"
- Disabled: "Contact", "Contact", "Contact"

**Use Case**: Disable when instances don't need numbering (e.g., "Add Item" vs "Item #1").

### Allow Reorder
**Default**: Checked

Enable drag-and-drop reordering of instances.

- Drag handle (☰) appears on left side of each instance
- Drag to reorder
- Useful when order matters (e.g., priority ranking)

**Disable** when order doesn't matter or you want to prevent accidental reordering.

## Real-World Examples

### Example 1: Emergency Contacts

**Repeater Configuration**:
- Label: "Emergency Contacts"
- Min: 1, Max: 5, Initial: 2
- Layout: Card
- Show Instance Number: Yes

**Child Fields**:
1. Name (Text - Required)
2. Relationship (Select - Required)
   Options: Spouse, Parent, Sibling, Friend, Other
3. Phone (Phone - Required)
4. Email (Email - Optional)

**Result**: Users provide 1-5 emergency contacts with full details.

---

### Example 2: Work Experience

**Repeater Configuration**:
- Label: "Work Experience"
- Min: 0, Max: 10, Initial: 1
- Layout: Accordion
- Show Instance Number: No
- Allow Reorder: Yes (most recent first)

**Child Fields**:
1. Company Name (Text - Required)
2. Job Title (Text - Required)
3. Start Date (Date - Required)
4. End Date (Date - Optional)
5. Description (Textarea - Optional)

**Result**: Collapsible work history entries, drag to reorder by recency.

---

### Example 3: Product Variants

**Repeater Configuration**:
- Label: "Product Variants"
- Min: 1, Max: 20, Initial: 1
- Layout: Plain
- Add Button: "+ Add Variant"
- Remove Button: "✕ Delete"

**Child Fields**:
1. Size (Select - Required): S, M, L, XL
2. Color (Select - Required): Red, Blue, Green, Black
3. SKU (Text - Required)
4. Price (Number - Required)
5. Stock (Number - Required)

**Result**: Compact list of product variants with pricing.

---

### Example 4: Family Members

**Repeater Configuration**:
- Label: "Household Members"
- Min: 1, Max: 10, Initial: 1
- Layout: Card
- Show Instance Number: Yes

**Child Fields**:
1. Full Name (Text - Required)
2. Date of Birth (Date - Required)
3. Relationship (Radio - Required)
   Options: Self, Spouse, Child, Parent, Other
4. SSN (Text - Optional, conditional on relationship)

**Result**: Family roster with conditional fields per member.

---

### Example 5: Educational History

**Repeater Configuration**:
- Label: "Education"
- Min: 1, Max: 5, Initial: 1
- Layout: Accordion
- Allow Reorder: Yes

**Child Fields**:
1. Institution (Text - Required)
2. Degree (Select - Required): High School, Associate, Bachelor, Master, PhD
3. Field of Study (Text - Required)
4. Graduation Year (Number - Required)
5. GPA (Number - Optional)

**Result**: Education timeline in accordion format.

## Behavior

### Adding Instances

1. User clicks "Add Another" button
2. New instance appears with empty fields
3. Instance count increments
4. Button disables when max instances reached

### Removing Instances

1. User clicks "Remove" button on specific instance
2. Confirmation (optional, via browser warning)
3. Instance removed, remaining instances re-indexed
4. Button disables when min instances reached

### Reordering Instances

1. User clicks and holds drag handle (☰)
2. Drags instance to new position
3. Other instances shift automatically
4. Order saved in submission

### Form Submission

**Data Structure**:
```json
{
  "repeater_field_id": [
    {
      "child_field_1_id": "value",
      "child_field_2_id": "value"
    },
    {
      "child_field_1_id": "value",
      "child_field_2_id": "value"
    }
  ]
}
```

**Validation**:
- Repeater validates instance count (min/max)
- Each child field validates per instance
- Empty instances not allowed (all required fields must be filled)

## Validation

### Repeater-Level Validation

- **Min Instances**: "At least X instances required"
- **Max Instances**: "Maximum X instances allowed"
- **Required**: Enforces min instances ≥ 1

### Child Field Validation

- Each instance validates independently
- Required child fields must be filled in ALL instances
- Validation errors show per instance
- Cannot submit with incomplete instances

**Example Error**:
> "Emergency Contacts: Contact #2 - Phone is required"

## Conditional Logic

### FOR Repeater Field
- Show/hide entire repeater based on other field values
- All instances shown/hidden together

**Example**: Show "Dependents" repeater only if "Has Dependents?" = Yes

### WITHIN Repeater (Per Instance)
- Child fields can have conditional logic based on OTHER child fields in SAME instance
- Each instance evaluates independently

**Example**: Show "Other Relationship" text field when "Relationship" = "Other" (per contact)

### Limitations
- Cross-instance logic not supported (Instance 1 cannot affect Instance 2)
- Repeater field cannot be used in calculations (yet)

## Tips & Best Practices

### 1. Keep It Simple
- Limit child fields to 5-10 per repeater
- More fields = more complex for users
- Consider splitting into multiple repeaters if needed

### 2. Set Reasonable Limits
- Max 10-20 instances for most use cases
- Higher limits can impact performance
- Users rarely add 50+ instances

### 3. Use Clear Labels
- Repeater label should describe the collection: "Work Experience", "Contacts", "Addresses"
- Child field labels should be instance-specific: "Company Name" not "Name"

### 4. Choose Appropriate Layout
- **Card**: 3+ fields, professional forms
- **Accordion**: 5+ fields, long forms, save space
- **Plain**: 1-3 fields, simple lists

### 5. Set Helpful Defaults
- Initial instances = 1 for most cases
- Set min=1 if at least one instance required
- Enable reordering for chronological data (work history, education)

### 6. Test Thoroughly
- Test add/remove with min/max limits
- Test validation on all instances
- Test with conditional logic
- Test reordering functionality

### 7. Mobile Considerations
- Repeaters work on mobile but take more space
- Accordion layout recommended for mobile
- Keep child fields mobile-friendly (avoid wide inputs)

## Troubleshooting

### Problem: Can't Remove Instance

**Cause**: At minimum instance limit

**Solution**: Increase min instances in repeater settings, or this is expected behavior

---

### Problem: Can't Add Instance

**Cause**: At maximum instance limit

**Solution**: Increase max instances, or user has reached intended limit

---

### Problem: Drag Handle Not Working

**Cause**: "Allow Reorder" disabled

**Solution**: Enable "Allow Reorder" in repeater settings

---

### Problem: Child Field Not Showing

**Cause**: Field not properly nested in repeater

**Solution**:
1. Delete the field
2. Drag new field directly INTO repeater container
3. Verify field appears in repeater's child fields list

---

### Problem: Validation Not Working on Child Fields

**Cause**: Validation rules not set on child fields

**Solution**: Select each child field and configure validation in properties panel

---

### Problem: Data Not Saving

**Cause**: JSON structure issue

**Solution**: Check browser console for errors, ensure all field names are unique

## Known Limitations

### Current Limitations

1. **No Nested Repeaters**: Cannot put a repeater inside another repeater
2. **No Layout Elements Inside**: Only fields allowed, no containers/rows/columns
3. **No Calculations Across Instances**: Cannot SUM values from all instances (yet)
4. **No Cross-Instance Conditional Logic**: Instance 1 cannot affect Instance 2

### Future Enhancements

Planned for future versions:
1. Nested repeaters (repeater within repeater)
2. Layout element support inside repeaters
3. Aggregate functions: `SUM_REPEATER()`, `AVG_REPEATER()`, `COUNT_REPEATER()`
4. Copy instance button (duplicate existing instance)
5. Import/Export CSV for bulk instance creation
6. Instance templates (save repeater group as reusable template)

## Technical Details

### Data Storage

Repeater data stored as JSON array in `slick_form_field_values`:

```json
[
  {
    "field_124": "John",
    "field_125": "Doe",
    "field_126": "john@example.com"
  },
  {
    "field_124": "Jane",
    "field_125": "Smith",
    "field_126": "jane@example.com"
  }
]
```

### Database Schema

- `parent_field_id` on `slick_form_fields` creates parent-child relationship
- Child fields have `parent_field_id = repeater_id`
- No schema changes needed (uses existing column)

### Performance

- Repeaters with 10 instances × 5 fields = 50 input fields
- Livewire handles reactivity efficiently
- Performance impact minimal for < 20 instances
- Consider pagination for 50+ instances

## Version Information

**Added in**: Phase 5
**Field Type**: `repeater`
**Icon**: `bi bi-collection` (Bootstrap Icons)
**Category**: Advanced Fields

---

**Generated with Claude Code** 🤖

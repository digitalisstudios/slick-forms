# Input Masks - User Guide

## Overview

Input Masks automatically format user input as they type, providing visual guidance and ensuring consistent data format. This improves data quality and user experience by showing the expected format immediately.

## Supported Field Types

Input masks work with:
- **Text Field** - All mask types
- **Number Field** - Number and currency masks
- **Phone Field** - Phone masks (enhances existing phone field)

## Quick Start

1. **Add a field** (Text, Number, or Phone) to your form
2. **Select the field** to open properties panel
3. **Enable Input Mask** checkbox
4. **Choose mask type** from dropdown
5. **Preview** shows the expected format
6. **Save** and test in form renderer

## Available Mask Types

### Phone Masks

**Phone (US)**: `(555) 123-4567`
- Standard US phone format with area code
- Automatically adds parentheses and dashes
- 10 digits required

**Phone (International)**: `+1 555 123 4567`
- International format with country code
- Space-separated for readability
- Includes +1 prefix

### Credit Card

**Credit Card**: `4111 1111 1111 1111`
- Auto-detects card type (Visa, MC, Amex, etc.)
- Groups digits for readability (4-4-4-4 or 4-6-5 for Amex)
- Validates card number format

### Date Masks

**Date (MM/DD/YYYY)**: `01/31/2024`
- US date format
- Month/Day/Year order
- Slash separators

**Date (DD/MM/YYYY)**: `31/01/2024`
- European date format
- Day/Month/Year order
- Slash separators

**Date (YYYY-MM-DD)**: `2024-01-31`
- ISO date format
- Year-Month-Day order
- Dash separators

### Time Masks

**Time (12-hour)**: `12:00`
- 12-hour format
- HH:MM pattern
- Use with AM/PM field

**Time (24-hour)**: `23:00`
- 24-hour format
- HH:MM pattern
- Military time

### Government IDs

**SSN**: `123-45-6789`
- Social Security Number format
- Dash separators
- 9 digits total

**ZIP Code**: `12345`
- Standard 5-digit ZIP
- Numbers only

**ZIP+4**: `12345-6789`
- Extended ZIP code format
- Dash separator
- 9 digits total

### Number Masks

**Number (Decimal)**: `1,234.56`
- Thousand separators
- 2 decimal places
- Clean number formatting

**Number (Integer)**: `1,234`
- Thousand separators
- No decimal places
- Whole numbers only

**Currency (USD)**: `$1,234.56`
- Dollar sign prefix
- Thousand separators
- 2 decimal places

**Percentage**: `98.5%`
- Percent sign suffix
- 1 decimal place
- Positive numbers only

### Custom Pattern

**Custom**: Define your own pattern

**Pattern Syntax**:
- `#` = Numeric digit (0-9)
- `A` = Alphabetic character (a-z, A-Z)
- `*` = Alphanumeric (letters or numbers)
- `-`, `/`, `:`, ` ` = Delimiters (appear in formatted output)

**Examples**:
- `###-##-####` → SSN: 123-45-6789
- `AAA-####` → License Plate: ABC-1234
- `##/##/####` → Date: 01/31/2024
- `***-***-****` → Mixed: AB1-CD2-EF34

## Configuration

### Enable Input Mask

1. Select field in builder
2. Check "Enable Input Mask"
3. Choose mask type from dropdown
4. Preview shows example format

### Custom Pattern

1. Select "Custom Pattern" from mask type
2. Enter pattern using syntax: `#`, `A`, `*`
3. Use delimiters: `-`, `/`, `:`, space
4. Test in form renderer

## Behavior

### As User Types

1. User starts typing
2. Mask auto-formats input
3. Delimiters added automatically
4. Cursor position maintained

**Example (Phone)**:
```
User types: 5551234567
Display shows: (555) 123-4567
```

### Copy/Paste

Masks handle pasted content:
- Full paste: Formats entire value
- Partial paste: Formats from cursor position
- Invalid characters: Ignored or stripped

### Backspace/Delete

- Skips over delimiters intelligently
- Maintains correct cursor position
- Deletes actual characters, not delimiters

### Mobile Keyboards

- Phone masks → Numeric keyboard
- Credit card masks → Numeric keyboard
- Number masks → Numeric keyboard with decimal
- Custom patterns → Appropriate keyboard

## Validation Integration

**Important**: Input masks format display but **don't replace validation**.

Always add validation rules for masked fields:

**Example: SSN Field**
- **Mask**: SSN (`123-45-6789`)
- **Validation**: Required + Regex `/^\d{3}-\d{2}-\d{4}$/`

**Example: Phone Field**
- **Mask**: Phone (US) (`(555) 123-4567`)
- **Validation**: Required + Regex `/^\(\d{3}\) \d{3}-\d{4}$/`

**Example: ZIP Code**
- **Mask**: ZIP+4 (`12345-6789`)
- **Validation**: Required + Regex `/^\d{5}-\d{4}$/`

## Best Practices

### 1. Choose Appropriate Mask
- Use preset masks when available (tested and proven)
- Custom patterns for unique formats only

### 2. Set Helpful Placeholders
- Masks auto-generate placeholders
- Override if needed for clarity
- Example: "Enter 10-digit phone number"

### 3. Combine with Validation
- Mask formats input
- Validation ensures correctness
- Both work together for best UX

### 4. Test Thoroughly
- Test typing from scratch
- Test copy/paste
- Test backspace/delete
- Test on mobile devices

### 5. Consider Existing Data
- Masks format unformatted data on focus
- Existing formatted data displays correctly
- Migration may be needed for old data

## Common Use Cases

### Registration Forms

**Phone Number**:
- Field: Phone
- Mask: Phone (US)
- Validation: Required
- Help Text: "10-digit phone number"

**SSN** (if required):
- Field: Text
- Mask: SSN
- Validation: Required + Regex
- Help Text: "Social Security Number"

### E-Commerce

**Credit Card**:
- Field: Text
- Mask: Credit Card
- Validation: Required + Credit Card validation
- Help Text: "Enter card number"

**Zip Code**:
- Field: Text
- Mask: ZIP or ZIP+4
- Validation: Required + Regex
- Help Text: "Billing ZIP code"

### Financial Forms

**Currency Amount**:
- Field: Number
- Mask: Currency (USD)
- Validation: Required + Min + Max
- Help Text: "Enter amount in dollars"

**Percentage**:
- Field: Number
- Mask: Percentage
- Validation: Required + Between 0-100
- Help Text: "Enter percentage (0-100)"

### Scheduling

**Date**:
- Field: Text (or use Date field with Flatpickr)
- Mask: Date (MM/DD/YYYY)
- Validation: Required + Date format
- Help Text: "MM/DD/YYYY"

**Time**:
- Field: Text (or use Time field)
- Mask: Time (12-hour) or Time (24-hour)
- Validation: Required + Time format
- Help Text: "HH:MM"

## Troubleshooting

### Problem: Mask Not Working

**Causes**:
- Cleave.js not loaded
- Mask not enabled in field options
- Incompatible field type

**Solutions**:
- Check browser console for errors
- Verify "Enable Input Mask" is checked
- Use Text, Number, or Phone field types only

---

### Problem: Validation Failing

**Cause**: Validation regex doesn't match formatted value

**Solution**: Update validation to match mask format
```
Mask: Phone (US) → (555) 123-4567
Regex: /^\(\d{3}\) \d{3}-\d{4}$/
```

---

### Problem: Livewire Not Syncing

**Cause**: Mask uses `wire:model.lazy` instead of `wire:model.live`

**Solution**: This is intentional for Cleave.js compatibility
- Value syncs on blur (when user leaves field)
- Calculations/conditional logic update after blur

---

### Problem: Custom Pattern Not Working

**Cause**: Invalid pattern syntax

**Solutions**:
- Use only: `#` (number), `A` (letter), `*` (alphanumeric)
- Use valid delimiters: `-`, `/`, `:`, space
- Test pattern in form renderer

**Valid**: `###-##-####`
**Invalid**: `@@@-@@-@@@@` (@ not supported)

---

### Problem: Mobile Keyboard Wrong

**Cause**: Field type or mask type mismatch

**Solution**:
- Phone/number masks auto-trigger numeric keyboard
- Ensure field type matches mask type
- Test on actual mobile device

## Technical Details

### Library

Uses **Cleave.js v1.6.0** (MIT license)
- Lightweight (~15KB)
- No dependencies
- Cross-browser compatible
- Mobile-friendly

### Livewire Compatibility

**Wire Model Binding**:
- Without mask: `wire:model.live` (real-time sync)
- With mask: `wire:model.lazy` (sync on blur)

**Why**: Cleave.js modifies input value, which conflicts with live updates. Lazy binding ensures formatted value syncs after user finishes typing.

### Data Storage

Masked values stored **as formatted**:
- Phone: `(555) 123-4567` (not `5551234567`)
- SSN: `123-45-6789` (not `123456789`)

**Benefit**: Consistent display in submissions and reports
**Consideration**: Validate against formatted pattern

### Performance

- Minimal performance impact
- Initializes on DOMContentLoaded
- One Cleave instance per masked field
- Efficient even with many masked fields

## Limitations

1. **Field Types**: Only Text, Number, Phone fields supported
2. **Calculation Fields**: Masks not compatible with calculation fields
3. **Repeater Fields**: Masks work but may need testing in repeater instances
4. **Server-Side**: Masks are client-side only (no server-side formatting)

## Future Enhancements

Potential additions for future versions:
- International phone formats (more countries)
- Credit card type-specific validation
- Custom delimiter characters
- Mask preview in builder
- Auto-apply masks based on field name (e.g., "ssn" → SSN mask)

## Version Information

**Added in**: Phase 6
**Library**: Cleave.js v1.6.0
**Compatible Fields**: Text, Number, Phone
**Browser Support**: All modern browsers + IE11

---

**Generated with Claude Code** 🤖

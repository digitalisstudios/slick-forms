# Calculation Fields - User Guide

## Overview

Calculation Fields allow you to create fields that automatically calculate values based on other fields in your form. Perfect for e-commerce, surveys, finance forms, and any scenario where you need dynamic calculations.

## Quick Start

1. **Add a Calculation Field:**
   - Drag "Calculation" from the field palette to your form
   - The field appears with a calculator icon

2. **Configure the Formula:**
   - Select the calculation field
   - In the properties panel, enter your formula in the "Formula" textarea
   - Reference other fields using `{field_name}`

3. **Example:**
   ```
   Formula: {price} * {quantity}
   ```
   When a user enters price = 10 and quantity = 5, the calculation field automatically shows 50.

## Formula Syntax

### Basic Operations

| Operator | Description | Example |
|----------|-------------|---------|
| `+` | Addition | `{subtotal} + {shipping}` |
| `-` | Subtraction | `{total} - {discount}` |
| `*` | Multiplication | `{price} * {quantity}` |
| `/` | Division | `{total} / {people}` |
| `%` | Modulo (remainder) | `{value} % 10` |

### Parentheses for Precedence

```
({price} + {tax}) * {quantity}
{subtotal} - ({subtotal} * {discount_percent} / 100)
```

### Built-in Functions

#### SUM - Add Multiple Fields
```
SUM({field1}, {field2}, {field3})
```
**Example:** `SUM({jan_sales}, {feb_sales}, {mar_sales})`

#### AVG - Average of Fields
```
AVG({field1}, {field2}, {field3})
```
**Example:** `AVG({score1}, {score2}, {score3})`

#### MIN - Minimum Value
```
MIN({field1}, {field2})
```
**Example:** `MIN({budget}, {actual_cost})`

#### MAX - Maximum Value
```
MAX({field1}, {field2})
```
**Example:** `MAX({january}, {february}, {march})`

#### ROUND - Round to Decimal Places
```
ROUND({field}, decimal_places)
```
**Example:** `ROUND({price} * {quantity}, 2)`

#### ABS - Absolute Value
```
ABS({field})
```
**Example:** `ABS({profit_loss})`

### Constants

You can use numeric constants in formulas:
```
{price} * 1.08          (add 8% tax)
{amount} - 10           (subtract $10 fee)
{hours} * 25.50         (hourly rate calculation)
```

## Real-World Examples

### Example 1: E-Commerce Order Total
```
Fields:
- price (Number Field)
- quantity (Number Field)
- tax_rate (Number Field, default: 8)
- total (Calculation Field)

Formula: ({price} * {quantity}) * (1 + {tax_rate} / 100)

Display Format: Currency
Prefix: $
Decimal Places: 2
```

### Example 2: Discount Calculator
```
Fields:
- original_price (Number Field)
- discount_percent (Number Field)
- final_price (Calculation Field)

Formula: {original_price} - ({original_price} * {discount_percent} / 100)

Display Format: Currency
Prefix: $
Decimal Places: 2
```

### Example 3: Survey Average Score
```
Fields:
- rating1 (Star Rating Field, 1-5)
- rating2 (Star Rating Field, 1-5)
- rating3 (Star Rating Field, 1-5)
- average_rating (Calculation Field)

Formula: AVG({rating1}, {rating2}, {rating3})

Display Format: Number
Decimal Places: 1
Suffix:  stars
```

### Example 4: Monthly Payment Calculator
```
Fields:
- loan_amount (Number Field)
- interest_rate (Number Field)
- months (Number Field)
- monthly_payment (Calculation Field)

Formula: ({loan_amount} * {interest_rate} / 100 / 12 + {loan_amount}) / {months}

Display Format: Currency
Prefix: $
Decimal Places: 2
```

### Example 5: BMI Calculator
```
Fields:
- weight_kg (Number Field)
- height_cm (Number Field)
- bmi (Calculation Field)

Formula: {weight_kg} / (({height_cm} / 100) * ({height_cm} / 100))

Display Format: Number
Decimal Places: 1
```

### Example 6: Quarterly Total
```
Fields:
- q1_sales (Number Field)
- q2_sales (Number Field)
- q3_sales (Number Field)
- q4_sales (Number Field)
- yearly_total (Calculation Field)

Formula: SUM({q1_sales}, {q2_sales}, {q3_sales}, {q4_sales})

Display Format: Currency
Prefix: $
Decimal Places: 0
```

## Configuration Options

### Formula (Required)
The calculation formula using field references and operators.

**Tips:**
- Use the exact field `name` (not label)
- Field names are case-sensitive
- Wrap field names in curly braces: `{field_name}`

### Display Format
Choose how the calculated value is displayed:

- **Number**: Standard numeric display (e.g., 1,234.56)
- **Currency**: Number with thousand separators (e.g., 1,234.56)
- **Percentage**: Number formatted as percentage (e.g., 98.5)

### Decimal Places
Number of decimal places to show (0-10).

**Examples:**
- `0` = 1,234
- `2` = 1,234.56 (default)
- `4` = 1,234.5678

### Prefix
Text to display before the value.

**Common Uses:**
- `$` for currency
- `€` for euros
- `£` for pounds
- Empty for no prefix

### Suffix
Text to display after the value.

**Common Uses:**
- `%` for percentage
- `km` for distance
- `hrs` for hours
- Empty for no suffix

### Display Mode
Control visibility of the calculation field:

- **Visible**: Show the calculated value to users (default)
- **Hidden**: Store the value but don't display it (useful for backend calculations)

## Field References

### How to Reference Fields

Use the field's **name** property (not the label):
```
Field Name: price
Field Label: "Product Price"

Formula: {price} * {quantity}  ✓ Correct
Formula: {Product Price}       ✗ Wrong
```

### Finding Field Names

1. Select any field in the form builder
2. Look at the "Field Name" input in the properties panel
3. Use this exact name in your formula

### Best Practices

1. **Use descriptive field names:**
   - Good: `{subtotal}`, `{tax_rate}`, `{shipping_cost}`
   - Bad: `{field1}`, `{x}`, `{temp}`

2. **Make names unique:**
   - Each field must have a unique name
   - Avoid similar names like `price` and `price2`

3. **Use underscores for multi-word names:**
   - Good: `{discount_percent}`, `{final_total}`
   - Bad: `{discount percent}`, `{finaltotal}`

## Behavior

### Real-Time Updates
Calculation fields update automatically as users fill out the form:

1. User enters a value in a referenced field
2. Calculation field recalculates instantly
3. New value displays immediately

### Initial State
- Calculation fields start empty
- They populate once referenced fields have values
- If a referenced field is empty, it's treated as 0

### Validation
- Calculation fields are read-only (users can't edit them)
- They can be marked as required (calculation must have a value)
- Validation happens on the calculated result

### With Conditional Logic
Calculation fields work with conditional logic:
- Can be shown/hidden based on other field values
- Can be used in conditions for other fields
- Formulas only use visible field values

## Troubleshooting

### Problem: Calculation shows nothing
**Causes:**
- Referenced fields are empty
- Formula has syntax errors
- Field names don't match

**Solutions:**
1. Check that referenced fields have values
2. Verify field names in formula match exactly
3. Test formula with simple values first

### Problem: Calculation shows 0
**Causes:**
- Division by zero
- Referenced fields are 0
- Formula references non-existent fields

**Solutions:**
1. Check for division by zero
2. Ensure referenced fields have non-zero values
3. Verify all field names exist

### Problem: Formula not working
**Causes:**
- Typo in function name (e.g., `SUM` not `sum`)
- Missing curly braces around field names
- Invalid operator or character

**Solutions:**
1. Use uppercase for functions: `SUM`, `AVG`, `MIN`, `MAX`, `ROUND`, `ABS`
2. Ensure all field references use `{curly_braces}`
3. Only use allowed operators: `+`, `-`, `*`, `/`, `%`, `()

### Problem: Wrong result
**Causes:**
- Operator precedence (multiplication before addition)
- Incorrect decimal places
- Referenced wrong field

**Solutions:**
1. Use parentheses to control order: `({a} + {b}) * {c}`
2. Check decimal places setting
3. Verify all field references are correct

## Security & Limitations

### What's Allowed
- Basic math operators: `+`, `-`, `*`, `/`, `%`
- Built-in functions: `SUM`, `AVG`, `MIN`, `MAX`, `ROUND`, `ABS`
- Parentheses for grouping
- Numeric constants
- Field references

### What's NOT Allowed
- Custom code execution
- Database queries
- External API calls
- File system access
- User-defined functions

### Formula Evaluation
- Formulas are validated before execution
- Only safe mathematical operations are allowed
- Results are sanitized before display
- Maximum calculation time: 1 second

## Advanced Tips

### Tip 1: Chain Calculations
```
Fields:
1. subtotal (Calculation): {price} * {quantity}
2. tax (Calculation): {subtotal} * 0.08
3. total (Calculation): {subtotal} + {tax}
```

### Tip 2: Conditional Calculations
```
Use conditional logic to show/hide calculation fields based on user choices.

Example: Show shipping calculation only if "Ship Items" checkbox is checked.
```

### Tip 3: Hidden Calculations
```
Use hidden calculation fields for intermediate values:
- Display Mode: Hidden
- Use in other calculation formulas
- Store for backend processing
```

### Tip 4: Format for Context
```
For money: 
- Display Format: Currency
- Prefix: $
- Decimal Places: 2

For percentages:
- Display Format: Percentage
- Suffix: %
- Decimal Places: 1

For counts:
- Display Format: Number
- Decimal Places: 0
```

## Testing Your Formulas

1. **Start Simple:**
   ```
   {field1} + {field2}
   ```

2. **Add Complexity Gradually:**
   ```
   ({field1} + {field2}) * {field3}
   ```

3. **Test with Real Values:**
   - Use the form preview/renderer
   - Enter test values
   - Verify calculations are correct

4. **Test Edge Cases:**
   - Empty fields (treated as 0)
   - Zero values
   - Very large numbers
   - Negative numbers

## Version Information

**Added in:** Phase 4
**Field Type:** calculation
**Icon:** bi bi-calculator (Bootstrap Icons)
**Category:** Advanced Fields

## Support

For issues, questions, or feature requests related to calculation fields:
1. Check this documentation
2. Review formula syntax examples
3. Test with simple formulas first
4. Consult the troubleshooting section

---

**Generated with Claude Code** 🤖

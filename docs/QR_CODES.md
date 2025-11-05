# QR Code Generation

**Slick Forms v2.0+** - Complete guide for generating QR codes for form URLs

---

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [QR Code URL Formats](#qr-code-url-formats)
- [Generating QR Codes](#generating-qr-codes)
- [Customization Options](#customization-options)
- [Embedding QR Codes](#embedding-qr-codes)
- [Use Cases](#use-cases)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## Overview

QR codes provide a convenient way to share forms that users can scan with their mobile devices. Slick Forms generates QR codes for form URLs using the SimpleQrCode package, supporting various formats and customization options.

### Benefits

- ✅ **Mobile-Friendly**: Users scan with camera apps
- ✅ **No Typing**: Eliminates URL entry errors
- ✅ **Print-Friendly**: Add to flyers, posters, business cards
- ✅ **Event Registration**: Quick check-in at events
- ✅ **Contactless**: Share forms without physical contact

---

## Installation

### 1. Install Package

The QR code library is included as a dependency:

```bash
composer require simplesoftwareio/simple-qrcode
```

### 2. Verify Installation

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Test QR generation
$qr = QrCode::size(300)->generate('https://example.com');
echo $qr; // Should output SVG markup
```

---

## Quick Start

### Generate QR Code for Form

```php
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;

$form = CustomForm::find(1);
$service = app(UrlObfuscationService::class);

// Generate form URL (hashid format)
$formUrl = $service->generateFormUrl($form);
// Result: https://example.com/form/x9kL2p

// Generate QR code for URL
$qrCode = $service->generateQrCode($formUrl);

// Display QR code
echo $qrCode; // SVG markup
```

---

## QR Code URL Formats

Slick Forms generates QR codes for various URL types:

### 1. Standard Form URL (Hashid)

Short, non-sequential URLs perfect for QR codes.

```php
$url = $service->generateFormUrl($form);
// https://yoursite.com/form/x9kL2p

$qrCode = $service->generateQrCode($url);
```

**URL Characteristics**:
- **Short**: Minimum 6 characters (configurable)
- **Non-sequential**: IDs are obfuscated
- **Memorable**: Easier to share than UUIDs
- **SEO-friendly**: No random characters

---

### 2. Pre-filled Form URL

QR code with encrypted form data pre-filled.

```php
// Pre-fill name and email
$prefillData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
];

$url = $service->generatePrefillUrl($form, $prefillData, expirationHours: 24);
// https://yoursite.com/form/x9kL2p/prefill/{encrypted_data}

$qrCode = $service->generateQrCode($url);
```

**Use Cases**:
- Event check-in (pre-fill attendee name)
- Customer feedback (pre-fill order number)
- Appointment confirmation (pre-fill patient ID)

---

### 3. Signed URL

Time-limited access URLs with signature.

```php
$url = $service->generateSignedUrl($form, expirationHours: 48);
// https://yoursite.com/form/x9kL2p?signature=abc123...

$qrCode = $service->generateQrCode($url);
```

**Use Cases**:
- Private surveys (expire after 2 days)
- One-time access forms
- Secure event tickets

---

## Generating QR Codes

### Basic QR Code (SVG)

```php
$service = app(UrlObfuscationService::class);

$formUrl = $service->generateFormUrl($form);
$qrCode = $service->generateQrCode($formUrl);

// Returns SVG markup (default: 300x300px)
echo $qrCode;
```

**Output**:
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300">
    <!-- QR code paths -->
</svg>
```

---

### Custom Size

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$url = $service->generateFormUrl($form);

// Small (for web badges)
$smallQr = QrCode::size(150)->generate($url);

// Medium (for email signatures)
$mediumQr = QrCode::size(300)->generate($url);

// Large (for print materials)
$largeQr = QrCode::size(600)->generate($url);
```

---

### PNG Format

Generate raster image instead of vector SVG.

```php
$url = $service->generateFormUrl($form);

$qrCode = QrCode::format('png')
    ->size(400)
    ->generate($url);

// Save to file
file_put_contents(public_path('qr-codes/form-1.png'), $qrCode);
```

---

### With Logo/Image

Add custom logo to center of QR code.

```php
$url = $service->generateFormUrl($form);

$qrCode = QrCode::format('png')
    ->merge(public_path('images/logo.png'), 0.3, true)
    ->size(500)
    ->generate($url);
```

**Parameters**:
- `0.3` - Logo takes up 30% of QR code area
- `true` - Absolute path (not relative)

---

### Color Customization

```php
$url = $service->generateFormUrl($form);

// Brand colors
$qrCode = QrCode::size(300)
    ->color(40, 86, 165) // RGB for dark blue
    ->backgroundColor(255, 255, 255)
    ->generate($url);
```

---

### Error Correction

Higher error correction allows QR code to work even if partially obscured.

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$url = $service->generateFormUrl($form);

// Error correction levels: L (7%), M (15%), Q (25%), H (30%)
$qrCode = QrCode::size(300)
    ->errorCorrection('H') // 30% of QR can be damaged and still work
    ->generate($url);
```

**When to Use**:
- **L (Low)**: Clean environments, digital displays
- **M (Medium)**: Standard use (default)
- **Q (Quartile)**: Outdoor use, printed materials
- **H (High)**: Harsh environments, adding logos

---

## Customization Options

### Complete Example

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;

$form = CustomForm::find(1);
$service = app(UrlObfuscationService::class);
$url = $service->generateFormUrl($form);

$qrCode = QrCode::format('svg')              // Format: svg, png, eps
    ->size(400)                              // Size in pixels
    ->color(31, 78, 120)                     // Foreground color (RGB)
    ->backgroundColor(240, 240, 240)         // Background color (RGB)
    ->margin(2)                              // Margin (quiet zone) size
    ->errorCorrection('M')                   // Error correction: L, M, Q, H
    ->encoding('UTF-8')                      // Character encoding
    ->merge(public_path('logo.png'), 0.2)    // Merge logo (20% of area)
    ->generate($url);

// Output or save
echo $qrCode;
file_put_contents(storage_path('qr-form-1.svg'), $qrCode);
```

---

## Embedding QR Codes

### In Blade Views

```blade
{{-- Inline SVG --}}
<div class="qr-code-container">
    <h3>Scan to Complete Form</h3>
    {!! $qrCode !!}
    <p class="text-muted">Or visit: {{ $formUrl }}</p>
</div>
```

---

### As Image Tag (PNG)

```php
// Generate and save PNG
$qrCode = QrCode::format('png')->size(300)->generate($url);
$filename = 'form-' . $form->id . '-' . time() . '.png';
Storage::disk('public')->put('qr-codes/' . $filename, $qrCode);

// Generate public URL
$qrUrl = Storage::disk('public')->url('qr-codes/' . $filename);
```

```blade
<img src="{{ $qrUrl }}" alt="QR Code for {{ $form->name }}" width="300">
```

---

### In PDF Documents

```php
use Barryvdh\DomPDF\Facade\Pdf;

$qrCode = QrCode::format('png')->size(200)->generate($url);
$qrBase64 = base64_encode($qrCode);

$pdf = Pdf::loadView('pdf.form-flyer', [
    'form' => $form,
    'qrCodeData' => $qrBase64,
]);

return $pdf->download('form-flyer.pdf');
```

```blade
{{-- In pdf.form-flyer view --}}
<div style="text-align: center;">
    <h1>{{ $form->name }}</h1>
    <p>Scan the QR code to complete this form:</p>
    <img src="data:image/png;base64,{{ $qrCodeData }}" width="300">
</div>
```

---

### Email Embeds

```blade
{{-- In email template --}}
<table>
    <tr>
        <td style="text-align: center; padding: 20px;">
            <h2>Complete Our Survey</h2>
            <img src="{{ $message->embed(storage_path('qr-codes/form-1.png')) }}"
                 alt="QR Code"
                 width="250">
            <p>Scan with your phone camera</p>
        </td>
    </tr>
</table>
```

---

### Print-Ready Materials

```php
// Generate high-resolution QR for print
$qrCode = QrCode::format('png')
    ->size(1000)  // High DPI for print
    ->errorCorrection('H')  // High error correction for durability
    ->margin(4)  // Larger margin for print cutting
    ->generate($url);

Storage::disk('public')->put('print/form-qr-print.png', $qrCode);
```

---

## Use Cases

### 1. Event Registration

Generate unique QR codes for each attendee.

```php
foreach ($attendees as $attendee) {
    // Pre-fill attendee information
    $prefillData = [
        'attendee_id' => $attendee->id,
        'name' => $attendee->name,
        'email' => $attendee->email,
    ];

    $url = $service->generatePrefillUrl($form, $prefillData, expirationHours: 72);
    $qrCode = QrCode::format('png')->size(300)->generate($url);

    // Save QR code for attendee badge
    Storage::put("badges/attendee-{$attendee->id}.png", $qrCode);
}
```

---

### 2. Restaurant Feedback

Table-specific feedback forms.

```php
$restaurantForm = CustomForm::where('name', 'Customer Feedback')->first();

foreach ($tables as $tableNumber) {
    $prefillData = ['table_number' => $tableNumber];

    $url = $service->generatePrefillUrl($restaurantForm, $prefillData);
    $qrCode = QrCode::size(200)->generate($url);

    // Print table tent cards with QR codes
    $pdf = Pdf::loadView('table-card', [
        'tableNumber' => $tableNumber,
        'qrCode' => base64_encode($qrCode),
    ]);

    $pdf->save(storage_path("cards/table-{$tableNumber}.pdf"));
}
```

---

### 3. Product Registration

Unique QR codes on product packaging.

```php
$registrationForm = CustomForm::where('name', 'Product Registration')->first();

foreach ($products as $product) {
    $prefillData = [
        'product_id' => $product->sku,
        'product_name' => $product->name,
    ];

    $url = $service->generatePrefillUrl($registrationForm, $prefillData);

    // Generate QR with product logo
    $qrCode = QrCode::format('png')
        ->size(400)
        ->merge(storage_path("logos/{$product->brand}.png"), 0.25)
        ->generate($url);

    Storage::put("products/{$product->sku}-qr.png", $qrCode);
}
```

---

### 4. Real Estate Open Houses

Generate sign-in sheet QR code for each property.

```php
$form = CustomForm::where('name', 'Open House Sign-In')->first();

foreach ($properties as $property) {
    $prefillData = [
        'property_id' => $property->mls_number,
        'address' => $property->address,
    ];

    $url = $service->generatePrefillUrl($form, $prefillData);

    // Generate branded QR code
    $qrCode = QrCode::size(500)
        ->color(0, 51, 102)  // Realtor brand color
        ->errorCorrection('H')
        ->generate($url);

    // Save for yard sign
    Storage::disk('public')->put(
        "properties/{$property->mls_number}-qr.svg",
        $qrCode
    );
}
```

---

### 5. Healthcare Patient Intake

Pre-filled patient information forms.

```php
$intakeForm = CustomForm::where('name', 'Patient Intake')->first();

foreach ($appointments as $appointment) {
    $prefillData = [
        'patient_id' => $appointment->patient->id,
        'patient_name' => $appointment->patient->name,
        'appointment_date' => $appointment->date->format('Y-m-d'),
    ];

    // Short-lived signed URL (expires in 24 hours)
    $url = $service->generatePrefillUrl($intakeForm, $prefillData, expirationHours: 24);

    $qrCode = QrCode::size(300)->generate($url);

    // Email QR code to patient
    Mail::to($appointment->patient->email)->send(
        new AppointmentReminder($appointment, $qrCode)
    );
}
```

---

## Best Practices

### 1. Use Appropriate Size

**Guidelines**:
- **Business cards**: 150-200px
- **Flyers/posters**: 300-500px
- **Billboards**: 1000+ px
- **Email**: 200-250px
- **Print (300 DPI)**: Calculate as `(inches × 300) px`

```php
// For 2-inch print size at 300 DPI
$printSize = 2 * 300; // 600px
$qrCode = QrCode::size($printSize)->generate($url);
```

---

### 2. Test Scannability

Always test QR codes before printing/distributing:

```php
// Generate test QR
$qrCode = QrCode::size(300)->generate($url);

// 1. Test with multiple devices (iPhone, Android)
// 2. Test at expected viewing distance
// 3. Test with different lighting conditions
// 4. Test partially obscured (if using high error correction)
```

---

### 3. Provide Fallback URL

Always include text URL alongside QR code:

```blade
<div class="qr-container text-center">
    {!! $qrCode !!}
    <p class="mt-3">
        <strong>Scan QR Code</strong><br>
        Or visit: <a href="{{ $url }}">{{ $shortUrl }}</a>
    </p>
</div>
```

---

### 4. Use High Error Correction for Print

```php
// For outdoor/printed QR codes
$qrCode = QrCode::errorCorrection('H') // 30% damage tolerance
    ->size(500)
    ->margin(4)  // Extra margin for cutting tolerance
    ->generate($url);
```

---

### 5. Optimize URL Length

Shorter URLs = simpler QR codes = faster scanning

```php
// Good: Hashid URL (short)
https://yoursite.com/form/x9kL2p

// Not ideal: Long numeric ID
https://yoursite.com/slick-forms/form/12345678

// Bad: UUID (very complex QR)
https://yoursite.com/form/550e8400-e29b-41d4-a716-446655440000
```

---

### 6. Brand Your QR Codes

```php
// Add company logo
$qrCode = QrCode::format('png')
    ->size(500)
    ->merge(public_path('images/logo.png'), 0.2) // Logo = 20% of QR
    ->color(31, 78, 120) // Brand color
    ->errorCorrection('H') // High correction for logo overlay
    ->generate($url);
```

---

### 7. Track QR Code Scans

Use analytics to track QR code effectiveness:

```php
// Add tracking parameter to URL
$trackedUrl = $url . '?utm_source=qr&utm_medium=print&utm_campaign=event2025';

$qrCode = QrCode::generate($trackedUrl);

// Or use separate tracking service
$trackedUrl = 'https://track.yoursite.com/r/' . $trackingId;
$qrCode = QrCode::generate($trackedUrl);
```

---

## Troubleshooting

### QR Code Won't Scan

**Possible Causes**:
1. QR code too small
2. Insufficient contrast
3. URL too long/complex
4. Insufficient quiet zone (margin)

**Solutions**:
```php
// Increase size
$qrCode = QrCode::size(500)->generate($url);

// Increase contrast
$qrCode = QrCode::color(0, 0, 0)           // Pure black
    ->backgroundColor(255, 255, 255)        // Pure white
    ->generate($url);

// Add margin
$qrCode = QrCode::margin(4)->generate($url);

// Use shorter URL (hashid)
$url = $service->generateFormUrl($form); // Uses hashid by default
```

---

### QR Code Quality Poor

**Problem**: Blurry or pixelated QR codes

**Solution**:
```php
// For print: Use higher resolution
$qrCode = QrCode::format('png')
    ->size(1200)  // High resolution
    ->generate($url);

// For web: Use SVG (scales perfectly)
$qrCode = QrCode::format('svg')->generate($url);
```

---

### QR Code Too Complex

**Problem**: Dense, difficult-to-scan QR code

**Cause**: URL too long

**Solution**:
```php
// Use hashid URLs (shortest option)
$url = $service->generateFormUrl($form);
// https://site.com/form/x9kL2p (14-20 chars total path)

// Or use URL shortener
$shortUrl = $service->generateShortUrl($url);
$qrCode = QrCode::generate($shortUrl);
```

---

### Logo Obscures QR Code

**Problem**: QR code with logo won't scan

**Solutions**:
```php
// 1. Reduce logo size
$qrCode = QrCode::merge(public_path('logo.png'), 0.15) // 15% instead of 25%
    ->generate($url);

// 2. Use higher error correction
$qrCode = QrCode::errorCorrection('H') // 30% tolerance
    ->merge(public_path('logo.png'), 0.25)
    ->generate($url);

// 3. Increase overall QR size
$qrCode = QrCode::size(600) // Larger = more redundancy
    ->merge(public_path('logo.png'), 0.2)
    ->generate($url);
```

---

### Colors Make QR Unscannable

**Problem**: Branded colors prevent scanning

**Solution**: Ensure sufficient contrast

```php
// Check contrast ratio (should be 3:1 minimum)
// Light brand colors require dark background or vice versa

// Bad: Light blue on white
$qrCode = QrCode::color(173, 216, 230)->generate($url);

// Good: Dark blue on white
$qrCode = QrCode::color(0, 51, 102)->generate($url);

// Always test with actual devices!
```

---

## Related Documentation

- [URL Obfuscation](FORM_VERSIONING.md#url-obfuscation) - Hashid URL generation
- [Form Sharing](FORM_BUILDER.md#sharing-forms) - Other form sharing methods
- [Analytics](ANALYTICS.md) - Track QR code scans
- [SimpleSoftwareIO/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) - Full QR library documentation

---

## Summary

QR codes make forms instantly accessible:

- ✅ Generate with `UrlObfuscationService::generateQrCode()`
- ✅ Use hashid URLs for shorter, simpler QR codes
- ✅ Customize size, color, format, and error correction
- ✅ Add logos for branding (with high error correction)
- ✅ Test scannability before printing/distributing
- ✅ Provide fallback text URL
- ✅ Use appropriate size for medium (print vs digital)
- ✅ Track scans with URL parameters

QR codes bridge the physical and digital worlds, making form access effortless for users on the go.

# Spam Protection

**Slick Forms v2.0+** - Complete guide for protecting forms against spam and abuse

---

## Table of Contents

- [Overview](#overview)
- [Protection Methods](#protection-methods)
- [Honeypot Fields](#honeypot-fields)
- [Rate Limiting](#rate-limiting)
- [CAPTCHA Integration](#captcha-integration)
- [IP Blacklisting](#ip-blacklisting)
- [Spam Logs](#spam-logs)
- [Configuration](#configuration)
- [Testing](#testing)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)
- [Events](#events)
- [API Reference](#api-reference)

---

## Overview

Slick Forms includes multiple layers of spam protection to prevent automated bot submissions and abuse. Protection methods can be used individually or combined for maximum security.

### Protection Layers

1. **Honeypot Fields** - Hidden fields that bots fill but humans don't
2. **Rate Limiting** - IP-based throttling to prevent mass submissions
3. **CAPTCHA** - Google reCAPTCHA v2/v3 and hCaptcha support
4. **IP Blacklisting** - Automatic blocking after repeated spam attempts

### Key Features

- ✅ Multiple protection methods (combine for best results)
- ✅ Per-form configuration
- ✅ Automatic spam logging and analytics
- ✅ IP-based rate limiting with configurable thresholds
- ✅ Honeypot with time-based detection
- ✅ reCAPTCHA v2, v3, and hCaptcha support
- ✅ Automatic IP blacklisting
- ✅ Whitelist for trusted IPs
- ✅ Comprehensive spam analytics

---

## Protection Methods

### Recommended Configuration

**Low-Risk Forms** (Newsletter signup, contact):
- Honeypot only

**Medium-Risk Forms** (Quote request, account signup):
- Honeypot + Rate limiting

**High-Risk Forms** (Payment, registration with benefits):
- Honeypot + Rate limiting + CAPTCHA

**Critical Forms** (Admin access, high-value transactions):
- All methods + IP whitelist

---

## Honeypot Fields

Hidden fields that trap bots while remaining invisible to real users.

### How It Works

1. **Hidden Field**: Form includes invisible field with tempting name (e.g., "website")
2. **Bots Fill It**: Spam bots automatically fill all fields, including hidden ones
3. **Humans Don't**: Real users can't see it, so they leave it empty
4. **Time Check**: Track how quickly form is submitted (bots submit instantly)

---

### Configuration

```php
// In config/slick-forms.php
'spam' => [
    'honeypot' => [
        'enabled' => true,
        'field_name' => 'website', // Tempting name for bots
        'time_threshold' => 3, // Minimum seconds before submission
    ],
],
```

### Environment Variables

```bash
# .env
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_HONEYPOT_FIELD_NAME=website
SLICK_FORMS_HONEYPOT_TIME_THRESHOLD=3
```

---

### HTML Implementation

```html
<!-- Honeypot field (invisible to users) -->
<input
    type="text"
    name="website"
    id="website"
    value=""
    tabindex="-1"
    autocomplete="off"
    style="position: absolute; left: -9999px; height: 0; width: 0; opacity: 0;"
>

<!-- Hidden timestamp for time-based check -->
<input type="hidden" name="_honeypot_time" value="{{ time() }}">
```

---

### Per-Form Configuration

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

$form = CustomForm::find(1);

$form->settings = array_merge($form->settings, [
    'spam' => [
        'enabled' => true,
        'honeypot' => [
            'enabled' => true,
            'field_name' => 'url', // Custom field name per form
            'time_threshold' => 5, // 5 seconds minimum
        ],
    ],
]);

$form->save();
```

---

### Time-Based Detection

Prevents instant bot submissions:

```php
// Check submission time
$formLoadTime = (int) $data['_honeypot_time'];
$submissionTime = time();
$elapsedTime = $submissionTime - $formLoadTime;

if ($elapsedTime < 3) {
    // Submitted too quickly - likely a bot
    return false;
}
```

---

## Rate Limiting

IP-based throttling to prevent mass submissions.

### Configuration

```php
// In config/slick-forms.php
'spam' => [
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 5, // Max submissions
        'decay_minutes' => 60, // Time window
    ],
],
```

### Environment Variables

```bash
SLICK_FORMS_RATE_LIMIT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_MAX_ATTEMPTS=5
SLICK_FORMS_RATE_LIMIT_DECAY_MINUTES=60
```

---

### How It Works

Uses Laravel's cache to track submission counts per IP:

```php
// Key: form_submission_rate_limit:{form_id}:{ip_address}
$key = 'form_submission_rate_limit:1:192.168.1.1';

// Check attempts in time window
$attempts = Cache::get($key, 0);

if ($attempts >= 5) {
    // Rate limit exceeded
    return false;
}

// Increment counter with TTL
Cache::put($key, $attempts + 1, now()->addMinutes(60));
```

---

### Per-Form Rate Limits

Different forms can have different limits:

```php
// Newsletter signup - lenient
$newsletterForm->settings['spam']['rate_limit'] = [
    'enabled' => true,
    'max_attempts' => 10,
    'decay_minutes' => 60,
];

// Account creation - strict
$signupForm->settings['spam']['rate_limit'] = [
    'enabled' => true,
    'max_attempts' => 3,
    'decay_minutes' => 120, // 2 hour window
];
```

---

### User-Friendly Error Messages

```php
// In form renderer
if ($rateLimitExceeded) {
    $this->addError('spam', 'Too many submission attempts. Please try again in ' . $remainingMinutes . ' minutes.');
}
```

---

## CAPTCHA Integration

Human verification using Google reCAPTCHA or hCaptcha.

### Supported CAPTCHA Types

1. **reCAPTCHA v2** - "I'm not a robot" checkbox
2. **reCAPTCHA v3** - Invisible, score-based
3. **hCaptcha** - Privacy-focused alternative

---

### reCAPTCHA v2 Setup

#### 1. Get API Keys

1. Visit [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Register a new site
3. Choose reCAPTCHA v2 → "I'm not a robot" Checkbox
4. Add your domains
5. Copy Site Key and Secret Key

#### 2. Configure Environment

```bash
# .env
RECAPTCHA_SITE_KEY=your-site-key-here
RECAPTCHA_SECRET_KEY=your-secret-key-here
SLICK_FORMS_CAPTCHA_ENABLED=true
SLICK_FORMS_CAPTCHA_TYPE=recaptcha_v2
```

#### 3. Add to Forms

```blade
{{-- In form template --}}
<div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
```

---

### reCAPTCHA v3 Setup

#### 1. Get API Keys

1. Visit [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Register a new site
3. Choose reCAPTCHA v3
4. Add your domains
5. Copy Site Key and Secret Key

#### 2. Configure Environment

```bash
# .env
RECAPTCHA_SITE_KEY=your-v3-site-key-here
RECAPTCHA_SECRET_KEY=your-v3-secret-key-here
RECAPTCHA_SCORE_THRESHOLD=0.5
SLICK_FORMS_CAPTCHA_ENABLED=true
SLICK_FORMS_CAPTCHA_TYPE=recaptcha_v3
```

#### 3. Add to Forms

```blade
<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>

<script>
grecaptcha.ready(function() {
    grecaptcha.execute('{{ config('recaptcha.site_key') }}', {action: 'submit'})
        .then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
});
</script>

<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
```

---

### Score Threshold

reCAPTCHA v3 returns a score from 0.0 (bot) to 1.0 (human):

```php
// In config/slick-forms.php
'spam' => [
    'captcha' => [
        'score_threshold' => 0.5, // Adjust based on your needs
    ],
],
```

**Score Guidelines**:
- **0.9 - 1.0**: Very likely human (lenient)
- **0.5 - 0.9**: Probably human (balanced)
- **0.0 - 0.5**: Likely bot (strict)

---

### hCaptcha Setup

Privacy-focused CAPTCHA alternative.

#### 1. Get API Keys

1. Visit [hCaptcha](https://www.hcaptcha.com/)
2. Sign up and add your site
3. Copy Site Key and Secret Key

#### 2. Configure Environment

```bash
# .env
HCAPTCHA_SITE_KEY=your-site-key-here
HCAPTCHA_SECRET_KEY=your-secret-key-here
SLICK_FORMS_CAPTCHA_ENABLED=true
SLICK_FORMS_CAPTCHA_TYPE=hcaptcha
```

#### 3. Add to Forms

```blade
<div class="h-captcha" data-sitekey="{{ config('hcaptcha.site_key') }}"></div>

<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
```

---

### Per-Form CAPTCHA

Enable CAPTCHA for specific forms only:

```php
// High-risk form - enable CAPTCHA
$form->settings['spam']['captcha'] = [
    'enabled' => true,
    'type' => 'recaptcha_v3',
];

// Low-risk form - no CAPTCHA
$form->settings['spam']['captcha'] = [
    'enabled' => false,
];

$form->save();
```

---

## IP Blacklisting

Automatically block IPs after repeated spam attempts.

### Automatic Blacklisting

```php
// In config/slick-forms.php
'spam' => [
    'ip_blacklist' => [
        'enabled' => true,
        'threshold' => 10, // Block after 10 spam attempts
        'duration_hours' => 24, // Block for 24 hours
    ],
],
```

---

### Manual IP Blocking

```php
use Illuminate\Support\Facades\Cache;

// Block specific IP
$blockedIp = '192.168.1.100';
Cache::put("ip_blacklist:{$blockedIp}", true, now()->addDays(7));

// Check if IP is blocked
if (Cache::has("ip_blacklist:{$ipAddress}")) {
    abort(403, 'Your IP has been blocked due to spam activity');
}
```

---

### IP Whitelist

Always allow trusted IPs:

```php
// In config/slick-forms.php
'spam' => [
    'ip_whitelist' => [
        '192.168.1.1',       // Office IP
        '10.0.0.0/8',        // Internal network
        '203.0.113.0/24',    // Partner network
    ],
],
```

---

### Unblock IP

```php
use Illuminate\Support\Facades\Cache;

// Remove IP from blacklist
$ipToUnblock = '192.168.1.100';
Cache::forget("ip_blacklist:{$ipToUnblock}");

echo "IP {$ipToUnblock} has been unblocked";
```

---

### View Blocked IPs

```php
use Illuminate\Support\Facades\Cache;

// Get all blacklisted IPs (requires cache tagging or manual tracking)
$blockedIps = FormSpamLog::select('ip_address')
    ->groupBy('ip_address')
    ->havingRaw('COUNT(*) >= ?', [10])
    ->get()
    ->pluck('ip_address');

foreach ($blockedIps as $ip) {
    if (Cache::has("ip_blacklist:{$ip}")) {
        echo "Blocked: {$ip}\n";
    }
}
```

---

## Spam Logs

All spam attempts are logged to `slick_form_spam_logs` table.

### Log Schema

```php
Schema::create('slick_form_spam_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('slick_forms')->cascadeOnDelete();
    $table->string('ip_address', 45); // Supports IPv4 and IPv6
    $table->string('detection_method'); // honeypot, rate_limit, recaptcha, hcaptcha
    $table->json('details')->nullable(); // Additional context
    $table->timestamp('created_at');

    $table->index(['form_id', 'created_at']);
    $table->index('ip_address');
});
```

---

### Viewing Spam Logs

```php
use DigitalisStudios\SlickForms\Models\FormSpamLog;

// Recent spam attempts
$logs = FormSpamLog::with('form')
    ->latest()
    ->paginate(50);

foreach ($logs as $log) {
    echo "IP: {$log->ip_address}\n";
    echo "Method: {$log->detection_method}\n";
    echo "Form: {$log->form->name}\n";
    echo "Time: {$log->created_at}\n\n";
}
```

---

### Filter by Method

```php
// Honeypot catches
$honeypotSpam = FormSpamLog::where('detection_method', 'honeypot')->get();

// Rate limit blocks
$rateLimitSpam = FormSpamLog::where('detection_method', 'rate_limit')->get();

// CAPTCHA failures
$captchaSpam = FormSpamLog::whereIn('detection_method', ['recaptcha', 'hcaptcha'])->get();
```

---

### Spam Statistics

```php
use DigitalisStudios\SlickForms\Services\SpamProtectionService;

$service = app(SpamProtectionService::class);
$form = CustomForm::find(1);

$stats = $service->getSpamStatistics($form);

print_r($stats);
```

**Output:**
```php
[
    'total_attempts' => 147,
    'by_method' => [
        'honeypot' => 89,
        'recaptcha' => 32,
        'rate_limit' => 26,
        'hcaptcha' => 0,
    ],
    'by_date' => [
        '2025-01-01' => 12,
        '2025-01-02' => 18,
        '2025-01-03' => 23,
        // ...
    ],
    'top_ips' => [
        '192.168.1.100' => 45,
        '203.0.113.50' => 23,
        '198.51.100.10' => 15,
        // ...
    ],
    'recent_attempts' => [...] // Last 20 attempts
]
```

---

### Analytics Dashboard

Display spam protection effectiveness:

```php
// Controller
public function spamDashboard($formId)
{
    $form = CustomForm::findOrFail($formId);
    $service = app(SpamProtectionService::class);
    $stats = $service->getSpamStatistics($form);

    return view('spam-dashboard', compact('form', 'stats'));
}
```

```blade
{{-- spam-dashboard.blade.php --}}
<h1>Spam Protection - {{ $form->name }}</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Blocked</h3>
        <p class="stat-number">{{ $stats['total_attempts'] }}</p>
    </div>

    <div class="stat-card">
        <h3>Honeypot Catches</h3>
        <p class="stat-number">{{ $stats['by_method']['honeypot'] }}</p>
    </div>

    <div class="stat-card">
        <h3>Rate Limit Blocks</h3>
        <p class="stat-number">{{ $stats['by_method']['rate_limit'] }}</p>
    </div>

    <div class="stat-card">
        <h3>CAPTCHA Failures</h3>
        <p class="stat-number">{{ $stats['by_method']['recaptcha'] + $stats['by_method']['hcaptcha'] }}</p>
    </div>
</div>

<h2>Top Spam IPs</h2>
<table>
    @foreach($stats['top_ips'] as $ip => $count)
        <tr>
            <td>{{ $ip }}</td>
            <td>{{ $count }} attempts</td>
            <td><button wire:click="blockIp('{{ $ip }}')">Block</button></td>
        </tr>
    @endforeach
</table>
```

---

## Configuration

### Complete Configuration

```php
// config/slick-forms.php
return [
    'spam' => [
        // Global enable/disable
        'enabled' => env('SLICK_FORMS_SPAM_PROTECTION_ENABLED', true),

        // Honeypot configuration
        'honeypot' => [
            'enabled' => env('SLICK_FORMS_HONEYPOT_ENABLED', true),
            'field_name' => env('SLICK_FORMS_HONEYPOT_FIELD_NAME', 'website'),
            'time_threshold' => env('SLICK_FORMS_HONEYPOT_TIME_THRESHOLD', 3),
        ],

        // Rate limiting
        'rate_limit' => [
            'enabled' => env('SLICK_FORMS_RATE_LIMIT_ENABLED', true),
            'max_attempts' => env('SLICK_FORMS_RATE_LIMIT_MAX_ATTEMPTS', 5),
            'decay_minutes' => env('SLICK_FORMS_RATE_LIMIT_DECAY_MINUTES', 60),
        ],

        // CAPTCHA
        'captcha' => [
            'enabled' => env('SLICK_FORMS_CAPTCHA_ENABLED', false),
            'type' => env('SLICK_FORMS_CAPTCHA_TYPE', 'recaptcha_v3'), // recaptcha_v2, recaptcha_v3, hcaptcha
            'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5), // v3 only
        ],

        // IP blacklisting
        'ip_blacklist' => [
            'enabled' => env('SLICK_FORMS_IP_BLACKLIST_ENABLED', true),
            'threshold' => env('SLICK_FORMS_IP_BLACKLIST_THRESHOLD', 10),
            'duration_hours' => env('SLICK_FORMS_IP_BLACKLIST_DURATION', 24),
        ],

        // IP whitelist (never block)
        'ip_whitelist' => explode(',', env('SLICK_FORMS_IP_WHITELIST', '')),
    ],
];
```

---

### Environment Variables

```bash
# Global spam protection
SLICK_FORMS_SPAM_PROTECTION_ENABLED=true

# Honeypot
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_HONEYPOT_FIELD_NAME=website
SLICK_FORMS_HONEYPOT_TIME_THRESHOLD=3

# Rate limiting
SLICK_FORMS_RATE_LIMIT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_MAX_ATTEMPTS=5
SLICK_FORMS_RATE_LIMIT_DECAY_MINUTES=60

# CAPTCHA
SLICK_FORMS_CAPTCHA_ENABLED=false
SLICK_FORMS_CAPTCHA_TYPE=recaptcha_v3
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
RECAPTCHA_SCORE_THRESHOLD=0.5

# hCaptcha (alternative)
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key

# IP Blacklisting
SLICK_FORMS_IP_BLACKLIST_ENABLED=true
SLICK_FORMS_IP_BLACKLIST_THRESHOLD=10
SLICK_FORMS_IP_BLACKLIST_DURATION=24

# IP Whitelist (comma-separated)
SLICK_FORMS_IP_WHITELIST=192.168.1.1,10.0.0.0/8
```

---

## Testing

### Test Honeypot

```php
// Trigger honeypot (fill hidden field)
$response = $this->post(route('slick-forms.submit', $form), [
    'website' => 'http://spam-site.com', // Bot fills this
    '_honeypot_time' => time(),
    // ... other fields
]);

$response->assertSessionHasErrors('spam');

// Check spam log
$this->assertDatabaseHas('slick_form_spam_logs', [
    'form_id' => $form->id,
    'detection_method' => 'honeypot',
]);
```

---

### Test Rate Limiting

```php
// Submit form multiple times
for ($i = 0; $i < 6; $i++) {
    $response = $this->post(route('slick-forms.submit', $form), $validData);
}

// 6th attempt should be blocked
$response->assertSessionHasErrors('spam');

$this->assertDatabaseHas('slick_form_spam_logs', [
    'detection_method' => 'rate_limit',
]);
```

---

### Test CAPTCHA

```php
// Mock CAPTCHA verification
Http::fake([
    'https://www.google.com/recaptcha/api/siteverify' => Http::response([
        'success' => false,
        'score' => 0.1, // Low score = bot
    ]),
]);

$response = $this->post(route('slick-forms.submit', $form), [
    'g-recaptcha-response' => 'fake-token',
    // ... other fields
]);

$response->assertSessionHasErrors('spam');
```

---

## Best Practices

### 1. Layer Protection Methods

Combine multiple methods for best results:

```php
$form->settings['spam'] = [
    'enabled' => true,
    'honeypot' => ['enabled' => true],
    'rate_limit' => ['enabled' => true, 'max_attempts' => 5],
    'captcha' => ['enabled' => true, 'type' => 'recaptcha_v3'],
];
```

---

### 2. Start Lenient, Adjust Based on Data

Begin with permissive settings and tighten as needed:

```php
// Initial configuration
'rate_limit' => [
    'max_attempts' => 10,  // Start lenient
    'decay_minutes' => 60,
],

// After analyzing spam logs, tighten
'rate_limit' => [
    'max_attempts' => 5,   // Reduce to 5
    'decay_minutes' => 120, // Extend window
],
```

---

### 3. Monitor Spam Statistics

Regularly review spam protection effectiveness:

```bash
php artisan tinker
```

```php
$form = CustomForm::find(1);
$service = app(SpamProtectionService::class);
$stats = $service->getSpamStatistics($form);

// Review which methods are catching spam
print_r($stats['by_method']);
```

---

### 4. Use reCAPTCHA v3 for Better UX

Invisible CAPTCHA provides protection without user interaction:

```php
'captcha' => [
    'enabled' => true,
    'type' => 'recaptcha_v3',  // Invisible
    'score_threshold' => 0.5,  // Adjust based on results
],
```

---

### 5. Whitelist Internal IPs

Prevent blocking legitimate testing:

```bash
SLICK_FORMS_IP_WHITELIST=192.168.1.0/24,10.0.0.0/8
```

---

### 6. Configure User-Friendly Error Messages

```php
// In FormRenderer component
if ($spamDetected) {
    $this->addError('spam', 'Your submission could not be processed. Please contact support if you believe this is an error.');
}
```

---

### 7. Regular Log Cleanup

Schedule automatic cleanup of old spam logs:

```php
// In App\Console\Kernel
$schedule->call(function () {
    FormSpamLog::where('created_at', '<', now()->subMonths(3))->delete();
})->monthly();
```

---

## Troubleshooting

### Legitimate Users Being Blocked

**Problem**: Real users can't submit forms

**Possible Causes:**
1. Rate limit too strict
2. CAPTCHA score threshold too high
3. Honeypot time threshold too high
4. IP incorrectly blacklisted

**Solutions:**
```php
// Increase rate limit
'rate_limit' => [
    'max_attempts' => 10, // From 5
    'decay_minutes' => 30, // From 60
],

// Lower CAPTCHA threshold
'captcha' => [
    'score_threshold' => 0.3, // From 0.5
],

// Reduce honeypot time
'honeypot' => [
    'time_threshold' => 2, // From 3
],

// Unblock IP
Cache::forget("ip_blacklist:192.168.1.100");
```

---

### CAPTCHA Not Showing

**Problem**: reCAPTCHA widget doesn't appear

**Check:**
1. Site key is correct
2. JavaScript included
3. Domain registered with Google
4. No JavaScript errors in console

**Debug:**
```html
<!-- Check browser console for errors -->
<script src="https://www.google.com/recaptcha/api.js"></script>

<!-- Verify site key -->
<div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>

<!-- Test key -->
<script>
    console.log('Site key:', '{{ config('recaptcha.site_key') }}');
</script>
```

---

### CAPTCHA Always Fails

**Problem**: All CAPTCHA verifications fail

**Solutions:**
```php
// Check secret key
dump(config('recaptcha.secret_key'));

// Test API connection
$response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
    'secret' => config('recaptcha.secret_key'),
    'response' => 'test-token',
]);

dd($response->json());

// Check server can reach Google
// Verify firewall isn't blocking outbound HTTPS
```

---

### Rate Limit Not Resetting

**Problem**: User still blocked after time window

**Cause**: Cache not configured or not clearing

**Solutions:**
```bash
# Clear cache
php artisan cache:clear

# Check cache driver
echo $CACHE_DRIVER

# Verify Redis/Memcached is running
redis-cli ping  # Should return "PONG"
```

---

### Honeypot Catching Real Users

**Problem**: Legitimate submissions being flagged

**Possible Causes:**
1. Browser autofill filling honeypot
2. Password manager filling all fields
3. Accessibility tools interacting with hidden field

**Solutions:**
```php
// Use more obscure field name
'honeypot' => [
    'field_name' => '_gotcha', // Instead of 'website'
],

// Add more attributes to prevent autofill
<input
    type="text"
    name="_gotcha"
    tabindex="-1"
    autocomplete="off"
    aria-hidden="true"
    style="position:absolute;left:-9999px;opacity:0;"
>
```

---

## Events

### SpamDetected

Dispatched when spam is detected.

```php
use DigitalisStudios\SlickForms\Events\SpamDetected;

Event::listen(SpamDetected::class, function ($event) {
    $form = $event->form;
    $ipAddress = $event->ipAddress;
    $method = $event->detectionMethod;

    // Alert administrators about persistent spam
    $spamCount = FormSpamLog::where('ip_address', $ipAddress)
        ->where('created_at', '>', now()->subHour())
        ->count();

    if ($spamCount > 10) {
        Mail::to('security@example.com')->send(
            new SpamAlert($ipAddress, $spamCount)
        );
    }
});
```

See [Events Documentation](EVENTS.md) for complete event reference.

---

## API Reference

### SpamProtectionService

#### `validateSubmission()`

Validate submission against all enabled spam protection methods.

```php
public function validateSubmission(
    CustomForm $form,
    array $data,
    string $ipAddress
): bool
```

**Returns:** `true` if valid, `false` if spam detected

---

#### `checkHoneypot()`

Check honeypot field and time threshold.

```php
public function checkHoneypot(
    array $data,
    string $fieldName,
    int $timeThreshold
): bool
```

---

#### `checkRateLimit()`

Check IP-based rate limiting.

```php
public function checkRateLimit(
    CustomForm $form,
    string $ipAddress,
    int $maxAttempts,
    int $decayMinutes
): bool
```

---

#### `verifyRecaptcha()`

Verify reCAPTCHA token.

```php
public function verifyRecaptcha(
    string $token,
    string $secretKey,
    float $scoreThreshold
): bool
```

---

#### `verifyHcaptcha()`

Verify hCaptcha token.

```php
public function verifyHcaptcha(
    string $token,
    string $secretKey
): bool
```

---

#### `logSpamAttempt()`

Log spam attempt for analytics.

```php
public function logSpamAttempt(
    CustomForm $form,
    string $ipAddress,
    string $method,
    array $details = []
): void
```

---

#### `getSpamStatistics()`

Get spam statistics for a form.

```php
public function getSpamStatistics(CustomForm $form): array
```

---

## Related Documentation

- [Events Reference](EVENTS.md) - SpamDetected event
- [Configuration](CONFIGURATION.md) - Complete configuration options
- [Security](SECURITY.md) - General security best practices

---

## Summary

Multi-layered spam protection keeps your forms clean:

- ✅ **Honeypot**: Silent trap for bots (89% effective)
- ✅ **Rate Limiting**: Prevent mass submissions
- ✅ **CAPTCHA**: Human verification (reCAPTCHA v2/v3, hCaptcha)
- ✅ **IP Blacklisting**: Automatic blocking after repeated spam
- ✅ **Comprehensive Logging**: Track all spam attempts
- ✅ **Per-Form Configuration**: Customize protection per form
- ✅ **Whitelist Support**: Never block trusted IPs

Start with honeypot + rate limiting, add CAPTCHA for high-risk forms, and monitor spam logs to fine-tune your protection strategy.

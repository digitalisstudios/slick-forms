# Form Analytics Guide

## Overview

Slick Forms includes a comprehensive analytics system that tracks form performance, user behavior, and submission patterns. No additional packages required - fully built-in.

## Accessing Analytics

**Route:** `/slick-forms/analytics/{form}`

**Livewire Component:** `slick-forms::form-analytics`

**Controller:** `FormAnalyticsController`

**Navigation:** Click the "Analytics" button in the form builder header

## Metrics Tracked

### Summary Metrics
- **Total Views** - Number of times form was viewed
- **Total Starts** - Number of users who began filling out the form
- **Total Completions** - Number of successful submissions
- **Conversion Rate** - Percentage of views that result in completions
- **Average Completion Time** - Mean time from start to submission

### Time-Series Data
- **Submissions Over Time** - Chart showing submissions by day/week/month
- **Views Over Time** - Form view trends
- **Starts Over Time** - When users begin filling forms

### Field-Level Analytics
- **Field Completion Rates** - Percentage of users who fill each field
- **Field Drop-Off Points** - Where users abandon the form
- **Field Focus Time** - Average time spent on each field
- **Field Validation Errors** - Most common validation failures per field

### Device & Browser Breakdown
- **Device Types** - Mobile, tablet, desktop distribution
- **Browsers** - Browser usage (Chrome, Firefox, Safari, Edge, etc.)
- **Operating Systems** - OS distribution

### Validation Error Analysis
- **Common Errors** - Most frequent validation failures
- **Error Rates** - Percentage of submissions with errors
- **Field-Specific Errors** - Which fields cause most issues

## Database Schema

### slick_form_analytics_sessions
Tracks unique user sessions:

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Session ID |
| `slick_form_id` | bigint | Form being tracked |
| `session_id` | string (UUID) | Unique session identifier |
| `device_type` | enum | mobile\|tablet\|desktop |
| `browser` | string | Browser name and version |
| `os` | string | Operating system |
| `started_at` | timestamp | When user began filling form |
| `completed_at` | timestamp (nullable) | When user submitted form |
| `created_at` | timestamp | Session creation time |
| `updated_at` | timestamp | Last update time |

### slick_form_analytics_events
Tracks individual events within sessions:

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Event ID |
| `analytics_session_id` | bigint | Foreign key to session |
| `event_type` | string | Type of event |
| `event_data` | json | Event-specific data |
| `created_at` | timestamp | When event occurred |

## Using Analytics Service

### Programmatic Access

```php
use DigitalisStudios\SlickForms\Services\FormAnalyticsService;

$analyticsService = app(FormAnalyticsService::class);

// Get summary for last 30 days
$summary = $analyticsService->getFormSummary($formId, $days = 30);
/*
Returns:
[
    'views' => 1250,
    'starts' => 820,
    'completions' => 456,
    'conversion_rate' => 36.48,
    'average_completion_time' => 180, // seconds
]
*/

// Get submissions over time
$chart = $analyticsService->getSubmissionsOverTime($formId, $days = 30);
/*
Returns:
[
    ['date' => '2025-01-01', 'count' => 15],
    ['date' => '2025-01-02', 'count' => 23],
    ...
]
*/

// Get field completion rates
$fieldRates = $analyticsService->getFieldCompletionRates($formId);
/*
Returns:
[
    ['field_name' => 'email', 'completion_rate' => 95.5],
    ['field_name' => 'phone', 'completion_rate' => 78.2],
    ...
]
*/

// Get device breakdown
$devices = $analyticsService->getDeviceBreakdown($formId, $days = 30);
/*
Returns:
[
    'mobile' => 450,
    'tablet' => 120,
    'desktop' => 680,
]
*/

// Get browser breakdown
$browsers = $analyticsService->getBrowserBreakdown($formId, $days = 30);
/*
Returns:
[
    'Chrome' => 580,
    'Safari' => 320,
    'Firefox' => 180,
    'Edge' => 170,
]
*/

// Get common validation errors
$errors = $analyticsService->getCommonValidationErrors($formId);
/*
Returns:
[
    ['field' => 'email', 'error' => 'Invalid email format', 'count' => 45],
    ['field' => 'phone', 'error' => 'Phone number required', 'count' => 32],
    ...
]
*/
```

### Event Types

**view** - User viewed the form
```php
Event Data: {
    "referrer": "https://example.com/products",
    "landing_page": "/contact"
}
```

**start** - User started filling the form (first field interaction)
```php
Event Data: {
    "first_field": "email"
}
```

**field_focus** - User focused on a field
```php
Event Data: {
    "field_id": 123,
    "field_name": "email"
}
```

**field_blur** - User left a field
```php
Event Data: {
    "field_id": 123,
    "field_name": "email",
    "duration_seconds": 5
}
```

**validation_error** - Field validation failed
```php
Event Data: {
    "field_id": 123,
    "field_name": "email",
    "error": "Invalid email format"
}
```

**submit_success** - Form submitted successfully
```php
Event Data: {
    "submission_id": 456,
    "duration_seconds": 120
}
```

**submit_failure** - Form submission failed
```php
Event Data: {
    "errors": ["email: Invalid format", "phone: Required"],
    "attempt_number": 2
}
```

## Analytics Dashboard

The built-in analytics dashboard provides:

### 1. Summary Cards
Key metrics displayed at the top:
- Total Views with trend indicator
- Total Starts with conversion from views
- Total Completions with conversion from starts
- Overall Conversion Rate (views → completions)

### 2. Submissions Chart
Line/bar chart showing submissions over time with:
- Date range selector (7, 30, 90, 365 days, all time)
- Hover tooltips showing exact counts
- Responsive design for mobile viewing

### 3. Field Performance Table
Table showing for each field:
- Field name and type
- Completion rate (percentage)
- Average focus time
- Drop-off rate
- Validation error count

### 4. Device Pie Chart
Visual breakdown of device types:
- Mobile percentage
- Tablet percentage
- Desktop percentage

### 5. Browser Pie Chart
Visual breakdown of browser usage:
- Chrome, Firefox, Safari, Edge, Other

### 6. Validation Errors List
Most common validation errors with:
- Field name
- Error message
- Occurrence count
- Percentage of total submissions

### Filtering by Date Range

Use the date range selector to view analytics for:
- **Last 7 days** - Recent activity
- **Last 30 days** (default) - Monthly overview
- **Last 90 days** - Quarterly trends
- **Last 365 days** - Annual performance
- **All time** - Complete history

## Privacy Considerations

### What IS Tracked
- Device type, browser, OS (from User-Agent header)
- Session timing and interactions
- Field-level behavior (focus, blur, completion)
- Validation errors and their frequency
- Form completion funnel

### What is NOT Tracked
- IP addresses (not stored in analytics tables)
- Personally identifiable information (PII) from form data
- User names or email addresses
- Actual form field values or submission content
- Cross-site tracking or third-party cookies

### Compliance
- **Session-based tracking** - No persistent cookies required
- **Self-hosted** - All data stays on your server
- **No third-party services** - No data sent to external analytics providers
- **GDPR-friendly** - No PII stored in analytics tables
- **Transparent** - Users can see what's tracked in event data

## Disabling Analytics

### Per-Form Basis

Disable analytics for a specific form:

```php
$form = CustomForm::find($formId);
$form->settings['analytics_enabled'] = false;
$form->save();
```

### Package-Wide

Disable analytics for all forms:

```php
// config/slick-forms.php
return [
    'analytics_enabled' => false,
];
```

Or via environment variable:

```env
SLICK_FORMS_ANALYTICS_ENABLED=false
```

### Clear Analytics Data

Delete analytics data older than specific date:

```php
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;

// Delete sessions older than 1 year
SlickFormAnalyticsSession::where('created_at', '<', now()->subYear())
    ->delete();

// This will cascade delete related events due to foreign key constraint
```

## Performance

### Impact on Form Rendering
- **Minimal overhead** - <5ms additional processing time
- **Async event tracking** - No blocking of form submission
- **Batched writes** - Events grouped for efficient database writes
- **Indexed queries** - Fast retrieval with proper database indexes

### Database Optimization

Analytics tables are optimized with indexes:

```php
// Automatically created by migrations
$table->index('slick_form_id');
$table->index('created_at');
$table->index(['slick_form_id', 'created_at']);
$table->index('session_id');
$table->index('event_type');
```

### Scaling Considerations

For high-traffic forms (>10,000 views/day):

1. **Archive Old Data** - Move data older than 1-2 years to archive tables
2. **Queue Event Processing** - Move event writes to Laravel queue
3. **Read Replicas** - Use read replicas for analytics queries
4. **Caching** - Cache dashboard data for 5-15 minutes

```php
// Example: Cache dashboard summary for 5 minutes
$summary = Cache::remember("analytics.{$formId}.summary", 300, function () use ($formId) {
    return app(FormAnalyticsService::class)->getFormSummary($formId);
});
```

## Best Practices

### 1. Set Retention Policy
Delete old analytics data after 1-2 years to keep database lean:

```php
// In a scheduled command
Schedule::command('analytics:cleanup')->monthly();
```

### 2. Monitor Drop-Off Points
- Identify problematic fields with high drop-off rates
- Simplify or remove fields causing abandonment
- Adjust validation rules if too strict

### 3. Track Conversion Rates
- Set baseline conversion rate goals
- A/B test different form designs
- Compare multi-page vs single-page forms

### 4. Analyze Device Data
- Optimize for most-used devices
- Test mobile experience if mobile traffic is high
- Consider device-specific form layouts

### 5. Fix Validation Errors
- Review common validation error messages
- Improve error message clarity
- Add input masks to reduce format errors
- Provide examples in placeholder text

### 6. Review Field Completion
- Remove optional fields with <30% completion
- Make required fields more prominent
- Add help text to confusing fields

## Integration Examples

### Custom Analytics Dashboard

Create your own analytics view:

```php
// In your controller
use DigitalisStudios\SlickForms\Services\FormAnalyticsService;

public function customAnalytics($formId)
{
    $service = app(FormAnalyticsService::class);

    return view('analytics.custom', [
        'summary' => $service->getFormSummary($formId, 30),
        'chart' => $service->getSubmissionsOverTime($formId, 30),
        'fields' => $service->getFieldCompletionRates($formId),
        'devices' => $service->getDeviceBreakdown($formId, 30),
    ]);
}
```

### Export Analytics to CSV

```php
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;

$sessions = SlickFormAnalyticsSession::where('slick_form_id', $formId)
    ->with('events')
    ->get();

$csv = "Session ID,Device,Browser,Started,Completed,Duration\n";

foreach ($sessions as $session) {
    $duration = $session->completed_at
        ? $session->completed_at->diffInSeconds($session->started_at)
        : 'N/A';

    $csv .= "{$session->session_id},{$session->device_type},{$session->browser},"
          . "{$session->started_at},{$session->completed_at},{$duration}\n";
}

return response($csv)
    ->header('Content-Type', 'text/csv')
    ->header('Content-Disposition', 'attachment; filename="analytics.csv"');
```

### Track Custom Events

Add custom event tracking:

```php
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsEvent;

// Get or create session
$session = SlickFormAnalyticsSession::firstOrCreate([
    'slick_form_id' => $formId,
    'session_id' => session()->getId(),
], [
    'device_type' => $this->detectDevice(),
    'browser' => $this->detectBrowser(),
    'os' => $this->detectOS(),
]);

// Track custom event
SlickFormAnalyticsEvent::create([
    'analytics_session_id' => $session->id,
    'event_type' => 'custom_action',
    'event_data' => [
        'action' => 'clicked_help_button',
        'field' => 'email',
        'timestamp' => now()->toIso8601String(),
    ],
]);
```

## Troubleshooting

### Analytics Not Recording

**Possible Causes:**
1. Analytics disabled in form settings
2. Analytics disabled globally in config
3. JavaScript errors preventing event tracking
4. Database migration not run

**Solutions:**
```php
// 1. Check form settings
$form = CustomForm::find($formId);
dd($form->settings['analytics_enabled'] ?? true);

// 2. Check global config
dd(config('slick-forms.analytics_enabled'));

// 3. Check browser console for JS errors
// Open DevTools → Console tab

// 4. Run migrations
php artisan migrate
```

### Data Not Showing in Dashboard

**Possible Causes:**
1. Date range filter too narrow
2. Form has no submissions yet
3. Sessions table empty

**Solutions:**
```php
// 1. Change date range to "All Time"
// 2. Check if form has submissions:
$submissionCount = SlickFormSubmission::where('slick_form_id', $formId)->count();

// 3. Check sessions table:
$sessionCount = SlickFormAnalyticsSession::where('slick_form_id', $formId)->count();
```

### Performance Issues with Large Data

**Solutions:**
1. Archive old data (>1 year)
2. Add additional indexes if needed
3. Cache dashboard results
4. Limit query date ranges

```php
// Archive old data
$oldData = SlickFormAnalyticsSession::where('created_at', '<', now()->subYear())->get();
// Move to archive table or delete
SlickFormAnalyticsSession::where('created_at', '<', now()->subYear())->delete();
```

### Incorrect Device Detection

**Cause:** User-Agent parsing issues

**Solution:** Verify User-Agent detection logic:

```php
// In FormAnalyticsService
private function detectDevice($userAgent)
{
    if (preg_match('/mobile|android|iphone/i', $userAgent)) {
        return 'mobile';
    }
    if (preg_match('/tablet|ipad/i', $userAgent)) {
        return 'tablet';
    }
    return 'desktop';
}
```

## API Reference

### FormAnalyticsService Methods

```php
// Get form summary
getFormSummary(int $formId, int $days = 30): array

// Get submissions over time
getSubmissionsOverTime(int $formId, int $days = 30): array

// Get field completion rates
getFieldCompletionRates(int $formId): array

// Get device breakdown
getDeviceBreakdown(int $formId, int $days = 30): array

// Get browser breakdown
getBrowserBreakdown(int $formId, int $days = 30): array

// Get common validation errors
getCommonValidationErrors(int $formId, int $limit = 10): array

// Get drop-off points
getDropOffPoints(int $formId): array

// Get average completion time
getAverageCompletionTime(int $formId): float
```

## Related Documentation

- [Form Builder Guide](CUSTOM_FIELD_TYPES.md)
- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)
- [Multi-Page Forms](../MULTI_PAGE_FORMS.md)
- [Conditional Logic](CONDITIONAL_LOGIC.md)

---

**Need Help?** Visit the [Slick Forms repository](https://bitbucket.org/bmooredigitalisstudios/slick-forms) for support.

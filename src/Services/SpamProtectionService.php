<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Events\SpamDetected;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormSpamLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Service for handling spam protection
 */
class SpamProtectionService
{
    /**
     * Validate submission against all enabled spam protection methods
     *
     * @param  array  $data  Form submission data
     * @param  string  $ipAddress  Submitter IP address
     * @return bool True if submission is valid (not spam)
     */
    public function validateSubmission(CustomForm $form, array $data, string $ipAddress): bool
    {
        // Check honeypot if enabled
        if (config('slick-forms.spam.honeypot.enabled', true)) {
            if (! $this->checkHoneypot(
                $data,
                config('slick-forms.spam.honeypot.field_name', 'website'),
                config('slick-forms.spam.honeypot.time_threshold', 3)
            )) {
                $this->logSpamAttempt($form, $ipAddress, 'honeypot', [
                    'field_filled' => ! empty($data[config('slick-forms.spam.honeypot.field_name', 'website')]),
                    'submission_time' => $data['_honeypot_time'] ?? null,
                ]);
                event(new SpamDetected($form, $ipAddress, 'honeypot'));

                return false;
            }
        }

        // Check rate limiting if enabled
        if (config('slick-forms.spam.rate_limit.enabled', true)) {
            if (! $this->checkRateLimit(
                $form,
                $ipAddress,
                config('slick-forms.spam.rate_limit.max_attempts', 5),
                config('slick-forms.spam.rate_limit.decay_minutes', 60)
            )) {
                $this->logSpamAttempt($form, $ipAddress, 'rate_limit', [
                    'max_attempts' => config('slick-forms.spam.rate_limit.max_attempts', 5),
                    'window_minutes' => config('slick-forms.spam.rate_limit.decay_minutes', 60),
                ]);
                event(new SpamDetected($form, $ipAddress, 'rate_limit'));

                return false;
            }
        }

        // Check reCAPTCHA if enabled
        if (! empty($data['g-recaptcha-response']) && config('recaptcha.secret_key')) {
            if (! $this->verifyRecaptcha(
                $data['g-recaptcha-response'],
                config('recaptcha.secret_key'),
                config('recaptcha.score_threshold', 0.5)
            )) {
                $this->logSpamAttempt($form, $ipAddress, 'recaptcha', [
                    'token_present' => true,
                ]);
                event(new SpamDetected($form, $ipAddress, 'recaptcha'));

                return false;
            }
        }

        // Check hCaptcha if enabled
        if (! empty($data['h-captcha-response']) && config('hcaptcha.secret_key')) {
            if (! $this->verifyHcaptcha(
                $data['h-captcha-response'],
                config('hcaptcha.secret_key')
            )) {
                $this->logSpamAttempt($form, $ipAddress, 'hcaptcha', [
                    'token_present' => true,
                ]);
                event(new SpamDetected($form, $ipAddress, 'hcaptcha'));

                return false;
            }
        }

        return true;
    }

    /**
     * Check honeypot field for spam detection
     *
     * @param  array  $data  Form submission data
     * @param  string  $fieldName  Name of honeypot field
     * @param  int  $timeThreshold  Minimum time in seconds before submission allowed
     * @return bool True if honeypot check passes
     */
    public function checkHoneypot(array $data, string $fieldName, int $timeThreshold): bool
    {
        // Check if honeypot field is filled (bots fill hidden fields)
        if (! empty($data[$fieldName])) {
            return false;
        }

        // Check if form was submitted too quickly (bots submit instantly)
        if (isset($data['_honeypot_time'])) {
            $submissionTime = time();
            $formLoadTime = (int) $data['_honeypot_time'];
            $elapsedTime = $submissionTime - $formLoadTime;

            if ($elapsedTime < $timeThreshold) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify reCAPTCHA v3 token
     *
     * @param  string  $token  reCAPTCHA response token
     * @param  string  $secretKey  reCAPTCHA secret key
     * @param  float  $scoreThreshold  Minimum score (0.0-1.0) to pass
     * @return bool True if reCAPTCHA verification passes
     */
    public function verifyRecaptcha(string $token, string $secretKey, float $scoreThreshold): bool
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            // Check if verification was successful
            if (! $result['success']) {
                return false;
            }

            // Check score (reCAPTCHA v3 only)
            if (isset($result['score'])) {
                return $result['score'] >= $scoreThreshold;
            }

            return true;
        } catch (\Exception $e) {
            // On error, allow submission but log the error
            \Log::error('reCAPTCHA verification failed', [
                'error' => $e->getMessage(),
            ]);

            return true;
        }
    }

    /**
     * Verify hCaptcha token
     *
     * @param  string  $token  hCaptcha response token
     * @param  string  $secretKey  hCaptcha secret key
     * @return bool True if hCaptcha verification passes
     */
    public function verifyHcaptcha(string $token, string $secretKey): bool
    {
        try {
            $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);

            // Check if response was successful
            if (! $response->successful()) {
                \Log::error('hCaptcha API returned error status', [
                    'status' => $response->status(),
                ]);

                return true; // Allow submission on API error
            }

            $result = $response->json();

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            // On error, allow submission but log the error
            \Log::error('hCaptcha verification failed', [
                'error' => $e->getMessage(),
            ]);

            return true;
        }
    }

    /**
     * Check rate limiting for form submissions
     *
     * @param  string  $ipAddress  Submitter IP address
     * @param  int  $maxAttempts  Maximum attempts allowed
     * @param  int  $decayMinutes  Time window in minutes
     * @return bool True if under rate limit
     */
    public function checkRateLimit(
        CustomForm $form,
        string $ipAddress,
        int $maxAttempts,
        int $decayMinutes
    ): bool {
        $key = 'form_submission_rate_limit:'.$form->id.':'.$ipAddress;

        // Get current attempt count
        $attempts = (int) Cache::get($key, 0);

        // Check if limit exceeded
        if ($attempts >= $maxAttempts) {
            return false;
        }

        // Increment attempts
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return true;
    }

    /**
     * Log spam attempt for analytics (only if spam_logs feature enabled)
     *
     * @param  string  $ipAddress  Submitter IP address
     * @param  string  $method  Detection method: 'honeypot', 'recaptcha', 'hcaptcha', 'rate_limit'
     * @param  array  $details  Additional context data
     */
    public function logSpamAttempt(
        CustomForm $form,
        string $ipAddress,
        string $method,
        array $details = []
    ): void {
        if (! slick_forms_feature_enabled('spam_logs')) {
            return;
        }

        FormSpamLog::create([
            'form_id' => $form->id,
            'ip_address' => $ipAddress,
            'detection_method' => $method,
            'details' => $details,
        ]);
    }

    /**
     * Get spam statistics for a form
     *
     * @return array Spam statistics including counts by method
     */
    public function getSpamStatistics(CustomForm $form): array
    {
        $logs = FormSpamLog::where('form_id', $form->id)->get();

        return [
            'total_attempts' => $logs->count(),
            'by_method' => [
                'honeypot' => $logs->where('detection_method', 'honeypot')->count(),
                'recaptcha' => $logs->where('detection_method', 'recaptcha')->count(),
                'hcaptcha' => $logs->where('detection_method', 'hcaptcha')->count(),
                'rate_limit' => $logs->where('detection_method', 'rate_limit')->count(),
            ],
            'by_date' => $logs->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            })->map->count(),
            'top_ips' => $logs->groupBy('ip_address')
                ->map->count()
                ->sortDesc()
                ->take(10),
            'recent_attempts' => FormSpamLog::where('form_id', $form->id)->latest()->take(20)->get()->values(),
        ];
    }
}

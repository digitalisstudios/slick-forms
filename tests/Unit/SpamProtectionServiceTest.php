<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\SpamDetected;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormSpamLog;
use DigitalisStudios\SlickForms\Services\SpamProtectionService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class SpamProtectionServiceTest extends TestCase
{
    protected SpamProtectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SpamProtectionService::class);
    }

    /** @test */
    public function it_detects_honeypot_spam_when_field_is_filled()
    {
        $data = [
            'website' => 'http://spam.com', // Honeypot field filled
            '_honeypot_time' => time(),
        ];

        $result = $this->service->checkHoneypot($data, 'website', 3);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_passes_honeypot_when_field_is_empty()
    {
        $data = [
            'website' => '',
            '_honeypot_time' => time() - 5, // 5 seconds ago
        ];

        $result = $this->service->checkHoneypot($data, 'website', 3);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_detects_honeypot_spam_when_submitted_too_quickly()
    {
        $data = [
            'website' => '',
            '_honeypot_time' => time() - 1, // Only 1 second ago
        ];

        $result = $this->service->checkHoneypot($data, 'website', 3);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_enforces_rate_limiting()
    {
        Cache::flush();
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        // First submission should pass
        $result1 = $this->service->checkRateLimit($form, '192.168.1.1', 3, 60);
        $this->assertTrue($result1);

        // Second submission should pass
        $result2 = $this->service->checkRateLimit($form, '192.168.1.1', 3, 60);
        $this->assertTrue($result2);

        // Third submission should pass
        $result3 = $this->service->checkRateLimit($form, '192.168.1.1', 3, 60);
        $this->assertTrue($result3);

        // Fourth submission should fail (exceeded limit of 3)
        $result4 = $this->service->checkRateLimit($form, '192.168.1.1', 3, 60);
        $this->assertFalse($result4);
    }

    /** @test */
    public function it_tracks_rate_limit_per_form_and_ip()
    {
        Cache::flush();
        $form1 = CustomForm::create(['name' => 'Form 1', 'is_active' => true]);
        $form2 = CustomForm::create(['name' => 'Form 2', 'is_active' => true]);

        // Use up rate limit for form1 with IP1
        $this->service->checkRateLimit($form1, '192.168.1.1', 2, 60);
        $this->service->checkRateLimit($form1, '192.168.1.1', 2, 60);

        // Should still be able to submit to form1 with different IP
        $result1 = $this->service->checkRateLimit($form1, '192.168.1.2', 2, 60);
        $this->assertTrue($result1);

        // Should still be able to submit to form2 with same IP
        $result2 = $this->service->checkRateLimit($form2, '192.168.1.1', 2, 60);
        $this->assertTrue($result2);
    }

    /** @test */
    public function it_logs_spam_attempts()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $this->service->logSpamAttempt($form, '192.168.1.1', 'honeypot', [
            'field_filled' => true,
        ]);

        $this->assertDatabaseHas('slick_form_spam_logs', [
            'form_id' => $form->id,
            'ip_address' => '192.168.1.1',
            'detection_method' => 'honeypot',
        ]);
    }

    /** @test */
    public function it_returns_spam_statistics()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        // Create various spam logs
        FormSpamLog::create([
            'form_id' => $form->id,
            'ip_address' => '192.168.1.1',
            'detection_method' => 'honeypot',
            'details' => [],
        ]);

        FormSpamLog::create([
            'form_id' => $form->id,
            'ip_address' => '192.168.1.2',
            'detection_method' => 'recaptcha',
            'details' => [],
        ]);

        FormSpamLog::create([
            'form_id' => $form->id,
            'ip_address' => '192.168.1.1',
            'detection_method' => 'rate_limit',
            'details' => [],
        ]);

        $stats = $this->service->getSpamStatistics($form);

        $this->assertEquals(3, $stats['total_attempts']);
        $this->assertEquals(1, $stats['by_method']['honeypot']);
        $this->assertEquals(1, $stats['by_method']['recaptcha']);
        $this->assertEquals(1, $stats['by_method']['rate_limit']);
        $this->assertArrayHasKey('192.168.1.1', $stats['top_ips']);
        $this->assertEquals(2, $stats['top_ips']['192.168.1.1']);
    }

    /** @test */
    public function it_validates_submission_with_honeypot_enabled()
    {
        config(['slick-forms.spam.honeypot.enabled' => true]);
        Event::fake();

        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $data = [
            'website' => 'spam',
            '_honeypot_time' => time(),
        ];

        $result = $this->service->validateSubmission($form, $data, '192.168.1.1');

        $this->assertFalse($result);
        Event::assertDispatched(SpamDetected::class);

        $this->assertDatabaseHas('slick_form_spam_logs', [
            'form_id' => $form->id,
            'detection_method' => 'honeypot',
        ]);
    }

    /** @test */
    public function it_validates_submission_with_rate_limiting_enabled()
    {
        Cache::flush();
        config(['slick-forms.spam.honeypot.enabled' => false]);
        config(['slick-forms.spam.rate_limit.enabled' => true]);
        config(['slick-forms.spam.rate_limit.max_attempts' => 2]);
        Event::fake();

        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        // First two should pass
        $result1 = $this->service->validateSubmission($form, [], '192.168.1.1');
        $result2 = $this->service->validateSubmission($form, [], '192.168.1.1');

        $this->assertTrue($result1);
        $this->assertTrue($result2);

        // Third should fail
        $result3 = $this->service->validateSubmission($form, [], '192.168.1.1');

        $this->assertFalse($result3);
        Event::assertDispatched(SpamDetected::class);

        $this->assertDatabaseHas('slick_form_spam_logs', [
            'form_id' => $form->id,
            'detection_method' => 'rate_limit',
        ]);
    }

    /** @test */
    public function it_verifies_recaptcha_successfully()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ]),
        ]);

        $result = $this->service->verifyRecaptcha('test-token', 'test-secret', 0.5);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_rejects_low_recaptcha_scores()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.3,
            ]),
        ]);

        $result = $this->service->verifyRecaptcha('test-token', 'test-secret', 0.5);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_verifies_hcaptcha_successfully()
    {
        Http::fake([
            'https://hcaptcha.com/siteverify' => Http::response([
                'success' => true,
            ]),
        ]);

        $result = $this->service->verifyHcaptcha('test-token', 'test-secret');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_rejects_failed_hcaptcha_verification()
    {
        Http::fake([
            'https://hcaptcha.com/siteverify' => Http::response([
                'success' => false,
            ]),
        ]);

        $result = $this->service->verifyHcaptcha('test-token', 'test-secret');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_handles_recaptcha_api_failures_gracefully()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response('', 500),
        ]);

        // Should return true (allow submission) on API failure
        $result = $this->service->verifyRecaptcha('test-token', 'test-secret', 0.5);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_handles_hcaptcha_api_failures_gracefully()
    {
        Http::fake([
            'https://hcaptcha.com/siteverify' => Http::response(null, 500),
        ]);

        // Should return true (allow submission) on API failure
        $result = $this->service->verifyHcaptcha('test-token', 'test-secret');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_passes_validation_when_all_checks_disabled()
    {
        config(['slick-forms.spam.honeypot.enabled' => false]);
        config(['slick-forms.spam.rate_limit.enabled' => false]);

        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $result = $this->service->validateSubmission($form, [], '192.168.1.1');

        $this->assertTrue($result);
    }
}

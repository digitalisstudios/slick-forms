<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\PrefillDataDecrypted;
use DigitalisStudios\SlickForms\Events\SignedUrlGenerated;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormSignedUrl;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

class UrlObfuscationServiceTest extends TestCase
{
    protected UrlObfuscationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UrlObfuscationService::class);
    }

    /** @test */
    public function it_generates_form_url_with_id_strategy()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'strategy' => 'id',
                ],
            ],
        ]);

        $url = $this->service->generateFormUrl($form);

        // All forms now use hashid URLs regardless of strategy setting
        $hashid = $this->service->encodeId($form->id);
        $this->assertStringContainsString($hashid, $url);
        $this->assertStringNotContainsString((string) $form->id, $url);
        $this->assertStringNotContainsString('signature', $url);
    }

    /** @test */
    public function it_generates_form_url_with_uuid_strategy()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'uuid' => 'test-uuid-12345',
            'settings' => [
                'url_security' => [
                    'strategy' => 'uuid',
                ],
            ],
        ]);

        $url = $this->service->generateFormUrl($form);

        // All forms now use hashid URLs regardless of strategy setting
        $hashid = $this->service->encodeId($form->id);
        $this->assertStringContainsString($hashid, $url);
        $this->assertStringNotContainsString('test-uuid-12345', $url);
    }

    /** @test */
    public function it_generates_form_url_with_hashid_strategy()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'strategy' => 'hashid',
                ],
            ],
        ]);

        $url = $this->service->generateFormUrl($form);

        // Should contain hashid, not numeric ID
        $hashid = $this->service->encodeId($form->id);
        $this->assertStringContainsString($hashid, $url);
        $this->assertStringNotContainsString((string) $form->id, $url);
    }

    /** @test */
    public function it_encodes_and_decodes_hashid_correctly()
    {
        $originalId = 12345;

        $encoded = $this->service->encodeId($originalId);
        $decoded = $this->service->decodeId($encoded);

        $this->assertNotEquals($originalId, $encoded); // Encoded should be different
        $this->assertEquals($originalId, $decoded); // Decoded should match original
    }

    /** @test */
    public function it_encodes_and_decodes_with_custom_salt()
    {
        $originalId = 12345;
        $customSalt = 'my-custom-salt-key';

        $encoded = $this->service->encodeId($originalId, $customSalt);
        $decoded = $this->service->decodeId($encoded, $customSalt);

        $this->assertEquals($originalId, $decoded);
    }

    /** @test */
    public function it_returns_null_for_invalid_hashid()
    {
        $decoded = $this->service->decodeId('invalid-hash-123');

        $this->assertNull($decoded);
    }

    /** @test */
    public function it_generates_signed_url_with_signature()
    {
        Event::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'strategy' => 'id',
                ],
            ],
        ]);

        $url = $this->service->generateSignedUrl($form, 24);

        $this->assertStringContainsString('signature=', $url);

        Event::assertDispatched(SignedUrlGenerated::class);

        $this->assertDatabaseHas('slick_form_signed_urls', [
            'form_id' => $form->id,
        ]);
    }

    /** @test */
    public function it_verifies_valid_signed_url()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $url = $this->service->generateSignedUrl($form, 24);

        // Extract signature from URL
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $signature = $params['signature'];

        $isValid = $this->service->verifySignedUrl($signature);

        $this->assertTrue($isValid);

        // Verify usage count incremented
        $signedUrl = FormSignedUrl::where('signature', $signature)->first();
        $this->assertEquals(1, $signedUrl->uses);
    }

    /** @test */
    public function it_rejects_invalid_signature()
    {
        $isValid = $this->service->verifySignedUrl('invalid-signature-123');

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_rejects_expired_signed_url()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        // Create expired signed URL
        $signature = 'test-signature-expired';
        FormSignedUrl::create([
            'form_id' => $form->id,
            'signature' => $signature,
            'expires_at' => now()->subHours(1), // Expired 1 hour ago
        ]);

        $isValid = $this->service->verifySignedUrl($signature);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_generates_prefill_url_with_encrypted_data()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'prefill_expiration_hours' => 48,
                ],
            ],
        ]);

        $prefillData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $url = $this->service->generatePrefillUrl($form, $prefillData, 48);

        $this->assertStringContainsString('prefill', $url);
        $this->assertStringContainsString('/form/', $url);

        $this->assertDatabaseHas('slick_form_signed_urls', [
            'form_id' => $form->id,
        ]);

        $signedUrl = FormSignedUrl::where('form_id', $form->id)->first();
        $this->assertEquals($prefillData, $signedUrl->prefill_data['data']);
    }

    /** @test */
    public function it_decrypts_valid_prefill_data()
    {
        Event::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $originalData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '555-1234',
        ];

        // Encrypt data as the service would
        $prefillData = [
            'data' => $originalData,
            'expires_at' => now()->addHours(24)->toIso8601String(),
        ];

        $encryptedData = base64_encode(Crypt::encryptString(json_encode($prefillData)));

        $decrypted = $this->service->decryptPrefillData($encryptedData, $form);

        $this->assertEquals($originalData, $decrypted);

        Event::assertDispatched(PrefillDataDecrypted::class);
    }

    /** @test */
    public function it_returns_null_for_expired_prefill_data()
    {
        $originalData = [
            'name' => 'Expired User',
            'email' => 'expired@example.com',
        ];

        // Create expired prefill data
        $prefillData = [
            'data' => $originalData,
            'expires_at' => now()->subHours(1)->toIso8601String(), // Expired 1 hour ago
        ];

        $encryptedData = base64_encode(Crypt::encryptString(json_encode($prefillData)));

        $decrypted = $this->service->decryptPrefillData($encryptedData);

        $this->assertNull($decrypted);
    }

    /** @test */
    public function it_returns_null_for_invalid_encrypted_data()
    {
        $decrypted = $this->service->decryptPrefillData('invalid-base64-data!!!');

        $this->assertNull($decrypted);
    }

    /** @test */
    public function it_returns_null_for_tampered_encrypted_data()
    {
        $validEncrypted = base64_encode(Crypt::encryptString(json_encode([
            'data' => ['test' => 'data'],
            'expires_at' => now()->addHours(24)->toIso8601String(),
        ])));

        // Tamper with the data
        $tamperedEncrypted = substr($validEncrypted, 0, -5).'XXXXX';

        $decrypted = $this->service->decryptPrefillData($tamperedEncrypted);

        $this->assertNull($decrypted);
    }

    /** @test */
    public function it_generates_qr_code_for_url()
    {
        $url = 'https://example.com/form/12345';

        $qrCode = $this->service->generateQrCode($url);

        $this->assertStringContainsString('<svg', $qrCode);
        $this->assertStringContainsString('</svg>', $qrCode);
    }

    /** @test */
    public function it_uses_default_strategy_when_not_specified()
    {
        config(['slick-forms.urls.default_strategy' => 'hashid']);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [], // No strategy specified
        ]);

        $url = $this->service->generateFormUrl($form);

        $hashid = $this->service->encodeId($form->id);
        $this->assertStringContainsString($hashid, $url);
    }

    /** @test */
    public function it_generates_signed_url_when_required_in_settings()
    {
        Event::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'strategy' => 'id',
                    'require_signature' => true,
                ],
            ],
        ]);

        $url = $this->service->generateFormUrl($form);

        $this->assertStringContainsString('signature=', $url);

        Event::assertDispatched(SignedUrlGenerated::class);
    }

    /** @test */
    public function it_uses_custom_hashid_salt_from_config()
    {
        config(['slick-forms.urls.hashid_salt' => 'custom-salt-123']);

        // Create new service instance to pick up config
        $service = new UrlObfuscationService;

        $encoded1 = $service->encodeId(100);

        // Change salt
        config(['slick-forms.urls.hashid_salt' => 'different-salt-456']);
        $service2 = new UrlObfuscationService;

        $encoded2 = $service2->encodeId(100);

        // Same ID with different salt should produce different hashes
        $this->assertNotEquals($encoded1, $encoded2);
    }

    /** @test */
    public function it_respects_custom_hashid_min_length()
    {
        config(['slick-forms.urls.hashid_min_length' => 10]);

        $service = new UrlObfuscationService;

        $encoded = $service->encodeId(1);

        $this->assertGreaterThanOrEqual(10, strlen($encoded));
    }
}

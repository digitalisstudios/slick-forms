<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Events\PrefillDataDecrypted;
use DigitalisStudios\SlickForms\Events\SignedUrlGenerated;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormSignedUrl;
use Hashids\Hashids;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Service for URL obfuscation and secure form links
 */
class UrlObfuscationService
{
    protected Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('slick-forms.urls.hashid_salt', config('app.key')),
            config('slick-forms.urls.hashid_min_length', 6)
        );
    }

    /**
     * Generate form URL with optional pre-fill data and expiration
     *
     * All forms now use hashid URLs (short, non-sequential, shareable)
     *
     * @param  CustomForm  $form  The form to generate URL for
     * @param  array|null  $prefillData  Optional data to pre-fill form fields
     * @param  int|null  $expirationHours  Optional expiration time in hours
     * @return string Generated form URL
     */
    public function generateFormUrl(CustomForm $form, ?array $prefillData = null, ?int $expirationHours = null): string
    {
        // Generate hashid-based URL (always use hashid strategy for all forms)
        $baseUrl = route('slick-forms.form.show.hash', [
            'hash' => $this->encodeId($form->id, $form->hashid_salt),
        ]);

        // If pre-fill data provided, generate pre-fill URL
        if ($prefillData) {
            return $this->generatePrefillUrl($form, $prefillData, $expirationHours);
        }

        // If signed URL required, generate signed URL
        if ($form->settings['url_security']['require_signature'] ?? false) {
            return $this->generateSignedUrl($form, $expirationHours);
        }

        return $baseUrl;
    }

    /**
     * Encode numeric ID to hashid string
     *
     * @param  int  $id  Numeric ID to encode
     * @param  string|null  $customSalt  Optional custom salt for encoding
     * @return string Encoded hashid
     */
    public function encodeId(int $id, ?string $customSalt = null): string
    {
        if ($customSalt) {
            $hashids = new Hashids($customSalt, config('slick-forms.urls.hashid_min_length', 6));

            return $hashids->encode($id);
        }

        return $this->hashids->encode($id);
    }

    /**
     * Decode hashid string to numeric ID
     *
     * @param  string  $hash  Hashid to decode
     * @param  string|null  $customSalt  Optional custom salt for decoding
     * @return int|null Decoded ID or null if invalid
     */
    public function decodeId(string $hash, ?string $customSalt = null): ?int
    {
        if ($customSalt) {
            $hashids = new Hashids($customSalt, config('slick-forms.urls.hashid_min_length', 6));
            $decoded = $hashids->decode($hash);
        } else {
            $decoded = $this->hashids->decode($hash);
        }

        return $decoded[0] ?? null;
    }

    /**
     * Generate signed URL for form access
     *
     * @param  CustomForm  $form  The form to generate signed URL for
     * @param  int|null  $expirationHours  Optional expiration time in hours
     * @return string Signed URL with signature parameter
     */
    public function generateSignedUrl(CustomForm $form, ?int $expirationHours = null): string
    {
        $expirationHours = $expirationHours ?? config('slick-forms.urls.signed_url_expiration', 24);
        $expiresAt = now()->addHours($expirationHours);

        // Generate unique signature
        $signature = Str::random(64);

        // Store signed URL record
        $signedUrl = FormSignedUrl::create([
            'form_id' => $form->id,
            'signature' => $signature,
            'expires_at' => $expiresAt,
            'prefill_data' => null,
        ]);

        // Dispatch event for tracking/logging
        event(new SignedUrlGenerated($form, $signature, $expiresAt));

        // Generate hashid-based URL without signature to avoid infinite loop
        $baseUrl = route('slick-forms.form.show.hash', [
            'hash' => $this->encodeId($form->id, $form->hashid_salt),
        ]);

        return $baseUrl.'?signature='.$signature;
    }

    /**
     * Verify signed URL signature
     *
     * @param  string  $signature  Signature from URL parameter
     * @return bool True if signature is valid and not expired
     */
    public function verifySignedUrl(string $signature): bool
    {
        $signedUrl = FormSignedUrl::where('signature', $signature)->first();

        if (! $signedUrl) {
            return false;
        }

        // Check if valid
        if (! $signedUrl->isValid()) {
            return false;
        }

        // Increment usage counter
        $signedUrl->incrementUses();

        return true;
    }

    /**
     * Generate URL with encrypted pre-fill data
     *
     * @param  CustomForm  $form  The form to generate pre-fill URL for
     * @param  array  $data  Data to pre-fill in form
     * @param  int|null  $expirationHours  Optional expiration time in hours
     * @return string URL with encrypted data parameter
     */
    public function generatePrefillUrl(CustomForm $form, array $data, ?int $expirationHours = null): string
    {
        $expirationHours = $expirationHours ?? $form->settings['url_security']['prefill_expiration_hours'] ?? 24;
        $expiresAt = now()->addHours($expirationHours);

        // Generate unique signature
        $signature = Str::random(64);

        // Prepare prefill data with expiration
        $prefillData = [
            'data' => $data,
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        // Encrypt the prefill data
        $encryptedData = base64_encode(Crypt::encryptString(json_encode($prefillData)));

        // Store signed URL record with prefill data
        FormSignedUrl::create([
            'form_id' => $form->id,
            'signature' => $signature,
            'prefill_data' => $prefillData,
            'expires_at' => $expiresAt,
        ]);

        // Generate prefill URL with hashid
        return route('slick-forms.form.show.prefilled', [
            'hash' => $this->encodeId($form->id, $form->hashid_salt),
            'data' => $encryptedData,
        ]);
    }

    /**
     * Decrypt pre-fill data from URL parameter
     *
     * @param  string  $encrypted  Encrypted data string from URL
     * @param  CustomForm|null  $form  Optional form instance for event tracking
     * @return array|null Decrypted data or null if invalid/expired
     */
    public function decryptPrefillData(string $encrypted, ?CustomForm $form = null): ?array
    {
        try {
            // Decrypt the base64-encoded encrypted string
            $decrypted = Crypt::decryptString(base64_decode($encrypted));
            $prefillData = json_decode($decrypted, true);

            // Check if data has expiration and is expired
            if (isset($prefillData['expires_at'])) {
                $expiresAt = \Carbon\Carbon::parse($prefillData['expires_at']);
                if ($expiresAt->isPast()) {
                    return null;
                }
            }

            // Dispatch event for tracking (only if form provided)
            if ($form) {
                event(new PrefillDataDecrypted($form, $prefillData['data'] ?? []));
            }

            return $prefillData['data'] ?? [];
        } catch (\Exception $e) {
            // Invalid or tampered data
            return null;
        }
    }

    /**
     * Generate QR code image for form URL
     *
     * @param  string  $url  Form URL to encode
     * @return string QR code image data (SVG format)
     */
    public function generateQrCode(string $url): string
    {
        return QrCode::size(300)
            ->format('svg')
            ->generate($url);
    }

    /**
     * Generate shortened URL for form
     *
     * Note: This is a placeholder implementation. In production, you would integrate
     * with a URL shortening service like Bitly, TinyURL, or your own service.
     *
     * @param  string  $url  Full form URL to shorten
     * @return string Shortened URL (currently returns original URL)
     */
    public function generateShortUrl(string $url): string
    {
        // Placeholder implementation - return original URL
        // In production, integrate with a URL shortening service:
        //
        // Example with Bitly API:
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer '.config('services.bitly.token'),
        // ])->post('https://api-ssl.bitly.com/v4/shorten', [
        //     'long_url' => $url,
        // ]);
        //
        // return $response->json()['link'] ?? $url;

        return $url;
    }
}

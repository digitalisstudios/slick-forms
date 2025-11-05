<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormRendererController extends Controller
{
    public function __construct(
        protected UrlObfuscationService $urlService
    ) {}

    /**
     * Show form by hashid
     */
    public function showByHash(string $hash, Request $request)
    {
        // Decode hashid to get form ID
        $formId = $this->urlService->decodeId($hash);

        if (! $formId) {
            abort(404, 'Invalid form URL.');
        }

        $form = CustomForm::findOrFail($formId);

        return $this->renderForm($form, $request);
    }

    /**
     * Show form with pre-filled data
     */
    public function showPrefilled(string $hash, string $data, Request $request)
    {
        // Resolve form from hashid
        $formId = $this->urlService->decodeId($hash);
        $formModel = CustomForm::findOrFail($formId);

        // Decrypt and validate prefill data (pass form for event tracking)
        $prefillData = $this->urlService->decryptPrefillData($data, $formModel);

        if ($prefillData === null) {
            abort(403, 'Invalid or expired pre-fill data.');
        }

        // Pass prefill data to the view
        return $this->renderForm($formModel, $request, $prefillData);
    }

    /**
     * Common method to render form with validation
     */
    protected function renderForm(CustomForm $form, Request $request, ?array $prefillData = null)
    {
        // Check if form is active
        if (! $form->is_active) {
            abort(404, 'This form is not currently available.');
        }

        // Check if form has expired
        if ($form->isExpired()) {
            abort(410, 'This form is no longer available.');
        }

        // Verify signed URL if required by form settings
        if ($form->settings['url_security']['require_signature'] ?? false) {
            $signature = $request->query('signature');

            if (! $signature || ! $this->urlService->verifySignedUrl($signature)) {
                abort(403, 'Invalid or expired URL signature.');
            }
        }

        // Check access control restrictions (if implemented in Phase 5)
        // TODO: Add password protection, IP restrictions, submission limits, etc.

        return view('slick-forms::renderer.show', compact('form', 'prefillData'));
    }
}

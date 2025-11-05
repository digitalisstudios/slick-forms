{{--
    URL Settings Tab

    Form-level URL obfuscation and security configuration.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="url-settings">
    {{-- URL Format Info --}}
    <div class="alert alert-info mb-4">
        <h6 class="alert-heading mb-2">
            <i class="bi bi-link-45deg me-1"></i>URL Format
        </h6>
        <p class="mb-2">
            All forms use <strong>hashid URLs</strong> (e.g., <code>/form/9zA4kL</code>)
        </p>
        <small class="text-muted mb-0">
            Hashid URLs are short, shareable, and non-sequential for privacy and security.
        </small>
    </div>

    {{-- Custom Hashid Salt --}}
    <div class="mb-4">
        <label for="hashidSalt" class="form-label">
            Custom Hashid Salt <span class="text-muted">(Optional)</span>
        </label>
        <input
            type="text"
            class="form-control font-monospace"
            id="hashidSalt"
            wire:model="form.hashid_salt"
            placeholder="Leave empty to use global salt"
        >
        <small class="text-muted">
            Per-form salt for additional obfuscation. Leave empty to use application default.
        </small>
    </div>

    <hr class="my-4">

    {{-- Signed URLs --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-shield-check me-2"></i>Signed URLs
            </h6>
        </div>
        <div class="card-body">
            {{-- Require Signature --}}
            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="requireSignature"
                    wire:model.live="form.settings.url_security.require_signature"
                >
                <label class="form-check-label" for="requireSignature">
                    Require signed URLs (tamper-proof)
                </label>
            </div>

            @if($form->settings['url_security']['require_signature'] ?? false)
                {{-- Signature Expiration --}}
                <div class="mb-3">
                    <label for="signatureExpiration" class="form-label">
                        Signature Expiration (hours)
                    </label>
                    <div class="d-flex align-items-center gap-3">
                        <input
                            type="range"
                            class="form-range flex-grow-1"
                            id="signatureExpiration"
                            min="1"
                            max="168"
                            step="1"
                            wire:model.live="form.settings.url_security.signature_expiration_hours"
                        >
                        <span class="badge bg-primary" style="min-width: 60px;">
                            {{ $form->settings['url_security']['signature_expiration_hours'] ?? 24 }} hrs
                        </span>
                    </div>
                    <small class="text-muted">
                        How long signed URLs remain valid (1-168 hours)
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- Pre-fill URLs --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-clipboard-data me-2"></i>Pre-fill URLs
            </h6>
        </div>
        <div class="card-body">
            {{-- Allow Pre-filled URLs --}}
            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="allowPrefill"
                    wire:model.live="form.settings.url_security.allow_prefilled_urls"
                >
                <label class="form-check-label" for="allowPrefill">
                    Allow pre-filled URLs
                </label>
            </div>

            @if($form->settings['url_security']['allow_prefilled_urls'] ?? true)
                {{-- Pre-fill Expiration --}}
                <div class="mb-3">
                    <label for="prefillExpiration" class="form-label">
                        Pre-fill Data Expiration (hours)
                    </label>
                    <div class="d-flex align-items-center gap-3">
                        <input
                            type="range"
                            class="form-range flex-grow-1"
                            id="prefillExpiration"
                            min="1"
                            max="168"
                            step="1"
                            wire:model.live="form.settings.url_security.prefill_expiration_hours"
                        >
                        <span class="badge bg-primary" style="min-width: 60px;">
                            {{ $form->settings['url_security']['prefill_expiration_hours'] ?? 24 }} hrs
                        </span>
                    </div>
                    <small class="text-muted">
                        How long encrypted pre-fill data remains valid (1-168 hours)
                    </small>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>How it works:</strong> Generate shareable URLs with encrypted field values.
                    Recipients see a pre-filled form. Use the Share Form panel to create pre-fill URLs.
                </div>
            @endif
        </div>
    </div>

    {{-- URL Preview --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-eye me-2"></i>URL Preview
            </h6>
        </div>
        <div class="card-body">
            <div class="input-group">
                <input
                    type="text"
                    class="form-control font-monospace"
                    value="{{ $this->getFormUrl() }}"
                    readonly
                    id="urlPreview"
                >
                <button
                    class="btn btn-outline-secondary"
                    type="button"
                    onclick="navigator.clipboard.writeText(document.getElementById('urlPreview').value); this.innerHTML='<i class=\'bi bi-check\'></i> Copied'; setTimeout(() => this.innerHTML='<i class=\'bi bi-clipboard\'></i> Copy', 2000)"
                >
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            <small class="text-muted">
                This preview URL always uses the hashid strategy (short, shareable, non-sequential).
            </small>
        </div>
    </div>

    {{-- Share Form Button --}}
    <div class="d-grid">
        <button
            type="button"
            class="btn btn-primary btn-lg"
            wire:click="$dispatch('openSharePanel')"
        >
            <i class="bi bi-share me-2"></i>Share This Form
        </button>
        <small class="text-muted text-center mt-2">
            Generate QR codes, pre-fill URLs, and more
        </small>
    </div>
</div>

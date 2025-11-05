{{--
    Spam Settings Tab

    Form-level spam protection configuration for honeypot, CAPTCHA, and rate limiting.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="spam-settings">
    {{-- Enable Spam Protection --}}
    <div class="mb-4">
        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                id="spamProtectionEnabled"
                wire:model.live="spamProtectionEnabled"
            >
            <label class="form-check-label fw-bold" for="spamProtectionEnabled">
                Enable Spam Protection
            </label>
        </div>
        <small class="text-muted d-block mt-1">
            Protect your form from spam submissions using multiple detection methods
        </small>
    </div>

    @if($spamProtectionEnabled)
        {{-- Honeypot Protection Section --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-bug me-2"></i>Honeypot Protection
                </h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="honeypotEnabled"
                        wire:model.live="honeypotEnabled"
                    >
                    <label class="form-check-label" for="honeypotEnabled">
                        Enable Honeypot Field
                    </label>
                </div>

                @if($honeypotEnabled)
                    <div class="mb-3">
                        <label for="honeypotFieldName" class="form-label">
                            Field Name
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="honeypotFieldName"
                            wire:model.live.debounce.500ms="honeypotFieldName"
                            wire:change="saveSpamSettings"
                            placeholder="website"
                        >
                        <small class="text-muted">
                            Hidden field name that bots typically fill out
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="honeypotTimeThreshold" class="form-label">
                            Minimum Submission Time (seconds)
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="honeypotTimeThreshold"
                            wire:model.live.debounce.500ms="honeypotTimeThreshold"
                            wire:change="saveSpamSettings"
                            min="1"
                            max="60"
                            placeholder="3"
                        >
                        <small class="text-muted">
                            Block submissions faster than this threshold (helps detect bots)
                        </small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>How it works:</strong> A hidden field is added to your form. Legitimate users won't see it, but bots often fill it out automatically. Submissions with filled honeypot fields are rejected.
                    </div>
                @endif
            </div>
        </div>

        {{-- CAPTCHA Protection Section --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>CAPTCHA Protection
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">CAPTCHA Type</label>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="captchaType"
                            id="captchaTypeNone"
                            value="none"
                            wire:model.live="captchaType"
                        >
                        <label class="form-check-label" for="captchaTypeNone">
                            None (Disabled)
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="captchaType"
                            id="captchaTypeRecaptcha"
                            value="recaptcha"
                            wire:model.live="captchaType"
                        >
                        <label class="form-check-label" for="captchaTypeRecaptcha">
                            Google reCAPTCHA v3 (Invisible)
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="captchaType"
                            id="captchaTypeHcaptcha"
                            value="hcaptcha"
                            wire:model.live="captchaType"
                        >
                        <label class="form-check-label" for="captchaTypeHcaptcha">
                            hCaptcha (Challenge-based)
                        </label>
                    </div>
                </div>

                {{-- reCAPTCHA v3 Settings --}}
                @if($captchaType === 'recaptcha')
                    <div class="border-start border-primary border-3 ps-3 mb-3">
                        <h6 class="mb-3">reCAPTCHA v3 Configuration</h6>

                        <div class="mb-3">
                            <label for="recaptchaSiteKey" class="form-label">
                                Site Key <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control font-monospace"
                                id="recaptchaSiteKey"
                                wire:model.live.debounce.500ms="recaptchaSiteKey"
                                wire:change="saveSpamSettings"
                                placeholder="6Lc..."
                            >
                            <small class="text-muted">
                                Get your keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">google.com/recaptcha/admin</a>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="recaptchaSecretKey" class="form-label">
                                Secret Key <span class="text-danger">*</span>
                            </label>
                            <input
                                type="password"
                                class="form-control font-monospace"
                                id="recaptchaSecretKey"
                                wire:model.live.debounce.500ms="recaptchaSecretKey"
                                wire:change="saveSpamSettings"
                                placeholder="6Lc..."
                            >
                        </div>

                        <div class="mb-3">
                            <label for="recaptchaScoreThreshold" class="form-label">
                                Score Threshold (0.0 - 1.0)
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                id="recaptchaScoreThreshold"
                                wire:model.live.debounce.500ms="recaptchaScoreThreshold"
                                wire:change="saveSpamSettings"
                                step="0.1"
                                min="0.0"
                                max="1.0"
                                placeholder="0.5"
                            >
                            <small class="text-muted">
                                Scores below this threshold will be rejected. 0.5 is recommended (0.0 = bot, 1.0 = human)
                            </small>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            reCAPTCHA v3 runs invisibly in the background and scores users based on their behavior. No challenges or clicking required.
                        </div>
                    </div>
                @endif

                {{-- hCaptcha Settings --}}
                @if($captchaType === 'hcaptcha')
                    <div class="border-start border-success border-3 ps-3 mb-3">
                        <h6 class="mb-3">hCaptcha Configuration</h6>

                        <div class="mb-3">
                            <label for="hcaptchaSiteKey" class="form-label">
                                Site Key <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control font-monospace"
                                id="hcaptchaSiteKey"
                                wire:model.live.debounce.500ms="hcaptchaSiteKey"
                                wire:change="saveSpamSettings"
                                placeholder="10000000-ffff-ffff-ffff-000000000001"
                            >
                            <small class="text-muted">
                                Get your keys at <a href="https://dashboard.hcaptcha.com/" target="_blank">dashboard.hcaptcha.com</a>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="hcaptchaSecretKey" class="form-label">
                                Secret Key <span class="text-danger">*</span>
                            </label>
                            <input
                                type="password"
                                class="form-control font-monospace"
                                id="hcaptchaSecretKey"
                                wire:model.live.debounce.500ms="hcaptchaSecretKey"
                                wire:change="saveSpamSettings"
                                placeholder="0x..."
                            >
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            hCaptcha shows users a challenge (like "Select all images with cars"). Privacy-focused alternative to reCAPTCHA.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Rate Limiting Section --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-speedometer me-2"></i>Rate Limiting
                </h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="rateLimitEnabled"
                        wire:model.live="rateLimitEnabled"
                    >
                    <label class="form-check-label" for="rateLimitEnabled">
                        Enable Rate Limiting
                    </label>
                </div>

                @if($rateLimitEnabled)
                    <div class="mb-3">
                        <label for="rateLimitMaxAttempts" class="form-label">
                            Maximum Attempts
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="rateLimitMaxAttempts"
                            wire:model.live.debounce.500ms="rateLimitMaxAttempts"
                            wire:change="saveSpamSettings"
                            min="1"
                            max="100"
                            placeholder="5"
                        >
                        <small class="text-muted">
                            Maximum number of submissions allowed per IP address
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="rateLimitDecayMinutes" class="form-label">
                            Time Window (minutes)
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="rateLimitDecayMinutes"
                            wire:model.live.debounce.500ms="rateLimitDecayMinutes"
                            wire:change="saveSpamSettings"
                            min="1"
                            max="1440"
                            placeholder="60"
                        >
                        <small class="text-muted">
                            Time period for rate limit counter (60 minutes = 1 hour)
                        </small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Example:</strong> With 5 attempts in 60 minutes, a user can submit 5 times per hour before being blocked.
                    </div>
                @endif
            </div>
        </div>

        {{-- Spam Logs Button --}}
        <div class="d-grid">
            <button
                type="button"
                class="btn btn-outline-secondary"
                wire:click="showSpamLogs"
            >
                <i class="bi bi-list-ul me-1"></i>View Spam Logs
            </button>
        </div>
    @endif
</div>

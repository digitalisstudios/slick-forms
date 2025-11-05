<div class="slick-form-wrapper">
    @once
        {{-- Tom Select (Searchable Dropdowns) --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        {{-- Flatpickr (Enhanced Date Picker) --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>

        {{-- Cleave.js (Input Masking) --}}
        <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/addons/cleave-phone.us.js"></script>

        {{-- Quill WYSIWYG Editor --}}
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @endonce

    @if($submitted)
        @php
            $successSettings = $this->getSuccessSettings();
        @endphp

        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">
                {{ $successSettings['message_title'] ?? 'Thank you!' }}
            </h4>

            <div class="success-message">
                {!! $this->renderSuccessMessage() !!}
            </div>

            @if(($successSettings['message_show_data'] ?? $successSettings['show_submission_data'] ?? false) && $lastSubmission)
                <div class="submission-data mt-4">
                    <h5>Your Submission:</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            @foreach($lastSubmission->fieldValues as $value)
                                @php
                                    $hiddenFields = $successSettings['message_hidden_fields'] ?? $successSettings['hidden_fields'] ?? [];
                                @endphp
                                @if(!in_array($value->field->name, $hiddenFields))
                                    <tr>
                                        <td class="fw-bold">{{ $value->field->label ?: $value->field->name }}:</td>
                                        <td>{{ $value->value }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif

            @if($lastSubmission)
                <div class="d-flex gap-2 mt-3">
                    @if($successSettings['download_pdf_enabled'] ?? $successSettings['enable_pdf_download'] ?? false)
                        <a href="#" class="btn btn-primary" onclick="alert('PDF download feature coming soon'); return false;">
                            <i class="bi bi-file-pdf me-1"></i>
                            {{ $successSettings['download_pdf_button_text'] ?? $successSettings['pdf_button_text'] ?? 'Download PDF' }}
                        </a>
                    @endif

                    @if($successSettings['download_csv_enabled'] ?? $successSettings['enable_csv_download'] ?? false)
                        <a href="#" class="btn btn-outline-primary" onclick="alert('CSV download feature coming soon'); return false;">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                            {{ $successSettings['download_csv_button_text'] ?? $successSettings['csv_button_text'] ?? 'Download CSV' }}
                        </a>
                    @endif

                    @if($successSettings['edit_link_enabled'] ?? $successSettings['enable_edit_link'] ?? false)
                        <a href="{{ route('slick-forms.form.show.hash', ['hash' => app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class)->encodeId($form->id)]) }}?edit={{ $lastSubmission->id }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i>
                            {{ $successSettings['edit_link_text'] ?? 'Edit Your Submission' }}
                        </a>
                    @endif
                </div>
            @endif

            @if(($successSettings['action_type'] ?? $successSettings['type'] ?? 'message') === 'message_then_redirect')
                @php
                    $redirectDelay = $successSettings['redirect_delay'] ?? $successSettings['redirect_delay_seconds'] ?? 3;
                    $redirectUrl = $this->parseRedirectUrl($successSettings['redirect_url'] ?? '', $lastSubmission);
                @endphp
                <div class="mt-4">
                    <p class="mb-0">
                        Redirecting to <a href="{{ $redirectUrl }}">{{ $redirectUrl }}</a> in <span id="countdown">{{ $redirectDelay }}</span> seconds...
                    </p>
                    <script>
                        let countdown = {{ $redirectDelay }};
                        const interval = setInterval(() => {
                            countdown--;
                            document.getElementById('countdown').innerText = countdown;
                            if (countdown === 0) {
                                clearInterval(interval);
                                window.location.href = '{{ $redirectUrl }}';
                            }
                        }, 1000);
                    </script>
                </div>
            @endif
        </div>
    @else
        {{-- Progress Indicator for Multi-Page Forms --}}
        @if($form->isMultiPage() && count($pages) > 1)
                            @php
                                $progressStyle = $form->getProgressStyle();
                                $totalPages = count($pages);
                                $currentStep = $currentPageIndex + 1;
                                $progressPercentage = ($currentStep / $totalPages) * 100;
                            @endphp

                            <div class="mt-3">
                                @if($progressStyle === 'steps')
                                    {{-- Step Indicators --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        @foreach($pages as $index => $page)
                                            @if($page['show_in_progress'])
                                                @php
                                                    $isActive = $index === $currentPageIndex;
                                                    $isCompleted = $index < $currentPageIndex;
                                                    $stepNumber = $index + 1;
                                                @endphp
                                                <div class="d-flex flex-column align-items-center flex-grow-1">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $isCompleted ? 'bg-success text-white' : ($isActive ? 'bg-primary text-white' : 'bg-secondary text-white') }}"
                                                         style="width: 40px; height: 40px; font-weight: bold;">
                                                        @if($isCompleted)
                                                            <i class="bi bi-check-lg"></i>
                                                        @else
                                                            @if($page['icon'])
                                                                <i class="{{ $page['icon'] }}"></i>
                                                            @else
                                                                {{ $stepNumber }}
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <small class="mt-1 text-center {{ $isActive ? 'fw-bold' : 'text-muted' }}" style="font-size: 0.75rem;">
                                                        {{ $page['title'] }}
                                                    </small>
                                                </div>
                                                @if(!$loop->last)
                                                    <div class="flex-grow-1 mx-2" style="height: 2px; background-color: {{ $isCompleted ? '#198754' : '#dee2e6' }};"></div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($progressStyle === 'bar')
                                    {{-- Progress Bar --}}
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Step {{ $currentStep }} of {{ $totalPages }}</span>
                                            <span class="text-muted">{{ round($progressPercentage) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $progressPercentage }}%;"
                                                 aria-valuenow="{{ $progressPercentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ $pages[$currentPageIndex]['title'] }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($progressStyle === 'breadcrumb')
                                    {{-- Breadcrumb Navigation --}}
                                    <nav aria-label="breadcrumb" class="mb-3">
                                        <ol class="breadcrumb mb-0">
                                            @foreach($pages as $index => $page)
                                                @if($page['show_in_progress'])
                                                    @php
                                                        $isActive = $index === $currentPageIndex;
                                                        $isCompleted = $index < $currentPageIndex;
                                                    @endphp
                                                    <li class="breadcrumb-item {{ $isActive ? 'active' : '' }}">
                                                        @if($page['icon'])
                                                            <i class="{{ $page['icon'] }} me-1"></i>
                                                        @endif
                                                        {{ $page['title'] }}
                                                        @if($isCompleted)
                                                            <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ol>
                                    </nav>
                                @endif
                            </div>
                        @endif

        <form wire:submit="submit" id="form-{{ $form->id }}">
            @php
                // Define getElementAttributes helper for rendering element attributes
                $getElementAttributes = function($element) {
                    return '';
                };
            @endphp

            @foreach($formStructure as $node)
                @include('slick-forms::livewire.partials.render-element', ['node' => $node, 'registry' => $registry, 'formData' => $formData, 'visibleFieldIds' => $visibleFieldIds, 'getElementAttributes' => $getElementAttributes, 'repeaterInstances' => $repeaterInstances])
            @endforeach

            {{-- Spam Protection Fields --}}
            @if($form->settings['spam']['enabled'] ?? false)
                {{-- Honeypot Field (Hidden) --}}
                @if($form->settings['spam']['honeypot']['enabled'] ?? false)
                    <div style="position: absolute; left: -9999px;" aria-hidden="true">
                        <input
                            type="text"
                            name="{{ $form->settings['spam']['honeypot']['field_name'] ?? 'website' }}"
                            wire:model="formData.{{ $form->settings['spam']['honeypot']['field_name'] ?? 'website' }}"
                            tabindex="-1"
                            autocomplete="off"
                        >
                        <input
                            type="hidden"
                            name="_honeypot_time"
                            wire:model="formData._honeypot_time"
                        >
                    </div>
                @endif

                {{-- Spam Error Display --}}
                @error('spam')
                    <div class="container">
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
                        </div>
                    </div>
                @enderror

                {{-- CAPTCHA Widgets --}}
                @php
                    $captchaType = $form->settings['spam']['captcha']['type'] ?? 'none';
                @endphp

                @if($captchaType === 'recaptcha')
                    {{-- reCAPTCHA v3 (Invisible) --}}
                    @once
                    <script src="https://www.google.com/recaptcha/api.js?render={{ $form->settings['spam']['captcha']['recaptcha_site_key'] }}"></script>
                    @endonce
                @elseif($captchaType === 'hcaptcha')
                    {{-- hCaptcha Widget --}}
                    <div class="container mt-3">
                        <div class="d-flex justify-content-center">
                            <div class="h-captcha"
                                 data-sitekey="{{ $form->settings['spam']['captcha']['hcaptcha_site_key'] }}"
                                 data-callback="onHcaptchaSuccess{{ $form->id }}"></div>
                        </div>
                    </div>

                    @once
                    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
                    @endonce
                @endif
            @endif

            {{-- Multi-Page Navigation or Single Submit --}}
            <div class="container">
                @if($form->isMultiPage() && count($pages) > 1)
                    <div class="d-flex justify-content-between mt-4">
                        @if(!$this->isFirstPage() && $form->allowBackNavigation())
                            <button type="button" class="btn btn-outline-secondary btn-lg" wire:click="previousPage">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if($this->isLastPage())
                            <button type="submit" class="btn btn-primary btn-lg">
                                Submit Form<i class="bi bi-check-lg ms-2"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-lg" wire:click="nextPage">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        @endif
                    </div>
                @else
                    {{-- Single-page form submit --}}
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Submit Form
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif

    {{-- Analytics Tracker - Works with ALL field types automatically --}}
    @if(!$submitted)
    <script>
        document.addEventListener('livewire:init', () => {
            const component = @this;
            const formElement = document.querySelector('[wire\\:submit="submit"]');

            if (!formElement) return;

            let hasInteracted = false;
            const trackedFields = new Set();

            // Helper to extract field ID from wire:model attribute
            function getFieldIdFromElement(element) {
                const wireModel = element.getAttribute('wire:model') ||
                                 element.getAttribute('wire:model.live') ||
                                 element.getAttribute('wire:model.defer');

                if (!wireModel) return null;

                // Extract field ID from formData.field_X pattern
                const match = wireModel.match(/formData\.field_(\d+)/);
                return match ? parseInt(match[1]) : null;
            }

            // Track form start on first interaction with ANY input
            function trackFirstInteraction(fieldId) {
                if (!hasInteracted && fieldId && component) {
                    hasInteracted = true;
                    component.call('trackFormStart');
                }
            }

            // Add event listeners using event delegation (works for dynamically added fields)
            formElement.addEventListener('focusin', (e) => {
                const target = e.target;
                if (target.matches('input, select, textarea')) {
                    const fieldId = getFieldIdFromElement(target);
                    if (fieldId && component) {
                        trackFirstInteraction(fieldId);
                        component.call('trackFieldEvent', fieldId, 'field_focus');
                    }
                }
            });

            formElement.addEventListener('change', (e) => {
                const target = e.target;
                if (target.matches('input, select, textarea')) {
                    const fieldId = getFieldIdFromElement(target);
                    if (fieldId && component) {
                        trackFirstInteraction(fieldId);
                        component.call('trackFieldEvent', fieldId, 'field_change');
                    }
                }
            });

            // Note: Form abandonment is tracked automatically when analytics sessions
            // remain without a submitted_at timestamp after a period of inactivity
        });
    </script>

    {{-- CAPTCHA JavaScript Handlers --}}
    @if($form->settings['spam']['enabled'] ?? false)
        @php
            $captchaType = $form->settings['spam']['captcha']['type'] ?? 'none';
        @endphp

        @if($captchaType === 'recaptcha')
            {{-- reCAPTCHA v3 Handler --}}
            <script>
                document.addEventListener('livewire:init', () => {
                    const formElement = document.getElementById('form-{{ $form->id }}');

                    formElement.addEventListener('submit', function(e) {
                        // Only prevent default if reCAPTCHA token is not set
                        const recaptchaResponse = @this.get('formData.g-recaptcha-response');

                        if (!recaptchaResponse || recaptchaResponse === '') {
                            e.preventDefault();
                            e.stopImmediatePropagation();

                            grecaptcha.ready(function() {
                                grecaptcha.execute('{{ $form->settings['spam']['captcha']['recaptcha_site_key'] }}', {action: 'submit'})
                                    .then(function(token) {
                                        @this.set('formData.g-recaptcha-response', token);
                                        @this.call('submit');
                                    })
                                    .catch(function(error) {
                                        console.error('reCAPTCHA error:', error);
                                        alert('reCAPTCHA verification failed. Please try again.');
                                    });
                            });
                        }
                    });
                });
            </script>
        @elseif($captchaType === 'hcaptcha')
            {{-- hCaptcha Handler --}}
            <script>
                // Callback function for hCaptcha
                window.onHcaptchaSuccess{{ $form->id }} = function(token) {
                    @this.set('formData.h-captcha-response', token);
                };

                // Reset hCaptcha on Livewire updates
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('morph.updated', ({ el, component }) => {
                        if (window.hcaptcha) {
                            const hcaptchaElement = document.querySelector('.h-captcha');
                            if (hcaptchaElement) {
                                hcaptcha.reset();
                            }
                        }
                    });
                });
            </script>
        @endif
    @endif
    @endif
</div>

{{--
    Success Screen Settings Tab

    Form-level success screen configuration for messages and redirects.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="success-settings">
    {{-- Success Action Type --}}
    <div class="mb-4">
        <label for="successActionType" class="form-label fw-bold">
            Success Action
        </label>
        <select
            class="form-select"
            id="successActionType"
            wire:model.live="successActionType"
        >
            <option value="message">Show Message</option>
            <option value="redirect">Redirect to URL</option>
            <option value="message_then_redirect">Show Message, Then Redirect</option>
        </select>
        <small class="text-muted">
            Choose what happens after successful form submission
        </small>
    </div>

    {{-- Message Settings (shown for 'message' and 'message_then_redirect') --}}
    @if(in_array($successActionType, ['message', 'message_then_redirect']))
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-chat-text me-2"></i>Success Message
                </h6>
            </div>
            <div class="card-body">
                {{-- Message Title --}}
                <div class="mb-3">
                    <label for="messageTitle" class="form-label">
                        Title
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="messageTitle"
                        wire:model="messageTitle"
                        placeholder="Thank you!"
                    >
                </div>

                {{-- Message Body (Quill) --}}
                <div class="mb-3">
                    <label class="form-label">
                        Message Body
                    </label>
                    <div id="successMessageEditor" style="height: 200px; background: white;"></div>
                    <div x-data="{ editorContent: '', quillInstance: null }" x-init="
                        $nextTick(() => {
                          setTimeout(() => {
                            const el = document.getElementById('successMessageEditor');
                            if (typeof Quill !== 'undefined' && el && el.offsetParent !== null) {
                              const quill = new Quill('#successMessageEditor', {
                                theme: 'snow',
                                modules: { toolbar: [['bold', 'italic', 'underline'], ['link'], [{'list': 'ordered'}, {'list': 'bullet'}]] }
                              });
                              quillInstance = quill;
                              const initialContent = $wire.get('messageBody') || '';
                              if (initialContent) { quill.root.innerHTML = initialContent; }
                              editorContent = quill.root.innerHTML;
                              quill.on('text-change', () => { editorContent = quill.root.innerHTML; });
                              $watch('editorContent', value => { $wire.set('messageBody', value, false); });
                            }
                          }, 100);
                        });
                    "></div>
                    <small class="text-muted">
                        Available variables: @{{submission.id}}, @{{submission.created_at}}, @{{field_name}}
                    </small>
                </div>

                {{-- Show Submission Data --}}
                <div class="form-check mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="showSubmissionData"
                        wire:model.live="showSubmissionData"
                    >
                    <label class="form-check-label" for="showSubmissionData">
                        Display submitted data on success screen
                    </label>
                </div>

                @if($showSubmissionData)
                    {{-- Hidden Fields --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Fields to Hide
                        </label>
                        @foreach($this->getFormFields() as $field)
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="hide-field-{{ $field->id }}"
                                    wire:model="hiddenFields"
                                    value="{{ $field->id }}"
                                >
                                <label class="form-check-label" for="hide-field-{{ $field->id }}">
                                    {{ $field->label ?: $field->name }}
                                </label>
                            </div>
                        @endforeach
                        <small class="text-muted">
                            Select sensitive fields to hide (e.g., passwords, credit cards)
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Redirect Settings (shown for 'redirect' and 'message_then_redirect') --}}
    @if(in_array($successActionType, ['redirect', 'message_then_redirect']))
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-arrow-right-circle me-2"></i>Redirect Settings
                </h6>
            </div>
            <div class="card-body">
                {{-- Redirect URL --}}
                <div class="mb-3">
                    <label for="redirectUrl" class="form-label">
                        Redirect URL
                    </label>
                    <input
                        type="url"
                        class="form-control"
                        id="redirectUrl"
                        wire:model="redirectUrl"
                        placeholder="https://example.com/thank-you"
                    >
                    <small class="text-muted">
                        Supports variables: @{{submission.id}}, @{{field_name}}
                    </small>
                </div>

                @if($successActionType === 'message_then_redirect')
                    {{-- Redirect Delay --}}
                    <div class="mb-3">
                        <label for="redirectDelay" class="form-label">
                            Redirect Delay (seconds)
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="redirectDelay"
                            wire:model="redirectDelay"
                            min="1"
                            max="60"
                        >
                        <small class="text-muted">
                            How long to show the message before redirecting (1-60 seconds)
                        </small>
                    </div>
                @endif

                {{-- Pass Submission ID --}}
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="passSubmissionId"
                        wire:model="passSubmissionId"
                    >
                    <label class="form-check-label" for="passSubmissionId">
                        Add submission ID as query parameter (?submission_id=123)
                    </label>
                </div>
            </div>
        </div>

        {{-- Conditional Redirects --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-filter me-2"></i>Conditional Redirects
                    </h6>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        wire:click="addConditionalRedirect"
                    >
                        <i class="bi bi-plus-circle me-1"></i>Add Rule
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(empty($conditionalRedirects))
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        No conditional redirects configured. Redirect to different URLs based on form values.
                    </div>
                @else
                    @foreach($conditionalRedirects as $index => $redirect)
                        <div class="border rounded p-3 mb-3" wire:key="redirect-{{ $index }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">Priority: {{ $redirect['priority'] ?? 1 }}</span>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:click="removeConditionalRedirect({{ $index }})"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            {{-- Redirect URL --}}
                            <div class="mb-2">
                                <label class="form-label small">Redirect URL</label>
                                <input
                                    type="url"
                                    class="form-control form-control-sm"
                                    wire:model="conditionalRedirects.{{ $index }}.url"
                                    placeholder="https://example.com/sales-thank-you"
                                >
                            </div>

                            {{-- Priority --}}
                            <div class="mb-2">
                                <label class="form-label small">Priority (higher = checked first)</label>
                                <input
                                    type="number"
                                    class="form-control form-control-sm"
                                    wire:model="conditionalRedirects.{{ $index }}.priority"
                                    min="1"
                                >
                            </div>

                            {{-- Conditions (simplified - can be enhanced later) --}}
                            <div class="small text-muted">
                                <i class="bi bi-info-circle"></i> Configure conditions via form settings JSON
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    {{-- Download Options --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-download me-2"></i>Download Options
            </h6>
        </div>
        <div class="card-body">
            {{-- PDF Download --}}
            <div class="mb-3">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="enablePdfDownload"
                        wire:model.live="enablePdfDownload"
                    >
                    <label class="form-check-label" for="enablePdfDownload">
                        Enable PDF download
                    </label>
                </div>

                @if($enablePdfDownload)
                    <div class="mt-2">
                        <label for="pdfButtonText" class="form-label small">Button Text</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="pdfButtonText"
                            wire:model="pdfButtonText"
                            placeholder="Download PDF"
                        >
                    </div>
                @endif
            </div>

            {{-- CSV Download --}}
            <div class="mb-3">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="enableCsvDownload"
                        wire:model.live="enableCsvDownload"
                    >
                    <label class="form-check-label" for="enableCsvDownload">
                        Enable CSV download
                    </label>
                </div>

                @if($enableCsvDownload)
                    <div class="mt-2">
                        <label for="csvButtonText" class="form-label small">Button Text</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="csvButtonText"
                            wire:model="csvButtonText"
                            placeholder="Download CSV"
                        >
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit Link Settings --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>Edit Submission Link
            </h6>
        </div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="enableEditLink"
                    wire:model.live="enableEditLink"
                >
                <label class="form-check-label" for="enableEditLink">
                    Show "Edit Your Submission" link
                </label>
            </div>

            @if($enableEditLink)
                <div class="mb-3">
                    <label for="editLinkText" class="form-label">Link Text</label>
                    <input
                        type="text"
                        class="form-control"
                        id="editLinkText"
                        wire:model="editLinkText"
                        placeholder="Edit Your Submission"
                    >
                </div>

                <div class="mb-3">
                    <label for="editLinkExpiration" class="form-label">Link Expiration (hours)</label>
                    <input
                        type="number"
                        class="form-control"
                        id="editLinkExpiration"
                        wire:model="editLinkExpiration"
                        min="1"
                        max="168"
                    >
                    <small class="text-muted">
                        How long the edit link should remain valid (1-168 hours / 7 days)
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- Save Button --}}
    <div class="d-grid">
        <button
            type="button"
            class="btn btn-primary"
            wire:click="saveSuccessSettings"
        >
            <i class="bi bi-save me-1"></i>Save Success Screen Configuration
        </button>
    </div>
</div>

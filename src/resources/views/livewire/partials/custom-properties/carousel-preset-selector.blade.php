{{--
    Carousel Preset Selector - Custom Property Component

    Allows users to select and apply preset configurations to carousels.
    Includes confirmation modal if carousel has existing slides.
--}}

@php
    $presetService = app(\DigitalisStudios\SlickForms\Services\CarouselPresetService::class);
    $presets = $presetService->getPresets();
    $presetOptions = $presetService->getPresetOptions();
    $currentCarousel = $selectedElement ?? null;
    $hasSlides = $currentCarousel && $currentCarousel->children()->count() > 0;
@endphp

<div class="mb-3"
     x-data="{
         showConfirmModal: false,
         selectedPresetKey: '',
         selectedPresetLabel: '',
         slideCount: {{ $hasSlides ? $currentCarousel->children()->count() : 0 }},
         confirmApplyPreset() {
             this.showConfirmModal = false;
             @this.applyCarouselPreset({{ $currentCarousel->id ?? 0 }}, this.selectedPresetKey);
         },
         selectPreset(key, label) {
             if (!key) return;

             this.selectedPresetKey = key;
             this.selectedPresetLabel = label;

             // If carousel has slides, show confirmation modal
             if (this.slideCount > 0) {
                 this.showConfirmModal = true;
             } else {
                 // No slides, apply directly
                 this.confirmApplyPreset();
             }
         }
     }">

    <label class="form-label">
        Preset
    </label>

    <select class="form-select"
            @change="selectPreset($event.target.value, $event.target.options[$event.target.selectedIndex].text); $event.target.value = '';">
        <option value="">Custom (No Preset)</option>
        @foreach($presets as $key => $preset)
            <option value="{{ $key }}">
                {{ $preset['label'] }} ({{ $preset['slideCount'] }} slides)
            </option>
        @endforeach
    </select>

    <div class="form-text">
        <i class="bi-info-circle me-1"></i>
        ⚠️ Applying a preset will replace ALL current settings and slides.
    </div>

    {{-- Confirmation Modal --}}
    <div class="modal fade"
         :class="{ 'show d-block': showConfirmModal }"
         tabindex="-1"
         x-show="showConfirmModal"
         x-cloak
         style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi-exclamation-triangle-fill me-2"></i>
                        Replace Carousel Configuration?
                    </h5>
                    <button type="button" class="btn-close" @click="showConfirmModal = false"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-3">
                        Applying the <strong x-text="selectedPresetLabel"></strong> preset will:
                    </p>

                    <ul class="mb-3">
                        <li>Replace all current carousel settings</li>
                        <li>Delete all existing slides (<strong x-text="slideCount"></strong> slides will be removed)</li>
                        <li>Create new slides based on the preset template</li>
                    </ul>

                    <div class="alert alert-danger mb-0">
                        <i class="bi-exclamation-octagon me-2"></i>
                        <strong>This action cannot be undone.</strong>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            @click="showConfirmModal = false">
                        <i class="bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="button"
                            class="btn btn-warning"
                            @click="confirmApplyPreset()">
                        <i class="bi-check-circle me-1"></i>
                        Apply Preset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

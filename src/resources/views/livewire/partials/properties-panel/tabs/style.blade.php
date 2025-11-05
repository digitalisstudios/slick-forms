{{-- SECTION: Bootstrap Utilities --}}
<div class="mb-4">
    <h6 class="text-uppercase text-muted small fw-bold mb-3">
        <i class="bi bi-palette me-1"></i> Bootstrap Utilities
    </h6>

    {{-- Text Alignment --}}
    <div class="mb-3">
        <label for="fieldTextAlign" class="form-label small fw-bold">Text Alignment</label>
        <select class="form-select form-select-sm" id="fieldTextAlign" wire:model="properties.text_alignment.align">
            <option value="">Default</option>
            <option value="start">Start (Left)</option>
            <option value="center">Center</option>
            <option value="end">End (Right)</option>
        </select>
        <div class="form-text small">Align text within the field</div>
    </div>

    {{-- Spacing Utilities --}}
    <div class="mb-3">
        <label class="form-label small fw-bold">Spacing</label>
        <div class="row g-2">
            {{-- Margin --}}
            <div class="col-6">
                <label for="fieldSpacing_margin_top" class="form-label small">Margin Top</label>
                <select class="form-select form-select-sm" id="fieldSpacing_margin_top" wire:model="properties.spacing.margin_top">
                    <option value="">None</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="auto">Auto</option>
                </select>
            </div>
            <div class="col-6">
                <label for="fieldSpacing_margin_bottom" class="form-label small">Margin Bottom</label>
                <select class="form-select form-select-sm" id="fieldSpacing_margin_bottom" wire:model="properties.spacing.margin_bottom">
                    <option value="">None</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="auto">Auto</option>
                </select>
            </div>
            {{-- Padding --}}
            <div class="col-6">
                <label for="fieldSpacing_padding_top" class="form-label small">Padding Top</label>
                <select class="form-select form-select-sm" id="fieldSpacing_padding_top" wire:model="properties.spacing.padding_top">
                    <option value="">None</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="col-6">
                <label for="fieldSpacing_padding_bottom" class="form-label small">Padding Bottom</label>
                <select class="form-select form-select-sm" id="fieldSpacing_padding_bottom" wire:model="properties.spacing.padding_bottom">
                    <option value="">None</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
        </div>
        <div class="form-text small mt-2">Quick spacing controls (Bootstrap spacing scale: 0-5)</div>
    </div>
</div>


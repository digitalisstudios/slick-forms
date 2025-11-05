{{--
    Card Component - Builder View
    Variables: $element, $children (slot content)
--}}
<div class="{{ $element->getCardClass() }}">
    @if($element->hasCardHeader())
        <div class="card-header">
            {{ $element->getCardHeaderText() }}
        </div>
    @endif

    <div class="card-body">
        @if($element->getCardTitle())
            <h5 class="card-title">{{ $element->getCardTitle() }}</h5>
        @endif

        @if($element->getCardSubtitle())
            <h6 class="card-subtitle mb-2 text-muted">{{ $element->getCardSubtitle() }}</h6>
        @endif

        <div class="text-muted small mb-2">
            <i class="bi bi-card-text me-1"></i>
            Card
            @if(!empty($element->settings['card_background']))
                <span class="badge bg-{{ $element->settings['card_background'] }} ms-1">
                    {{ ucfirst($element->settings['card_background']) }}
                </span>
            @endif
        </div>

        {{ $children }}
    </div>

    @if($element->hasCardFooter())
        <div class="card-footer text-muted">
            {{ $element->getCardFooterText() }}
        </div>
    @endif
</div>

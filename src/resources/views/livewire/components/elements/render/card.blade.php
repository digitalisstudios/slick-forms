{{--
    Card Component - Render View
    Enhanced card with header/footer/title/subtitle
    Variables: $node, $registry, $formData, $visibleFieldIds, $visibleElementIds, $getElementAttributes
--}}
@php
    $attrs = $getElementAttributes($node['data']);
@endphp

<div class="{{ $node['data']->getCardClass() }} mb-3 {{ $attrs['class'] }}"
     @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>

    @if($node['data']->hasCardHeader())
        <div class="card-header">
            {{ $node['data']->getCardHeaderText() }}
        </div>
    @endif

    <div class="card-body">
        @if($node['data']->getCardTitle())
            <h5 class="card-title">{{ $node['data']->getCardTitle() }}</h5>
        @endif

        @if($node['data']->getCardSubtitle())
            <h6 class="card-subtitle mb-2 text-muted">{{ $node['data']->getCardSubtitle() }}</h6>
        @endif

        @foreach($node['children'] as $child)
            @include('slick-forms::livewire.partials.render-element', [
                'node' => $child,
                'registry' => $registry,
                'formData' => $formData,
                'visibleFieldIds' => $visibleFieldIds ?? [],
                'visibleElementIds' => $visibleElementIds ?? [],
                'getElementAttributes' => $getElementAttributes
            ])
        @endforeach
    </div>

    @if($node['data']->hasCardFooter())
        <div class="card-footer text-muted">
            {{ $node['data']->getCardFooterText() }}
        </div>
    @endif
</div>

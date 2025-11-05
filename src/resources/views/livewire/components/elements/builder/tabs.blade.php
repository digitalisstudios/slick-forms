{{--
    Tabs Component - Builder View
    Variables: $element, $children (array of child nodes), $registry
--}}
<div>
    <div class="text-muted small mb-2">
        <i class="bi bi-window me-1"></i>
        Tabs
        @if(!empty($element->settings['tab_style']) && $element->settings['tab_style'] !== 'nav-tabs')
            <span class="badge bg-secondary ms-1">{{ ucfirst(str_replace('nav-', '', $element->settings['tab_style'])) }}</span>
        @endif
    </div>

    {{-- Tab Navigation --}}
    <ul class="{{ $element->getTabsClass() }}" role="tablist">
        @foreach($children as $index => $child)
            @if($child['type'] === 'element' && $child['element_type'] === 'tab')
                <li class="nav-item">
                    <button class="nav-link {{ $index === $element->getDefaultActiveTab() ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-builder-{{ $element->id }}-{{ $index }}"
                            type="button">
                        @if($child['data']->getTabIcon())
                            <i class="{{ $child['data']->getTabIcon() }} me-1"></i>
                        @endif
                        {{ $child['data']->getTabLabel() }}
                    </button>
                </li>
            @endif
        @endforeach
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content pt-3">
        @foreach($children as $index => $child)
            @if($child['type'] === 'element' && $child['element_type'] === 'tab')
                <div class="tab-pane {{ $element->hasFadeAnimation() ? 'fade' : '' }} {{ $index === $element->getDefaultActiveTab() ? 'show active' : '' }}"
                     id="tab-builder-{{ $element->id }}-{{ $index }}"
                     style="min-height: 40px;">
                    {{-- Children will be rendered by parent --}}
                </div>
            @endif
        @endforeach
    </div>
</div>

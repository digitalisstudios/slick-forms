@assets
    {{-- FormBuilder Core Styles --}}
    <link rel="stylesheet" href="{{ asset('vendor/slick-forms/css/form-builder.css') }}">

    {{-- Mode-specific Styles (Preview & Picker) - Always loaded for instant mode switching --}}
    <link rel="stylesheet" href="{{ asset('vendor/slick-forms/css/form-builder-modes.css') }}">

    {{-- Third-Party Library Customizations --}}
    <link rel="stylesheet" href="{{ asset('vendor/slick-forms/css/form-builder-third-party.css') }}">

    {{-- Lazy Loader - Loads external libraries on-demand --}}
    <script src="{{ asset('vendor/slick-forms/js/lazy-loader.js') }}"></script>

    {{-- Alpine Component Registration --}}
    <script src="{{ asset('vendor/slick-forms/js/form-builder-alpine.js') }}"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            // Debounce utility for performance optimization
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Global mouse event handlers for resizing properties panel
            let resizeState = null;

            document.addEventListener('mousemove', (e) => {
                if (resizeState) {
                    const delta = resizeState.startX - e.clientX;
                    const newWidth = resizeState.startWidth + delta;
                    const minWidth = 300;
                    const maxWidth = window.innerWidth * 0.5; // 50% of viewport width
                    const constrainedWidth = Math.max(minWidth, Math.min(maxWidth, newWidth));

                    resizeState.panel.style.width = constrainedWidth + 'px';
                    resizeState.width = constrainedWidth;

                    // Save to localStorage
                    localStorage.setItem('properties-panel-width', constrainedWidth);

                    // Dispatch event for Alpine to update canvas margin
                    window.dispatchEvent(new CustomEvent('properties-panel-resized', {
                        detail: { width: constrainedWidth }
                    }));
                }
            });

            document.addEventListener('mouseup', () => {
                if (resizeState) {
                    resizeState.handle.classList.remove('resizing');
                    document.body.style.cursor = '';
                    document.body.style.userSelect = '';
                    resizeState = null;
                }
            });

            // Attach resize handler using event delegation
            document.addEventListener('mousedown', (e) => {
                if (e.target.closest('.properties-resize-handle')) {
                    e.preventDefault();
                    const handle = e.target.closest('.properties-resize-handle');
                    const panel = e.target.closest('.builder-properties');

                    if (panel) {
                        resizeState = {
                            handle: handle,
                            panel: panel,
                            startX: e.clientX,
                            startWidth: panel.offsetWidth,
                            width: panel.offsetWidth
                        };

                        handle.classList.add('resizing');
                        document.body.style.cursor = 'col-resize';
                        document.body.style.userSelect = 'none';
                    }
                }
            });

            // Initialize Bootstrap popovers for help text icons
            function initPopovers() {
                const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                [...popoverTriggerList].forEach(popoverTriggerEl => {
                    // Dispose existing popover instance to prevent duplicates
                    const existingPopover = bootstrap.Popover.getInstance(popoverTriggerEl);
                    if (existingPopover) {
                        existingPopover.dispose();
                    }
                    // Create new popover
                    new bootstrap.Popover(popoverTriggerEl);
                });
            }

            // Restore properties panel width from localStorage
            function restorePanelWidth() {
                const panel = document.querySelector('.builder-properties');
                if (panel) {
                    const savedWidth = localStorage.getItem('properties-panel-width');
                    if (savedWidth) {
                        panel.style.width = savedWidth + 'px';
                    }
                }
            }

            // Create debounced versions of expensive operations
            const debouncedInitPopovers = debounce(initPopovers, 100);
            const debouncedRestorePanelWidth = debounce(restorePanelWidth, 100);

            // Initialize on page load
            initPopovers();
            restorePanelWidth();

            // Get spinner element
            function getSpinner() {
                return document.getElementById('properties-spinner');
            }

            // Show spinner when Livewire methods are called
            Livewire.hook('commit', ({ component, commit }) => {
                // Check if any of the property editing methods are being called
                const editMethods = ['editField', 'editElement', 'editTableCell'];
                const isEditingProperties = commit.calls.some(call => editMethods.includes(call.method));

                if (isEditingProperties) {
                    const spinner = getSpinner();
                    if (spinner) {
                        spinner.classList.add('show');
                    }
                }
            });

            // Debounced icon picker initialization to prevent multiple rapid calls
            let iconPickerInitTimer = null;
            function scheduleIconPickerInit() {
                if (iconPickerInitTimer) {
                    clearTimeout(iconPickerInitTimer);
                }
                iconPickerInitTimer = setTimeout(() => {
                    initIconPicker();
                    iconPickerInitTimer = null;
                }, 100);
            }

            // Initialize Custom Vanilla JS Icon Pickers (for both page icons and field icons)
            function initIconPicker() {
                // Find all icon picker buttons (page icon + field label icons)
                const iconPickerBtns = document.querySelectorAll('.icon-picker-btn, #pageIconPicker');

                iconPickerBtns.forEach(iconPickerBtn => {
                    // Prevent duplicate initialization
                    if (iconPickerBtn._iconPickerInitialized) return;
                    iconPickerBtn._iconPickerInitialized = true;

                    // Find the associated input (sibling or specific ID for page picker)
                    let iconInput;
                    if (iconPickerBtn.id === 'pageIconPicker') {
                        iconInput = document.getElementById('pageIconInput');
                    } else {
                        iconInput = iconPickerBtn.parentElement.querySelector('.icon-picker-input');
                    }

                    if (!iconInput) return;

                    const wireModel = iconPickerBtn.dataset.wireModel || null;
                    initSingleIconPicker(iconPickerBtn, iconInput, wireModel);
                });
            }

            // Initialize a single icon picker instance
            function initSingleIconPicker(iconPickerBtn, iconInput, wireModel) {
                @verbatim
                // Popular Bootstrap Icons (subset for performance)
                const popularIcons = [
                    'bi-file-text', 'bi-calendar-event', 'bi-person', 'bi-people', 'bi-envelope',
                    'bi-telephone', 'bi-house', 'bi-building', 'bi-gear', 'bi-cart',
                    'bi-credit-card', 'bi-cash', 'bi-clock', 'bi-calendar', 'bi-map',
                    'bi-geo-alt', 'bi-briefcase', 'bi-journal', 'bi-book', 'bi-bookmark',
                    'bi-heart', 'bi-star', 'bi-flag', 'bi-tag', 'bi-tags',
                    'bi-box', 'bi-truck', 'bi-basket', 'bi-bag', 'bi-gift',
                    'bi-clipboard', 'bi-list', 'bi-check-circle', 'bi-x-circle', 'bi-exclamation-circle',
                    'bi-info-circle', 'bi-question-circle', 'bi-bell', 'bi-chat', 'bi-megaphone',
                    'bi-camera', 'bi-image', 'bi-file-earmark', 'bi-folder', 'bi-download',
                    'bi-upload', 'bi-cloud', 'bi-printer', 'bi-display', 'bi-phone',
                    'bi-laptop', 'bi-tablet', 'bi-wifi', 'bi-bluetooth', 'bi-battery',
                    'bi-search', 'bi-filter', 'bi-pencil', 'bi-trash', 'bi-plus',
                    'bi-dash', 'bi-arrow-up', 'bi-arrow-down', 'bi-arrow-left', 'bi-arrow-right',
                    'bi-shield', 'bi-lock', 'bi-unlock', 'bi-key', 'bi-eye',
                    'bi-eye-slash', 'bi-lightbulb', 'bi-sun', 'bi-moon', 'bi-palette'
                ];

                // Get current icon, handle 'None' as empty
                let currentInputValue = iconInput?.value || '';
                let selectedIcon = (currentInputValue === 'None' || !currentInputValue) ? '' : currentInputValue;
                let dropdown = null;

                // Create dropdown
                function createDropdown() {
                    dropdown = document.createElement('div');
                    dropdown.className = 'icon-picker-dropdown';
                    dropdown.innerHTML = `
                        <div class="icon-picker-search">
                            <div class="d-flex gap-2 align-items-center">
                                <input type="search" class="form-control form-control-sm flex-grow-1" placeholder="Search icons..." id="iconSearchInput">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="iconClearBtn" title="Remove icon">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="icon-picker-grid" id="iconGrid"></div>
                    `;
                    document.body.appendChild(dropdown);

                    // Position dropdown below button
                    const rect = iconPickerBtn.getBoundingClientRect();
                    dropdown.style.top = (rect.bottom + window.scrollY + 5) + 'px';
                    dropdown.style.left = rect.left + 'px';

                    // Populate grid
                    renderIcons(popularIcons);

                    // Search functionality
                    const searchInput = dropdown.querySelector('#iconSearchInput');
                    searchInput.addEventListener('input', (e) => {
                        const query = e.target.value.toLowerCase();
                        const filtered = popularIcons.filter(icon => icon.includes(query));
                        renderIcons(filtered);
                    });

                    // Clear button functionality
                    const clearBtn = dropdown.querySelector('#iconClearBtn');
                    clearBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        selectIcon(''); // Clear the icon
                    });

                    // Close on outside click
                    setTimeout(() => {
                        document.addEventListener('click', closeOnOutsideClick);
                    }, 10);
                }

                function renderIcons(icons) {
                    const grid = dropdown.querySelector('#iconGrid');
                    grid.innerHTML = icons.map(icon => `
                        <div class="icon-picker-item ${icon === selectedIcon ? 'selected' : ''}" data-icon="${icon}">
                            <i class="${icon}"></i>
                        </div>
                    `).join('');
                @endverbatim

                    // Add click handlers
                    grid.querySelectorAll('.icon-picker-item').forEach(item => {
                        item.addEventListener('click', () => selectIcon(item.dataset.icon));
                    });
                }

                function selectIcon(icon) {
                    @verbatim
                    selectedIcon = icon;
                    if (iconInput) {
                        iconInput.value = icon || 'None';
                    }

                    // Update button display
                    if (icon) {
                        iconPickerBtn.innerHTML = `<i class="${icon}"></i>`;
                    } else {
                        iconPickerBtn.innerHTML = '<span class="text-muted">—</span>';
                    }
                    @endverbatim

                    // Update Livewire - handle both page icons and field icons
                    if (wireModel) {
                        @this.set(wireModel, icon || '');
                    } else if (iconPickerBtn.id === 'pageIconPicker') {
                        @this.set('pageIcon', icon || '');
                    }

                    @verbatim
                    closeDropdown();
                    @endverbatim
                }

                @verbatim
                function closeDropdown() {
                    if (dropdown) {
                        dropdown.remove();
                        dropdown = null;
                        document.removeEventListener('click', closeOnOutsideClick);
                    }
                }

                function closeOnOutsideClick(e) {
                    if (dropdown && !dropdown.contains(e.target) && e.target !== iconPickerBtn) {
                        closeDropdown();
                    }
                }

                // Toggle dropdown on button click
                iconPickerBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (dropdown) {
                        closeDropdown();
                    } else {
                        createDropdown();
                    }
                });

                    // Open picker when input field is focused
                    if (iconInput) {
                        iconInput.addEventListener('focus', (e) => {
                            e.preventDefault();
                            if (!dropdown) {
                                createDropdown();
                            }
                        });

                        // Also trigger on click
                        iconInput.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (!dropdown) {
                                createDropdown();
                            }
                        });
                    }
                @endverbatim
            }

            // Single consolidated hook for Livewire updates (replaces 3 separate hooks)
            Livewire.hook('morph.updated', ({el, component}) => {
                // Use debounced versions for better performance
                debouncedInitPopovers();
                // Note: Panel width restoration handled by Alpine x-init, not here

                // Check if properties panel or icon picker was updated
                if (el && (
                    el.querySelector('.icon-picker-btn') ||
                    el.querySelector('#pageIconPicker') ||
                    el.classList?.contains('icon-picker-btn') ||
                    el.id === 'pageIconPicker'
                )) {
                    scheduleIconPickerInit();
                }

                // Hide spinner after properties panel updates
                const spinner = getSpinner();
                if (spinner) {
                    spinner.classList.remove('show');
                }
            });

            Livewire.on('scroll-to-field', (event) => {
                const fieldId = event.fieldId;
                setTimeout(() => {
                    const element = document.querySelector(`[data-field-id="${fieldId}"]`);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        // Add highlight animation
                        element.classList.add('field-highlight');

                        // Remove the class after animation completes
                        setTimeout(() => {
                            element.classList.remove('field-highlight');
                        }, 2000);
                    }
                }, 100);
            });

            Livewire.on('scroll-to-element', (event) => {
                const elementId = event.elementId;
                setTimeout(() => {
                    const element = document.querySelector(`[data-element-id="${elementId}"]`);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            });

            // Lazy load external libraries when field editor opens or properties panel updates
            let editFieldTriggered = false;

            // Detect when editField is called or properties panel updates
            Livewire.hook('commit', ({ component, commit }) => {
                if (component.name === 'slick-forms::form-builder') {
                    const isEditingField = commit.calls.some(call => call.method === 'editField');
                    const isUpdatingProperty = commit.calls.some(call => call.method === 'updateFieldProperty');
                    const isAddingField = commit.calls.some(call => call.method === 'addField');

                    if (isEditingField || isUpdatingProperty || isAddingField) {
                        editFieldTriggered = true;
                    }
                }
            });

            // Load libraries after DOM is updated - poll for editor containers
            Livewire.hook('morph.updated', ({ component }) => {
                if (editFieldTriggered && component.name === 'slick-forms::form-builder') {
                    editFieldTriggered = false; // Reset flag

                    // Poll for editor containers (properties panel loads asynchronously)
                    let pollAttempts = 0;
                    const maxAttempts = 20; // Poll for up to 2 seconds (20 * 100ms)

                    const pollForEditors = () => {
                        pollAttempts++;
                        let foundEditor = false;

                        // Check DOM for Quill editor containers
                        const quillEditors = document.querySelectorAll('[id^="quill_"]');
                        if (quillEditors.length > 0 && typeof Quill === 'undefined') {
                            if (typeof window.loadQuill === 'function') {
                                window.loadQuill()
                                    .then(() => {
                                        window.dispatchEvent(new CustomEvent('quill-loaded'));
                                    })
                                    .catch(err => console.error('Failed to load Quill:', err));
                            }
                            foundEditor = true;
                        }

                        // Check DOM for Ace editor containers
                        const aceEditors = document.querySelectorAll('[id^="ace_"]');
                        if (aceEditors.length > 0 && typeof ace === 'undefined') {
                            if (typeof window.loadAce === 'function') {
                                window.loadAce()
                                    .then(() => {
                                        window.dispatchEvent(new CustomEvent('ace-loaded'));
                                    })
                                    .catch(err => console.error('Failed to load Ace:', err));
                            }
                            foundEditor = true;
                        }

                        // Stop polling if we found an editor, otherwise continue
                        if (!foundEditor && pollAttempts < maxAttempts) {
                            setTimeout(pollForEditors, 100);
                        }
                    };

                    // Start polling
                    pollForEditors();
                }
            });
        });
    </script>
@endassets

<div class="slick-forms-builder-wrapper"
     data-preview-mode="{{ $previewMode ? 'true' : 'false' }}"
     data-picker-mode="{{ $pickerMode ? 'true' : 'false' }}"
     style="position: relative; height: 100%; overflow: hidden; transform: translateZ(0); display: flex; flex-direction: column;">

    {{-- Full Screen Loading Spinner --}}
    <div id="properties-spinner"
         class="properties-loading-overlay"
         wire:loading.class="show"
         wire:target="activePropertiesTab"
         style="position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                backdrop-filter: blur(2px);
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-out, visibility 0s linear 0.3s;">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading properties...</span>
        </div>
    </div>

    {{-- Builder Header Bar (inside wrapper for CSS containing block) --}}
    @if(!$settingsOnly)
    <div class="builder-internal-header" wire:key="builder-header">
        <div class="header-container">
            <div class="d-flex align-items-center gap-3 w-100">
	            {{-- Exit Button --}}
	            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm" title="Exit Builder">
	                <i class="bi bi-arrow-left"></i> Exit
	            </a>

	            {{-- Form Name (Editable) --}}
	            <div class="flex-grow-1">
	                <input
	                    type="text"
	                    wire:model.blur="formName"
	                    class="form-control form-control-lg border-0 form-name-input"
	                    style="font-size: 1.125rem; font-weight: 600; padding: 0.25rem 0.5rem; background: transparent;"
	                    placeholder="Form Name"
	                >
	            </div>

	            {{-- Actions --}}
	            <div class="d-flex gap-3 align-items-center">
	                {{-- Multi-Page Mode Toggle --}}
	                <div class="form-check form-switch mb-0">
	                    <input
	                        class="form-check-input"
	                        type="checkbox"
	                        role="switch"
	                        id="multiPageToggle"
	                        wire:click="toggleMultiPageMode"
	                        @checked($form->isMultiPage())
	                        style="cursor: pointer;"
	                    >
	                    <label class="form-check-label" for="multiPageToggle" style="cursor: pointer;">
	                        Multi-Page
	                    </label>
	                </div>

	                {{-- Preview Mode Toggle --}}
	                <div class="form-check form-switch mb-0">
	                    <input
	                        class="form-check-input"
	                        type="checkbox"
	                        role="switch"
	                        id="previewModeToggle"
	                        wire:model.live="previewMode"
	                        style="cursor: pointer;"
	                    >
	                    <label class="form-check-label" for="previewModeToggle" style="cursor: pointer;">
	                        Preview
	                    </label>
	                </div>

	                {{-- Viewport Mode Switcher --}}
	                <div class="btn-group" role="group" aria-label="Viewport mode">
	                    <button
	                        type="button"
	                        wire:click="$set('viewportMode', 'mobile')"
	                        class="btn btn-sm {{ $viewportMode === 'mobile' ? 'btn-primary' : 'btn-outline-secondary' }}"
	                        title="Mobile View (375px)"
	                    >
	                        <i class="bi bi-phone"></i>
	                    </button>
	                    <button
	                        type="button"
	                        wire:click="$set('viewportMode', 'tablet')"
	                        class="btn btn-sm {{ $viewportMode === 'tablet' ? 'btn-primary' : 'btn-outline-secondary' }}"
	                        title="Tablet View (768px)"
	                    >
	                        <i class="bi bi-tablet"></i>
	                    </button>
	                    <button
	                        type="button"
	                        wire:click="$set('viewportMode', 'desktop')"
	                        class="btn btn-sm {{ $viewportMode === 'desktop' ? 'btn-primary' : 'btn-outline-secondary' }}"
	                        title="Desktop View (100%)"
	                    >
	                        <i class="bi bi-display"></i>
	                    </button>
	                </div>

	                {{-- Action Buttons Group --}}
	                <div class="btn-group" role="group" aria-label="Form actions">
	                    {{-- View Form --}}
	                    <a href="{{ route('slick-forms.form.show.hash', ['hash' => app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class)->encodeId($form->id)]) }}" class="btn btn-sm btn-outline-success" title="View Form" target="_blank">
	                        <i class="bi bi-eye"></i> View
	                    </a>

	                    {{-- Submissions --}}
	                    <a href="{{ route('slick-forms.submissions.show', $form) }}" class="btn btn-sm btn-outline-info" title="View Submissions" target="_blank">
	                        <i class="bi bi-list-check"></i> Submissions
	                    </a>

	                    {{-- Analytics --}}
	                    <a href="{{ route('slick-forms.analytics.show', $form) }}" class="btn btn-sm btn-outline-primary" title="View Analytics">
	                        <i class="bi bi-graph-up"></i> Analytics
	                    </a>

	                    {{-- More Actions Dropdown --}}
	                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
	                        <i class="bi bi-gear"></i>
	                    </button>
	                    <ul class="dropdown-menu dropdown-menu-end">
	                        <li>
	                            <button type="button" class="dropdown-item" wire:click="showFormSettings">
	                                <i class="bi bi-gear me-2"></i>Settings
	                            </button>
	                        </li>
	                        @if(slick_forms_feature_enabled('versioning'))
	                            <li>
	                                <button type="button" class="dropdown-item" wire:click="toggleVersionHistory">
	                                    <i class="bi bi-clock-history me-2"></i>Version History
	                                </button>
	                            </li>
	                        @endif
	                        <li><hr class="dropdown-divider"></li>
	                        <li>
	                            <form action="{{ route('slick-forms.forms.duplicate', $form) }}" method="POST">
	                                @csrf
	                                <button type="submit" class="dropdown-item">
	                                    <i class="bi bi-files me-2"></i>Duplicate
	                                </button>
	                            </form>
	                        </li>
	                        <li>
	                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#templateModal{{ $form->id }}">
	                                <i class="bi bi-star me-2"></i>Make Template
	                            </button>
	                        </li>
	                        <li>
	                            <form action="{{ route('slick-forms.forms.toggle-active', $form) }}" method="POST">
	                                @csrf
	                                <button type="submit" class="dropdown-item">
	                                    @if($form->is_active)
	                                        <i class="bi bi-pause-circle me-2"></i>Disable
	                                    @else
	                                        <i class="bi bi-play-circle me-2"></i>Enable
	                                    @endif
	                                </button>
	                            </form>
	                        </li>
	                        <li><hr class="dropdown-divider"></li>
	                        <li>
	                            <form action="{{ route('slick-forms.manage.destroy', $form) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this form? This action cannot be undone.')">
	                                @csrf
	                                @method('DELETE')
	                                <button type="submit" class="dropdown-item text-danger">
	                                    <i class="bi bi-trash me-2"></i>Delete
	                                </button>
	                            </form>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
    </div>
</div>
    @endif

{{-- Fixed 3-Panel Layout with Independent Scrolling --}}
    @if(!$settingsOnly)
    <div wire:key="builder-layout"
         x-data="{
            ...formBuilder(),
            showFieldEditor: @entangle('showFieldEditor'),
            showElementEditor: @entangle('showElementEditor'),
            showPageEditor: @entangle('showPageEditor'),
            showFormEditor: @entangle('showFormEditor'),
            selectedTableCellId: @entangle('selectedTableCellId'),
            propertiesOpen: false,
            propertiesOpenDebounce: null,
            updatePropertiesOpen() {
                clearTimeout(this.propertiesOpenDebounce);
                const shouldBeOpen = this.showFieldEditor || this.showElementEditor || this.showPageEditor || this.showFormEditor || this.selectedTableCellId;

                if (shouldBeOpen) {
                    this.propertiesOpen = true;
                } else {
                    this.propertiesOpenDebounce = setTimeout(() => {
                        this.propertiesOpen = false;
                    }, 150);
                }
            }
         }"
         x-init="
            init();
            $watch('showFieldEditor', () => updatePropertiesOpen());
            $watch('showElementEditor', () => updatePropertiesOpen());
            $watch('showPageEditor', () => updatePropertiesOpen());
            $watch('showFormEditor', () => updatePropertiesOpen());
            $watch('selectedTableCellId', () => updatePropertiesOpen());
            updatePropertiesOpen();
         "
         class="builder-layout">

        {{-- Left Sidebar (Field Palette) --}}
        <div class="builder-sidebar" :class="{ 'collapsed': sidebarCollapsed }">
            @include('slick-forms::livewire.partials.builder-sidebar')
        </div>

        {{-- Canvas (Center) --}}
        <div class="builder-canvas"
             :class="{ 'sidebar-collapsed': sidebarCollapsed }"
             :style="propertiesOpen ? `right: ${propertiesPanelWidth}px` : ''">

            {{-- Viewport Wrapper for Responsive Preview --}}
            <div class="viewport-wrapper pt-3 viewport-{{ $viewportMode }}">

            {{-- Picker Mode Banner --}}
            @if($pickerMode)
                <div class="alert alert-success d-flex justify-content-between align-items-center mb-3" role="alert">
                    <div>
                        <i class="bi bi-eyedropper me-2"></i>
                        <strong>Field Picker Active:</strong> Click on any field in the canvas to select it
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-success"
                        wire:click="cancelPicker"
                    >
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            @endif

            {{-- Canvas Content --}}
            <div class="card">
                {{-- Multi-Page Tabs --}}
                @if($form->isMultiPage() && count($pages) > 0)
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <ul class="nav nav-tabs card-header-tabs flex-grow-1 mb-0"
                                role="tablist"
                                id="pages-list"
                                x-data="{
                                    sortableInstance: null,
                                    initPageSortable() {
                                        if (typeof Sortable !== 'undefined') {
                                            // Destroy existing instance to prevent memory leaks
                                            if (this.sortableInstance) {
                                                this.sortableInstance.destroy();
                                            }
                                            // Create and store new instance
                                            this.sortableInstance = new Sortable(this.$el, {
                                                animation: 150,
                                                handle: '.page-drag-handle',
                                                filter: '.btn, button:not(.page-drag-handle)',
                                                preventOnFilter: false,
                                                onEnd: (evt) => {
                                                    const pageIds = Array.from(this.$el.children).map(li => {
                                                        return parseInt(li.dataset.pageId);
                                                    });
                                                    @this.call('reorderPages', pageIds);
                                                }
                                            });
                                        }
                                    }
                                }"
                                x-init="initPageSortable()"
                                x-cleanup="sortableInstance?.destroy()"
                                @foreach($pages as $page)
                                    <li class="nav-item" role="presentation" data-page-id="{{ $page['id'] }}">
                                        <div class="nav-link {{ $currentPageId == $page['id'] ? 'active' : '' }} d-flex align-items-center gap-2 pe-2">
                                            <span
                                                class="page-drag-handle text-secondary"
                                                style="cursor: grab; user-select: none; display: inline-flex; align-items: center;"
                                                title="Drag to reorder"
                                            >
                                                <i class="bi bi-grip-vertical"></i>
                                            </span>
                                            <button
                                                class="btn btn-link text-decoration-none p-0 flex-grow-1 text-start"
                                                type="button"
                                                wire:click="selectPage({{ $page['id'] }})"
                                                style="color: inherit;"
                                            >
                                                @if($page['icon'])
                                                    <i class="{{ $page['icon'] }} me-1"></i>
                                                @endif
                                                {{ $page['title'] }}
                                            </button>
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-sm btn-link text-secondary p-0"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    style="line-height: 1;"
                                                >
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button
                                                            class="dropdown-item"
                                                            type="button"
                                                            wire:click.prevent="editPage({{ $page['id'] }})"
                                                        >
                                                            <i class="bi bi-pencil me-2"></i>
                                                            Edit Page
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button
                                                            class="dropdown-item text-danger"
                                                            type="button"
                                                            wire:click.prevent="deletePage({{ $page['id'] }})"
                                                            wire:confirm="Are you sure you want to delete this page? All fields and elements on this page will be moved to the first page."
                                                        >
                                                            <i class="bi bi-trash me-2"></i>
                                                            Delete Page
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="ms-3">
                                <button class="btn btn-sm btn-primary" wire:click="addPage" type="button">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Add Page
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-body" style="min-height: 500px;">
                    @if(empty($formStructure))
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-plus-circle" style="font-size: 3rem;"></i>
                            <p class="mt-3">No elements added yet. Click a layout element or field type on the left to get started.</p>
                        </div>
                    @else

                        <div id="form-canvas"
                             class="sortable-container"
                             data-parent-element-id="null"
                             wire:click="selectElement(null)"
                             x-data="{
                                 sortableInstance: null,
                                 initSortable() {
                                     if (typeof Sortable !== 'undefined') {
                                         // Destroy existing instance to prevent memory leaks
                                         if (this.sortableInstance) {
                                             this.sortableInstance.destroy();
                                         }
                                         // Create and store new instance
                                         this.sortableInstance = new Sortable(this.$el, {
                                             animation: 150,
                                             handle: '.drag-handle',
                                             group: 'shared',
                                             filter: '.placeholder-text, .btn, button',
                                             onStart: (evt) => {
                                                 document.getElementById('form-canvas').classList.add('drag-active');
                                             },
                                             onAdd: (evt) => {
                                                 // Skip if already handled by a nested container (e.g., repeater)
                                                 if (evt.item.dataset.handledBy) {
                                                     evt.item.remove(); // Clean up the item since it was handled elsewhere
                                                     return;
                                                 }

                                                 // Check if this is a new item from the palette
                                                 if (evt.item.dataset.type === 'new-field') {
                                                     const fieldType = evt.item.dataset.fieldType;
                                                     const index = evt.newIndex;
                                                     evt.item.remove(); // Remove the cloned placeholder
                                                     @this.call('addField', fieldType, null, false, index);
                                                     return;
                                                 }
                                                 if (evt.item.dataset.type === 'new-element') {
                                                     const elementType = evt.item.dataset.elementType;
                                                     const index = evt.newIndex;
                                                     evt.item.remove(); // Remove the cloned placeholder
                                                     @this.call('addLayoutElement', elementType, null, {}, index);
                                                     return;
                                                 }

                                                 // Collect all children (fields and elements) in their current order
                                                 let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                                                 let orderedItems = items.map(el => {
                                                     if (el.dataset.fieldId) {
                                                         return { type: 'field', id: parseInt(el.dataset.fieldId) };
                                                     } else if (el.dataset.elementId) {
                                                         return { type: 'element', id: parseInt(el.dataset.elementId) };
                                                     }
                                                 }).filter(item => item !== undefined);

                                                 if (orderedItems.length > 0) {
                                                     @this.call('updateChildrenOrderInParent', null, orderedItems);
                                                 }
                                             },
                                             onEnd: (evt) => {
                                                 document.getElementById('form-canvas').classList.remove('drag-active');

                                                 if (evt.to !== this.$el) {
                                                     return;
                                                 }

                                                 let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                                                 let orderedItems = items.map(el => {
                                                     if (el.dataset.fieldId) {
                                                         return { type: 'field', id: parseInt(el.dataset.fieldId) };
                                                     } else if (el.dataset.elementId) {
                                                         return { type: 'element', id: parseInt(el.dataset.elementId) };
                                                     }
                                                 }).filter(item => item !== undefined);

                                                 if (orderedItems.length > 0) {
                                                     @this.call('updateChildrenOrderInParent', null, orderedItems);
                                                 }
                                             }
                                         });
                                     }
                                 }
                             }"
                             x-init="initSortable()"
                             x-cleanup="sortableInstance?.destroy()">
                            @foreach($formStructure as $node)
                                @include('slick-forms::livewire.partials.builder-element', ['node' => $node, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            </div>{{-- End Viewport Wrapper --}}
        </div>

        {{-- Right Panel (Properties - Slideout) --}}
        <div class="builder-properties"
             wire:ignore.self
             x-show="propertiesOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform translate-x-full"
             x-init="$el.style.width = (localStorage.getItem('properties-panel-width') || 450) + 'px'">

            {{-- Resize Handle --}}
            <div class="properties-resize-handle"></div>

            @include('slick-forms::livewire.partials.properties-panel')
        </div>

    </div>
    @else
    {{-- Settings-only mode: render Form Settings panel full-width --}}
    <div class="container py-3">
        @include('slick-forms::livewire.partials.properties-panel')
    </div>
    @endif

    {{-- Email Logs Viewer Modal --}}
    @if($showEmailLogsModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-list-ul text-info me-2"></i>
                            Email Delivery Logs
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeEmailLogsModal"></button>
                    </div>
                    <div class="modal-body">
                        @livewire('slick-forms::email-logs-viewer', ['formId' => $form->id], key('email-logs-'.$form->id))
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Spam Logs Viewer Modal --}}
    @if($showSpamLogsModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-danger bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-shield-exclamation text-danger me-2"></i>
                            Spam Detection Logs
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeSpamLogsModal"></button>
                    </div>
                    <div class="modal-body">
                        @livewire('slick-forms::spam-logs-viewer', ['formId' => $form->id], key('spam-logs-'.$form->id))
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Email Template Editor Modal --}}
    @if($showEmailTemplateModal && $editingTemplateIndex !== null)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:key="email-template-modal-{{ $editingTemplateIndex }}">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            @if(empty($templateRecipients))
                                New Email Template
                            @else
                                Edit Email Template
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeEmailTemplateModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="saveTemplate" id="email-template-form">
                            {{-- Enabled Toggle --}}
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="templateEnabled"
                                        wire:model="templateEnabled"
                                    >
                                    <label class="form-check-label" for="templateEnabled">
                                        <strong>Template Enabled</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Disabled templates will not send emails</small>
                            </div>

                            {{-- Recipients --}}
                            <div class="mb-3">
                                <label for="templateRecipients" class="form-label">
                                    Recipients <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="templateRecipients"
                                    wire:model="templateRecipients"
                                    placeholder="admin@example.com, manager@example.com"
                                    required
                                >
                                <small class="text-muted">
                                    Comma-separated email addresses, or use field reference: <code>field:email_field_name</code>
                                </small>
                            </div>

                            {{-- Subject Line --}}
                            <div class="mb-3">
                                <label for="templateSubject" class="form-label">
                                    Subject Line <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="templateSubject"
                                    wire:model="templateSubject"
                                    placeholder="New form submission"
                                    required
                                >
                                <small class="text-muted">
                                    Available variables: <code>@{{form.name}}</code>, <code>@{{submission.id}}</code>, <code>@{{submission.created_at}}</code>
                                </small>
                            </div>

                            {{-- Priority --}}
                            <div class="mb-3">
                                <label for="templatePriority" class="form-label">
                                    Priority
                                </label>
                                <select
                                    class="form-select"
                                    id="templatePriority"
                                    wire:model="templatePriority"
                                >
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? '(Highest)' : ($i === 10 ? '(Lowest)' : '') }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">
                                    Templates are sent in priority order (1 = highest priority)
                                </small>
                            </div>

                            {{-- Attach PDF --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="templateAttachPdf"
                                        wire:model="templateAttachPdf"
                                    >
                                    <label class="form-check-label" for="templateAttachPdf">
                                        Attach PDF of submission
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Automatically generates and attaches a PDF with all submission data
                                </small>
                            </div>

                            {{-- Email Body Template (Info Only) --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Email Body Template
                                </label>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <small>
                                        Uses default template: <code>emails/admin-notification.blade.php</code>
                                        <br>
                                        Includes form name, submission time, IP address, and all field values.
                                    </small>
                                </div>
                            </div>

                            {{-- Conditional Rules --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Conditional Rules
                                    <span class="badge bg-secondary ms-2">{{ count($templateConditionalRules) }} rule(s)</span>
                                </label>
                                <div class="alert alert-secondary">
                                    <i class="bi bi-filter me-1"></i>
                                    <small>
                                        Conditional rules allow you to send this email only when specific conditions are met.
                                        <br>
                                        <strong>Coming soon:</strong> Visual rule builder
                                    </small>
                                </div>

                                @if(!empty($templateConditionalRules))
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="small">Existing Rules:</strong>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="$set('templateConditionalRules', [])"
                                            >
                                                <i class="bi bi-trash"></i> Clear All
                                            </button>
                                        </div>
                                        <pre class="small mb-0" style="max-height: 150px; overflow-y: auto;">{{ json_encode($templateConditionalRules, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEmailTemplateModal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" form="email-template-form" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i> Save Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Field Deletion Confirmation Modal --}}
    @if($showDeleteConfirmation)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                            Confirm Field Deletion
                        </h5>
                        <button type="button" class="btn-close" wire:click="cancelDeleteField"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This field is referenced by conditional logic in other fields.
                        </div>

                        <p class="mb-3">The following fields have conditional logic that depends on this field:</p>

                        <ul class="list-group mb-3">
                            @foreach($deletionDependencies as $dependency)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $dependency['field_label'] }}</strong>
                                            <div class="small text-muted">{{ $dependency['field_element_id'] }}</div>
                                        </div>
                                        <span class="badge bg-secondary">{{ $dependency['condition_count'] }} condition(s)</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mb-0">
                            If you proceed, all conditional logic references to this field will be automatically removed from the affected fields.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDeleteField">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="confirmDeleteField">
                            <i class="bi bi-trash me-1"></i> Delete Field and Clean Up
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Share Form Panel Modal --}}
    @if($showSharePanel)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" x-data="sharePanel()" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-share-fill text-primary me-2"></i>
                            Share Form: {{ $form->name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeSharePanel"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Left Column: Form URL & QR Code --}}
                            <div class="col-md-6 border-end">
                                <h6 class="mb-3">
                                    <i class="bi bi-link-45deg me-2"></i>Form URL
                                </h6>

                                {{-- Form URL with Copy --}}
                                <div class="input-group mb-4">
                                    <input
                                        type="text"
                                        class="form-control font-monospace"
                                        value="{{ $this->getFormUrl() }}"
                                        readonly
                                        id="shareFormUrl"
                                    >
                                    <button
                                        class="btn btn-outline-primary"
                                        type="button"
                                        @click="copyToClipboard('shareFormUrl', $event.target)"
                                    >
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>

                                {{-- QR Code --}}
                                <h6 class="mb-3">
                                    <i class="bi bi-qr-code me-2"></i>QR Code
                                </h6>

                                <div class="text-center mb-3">
                                    <div class="border rounded p-3 d-inline-block bg-white">
                                        {!! $this->getQrCode() !!}
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        @click="downloadQrCode()"
                                    >
                                        <i class="bi bi-download me-2"></i>Download QR Code (SVG)
                                    </button>
                                </div>
                            </div>

                            {{-- Right Column: Pre-fill URL Generator --}}
                            <div class="col-md-6">
                                <h6 class="mb-3">
                                    <i class="bi bi-clipboard-data me-2"></i>Pre-fill URL Generator
                                </h6>

                                @if($form->settings['url_security']['allow_prefilled_urls'] ?? true)
                                    <p class="text-muted small mb-3">
                                        Generate a URL with pre-populated field values. Recipients will see the form with these values filled in.
                                    </p>

                                    <form wire:submit="generatePrefillUrl">
                                        {{-- Field Inputs --}}
                                        <div class="mb-3" style="max-height: 400px; overflow-y: auto;">
                                            @foreach($form->fields()->whereNull('parent_field_id')->orderBy('order')->get() as $field)
                                                @if(!in_array($field->field_type, ['header', 'paragraph', 'code', 'image', 'video', 'calculation', 'repeater']))
                                                    <div class="mb-3">
                                                        <label for="prefill_{{ $field->id }}" class="form-label">
                                                            {{ $field->label }}
                                                            <small class="text-muted">({{ $field->name }})</small>
                                                        </label>

                                                        @if($field->field_type === 'textarea')
                                                            <textarea
                                                                class="form-control form-control-sm"
                                                                id="prefill_{{ $field->id }}"
                                                                wire:model="prefillData.{{ $field->name }}"
                                                                rows="2"
                                                            ></textarea>
                                                        @elseif($field->field_type === 'select' || $field->field_type === 'radio')
                                                            <select
                                                                class="form-select form-select-sm"
                                                                id="prefill_{{ $field->id }}"
                                                                wire:model="prefillData.{{ $field->name }}"
                                                            >
                                                                <option value="">-- Select --</option>
                                                                @foreach($field->options['values'] ?? [] as $option)
                                                                    <option value="{{ is_array($option) ? ($option['value'] ?? '') : $option }}">
                                                                        {{ is_array($option) ? ($option['label'] ?? $option['value']) : $option }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @elseif($field->field_type === 'checkbox' || $field->field_type === 'switch')
                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    id="prefill_{{ $field->id }}"
                                                                    wire:model="prefillData.{{ $field->name }}"
                                                                >
                                                                <label class="form-check-label" for="prefill_{{ $field->id }}">
                                                                    Checked
                                                                </label>
                                                            </div>
                                                        @else
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                id="prefill_{{ $field->id }}"
                                                                wire:model="prefillData.{{ $field->name }}"
                                                            >
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-magic me-2"></i>Generate Pre-fill URL
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Generated Pre-fill URL --}}
                                    @if($generatedPrefillUrl)
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">
                                                <i class="bi bi-check-circle me-2"></i>Pre-fill URL Generated!
                                            </h6>
                                            <div class="input-group input-group-sm">
                                                <input
                                                    type="text"
                                                    class="form-control font-monospace"
                                                    value="{{ $generatedPrefillUrl }}"
                                                    readonly
                                                    id="generatedPrefillUrl"
                                                >
                                                <button
                                                    class="btn btn-outline-secondary"
                                                    type="button"
                                                    @click="copyToClipboard('generatedPrefillUrl', $event.target)"
                                                >
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Expires in {{ $form->settings['url_security']['prefill_expiration_hours'] ?? 24 }} hours
                                            </small>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Pre-fill URLs are disabled for this form. Enable them in the URLs settings tab.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeSharePanel">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alpine.js Share Panel Logic --}}
        <script>
            function sharePanel() {
                return {
                    copyToClipboard(elementId, button) {
                        const input = document.getElementById(elementId);
                        input.select();
                        navigator.clipboard.writeText(input.value);

                        // Visual feedback
                        const originalHtml = button.innerHTML;
                        button.innerHTML = '<i class="bi bi-check"></i>';
                        button.classList.add('btn-success');
                        button.classList.remove('btn-outline-primary', 'btn-outline-secondary');

                        setTimeout(() => {
                            button.innerHTML = originalHtml;
                            button.classList.remove('btn-success');
                            button.classList.add(elementId === 'shareFormUrl' ? 'btn-outline-primary' : 'btn-outline-secondary');
                        }, 2000);
                    },

                    downloadQrCode() {
                        const svg = document.querySelector('.modal-body svg');
                        const svgData = new XMLSerializer().serializeToString(svg);
                        const blob = new Blob([svgData], { type: 'image/svg+xml' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'form-qr-code.svg';
                        a.click();
                        URL.revokeObjectURL(url);
                    }
                }
            }
        </script>
    @endif

    {{-- Version History Modal --}}
    @if($showVersionHistory && slick_forms_feature_enabled('versioning'))
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:key="version-history-modal">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Version History
                        </h5>
                        <button type="button" class="btn-close" wire:click="toggleVersionHistory" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(empty($versions))
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No versions saved yet. Click "Create Version" to save a snapshot of your form.
                            </div>
                        @else
                            <div class="list-group">
                                @foreach($versions as $version)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <span class="badge bg-primary me-2">v{{ $version['version_number'] }}</span>
                                                    @if($version['version_name'])
                                                        {{ $version['version_name'] }}
                                                    @else
                                                        Version {{ $version['version_number'] }}
                                                    @endif
                                                </h6>
                                                <p class="mb-1 text-muted small">{{ $version['change_summary'] }}</p>
                                                <div class="small text-muted">
                                                    <i class="bi bi-calendar me-1"></i>{{ $version['published_at'] }}
                                                    <i class="bi bi-person ms-3 me-1"></i>{{ $version['published_by'] }}
                                                    <i class="bi bi-file-text ms-3 me-1"></i>{{ $version['submission_count'] }} submissions
                                                </div>
                                            </div>
                                            <div class="btn-group-vertical btn-group-sm ms-3">
                                                <button type="button" class="btn btn-outline-primary" wire:click="restoreVersion({{ $version['id'] }})" wire:confirm="Restore form to version {{ $version['version_number'] }}? Current state will be lost.">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                                </button>
                                                @if($version['submission_count'] == 0)
                                                    <button type="button" class="btn btn-outline-danger" wire:click="deleteVersion({{ $version['id'] }})" wire:confirm="Delete version {{ $version['version_number'] }}? This cannot be undone.">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="toggleVersionHistory">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="createVersion">
                            <i class="bi bi-plus-circle me-1"></i>Create New Version
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

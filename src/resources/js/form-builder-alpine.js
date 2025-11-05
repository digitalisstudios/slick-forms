/**
 * SlickForms FormBuilder Alpine Integration
 * Registers Alpine components and initializes the form builder
 *
 * Note: This cannot use ES6 imports because Alpine is loaded globally via Livewire
 * We register the component directly with the global Alpine instance
 */

document.addEventListener('alpine:init', () => {
    window.Alpine.data('formBuilder', () => ({
        // State (Note: showFieldEditor, showElementEditor, etc. are provided via @entangle in blade template)
        sidebarCollapsed: false,
        propertiesPanelWidth: 450,
        searchQuery: '',

        // Computed
        get propertiesOpen() {
            return this.showFieldEditor || this.showElementEditor || this.showPageEditor || this.showFormEditor || this.selectedTableCellId;
        },

        // Initialization
        init() {
            // Restore sidebar collapsed state
            this.sidebarCollapsed = localStorage.getItem('slickFormsSidebarCollapsed') === 'true';

            // Restore properties panel width
            this.propertiesPanelWidth = parseInt(localStorage.getItem('properties-panel-width') || '450');

            // Initialize sidebar accordion state
            this.initSidebarAccordion();

            // Listen for properties panel resize events
            window.addEventListener('properties-panel-resized', (event) => {
                this.propertiesPanelWidth = event.detail.width;
            });
        },

        // Methods
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('slickFormsSidebarCollapsed', this.sidebarCollapsed);
        },

        filterItems(query) {
            this.searchQuery = query.toLowerCase();
            const items = document.querySelectorAll('#builderSidebar .list-group-item');
            const sections = document.querySelectorAll('#builderSidebar .accordion-collapse');

            items.forEach(item => {
                // Clone the item and remove <small> helper text elements
                const clone = item.cloneNode(true);
                const smalls = clone.querySelectorAll('small');
                smalls.forEach(small => small.remove());

                // Get text content without icons and helper text
                const labelText = clone.textContent.trim().toLowerCase();

                const matches = !query || labelText.includes(this.searchQuery);

                if (matches) {
                    item.style.removeProperty('display');
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });

            // Show all sections when searching
            if (query) {
                sections.forEach(section => {
                    if (!section.classList.contains('show')) {
                        bootstrap.Collapse.getOrCreateInstance(section).show();
                    }
                });
            }
        },

        initSidebarAccordion() {
            // Restore accordion state from localStorage
            const savedState = localStorage.getItem('builderAccordionState');
            if (savedState) {
                try {
                    const state = JSON.parse(savedState);
                    // Wait for next tick to ensure DOM is ready
                    this.$nextTick(() => {
                        Object.keys(state).forEach(collapseId => {
                            const element = document.getElementById(collapseId);
                            if (element) {
                                if (state[collapseId]) {
                                    // Should be shown
                                    if (!element.classList.contains('show')) {
                                        new bootstrap.Collapse(element, { toggle: true });
                                    }
                                } else {
                                    // Should be hidden
                                    if (element.classList.contains('show')) {
                                        new bootstrap.Collapse(element, { toggle: true });
                                    }
                                }
                            }
                        });
                    });
                } catch (e) {
                    console.error('Failed to restore accordion state:', e);
                }
            }

            // Save accordion state when it changes
            ['layoutCollapse', 'contentCollapse', 'formFieldsCollapse'].forEach(collapseId => {
                const element = document.getElementById(collapseId);
                if (element) {
                    element.addEventListener('shown.bs.collapse', () => this.saveAccordionState());
                    element.addEventListener('hidden.bs.collapse', () => this.saveAccordionState());
                }
            });
        },

        saveAccordionState() {
            const state = {
                layoutCollapse: document.getElementById('layoutCollapse')?.classList.contains('show'),
                contentCollapse: document.getElementById('contentCollapse')?.classList.contains('show'),
                formFieldsCollapse: document.getElementById('formFieldsCollapse')?.classList.contains('show')
            };
            localStorage.setItem('builderAccordionState', JSON.stringify(state));
        }
    }));
});

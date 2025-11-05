{{--
    Shared SortableJS Initialization Script
    This script handles drag-and-drop for adding new items and reordering
--}}
initSortable() {
    if (typeof Sortable !== 'undefined') {
        new Sortable(this.$el, {
            animation: 150,
            handle: '.drag-handle',
            group: 'shared',
            filter: '.placeholder-text, .btn, button',
            onStart: (evt) => {
                document.getElementById('form-canvas').classList.add('drag-active');
            },
            onAdd: (evt) => {
                let parentId = parseInt(evt.to.dataset.parentElementId);

                // Check if this is a new item from the palette
                if (evt.item.dataset.type === 'new-field') {
                    const fieldType = evt.item.dataset.fieldType;
                    evt.item.remove();
                    @this.call('addField', fieldType, parentId);
                    return;
                }
                if (evt.item.dataset.type === 'new-element') {
                    const elementType = evt.item.dataset.elementType;
                    evt.item.remove();
                    @this.call('addLayoutElement', elementType, parentId);
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
                    @this.call('updateChildrenOrderInParent', parentId, orderedItems);
                }
            },
            onEnd: (evt) => {
                document.getElementById('form-canvas').classList.remove('drag-active');

                let parentId = parseInt(evt.to.dataset.parentElementId);
                let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                let orderedItems = items.map(el => {
                    if (el.dataset.fieldId) {
                        return { type: 'field', id: parseInt(el.dataset.fieldId) };
                    } else if (el.dataset.elementId) {
                        return { type: 'element', id: parseInt(el.dataset.elementId) };
                    }
                }).filter(item => item !== undefined);

                if (orderedItems.length > 0) {
                    @this.call('updateChildrenOrderInParent', parentId, orderedItems);
                }
            }
        });
    }
}

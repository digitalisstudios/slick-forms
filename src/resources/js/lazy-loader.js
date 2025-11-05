/**
 * SlickForms Lazy Loader
 * Dynamically loads external libraries only when needed
 */

(function() {
    'use strict';

    // Track loaded libraries to prevent duplicate loading
    const loadedLibraries = {
        scripts: {},
        styles: {}
    };

    // Track pending loads to prevent duplicate requests
    const pendingLoads = {};

    /**
     * Load a JavaScript file dynamically
     */
    function loadScript(url) {
        if (loadedLibraries.scripts[url]) {
            return Promise.resolve();
        }

        if (pendingLoads[url]) {
            return pendingLoads[url];
        }

        pendingLoads[url] = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = url;
            script.onload = () => {
                loadedLibraries.scripts[url] = true;
                delete pendingLoads[url];
                resolve();
            };
            script.onerror = () => {
                delete pendingLoads[url];
                reject(new Error(`Failed to load script: ${url}`));
            };
            document.head.appendChild(script);
        });

        return pendingLoads[url];
    }

    /**
     * Load a CSS file dynamically
     */
    function loadStyle(url) {
        if (loadedLibraries.styles[url]) {
            return Promise.resolve();
        }

        if (pendingLoads[url]) {
            return pendingLoads[url];
        }

        pendingLoads[url] = new Promise((resolve, reject) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = url;
            link.onload = () => {
                loadedLibraries.styles[url] = true;
                delete pendingLoads[url];
                resolve();
            };
            link.onerror = () => {
                delete pendingLoads[url];
                reject(new Error(`Failed to load stylesheet: ${url}`));
            };
            document.head.appendChild(link);
        });

        return pendingLoads[url];
    }

    /**
     * Load Quill WYSIWYG editor
     */
    window.loadQuill = function() {
        return Promise.all([
            loadStyle('https://cdn.quilljs.com/1.3.6/quill.snow.css'),
            loadScript('https://cdn.quilljs.com/1.3.6/quill.js')
        ]);
    };

    /**
     * Load Ace code editor
     */
    window.loadAce = function() {
        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ace.js')
            .then(() => Promise.all([
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ext-language_tools.min.js'),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-javascript.min.js'),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-css.min.js'),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-html.min.js'),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/theme-monokai.min.js')
            ]));
    };

    /**
     * Load TomSelect (searchable dropdowns)
     */
    window.loadTomSelect = function() {
        return Promise.all([
            loadStyle('https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css'),
            loadScript('https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js')
        ]);
    };

    /**
     * Load Flatpickr (date/time picker)
     */
    window.loadFlatpickr = function() {
        return Promise.all([
            loadStyle('https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css'),
            loadScript('https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js')
        ]);
    };

    console.log('[SlickForms] Lazy loader initialized');
})();

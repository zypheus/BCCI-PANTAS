(function (global) {
    'use strict';

    const runPageScripts = () => {
        document.body.classList.remove('sidebar-open');
        global.initSidebar?.();
        global.initLogoutConfirm?.();
        global.DataPanel?.initAll();
        global.DataPanel?.initDropdowns?.();
        global.initPatronImportLabels?.();
    };

    document.addEventListener('turbo:load', runPageScripts);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runPageScripts);
    } else {
        runPageScripts();
    }

    if (!global.Turbo) {
        return;
    }

    document.addEventListener('turbo:submit-start', (event) => {
        const form = event.target;

        if (form?.enctype === 'multipart/form-data') {
            event.preventDefault();
            form.removeAttribute('data-turbo');
            form.submit();
        }
    });
})(window);

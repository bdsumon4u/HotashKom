// Global notify handler for Livewire
(function registerGlobalNotifyHandler() {
    if (window.__notifyListenerRegistered) {
        return;
    }
    window.__notifyListenerRegistered = true;

    const queue = [];
    let flushScheduled = false;

    const flushQueue = () => {
        if (!window.$ || typeof window.$.notify !== 'function') {
            flushScheduled = false;
            setTimeout(flushQueue, 100);
            return;
        }

        while (queue.length) {
            const items = queue.shift();
            const itemArray = Array.isArray(items) ? items : [items];

            for (let item of itemArray) {
                const data = Array.isArray(item) ? item[0] : item;
                const message = typeof data === 'object' ? data.message : data;
                const type = typeof data === 'object' ? (data.type ?? 'info') : 'info';

                window.$.notify(message, {
                    type
                });
            }
        }

        flushScheduled = false;
    };

    const pushToQueue = (items) => {
        queue.push(items);
        if (!flushScheduled) {
            flushScheduled = true;
            queueMicrotask(flushQueue);
        }
    };

    const registerLivewireListener = () => {
        if (window.Livewire && window.Livewire.on) {
            window.Livewire.on('notify', pushToQueue);
        }
    };

    if (window.Livewire) {
        registerLivewireListener();
    } else {
        document.addEventListener('livewire:load', registerLivewireListener, {
            once: true
        });
    }
})();


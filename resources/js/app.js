import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Banner "Agregar a pantalla de inicio"
Alpine.data('pwaInstall', () => ({
    visible: false,
    esIos: false,
    promptEvent: null,

    init() {
        const descartado = localStorage.getItem('pwa-banner-descartado');
        const standalone = window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;
        if (descartado || standalone) return;

        this.esIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
        if (this.esIos) {
            this.visible = true;
            return;
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.promptEvent = e;
            this.visible = true;
        });
    },

    instalar() {
        if (!this.promptEvent) return;
        this.promptEvent.prompt();
        this.promptEvent.userChoice.then(() => {
            this.visible = false;
            this.promptEvent = null;
        });
    },

    descartar() {
        localStorage.setItem('pwa-banner-descartado', '1');
        this.visible = false;
    },
}));

Alpine.start();

if (import.meta.env.PROD && 'serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}

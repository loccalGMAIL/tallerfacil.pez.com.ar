{{-- Banner "Agregar a pantalla de inicio" (PWA) --}}
<div x-data="pwaInstall" x-show="visible" x-cloak x-transition
     class="fixed bottom-20 md:bottom-4 left-4 right-4 md:left-auto md:w-96 z-50
            bg-gray-900 text-white rounded-xl shadow-lg p-4 flex items-start gap-3">
    <img src="/icons/icon-192.png" alt="TallerFácil" class="w-10 h-10 rounded-lg shrink-0">
    <div class="flex-1 text-sm">
        <p class="font-semibold mb-0.5">Agregar a pantalla de inicio</p>
        <template x-if="esIos">
            <p class="text-gray-300 text-xs leading-relaxed">
                Tocá <strong>Compartir</strong>
                <svg class="w-3.5 h-3.5 inline -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                y elegí <strong>Agregar a pantalla de inicio</strong> para usar TallerFácil como una app.
            </p>
        </template>
        <template x-if="!esIos">
            <div>
                <p class="text-gray-300 text-xs mb-2">Instalá TallerFácil para usarla como una app.</p>
                <button @click="instalar"
                        class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-3 py-1.5 rounded-lg text-xs">
                    Instalar
                </button>
            </div>
        </template>
    </div>
    <button @click="descartar" aria-label="Cerrar" class="text-gray-500 hover:text-gray-300 shrink-0">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>

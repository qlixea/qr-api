@props([
    'qrImage' => '',
    'amount' => '0.00',
    'reference' => 'N/A',
    'currency' => 'BOB',
    'isOpen' => false
])

<div id="qlixea-qr-modal" 
     class="fixed inset-0 z-50 hidden" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop con efecto blur -->
    <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" 
         onclick="QlixeaModal.close()"></div>

    <!-- Panel del Modal -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                
                <!-- Botón de Cerrar -->
                <button type="button" 
                        onclick="QlixeaModal.close()" 
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Encabezado -->
                <div class="bg-indigo-600 px-4 py-6 sm:px-6 text-center">
                    <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">
                        Escanea para Pagar
                    </h3>
                    <p class="mt-2 text-sm text-indigo-100">
                        Usa tu aplicación bancaria para completar el pago
                    </p>
                </div>

                <!-- Contenido -->
                <div class="px-4 py-6 sm:px-6 space-y-6">
                    
                    <!-- Monto -->
                    <div class="text-center">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Monto a pagar</p>
                        <p class="text-4xl font-extrabold text-slate-900 dark:text-white mt-1">
                            {{ $currency === 'USD' ? '$' : 'Bs.' }} {{ number_format($amount, 2) }}
                        </p>
                    </div>

                    <!-- Código QR -->
                    <div class="flex justify-center">
                        <div class="bg-white p-4 rounded-xl shadow-inner border border-slate-200">
                            @if($qrImage)
                                <img src="data:image/png;base64,{{ $qrImage }}" 
                                     alt="Código QR de Pago" 
                                     class="w-56 h-56 object-contain">
                            @else
                                <div class="w-56 h-56 bg-slate-100 flex items-center justify-center text-slate-400">
                                    Error al cargar QR
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Referencia y Copiar -->
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                            Referencia de Transacción
                        </p>
                        <div class="flex items-center justify-between gap-2">
                            <code id="qlixea-ref-code" class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200 truncate">
                                {{ $reference }}
                            </code>
                            <button onclick="QlixeaModal.copyRef()" 
                                    class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span id="copy-btn-text">Copiar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Estado / Instrucción -->
                    <div class="flex items-center justify-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                        Esperando confirmación del banco...
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" 
                            onclick="QlixeaModal.close()" 
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 sm:mt-0 sm:w-auto transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Vanilla JS para manejar el modal (Sin dependencias) -->
<script>
const QlixeaModal = {
    open: function() {
        document.getElementById('qlixea-qr-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevenir scroll del body
    },
    close: function() {
        document.getElementById('qlixea-qr-modal').classList.add('hidden');
        document.body.style.overflow = ''; // Restaurar scroll
    },
    copyRef: function() {
        const refText = document.getElementById('qlixea-ref-code').innerText;
        navigator.clipboard.writeText(refText).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            const originalText = btnText.innerText;
            btnText.innerText = '¡Copiado!';
            setTimeout(() => {
                btnText.innerText = originalText;
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }
};

// Abrir automáticamente si la propiedad isOpen es true
@if($isOpen)
    document.addEventListener('DOMContentLoaded', () => {
        QlixeaModal.open();
    });
@endif
</script>
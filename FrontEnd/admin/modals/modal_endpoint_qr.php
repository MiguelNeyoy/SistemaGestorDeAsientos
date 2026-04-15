<!-- FrontEnd/admin/modals/modal_endpoint_qr.php -->
<div class="modal fade" id="qrEndpointModal" tabindex="-1" aria-labelledby="qrEndpointModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0 text-center">
                <!-- Encabezado de éxito -->
                <div class="bg-success text-white py-4 mb-4">
                    <i class="bi bi-check-circle-fill display-1 animate__animated animate__zoomIn"></i>
                    <h2 class="mt-2 fw-bold" id="qrEndpointModalLabel">¡Confirmado!</h2>
                </div>
                
                <!-- Detalles del alumno -->
                <div class="px-4 pb-4">
                    <h3 class="fw-bold mb-1" id="endpointNombre">MIGUEL ANGEL NEYOY</h3>
                    <p class="text-muted mb-4">Llegada registrada exitosamente</p>
                    
                    <div class="badge bg-light text-dark border p-3 mb-4 w-100" style="font-size: 1.5rem; border-radius: 12px;">
                        <span class="text-muted me-2" style="font-size: 1rem;">Asiento:</span>
                        <span class="fw-bold" id="endpointAsiento">H17</span>
                    </div>
                </div>
                
                <!-- Barra de progreso / Tiempo de espera -->
                <div class="progress" style="height: 6px; border-radius: 0;">
                    <div id="endpointProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%; transition: width 1.5s linear;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Clase para animación si se usa Animate.css */
.animate__animated {
    animation-duration: 0.5s;
}

#qrEndpointModal .modal-content {
    background: #fff;
}

#qrEndpointModal .bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}
</style>

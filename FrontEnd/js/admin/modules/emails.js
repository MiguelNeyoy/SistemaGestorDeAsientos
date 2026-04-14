export function setupEmailFormListener() {
    const form = document.getElementById("formEnviarQRs");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const carrera = document.getElementById("selectCarreraEnvio").value;
        const turno = document.getElementById("selectTurnoEnvio").value;
        const btnEnvio = document.getElementById("btnProcesarEnvio");
        const spinner = document.getElementById("spinnerEnvio");
        const icon = document.getElementById("iconEnvio");
        const alertBox = document.getElementById("envioAlert");

        if (!carrera || !turno) {
            showAlert("Por favor, selecciona carrera y turno.", "warning");
            return;
        }

        // Estado cargando
        btnEnvio.disabled = true;
        spinner.classList.remove("d-none");
        icon.classList.add("d-none");
        alertBox.classList.add("d-none");

        try {
            // Este endpoint depende de la implementación del backend, la simulamos o apuntamos al real
            const response = await fetch(`${window.BASE_API_URL}/admin/enviar-qrs`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${window.ADMIN_TOKEN}`
                },
                body: JSON.stringify({ carrera, turno })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(`Correos enviados exitosamente: ${data.message || data.enviados || 0}`, "success");
                form.reset();
            } else {
                showAlert(data.message || "No se pudieron enviar los correos.", "danger");
            }
        } catch (error) {
            console.error("Error enviando QRs:", error);
            showAlert("Ocurrió un error en la conexión al intentar enviar los correos.", "danger");
        } finally {
            // Restaurar estado del botón
            btnEnvio.disabled = false;
            spinner.classList.add("d-none");
            icon.classList.remove("d-none");
        }
    });
}

function showAlert(message, type) {
    const alertBox = document.getElementById("envioAlert");
    alertBox.className = `alert alert-${type} mt-3 mb-0`;
    alertBox.textContent = message;
    alertBox.classList.remove("d-none");
}

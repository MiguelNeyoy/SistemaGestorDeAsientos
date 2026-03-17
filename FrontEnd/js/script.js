document.addEventListener("DOMContentLoaded", function() {

    const asientos = document.querySelectorAll(".asiento");
    const info = document.getElementById("info-asiento");

    asientos.forEach(asiento => {

        asiento.addEventListener("click", function() {

            const nombre = this.dataset.nombre;
            const numero = this.dataset.numero;

            if (this.classList.contains("ocupado")) {

                info.innerHTML = `
                    <h3>Asiento ${numero}</h3>
                    <p><strong>Alumno:</strong> ${nombre}</p>
                    <p style="color:green;">Este asiento está confirmado.</p>
                `;

            } else {

                info.innerHTML = `
                    <h3>Asiento ${numero}</h3>
                    <p style="color:#0B3C5D;">
                        Este lugar está disponible actualmente.
                    </p>
                `;
            }

        });

    });

});

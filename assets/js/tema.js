/*
 * Selector de tema de MARISCOS PIPE.
 * El tema elegido se guarda en el navegador para conservarlo al cambiar de página.
 */
(function () {
    const clave = "mariscos-pipe-tema";
    const temaGuardado = localStorage.getItem(clave);
    const temaInicial = temaGuardado === "dark" ? "dark" : "light";

    // Aplico el tema antes de crear el botón para evitar mostrar colores incorrectos.
    document.body.dataset.theme = temaInicial;

    // En el panel el botón queda al final del menú lateral; en el acceso va dentro del formulario.
    const contenedor = document.querySelector(".sidebar-note")
        || document.querySelector("header")
        || document.querySelector(".login");

    if (!contenedor) return;

    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = "theme-toggle";
    boton.setAttribute("aria-label", "Cambiar tema de color");

    function actualizarBoton(tema) {
        const oscuro = tema === "dark";
        boton.textContent = oscuro ? "Cambiar a tema claro" : "Cambiar a tema oscuro";
        boton.setAttribute("aria-pressed", String(oscuro));
    }

    boton.addEventListener("click", function () {
        const nuevoTema = document.body.dataset.theme === "dark" ? "light" : "dark";
        document.body.dataset.theme = nuevoTema;
        localStorage.setItem(clave, nuevoTema);
        actualizarBoton(nuevoTema);
    });

    actualizarBoton(temaInicial);
    contenedor.appendChild(boton);
})();

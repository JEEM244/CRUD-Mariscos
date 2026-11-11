/*
 * Selector de tema de El Puerto.
 * El botón se agrega desde JavaScript para no repetirlo en cada página PHP.
 */
(function () {
    const clave = "el-puerto-tema";
    const temaGuardado = localStorage.getItem(clave);
    const temaInicial = temaGuardado === "dark" ? "dark" : "light";

    document.body.dataset.theme = temaInicial;

    const contenedor = document.querySelector("header") || document.querySelector(".login");
    if (!contenedor) return;

    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = "theme-toggle";
    boton.setAttribute("aria-label", "Cambiar tema de color");
    boton.setAttribute("aria-pressed", String(temaInicial === "dark"));

    function actualizarBoton(tema) {
        const oscuro = tema === "dark";
        boton.textContent = oscuro ? "☀  Cambiar a tema claro" : "◐  Cambiar a tema oscuro";
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

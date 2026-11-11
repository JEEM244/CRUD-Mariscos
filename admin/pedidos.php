<?php
// Iniciar la sesión para comprobar que el usuario tenga acceso al panel.
session_start();

// Si no ha iniciado sesión, no permitir que entre directamente a los pedidos.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Conectar con MySQL para consultar mesas, clientes, platillos y pedidos.
require_once "../config/conexion.php";

// Traer las mesas ordenadas por su número para llenar el primer selector.
$consulta = $conexion->query("SELECT * FROM mesas ORDER BY numero");
$mesas = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Consultar solo los platillos que están disponibles para venderse.
$consulta = $conexion->query("SELECT * FROM platillos WHERE disponible = 1 ORDER BY nombre");
$platillos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Obtener los clientes para que el mesero pueda relacionar el pedido con uno de ellos.
$consulta = $conexion->query("SELECT * FROM clientes ORDER BY nombre");
$clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Consultar los últimos pedidos y agregar el número de mesa a cada resultado.
$sql = "SELECT pedidos.*, mesas.numero AS numero_mesa
        FROM pedidos
        INNER JOIN mesas ON pedidos.mesa_id = mesas.id
        ORDER BY pedidos.id DESC
        LIMIT 12";

$consulta = $conexion->query($sql);
$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Comienza la página que verá el mesero al registrar pedidos. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
        <!-- Encabezado con el nombre del restaurante y los enlaces principales. -->
    <header class="topbar">
        <a class="logo-admin" href="panel.php">
            EL PUERTO
            <small>ADMINISTRACIÓN</small>
        </a>

        <nav>
            <a href="panel.php">Panel</a>
            <a href="index.php?salir=1">Cerrar sesión</a>
        </nav>
    </header>

        <!-- Contenido principal de la página de pedidos. -->
    <main class="wrap">
        <p class="eyebrow">PEDIDOS</p>
        <h1>Crear pedido</h1>
        <p class="intro">Selecciona una mesa, agrega los platillos y guarda el pedido.</p>

        <?php if (isset($_GET["ok"])): ?>
            <p class="notice">El pedido se registró correctamente.</p>
        <?php endif; ?>

        <?php if (isset($_GET["error"])): ?>
            <p class="notice">No se pudo guardar el pedido. Revisa la mesa y los platillos seleccionados.</p>
        <?php endif; ?>

                <!-- El formulario manda los datos a guardar_pedido.php para registrarlos en MySQL. -->
        <form class="panel-contenido" method="POST" action="guardar_pedido.php" id="pedidoForm">
                        <!-- Primeros datos del pedido: mesa y cliente. -->
            <div class="form-grid">
                <label>
                    Mesa
                    <select name="mesa_id" required>
                        <option value="">Selecciona una mesa</option>

                        <?php foreach ($mesas as $mesa): ?>
                            <option
                                value="<?= (int) $mesa["id"] ?>"
                                <?= $mesa["estado"] === "Ocupada" ? "disabled" : "" ?>
                            >
                                Mesa <?= (int) $mesa["numero"] ?> - <?= htmlspecialchars($mesa["estado"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Cliente (opcional)
                    <select name="cliente_id">
                        <option value="">Cliente sin registrar</option>

                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= (int) $cliente["id"] ?>">
                                <?= htmlspecialchars($cliente["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <h2 class="section-title">Platillos del pedido</h2>

                        <!-- Aquí se muestran las filas de platillos. JavaScript permite agregar más filas. -->
            <div id="lineas">
                <div class="order-line">
                    <select name="platillo[]" required>
                        <option value="">Selecciona un platillo</option>

                        <?php foreach ($platillos as $platillo): ?>
                            <option
                                value="<?= (int) $platillo["id"] ?>"
                                data-precio="<?= (float) $platillo["precio"] ?>"
                            >
                                <?= htmlspecialchars($platillo["nombre"]) ?>
                                - $<?= number_format((float) $platillo["precio"], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input
                        type="number"
                        name="cantidad[]"
                        value="1"
                        min="1"
                        max="30"
                        required
                        aria-label="Cantidad"
                    >

                    <button type="button" class="boton-secundario remove-line">Quitar</button>
                </div>
            </div>

            <?php if (count($platillos) === 0): ?>
                <p class="nota-admin">Primero agrega platillos al menú para poder crear un pedido.</p>
            <?php endif; ?>

            <button type="button" class="boton-secundario" id="addLine">
                + Agregar otro platillo
            </button>

                        <!-- El total es una estimación visual; el servidor vuelve a calcularlo antes de guardar. -->
            <div class="total-box">
                <span>Total estimado</span>
                <strong id="total">$0.00</strong>
            </div>

            <button class="boton-principal" type="submit" <?= count($platillos) === 0 ? "disabled" : "" ?>>
                Guardar pedido
            </button>
        </form>

                <!-- Historial corto para revisar los pedidos registrados recientemente. -->
        <h2 class="section-title">Pedidos recientes</h2>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Mesa</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= (int) $pedido["id"] ?></td>
                            <td>Mesa <?= (int) $pedido["numero_mesa"] ?></td>
                            <td><?= htmlspecialchars($pedido["fecha"]) ?></td>
                            <td><?= htmlspecialchars($pedido["estado"]) ?></td>
                            <td>$<?= number_format((float) $pedido["total"], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($pedidos) === 0): ?>
                        <tr>
                            <td colspan="5">Todavía no hay pedidos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Obtener los elementos del formulario que JavaScript necesita modificar.
        const contenedorLineas = document.getElementById("lineas");
        const botonAgregar = document.getElementById("addLine");
        const totalTexto = document.getElementById("total");

        // Sumar el precio de cada platillo multiplicado por la cantidad seleccionada.
        function calcularTotal() {
            let total = 0;

            const lineas = contenedorLineas.querySelectorAll(".order-line");

            // Revisar cada fila del formulario para obtener el precio y la cantidad.
            lineas.forEach(function (linea) {
                const selector = linea.querySelector("select");
                // Leer la cantidad y la opción que está seleccionada en esta fila.
                const cantidad = linea.querySelector("input").value;
                const opcion = selector.options[selector.selectedIndex];

                // Convertir los valores a números antes de hacer la multiplicación.
                const precio = Number(opcion.dataset.precio || 0);
                total += precio * Number(cantidad || 0);
            });

            totalTexto.textContent = "$" + total.toFixed(2);
        }

        // Al presionar el botón, copiar la primera fila para agregar otro platillo.
        botonAgregar.addEventListener("click", function () {
            // Copiar la primera fila para conservar los mismos campos y opciones.
            const primeraLinea = contenedorLineas.querySelector(".order-line");
            const nuevaLinea = primeraLinea.cloneNode(true);

            // Dejar el nuevo selector sin platillo y regresar la cantidad a uno.
            nuevaLinea.querySelector("select").selectedIndex = 0;
            nuevaLinea.querySelector("input").value = 1;

            contenedorLineas.appendChild(nuevaLinea);
            calcularTotal();
        });

        // Actualizar el total cuando se cambie el platillo o su cantidad.
        contenedorLineas.addEventListener("change", calcularTotal);
        contenedorLineas.addEventListener("input", calcularTotal);

        // Detectar si se presionó el botón Quitar dentro de alguna fila.
        contenedorLineas.addEventListener("click", function (evento) {
            // Revisar si el clic se hizo sobre el botón que quita una fila.
            if (evento.target.classList.contains("remove-line")) {
                // Contar cuántas filas hay antes de quitar la seleccionada.
                const lineas = contenedorLineas.querySelectorAll(".order-line");

                // No permitir que se elimine la última fila del pedido.
                if (lineas.length > 1) {
                    // Buscar la fila del botón presionado y quitarla del formulario.
                    evento.target.closest(".order-line").remove();
                    calcularTotal();
                }
            }
        });

        // Calcular el total inicial al cargar la página.
        calcularTotal();
    </script>
</body>
</html>

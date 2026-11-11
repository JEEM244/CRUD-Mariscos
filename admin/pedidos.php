<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Datos que se necesitan para llenar el formulario.
$consulta = $conexion->query("SELECT * FROM mesas ORDER BY numero");
$mesas = $consulta->fetchAll(PDO::FETCH_ASSOC);

$consulta = $conexion->query("SELECT * FROM platillos WHERE disponible = 1 ORDER BY nombre");
$platillos = $consulta->fetchAll(PDO::FETCH_ASSOC);

$consulta = $conexion->query("SELECT * FROM clientes ORDER BY nombre");
$clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Mostrar los últimos pedidos que se han registrado.
$sql = "SELECT pedidos.*, mesas.numero AS numero_mesa
        FROM pedidos
        INNER JOIN mesas ON pedidos.mesa_id = mesas.id
        ORDER BY pedidos.id DESC
        LIMIT 12";

$consulta = $conexion->query($sql);
$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
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

        <form class="panel-contenido" method="POST" action="guardar_pedido.php" id="pedidoForm">
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

            <div class="total-box">
                <span>Total estimado</span>
                <strong id="total">$0.00</strong>
            </div>

            <button class="boton-principal" type="submit" <?= count($platillos) === 0 ? "disabled" : "" ?>>
                Guardar pedido
            </button>
        </form>

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
        const contenedorLineas = document.getElementById("lineas");
        const botonAgregar = document.getElementById("addLine");
        const totalTexto = document.getElementById("total");

        // Sumar el precio de cada platillo por la cantidad elegida.
        function calcularTotal() {
            let total = 0;

            const lineas = contenedorLineas.querySelectorAll(".order-line");

            lineas.forEach(function (linea) {
                const selector = linea.querySelector("select");
                const cantidad = linea.querySelector("input").value;
                const opcion = selector.options[selector.selectedIndex];

                const precio = Number(opcion.dataset.precio || 0);
                total += precio * Number(cantidad || 0);
            });

            totalTexto.textContent = "$" + total.toFixed(2);
        }

        // Agregar otra fila copiando la primera.
        botonAgregar.addEventListener("click", function () {
            const primeraLinea = contenedorLineas.querySelector(".order-line");
            const nuevaLinea = primeraLinea.cloneNode(true);

            nuevaLinea.querySelector("select").selectedIndex = 0;
            nuevaLinea.querySelector("input").value = 1;

            contenedorLineas.appendChild(nuevaLinea);
            calcularTotal();
        });

        // Volver a calcular cuando cambie un platillo o una cantidad.
        contenedorLineas.addEventListener("change", calcularTotal);
        contenedorLineas.addEventListener("input", calcularTotal);

        // Quitar una fila, pero dejar siempre al menos una.
        contenedorLineas.addEventListener("click", function (evento) {
            if (evento.target.classList.contains("remove-line")) {
                const lineas = contenedorLineas.querySelectorAll(".order-line");

                if (lineas.length > 1) {
                    evento.target.closest(".order-line").remove();
                    calcularTotal();
                }
            }
        });

        calcularTotal();
    </script>
</body>
</html>

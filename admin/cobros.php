<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

$error = "";

// Cuando se confirma un pago, se actualiza el pedido y se libera la mesa.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pedidoId = (int) ($_POST["pedido_id"] ?? 0);

    try {
        $conexion->beginTransaction();

        $consulta = $conexion->prepare(
            "SELECT mesa_id
             FROM pedidos
             WHERE id = ? AND estado <> 'Pagado'
             FOR UPDATE"
        );
        $consulta->execute([$pedidoId]);

        $mesaId = $consulta->fetchColumn();

        if ($mesaId) {
            $actualizarPedido = $conexion->prepare(
                "UPDATE pedidos SET estado = 'Pagado' WHERE id = ?"
            );
            $actualizarPedido->execute([$pedidoId]);

            $liberarMesa = $conexion->prepare(
                "UPDATE mesas SET estado = 'Libre' WHERE id = ?"
            );
            $liberarMesa->execute([$mesaId]);
        }

        $conexion->commit();

        header("Location: cobros.php?ok=1");
        exit;

    } catch (Throwable $error) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        $error = "No se pudo registrar el pago. Intenta de nuevo.";
    }
}

// Mostrar los pedidos que todavía no se han pagado.
$sql = "SELECT
            pedidos.id,
            pedidos.total,
            pedidos.fecha,
            mesas.numero AS numero_mesa,
            clientes.nombre AS cliente
        FROM pedidos
        INNER JOIN mesas ON pedidos.mesa_id = mesas.id
        LEFT JOIN clientes ON pedidos.cliente_id = clientes.id
        WHERE pedidos.estado <> 'Pagado'
        ORDER BY pedidos.fecha";

$consulta = $conexion->query($sql);
$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobros | El Puerto</title>
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
        <p class="eyebrow">CAJA</p>
        <h1>Cobro de pedidos</h1>
        <p class="intro">
            Revisa el total de cada mesa. Cuando registres el pago, la mesa quedará libre para otro pedido.
        </p>

        <?php if (isset($_GET["ok"])): ?>
            <p class="notice">El pago se registró correctamente y la mesa quedó libre.</p>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <p class="notice"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Mesa</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= (int) $pedido["id"] ?></td>
                            <td>Mesa <?= (int) $pedido["numero_mesa"] ?></td>
                            <td><?= htmlspecialchars($pedido["cliente"] ?? "Sin registrar") ?></td>
                            <td><?= htmlspecialchars($pedido["fecha"]) ?></td>
                            <td>$<?= number_format((float) $pedido["total"], 2) ?></td>
                            <td>
                                <form
                                    method="POST"
                                    onsubmit="return confirm('¿Confirmas que este pedido ya fue pagado?')"
                                >
                                    <input type="hidden" name="pedido_id" value="<?= (int) $pedido["id"] ?>">
                                    <button class="boton-secundario" type="submit">Registrar pago</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($pedidos) === 0): ?>
                        <tr>
                            <td colspan="6">No hay pedidos pendientes de cobro.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

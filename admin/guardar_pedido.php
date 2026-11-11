<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Recibir los datos que vienen del formulario.
$mesaId = (int) ($_POST["mesa_id"] ?? 0);
$clienteId = $_POST["cliente_id"] ?? "";
$clienteId = $clienteId === "" ? null : (int) $clienteId;

$platillos = $_POST["platillo"] ?? [];
$cantidades = $_POST["cantidad"] ?? [];

// Comprobar que se haya elegido una mesa y por lo menos un platillo.
if ($mesaId <= 0 || !is_array($platillos) || count($platillos) === 0) {
    header("Location: pedidos.php?error=1");
    exit;
}

try {
    // La transacción evita guardar un pedido a medias.
    $conexion->beginTransaction();

    // Revisar que la mesa siga libre antes de ocuparla.
    $consulta = $conexion->prepare("SELECT estado FROM mesas WHERE id = ? FOR UPDATE");
    $consulta->execute([$mesaId]);
    $estadoMesa = $consulta->fetchColumn();

    if ($estadoMesa !== "Libre") {
        throw new Exception("La mesa no está disponible.");
    }

    // Crear el pedido. El total se calcula más adelante.
    $consulta = $conexion->prepare(
        "INSERT INTO pedidos (mesa_id, cliente_id, total)
         VALUES (?, ?, 0)"
    );
    $consulta->execute([$mesaId, $clienteId]);

    $pedidoId = $conexion->lastInsertId();
    $total = 0;

    // Estas consultas se preparan una sola vez y se usan para cada platillo.
    $buscarPlatillo = $conexion->prepare(
        "SELECT precio FROM platillos WHERE id = ? AND disponible = 1"
    );

    $guardarDetalle = $conexion->prepare(
        "INSERT INTO detalle_pedido (pedido_id, platillo_id, cantidad, precio)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($platillos as $posicion => $idPlatillo) {
        $idPlatillo = (int) $idPlatillo;
        $cantidad = (int) ($cantidades[$posicion] ?? 1);

        if ($idPlatillo <= 0 || $cantidad < 1 || $cantidad > 30) {
            throw new Exception("Revisa los platillos y las cantidades.");
        }

        // El precio se toma de la base de datos para evitar alterarlo desde el formulario.
        $buscarPlatillo->execute([$idPlatillo]);
        $precio = $buscarPlatillo->fetchColumn();

        if ($precio === false) {
            throw new Exception("Uno de los platillos ya no está disponible.");
        }

        $guardarDetalle->execute([
            $pedidoId,
            $idPlatillo,
            $cantidad,
            $precio
        ]);

        $total += (float) $precio * $cantidad;
    }

    if ($total <= 0) {
        throw new Exception("El pedido no tiene platillos válidos.");
    }

    // Guardar el total y marcar la mesa como ocupada.
    $actualizarPedido = $conexion->prepare(
        "UPDATE pedidos SET total = ? WHERE id = ?"
    );
    $actualizarPedido->execute([$total, $pedidoId]);

    $actualizarMesa = $conexion->prepare(
        "UPDATE mesas SET estado = 'Ocupada' WHERE id = ?"
    );
    $actualizarMesa->execute([$mesaId]);

    $conexion->commit();

    header("Location: pedidos.php?ok=1");
    exit;

} catch (Throwable $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    header("Location: pedidos.php?error=1");
    exit;
}

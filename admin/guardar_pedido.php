<?php
// Iniciar la sesión para confirmar que la petición viene de un administrador conectado.
session_start();

// Bloquear el acceso si no hay una sesión activa.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Cargar la conexión con la base de datos.
require_once "../config/conexion.php";

// Recibir los datos que vienen del formulario.
// Leer el número de la mesa que seleccionó el mesero y convertirlo a entero.
$mesaId = (int) ($_POST["mesa_id"] ?? 0);
// El cliente es opcional: si el selector viene vacío, guardar null.
$clienteId = $_POST["cliente_id"] ?? "";
$clienteId = $clienteId === "" ? null : (int) $clienteId;

// Recibir las listas de platillos y cantidades que llegaron desde el formulario.
$platillos = $_POST["platillo"] ?? [];
$cantidades = $_POST["cantidad"] ?? [];

// Comprobar que se haya elegido una mesa y por lo menos un platillo.
// Revisar que exista una mesa válida y que se haya agregado al menos un platillo.
if ($mesaId <= 0 || !is_array($platillos) || count($platillos) === 0) {
    header("Location: pedidos.php?error=1");
    exit;
}

try {
    // Empezar una transacción para que todos los cambios se guarden juntos.
    // Si algo falla, se pueden cancelar para no dejar un pedido incompleto.
    // La transacción evita guardar un pedido a medias.
    $conexion->beginTransaction();

    // Revisar que la mesa siga libre antes de ocuparla.
    // Bloquear temporalmente la fila de la mesa y consultar si continúa libre.
    $consulta = $conexion->prepare("SELECT estado FROM mesas WHERE id = ? FOR UPDATE");
    $consulta->execute([$mesaId]);
    $estadoMesa = $consulta->fetchColumn();

    // Si alguien ocupó la mesa antes de guardar, cancelar el pedido.
    if ($estadoMesa !== "Libre") {
        throw new Exception("La mesa no está disponible.");
    }

    // Crear el pedido. El total se calcula más adelante.
    // Crear primero el pedido principal. El total se actualizará cuando se sumen los platillos.
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

    // Recorrer los platillos que eligió el mesero, junto con su cantidad.
    foreach ($platillos as $posicion => $idPlatillo) {
        $idPlatillo = (int) $idPlatillo;
        $cantidad = (int) ($cantidades[$posicion] ?? 1);

        if ($idPlatillo <= 0 || $cantidad < 1 || $cantidad > 30) {
            throw new Exception("Revisa los platillos y las cantidades.");
        }

        // El precio se toma de la base de datos para evitar alterarlo desde el formulario.
        // Consultar el precio real desde MySQL; no confiar en el precio enviado por el navegador.
        $buscarPlatillo->execute([$idPlatillo]);
        $precio = $buscarPlatillo->fetchColumn();

        if ($precio === false) {
            throw new Exception("Uno de los platillos ya no está disponible.");
        }

        // Guardar el platillo, su cantidad y el precio que tenía al momento del pedido.
        $guardarDetalle->execute([
            $pedidoId,
            $idPlatillo,
            $cantidad,
            $precio
        ]);

        // Sumar el subtotal de esta fila al total general del pedido.
        $total += (float) $precio * $cantidad;
    }

    if ($total <= 0) {
        throw new Exception("El pedido no tiene platillos válidos.");
    }

    // Guardar el total y marcar la mesa como ocupada.
    // Guardar el total final en el registro principal del pedido.
    $actualizarPedido = $conexion->prepare(
        "UPDATE pedidos SET total = ? WHERE id = ?"
    );
    $actualizarPedido->execute([$total, $pedidoId]);

    // Cambiar el estado de la mesa para que ya no aparezca como disponible.
    $actualizarMesa = $conexion->prepare(
        "UPDATE mesas SET estado = 'Ocupada' WHERE id = ?"
    );
    $actualizarMesa->execute([$mesaId]);

    // Confirmar la transacción: pedido, detalles y estado de la mesa quedan guardados.
    $conexion->commit();

    header("Location: pedidos.php?ok=1");
    exit;

// Si ocurre un error, cancelar todos los cambios de la transacción.
} catch (Throwable $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    header("Location: pedidos.php?error=1");
    exit;
}
